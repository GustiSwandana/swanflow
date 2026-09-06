<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiptScannerController extends Controller
{
    /**
     * Scan an uploaded receipt / transfer proof image.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:10240'], // max 10MB
        ], [
            'image.required' => 'Pilih atau ambil foto bukti pembayaran terlebih dahulu.',
            'image.image' => 'Berkas harus berupa gambar (JPG, PNG, WEBP, atau HEIC).',
            'image.max' => 'Ukuran gambar maksimal adalah 10 MB.',
        ]);

        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $image = $request->file('image');
        $mimeType = $image->getClientMimeType() ?: 'image/jpeg';
        $imageData = base64_encode(file_get_contents($image->getRealPath()));

        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->get(['id', 'name', 'type']);

        $categoryNames = $categories->pluck('name')->toArray();
        $apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');

        // If Gemini API Key is available, process with Gemini AI Vision
        if (! empty($apiKey)) {
            try {
                $geminiResult = $this->callGeminiVision($apiKey, $imageData, $mimeType, $categoryNames);

                if ($geminiResult && ! empty($geminiResult['amount'])) {
                    $matchedCategory = $this->matchCategory($geminiResult['category_guess'] ?? '', $categories, $geminiResult['type'] ?? 'expense');

                    return response()->json([
                        'success' => true,
                        'source' => 'gemini_ai',
                        'amount' => (float) $geminiResult['amount'],
                        'date' => $geminiResult['date'] ?? now()->format('Y-m-d'),
                        'merchant' => $geminiResult['merchant'] ?? 'Struk Pembayaran',
                        'description' => $geminiResult['description'] ?? ($geminiResult['merchant'] ?? 'Pembayaran Struk'),
                        'type' => in_array($geminiResult['type'] ?? '', ['income', 'expense']) ? $geminiResult['type'] : 'expense',
                        'category_id' => $matchedCategory?->id,
                        'category_name' => $matchedCategory?->name ?? ($geminiResult['category_guess'] ?? null),
                        'items' => $geminiResult['items'] ?? [],
                        'notes' => $geminiResult['notes'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini Receipt Scan failed: '.$e->getMessage());
            }
        }

        // Return fallback directive so client-side Tesseract.js / regex parser kicks in
        return response()->json([
            'success' => true,
            'source' => 'client_ocr_fallback',
            'message' => 'Silakan gunakan OCR lokal peramban untuk membaca berkas struk.',
            'categories' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'type' => is_string($c->type) ? $c->type : $c->type->value,
            ]),
        ]);
    }

    /**
     * Parse raw text extracted from OCR into structured receipt data.
     */
    public function parseText(Request $request): JsonResponse
    {
        $request->validate([
            'text' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $text = $request->input('text');
        $parsed = $this->extractReceiptDataFromText($text);

        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->get(['id', 'name', 'type']);

        $matchedCategory = $this->matchCategory($parsed['category_guess'], $categories, $parsed['type']);

        return response()->json([
            'success' => true,
            'source' => 'heuristic_text_parser',
            'amount' => $parsed['amount'],
            'date' => $parsed['date'],
            'merchant' => $parsed['merchant'],
            'description' => $parsed['description'],
            'type' => $parsed['type'],
            'category_id' => $matchedCategory?->id,
            'category_name' => $matchedCategory?->name,
            'raw_text_length' => strlen($text),
        ]);
    }

    /**
     * Call Google Gemini API with Vision multimodal payload.
     *
     * @param  array<string>  $categoryNames
     * @return array<string, mixed>|null
     */
    protected function callGeminiVision(string $apiKey, string $base64Image, string $mimeType, array $categoryNames): ?array
    {
        $categoriesListStr = implode(', ', $categoryNames);
        $systemPrompt = "You are an expert OCR and financial document analyzer specialized in Indonesian receipts, store bills, QRIS receipts, and mobile banking transfer proofs (such as BCA, Mandiri, BRI, BNI, BSI, Seabank, GoPay, OVO, Dana, ShopeePay, Indomaret, Alfamart, Pertamina, cafes, restaurants).

Analyze the provided receipt/transfer image and extract the following JSON fields:
1. 'amount': (number, float or integer) The total final amount paid or transferred. Do not include currency symbols or commas. (e.g. 45000). Look for 'TOTAL', 'GRAND TOTAL', 'JUMLAH', 'TOTAL BAYAR', 'NOMINAL TRANSFER', 'TAGIHAN'.
2. 'date': (string, YYYY-MM-DD) Transaction date. If year is missing or unclear, assume year 2026.
3. 'merchant': (string) Store, merchant, restaurant, biller, or transfer recipient name.
4. 'description': (string) A concise clean description for the transaction (e.g., 'Belanja di Indomaret', 'Isi Bensin Pertamina', 'Transfer ke Budi').
5. 'type': (string) Either 'expense' (for purchases, bills, transfer out) or 'income' (for incoming transfers, salary, refunds).
6. 'category_guess': (string) Pick the best matching category from this list: [{$categoriesListStr}, Makanan & Minuman, Transportasi, Belanja Harian, Tagihan & Utilitas, Hiburan, Kesehatan, Gaji Pokok, Freelance & Bonus].
7. 'items': (array of strings) Up to 5 detected line items if visible on the receipt.
8. 'notes': (string|null) Any reference number or payment method (e.g. 'QRIS', 'BCA Mobile').

Return STRICTLY a valid JSON object only. No markdown formatting, no backticks, no preamble.";

        // Models to try in order of preference
        $models = ['gemini-2.0-flash', 'gemini-1.5-flash'];

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::timeout(15)->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Image,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                        'temperature' => 0.1,
                    ],
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        // Clean up markdown markers if any
                        $text = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($text));
                        $decoded = json_decode($text, true);
                        if (is_array($decoded) && isset($decoded['amount'])) {
                            return $decoded;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini model {$model} failed: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Heuristic regex parser for Indonesian receipt text.
     *
     * @return array<string, mixed>
     */
    protected function extractReceiptDataFromText(string $text): array
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

        $amount = 0;
        $date = now()->format('Y-m-d');
        $merchant = '';
        $type = 'expense';
        $categoryGuess = 'Belanja Harian';

        // 1. Detect Amount
        // Look for keywords like TOTAL, GRAND TOTAL, JUMLAH, BAYAR, NOMINAL, RP
        $amountPatterns = [
            '/(?:GRAND\s*TOTAL|TOTAL\s*BAYAR|TOTAL|JUMLAH|TAGIHAN|NOMINAL)\s*[:.]?\s*(?:RP\.?|IDR)?\s*([\d.,]{3,})/i',
            '/(?:RP\.?|IDR)\s*([\d.,]{3,})/i',
            '/([\d.,]{4,})\s*(?:CR|DB)?$/m',
        ];

        foreach ($amountPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $candidates = $matches[1];
                // Take the largest reasonable number as total
                foreach ($candidates as $cand) {
                    $cleaned = preg_replace('/[^0-9]/', '', $cand);
                    $val = (float) $cleaned;
                    if ($val >= 1000 && $val > $amount && $val < 1000000000) {
                        $amount = $val;
                    }
                }
                if ($amount > 0) {
                    break;
                }
            }
        }

        // 2. Detect Date (DD/MM/YYYY, DD-MM-YYYY, YYYY-MM-DD, etc.)
        if (preg_match('/(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{2,4})/', $text, $dateMatch)) {
            $p1 = (int) $dateMatch[1];
            $p2 = (int) $dateMatch[2];
            $p3 = (int) $dateMatch[3];
            if ($p3 < 100) {
                $p3 += 2000;
            }

            // Assume DD-MM-YYYY if p1 <= 31 and p2 <= 12
            if ($p2 <= 12 && $p1 <= 31) {
                $date = sprintf('%04d-%02d-%02d', $p3, $p2, $p1);
            } elseif ($p1 <= 12 && $p2 <= 31) {
                $date = sprintf('%04d-%02d-%02d', $p3, $p1, $p2);
            }
        }

        // 3. Detect Merchant
        if (! empty($lines)) {
            // First 3 non-empty lines usually have the store name
            for ($i = 0; $i < min(4, count($lines)); $i++) {
                $line = $lines[$i];
                if (strlen($line) >= 3 && ! preg_match('/^(selamat|terima kasih|no|tanggal|struk|nota|transaksi)/i', $line)) {
                    $merchant = $line;
                    break;
                }
            }
        }

        if (empty($merchant)) {
            $merchant = 'Struk Pembelian';
        }

        // 4. Detect Category & Type
        $lowerText = strtolower($text);

        if (preg_match('/(transfer\s*masuk|gaji|salary|payroll|pemasukan|refund)/i', $lowerText)) {
            $type = 'income';
            $categoryGuess = 'Gaji Pokok';
        } elseif (preg_match('/(resto|cafe|kopi|coffee|makan|food|bakso|ayam|mie|burger|pizza|kitchen|roti|teh|kuliner)/i', $lowerText)) {
            $categoryGuess = 'Makanan & Minuman';
        } elseif (preg_match('/(spbu|pertamina|shell|bensin|pertalite|pertamax|solar|parkir|toll|grab|gojek|gocar|goride|ojol|kereta|kai|tiket)/i', $lowerText)) {
            $categoryGuess = 'Transportasi';
        } elseif (preg_match('/(indomaret|alfamart|superindo|hypermart|alfa|mart|toko|belanja|minimarket|pasar|swalayan)/i', $lowerText)) {
            $categoryGuess = 'Belanja Harian';
        } elseif (preg_match('/(pln|listrik|pdam|telkom|indihome|wifi|pulsa|paket data|bpjs|tagihan|internet)/i', $lowerText)) {
            $categoryGuess = 'Tagihan & Utilitas';
        } elseif (preg_match('/(cinema|xxi|bioskop|netflix|spotify|game|steam|playstation|karaoke|hiburan)/i', $lowerText)) {
            $categoryGuess = 'Hiburan';
        } elseif (preg_match('/(apotek|kimia farma|k24|obat|klinik|dokter|rumah sakit|rs|sehat|vitamin)/i', $lowerText)) {
            $categoryGuess = 'Kesehatan';
        }

        return [
            'amount' => $amount,
            'date' => $date,
            'merchant' => $merchant,
            'description' => 'Pembayaran di '.$merchant,
            'type' => $type,
            'category_guess' => $categoryGuess,
        ];
    }

    /**
     * Match a guess string with user categories in database.
     *
     * @param  Collection<int, Category>  $categories
     */
    protected function matchCategory(?string $guess, $categories, string $type = 'expense'): ?Category
    {
        if (empty($guess)) {
            return $categories->firstWhere('type', $type);
        }

        $guessLower = strtolower(trim($guess));

        // Exact match
        $matched = $categories->first(function ($cat) use ($guessLower) {
            return strtolower($cat->name) === $guessLower;
        });

        if ($matched) {
            return $matched;
        }

        // Partial match
        $matched = $categories->first(function ($cat) use ($guessLower) {
            $nameLower = strtolower($cat->name);

            return str_contains($guessLower, $nameLower) || str_contains($nameLower, $guessLower);
        });

        if ($matched) {
            return $matched;
        }

        // Fallback to first matching type
        return $categories->firstWhere('type', $type) ?? $categories->first();
    }
}
