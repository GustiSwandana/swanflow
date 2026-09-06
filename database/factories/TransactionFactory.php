<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
            'category_id' => Category::factory(),
            'amount' => fake()->randomFloat(2, 15000, 1500000),
            'type' => fake()->randomElement([TransactionType::Income, TransactionType::Expense]),
            'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'description' => fake()->sentence(3),
        ];
    }

    /**
     * Indicate that the transaction is an income.
     */
    public function income(): static
    {
        return $this->state(fn () => [
            'type' => TransactionType::Income,
        ]);
    }

    /**
     * Indicate that the transaction is an expense.
     */
    public function expense(): static
    {
        return $this->state(fn () => [
            'type' => TransactionType::Expense,
        ]);
    }
}
