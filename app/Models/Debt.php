<?php

namespace App\Models;

use Database\Factories\DebtFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Fillable(['user_id', 'wallet_id', 'type', 'person_name', 'amount', 'paid_amount', 'due_date', 'status', 'notes'])]
class Debt extends Model
{
    /** @use HasFactory<DebtFactory> */
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
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    /**
     * Get the user that owns the debt.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with the debt.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope a query to only include debts (money owed to others).
     *
     * @param  Builder<static>  $query
     */
    public function scopeDebts(Builder $query): void
    {
        $query->where('type', 'debt');
    }

    /**
     * Scope a query to only include receivables (money others owe to user).
     *
     * @param  Builder<static>  $query
     */
    public function scopeReceivables(Builder $query): void
    {
        $query->where('type', 'receivable');
    }

    /**
     * Scope a query to only include unpaid or partially paid items.
     *
     * @param  Builder<static>  $query
     */
    public function scopeUnpaid(Builder $query): void
    {
        $query->whereIn('status', ['unpaid', 'partially_paid']);
    }

    /**
     * Scope a query to only include paid items.
     *
     * @param  Builder<static>  $query
     */
    public function scopePaid(Builder $query): void
    {
        $query->where('status', 'paid');
    }

    /**
     * Get remaining amount left to be paid.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    /**
     * Get repayment progress percentage.
     */
    public function getProgressPercentAttribute(): int
    {
        if ((float) $this->amount <= 0) {
            return 100;
        }

        return min(100, (int) round(((float) $this->paid_amount / (float) $this->amount) * 100));
    }

    /**
     * Record a repayment or installment, create a financial transaction, and update wallet balance.
     */
    public function recordPayment(float $paymentAmount, int $walletId, ?string $date = null, ?string $note = null): Transaction
    {
        return DB::transaction(function () use ($paymentAmount, $walletId, $date, $note) {
            $wallet = Wallet::findOrFail($walletId);

            $newPaid = (float) $this->paid_amount + $paymentAmount;
            $newStatus = $newPaid >= (float) $this->amount ? 'paid' : 'partially_paid';

            $isDebt = $this->type === 'debt';
            $txType = $isDebt ? 'expense' : 'income';

            $desc = $note ?: ($isDebt
                ? 'Pembayaran Utang ke '.$this->person_name
                : 'Penerimaan Piutang dari '.$this->person_name);

            $transaction = Transaction::create([
                'user_id' => $this->user_id,
                'wallet_id' => $wallet->id,
                'target_wallet_id' => null,
                'category_id' => null,
                'amount' => $paymentAmount,
                'type' => $txType,
                'date' => $date ?: now()->format('Y-m-d'),
                'description' => $desc,
            ]);

            if ($isDebt) {
                $wallet->decrement('balance', $paymentAmount);
            } else {
                $wallet->increment('balance', $paymentAmount);
            }

            $this->update([
                'paid_amount' => $newPaid,
                'status' => $newStatus,
            ]);

            return $transaction;
        });
    }
}
