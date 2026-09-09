<?php

namespace App\Models;

use Database\Factories\InvestmentTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['investment_id', 'wallet_id', 'type', 'amount', 'date', 'notes', 'affects_wallet'])]
class InvestmentTransaction extends Model
{
    /** @use HasFactory<InvestmentTransactionFactory> */
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
            'affects_wallet' => 'boolean',
        ];
    }

    /**
     * Get the investment this transaction belongs to.
     *
     * @return BelongsTo<Investment, $this>
     */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * Get the wallet associated with this transaction.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get human-readable label for transaction type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'topup' => 'Beli / Setor Modal',
            'withdraw' => 'Jual / Tarik Dana',
            'dividend' => 'Bagi Hasil / Dividen',
            default => 'Transaksi',
        };
    }
}
