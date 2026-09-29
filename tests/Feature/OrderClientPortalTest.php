<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\Order;
use App\Models\OrderSetting;
use App\Models\StoredFile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderClientPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_orders_index(): void
    {
        $user = User::factory()->create();
        Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 1000000,
        ]);

        $response = $this->actingAs($user)->get(route('orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Penyerahan File');
    }

    public function test_user_can_create_new_order_and_link(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 500000,
        ]);

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'client_name' => 'Dimas & Sarah',
            'client_phone' => '081234567890',
            'project_title' => 'Wedding Documentation 2026',
            'amount' => 3000000,
            'discount' => 500000,
            'discount_label' => 'Early Bird Promo',
            'is_free' => false,
            'gdrive_url' => 'https://drive.google.com/test',
            'wallet_id' => $wallet->id,
            'notes' => 'Link aktif 30 hari',
            'custom_token' => 'dimas-sarah-wedding',
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'token' => 'dimas-sarah-wedding',
            'client_name' => 'Dimas & Sarah',
            'amount' => 3000000,
            'discount' => 500000,
            'status' => 'unpaid',
        ]);
    }

    public function test_client_can_view_public_portal_and_json_data(): void
    {
        $user = User::factory()->create();
        OrderSetting::create([
            'user_id' => $user->id,
            'studio_name' => 'Gusti Creative Studio',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'token' => 'test-token-123',
            'client_name' => 'Andi Wijaya',
            'project_title' => 'Wisuda Session',
            'amount' => 1500000,
            'discount' => 0,
            'status' => 'unpaid',
            'gdrive_url' => 'https://drive.google.com/download/test',
        ]);

        // 1. Web view
        $viewRes = $this->get(route('orders.portal', ['token' => 'test-token-123']));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Wisuda Session');
        $viewRes->assertSee('Andi Wijaya');

        // 2. API data
        $apiRes = $this->get(route('api.orders.portal.data', ['token' => 'test-token-123']));
        $apiRes->assertStatus(200);
        $apiRes->assertJsonPath('project.client_name', 'Andi Wijaya');
        $apiRes->assertJsonPath('project.status', 'UNPAID');
        // Zero Leak Guarantee: gdrive_url must NOT be exposed before payment/verification
        $apiRes->assertJsonPath('project.gdrive_url', null);
    }

    public function test_client_can_upload_payment_proof(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'token' => 'proof-upload-token',
            'client_name' => 'Rina',
            'project_title' => 'Engagement Photoshoot',
            'amount' => 2000000,
            'status' => 'unpaid',
        ]);

        $file = UploadedFile::fake()->image('struk_transfer.jpg');

        $response = $this->post(route('api.orders.portal.upload', ['token' => 'proof-upload-token']), [
            'proof' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('project.status', 'PENDING_VERIFICATION');

        $order->refresh();
        $this->assertEquals('waiting_verification', $order->status);
        $this->assertNotNull($order->payment_proof_path);
        Storage::disk('public')->assertExists($order->payment_proof_path);
    }

    public function test_admin_can_verify_payment_and_auto_record_transaction_to_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Bisnis',
            'type' => 'bank',
            'balance' => 1000000,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'token' => 'verify-order-token',
            'client_name' => 'Budi Santoso',
            'project_title' => 'Commercial Video',
            'amount' => 5000000,
            'discount' => 1000000,
            'wallet_id' => $wallet->id,
            'status' => 'waiting_verification',
        ]);

        $response = $this->actingAs($user)->post(route('orders.verify', $order), [
            'wallet_id' => $wallet->id,
            'record_transaction' => true,
        ]);

        $response->assertRedirect(route('orders.index'));

        $order->refresh();
        $this->assertEquals('verified', $order->status);
        $this->assertNotNull($order->transaction_id);

        // Wallet balance must increase by 4,000,000 (5,000,000 - 1,000,000)
        $wallet->refresh();
        $this->assertEquals(5000000, (float) $wallet->balance);

        $this->assertDatabaseHas('transactions', [
            'id' => $order->transaction_id,
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 4000000,
            'type' => 'income',
        ]);
    }

    public function test_admin_can_reject_proof_with_reason(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'token' => 'reject-order-token',
            'client_name' => 'Citra',
            'project_title' => 'Birthday Party',
            'amount' => 1000000,
            'status' => 'waiting_verification',
        ]);

        $response = $this->actingAs($user)->post(route('orders.reject', $order), [
            'rejection_reason' => 'Nominal transfer kurang Rp 50.000',
        ]);

        $response->assertRedirect(route('orders.index'));

        $order->refresh();
        $this->assertEquals('rejected', $order->status);
        $this->assertEquals('Nominal transfer kurang Rp 50.000', $order->rejection_reason);

        // Verify client API sees REJECTED with reason
        $apiRes = $this->get(route('api.orders.portal.data', ['token' => 'reject-order-token']));
        $apiRes->assertStatus(200);
        $apiRes->assertJsonPath('project.status', 'REJECTED');
        $apiRes->assertJsonPath('project.reject_reason', 'Nominal transfer kurang Rp 50.000');
    }

    public function test_user_can_create_order_linked_with_swandrive_folder(): void
    {
        $user = User::factory()->create();
        $folder = Folder::create([
            'user_id' => $user->id,
            'name' => 'Dokumentasi Bali Wedding',
            'is_public' => false,
            'share_token' => null,
        ]);

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'client_name' => 'Reza & Maya',
            'project_title' => 'Wedding Highlights Bali',
            'amount' => 5000000,
            'folder_id' => $folder->id,
        ]);

        $response->assertRedirect(route('orders.index'));

        // Folder must be auto-published with share_token
        $folder->refresh();
        $this->assertTrue($folder->is_public);
        $this->assertNotNull($folder->share_token);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertEquals($folder->id, $order->folder_id);

        // Before payment: download_url and zip_url must be null (Zero Leak Guarantee)
        $portalRes = $this->get(route('api.orders.portal.data', ['token' => $order->token]));
        $portalRes->assertStatus(200);
        $portalRes->assertJsonPath('project.download_url', null);
        $portalRes->assertJsonPath('project.zip_url', null);
        $portalRes->assertJsonPath('project.deliverable_type', 'folder');
        $portalRes->assertJsonPath('project.folder_name', 'Dokumentasi Bali Wedding');

        // After verified: download_url and zip_url point to SwanDrive folder routes
        $order->update(['status' => 'verified']);
        $verifiedRes = $this->get(route('api.orders.portal.data', ['token' => $order->token]));
        $verifiedRes->assertStatus(200);
        $this->assertEquals(
            route('drive.shared.folder.view', ['token' => $folder->share_token]),
            $verifiedRes->json('project.download_url')
        );
        $this->assertEquals(
            route('drive.shared.folder.zip', ['token' => $folder->share_token]),
            $verifiedRes->json('project.zip_url')
        );
    }

    public function test_user_can_create_order_linked_with_swandrive_file(): void
    {
        $user = User::factory()->create();
        $file = StoredFile::create([
            'user_id' => $user->id,
            'title' => 'Logo Master Vector.ai',
            'original_name' => 'Logo Master Vector.ai',
            'file_path' => 'files/test.ai',
            'mime_type' => 'application/illustrator',
            'extension' => 'ai',
            'size_bytes' => 10485760, // 10MB
            'category' => 'other',
            'is_public' => false,
            'share_token' => null,
        ]);

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'client_name' => 'PT Brand Nusantara',
            'project_title' => 'Corporate Identity Kit',
            'amount' => 2500000,
            'stored_file_id' => $file->id,
        ]);

        $response->assertRedirect(route('orders.index'));

        // StoredFile must be auto-published with share_token
        $file->refresh();
        $this->assertTrue($file->is_public);
        $this->assertNotNull($file->share_token);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertEquals($file->id, $order->stored_file_id);

        // Before payment: download_url null
        $portalRes = $this->get(route('api.orders.portal.data', ['token' => $order->token]));
        $portalRes->assertStatus(200);
        $portalRes->assertJsonPath('project.download_url', null);
        $portalRes->assertJsonPath('project.deliverable_type', 'file');
        $portalRes->assertJsonPath('project.file_name', 'Logo Master Vector.ai');

        // After verified: direct download route
        $order->update(['status' => 'verified']);
        $verifiedRes = $this->get(route('api.orders.portal.data', ['token' => $order->token]));
        $verifiedRes->assertStatus(200);
        $this->assertEquals(
            route('drive.shared.download', ['token' => $file->share_token]),
            $verifiedRes->json('project.download_url')
        );
    }

    public function test_orders_index_with_swandrive_prefill_params(): void
    {
        $user = User::factory()->create();
        $folder = Folder::create([
            'user_id' => $user->id,
            'name' => 'Folder Prefill Test',
            'is_public' => false,
        ]);

        $response = $this->actingAs($user)->get(route('orders.index', [
            'folder_id' => $folder->id,
            'create' => '1',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('preselectedFolderId', $folder->id);
        $response->assertViewHas('autoCreate', true);
    }

    public function test_user_can_update_orders_settings_with_midtrans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('orders.settings.update'), [
            'studio_name' => 'Lensa Art Studio Bali',
            'bank_instructions' => 'Bayar via Midtrans QRIS atau VA.',
            'midtrans_enabled' => '1',
            'midtrans_server_key' => 'SB-Mid-server-test-999',
            'midtrans_client_key' => 'SB-Mid-client-test-999',
            'midtrans_is_production' => '1',
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertDatabaseHas('order_settings', [
            'user_id' => $user->id,
            'studio_name' => 'Lensa Art Studio Bali',
            'midtrans_enabled' => true,
            'midtrans_server_key' => 'SB-Mid-server-test-999',
            'midtrans_client_key' => 'SB-Mid-client-test-999',
            'midtrans_is_production' => true,
        ]);
    }
}
