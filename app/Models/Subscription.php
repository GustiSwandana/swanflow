<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Fillable(['user_id', 'wallet_id', 'category_id', 'name', 'amount', 'cycle', 'billing_date', 'next_due_date', 'status', 'notes'])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'billing_date' => 'integer',
            'next_due_date' => 'date',
        ];
    }

    /**
     * Get the user that owns the subscription.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with the subscription.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the category associated with the subscription.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include active subscriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    /**
     * Mark this subscription as paid, create transaction, deduct balance, and advance next due date.
     */
    public function markAsPaid(?int $walletId = null, ?string $date = null): Transaction
    {
        return DB::transaction(function () use ($walletId, $date) {
            $effectiveWalletId = $walletId ?: $this->wallet_id;
            $wallet = Wallet::findOrFail($effectiveWalletId);

            $transaction = Transaction::create([
                'user_id' => $this->user_id,
                'wallet_id' => $wallet->id,
                'target_wallet_id' => null,
                'category_id' => $this->category_id,
                'amount' => $this->amount,
                'type' => 'expense',
                'date' => $date ?: now()->format('Y-m-d'),
                'description' => 'Pembayaran Rutin: '.$this->name,
            ]);

            $wallet->decrement('balance', $this->amount);

            $currentDue = Carbon::parse($this->next_due_date);
            $nextDue = match ($this->cycle) {
                'weekly' => $currentDue->addWeek(),
                'yearly' => $currentDue->addYear(),
                default => $currentDue->addMonth(),
            };

            $this->update([
                'next_due_date' => $nextDue->format('Y-m-d'),
            ]);

            return $transaction;
        });
    }
}
