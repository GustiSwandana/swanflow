<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\StoredFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DriveFolderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_folder_at_root(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('drive.folders.store'), [
            'name' => 'Dokumen Bisnis',
            'color' => 'indigo',
            'description' => 'Arsip proposal dan kontrak',
            'is_public' => '1',
        ]);

        $response->assertRedirect(route('drive.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('folders', [
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'Dokumen Bisnis',
            'color' => 'indigo',
            'description' => 'Arsip proposal dan kontrak',
            'is_public' => true,
        ]);

        $folder = Folder::where('user_id', $user->id)->first();
        $this->assertNotNull($folder->share_token);
    }

    public function test_user_can_create_subfolder(): void
    {
        $user = User::factory()->create();
        $parent = Folder::factory()->create(['user_id' => $user->id, 'name' => 'Parent Folder']);

        $response = $this->actingAs($user)->post(route('drive.folders.store'), [
            'parent_id' => $parent->id,
            'name' => 'Subfolder 2026',
            'color' => 'teal',
        ]);

        $response->assertRedirect(route('drive.index', ['folder_id' => $parent->id]));

        $this->assertDatabaseHas('folders', [
            'user_id' => $user->id,
            'parent_id' => $parent->id,
            'name' => 'Subfolder 2026',
        ]);
    }

    public function test_user_can_update_folder_details(): void
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->create([
            'user_id' => $user->id,
            'name' => 'Nama Lama',
            'color' => 'teal',
        ]);

        $response = $this->actingAs($user)->patch(route('drive.folders.update', $folder), [
            'name' => 'Nama Baru',
            'color' => 'rose',
            'description' => 'Deskripsi diperbarui',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('folders', [
            'id' => $folder->id,
            'name' => 'Nama Baru',
            'color' => 'rose',
            'description' => 'Deskripsi diperbarui',
        ]);
    }

    public function test_user_cannot_modify_another_users_folder(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->patch(route('drive.folders.update', $folder), [
            'name' => 'Hacked Name',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('folders', [
            'id' => $folder->id,
            'name' => 'Hacked Name',
        ]);
    }

    public function test_user_can_delete_folder_recursively_with_files_and_children(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $folder = Folder::factory()->create(['user_id' => $user->id, 'name' => 'Root Folder']);
        $subfolder = Folder::factory()->create(['user_id' => $user->id, 'parent_id' => $folder->id, 'name' => 'Sub Folder']);

        $fileInRoot = StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'file_path' => 'drive/'.$user->id.'/file1.txt',
        ]);
        Storage::disk('local')->put($fileInRoot->file_path, 'Root file content');

        $fileInSub = StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $subfolder->id,
            'file_path' => 'drive/'.$user->id.'/file2.txt',
        ]);
        Storage::disk('local')->put($fileInSub->file_path, 'Sub file content');

        $response = $this->actingAs($user)->delete(route('drive.folders.destroy', $folder));

        $response->assertRedirect(route('drive.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('folders', ['id' => $folder->id]);
        $this->assertDatabaseMissing('folders', ['id' => $subfolder->id]);
        $this->assertDatabaseMissing('stored_files', ['id' => $fileInRoot->id]);
        $this->assertDatabaseMissing('stored_files', ['id' => $fileInSub->id]);

        Storage::disk('local')->assertMissing($fileInRoot->file_path);
        Storage::disk('local')->assertMissing($fileInSub->file_path);
    }

    public function test_user_can_toggle_folder_public_sharing(): void
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $user->id, 'is_public' => false]);

        $response = $this->actingAs($user)->patchJson(route('drive.folders.share.toggle', $folder));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_public' => true,
        ]);

        $folder->refresh();
        $this->assertTrue($folder->is_public);
    }

    public function test_guest_can_view_public_shared_folder(): void
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->public()->create(['user_id' => $user->id, 'name' => 'Project Alpha']);
        StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'title' => 'Blueprint Alpha',
            'is_public' => true,
        ]);

        $response = $this->get(route('drive.shared.folder.view', $folder->share_token));

        $response->assertOk();
        $response->assertViewIs('drive.share_folder');
        $response->assertSee('Project Alpha');
        $response->assertSee('Blueprint Alpha');
    }

    public function test_guest_cannot_view_private_shared_folder(): void
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $user->id, 'is_public' => false]);

        $response = $this->get(route('drive.shared.folder.view', $folder->share_token));

        $response->assertStatus(404);
    }

    public function test_guest_can_preview_and_download_file_from_public_folder(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $folder = Folder::factory()->public()->create(['user_id' => $user->id]);

        $file = StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'file_path' => 'drive/'.$user->id.'/doc.txt',
            'original_name' => 'doc.txt',
            'mime_type' => 'text/plain',
        ]);
        Storage::disk('local')->put($file->file_path, 'Hello world shared folder');

        // Test preview
        $previewRes = $this->get(route('drive.shared.folder.preview', ['token' => $folder->share_token, 'file' => $file]));
        $previewRes->assertOk();
        $this->assertEquals('Hello world shared folder', $previewRes->streamedContent());

        // Test download
        $downloadRes = $this->get(route('drive.shared.folder.download', ['token' => $folder->share_token, 'file' => $file]));
        $downloadRes->assertOk();
    }

    public function test_guest_can_download_public_folder_as_zip(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $folder = Folder::factory()->public()->create(['user_id' => $user->id, 'name' => 'Assets']);

        $file1 = StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'file_path' => 'drive/'.$user->id.'/asset1.txt',
            'original_name' => 'asset1.txt',
        ]);
        Storage::disk('local')->put($file1->file_path, 'Asset 1 content');

        $file2 = StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'file_path' => 'drive/'.$user->id.'/asset2.txt',
            'original_name' => 'asset2.txt',
        ]);
        Storage::disk('local')->put($file2->file_path, 'Asset 2 content');

        $response = $this->get(route('drive.shared.folder.zip', $folder->share_token));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'zip') || str_contains($response->headers->get('content-disposition'), '.zip'));
    }

    public function test_user_can_move_file_between_folders(): void
    {
        $user = User::factory()->create();
        $folderA = Folder::factory()->create(['user_id' => $user->id, 'name' => 'Folder A']);
        $folderB = Folder::factory()->create(['user_id' => $user->id, 'name' => 'Folder B']);

        $file = StoredFile::factory()->create([
            'user_id' => $user->id,
            'folder_id' => $folderA->id,
            'title' => 'File to Move',
        ]);

        // Move to Folder B
        $response = $this->actingAs($user)->patch(route('drive.file.move', $file), [
            'folder_id' => $folderB->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stored_files', [
            'id' => $file->id,
            'folder_id' => $folderB->id,
        ]);

        // Move back to root (null)
        $response = $this->actingAs($user)->patch(route('drive.file.move', $file), [
            'folder_id' => '',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stored_files', [
            'id' => $file->id,
            'folder_id' => null,
        ]);
    }

    public function test_user_can_upload_file_into_folder(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $user->id, 'name' => 'Financials']);

        $fakeFile = UploadedFile::fake()->create('report.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('drive.store'), [
            'file' => $fakeFile,
            'title' => 'Financial Report',
            'folder_id' => $folder->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stored_files', [
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'title' => 'Financial Report',
        ]);
    }
}
