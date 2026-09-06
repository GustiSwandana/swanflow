<?php

namespace App\Enums;

enum TransactionType: string
{
    case Income = 'income';
    case Expense = 'expense';
    case Transfer = 'transfer';

    /**
     * Get the human-friendly label for the transaction type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Income => 'Pemasukan',
            self::Expense => 'Pengeluaran',
            self::Transfer => 'Transfer',
        };
    }

    /**
     * Check if the transaction is an income.
     */
    public function isIncome(): bool
    {
        return $this === self::Income;
    }

    /**
     * Check if the transaction is an expense.
     */
    public function isExpense(): bool
    {
        return $this === self::Expense;
    }

    /**
     * Check if the transaction is a transfer.
     */
    public function isTransfer(): bool
    {
        return $this === self::Transfer;
    }
}
