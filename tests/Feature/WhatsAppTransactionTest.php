<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_verify_endpoint_returns_active_status(): void
    {
        $response = $this->getJson('/api/webhook/whatsapp');
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'active',
                'service' => 'SwanFlow WhatsApp Gateway Webhook',
            ]);
    }

    public function test_meta_webhook_challenge_verification(): void
    {
        putenv('WHATSAPP_VERIFY_TOKEN=secret_token_123');
        $_ENV['WHATSAPP_VERIFY_TOKEN'] = 'secret_token_123';

        $response = $this->get('/api/webhook/whatsapp?hub_mode=subscribe&hub_verify_token=secret_token_123&hub_challenge=1158201444');
        $response->assertStatus(200);
        $this->assertEquals('1158201444', $response->getContent());

        // Invalid token returns 403
        $invalid = $this->get('/api/webhook/whatsapp?hub_mode=subscribe&hub_verify_token=wrong&hub_challenge=1158201444');
        $invalid->assertStatus(403);

        putenv('WHATSAPP_VERIFY_TOKEN');
        unset($_ENV['WHATSAPP_VERIFY_TOKEN']);
    }

    public function test_expense_transaction_via_whatsapp_text(): void
    {
        $user = User::factory()->create([
            'whatsapp_number' => '6281234567890',
        ]);

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 100000,
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => 'Makanan & Minuman',
            'type' => 'expense',
            'icon' => 'utensils',
            'color' => '#EF4444',
        ]);

        $payload = [
            'sender' => '6281234567890',
            'message' => 'kopi kenangan 25rb bca',
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $data = $response->json();
        $this->assertStringContainsString('Pengeluaran Berhasil Dicatat', $data['reply']);
        $this->assertStringContainsString('Rp 25.000', $data['reply']);
        $this->assertStringContainsString('Rp 75.000', $data['reply']);

        // Check database
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 25000,
            'type' => 'expense',
        ]);

        // Check wallet balance
        $this->assertEquals(75000, (float) $wallet->fresh()->balance);
    }

    public function test_income_transaction_via_whatsapp_text(): void
    {
        $user = User::factory()->create([
            'whatsapp_number' => '6281234567890',
        ]);

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Mandiri',
            'type' => 'bank',
            'balance' => 500000,
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => 'Freelance & Bonus',
            'type' => 'income',
            'icon' => 'briefcase',
            'color' => '#10B981',
        ]);

        $payload = [
            'sender' => '6281234567890',
            'message' => 'gaji freelance 1.5jt mandiri',
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 1500000,
            'type' => 'income',
        ]);

        $this->assertEquals(2000000, (float) $wallet->fresh()->balance);
    }

    public function test_balance_query_command(): void
    {
        $user = User::factory()->create([
            'whatsapp_number' => '6281234567890',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 1500000,
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 250000,
        ]);

        $payload = [
            'sender' => '6281234567890',
            'message' => 'saldo',
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200);
        $reply = $response->json('reply');

        $this->assertStringContainsString('Ringkasan Saldo Dompet SwanFlow', $reply);
        $this->assertStringContainsString('*BCA:* Rp 1.500.000', $reply);
        $this->assertStringContainsString('*GoPay:* Rp 250.000', $reply);
        $this->assertStringContainsString('Total Net Worth:* Rp 1.750.000', $reply);

        // Ensure no transaction was recorded
        $this->assertEquals(0, Transaction::count());
    }

    public function test_help_command(): void
    {
        $user = User::factory()->create([
            'whatsapp_number' => '6281234567890',
        ]);

        $payload = [
            'sender' => '6281234567890',
            'message' => 'bantuan',
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200);
        $reply = $response->json('reply');
        $this->assertStringContainsString('Panduan Chat SwanFlow Tracker', $reply);
    }

    public function test_unregistered_sender_rejection(): void
    {
        // Two users in DB so auto-bind doesn't trigger
        User::factory()->create(['whatsapp_number' => '62811111111']);
        User::factory()->create(['whatsapp_number' => '62822222222']);

        $payload = [
            'sender' => '6289999999999',
            'message' => 'kopi 20rb',
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'failed',
            ]);

        $this->assertStringContainsString('Akses Ditolak', $response->json('reply'));
    }
}
