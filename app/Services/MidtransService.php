<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransService
{
    /**
     * Check if Midtrans is enabled for a given user or globally.
     */
    public function isEnabled(User|int|null $user = null): bool
    {
        $setting = $this->getSetting($user);

        if ($setting && $setting->midtrans_enabled && ! empty($setting->midtrans_server_key)) {
            return true;
        }

        return ! empty(config('services.midtrans.server_key'));
    }

    /**
     * Get Midtrans Server Key.
     */
    public function getServerKey(User|int|null $user = null): ?string
    {
        $setting = $this->getSetting($user);

        if ($setting && ! empty($setting->midtrans_server_key)) {
            return trim($setting->midtrans_server_key);
        }

        return config('services.midtrans.server_key');
    }

    /**
     * Get Midtrans Client Key.
     */
    public function getClientKey(User|int|null $user = null): ?string
    {
        $setting = $this->getSetting($user);

        if ($setting && ! empty($setting->midtrans_client_key)) {
            return trim($setting->midtrans_client_key);
        }

        return config('services.midtrans.client_key');
    }

    /**
     * Check if production environment is enabled.
     */
    public function isProduction(User|int|null $user = null): bool
    {
        $setting = $this->getSetting($user);

        if ($setting && $setting->midtrans_is_production !== null) {
            return (bool) $setting->midtrans_is_production;
        }

        return (bool) config('services.midtrans.is_production', false);
    }

    /**
     * Get Snap API Endpoint URL.
     */
    public function getSnapApiUrl(bool $isProduction): string
    {
        return $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Get Snap JS Script URL.
     */
    public function getSnapJsUrl(bool $isProduction): string
    {
        return $isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Request a new Snap Token for an Order.
     */
    public function createSnapTransaction(Order $order): array
    {
        $user = $order->user;
        $serverKey = $this->getServerKey($user);
        $clientKey = $this->getClientKey($user);
        $isProd = $this->isProduction($user);

        if (empty($serverKey)) {
            throw new Exception('Midtrans Server Key belum dikonfigurasi.');
        }

        $grossAmount = (int) round($order->final_amount);
        if ($grossAmount <= 0) {
            throw new Exception('Nominal pembayaran harus lebih besar dari 0.');
        }

        // Generate unique reference to allow retries if previous popup closed
        $midtransOrderId = 'SWAN-'.$order->id.'-'.time().'-'.Str::lower(Str::random(4));

        $payload = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->client_name,
                'phone' => $order->client_phone,
            ],
            'item_details' => [
                [
                    'id' => 'ORDER-'.$order->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => Str::limit($order->project_title ?: 'Proyek Kreatif', 45, '...'),
                ],
            ],
            'language' => 'id',
            'callbacks' => [
                'finish' => url('/p/'.$order->token),
            ],
        ];

        $apiUrl = $this->getSnapApiUrl($isProd);

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($apiUrl, $payload);

        if (! $response->successful()) {
            Log::error('Midtrans Snap Error: '.$response->body(), [
                'order_id' => $order->id,
                'status' => $response->status(),
            ]);

            $errorMsg = $response->json('error_messages.0') ?? 'Gagal membuat transaksi di Midtrans.';
            throw new Exception($errorMsg);
        }

        $data = $response->json();
        $snapToken = $data['token'] ?? null;
        $redirectUrl = $data['redirect_url'] ?? null;

        if (! $snapToken) {
            throw new Exception('Token Midtrans Snap tidak ditemukan.');
        }

        // Save reference and snap token to order
        $order->snap_token = $snapToken;
        $order->payment_gateway = 'midtrans';
        $order->payment_reference = $midtransOrderId;
        $order->save();

        return [
            'success' => true,
            'token' => $snapToken,
            'redirect_url' => $redirectUrl,
            'client_key' => $clientKey,
            'is_production' => $isProd,
            'snap_js_url' => $this->getSnapJsUrl($isProd),
            'order_id' => $midtransOrderId,
        ];
    }

    /**
     * Verify Midtrans Webhook SHA512 Signature.
     */
    public function verifySignature(array $payload, ?string $serverKey = null): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        if (empty($signatureKey)) {
            return false;
        }

        // 1. Try provided server key
        if (! empty($serverKey)) {
            $expected = hash('sha512', $orderId.$statusCode.$grossAmount.trim($serverKey));
            if (hash_equals($expected, $signatureKey)) {
                return true;
            }
        }

        // 2. Try global config server key as fallback
        $configKey = config('services.midtrans.server_key');
        if (! empty($configKey) && $configKey !== $serverKey) {
            $expectedConfig = hash('sha512', $orderId.$statusCode.$grossAmount.trim($configKey));
            if (hash_equals($expectedConfig, $signatureKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle incoming notification webhook from Midtrans.
     */
    public function handleNotification(array $payload): array
    {
        $rawOrderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? 'midtrans';
        $transactionId = $payload['transaction_id'] ?? $rawOrderId;

        // Extract internal order ID from SWAN-{id}-{timestamp}-{rand}
        $order = null;
        if (preg_match('/^SWAN-(\d+)/', $rawOrderId, $matches)) {
            $order = Order::find((int) $matches[1]);
        }

        if (! $order) {
            $order = Order::where('payment_reference', $rawOrderId)->first();
        }

        if (! $order) {
            Log::warning('Midtrans Webhook: Proyek order tidak ditemukan', ['order_id' => $rawOrderId]);
            throw new Exception('Order tidak ditemukan: '.$rawOrderId);
        }

        $serverKey = $this->getServerKey($order->user);
        if (! $this->verifySignature($payload, $serverKey)) {
            Log::warning('Midtrans Webhook: Signature tidak valid', [
                'order_id' => $rawOrderId,
                'payload' => $payload,
            ]);
            throw new Exception('Signature verification failed.');
        }

        // Idempotency: If already verified, return immediately
        if ($order->status === 'verified') {
            return [
                'success' => true,
                'status' => 'already_verified',
                'order' => $order,
            ];
        }

        // Check if status is paid / settlement / capture
        $isPaid = false;
        if ($transactionStatus === 'capture') {
            $isPaid = ($fraudStatus === 'accept');
        } elseif ($transactionStatus === 'settlement') {
            $isPaid = true;
        }

        if ($isPaid) {
            DB::transaction(function () use ($order, $paymentType, $transactionId) {
                $order->status = 'verified';
                $order->verified_at = now();
                $order->paid_at = now();
                $order->payment_gateway = 'midtrans';
                $order->payment_type = $paymentType;
                $order->payment_reference = $transactionId;
                $order->rejection_reason = null;

                // Auto-create income transaction in SwanFlow if amount > 0
                if ($order->final_amount > 0 && ! $order->transaction_id) {
                    $userId = $order->user_id;

                    // Resolve wallet
                    $wallet = null;
                    if ($order->wallet_id) {
                        $wallet = Wallet::where('user_id', $userId)->find($order->wallet_id);
                    }
                    if (! $wallet) {
                        $wallet = Wallet::where('user_id', $userId)->first();
                    }

                    if ($wallet) {
                        $category = Category::firstOrCreate(
                            [
                                'user_id' => $userId,
                                'name' => 'Pendapatan Project',
                                'type' => TransactionType::Income->value,
                            ],
                            [
                                'icon' => 'camera',
                                'color' => '#10b981',
                            ]
                        );

                        $formattedType = strtoupper(str_replace('_', ' ', $paymentType));
                        $tx = Transaction::create([
                            'user_id' => $userId,
                            'wallet_id' => $wallet->id,
                            'category_id' => $category->id,
                            'type' => TransactionType::Income->value,
                            'amount' => $order->final_amount,
                            'admin_fee' => 0,
                            'fee_payer' => 'source',
                            'date' => now()->toDateString(),
                            'description' => "Pembayaran Midtrans ({$formattedType}): {$order->client_name} ({$order->project_title})",
                        ]);

                        $wallet->increment('balance', $order->final_amount);
                        $order->wallet_id = $wallet->id;
                        $order->transaction_id = $tx->id;
                    }
                }

                $order->save();
            });

            Log::info("Midtrans Webhook: Order #{$order->id} berhasil diverifikasi otomatis via {$paymentType}.");
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            // Payment failed or expired
            $order->snap_token = null;
            $order->save();
            Log::info("Midtrans Webhook: Order #{$order->id} dibatalkan atau kedaluwarsa ({$transactionStatus}).");
        } elseif ($transactionStatus === 'pending') {
            $order->payment_gateway = 'midtrans';
            $order->payment_type = $paymentType;
            $order->save();
        }

        return [
            'success' => true,
            'status' => $transactionStatus,
            'order' => $order->fresh(),
        ];
    }

    /**
     * Resolve user OrderSetting.
     */
    protected function getSetting(User|int|null $user = null): ?OrderSetting
    {
        $userId = $user instanceof User ? $user->id : $user;

        if (! $userId) {
            $firstUser = User::first();
            $userId = $firstUser?->id;
        }

        if (! $userId) {
            return null;
        }

        return OrderSetting::where('user_id', $userId)->first();
    }
}
