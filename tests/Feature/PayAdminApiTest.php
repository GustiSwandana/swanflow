<?php

namespace Tests\Feature;

use App\Models\OrderSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayAdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_view_returns_ok(): void
    {
        $response = $this->get(route('pay.admin'));
        $response->assertStatus(200);
        $response->assertSee('Admin Access');
        $response->assertSee('PIN Admin');
    }

    public function test_pin_login_and_auth_check(): void
    {
        $user = User::factory()->create(['pin' => '654321']);
        OrderSetting::create([
            'user_id' => $user->id,
            'studio_name' => 'Lensa Art Studio',
            'admin_pin' => '654321',
        ]);

        // 1. Initial /api/admin/me should be false
        $this->getJson('/api/admin/me')->assertJson(['authenticated' => false]);

        // 2. Wrong PIN
        $this->postJson('/api/admin/login', ['pin' => '000000'])->assertStatus(401);

        // 3. Correct PIN
        $loginRes = $this->postJson('/api/admin/login', ['pin' => '654321']);
        $loginRes->assertStatus(200)->assertJson(['success' => true]);

        // 4. /api/admin/me should now be true
        $this->getJson('/api/admin/me')->assertJson(['authenticated' => true]);

        // 5. Logout
        $this->postJson('/api/admin/logout')->assertStatus(200);
        $this->getJson('/api/admin/me')->assertJson(['authenticated' => false]);
    }

    public function test_admin_settings_crud(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Get Settings
        $getRes = $this->getJson('/api/admin/settings');
        $getRes->assertStatus(200);

        // Update Settings
        $updateRes = $this->postJson('/api/admin/settings', [
            'studio_name' => 'Swan Studio Premium',
            'bank_instructions' => 'Transfer via BCA atau Mandiri.',
            'admin_pin' => '998877',
        ]);
        $updateRes->assertStatus(200)->assertJsonPath('settings.studio_name', 'Swan Studio Premium');

        $this->assertDatabaseHas('order_settings', [
            'studio_name' => 'Swan Studio Premium',
            'admin_pin' => '998877',
        ]);
    }

    public function test_admin_projects_lifecycle_and_swanflow_financial_integration(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 1000000,
        ]);
        $this->actingAs($user);

        // 1. Create project
        $createRes = $this->postJson('/api/admin/projects', [
            'client_name' => 'Dimas & Sarah',
            'client_phone' => '081234567890',
            'project_title' => 'Wedding Highlight Video',
            'amount' => 5000000,
            'discount' => 1000000,
            'discount_label' => 'DP Promo',
            'gdrive_url' => 'https://drive.google.com/test-order',
            'notes' => 'Resolution 4K UHD',
            'is_free' => false,
        ]);

        $createRes->assertStatus(200)->assertJsonPath('success', true);
        $orderId = $createRes->json('project.id');
        $token = $createRes->json('project.token');

        $this->assertNotNull($orderId);
        $this->assertNotNull($token);

        // 2. Client views project (gdrive_url MUST be null because unpaid)
        $clientRes = $this->getJson('/api/p/'.$token);
        $clientRes->assertStatus(200);
        $clientRes->assertJsonPath('project.status', 'UNPAID');
        $clientRes->assertJsonPath('project.gdrive_url', null);

        // 3. Update project (custom URL slug)
        $updateRes = $this->putJson('/api/admin/projects/'.$orderId, [
            'client_name' => 'Dimas & Sarah Updated',
            'client_phone' => '081234567890',
            'project_title' => 'Wedding Highlight Video 2026',
            'token' => 'dimas-sarah-wedding-2026',
            'amount' => 5000000,
            'discount' => 1000000,
            'discount_label' => 'DP Promo',
            'status' => 'UNPAID',
            'gdrive_url' => 'https://drive.google.com/test-order',
            'notes' => 'Resolution 4K UHD',
            'is_free' => false,
        ]);
        $updateRes->assertStatus(200)->assertJsonPath('project.token', 'dimas-sarah-wedding-2026');

        // 4. Admin approves payment -> SwanFlow Financial integration triggers!
        $verifyRes = $this->postJson('/api/admin/projects/'.$orderId.'/verify', [
            'status' => 'APPROVED',
        ]);
        $verifyRes->assertStatus(200);

        // Check that SwanFlow recorded an Income Transaction of Rp 4.000.000 (5jt - 1jt discount)
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 4000000,
        ]);

        // Check that client can now view GDrive URL
        $clientUnlocked = $this->getJson('/api/p/dimas-sarah-wedding-2026');
        $clientUnlocked->assertStatus(200);
        $clientUnlocked->assertJsonPath('project.status', 'APPROVED');
        $clientUnlocked->assertJsonPath('project.gdrive_url', 'https://drive.google.com/test-order');

        // 5. Delete project
        $delRes = $this->deleteJson('/api/admin/projects/'.$orderId);
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('orders', ['id' => $orderId]);
    }

    public function test_admin_bank_accounts_crud(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create bank account
        $res = $this->postJson('/api/admin/bank-accounts', [
            'bank_name' => 'Bank Mandiri',
            'account_number' => '1370012345678',
            'account_name' => 'SWANFLOW STUDIO',
        ]);
        $res->assertStatus(200);
        $accId = $res->json('bank_account.id');

        // 2. List bank accounts
        $listRes = $this->getJson('/api/admin/bank-accounts');
        $listRes->assertStatus(200)->assertJsonPath('bank_accounts.0.bank_name', 'Bank Mandiri');

        // 3. Delete bank account
        $delRes = $this->deleteJson('/api/admin/bank-accounts/'.$accId);
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('order_bank_accounts', ['id' => $accId]);
    }
}
