<?php

namespace App\Models;

use App\Enums\TransactionType;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'wallet_id', 'target_wallet_id', 'category_id', 'amount', 'type', 'date', 'description'])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
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
            'date' => 'date',
            'type' => TransactionType::class,
        ];
    }

    /**
     * Get the user who owns the transaction.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with the transaction.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the category of the transaction.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the target wallet associated with a transfer transaction.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function targetWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'target_wallet_id');
    }

    /**
     * Scope a query to only include income transactions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeIncome(Builder $query): void
    {
        $query->where('type', TransactionType::Income);
    }

    /**
     * Scope a query to only include expense transactions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeExpense(Builder $query): void
    {
        $query->where('type', TransactionType::Expense);
    }

    /**
     * Scope a query to only include transfer transactions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeTransfer(Builder $query): void
    {
        $query->where('type', TransactionType::Transfer);
    }

    /**
     * Scope a query to order transactions by date descending.
     *
     * @param  Builder<static>  $query
     */
    public function scopeRecent(Builder $query, int $limit = 10): void
    {
        $query->latest('date')->latest('id')->limit($limit);
    }
}
