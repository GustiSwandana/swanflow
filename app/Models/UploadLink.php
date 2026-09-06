<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UploadLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'token',
        'description',
        'expires_at',
        'max_files',
        'uploaded_files_count',
        'max_file_size_mb',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'max_files' => 'integer',
        'uploaded_files_count' => 'integer',
        'max_file_size_mb' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(StoredFile::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isLimitReached(): bool
    {
        return $this->uploaded_files_count >= $this->max_files;
    }

    public function canAcceptUpload(): bool
    {
        return $this->is_active && ! $this->isExpired() && ! $this->isLimitReached();
    }

    public function remainingSlots(): int
    {
        return max(0, $this->max_files - $this->uploaded_files_count);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('drive.drop.view', ['token' => $this->token]);
    }

    /**
     * Get badge visual metadata without using blue classes.
     *
     * @return array{label: string, bg: string, text: string, border: string}
     */
    public function statusMeta(): array
    {
        if (! $this->is_active) {
            return [
                'label' => 'Ditutup',
                'bg' => 'bg-slate-100 dark:bg-slate-800',
                'text' => 'text-slate-500 dark:text-slate-400',
                'border' => 'border-slate-200 dark:border-slate-700',
            ];
        }

        if ($this->isExpired()) {
            return [
                'label' => 'Kedaluwarsa',
                'bg' => 'bg-rose-100 dark:bg-rose-950/50',
                'text' => 'text-rose-600 dark:text-rose-400',
                'border' => 'border-rose-200 dark:border-rose-900/50',
            ];
        }

        if ($this->isLimitReached()) {
            return [
                'label' => 'Penuh',
                'bg' => 'bg-amber-100 dark:bg-amber-950/50',
                'text' => 'text-amber-700 dark:text-amber-400',
                'border' => 'border-amber-200 dark:border-amber-900/50',
            ];
        }

        return [
            'label' => 'Aktif',
            'bg' => 'bg-emerald-100 dark:bg-emerald-950/50',
            'text' => 'text-emerald-700 dark:text-emerald-400',
            'border' => 'border-emerald-200 dark:border-emerald-900/50',
        ];
    }
}
