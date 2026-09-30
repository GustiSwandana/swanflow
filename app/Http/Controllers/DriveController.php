<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\StoredFile;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
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
        $fileId = $request->query('file_id');
        $targetFileId = $fileId ? (int) $fileId : null;

        $currentFolder = null;
        $breadcrumbs = collect();
        $targetFile = null;

        if ($fileId) {
            $targetFile = $user->storedFiles()->find($fileId);
            if ($targetFile) {
                if (! $folderId && $targetFile->folder_id) {
                    $folderId = $targetFile->folder_id;
                }
                if ($activeCategory !== 'all' && $targetFile->category !== $activeCategory && ! $request->has('category')) {
                    $activeCategory = 'all';
                }
            }
        }

        $activeSource = $request->query('source', 'all'); // 'all', 'my', 'received'

        if ($folderId) {
            $currentFolder = $user->folders()->with('parent')->findOrFail($folderId);
            $breadcrumbs = $currentFolder->breadcrumbs();
            $folderQuery = $currentFolder->children()->withCount('files')->latest();
            $query = $currentFolder->files();
        } else {
            // Root level folders
            $folderQuery = $user->folders()->whereNull('parent_id')->withCount('files')->latest();
            $query = $user->storedFiles();

            // In root level, if not searching and category is 'all', show root files (files without folder)
            // But if a specific root file is targeted, ensure it is included
            if (empty($search) && $activeCategory === 'all') {
                $query->whereNull('folder_id');
            }
        }

        // Filter by source (Ownership / Asal Berkas)
        if ($activeSource === 'my') {
            // User's own folders (not starting with 📥 and not drop folders)
            $folderQuery->where('name', 'not like', '📥%')
                ->whereDoesntHave('uploadLinks');
            // User's own files (not from upload link and no uploader name)
            $query->whereNull('upload_link_id')
                ->whereNull('uploader_name');
        } elseif ($activeSource === 'received') {
            // Received / external folders (drop folders or starting with 📥)
            $folderQuery->where(function ($q) {
                $q->where('name', 'like', '📥%')
                    ->orWhereHas('uploadLinks');
            });
            // External files (uploaded via link or has uploader name)
            $query->where(function ($q) {
                $q->whereNotNull('upload_link_id')
                    ->orWhereNotNull('uploader_name');
            });
        }

        $folders = $folderQuery->get();

        if ($activeCategory === 'received') {
            $query->where(function ($q) {
                $q->whereNotNull('upload_link_id')
                    ->orWhereNotNull('uploader_name');
            });
        } elseif ($activeCategory === 'shared') {
            $query->where('is_public', true);
        } elseif ($activeCategory !== 'all' && in_array($activeCategory, ['document', 'image', 'archive', 'other'])) {
            $query->where('category', $activeCategory);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('uploader_name', 'like', "%{$search}%");
            });
        }

        $files = $query->with(['folder', 'uploadLink'])->latest()->get();

        $allFiles = $user->storedFiles()->get(['category', 'size_bytes', 'upload_link_id', 'uploader_name', 'is_public']);
        $totalBytes = $allFiles->sum('size_bytes');
        $totalFiles = $allFiles->count();

        $categoryCounts = [
            'all' => $totalFiles,
            'shared' => $allFiles->where('is_public', true)->count(),
            'received' => $allFiles->filter(fn ($f) => ! empty($f->upload_link_id) || ! empty($f->uploader_name))->count(),
            'document' => $allFiles->where('category', 'document')->count(),
            'image' => $allFiles->where('category', 'image')->count(),
            'archive' => $allFiles->where('category', 'archive')->count(),
            'other' => $allFiles->where('category', 'other')->count(),
        ];

        // Source counts (Berkas Saya vs Dari Pihak Luar)
        $receivedFilesCount = $categoryCounts['received'];
        $myFilesCount = max(0, $totalFiles - $receivedFilesCount);
        $sourceCounts = [
            'all' => $totalFiles,
            'my' => $myFilesCount,
            'received' => $receivedFilesCount,
        ];

        // Storage quota calculation - Adapt to Google Drive
        $isGoogleConnected = $this->isGoogleDriveConnected();
        $gDriveQuota = $this->getGoogleDriveQuotaInfo();

        if ($gDriveQuota && $gDriveQuota['limit_bytes'] > 0) {
            $quotaBytes = (int) $gDriveQuota['limit_bytes'];
            $quotaMb = (int) round($quotaBytes / (1024 * 1024));

            if ($user->storage_quota_mb !== $quotaMb) {
                $user->update(['storage_quota_mb' => $quotaMb]);
            }
        } else {
            // Default 15 GB if Google Drive standard, or user configured quota
            $quotaMb = (int) ($user->storage_quota_mb ?: 15360);
            $quotaBytes = $quotaMb * 1024 * 1024;
        }

        $storagePercent = min(100, max(0, round(($totalBytes / max(1, $quotaBytes)) * 100)));

        if ($quotaBytes >= 1099511627776) {
            $formattedQuotaSize = number_format($quotaBytes / 1099511627776, ($quotaBytes % 1099511627776 === 0 ? 0 : 1), ',', '.').' TB';
        } elseif ($quotaBytes >= 1073741824) {
            $formattedQuotaSize = number_format($quotaBytes / 1073741824, ($quotaBytes % 1073741824 === 0 ? 0 : 1), ',', '.').' GB';
        } elseif ($quotaBytes >= 1048576) {
            $formattedQuotaSize = number_format($quotaBytes / 1048576, 0, ',', '.').' MB';
        } else {
            $formattedQuotaSize = number_format($quotaBytes / 1024, 0, ',', '.').' KB';
        }

        // Formatted Remaining Size
        $remainingBytes = max(0, $quotaBytes - $totalBytes);
        if ($remainingBytes >= 1099511627776) {
            $formattedRemainingSize = number_format($remainingBytes / 1099511627776, 1, ',', '.').' TB';
        } elseif ($remainingBytes >= 1073741824) {
            $formattedRemainingSize = number_format($remainingBytes / 1073741824, 1, ',', '.').' GB';
        } elseif ($remainingBytes >= 1048576) {
            $formattedRemainingSize = number_format($remainingBytes / 1048576, 1, ',', '.').' MB';
        } else {
            $formattedRemainingSize = number_format($remainingBytes / 1024, 0, ',', '.').' KB';
        }

        // Format total storage
        if ($totalBytes >= 1099511627776) {
            $formattedTotalSize = number_format($totalBytes / 1099511627776, 2, ',', '.').' TB';
        } elseif ($totalBytes >= 1073741824) {
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
            'formattedRemainingSize',
            'storagePercent',
            'categoryCounts',
            'activeCategory',
            'activeSource',
            'sourceCounts',
            'search',
            'uploadLinks',
            'activeTab',
            'targetFileId',
            'isGoogleConnected',
            'gDriveQuota'
        ));
    }

    public function connectGoogle(Request $request)
    {
        $clientId = config('filesystems.disks.google.clientId');
        $clientSecret = config('filesystems.disks.google.clientSecret');
        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('drive.index')->with('error', 'Google Client ID / Secret belum dikonfigurasi di server.');
        }

        $client = new Client;
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(route('drive.callback'));
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');

        return redirect()->away($client->createAuthUrl());
    }

    public function googleCallback(Request $request)
    {
        if ($request->has('code')) {
            $clientId = config('filesystems.disks.google.clientId');
            $clientSecret = config('filesystems.disks.google.clientSecret');

            $client = new Client;
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri(route('drive.callback'));

            try {
                $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            } catch (\Throwable $e) {
                Log::error('Google OAuth Token Fetch Exception: '.$e->getMessage());

                return redirect()->route('drive.index')->with('error', 'Gagal memperoleh token Google: '.$e->getMessage());
            }

            Log::info('Google OAuth Callback Token:', is_array($token) ? $token : ['raw' => $token]);

            if (isset($token['error'])) {
                $errMsg = $token['error_description'] ?? $token['error'];

                return redirect()->route('drive.index')->with('error', 'Google OAuth Error: '.$errMsg);
            }

            if (isset($token['refresh_token'])) {
                // 1. Save to JSON token file in storage (always writable on production)
                $tokenDir = storage_path('app');
                if (! is_dir($tokenDir)) {
                    @mkdir($tokenDir, 0755, true);
                }
                @file_put_contents(storage_path('app/google_drive_token.json'), json_encode($token, JSON_PRETTY_PRINT));

                // 2. Try saving to .env as well if writable
                try {
                    $envPath = base_path('.env');
                    if (file_exists($envPath) && is_writable($envPath)) {
                        $envContent = file_get_contents($envPath);
                        if (preg_match('/GOOGLE_DRIVE_REFRESH_TOKEN=(.*)/', $envContent)) {
                            $envContent = preg_replace('/GOOGLE_DRIVE_REFRESH_TOKEN=(.*)/', 'GOOGLE_DRIVE_REFRESH_TOKEN='.$token['refresh_token'], $envContent);
                        } else {
                            $envContent .= "\nGOOGLE_DRIVE_REFRESH_TOKEN=".$token['refresh_token']."\n";
                        }
                        @file_put_contents($envPath, $envContent);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Could not write to .env: '.$e->getMessage());
                }

                // 3. Clear cache and fetch quota immediately
                try {
                    Artisan::call('config:clear');
                } catch (\Throwable $e) {
                }

                Cache::forget('google_drive_quota_info');
                $quota = $this->getGoogleDriveQuotaInfo();
                $quotaText = '';
                if ($quota && $quota['limit_bytes'] > 0) {
                    $gb = round($quota['limit_bytes'] / (1024 * 1024 * 1024));
                    $quotaText = " Kapasitas otomatis disesuaikan dengan Google Drive ({$gb} GB)!";
                }

                return redirect()->route('drive.index')->with('success', 'Google Drive berhasil disambungkan!'.$quotaText);
            }

            $tokenFile = storage_path('app/google_drive_token.json');
            if (file_exists($tokenFile)) {
                $existing = json_decode(file_get_contents($tokenFile), true);
                if (! empty($existing['refresh_token'])) {
                    return redirect()->route('drive.index')->with('success', 'Google Drive telah aktif menggunakan token yang tersimpan.');
                }
            }

            return redirect()->route('drive.index')->with('error', 'Google tidak mengirimkan Refresh Token. Buka https://myaccount.google.com/connections, hapus izin swanflow, lalu klik Sambung Google lagi agar izin baru diterbitkan.');
        }

        return redirect()->route('drive.index')->with('error', 'Otorisasi Google Drive dibatalkan.');
    }

    /**
     * Check if Google Drive is configured and authenticated.
     */
    public function isGoogleDriveConnected(): bool
    {
        $refreshToken = config('filesystems.disks.google.refreshToken');
        if (empty($refreshToken)) {
            $tokenFile = storage_path('app/google_drive_token.json');
            if (file_exists($tokenFile)) {
                $tokenData = json_decode(@file_get_contents($tokenFile), true);
                $refreshToken = $tokenData['refresh_token'] ?? null;
            }
        }

        if (! empty($refreshToken)) {
            return true;
        }

        $serviceAccount = config('filesystems.disks.google.serviceAccountKey');

        return ! empty($serviceAccount) && file_exists($serviceAccount);
    }

    /**
     * Resolve active storage disk (with local fallback if Google Drive is not connected).
     */
    public function resolveStorageDisk(): string
    {
        $defaultDisk = config('filesystems.default', 'local');
        if ($defaultDisk === 'google' && ! $this->isGoogleDriveConnected()) {
            return 'local';
        }

        return $defaultDisk;
    }

    /**
     * Determine which disk actually stores the given file.
     */
    public function getDiskForFile(StoredFile $file): string
    {
        try {
            if (Storage::disk('local')->exists($file->file_path)) {
                return 'local';
            }
        } catch (\Throwable $e) {
        }

        if ($this->isGoogleDriveConnected()) {
            try {
                if (Storage::disk('google')->exists($file->file_path)) {
                    return 'google';
                }
            } catch (\Throwable $e) {
                Log::warning("Could not check google disk for file {$file->id}: ".$e->getMessage());
            }
        }

        return config('filesystems.default', 'local');
    }

    /**
     * Get Google Drive Client instance.
     */
    protected function getGoogleClient(): ?Client
    {
        $clientId = config('filesystems.disks.google.clientId');
        $clientSecret = config('filesystems.disks.google.clientSecret');
        $refreshToken = config('filesystems.disks.google.refreshToken');

        if (empty($refreshToken)) {
            $tokenFile = storage_path('app/google_drive_token.json');
            if (file_exists($tokenFile)) {
                $tokenData = json_decode(@file_get_contents($tokenFile), true);
                $refreshToken = $tokenData['refresh_token'] ?? null;
            }
        }

        if (empty($refreshToken) && (empty(config('filesystems.disks.google.serviceAccountKey')) || ! file_exists(config('filesystems.disks.google.serviceAccountKey')))) {
            return null;
        }

        $client = new Client;
        if (! empty(config('filesystems.disks.google.serviceAccountKey')) && file_exists(config('filesystems.disks.google.serviceAccountKey'))) {
            $client->setAuthConfig(config('filesystems.disks.google.serviceAccountKey'));
            $client->addScope(Drive::DRIVE);
        } else {
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            if (! empty($refreshToken)) {
                $client->refreshToken($refreshToken);
            }
        }

        return $client;
    }

    /**
     * Fetch Google Drive real storage quota details.
     */
    public function getGoogleDriveQuotaInfo(): ?array
    {
        if (! $this->isGoogleDriveConnected()) {
            return null;
        }

        return Cache::remember('google_drive_quota_info', 300, function () {
            try {
                $client = $this->getGoogleClient();
                if (! $client) {
                    return null;
                }

                $service = new Drive($client);
                $about = $service->about->get(['fields' => 'storageQuota,user']);
                $storageQuota = $about->getStorageQuota();
                $gUser = $about->getUser();

                $limitBytes = (int) $storageQuota->getLimit();
                $usageBytes = (int) $storageQuota->getUsage();
                $usageInDrive = (int) $storageQuota->getUsageInDrive();

                // If limit is 0 (Google Workspace unlimited / pooled storage), standard default to 5 TB
                if ($limitBytes <= 0) {
                    $limitBytes = 5 * 1024 * 1024 * 1024 * 1024; // 5 TB
                }

                return [
                    'connected' => true,
                    'limit_bytes' => $limitBytes,
                    'usage_bytes' => $usageBytes,
                    'usage_in_drive_bytes' => $usageInDrive,
                    'email' => $gUser ? $gUser->getEmailAddress() : null,
                    'display_name' => $gUser ? $gUser->getDisplayName() : null,
                ];
            } catch (\Throwable $e) {
                Log::warning('Google Drive Quota fetch error: '.$e->getMessage());

                return null;
            }
        });
    }

    /**
     * Refresh and sync storage quota directly from Google Drive API.
     */
    public function syncGoogleQuota(Request $request): RedirectResponse
    {
        Cache::forget('google_drive_quota_info');
        $quota = $this->getGoogleDriveQuotaInfo();

        if ($quota && $quota['limit_bytes'] > 0) {
            $limitGb = round($quota['limit_bytes'] / (1024 * 1024 * 1024));
            $user = $request->user();
            $user->update(['storage_quota_mb' => (int) round($quota['limit_bytes'] / (1024 * 1024))]);

            return back()->with('success', "Kapasitas SwanDrive berhasil disinkronkan dengan Google Drive ({$limitGb} GB)!");
        }

        if (! $this->isGoogleDriveConnected()) {
            return back()->with('error', 'Google Drive belum terhubung. Silakan sambungkan Google Drive terlebih dahulu.');
        }

        return back()->with('success', 'Sinkronisasi kapasitas berhasil diperbarui.');
    }

    /**
     * Update user's storage quota limit.
     */
    public function updateQuota(Request $request): RedirectResponse
    {
        $request->validate([
            'storage_quota_mb' => ['required', 'integer', 'min:10', 'max:5242880'],
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
            'files.*' => ['file', 'max:262144'], // up to 256 MB per file
            'file' => ['nullable', 'file', 'max:262144'],
            'title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ], [
            'files.*.max' => 'Ukuran salah satu berkas melebihi batas maksimal 256 MB.',
            'file.max' => 'Ukuran berkas melebihi batas maksimal 256 MB.',
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

            $disk = $this->resolveStorageDisk();

            $thumbnailPath = null;
            if ($category === 'image') {
                try {
                    $thumbnailPath = $this->generateWebpThumbnail($uploadedFile->getRealPath(), $request->user()->id, $disk);
                } catch (\Throwable $e) {
                    Log::warning('Thumbnail generation error in DriveController::store: '.$e->getMessage());
                }
            }

            // Upload directly to resolved storage disk (with automatic fallback to local if cloud fails)
            try {
                $path = $uploadedFile->store('drive/'.$request->user()->id, $disk);
            } catch (\Throwable $e) {
                Log::warning("Drive upload to disk [{$disk}] failed: ".$e->getMessage().'. Falling back to local storage.');
                $disk = 'local';
                $path = $uploadedFile->store('drive/'.$request->user()->id, 'local');
            }

            $request->user()->storedFiles()->create([
                'folder_id' => $folderId,
                'title' => $title,
                'original_name' => $originalName,
                'file_path' => $path,
                'thumbnail_path' => $thumbnailPath,
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
                $fileDisk = $this->getDiskForFile($file);
                if (Storage::disk($fileDisk)->exists($file->file_path)) {
                    Storage::disk($fileDisk)->delete($file->file_path);
                }
                if ($file->thumbnail_path && Storage::disk($fileDisk)->exists($file->thumbnail_path)) {
                    Storage::disk($fileDisk)->delete($file->thumbnail_path);
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

        $disk = $this->getDiskForFile($file);

        if ($request->boolean('thumb') && $file->thumbnail_path && Storage::disk($disk)->exists($file->thumbnail_path)) {
            return Storage::disk($disk)->response($file->thumbnail_path, pathinfo($file->original_name, PATHINFO_FILENAME).'_thumb.webp', [
                'Content-Type' => 'image/webp',
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        if (! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = $file->mime_type ?: Storage::disk($disk)->mimeType($file->file_path) ?: 'application/octet-stream';

        return Storage::disk($disk)->response($file->file_path, $file->original_name, [
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

        $disk = $this->getDiskForFile($file);

        if (! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $file->increment('download_count');

        return Storage::disk($disk)->download($file->file_path, $file->original_name);
    }

    /**
     * Toggle the public share/transfer link for a file.
     */
    public function toggleShare(Request $request, StoredFile $file): JsonResponse|RedirectResponse
    {
        if ($file->user_id !== $request->user()->id) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak.');
        }

        $newStatus = $request->has('is_public')
            ? $request->boolean('is_public')
            : ! $file->is_public;

        $file->update([
            'is_public' => $newStatus,
            'share_token' => $file->share_token ?: Str::random(40),
        ]);

        $message = $newStatus
            ? 'Tautan transfer diaktifkan! Siap disalin atau dibagikan.'
            : 'Tautan transfer dinonaktifkan.';

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_public' => $newStatus,
                'share_url' => $file->share_url,
                'message' => $message,
            ]);
        }

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

        $disk = $this->getDiskForFile($file);

        try {
            if (Storage::disk($disk)->exists($file->file_path)) {
                Storage::disk($disk)->delete($file->file_path);
            }
            if ($file->thumbnail_path && Storage::disk($disk)->exists($file->thumbnail_path)) {
                Storage::disk($disk)->delete($file->thumbnail_path);
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
     * Batch delete multiple files and/or folders simultaneously.
     */
    public function batchDestroy(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'file_ids' => ['nullable', 'array'],
            'file_ids.*' => ['integer'],
            'folder_ids' => ['nullable', 'array'],
            'folder_ids.*' => ['integer'],
        ]);

        $fileIds = $validated['file_ids'] ?? [];
        $folderIds = $validated['folder_ids'] ?? [];

        if (empty($fileIds) && empty($folderIds)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada berkas atau folder yang dipilih untuk dihapus.',
                ], 422);
            }

            return back()->with('error', 'Tidak ada berkas atau folder yang dipilih untuk dihapus.');
        }

        $user = $request->user();
        $deletedFilesCount = 0;
        $deletedFoldersCount = 0;

        // 1. Delete selected files
        if (! empty($fileIds)) {
            $files = $user->storedFiles()->whereIn('id', $fileIds)->get();
            foreach ($files as $file) {
                $disk = $this->getDiskForFile($file);
                try {
                    if (Storage::disk($disk)->exists($file->file_path)) {
                        Storage::disk($disk)->delete($file->file_path);
                    }
                    if ($file->thumbnail_path && Storage::disk($disk)->exists($file->thumbnail_path)) {
                        Storage::disk($disk)->delete($file->thumbnail_path);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Gagal menghapus file saat batch delete SwanDrive: '.$e->getMessage(), [
                        'file_id' => $file->id,
                        'file_path' => $file->file_path,
                    ]);
                }

                $file->delete();
                $deletedFilesCount++;
            }
        }

        // 2. Delete selected folders recursively
        if (! empty($folderIds)) {
            $folders = $user->folders()->whereIn('id', $folderIds)->get();
            foreach ($folders as $folder) {
                $this->deleteFolderRecursively($folder);
                $deletedFoldersCount++;
            }
        }

        $messageParts = [];
        if ($deletedFoldersCount > 0) {
            $messageParts[] = "{$deletedFoldersCount} folder";
        }
        if ($deletedFilesCount > 0) {
            $messageParts[] = "{$deletedFilesCount} berkas";
        }
        $summaryText = implode(' dan ', $messageParts).' berhasil dihapus dari SwanDrive.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $summaryText,
                'deleted_files_count' => $deletedFilesCount,
                'deleted_folders_count' => $deletedFoldersCount,
            ]);
        }

        return back()->with('success', $summaryText);
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

        $disk = $this->getDiskForFile($file);

        if (! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = $file->mime_type ?: Storage::disk($disk)->mimeType($file->file_path) ?: 'application/octet-stream';

        return Storage::disk($disk)->response($file->file_path, $file->original_name, [
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

        $disk = $this->getDiskForFile($file);

        if (! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $file->increment('download_count');

        return Storage::disk($disk)->download($file->file_path, $file->original_name);
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

        $disk = $this->getDiskForFile($file);

        if (! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = $file->mime_type ?: Storage::disk($disk)->mimeType($file->file_path) ?: 'application/octet-stream';

        return Storage::disk($disk)->response($file->file_path, $file->original_name, [
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

        $disk = $this->getDiskForFile($file);

        if (! Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $file->increment('download_count');

        return Storage::disk($disk)->download($file->file_path, $file->original_name);
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
        $tempFiles = [];

        $zip = new ZipArchive;
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $disk = $this->getDiskForFile($file);
                if (Storage::disk($disk)->exists($file->file_path)) {
                    if ($disk === 'local') {
                        $zip->addFile(Storage::disk('local')->path($file->file_path), $file->original_name);
                    } else {
                        // Download to temp file first to prevent OOM
                        $tempFile = tempnam(sys_get_temp_dir(), 'swanflow_file_');
                        $tempFiles[] = $tempFile;

                        $stream = Storage::disk($disk)->readStream($file->file_path);
                        $out = fopen($tempFile, 'w');
                        stream_copy_to_stream($stream, $out);
                        fclose($out);
                        fclose($stream);

                        $zip->addFile($tempFile, $file->original_name);
                    }
                    $file->increment('download_count');
                }
            }
            $zip->close();
        }

        // Clean up temporary files after zip is closed
        foreach ($tempFiles as $tf) {
            @unlink($tf);
        }

        return response()->download($tempZipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Handle chunked file uploads for large files over cellular/high-latency networks.
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        $request->validate([
            'chunk' => ['required', 'file', 'max:15360'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'file_uuid' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'file_name' => ['required', 'string', 'max:255'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
            'title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $folderId = $request->input('folder_id');
        if ($folderId && ! $user->folders()->where('id', $folderId)->exists()) {
            return response()->json(['error' => 'Folder tidak valid'], 403);
        }

        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $fileUuid = $request->input('file_uuid');
        $originalName = $request->input('file_name');

        $tempDir = 'temp_chunks/'.$user->id.'/'.$fileUuid;
        $chunkFile = $request->file('chunk');
        $chunkFileName = "chunk_{$chunkIndex}.part";

        // Save incoming chunk to temporary storage
        Storage::disk('local')->putFileAs($tempDir, $chunkFile, $chunkFileName);

        // If not all chunks are uploaded yet, return progress status
        if ($chunkIndex + 1 < $totalChunks) {
            return response()->json([
                'status' => 'chunk_saved',
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks,
                'progress' => round((($chunkIndex + 1) / $totalChunks) * 100),
            ]);
        }

        // All chunks arrived, assemble the complete file
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');
        ignore_user_abort(true);

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeExtension = $extension ? strtolower($extension) : 'bin';
        $finalFilename = Str::uuid().'.'.$safeExtension;
        $finalRelativePath = 'drive/'.$user->id.'/'.$finalFilename;
        $finalFullPath = Storage::disk('local')->path($finalRelativePath);

        // Ensure user's destination folder exists
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
                    $buffer = fread($inHandle, 1048576); // 1MB buffer
                    fwrite($outHandle, $buffer);
                }
                fclose($inHandle);
            }
        }
        fclose($outHandle);

        // Clean up temporary chunk files
        Storage::disk('local')->deleteDirectory($tempDir);

        $sizeBytes = filesize($finalFullPath);

        // Storage Quota Enforcement
        $userQuotaBytes = ($user->storage_quota_mb ?: 15360) * 1024 * 1024;
        $currentUsedBytes = (int) $user->storedFiles()->sum('size_bytes');
        if (($currentUsedBytes + $sizeBytes) > $userQuotaBytes) {
            @unlink($finalFullPath);

            return response()->json(['error' => 'Kapasitas penyimpanan SwanDrive Anda tidak mencukupi untuk menyimpan berkas ini.'], 422);
        }

        $mimeType = mime_content_type($finalFullPath) ?: 'application/octet-stream';
        $category = StoredFile::detectCategory($safeExtension, $mimeType);

        $disk = $this->resolveStorageDisk();

        // Generate webp thumbnail if file is an image using full local file path
        $thumbnailPath = null;
        if ($category === 'image') {
            try {
                $thumbnailPath = $this->generateWebpThumbnail($finalFullPath, $user->id, $disk);
            } catch (\Throwable $e) {
                Log::warning('Thumbnail generation error in uploadChunk: '.$e->getMessage());
            }
        }

        if ($disk !== 'local') {
            try {
                $fileStream = fopen($finalFullPath, 'r');
                Storage::disk($disk)->put($finalRelativePath, $fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
                Storage::disk('local')->delete($finalRelativePath);
            } catch (\Throwable $e) {
                Log::warning("uploadChunk transfer to [{$disk}] failed: ".$e->getMessage().'. Kept file on local disk.');
                $disk = 'local';
            }
        }

        $title = $request->filled('title')
            ? trim($request->input('title'))
            : pathinfo($originalName, PATHINFO_FILENAME);

        $storedFile = $user->storedFiles()->create([
            'folder_id' => $folderId,
            'title' => $title,
            'original_name' => $originalName,
            'file_path' => $finalRelativePath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => $mimeType,
            'extension' => $safeExtension,
            'size_bytes' => $sizeBytes,
            'category' => $category,
            'share_token' => Str::random(40),
            'is_public' => false,
            'download_count' => 0,
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Berkas berhasil disimpan ke SwanDrive!',
            'file' => [
                'id' => $storedFile->id,
                'title' => $storedFile->title,
                'size_formatted' => $storedFile->formatted_size,
            ],
        ]);
    }

    /**
     * Abort and clean up temporary chunks when an upload is cancelled.
     */
    public function abortChunkUpload(Request $request): JsonResponse
    {
        $request->validate([
            'file_uuid' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/'],
        ]);

        $user = $request->user();
        $tempDir = 'temp_chunks/'.$user->id.'/'.$request->input('file_uuid');
        Storage::disk('local')->deleteDirectory($tempDir);

        return response()->json(['status' => 'aborted']);
    }

    /**
     * Generate an optimized WebP thumbnail for images using PHP's native GD extension and upload directly to target disk.
     */
    protected function generateWebpThumbnail(string $sourcePath, int $userId, ?string $disk = null): ?string
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            return null;
        }

        if (! file_exists($sourcePath)) {
            return null;
        }

        $imageInfo = @getimagesize($sourcePath);
        if (! $imageInfo) {
            return null;
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;
        if ($origWidth <= 0 || $origHeight <= 0) {
            return null;
        }

        $srcImage = null;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = @imagecreatefromwebp($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $srcImage = @imagecreatefromgif($sourcePath);
                break;
        }

        if (! $srcImage) {
            return null;
        }

        // Target thumbnail max dimension: 320px
        $maxDimension = 320;
        $scalingFactor = min($maxDimension / $origWidth, $maxDimension / $origHeight, 1.0);
        $newWidth = max(1, (int) round($origWidth * $scalingFactor));
        $newHeight = max(1, (int) round($origHeight * $scalingFactor));

        $thumbImage = imagecreatetruecolor($newWidth, $newHeight);
        if (! $thumbImage) {
            imagedestroy($srcImage);

            return null;
        }

        // Preserve alpha transparency for PNG/WebP
        imagealphablending($thumbImage, false);
        imagesavealpha($thumbImage, true);
        $transparent = imagecolorallocatealpha($thumbImage, 255, 255, 255, 127);
        imagefilledrectangle($thumbImage, 0, 0, $newWidth, $newHeight, $transparent);
        imagealphablending($thumbImage, true);

        imagecopyresampled(
            $thumbImage,
            $srcImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        $thumbFilename = 'thumb_'.Str::uuid().'.webp';
        $thumbRelativePath = "drive/{$userId}/thumbs/{$thumbFilename}";

        ob_start();
        imagewebp($thumbImage, null, 82);
        $thumbData = ob_get_clean();

        imagedestroy($srcImage);
        imagedestroy($thumbImage);

        if ($thumbData) {
            $targetDisk = $disk ?: $this->resolveStorageDisk();
            try {
                Storage::disk($targetDisk)->put($thumbRelativePath, $thumbData);

                return $thumbRelativePath;
            } catch (\Throwable $e) {
                Log::warning("Thumbnail save to [{$targetDisk}] failed: ".$e->getMessage().'. Falling back to local disk.');
                try {
                    Storage::disk('local')->put($thumbRelativePath, $thumbData);

                    return $thumbRelativePath;
                } catch (\Throwable $e2) {
                    return null;
                }
            }
        }

        return null;
    }
}
