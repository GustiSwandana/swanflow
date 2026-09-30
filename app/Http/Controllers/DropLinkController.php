<?php

namespace App\Http\Controllers;

use App\Models\StoredFile;
use App\Models\UploadLink;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

        $folderTitle = trim($request->input('title'));
        $folderName = str_starts_with($folderTitle, '📥') ? $folderTitle : '📥 '.$folderTitle;

        $folder = $request->user()->folders()->create([
            'name' => $folderName,
            'color' => 'amber',
            'share_token' => Str::random(40),
            'is_public' => false,
            'description' => 'Folder penerimaan berkas dari pihak luar via link: '.$folderTitle,
        ]);

        $request->user()->uploadLinks()->create([
            'folder_id' => $folder->id,
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
            ->with('success', 'Tautan terima berkas (Drop Link) dan folder khusus berhasil dibuat!');
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
            'files' => ['nullable', 'array'],
            'files.*' => ['file', "max:{$maxKb}"],
            'file' => ['nullable', 'file', "max:{$maxKb}"],
            'uploader_name' => ['nullable', 'string', 'max:100'],
            'uploader_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'files.*.max' => "Ukuran berkas melebihi batas maksimal {$link->max_file_size_mb} MB.",
            'file.max' => "Ukuran berkas melebihi batas maksimal {$link->max_file_size_mb} MB.",
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = (array) $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            return back()->withErrors(['file' => 'Pilih setidaknya satu berkas yang ingin diunggah.']);
        }

        // Check if remaining slots allow this upload batch
        $remaining = $link->remainingSlots();
        if ($remaining > 0 && count($uploadedFiles) > $remaining) {
            return back()->withErrors(['file' => "Slot pengunggahan tersisa {$remaining} berkas lagi."]);
        }

        $folder = $link->getOrCreateFolder();

        $uploaderName = $request->filled('uploader_name')
            ? trim($request->input('uploader_name'))
            : 'Pihak Luar (Drop Link)';

        $notes = $request->filled('uploader_notes')
            ? trim($request->input('uploader_notes'))
            : "Diterima melalui tautan: {$link->title}";

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

            $hasGoogle = ! empty(config('filesystems.disks.google.refreshToken')) || file_exists(storage_path('app/google_drive_token.json'));
            $disk = $hasGoogle ? 'google' : config('filesystems.default', 'local');
            if ($disk === 'google' && ! $hasGoogle) {
                $disk = 'local';
            }

            // Always store to local disk first so file is immediately safe
            $path = $uploadedFile->store('drive/'.$link->user_id, 'local');
            $title = pathinfo($originalName, PATHINFO_FILENAME);

            $link->user->storedFiles()->create([
                'folder_id' => $folder->id,
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

            if ($disk !== 'local') {
                try {
                    $localPath = Storage::disk('local')->path($path);
                    if (file_exists($localPath)) {
                        $stream = fopen($localPath, 'r');
                        Storage::disk($disk)->put($path, $stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                        if (Storage::disk($disk)->exists($path)) {
                            Storage::disk('local')->delete($path);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("DropLink upload to [{$disk}] failed: ".$e->getMessage().'. Storing locally.');
                }
            }

            $storedCount++;
        }

        $link->increment('uploaded_files_count', $storedCount);

        $msg = $storedCount === 1
            ? "1 berkas berhasil terkirim ke folder \"{$folder->name}\" di SwanDrive!"
            : "{$storedCount} berkas berhasil terkirim ke folder \"{$folder->name}\" di SwanDrive!";

        return back()->with('drop_success', $msg);
    }

    /**
     * Handle chunked public file upload through the drop link.
     */
    public function uploadChunk(Request $request, string $token): JsonResponse
    {
        $link = UploadLink::where('token', $token)->firstOrFail();

        if (! $link->canAcceptUpload()) {
            return response()->json(['error' => 'Tautan pengunggahan ini sudah tidak aktif atau kuota telah penuh.'], 403);
        }

        $maxBytes = $link->max_file_size_mb * 1024 * 1024;

        $request->validate([
            'chunk' => ['required', 'file', 'max:15360'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'file_uuid' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'file_name' => ['required', 'string', 'max:255'],
            'uploader_name' => ['nullable', 'string', 'max:100'],
            'uploader_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $fileUuid = $request->input('file_uuid');
        $originalName = $request->input('file_name');

        $tempDir = 'temp_chunks/drop_'.$link->id.'/'.$fileUuid;
        $chunkFile = $request->file('chunk');
        $chunkFileName = "chunk_{$chunkIndex}.part";

        Storage::disk('local')->putFileAs($tempDir, $chunkFile, $chunkFileName);

        if ($chunkIndex + 1 < $totalChunks) {
            return response()->json([
                'status' => 'chunk_saved',
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks,
                'progress' => round((($chunkIndex + 1) / $totalChunks) * 100),
            ]);
        }

        @set_time_limit(600);
        @ini_set('memory_limit', '512M');
        ignore_user_abort(true);

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeExtension = $extension ? strtolower($extension) : 'bin';
        $finalFilename = Str::uuid().'.'.$safeExtension;
        $finalRelativePath = 'drive/'.$link->user_id.'/'.$finalFilename;
        $finalFullPath = Storage::disk('local')->path($finalRelativePath);

        $destDir = dirname($finalFullPath);
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $outHandle = fopen($finalFullPath, 'wb');
        if (! $outHandle) {
            return response()->json(['error' => 'Gagal membuat berkas final.'], 500);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $partPath = Storage::disk('local')->path("{$tempDir}/chunk_{$i}.part");
            if (! file_exists($partPath)) {
                fclose($outHandle);
                @unlink($finalFullPath);

                return response()->json(['error' => "Potongan berkas ke-{$i} hilang."], 400);
            }

            $inHandle = fopen($partPath, 'rb');
            if ($inHandle) {
                while (! feof($inHandle)) {
                    $buffer = fread($inHandle, 1048576);
                    fwrite($outHandle, $buffer);
                }
                fclose($inHandle);
            }
        }
        fclose($outHandle);

        Storage::disk('local')->deleteDirectory($tempDir);

        $sizeBytes = filesize($finalFullPath);
        if ($sizeBytes > $maxBytes) {
            @unlink($finalFullPath);

            return response()->json(['error' => "Ukuran berkas ({$originalName}) melebihi batas maksimal {$link->max_file_size_mb} MB."], 422);
        }

        $mimeType = mime_content_type($finalFullPath) ?: 'application/octet-stream';
        $category = StoredFile::detectCategory($safeExtension, $mimeType);

        $folder = $link->getOrCreateFolder();

        $hasGoogle = ! empty(config('filesystems.disks.google.refreshToken')) || file_exists(storage_path('app/google_drive_token.json'));
        $disk = $hasGoogle ? 'google' : config('filesystems.default', 'local');
        if ($disk === 'google' && ! $hasGoogle) {
            $disk = 'local';
        }

        $uploaderName = $request->filled('uploader_name')
            ? trim($request->input('uploader_name'))
            : 'Pihak Luar (Drop Link)';

        $notes = $request->filled('uploader_notes')
            ? trim($request->input('uploader_notes'))
            : "Diterima melalui tautan: {$link->title}";

        // Create DB record immediately so file is safely recorded
        $storedFile = $link->user->storedFiles()->create([
            'folder_id' => $folder->id,
            'upload_link_id' => $link->id,
            'title' => pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'file_path' => $finalRelativePath,
            'mime_type' => $mimeType,
            'extension' => $safeExtension,
            'size_bytes' => $sizeBytes,
            'category' => $category,
            'share_token' => Str::random(40),
            'is_public' => false,
            'download_count' => 0,
            'notes' => $notes,
            'uploader_name' => $uploaderName,
        ]);

        $link->increment('uploaded_files_count', 1);

        $responseData = [
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Berkas berhasil dikirim!',
            'file' => [
                'id' => $storedFile->id,
                'title' => $storedFile->title,
            ],
        ];

        $canFinishEarly = false;
        if (function_exists('litespeed_finish_request')) {
            response()->json($responseData)->send();
            litespeed_finish_request();
            $canFinishEarly = true;
        } elseif (function_exists('fastcgi_finish_request')) {
            response()->json($responseData)->send();
            fastcgi_finish_request();
            $canFinishEarly = true;
        }

        if ($disk !== 'local') {
            try {
                $fileStream = fopen($finalFullPath, 'r');
                Storage::disk($disk)->put($finalRelativePath, $fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
                if (Storage::disk($disk)->exists($finalRelativePath)) {
                    Storage::disk('local')->delete($finalRelativePath);
                }
            } catch (\Throwable $e) {
                Log::warning("DropLink upload to [{$disk}] failed: ".$e->getMessage().'. Storing locally.');
            }
        }

        if ($canFinishEarly) {
            exit;
        }

        return response()->json($responseData);
    }
}
