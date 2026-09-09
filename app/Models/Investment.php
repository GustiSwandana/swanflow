<?php

namespace App\Models;

use Database\Factories\InvestmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'wallet_id', 'name', 'platform', 'type', 'initial_amount', 'current_value', 'target_amount', 'status', 'notes'])]
class Investment extends Model
{
    /** @use HasFactory<InvestmentFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_amount' => 'decimal:2',
            'current_value' => 'decimal:2',
            'target_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user who owns the investment.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default funding wallet for this investment.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the transactions/logs for this investment.
     *
     * @return HasMany<InvestmentTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InvestmentTransaction::class)->orderBy('date', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Scope a query to only include active investments.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    /**
     * Calculate total deposits/capital placed into this investment.
     */
    public function getTotalDepositedAttribute(): float
    {
        $hasInitialTx = $this->transactions()
            ->where('type', 'topup')
            ->where('notes', 'Setoran modal awal')
            ->exists();

        $topups = (float) $this->transactions()->where('type', 'topup')->sum('amount');

        if ($hasInitialTx) {
            return $topups;
        }

        return (float) $this->initial_amount + $topups;
    }

    /**
     * Calculate total withdrawals made from this investment.
     */
    public function getTotalWithdrawnAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'withdraw')->sum('amount');
    }

    /**
     * Calculate the net total capital invested.
     */
    public function getTotalInvestedAttribute(): float
    {
        // Exclude the initial deposit transaction log ('Setoran modal awal') to prevent double-counting with initial_amount
        $topups = (float) $this->transactions()
            ->where('type', 'topup')
            ->where(function ($q) {
                $q->whereNull('notes')->orWhere('notes', '!=', 'Setoran modal awal');
            })
            ->sum('amount');

        $withdraws = (float) $this->transactions()->where('type', 'withdraw')->sum('amount');

        $total = ((float) $this->initial_amount + $topups) - $withdraws;

        return max(0.0, (float) $total);
    }

    /**
     * Calculate the net profit or loss (current_value - total_invested).
     */
    public function getProfitLossAttribute(): float
    {
        return (float) ($this->current_value - $this->total_invested);
    }

    /**
     * Calculate the Return on Investment (ROI) percentage.
     */
    public function getRoiPercentageAttribute(): float
    {
        $invested = $this->total_invested;
        if ($invested <= 0) {
            return 0.0;
        }

        return round((($this->current_value - $invested) / $invested) * 100, 2);
    }

    /**
     * Check whether the investment is currently profitable or break-even.
     */
    public function getIsProfitAttribute(): bool
    {
        return $this->current_value >= $this->total_invested;
    }

    /**
     * Get a human-readable label for the investment type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'mutual_fund' => 'Reksadana',
            'stock' => 'Saham',
            'crypto' => 'Kripto',
            'gold' => 'Emas',
            'deposit' => 'Deposito',
            'bond' => 'SBN / Obligasi',
            'p2p' => 'P2P Lending',
            default => 'Lainnya',
        };
    }

    /**
     * Get visual badge style classes according to investment type.
     *
     * @return array{bg: string, text: string, border: string, icon: string}
     */
    public function getTypeBadgeAttribute(): array
    {
        return match ($this->type) {
            'mutual_fund' => [
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/50',
                'text' => 'text-emerald-700 dark:text-emerald-300',
                'border' => 'border-emerald-200 dark:border-emerald-800/60',
                'icon' => 'chart-pie',
            ],
            'stock' => [
                'bg' => 'bg-blue-50 dark:bg-blue-950/50',
                'text' => 'text-blue-700 dark:text-blue-300',
                'border' => 'border-blue-200 dark:border-blue-800/60',
                'icon' => 'chart-bar',
            ],
            'crypto' => [
                'bg' => 'bg-amber-50 dark:bg-amber-950/50',
                'text' => 'text-amber-700 dark:text-amber-300',
                'border' => 'border-amber-200 dark:border-amber-800/60',
                'icon' => 'currency-dollar',
            ],
            'gold' => [
                'bg' => 'bg-yellow-50 dark:bg-yellow-950/50',
                'text' => 'text-yellow-700 dark:text-yellow-300',
                'border' => 'border-yellow-200 dark:border-yellow-800/60',
                'icon' => 'sparkles',
            ],
            'deposit' => [
                'bg' => 'bg-indigo-50 dark:bg-indigo-950/50',
                'text' => 'text-indigo-700 dark:text-indigo-300',
                'border' => 'border-indigo-200 dark:border-indigo-800/60',
                'icon' => 'building-library',
            ],
            'bond' => [
                'bg' => 'bg-teal-50 dark:bg-teal-950/50',
                'text' => 'text-teal-700 dark:text-teal-300',
                'border' => 'border-teal-200 dark:border-teal-800/60',
                'icon' => 'document-text',
            ],
            'p2p' => [
                'bg' => 'bg-purple-50 dark:bg-purple-950/50',
                'text' => 'text-purple-700 dark:text-purple-300',
                'border' => 'border-purple-200 dark:border-purple-800/60',
                'icon' => 'arrows-right-left',
            ],
            default => [
                'bg' => 'bg-slate-100 dark:bg-slate-800',
                'text' => 'text-slate-700 dark:text-slate-300',
                'border' => 'border-slate-200 dark:border-slate-700',
                'icon' => 'banknotes',
            ],
        };
    }
}
