<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
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
            'image' => ['required', 'file', 'image', 'max:15360'], // max 15MB
        ], [
            'image.required' => 'Pilih atau ambil foto bukti pembayaran terlebih dahulu.',
            'image.image' => 'Berkas harus berupa gambar (JPG, PNG, WEBP, atau HEIC).',
            'image.max' => 'Ukuran gambar maksimal adalah 15 MB.',
        ]);

        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $image = $request->file('image');
        $mimeType = $image->getClientMimeType() ?: 'image/jpeg';
        $imageData = base64_encode(file_get_contents($image->getRealPath()));

        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->get(['id', 'name', 'type']);

        $wallets = Wallet::where('user_id', $user->id)->get(['id', 'name', 'type', 'balance']);

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
                        'wallets' => $wallets,
                        'categories' => $categories->map(fn ($c) => [
                            'id' => $c->id,
                            'name' => $c->name,
                            'type' => is_string($c->type) ? $c->type : $c->type->value,
                        ]),
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
            'wallets' => $wallets,
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

        $wallets = Wallet::where('user_id', $user->id)->get(['id', 'name', 'type', 'balance']);

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
            'wallets' => $wallets,
            'categories' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'type' => is_string($c->type) ? $c->type : $c->type->value,
            ]),
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
     * Heuristic parser with semantic line scoring and Indonesian format awareness.
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

        // 1. Semantic Line-by-Line Amount Detection
        $scoredCandidates = [];

        foreach ($lines as $line) {
            $upperLine = strtoupper($line);

            // Skip lines that represent change or cash received
            $isCashOrChange = (bool) preg_match('/\b(TUNAI|CASH|KEMBALI|KEMBALIAN|CHANGE|DIBAYAR|UANG\s*DITERIMA|KEMBALI\s*KE\s*KONSUMEN)\b/i', $upperLine);
            $isDiscountOrTax = (bool) preg_match('/\b(DISKON|DISCOUNT|HEMAT|POTONGAN|PPN|PAJAK|TAX|CASHBACK|POIN)\b/i', $upperLine);

            // Strong Total keywords
            $isGrandTotal = (bool) preg_match('/\b(GRAND\s*TOTAL|TOTAL\s*BAYAR|TOTAL\s*TAGIHAN|TOTAL\s*TRANSAKSI|JUMLAH\s*TOTAL|TOTAL\s*AKHIR|TOTAL\s*PEMBAYARAN|NOMINAL\s*TRANSAKSI|JUMLAH\s*TRANSFER|TOTAL\s*BELANJA|TOTAL\s*HARGA)\b/i', $upperLine);
            $isSimpleTotal = (bool) preg_match('/\b(TOTAL|TAGIHAN|JUMLAH|NOMINAL)\b/i', $upperLine);
            $isSubtotal = (bool) preg_match('/\b(SUB\s*TOTAL|SUBTOTAL)\b/i', $upperLine);

            // Extract numeric tokens in line (with Rp/IDR optional)
            if (preg_match_all('/(?:RP\.?|IDR)?\s*([0-9.,]{3,})/i', $line, $numMatches)) {
                foreach ($numMatches[1] as $rawNum) {
                    $parsedVal = $this->parseIndonesianCurrency($rawNum);

                    if ($parsedVal < 500 || $parsedVal > 1000000000) {
                        continue;
                    }

                    $score = 10;
                    if ($isGrandTotal) {
                        $score += 100;
                    } elseif ($isSimpleTotal && ! $isCashOrChange) {
                        $score += 80;
                    } elseif ($isSubtotal) {
                        $score += 40;
                    }

                    if ($isCashOrChange) {
                        $score -= 60; // Penalize cash given or change
                    }

                    if ($isDiscountOrTax) {
                        $score -= 30; // Penalize discount/tax
                    }

                    $scoredCandidates[] = [
                        'val' => $parsedVal,
                        'score' => $score,
                    ];
                }
            }
        }

        // Sort candidates by highest score, then by reasonable amount
        if (! empty($scoredCandidates)) {
            usort($scoredCandidates, function ($a, $b) {
                if ($a['score'] === $b['score']) {
                    return $b['val'] <=> $a['val'];
                }

                return $b['score'] <=> $a['score'];
            });

            $amount = $scoredCandidates[0]['val'];
        }

        // Fallback amount check if line scoring found nothing
        if ($amount === 0) {
            if (preg_match('/(?:TOTAL|JUMLAH|BAYAR|TAGIHAN)[^0-9\n]*([0-9.,]{3,})/i', $text, $m)) {
                $amount = $this->parseIndonesianCurrency($m[1]);
            }
        }

        // 2. Detect Date (Indonesian textual months and numeric formats)
        $date = $this->extractDateFromText($text);

        // 3. Detect Merchant & Type from Text
        [$detectedMerchant, $detectedType, $detectedCategory] = $this->extractMerchantAndType($lines, $text);

        if ($detectedMerchant) {
            $merchant = $detectedMerchant;
        } else {
            $merchant = 'Struk Pembelian';
        }

        if ($detectedType) {
            $type = $detectedType;
        }

        if ($detectedCategory) {
            $categoryGuess = $detectedCategory;
        }

        return [
            'amount' => $amount,
            'date' => $date,
            'merchant' => $merchant,
            'description' => str_starts_with(strtolower($merchant), 'transfer') ? $merchant : 'Pembayaran di '.$merchant,
            'type' => $type,
            'category_guess' => $categoryGuess,
        ];
    }

    /**
     * Parse Indonesian currency string properly removing sen/cents (,00) and thousands dots.
     */
    public function parseIndonesianCurrency(string $raw): float
    {
        $str = trim($raw);
        // Remove trailing non-digit except commas/dots
        $str = preg_replace('/[^0-9.,]/', '', $str);

        // Handle trailing ,00 or .00 (cents/sen)
        if (preg_match('/[,.]00$/', $str)) {
            $str = substr($str, 0, -3);
        } elseif (preg_match('/[,.]0$/', $str)) {
            $str = substr($str, 0, -2);
        }

        // If string contains both dots and commas, determine which is decimal
        if (str_contains($str, '.') && str_contains($str, ',')) {
            $lastDot = strrpos($str, '.');
            $lastComma = strrpos($str, ',');

            if ($lastComma > $lastDot && strlen($str) - $lastComma - 1 === 2) {
                // Indonesian format: 50.000,50
                $thousands = substr($str, 0, $lastComma);
                $decimals = substr($str, $lastComma + 1);
                $cleanThousands = str_replace(['.', ','], '', $thousands);

                return (float) ($cleanThousands.'.'.$decimals);
            } elseif ($lastDot > $lastComma && strlen($str) - $lastDot - 1 === 2) {
                // US format: 50,000.50
                $thousands = substr($str, 0, $lastDot);
                $decimals = substr($str, $lastDot + 1);
                $cleanThousands = str_replace(['.', ','], '', $thousands);

                return (float) ($cleanThousands.'.'.$decimals);
            }
        }

        // Standard integer extraction without decimals
        $clean = preg_replace('/[^0-9]/', '', $str);

        return (float) $clean;
    }

    /**
     * Extract date with support for Indonesian month names and standard formats.
     */
    protected function extractDateFromText(string $text): string
    {
        $defaultDate = now()->format('Y-m-d');

        // Month dictionary
        $monthMap = [
            'jan' => 1, 'januari' => 1, 'january' => 1,
            'feb' => 2, 'februari' => 2, 'february' => 2,
            'mar' => 3, 'maret' => 3, 'march' => 3,
            'apr' => 4, 'april' => 4,
            'mei' => 5, 'may' => 5,
            'jun' => 6, 'juni' => 6, 'june' => 6,
            'jul' => 7, 'juli' => 7, 'july' => 7,
            'agu' => 8, 'ags' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
            'sep' => 9, 'september' => 9,
            'okt' => 10, 'oktober' => 10, 'oct' => 10, 'october' => 10,
            'nov' => 11, 'november' => 11,
            'des' => 12, 'desember' => 12, 'dec' => 12, 'december' => 12,
        ];

        // 1. Match textual month: DD Month YYYY (e.g. 06 Sep 2026 or 15 Agustus 2026)
        if (preg_match('#(\d{1,2})\s*[-/\s]\s*([a-zA-Z]{3,10})\s*[-/\s]\s*(\d{2,4})#i', $text, $textDateMatch)) {
            $day = (int) $textDateMatch[1];
            $monthStr = strtolower(substr($textDateMatch[2], 0, 3));
            $year = (int) $textDateMatch[3];

            if ($year < 100) {
                $year += 2000;
            }

            if (isset($monthMap[$monthStr]) && $day >= 1 && $day <= 31) {
                return sprintf('%04d-%02d-%02d', $year, $monthMap[$monthStr], $day);
            }
        }

        // 2. Standard numeric date: DD/MM/YYYY or DD-MM-YYYY
        if (preg_match('/(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{2,4})/', $text, $dateMatch)) {
            $p1 = (int) $dateMatch[1];
            $p2 = (int) $dateMatch[2];
            $p3 = (int) $dateMatch[3];

            if ($p3 < 100) {
                $p3 += 2000;
            }

            if ($p2 <= 12 && $p1 <= 31) {
                return sprintf('%04d-%02d-%02d', $p3, $p2, $p1);
            } elseif ($p1 <= 12 && $p2 <= 31) {
                return sprintf('%04d-%02d-%02d', $p3, $p1, $p2);
            }
        }

        // 3. YYYY-MM-DD
        if (preg_match('/(\d{4})[\/\.-](\d{1,2})[\/\.-](\d{1,2})/', $text, $isoMatch)) {
            return sprintf('%04d-%02d-%02d', (int) $isoMatch[1], (int) $isoMatch[2], (int) $isoMatch[3]);
        }

        return $defaultDate;
    }

    /**
     * Extract merchant name, transaction type, and category suggestion.
     *
     * @param  array<string>  $lines
     * @return array{0: string, 1: string, 2: string}
     */
    protected function extractMerchantAndType(array $lines, string $fullText): array
    {
        $lowerText = strtolower($fullText);
        $merchant = '';
        $type = 'expense';
        $category = 'Belanja Harian';

        // Known Popular Indonesian Merchants Mapping
        $knownMerchants = [
            'indomaret' => ['Indomaret', 'expense', 'Belanja Harian'],
            'alfamart' => ['Alfamart', 'expense', 'Belanja Harian'],
            'alfamidi' => ['Alfamidi', 'expense', 'Belanja Harian'],
            'superindo' => ['Superindo', 'expense', 'Belanja Harian'],
            'hypermart' => ['Hypermart', 'expense', 'Belanja Harian'],
            'transmart' => ['Transmart', 'expense', 'Belanja Harian'],
            'circle k' => ['Circle K', 'expense', 'Belanja Harian'],
            'lawson' => ['Lawson', 'expense', 'Makanan & Minuman'],
            'familymart' => ['FamilyMart', 'expense', 'Makanan & Minuman'],
            'pertamina' => ['SPBU Pertamina', 'expense', 'Transportasi'],
            'spbu' => ['SPBU Pertamina', 'expense', 'Transportasi'],
            'shell' => ['SPBU Shell', 'expense', 'Transportasi'],
            'bp akr' => ['SPBU BP', 'expense', 'Transportasi'],
            'kopi kenangan' => ['Kopi Kenangan', 'expense', 'Makanan & Minuman'],
            'fore coffee' => ['Fore Coffee', 'expense', 'Makanan & Minuman'],
            'janji jiwa' => ['Kopi Janji Jiwa', 'expense', 'Makanan & Minuman'],
            'starbucks' => ['Starbucks Coffee', 'expense', 'Makanan & Minuman'],
            'tomoro' => ['Tomoro Coffee', 'expense', 'Makanan & Minuman'],
            'point coffee' => ['Point Coffee', 'expense', 'Makanan & Minuman'],
            'mie gacoan' => ['Mie Gacoan', 'expense', 'Makanan & Minuman'],
            'solaria' => ['Solaria Restaurant', 'expense', 'Makanan & Minuman'],
            'mcdonald' => ["McDonald's", 'expense', 'Makanan & Minuman'],
            'mcd' => ["McDonald's", 'expense', 'Makanan & Minuman'],
            'kfc' => ['KFC Indonesia', 'expense', 'Makanan & Minuman'],
            'hokben' => ['HokBen', 'expense', 'Makanan & Minuman'],
            'richeese' => ['Richeese Factory', 'expense', 'Makanan & Minuman'],
            'burger king' => ['Burger King', 'expense', 'Makanan & Minuman'],
            'pizza hut' => ['Pizza Hut', 'expense', 'Makanan & Minuman'],
            'subway' => ['Subway', 'expense', 'Makanan & Minuman'],
            'chatime' => ['Chatime', 'expense', 'Makanan & Minuman'],
            'mixue' => ['Mixue', 'expense', 'Makanan & Minuman'],
            'j.co' => ['J.CO Donuts & Coffee', 'expense', 'Makanan & Minuman'],
            'tokopedia' => ['Tokopedia', 'expense', 'Belanja Harian'],
            'shopee' => ['Shopee', 'expense', 'Belanja Harian'],
            'tiktok shop' => ['TikTok Shop', 'expense', 'Belanja Harian'],
            'blibli' => ['Blibli', 'expense', 'Belanja Harian'],
            'lazada' => ['Lazada', 'expense', 'Belanja Harian'],
            'grab' => ['Grab', 'expense', 'Transportasi'],
            'gojek' => ['Gojek', 'expense', 'Transportasi'],
            'goride' => ['GoRide', 'expense', 'Transportasi'],
            'gocar' => ['GoCar', 'expense', 'Transportasi'],
            'maxim' => ['Maxim', 'expense', 'Transportasi'],
            'indrive' => ['inDrive', 'expense', 'Transportasi'],
            'bluebird' => ['Bluebird Taxi', 'expense', 'Transportasi'],
            'kai' => ['KAI / Kereta Api', 'expense', 'Transportasi'],
            'pln' => ['PLN Listrik', 'expense', 'Tagihan & Utilitas'],
            'pdam' => ['PDAM Air Minum', 'expense', 'Tagihan & Utilitas'],
            'telkom' => ['Telkom / IndiHome', 'expense', 'Tagihan & Utilitas'],
            'indihome' => ['IndiHome', 'expense', 'Tagihan & Utilitas'],
            'biznet' => ['Biznet Networks', 'expense', 'Tagihan & Utilitas'],
            'kimia farma' => ['Apotek Kimia Farma', 'expense', 'Kesehatan'],
            'k-24' => ['Apotek K-24', 'expense', 'Kesehatan'],
            'k24' => ['Apotek K-24', 'expense', 'Kesehatan'],
            'guardian' => ['Guardian', 'expense', 'Kesehatan'],
            'watsons' => ['Watsons', 'expense', 'Kesehatan'],
            'cinema xxi' => ['Cinema XXI', 'expense', 'Hiburan'],
            'cgv' => ['CGV Cinemas', 'expense', 'Hiburan'],
            'netflix' => ['Netflix', 'expense', 'Hiburan'],
            'spotify' => ['Spotify', 'expense', 'Hiburan'],
            'steam' => ['Steam Games', 'expense', 'Hiburan'],
        ];

        foreach ($knownMerchants as $keyword => $info) {
            if (str_contains($lowerText, $keyword)) {
                return $info;
            }
        }

        // Bank Transfer Proof Detection (BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana)
        if (preg_match('/(transfer\s*berhasil|transaksi\s*berhasil|berhasil\s*transfer|kirim\s*uang)/i', $lowerText)) {
            // Check for recipient name
            if (preg_match('/(?:KEPADA|PENERIMA|TUJUAN|KE)\s*[:.]?\s*([A-Za-z0-9\s.]+?)(?:\n|bank|rekening|nominal|$)/i', $fullText, $recMatch)) {
                $cleanRecipient = trim($recMatch[1]);
                if (strlen($cleanRecipient) >= 2 && strlen($cleanRecipient) <= 30) {
                    return ['Transfer ke '.$cleanRecipient, 'expense', 'Tagihan & Utilitas'];
                }
            }

            return ['Transfer Dana', 'expense', 'Tagihan & Utilitas'];
        }

        if (preg_match('/(transfer\s*masuk|dana\s*masuk|gaji|salary|payroll|pemasukan|refund)/i', $lowerText)) {
            return ['Penerimaan Transfer', 'income', 'Gaji Pokok'];
        }

        // Fallback: Pick top meaningful line as merchant
        $noiseRegex = '/^(selamat|terima\s*kasih|no|tanggal|struk|nota|transaksi|kasir|waktu|npwp|telp|jl\.|jalan|kota|pos|terminal|id|kode)/i';
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (strlen($trimmed) >= 3 && strlen($trimmed) <= 40 && ! preg_match($noiseRegex, $trimmed)) {
                $merchant = $trimmed;
                break;
            }
        }

        return [$merchant ?: 'Struk Pembelian', $type, $category];
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
