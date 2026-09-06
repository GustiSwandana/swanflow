<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoredFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'original_name',
        'file_path',
        'mime_type',
        'extension',
        'size_bytes',
        'category',
        'share_token',
        'is_public',
        'download_count',
        'notes',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'is_public' => 'boolean',
        'download_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format file size to human-readable format (B, KB, MB, GB).
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2, ',', '.').' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.').' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return $bytes.' B';
    }

    /**
     * Get the public share URL for transferring this file.
     */
    public function getShareUrlAttribute(): string
    {
        return route('drive.shared.view', ['token' => $this->share_token]);
    }

    /**
     * Auto-detect category based on extension and mime type.
     */
    public static function detectCategory(string $extension, ?string $mimeType = null): string
    {
        $ext = strtolower($extension);

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'heic', 'bmp'])) {
            return 'image';
        }

        if (in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf', 'md'])) {
            return 'document';
        }

        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'])) {
            return 'archive';
        }

        return 'other';
    }

    /**
     * Category visual metadata without using blue classes.
     *
     * @return array{label: string, bg: string, text: string, icon: string}
     */
    public function categoryMeta(): array
    {
        return match ($this->category) {
            'image' => [
                'label' => 'Gambar',
                'bg' => 'bg-purple-100 dark:bg-purple-950/50',
                'text' => 'text-purple-600 dark:text-purple-400',
                'border' => 'border-purple-200 dark:border-purple-900/50',
                'icon' => 'image',
            ],
            'document' => [
                'label' => 'Dokumen',
                'bg' => 'bg-emerald-100 dark:bg-emerald-950/50',
                'text' => 'text-emerald-600 dark:text-emerald-400',
                'border' => 'border-emerald-200 dark:border-emerald-900/50',
                'icon' => 'document',
            ],
            'archive' => [
                'label' => 'Arsip',
                'bg' => 'bg-amber-100 dark:bg-amber-950/50',
                'text' => 'text-amber-600 dark:text-amber-400',
                'border' => 'border-amber-200 dark:border-amber-900/50',
                'icon' => 'archive',
            ],
            default => [
                'label' => 'Berkas',
                'bg' => 'bg-slate-100 dark:bg-slate-800',
                'text' => 'text-slate-600 dark:text-slate-300',
                'border' => 'border-slate-200 dark:border-slate-700',
                'icon' => 'other',
            ],
        };
    }
}
