<?php

namespace Tests\Feature;

use App\Models\StoredFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DriveTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('drive.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_drive_index_and_files(): void
    {
        $user = User::factory()->create();
        StoredFile::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('drive.index'));

        $response->assertStatus(200);
        $response->assertViewIs('drive.index');
        $response->assertSee('SwanDrive');
        $response->assertSee('Ruang Penyimpanan Terpakai');
    }

    public function test_user_can_upload_file_to_private_storage(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('laporan_keuangan_2026.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($user)->post(route('drive.store'), [
            'file' => $file,
            'title' => 'Laporan Tahunan 2026',
            'notes' => 'Arsip audit keuangan penting',
        ]);

        $response->assertRedirect(route('drive.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stored_files', [
            'user_id' => $user->id,
            'title' => 'Laporan Tahunan 2026',
            'original_name' => 'laporan_keuangan_2026.pdf',
            'extension' => 'pdf',
            'category' => 'document',
            'is_public' => false,
        ]);

        $stored = StoredFile::where('user_id', $user->id)->first();
        $this->assertNotNull($stored);
        Storage::disk('local')->assertExists($stored->file_path);
    }

    public function test_upload_validation_requires_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('drive.store'), [
            'title' => 'Berkas Kosong',
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_user_can_download_their_own_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $fakeFile = UploadedFile::fake()->create('dokumen.pdf', 500);
        $path = $fakeFile->store('drive/'.$user->id, 'local');

        $storedFile = StoredFile::factory()->create([
            'user_id' => $user->id,
            'original_name' => 'dokumen.pdf',
            'file_path' => $path,
            'download_count' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('drive.download', $storedFile));

        $response->assertStatus(200);
        $this->assertEquals(1, $storedFile->fresh()->download_count);
    }

    public function test_user_cannot_download_another_users_file(): void
    {
        Storage::fake('local');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $fakeFile = UploadedFile::fake()->create('rahasia.pdf', 500);
        $path = $fakeFile->store('drive/'.$user1->id, 'local');

        $storedFile = StoredFile::factory()->create([
            'user_id' => $user1->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($user2)->get(route('drive.download', $storedFile));

        $response->assertStatus(403);
    }

    public function test_user_can_toggle_share_status(): void
    {
        $user = User::factory()->create();
        $storedFile = StoredFile::factory()->create([
            'user_id' => $user->id,
            'is_public' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('drive.share.toggle', $storedFile));

        $response->assertRedirect();
        $this->assertTrue($storedFile->fresh()->is_public);

        // Toggle back
        $this->actingAs($user)->patch(route('drive.share.toggle', $storedFile));
        $this->assertFalse($storedFile->fresh()->is_public);
    }

    public function test_public_user_can_view_and_download_shared_file_when_active(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $fakeFile = UploadedFile::fake()->create('foto_nota.png', 300);
        $path = $fakeFile->store('drive/'.$user->id, 'local');

        $storedFile = StoredFile::factory()->create([
            'user_id' => $user->id,
            'original_name' => 'foto_nota.png',
            'file_path' => $path,
            'is_public' => true,
            'share_token' => 'test-unique-share-token-12345',
            'download_count' => 0,
        ]);

        // Unauthenticated guest can view public share page
        $viewResponse = $this->get(route('drive.shared.view', ['token' => 'test-unique-share-token-12345']));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($storedFile->title);
        $viewResponse->assertSee('SwanDrive File Transfer');
        // Ensure no confidential user balances are leaked on the public page
        $viewResponse->assertDontSee('Gusti Swandana');
        $viewResponse->assertDontSee('Total Saldo');

        // Unauthenticated guest can download
        $downloadResponse = $this->get(route('drive.shared.download', ['token' => 'test-unique-share-token-12345']));
        $downloadResponse->assertStatus(200);
        $this->assertEquals(1, $storedFile->fresh()->download_count);
    }

    public function test_public_user_cannot_access_shared_file_when_inactive(): void
    {
        $user = User::factory()->create();
        $storedFile = StoredFile::factory()->create([
            'user_id' => $user->id,
            'is_public' => false,
            'share_token' => 'inactive-share-token-999',
        ]);

        $response = $this->get(route('drive.shared.view', ['token' => 'inactive-share-token-999']));
        $response->assertStatus(404);

        $downloadResponse = $this->get(route('drive.shared.download', ['token' => 'inactive-share-token-999']));
        $downloadResponse->assertStatus(404);
    }

    public function test_user_can_delete_file_and_it_removes_physical_storage(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $fakeFile = UploadedFile::fake()->create('hapus_saya.zip', 200);
        $path = $fakeFile->store('drive/'.$user->id, 'local');

        $storedFile = StoredFile::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
        ]);

        Storage::disk('local')->assertExists($path);

        $response = $this->actingAs($user)->delete(route('drive.destroy', $storedFile));

        $response->assertRedirect(route('drive.index'));
        $this->assertDatabaseMissing('stored_files', ['id' => $storedFile->id]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_user_cannot_delete_another_users_file(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $storedFile = StoredFile::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete(route('drive.destroy', $storedFile));

        $response->assertStatus(403);
        $this->assertDatabaseHas('stored_files', ['id' => $storedFile->id]);
    }

    public function test_drive_category_filtering_and_search(): void
    {
        $user = User::factory()->create();

        StoredFile::factory()->create([
            'user_id' => $user->id,
            'title' => 'Proposal Bisnis',
            'category' => 'document',
        ]);
        StoredFile::factory()->create([
            'user_id' => $user->id,
            'title' => 'Foto Bukti Transfer',
            'category' => 'image',
        ]);

        // Filter document
        $docResponse = $this->actingAs($user)->get(route('drive.index', ['category' => 'document']));
        $docResponse->assertStatus(200);
        $docResponse->assertSee('Proposal Bisnis');
        $docResponse->assertDontSee('Foto Bukti Transfer');

        // Search
        $searchResponse = $this->actingAs($user)->get(route('drive.index', ['search' => 'Transfer']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Foto Bukti Transfer');
        $searchResponse->assertDontSee('Proposal Bisnis');
    }

    public function test_user_can_update_storage_quota(): void
    {
        $user = User::factory()->create(['storage_quota_mb' => 500]);

        $response = $this->actingAs($user)->patch(route('drive.quota.update'), [
            'storage_quota_mb' => 2048,
        ]);

        $response->assertRedirect(route('drive.index', ['tab' => 'files']));
        $response->assertSessionHas('success');

        $this->assertEquals(2048, $user->fresh()->storage_quota_mb);
    }
}
