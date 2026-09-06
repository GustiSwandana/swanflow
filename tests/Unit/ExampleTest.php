<?php

namespace Tests\Unit;

use App\Enums\TransactionType;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_transaction_type_values_and_labels(): void
    {
        $this->assertSame('income', TransactionType::Income->value);
        $this->assertSame('expense', TransactionType::Expense->value);
        $this->assertSame('transfer', TransactionType::Transfer->value);

        $this->assertSame('Pemasukan', TransactionType::Income->label());
        $this->assertSame('Pengeluaran', TransactionType::Expense->label());
        $this->assertSame('Transfer', TransactionType::Transfer->label());
    }

    public function test_transaction_type_boolean_helpers(): void
    {
        $this->assertTrue(TransactionType::Income->isIncome());
        $this->assertFalse(TransactionType::Income->isExpense());
        $this->assertFalse(TransactionType::Income->isTransfer());

        $this->assertTrue(TransactionType::Expense->isExpense());
        $this->assertFalse(TransactionType::Expense->isIncome());

        $this->assertTrue(TransactionType::Transfer->isTransfer());
        $this->assertFalse(TransactionType::Transfer->isIncome());
    }
}
