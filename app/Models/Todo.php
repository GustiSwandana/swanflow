<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'category',
        'due_date',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array{label: string, color: string}
     */
    public function priorityBadge(): array
    {
        return match ($this->priority) {
            'high' => ['label' => 'Tinggi', 'color' => 'text-rose-600 dark:text-rose-400 bg-rose-100 dark:bg-rose-900/30'],
            'low' => ['label' => 'Rendah', 'color' => 'text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50'],
            default => ['label' => 'Sedang', 'color' => 'text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30'],
        };
    }

    /**
     * @return array{label: string, icon: string}
     */
    public function categoryBadge(): array
    {
        return match ($this->category) {
            'kerja' => ['label' => 'Kerja', 'icon' => '💼'],
            'pribadi' => ['label' => 'Pribadi', 'icon' => '👤'],
            'belanja' => ['label' => 'Belanja', 'icon' => '🛒'],
            'keuangan' => ['label' => 'Keuangan', 'icon' => '💰'],
            default => ['label' => 'Umum', 'icon' => '📌'],
        };
    }
}
