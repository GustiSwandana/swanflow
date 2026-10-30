<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppTransactionService
{
    /**
     * Normalize a phone number to standard international format without leading plus or zeros (e.g. 6281234567890).
     */
    public static function normalizePhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62'.$clean;
        }

        return $clean;
    }

    /**
     * Locate the registered User for the given sender phone number.
     */
    public function findUserBySender(string $sender): ?User
    {
        $norm = self::normalizePhoneNumber($sender);

        $user = User::where('whatsapp_number', $norm)
            ->orWhere('whatsapp_number', $sender)
            ->orWhere('whatsapp_number', '0'.substr($norm, 2))
            ->first();

        if ($user) {
            return $user;
        }

        $authEnv = env('WHATSAPP_AUTHORIZED_NUMBER');
        if (! empty($authEnv) && (self::normalizePhoneNumber($authEnv) === $norm)) {
            return User::first() ?? User::getPrimaryUser();
        }

        // Auto-bind to primary user if there is only 1 user in the system without a registered WhatsApp number
        if (User::count() === 1) {
            $primary = User::first();
            if ($primary && empty($primary->whatsapp_number)) {
                $primary->update(['whatsapp_number' => $norm]);

                return $primary;
            }
        }

        return null;
    }

    /**
     * Handle incoming WhatsApp message payload from webhook (text or image).
     *
     * @return array{success: bool, reply: string, transaction: ?Transaction}
     */
    public function handleIncoming(string $sender, ?string $message, ?string $imageUrl = null): array
    {
        $user = $this->findUserBySender($sender);

        if (! $user) {
            $norm = self::normalizePhoneNumber($sender);

            return [
                'success' => false,
                'reply' => "⚠️ *Akses Ditolak*\n\nNomor WhatsApp Anda (+{$norm}) belum terdaftar di SwanFlow.\n\nSilakan buka menu *Profil* di SwanFlow untuk mendaftarkan nomor ini atau hubungi pemilik akun.",
                'transaction' => null,
            ];
        }

        $text = trim((string) $message);

        // 1. Check for command triggers
        $lower = strtolower($text);
        if (in_array($lower, ['saldo', 'cek saldo', 'balance', 'dompet'])) {
            return [
                'success' => true,
                'reply' => $this->formatBalanceResponse($user),
                'transaction' => null,
            ];
        }

        if (in_array($lower, ['rekap', 'rekap hari ini', 'laporan', 'pengeluaran hari ini', 'summary'])) {
            return [
                'success' => true,
                'reply' => $this->formatSummaryResponse($user),
                'transaction' => null,
            ];
        }

        if (in_array($lower, ['bantuan', 'help', 'menu', 'panduan', '?'])) {
            return [
                'success' => true,
                'reply' => $this->formatHelpResponse($user),
                'transaction' => null,
            ];
        }

        // 2. Handle Image / Receipt Scan
        if (! empty($imageUrl)) {
            return $this->handleReceiptImage($user, $imageUrl, $text);
        }

        // 3. Handle Natural Language Text Transaction
        if (! empty($text)) {
            return $this->handleTextTransaction($user, $text);
        }

        return [
            'success' => false,
            'reply' => 'Halo kak! Kirimkan detail transaksi Anda (contoh: *kopi 25rb bca* atau foto struk) atau ketik *saldo* untuk cek dompet.',
            'transaction' => null,
        ];
    }

    /**
     * Process an image URL via Gemini Vision OCR and record transaction.
     *
     * @return array{success: bool, reply: string, transaction: ?Transaction}
     */
    protected function handleReceiptImage(User $user, string $imageUrl, string $caption = ''): array
    {
        try {
            $response = Http::timeout(15)->get($imageUrl);
            if (! $response->successful()) {
                return [
                    'success' => false,
                    'reply' => '⚠️ Gagal mengunduh foto bukti pembayaran dari WhatsApp. Silakan coba kirim ulang.',
                    'transaction' => null,
                ];
            }

            $imageData = base64_encode($response->body());
            $mimeType = $response->header('Content-Type') ?: 'image/jpeg';
            $apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');

            $categories = Category::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhereNull('user_id');
            })->get(['id', 'name', 'type']);
            $categoryNames = $categories->pluck('name')->toArray();

            $parsedData = null;
            if (! empty($apiKey)) {
                $parsedData = $this->callGeminiVision($apiKey, $imageData, $mimeType, $categoryNames);
            }

            if (! $parsedData || empty($parsedData['amount'])) {
                return [
                    'success' => false,
                    'reply' => "⚠️ *Foto Belum Jelas*\n\nAI tidak dapat mendeteksi total pembayaran dari foto tersebut. Anda bisa mencatat manual dengan mengetik teks (contoh: *belanja 45rb indomaret bca*).",
                    'transaction' => null,
                ];
            }

            $amount = (float) $parsedData['amount'];
            $type = in_array($parsedData['type'] ?? '', ['income', 'expense']) ? $parsedData['type'] : 'expense';
            $desc = $parsedData['description'] ?? ($parsedData['merchant'] ?? 'Pembayaran Struk');
            $date = ! empty($parsedData['date']) ? $parsedData['date'] : now()->toDateString();

            // Match wallet from notes or user caption, fallback to first wallet
            $wallet = $this->matchWallet($user, $caption.' '.($parsedData['notes'] ?? ''));
            $category = $this->matchCategory($user, $parsedData['category_guess'] ?? '', $categories, $type);

            $transaction = $this->saveTransaction($user, $wallet, $category, $amount, $type, $desc, $date);

            $balanceStr = 'Rp '.number_format((float) $wallet->fresh()->balance, 0, ',', '.');
            $amountStr = 'Rp '.number_format($amount, 0, ',', '.');

            $reply = "🧾 *Struk Berhasil Dicatat!*\n\n".
                "🏪 *Merchant:* {$parsedData['merchant']}\n".
                "💸 *Nominal:* {$amountStr}\n".
                "🏦 *Dompet:* {$wallet->name} (Sisa: {$balanceStr})\n".
                "📁 *Kategori:* {$category->name}\n".
                "📅 *Tanggal:* {$date}\n\n".
                'SwanFlow Tracker ✨';

            return [
                'success' => true,
                'reply' => $reply,
                'transaction' => $transaction,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Receipt OCR error: '.$e->getMessage());

            return [
                'success' => false,
                'reply' => '⚠️ Terjadi kendala saat membaca struk: '.$e->getMessage(),
                'transaction' => null,
            ];
        }
    }

    /**
     * Process a natural language text message into a transaction.
     *
     * @return array{success: bool, reply: string, transaction: ?Transaction}
     */
    protected function handleTextTransaction(User $user, string $text): array
    {
        $parsed = $this->parseTextWithRegex($text);

        // Fallback to Gemini AI if amount could not be extracted with regex
        if ($parsed['amount'] <= 0) {
            $apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');
            if (! empty($apiKey)) {
                $geminiParsed = $this->parseTextWithGemini($apiKey, $text, $user);
                if ($geminiParsed && $geminiParsed['amount'] > 0) {
                    $parsed = $geminiParsed;
                }
            }
        }

        if ($parsed['amount'] <= 0) {
            return [
                'success' => false,
                'reply' => "⚠️ *Format Belum Dikenali*\n\nTidak ditemukan nominal transaksi pada pesan Anda.\n\n*Contoh mudah:*\n• _kopi 25rb bca_\n• _bensin 50k gopay_\n• _gaji freelance 2.5jt mandiri_\n• _makan siang 35000 tunai_",
                'transaction' => null,
            ];
        }

        $amount = $parsed['amount'];
        $type = $parsed['type'];
        $desc = $parsed['description'] ?: 'Transaksi Tanpa Keterangan';
        $date = now()->toDateString();

        $wallet = $this->matchWallet($user, $text.' '.($parsed['wallet_hint'] ?? ''));
        $category = $this->matchCategory($user, $parsed['category_hint'] ?? $desc, null, $type);

        $transaction = $this->saveTransaction($user, $wallet, $category, $amount, $type, $desc, $date);

        $typeLabel = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';
        $typeIcon = $type === 'income' ? '🟢' : '🔴';
        $balanceStr = 'Rp '.number_format((float) $wallet->fresh()->balance, 0, ',', '.');
        $amountStr = 'Rp '.number_format($amount, 0, ',', '.');

        $reply = "{$typeIcon} *{$typeLabel} Berhasil Dicatat!*\n\n".
            "📝 *Keterangan:* {$desc}\n".
            "💰 *Nominal:* {$amountStr}\n".
            "🏦 *Dompet:* {$wallet->name} (Sisa: {$balanceStr})\n".
            "📁 *Kategori:* {$category->name}\n".
            '📅 *Tanggal:* '.now()->translatedFormat('d M Y, H:i')."\n\n".
            'SwanFlow Tracker ✨';

        return [
            'success' => true,
            'reply' => $reply,
            'transaction' => $transaction,
        ];
    }

    /**
     * Fast Heuristic / Regex Parser for Indonesian financial chat messages.
     *
     * @return array{amount: float, type: string, description: string, wallet_hint: ?string, category_hint: ?string}
     */
    public function parseTextWithRegex(string $text): array
    {
        $cleaned = trim($text);
        $lower = strtolower($cleaned);

        // 1. Detect Transaction Type
        $incomeKeywords = ['gaji', 'terima', 'dapat', 'masuk', 'transfer dari', 'penjualan', 'bonus', 'topup', 'top up', 'kembalian', 'dividen', 'cuan'];
        $isIncome = false;
        foreach ($incomeKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                $isIncome = true;
                break;
            }
        }
        $type = $isIncome ? 'income' : 'expense';

        // 2. Extract Amount
        $amount = 0.0;
        $matchedAmountToken = '';

        // Patterns like: 1.5jt, 2.5 juta, 100jt
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:jt|juta|million)\b/i', $cleaned, $m)) {
            $num = (float) str_replace(',', '.', $m[1]);
            $amount = $num * 1000000;
            $matchedAmountToken = $m[0];
        }
        // Patterns like: 25rb, 50k, 250 ribu
        elseif (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:rb|k|ribu|thousand)\b/i', $cleaned, $m)) {
            $num = (float) str_replace(',', '.', $m[1]);
            $amount = $num * 1000;
            $matchedAmountToken = $m[0];
        }
        // Standard Indonesian currency: Rp 25.000 or 25000
        elseif (preg_match('/(?:rp\.?|idr)?\s*(\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d{4,})/i', $cleaned, $m)) {
            $raw = str_replace('.', '', $m[1]);
            $raw = str_replace(',', '.', $raw);
            $amount = (float) $raw;
            $matchedAmountToken = $m[0];
        }
        // Small 3-digit amount if explicit: Rp 500
        elseif (preg_match('/(?:rp\.?|idr)\s*(\d+)/i', $cleaned, $m)) {
            $amount = (float) $m[1];
            $matchedAmountToken = $m[0];
        }

        // 3. Remove amount token from description
        $desc = $cleaned;
        if (! empty($matchedAmountToken)) {
            $desc = preg_replace('/'.preg_quote($matchedAmountToken, '/').'/i', '', $desc, 1);
        }

        // Clean up common prefixes/suffixes
        $desc = preg_replace('/\b(beli|bayar|keluar|buat|untuk|pake|pakai|via|dari|ke)\b/i', '', $desc);
        $desc = trim(preg_replace('/\s+/', ' ', $desc));

        // Capitalize description nicely
        $desc = ! empty($desc) ? ucwords(strtolower($desc)) : ($isIncome ? 'Pemasukan' : 'Pengeluaran');

        return [
            'amount' => $amount,
            'type' => $type,
            'description' => $desc,
            'wallet_hint' => null,
            'category_hint' => null,
        ];
    }

    /**
     * Fallback to Gemini AI for complex natural conversational Indonesian phrasing.
     *
     * @return array{amount: float, type: string, description: string, wallet_hint: ?string, category_hint: ?string}|null
     */
    protected function parseTextWithGemini(string $apiKey, string $text, User $user): ?array
    {
        $wallets = $user->wallets()->pluck('name')->implode(', ');
        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->pluck('name')->implode(', ');

        $prompt = "You are an Indonesian financial NLP parser. Parse this user message: \"{$text}\".
User's wallets: [{$wallets}].
User's categories: [{$categories}].

Return STRICTLY a JSON object with:
1. 'amount': (number, e.g. 25000)
2. 'type': 'expense' or 'income'
3. 'description': concise title in Indonesian
4. 'wallet_hint': best matching wallet from list or null
5. 'category_hint': best matching category from list or null

JSON only, no markdown.";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}";
            $res = Http::timeout(4)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
            ]);

            if ($res->successful()) {
                $raw = $res->json('candidates.0.content.parts.0.text');
                if (preg_match('/\{[\s\S]*\}/', $raw, $matches)) {
                    $json = json_decode($matches[0], true);
                    if (is_array($json) && ! empty($json['amount'])) {
                        return [
                            'amount' => (float) $json['amount'],
                            'type' => in_array($json['type'] ?? '', ['income', 'expense']) ? $json['type'] : 'expense',
                            'description' => $json['description'] ?? 'Transaksi',
                            'wallet_hint' => $json['wallet_hint'] ?? null,
                            'category_hint' => $json['category_hint'] ?? null,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini NLP text parse failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Match a Wallet from text or return user's default/first wallet.
     */
    public function matchWallet(User $user, string $text): Wallet
    {
        $wallets = $user->wallets()->get();
        if ($wallets->isEmpty()) {
            return Wallet::create([
                'user_id' => $user->id,
                'name' => 'Utama',
                'type' => 'cash',
                'balance' => 0,
            ]);
        }

        $lower = strtolower($text);

        // Alias matching for Indonesian banks/wallets
        $aliases = [
            'bca' => ['bca', 'bank central asia'],
            'mandiri' => ['mandiri', 'livin'],
            'bri' => ['bri', 'brimo'],
            'bni' => ['bni'],
            'gopay' => ['gopay', 'go-pay', 'gojek'],
            'ovo' => ['ovo'],
            'dana' => ['dana'],
            'shopeepay' => ['shopeepay', 'shopee pay', 'shopee'],
            'jago' => ['jago', 'bank jago'],
            'seabank' => ['seabank', 'sea bank'],
            'cash' => ['cash', 'tunai', 'kantong', 'dompet'],
        ];

        foreach ($wallets as $wallet) {
            $walletLower = strtolower($wallet->name);
            if (str_contains($lower, $walletLower)) {
                return $wallet;
            }

            foreach ($aliases as $key => $keywords) {
                if (str_contains($walletLower, $key)) {
                    foreach ($keywords as $kw) {
                        if (str_contains($lower, $kw)) {
                            return $wallet;
                        }
                    }
                }
            }
        }

        return $wallets->first();
    }

    /**
     * Match an appropriate Category.
     */
    public function matchCategory(User $user, string $hint, $categories = null, string $type = 'expense'): Category
    {
        if ($categories === null) {
            $categories = Category::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhereNull('user_id');
            })->get();
        }

        $lowerHint = strtolower($hint);

        // Heuristic category rules
        $rules = [
            'Makanan & Minuman' => ['kopi', 'ngopi', 'makan', 'sarapan', 'lunch', 'dinner', 'resto', 'warung', 'cafe', 'mie', 'ayam', 'nasi', 'bakso', 'minum', 'snack', 'boba', 'martabak', 'roti', 'geprek', 'burger', 'pizza', 'sate'],
            'Transportasi' => ['bensin', 'pertamax', 'pertalite', 'solar', 'spbu', 'parkir', 'tol', 'ojol', 'grab', 'gojek', 'maxim', 'kereta', 'mrt', 'bus', 'bengkel', 'cuci mobil', 'cuci motor', 'service'],
            'Belanja Harian' => ['belanja', 'supermarket', 'indomaret', 'alfamart', 'hypermart', 'shopee', 'tokopedia', 'baju', 'celana', 'sepatu', 'mall', 'toko'],
            'Tagihan & Utilitas' => ['listrik', 'token', 'pln', 'pdam', 'air', 'wifi', 'indihome', 'biznet', 'pulsa', 'kuota', 'iuran', 'bpjs', 'sewa', 'kos', 'kontrakan'],
            'Gaji Pokok' => ['gaji', 'salary', 'upah', 'payroll'],
            'Freelance & Bonus' => ['freelance', 'project', 'proyek', 'bonus', 'dividen', 'cuan', 'side income'],
            'Kesehatan' => ['obat', 'dokter', 'apotek', 'rs', 'klinik', 'vitamin', 'medis'],
            'Hiburan' => ['nonton', 'bioskop', 'netflix', 'spotify', 'game', 'steam', 'topup game', 'liburan', 'hotel'],
        ];

        foreach ($rules as $categoryName => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lowerHint, $kw)) {
                    $found = $categories->firstWhere('name', $categoryName);
                    if ($found) {
                        return $found;
                    }
                }
            }
        }

        // Direct name match
        foreach ($categories as $cat) {
            if (str_contains($lowerHint, strtolower($cat->name))) {
                return $cat;
            }
        }

        // Fallback to first matching type
        $fallback = $categories->first(function ($c) use ($type) {
            $catType = is_string($c->type) ? $c->type : $c->type->value;

            return $catType === $type;
        });

        if ($fallback) {
            return $fallback;
        }

        // Final fallback: create a default category
        return Category::create([
            'user_id' => $user->id,
            'name' => $type === 'income' ? 'Pendapatan Lainnya' : 'Pengeluaran Lainnya',
            'type' => $type,
            'icon' => 'tag',
            'color' => '#10B981',
        ]);
    }

    /**
     * Atomically create transaction and update wallet balance.
     */
    protected function saveTransaction(User $user, Wallet $wallet, Category $category, float $amount, string $type, string $desc, string $date): Transaction
    {
        return DB::transaction(function () use ($user, $wallet, $category, $amount, $type, $desc, $date) {
            $txn = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'category_id' => $category->id,
                'amount' => $amount,
                'type' => $type,
                'date' => $date,
                'description' => $desc,
                'admin_fee' => 0,
                'fee_payer' => 'source',
            ]);

            if ($type === TransactionType::Income->value) {
                $wallet->increment('balance', $amount);
            } else {
                $wallet->decrement('balance', $amount);
            }

            return $txn;
        });
    }

    /**
     * Format current wallet balances.
     */
    public function formatBalanceResponse(User $user): string
    {
        $wallets = $user->wallets()->get();
        if ($wallets->isEmpty()) {
            return "📊 *Saldo Dompet SwanFlow*\n\nAnda belum memiliki dompet aktif. Silakan tambahkan dompet di aplikasi web SwanFlow.";
        }

        $lines = ["📊 *Ringkasan Saldo Dompet SwanFlow*\n"];
        $total = 0.0;

        foreach ($wallets as $w) {
            $bal = (float) $w->balance;
            $total += $bal;
            $formatted = 'Rp '.number_format($bal, 0, ',', '.');
            $lines[] = "• *{$w->name}:* {$formatted}";
        }

        $totalFormatted = 'Rp '.number_format($total, 0, ',', '.');
        $lines[] = '━━━━━━━━━━━━━━━';
        $lines[] = "💰 *Total Net Worth:* {$totalFormatted}\n";
        $lines[] = 'SwanFlow Tracker ✨';

        return implode("\n", $lines);
    }

    /**
     * Format today's transaction summary.
     */
    public function formatSummaryResponse(User $user): string
    {
        $today = now()->toDateString();
        $txns = Transaction::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get();

        $income = (float) $txns->where('type', 'income')->sum('amount');
        $expense = (float) $txns->where('type', 'expense')->sum('amount');
        $count = $txns->count();

        $incomeFormatted = 'Rp '.number_format($income, 0, ',', '.');
        $expenseFormatted = 'Rp '.number_format($expense, 0, ',', '.');

        return "📈 *Rekap Transaksi Hari Ini*\n".
            '📅 '.now()->translatedFormat('l, d F Y')."\n\n".
            "🟢 *Pemasukan:* {$incomeFormatted}\n".
            "🔴 *Pengeluaran:* {$expenseFormatted}\n".
            "🔢 *Total Transaksi:* {$count} catatan\n\n".
            'SwanFlow Tracker ✨';
    }

    /**
     * Format help and syntax guide.
     */
    public function formatHelpResponse(User $user): string
    {
        return "💡 *Panduan Chat SwanFlow Tracker*\n\n".
            "Anda bisa langsung mencatat keuangan dengan mengetik pesan santai:\n\n".
            "• *Pengeluaran:*\n".
            "  _kopi kenangan 25rb bca_\n".
            "  _bensin pertamax 50k gopay_\n".
            "  _makan siang 35000 tunai_\n\n".
            "• *Pemasukan:*\n".
            "  _gaji freelance 2.5jt mandiri_\n".
            "  _terima transfer 200rb dana_\n\n".
            "• *Kirim Foto Struk:*\n".
            "  Kirim foto struk belanja / screenshot QRIS m-banking, AI akan membaca totalnya otomatis!\n\n".
            "• *Perintah Tambahan:*\n".
            "  Ketik *saldo* untuk cek dompet.\n".
            "  Ketik *rekap* untuk ringkasan hari ini.\n\n".
            'SwanFlow Tracker ✨';
    }

    /**
     * Call Google Gemini API with Vision multimodal payload for receipt reading.
     *
     * @param  array<string>  $categoryNames
     * @return array<string, mixed>|null
     */
    protected function callGeminiVision(string $apiKey, string $base64Image, string $mimeType, array $categoryNames): ?array
    {
        $categoriesListStr = implode(', ', $categoryNames);
        $systemPrompt = "You are an expert OCR and financial document analyzer specialized in Indonesian receipts, store bills, QRIS receipts, and mobile banking transfer proofs (BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay, Indomaret, Alfamart, Pertamina, cafes, restaurants).

Analyze the provided receipt/transfer image and extract the following JSON fields:
1. 'amount': (number) The total final amount paid or transferred (e.g. 45000).
2. 'date': (string, YYYY-MM-DD) Transaction date in strictly YYYY-MM-DD format. Assume year 2026 if missing.
3. 'merchant': (string) Store, merchant, restaurant, biller, or transfer recipient name.
4. 'description': (string) Clean concise description.
5. 'type': Either 'expense' or 'income'.
6. 'category_guess': Pick the best matching category from [{$categoriesListStr}, Makanan & Minuman, Transportasi, Belanja Harian, Tagihan & Utilitas, Hiburan, Kesehatan, Gaji Pokok, Freelance & Bonus].
7. 'notes': Any reference number or payment method (e.g. 'QRIS', 'BCA Mobile').

Return STRICTLY a valid JSON object only. No markdown formatting, no backticks, no preamble.";

        $models = ['gemini-3.1-flash-lite', 'gemini-3.8-flash', 'gemini-flash-latest'];

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $response = Http::timeout(6)->post($url, [
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
                ]);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                        $decoded = json_decode($matches[0], true);
                        if (is_array($decoded) && isset($decoded['amount'])) {
                            return $decoded;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini model {$model} failed in WhatsApp Service: ".$e->getMessage());
            }
        }

        return null;
    }
}
