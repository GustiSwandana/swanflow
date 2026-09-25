<?php

namespace App\Models;

use Database\Factories\FolderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Folder extends Model
{
    /** @use HasFactory<FolderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'color',
        'share_token',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * User who owns this folder.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parent folder if nested.
     *
     * @return BelongsTo<Folder, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Sub-folders inside this folder.
     *
     * @return HasMany<Folder, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Stored files inside this folder.
     *
     * @return HasMany<StoredFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(StoredFile::class);
    }

    /**
     * Upload links associated with this folder.
     *
     * @return HasMany<UploadLink, $this>
     */
    public function uploadLinks(): HasMany
    {
        return $this->hasMany(UploadLink::class);
    }

    /**
     * Check if this folder is dedicated for received public/drop uploads.
     */
    public function isDropFolder(): bool
    {
        if (str_starts_with($this->name, '📥')) {
            return true;
        }

        try {
            return $this->uploadLinks()->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Calculate total bytes of files directly in this folder.
     */
    public function getTotalSizeBytesAttribute(): int
    {
        return (int) $this->files()->sum('size_bytes');
    }

    /**
     * Format total size to human-readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->total_size_bytes;
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
     * Public share URL for this folder.
     */
    public function getShareUrlAttribute(): string
    {
        return route('drive.shared.folder.view', ['token' => $this->share_token]);
    }

    /**
     * Get ancestors list for breadcrumbs.
     *
     * @return Collection<int, Folder>
     */
    public function breadcrumbs(): Collection
    {
        $crumbs = collect();
        $current = $this;

        while ($current) {
            $crumbs->prepend($current);
            $current = $current->parent;
        }

        return $crumbs;
    }

    /**
     * Color theme meta for visual rendering.
     *
     * @return array{bg: string, text: string, border: string, iconBg: string, ring: string, glow: string}
     */
    public function colorMeta(): array
    {
        return match ($this->color) {
            'amber' => [
                'bg' => 'bg-amber-500/10 dark:bg-amber-500/15',
                'text' => 'text-amber-600 dark:text-amber-400',
                'border' => 'border-amber-400/30 dark:border-amber-500/30',
                'iconBg' => 'bg-amber-500/20 text-amber-600 dark:text-amber-300',
                'ring' => 'focus:ring-amber-500',
                'glow' => 'from-amber-500/20 to-amber-600/10',
            ],
            'purple' => [
                'bg' => 'bg-purple-500/10 dark:bg-purple-500/15',
                'text' => 'text-purple-600 dark:text-purple-400',
                'border' => 'border-purple-400/30 dark:border-purple-500/30',
                'iconBg' => 'bg-purple-500/20 text-purple-600 dark:text-purple-300',
                'ring' => 'focus:ring-purple-500',
                'glow' => 'from-purple-500/20 to-purple-600/10',
            ],
            'emerald' => [
                'bg' => 'bg-emerald-500/10 dark:bg-emerald-500/15',
                'text' => 'text-emerald-600 dark:text-emerald-400',
                'border' => 'border-emerald-400/30 dark:border-emerald-500/30',
                'iconBg' => 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-300',
                'ring' => 'focus:ring-emerald-500',
                'glow' => 'from-emerald-500/20 to-emerald-600/10',
            ],
            'rose' => [
                'bg' => 'bg-rose-500/10 dark:bg-rose-500/15',
                'text' => 'text-rose-600 dark:text-rose-400',
                'border' => 'border-rose-400/30 dark:border-rose-500/30',
                'iconBg' => 'bg-rose-500/20 text-rose-600 dark:text-rose-300',
                'ring' => 'focus:ring-rose-500',
                'glow' => 'from-rose-500/20 to-rose-600/10',
            ],
            'indigo' => [
                'bg' => 'bg-indigo-500/10 dark:bg-indigo-500/15',
                'text' => 'text-indigo-600 dark:text-indigo-400',
                'border' => 'border-indigo-400/30 dark:border-indigo-500/30',
                'iconBg' => 'bg-indigo-500/20 text-indigo-600 dark:text-indigo-300',
                'ring' => 'focus:ring-indigo-500',
                'glow' => 'from-indigo-500/20 to-indigo-600/10',
            ],
            'sky' => [
                'bg' => 'bg-sky-500/10 dark:bg-sky-500/15',
                'text' => 'text-sky-600 dark:text-sky-400',
                'border' => 'border-sky-400/30 dark:border-sky-500/30',
                'iconBg' => 'bg-sky-500/20 text-sky-600 dark:text-sky-300',
                'ring' => 'focus:ring-sky-500',
                'glow' => 'from-sky-500/20 to-sky-600/10',
            ],
            default => [
                'bg' => 'bg-teal-500/10 dark:bg-teal-500/15',
                'text' => 'text-teal-600 dark:text-teal-400',
                'border' => 'border-teal-400/30 dark:border-teal-500/30',
                'iconBg' => 'bg-teal-500/20 text-teal-600 dark:text-teal-300',
                'ring' => 'focus:ring-teal-500',
                'glow' => 'from-teal-500/20 to-teal-600/10',
            ],
        };
    }
}
