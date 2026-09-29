<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Wallet $wallet;

    protected OrderSetting $setting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'gusti@swanflow.test',
        ]);

        $this->wallet = Wallet::create([
            'user_id' => $this->user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 1000000.00,
            'is_active' => true,
        ]);

        $this->setting = OrderSetting::create([
            'user_id' => $this->user->id,
            'studio_name' => 'Swan Studio',
            'midtrans_enabled' => true,
            'midtrans_server_key' => 'SB-Mid-server-test-12345678',
            'midtrans_client_key' => 'SB-Mid-client-test-87654321',
            'midtrans_is_production' => false,
        ]);
    }

    public function test_client_can_request_midtrans_snap_token(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'token' => 'order-snap-token-test',
            'client_name' => 'John Doe',
            'client_phone' => '081234567890',
            'project_title' => 'Wedding Highlight 4K',
            'amount' => 500000.00,
            'discount' => 50000.00,
            'status' => 'unpaid',
            'wallet_id' => $this->wallet->id,
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'mock-snap-token-abc-xyz',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mock-token',
            ], 201),
        ]);

        $response = $this->postJson(route('api.orders.portal.snap-token', ['token' => $order->token]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'token' => 'mock-snap-token-abc-xyz',
            'client_key' => 'SB-Mid-client-test-87654321',
            'is_production' => false,
        ]);

        $order->refresh();
        $this->assertEquals('mock-snap-token-abc-xyz', $order->snap_token);
        $this->assertEquals('midtrans', $order->payment_gateway);
    }

    public function test_snap_token_cannot_be_requested_if_already_verified(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'token' => 'order-already-verified',
            'client_name' => 'Jane Doe',
            'project_title' => 'Prewedding Photo',
            'amount' => 300000.00,
            'status' => 'verified',
        ]);

        $response = $this->postJson(route('api.orders.portal.snap-token', ['token' => $order->token]));

        $response->assertStatus(400);
        $response->assertJsonFragment(['status' => 'APPROVED']);
    }

    public function test_midtrans_webhook_settlement_verifies_order_and_creates_wallet_transaction(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'token' => 'order-webhook-test',
            'client_name' => 'Andi Wijaya',
            'project_title' => 'Commercial Video Clip',
            'amount' => 1500000.00,
            'discount' => 0.00,
            'status' => 'unpaid',
            'wallet_id' => $this->wallet->id,
        ]);

        $serverKey = 'SB-Mid-server-test-12345678';
        $midtransOrderId = 'SWAN-'.$order->id.'-1727618000-abcd';
        $statusCode = '200';
        $grossAmount = '1500000.00';
        $signature = hash('sha512', $midtransOrderId.$statusCode.$grossAmount.$serverKey);

        $payload = [
            'order_id' => $midtransOrderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-trx-999888',
        ];

        $initialBalance = (float) $this->wallet->balance;

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'transaction_status' => 'settlement',
        ]);

        $order->refresh();
        $this->assertEquals('verified', $order->status);
        $this->assertEquals('midtrans', $order->payment_gateway);
        $this->assertEquals('qris', $order->payment_type);
        $this->assertEquals('midtrans-trx-999888', $order->payment_reference);
        $this->assertNotNull($order->verified_at);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($order->transaction_id);

        // Verify Wallet balance incremented
        $this->wallet->refresh();
        $this->assertEquals($initialBalance + 1500000.00, (float) $this->wallet->balance);

        // Verify Income Transaction recorded
        $this->assertDatabaseHas('transactions', [
            'id' => $order->transaction_id,
            'user_id' => $this->user->id,
            'wallet_id' => $this->wallet->id,
            'amount' => 1500000.00,
        ]);
    }

    public function test_midtrans_webhook_is_idempotent(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'token' => 'order-idempotent-test',
            'client_name' => 'Rina S',
            'project_title' => 'Event Coverage',
            'amount' => 1000000.00,
            'status' => 'unpaid',
            'wallet_id' => $this->wallet->id,
        ]);

        $serverKey = 'SB-Mid-server-test-12345678';
        $midtransOrderId = 'SWAN-'.$order->id.'-1727618000-xyz';
        $statusCode = '200';
        $grossAmount = '1000000.00';
        $signature = hash('sha512', $midtransOrderId.$statusCode.$grossAmount.$serverKey);

        $payload = [
            'order_id' => $midtransOrderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-idempotent',
        ];

        // First webhook call
        $res1 = $this->postJson(route('midtrans.webhook'), $payload);
        $res1->assertStatus(200);

        $order->refresh();
        $firstTxId = $order->transaction_id;
        $this->assertNotNull($firstTxId);

        $walletBalanceAfterFirst = (float) $this->wallet->fresh()->balance;

        // Second duplicate webhook call
        $res2 = $this->postJson(route('midtrans.webhook'), $payload);
        $res2->assertStatus(200);

        // Ensure wallet balance and transaction count did not increase
        $this->assertEquals($walletBalanceAfterFirst, (float) $this->wallet->fresh()->balance);
        $this->assertEquals(1, Transaction::where('id', $firstTxId)->count());
    }

    public function test_midtrans_webhook_rejects_invalid_signature(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'token' => 'order-tampered-test',
            'client_name' => 'Hacker',
            'project_title' => 'Tampered Project',
            'amount' => 500000.00,
            'status' => 'unpaid',
        ]);

        $payload = [
            'order_id' => 'SWAN-'.$order->id.'-123456',
            'status_code' => '200',
            'gross_amount' => '500000.00',
            'signature_key' => 'completely-fake-invalid-signature',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'handled',
        ]);
        $order->refresh();
        $this->assertEquals('unpaid', $order->status);
    }
}
