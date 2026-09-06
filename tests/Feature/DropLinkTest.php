<?php

namespace Tests\Feature;

use App\Models\StoredFile;
use App\Models\UploadLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DropLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_upload_drop_link_with_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('drive.drop-links.store'), [
            'title' => 'Pengumpulan Kuitansi Pembelian',
            'description' => 'Silakan upload nota fisik',
            'expiry_preset' => '3d',
            'max_files' => 10,
            'max_file_size_mb' => 25,
        ]);

        $response->assertRedirect(route('drive.index', ['tab' => 'drops']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('upload_links', [
            'user_id' => $user->id,
            'title' => 'Pengumpulan Kuitansi Pembelian',
            'description' => 'Silakan upload nota fisik',
            'max_files' => 10,
            'max_file_size_mb' => 25,
            'is_active' => true,
        ]);

        $link = UploadLink::where('user_id', $user->id)->first();
        $this->assertNotNull($link->expires_at);
        $this->assertTrue($link->expires_at->isFuture());
    }

    public function test_create_drop_link_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('drive.drop-links.store'), [
            'title' => '',
            'expiry_preset' => 'invalid',
            'max_files' => 100, // over max 50
            'max_file_size_mb' => 999, // invalid size preset
        ]);

        $response->assertSessionHasErrors(['title', 'expiry_preset', 'max_files', 'max_file_size_mb']);
    }

    public function test_user_can_toggle_drop_link_active_status(): void
    {
        $user = User::factory()->create();
        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('drive.drop-links.toggle', $link));

        $response->assertRedirect();
        $this->assertFalse($link->fresh()->is_active);

        // Toggle back to active
        $this->actingAs($user)->patch(route('drive.drop-links.toggle', $link));
        $this->assertTrue($link->fresh()->is_active);
    }

    public function test_user_cannot_toggle_another_users_drop_link(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $link = UploadLink::factory()->create([
            'user_id' => $user1->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user2)->patch(route('drive.drop-links.toggle', $link));

        $response->assertStatus(403);
        $this->assertTrue($link->fresh()->is_active);
    }

    public function test_user_can_delete_drop_link(): void
    {
        $user = User::factory()->create();
        $link = UploadLink::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('drive.drop-links.destroy', $link));

        $response->assertRedirect(route('drive.index', ['tab' => 'drops']));
        $this->assertDatabaseMissing('upload_links', ['id' => $link->id]);
    }

    public function test_user_cannot_delete_another_users_drop_link(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $link = UploadLink::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete(route('drive.drop-links.destroy', $link));

        $response->assertStatus(403);
        $this->assertDatabaseHas('upload_links', ['id' => $link->id]);
    }

    public function test_public_user_can_view_active_drop_portal(): void
    {
        $user = User::factory()->create();
        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'title' => 'Kirim Berkas Laporan',
            'token' => 'portal-token-abc-123',
            'max_files' => 5,
            'max_file_size_mb' => 25,
            'expires_at' => now()->addDays(2),
            'is_active' => true,
        ]);

        $response = $this->get(route('drive.drop.view', ['token' => 'portal-token-abc-123']));

        $response->assertStatus(200);
        $response->assertSee('Kirim Berkas Laporan');
        $response->assertSee('SwanDrive Drop Portal');
        $response->assertSee('25 MB');
        // Ensure no confidential user data is leaked to external uploader
        $response->assertDontSee('Total Saldo');
        $response->assertDontSee('Gusti Swandana');
    }

    public function test_public_user_can_upload_file_through_drop_link(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'token' => 'upload-test-token',
            'max_files' => 5,
            'uploaded_files_count' => 0,
            'max_file_size_mb' => 10,
            'expires_at' => now()->addDays(2),
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('dokumen_kontrak.pdf', 1024, 'application/pdf');

        $response = $this->post(route('drive.drop.upload', ['token' => 'upload-test-token']), [
            'file' => $file,
            'uploader_name' => 'Budi Santoso',
            'uploader_notes' => 'Dokumen asli sudah ditandatangani',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('drop_success');

        // File is stored into Gusti's files
        $this->assertDatabaseHas('stored_files', [
            'user_id' => $user->id,
            'upload_link_id' => $link->id,
            'original_name' => 'dokumen_kontrak.pdf',
            'uploader_name' => 'Budi Santoso',
            'category' => 'document',
        ]);

        // Upload link count incremented
        $this->assertEquals(1, $link->fresh()->uploaded_files_count);

        $stored = StoredFile::where('user_id', $user->id)->first();
        Storage::disk('local')->assertExists($stored->file_path);
    }

    public function test_upload_is_rejected_when_file_exceeds_configured_size(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'token' => 'size-limit-token',
            'max_file_size_mb' => 5, // max 5 MB
            'is_active' => true,
            'expires_at' => now()->addDay(),
        ]);

        // 6 MB file (exceeds 5MB)
        $file = UploadedFile::fake()->create('video_besar.mp4', 6 * 1024);

        $response = $this->post(route('drive.drop.upload', ['token' => 'size-limit-token']), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertEquals(0, $link->fresh()->uploaded_files_count);
    }

    public function test_upload_is_rejected_when_max_files_limit_reached(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'token' => 'limit-reached-token',
            'max_files' => 3,
            'uploaded_files_count' => 3, // limit full
            'is_active' => true,
            'expires_at' => now()->addDay(),
        ]);

        $file = UploadedFile::fake()->create('file_keempat.pdf', 500);

        $response = $this->post(route('drive.drop.upload', ['token' => 'limit-reached-token']), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_is_rejected_when_link_has_expired(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'token' => 'expired-token',
            'expires_at' => now()->subHour(), // expired 1 hour ago
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('file_terlambat.pdf', 500);

        $response = $this->post(route('drive.drop.upload', ['token' => 'expired-token']), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_is_rejected_when_link_is_deactivated(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $link = UploadLink::factory()->create([
            'user_id' => $user->id,
            'token' => 'closed-token',
            'is_active' => false, // manually closed
            'expires_at' => now()->addDays(5),
        ]);

        $file = UploadedFile::fake()->create('file_tertutup.pdf', 500);

        $response = $this->post(route('drive.drop.upload', ['token' => 'closed-token']), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }
}
