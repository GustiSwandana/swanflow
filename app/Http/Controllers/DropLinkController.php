<?php

namespace App\Http\Controllers;

use App\Models\StoredFile;
use App\Models\UploadLink;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DropLinkController extends Controller
{
    /**
     * Store a new file drop upload link.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expiry_preset' => ['required', 'in:1h,24h,3d,7d,30d,never'],
            'max_files' => ['required', 'integer', 'min:1', 'max:50'],
            'max_file_size_mb' => ['required', 'integer', 'in:5,10,25,50'],
        ]);

        $expiresAt = match ($request->input('expiry_preset')) {
            '1h' => Carbon::now()->addHour(),
            '24h' => Carbon::now()->addDay(),
            '3d' => Carbon::now()->addDays(3),
            '7d' => Carbon::now()->addDays(7),
            '30d' => Carbon::now()->addDays(30),
            default => null,
        };

        $request->user()->uploadLinks()->create([
            'title' => trim($request->input('title')),
            'token' => Str::random(40),
            'description' => $request->input('description'),
            'expires_at' => $expiresAt,
            'max_files' => (int) $request->input('max_files'),
            'uploaded_files_count' => 0,
            'max_file_size_mb' => (int) $request->input('max_file_size_mb'),
            'is_active' => true,
        ]);

        return redirect()->route('drive.index', ['tab' => 'drops'])
            ->with('success', 'Tautan terima berkas (Drop Link) berhasil dibuat!');
    }

    /**
     * Toggle the active/closed status of an upload link.
     */
    public function toggle(Request $request, UploadLink $link): RedirectResponse
    {
        if ($link->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        $link->update([
            'is_active' => ! $link->is_active,
        ]);

        $msg = $link->is_active
            ? 'Tautan terima berkas dibuka kembali!'
            : 'Tautan terima berkas telah ditutup.';

        return back()->with('success', $msg);
    }

    /**
     * Delete an upload link.
     */
    public function destroy(Request $request, UploadLink $link): RedirectResponse
    {
        if ($link->user_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }

        $link->delete();

        return redirect()->route('drive.index', ['tab' => 'drops'])
            ->with('success', 'Tautan terima berkas berhasil dihapus.');
    }

    /**
     * Display the public drop upload portal for guests.
     */
    public function show(string $token): View
    {
        $link = UploadLink::where('token', $token)->firstOrFail();

        return view('drive.drop', compact('link'));
    }

    /**
     * Handle public file upload through the drop link.
     */
    public function upload(Request $request, string $token): RedirectResponse
    {
        $link = UploadLink::where('token', $token)->firstOrFail();

        if (! $link->canAcceptUpload()) {
            if (! $link->is_active) {
                return back()->withErrors(['file' => 'Tautan pengunggahan ini telah ditutup oleh pemilik.']);
            }
            if ($link->isExpired()) {
                return back()->withErrors(['file' => 'Tautan pengunggahan ini telah kedaluwarsa.']);
            }
            if ($link->isLimitReached()) {
                return back()->withErrors(['file' => 'Batas maksimal kuota berkas untuk tautan ini sudah penuh.']);
            }
        }

        $maxKb = $link->max_file_size_mb * 1024;

        $request->validate([
            'file' => ['required', 'file', "max:{$maxKb}"],
            'uploader_name' => ['nullable', 'string', 'max:100'],
            'uploader_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'file.required' => 'Pilih berkas yang ingin diunggah terlebih dahulu.',
            'file.max' => "Ukuran berkas melebihi batas maksimal {$link->max_file_size_mb} MB.",
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION);
        $mimeType = $uploadedFile->getClientMimeType();
        $sizeBytes = $uploadedFile->getSize();
        $category = StoredFile::detectCategory($extension, $mimeType);

        $path = $uploadedFile->store('drive/'.$link->user_id, 'local');

        $title = pathinfo($originalName, PATHINFO_FILENAME);
        $uploaderName = $request->filled('uploader_name')
            ? trim($request->input('uploader_name'))
            : 'Pihak Luar (Drop Link)';

        $notes = $request->filled('uploader_notes')
            ? trim($request->input('uploader_notes'))
            : "Diterima melalui tautan: {$link->title}";

        $link->user->storedFiles()->create([
            'upload_link_id' => $link->id,
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
            'uploader_name' => $uploaderName,
        ]);

        $link->increment('uploaded_files_count');

        return back()->with('drop_success', "Berkas \"{$originalName}\" berhasil terkirim ke SwanDrive!");
    }
}
