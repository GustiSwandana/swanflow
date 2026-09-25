<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\StoredFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DriveController extends Controller
{
    /**
     * Display the user's SwanDrive dashboard, folders, and stored files.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeCategory = $request->query('category', 'all');
        $search = $request->query('search');
        $folderId = $request->query('folder_id');

        $currentFolder = null;
        $breadcrumbs = collect();

        if ($folderId) {
            $currentFolder = $user->folders()->with('parent')->findOrFail($folderId);
            $breadcrumbs = $currentFolder->breadcrumbs();
            $folders = $currentFolder->children()->withCount('files')->latest()->get();
            $query = $currentFolder->files();
        } else {
            // Root level folders
            $folders = $user->folders()->whereNull('parent_id')->withCount('files')->latest()->get();
            $query = $user->storedFiles();

            // In root level, if not searching and category is 'all', show root files (files without folder)
            if (empty($search) && $activeCategory === 'all') {
                $query->whereNull('folder_id');
            }
        }

        if ($activeCategory !== 'all' && in_array($activeCategory, ['document', 'image', 'archive', 'other'])) {
            $query->where('category', $activeCategory);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $files = $query->latest()->get();

        $allFiles = $user->storedFiles()->get(['category', 'size_bytes']);
        $totalBytes = $allFiles->sum('size_bytes');
        $totalFiles = $allFiles->count();

        $categoryCounts = [
            'all' => $totalFiles,
            'document' => $allFiles->where('category', 'document')->count(),
            'image' => $allFiles->where('category', 'image')->count(),
            'archive' => $allFiles->where('category', 'archive')->count(),
            'other' => $allFiles->where('category', 'other')->count(),
        ];

        // Storage quota calculation
        $quotaMb = (int) ($user->storage_quota_mb ?: 500);
        $quotaBytes = $quotaMb * 1024 * 1024;
        $storagePercent = min(100, max(0, round(($totalBytes / max(1, $quotaBytes)) * 100)));

        if ($quotaBytes >= 1073741824) {
            $formattedQuotaSize = number_format($quotaBytes / 1073741824, ($quotaBytes % 1073741824 === 0 ? 0 : 1), ',', '.').' GB';
        } elseif ($quotaBytes >= 1048576) {
            $formattedQuotaSize = number_format($quotaBytes / 1048576, 0, ',', '.').' MB';
        } else {
            $formattedQuotaSize = number_format($quotaBytes / 1024, 0, ',', '.').' KB';
        }

        // Format total storage
        if ($totalBytes >= 1073741824) {
            $formattedTotalSize = number_format($totalBytes / 1073741824, 2, ',', '.').' GB';
        } elseif ($totalBytes >= 1048576) {
            $formattedTotalSize = number_format($totalBytes / 1048576, 1, ',', '.').' MB';
        } elseif ($totalBytes >= 1024) {
            $formattedTotalSize = number_format($totalBytes / 1024, 0, ',', '.').' KB';
        } else {
            $formattedTotalSize = $totalBytes.' B';
        }

        $activeTab = $request->query('tab', 'files');
        $uploadLinks = $user->uploadLinks()->latest()->get();
        $allUserFolders = $user->folders()->orderBy('name')->get();

        return view('drive.index', compact(
            'files',
            'folders',
            'currentFolder',
            'breadcrumbs',
            'allUserFolders',
            'totalBytes',
            'totalFiles',
            'formattedTotalSize',
            'quotaMb',
            'quotaBytes',
            'formattedQuotaSize',
            'storagePercent',
            'categoryCounts',
            'activeCategory',
            'search',
            'uploadLinks',
            'activeTab'
        ));
    }

    /**
     * Update user's storage quota limit.
     */
    public function updateQuota(Request $request): RedirectResponse
    {
        $request->validate([
            'storage_quota_mb' => ['required', 'integer', 'min:10', 'max:1048576'],
        ]);

        $request->user()->update([
            'storage_quota_mb' => (int) $request->input('storage_quota_mb'),
        ]);

        return redirect()->route('drive.index', ['tab' => $request->input('tab', 'files')])
            ->with('success', 'Kapasitas ruang penyimpanan berhasil diperbarui!');
    }

    /**
     * Store a new uploaded file to private storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:51200'], // max 50 MB per file
            'file' => ['nullable', 'file', 'max:51200'],
            'title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ], [
            'files.*.max' => 'Ukuran salah satu berkas melebihi batas maksimal 50 MB.',
            'file.max' => 'Ukuran berkas melebihi batas maksimal 50 MB.',
        ]);

        $folderId = $request->input('folder_id');
        if ($folderId && ! $request->user()->folders()->where('id', $folderId)->exists()) {
            abort(403, 'Folder tidak valid.');
        }

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = (array) $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            return back()->withErrors(['file' => 'Pilih setidaknya satu berkas untuk diunggah.']);
        }

        $isMultiple = count($uploadedFiles) > 1;
        $customTitle = $request->filled('title') ? trim($request->input('title')) : null;
        $notes = $request->input('notes');

        $storedCount = 0;
        foreach ($uploadedFiles as $uploadedFile) {
            if (! $uploadedFile) {
                continue;
            }

            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION);
            $mimeType = $uploadedFile->getClientMimeType();
            $sizeBytes = $uploadedFile->getSize();
            $category = StoredFile::detectCategory($extension, $mimeType);

            // If user provided a custom title and only 1 file is uploaded, use it. Otherwise, use file's name.
            $title = (! $isMultiple && $customTitle)
                ? $customTitle
                : pathinfo($originalName, PATHINFO_FILENAME);

            $path = $uploadedFile->store('drive/'.$request->user()->id, 'local');

            $request->user()->storedFiles()->create([
                'folder_id' => $folderId,
                'title' => $title,
                'original_name' => $originalName,
                'file_path' => $path,
                'mime_type' => $mimeType,
                'extension' => strtolower($extension),
                'size_bytes' => $sizeBytes,
                'category' => $category,
                'share_token' => Str::random(40),
                'is_public' => false,
                'download_count' => 0,
                'notes' => $notes,
            ]);

            $storedCount++;
        }

        $redirectParams = $folderId ? ['folder_id' => $folderId] : [];
        $successMsg = $storedCount > 1
            ? "{$storedCount} berkas berhasil disimpan ke SwanDrive!"
            : 'File berhasil disimpan ke SwanDrive!';

        return redirect()->route('drive.index', $redirectParams)
            ->with('success', $successMsg);
    }

    /**
     * Create a new folder.
     */
    public function createFolder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'parent_id' => ['nullable', 'integer', 'exists:folders,id'],
            'color' => ['nullable', 'string', 'in:teal,emerald,purple,indigo,amber,rose,sky,slate'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId && ! $request->user()->folders()->where('id', $parentId)->exists()) {
            abort(403, 'Folder induk tidak valid.');
        }

        $folder = $request->user()->folders()->create([
            'parent_id' => $parentId,
            'name' => trim($validated['name']),
            'color' => $validated['color'] ?? 'teal',
            'share_token' => Str::random(40),
            'is_public' => $request->boolean('is_public'),
            'description' => $validated['description'] ?? null,
        ]);

        $redirectParams = $parentId ? ['folder_id' => $parentId] : [];

        return redirect()->route('drive.index', $redirectParams)
            ->with('success', "Folder \"{$folder->name}\" berhasil dibuat!");
    }

    /**
     * Update an existing folder.
     */
    public function updateFolder(Request $request, Folder $folder): RedirectResponse
    {
        if ($folder->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'color' => ['nullable', 'string', 'in:teal,emerald,purple,indigo,amber,rose,sky,slate'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $folder->update([
            'name' => trim($validated['name']),
            'color' => $validated['color'] ?? $folder->color,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Folder \"{$folder->name}\" berhasil diperbarui!");
    }

    /**
     * Delete a folder and its contents recursively.
     */
    public function destroyFolder(Request $request, Folder $folder): RedirectResponse|JsonResponse
    {
        if ($folder->user_id !== $request->user()->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak.');
        }

        $parentId = $folder->parent_id;
        $folderName = $folder->name;

        $this->deleteFolderRecursively($folder);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Folder \"{$folderName}\" beserta isinya berhasil dihapus.",
            ]);
        }

        $redirectParams = $parentId ? ['folder_id' => $parentId] : [];

        return redirect()->route('drive.index', $redirectParams)
            ->with('success', "Folder \"{$folderName}\" beserta isinya berhasil dihapus.");
    }

    /**
     * Recursively delete folder, subfolders, and physical files.
     */
    protected function deleteFolderRecursively(Folder $folder): void
    {
        foreach ($folder->files as $file) {
            try {
                if (Storage::disk('local')->exists($file->file_path)) {
                    Storage::disk('local')->delete($file->file_path);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal menghapus file saat hapus folder di SwanDrive: '.$e->getMessage(), [
                    'file_id' => $file->id,
                    'file_path' => $file->file_path,
                ]);
            }
            $file->delete();
        }

        foreach ($folder->children as $child) {
            $this->deleteFolderRecursively($child);
        }

        $folder->delete();
    }

    /**
     * Toggle public sharing for a folder.
     */
    public function toggleFolderShare(Request $request, Folder $folder): JsonResponse|RedirectResponse
    {
        if ($folder->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        $newStatus = ! $folder->is_public;

        $folder->update([
            'is_public' => $newStatus,
            'share_token' => $folder->share_token ?: Str::random(40),
        ]);

        $message = $newStatus
            ? 'Tautan berbagi folder diaktifkan! Siap disalin atau dibagikan.'
            : 'Tautan berbagi folder dinonaktifkan.';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_public' => $newStatus,
                'share_url' => $folder->share_url,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Move a file into another folder or root.
     */
    public function moveFile(Request $request, StoredFile $file): RedirectResponse
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ]);

        $destinationFolderId = $request->input('folder_id');
        if ($destinationFolderId && ! $request->user()->folders()->where('id', $destinationFolderId)->exists()) {
            abort(403, 'Folder tujuan tidak valid.');
        }

        $file->update(['folder_id' => $destinationFolderId]);

        return back()->with('success', 'Berkas berhasil dipindahkan!');
    }

    /**
     * Preview / view the file inline without downloading.
     */
    public function preview(Request $request, StoredFile $file): StreamedResponse
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = $file->mime_type ?: Storage::disk('local')->mimeType($file->file_path) ?: 'application/octet-stream';

        return Storage::disk('local')->response($file->file_path, $file->original_name, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Download the file for the owner.
     */
    public function download(Request $request, StoredFile $file): StreamedResponse
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $file->increment('download_count');

        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }

    /**
     * Toggle the public share/transfer link for a file.
     */
    public function toggleShare(Request $request, StoredFile $file): RedirectResponse
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        $newStatus = ! $file->is_public;

        $file->update([
            'is_public' => $newStatus,
            'share_token' => $file->share_token ?: Str::random(40),
        ]);

        $message = $newStatus
            ? 'Tautan transfer diaktifkan! Siap disalin atau dibagikan.'
            : 'Tautan transfer dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * Delete a file from disk and database.
     */
    public function destroy(Request $request, StoredFile $file): RedirectResponse|JsonResponse
    {
        if ($file->user_id !== $request->user()->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak.');
        }

        try {
            if (Storage::disk('local')->exists($file->file_path)) {
                Storage::disk('local')->delete($file->file_path);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal menghapus file fisik di SwanDrive: '.$e->getMessage(), [
                'file_id' => $file->id,
                'file_path' => $file->file_path,
            ]);
        }

        $file->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus dari SwanDrive.',
            ]);
        }

        $redirect = $request->filled('tab')
            ? route('drive.index', ['tab' => $request->input('tab')])
            : route('drive.index');

        return redirect($redirect)->with('success', 'File berhasil dihapus dari SwanDrive.');
    }

    /**
     * Public page to view and receive a shared file.
     */
    public function sharedView(string $token): View
    {
        $file = StoredFile::where('share_token', $token)->firstOrFail();

        if (! $file->is_public) {
            abort(404, 'Tautan transfer ini tidak aktif atau telah dinonaktifkan oleh pemilik.');
        }

        return view('drive.share', compact('file'));
    }

    /**
     * Preview the shared file inline publicly.
     */
    public function sharedPreview(string $token): StreamedResponse
    {
        $file = StoredFile::where('share_token', $token)->firstOrFail();

        if (! $file->is_public) {
            abort(404, 'Tautan transfer ini tidak aktif.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = $file->mime_type ?: Storage::disk('local')->mimeType($file->file_path) ?: 'application/octet-stream';

        return Storage::disk('local')->response($file->file_path, $file->original_name, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Download the shared file publicly.
     */
    public function sharedDownload(string $token): StreamedResponse
    {
        $file = StoredFile::where('share_token', $token)->firstOrFail();

        if (! $file->is_public) {
            abort(404, 'Tautan transfer ini tidak aktif.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $file->increment('download_count');

        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }

    /**
     * Public page to view a shared folder.
     */
    public function sharedFolderView(string $token): View
    {
        $folder = Folder::where('share_token', $token)->firstOrFail();

        if (! $folder->is_public) {
            abort(404, 'Tautan transfer folder ini tidak aktif atau telah dinonaktifkan oleh pemilik.');
        }

        $files = $folder->files()->latest()->get();

        return view('drive.share_folder', compact('folder', 'files'));
    }

    /**
     * Preview a file inside a shared folder inline publicly.
     */
    public function sharedFolderPreview(string $token, StoredFile $file): StreamedResponse
    {
        $folder = Folder::where('share_token', $token)->where('is_public', true)->firstOrFail();

        if ($file->folder_id !== $folder->id) {
            abort(404, 'Berkas tidak ditemukan dalam folder ini.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = $file->mime_type ?: Storage::disk('local')->mimeType($file->file_path) ?: 'application/octet-stream';

        return Storage::disk('local')->response($file->file_path, $file->original_name, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Download a file inside a shared folder publicly.
     */
    public function sharedFolderDownload(string $token, StoredFile $file): StreamedResponse
    {
        $folder = Folder::where('share_token', $token)->where('is_public', true)->firstOrFail();

        if ($file->folder_id !== $folder->id) {
            abort(404, 'Berkas tidak ditemukan dalam folder ini.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $file->increment('download_count');

        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }

    /**
     * Download all files in a shared folder as a ZIP archive.
     */
    public function sharedFolderZip(string $token): StreamedResponse|BinaryFileResponse|RedirectResponse
    {
        $folder = Folder::where('share_token', $token)->where('is_public', true)->firstOrFail();
        $files = $folder->files;

        if ($files->isEmpty()) {
            return back()->with('error', 'Folder ini kosong, tidak ada berkas untuk diunduh.');
        }

        $zipFileName = Str::slug($folder->name).'_'.date('YmdHis').'.zip';
        $tempZipPath = tempnam(sys_get_temp_dir(), 'swanflow_zip_');

        $zip = new ZipArchive;
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (Storage::disk('local')->exists($file->file_path)) {
                    $zip->addFile(Storage::disk('local')->path($file->file_path), $file->original_name);
                    $file->increment('download_count');
                }
            }
            $zip->close();
        }

        return response()->download($tempZipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}
