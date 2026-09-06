<?php

namespace App\Http\Controllers;

use App\Models\StoredFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriveController extends Controller
{
    /**
     * Display the user's SwanDrive dashboard and stored files.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeCategory = $request->query('category', 'all');
        $search = $request->query('search');

        $query = $user->storedFiles();

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

        return view('drive.index', compact(
            'files',
            'totalBytes',
            'totalFiles',
            'formattedTotalSize',
            'categoryCounts',
            'activeCategory',
            'search',
            'uploadLinks',
            'activeTab'
        ));
    }

    /**
     * Store a new uploaded file to private storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:51200'], // max 50 MB
            'title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION);
        $mimeType = $uploadedFile->getClientMimeType();
        $sizeBytes = $uploadedFile->getSize();
        $category = StoredFile::detectCategory($extension, $mimeType);

        $title = $request->filled('title')
            ? trim($request->input('title'))
            : pathinfo($originalName, PATHINFO_FILENAME);

        $path = $uploadedFile->store('drive/'.$request->user()->id, 'local');

        $request->user()->storedFiles()->create([
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
            'notes' => $request->input('notes'),
        ]);

        return redirect()->route('drive.index')->with('success', 'File berhasil disimpan ke SwanDrive!');
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
    public function destroy(Request $request, StoredFile $file): RedirectResponse
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->route('drive.index')->with('success', 'File berhasil dihapus dari SwanDrive.');
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
}
