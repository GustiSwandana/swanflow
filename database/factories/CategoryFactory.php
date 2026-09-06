<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
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
            'name' => fake()->randomElement(['Makanan & Minuman', 'Gaji Bulanan', 'Transportasi', 'Belanja', 'Tagihan']),
            'type' => fake()->randomElement([TransactionType::Income, TransactionType::Expense]),
            'icon' => 'tag',
            'color' => '#3B82F6',
        ];
    }

    /**
     * Indicate that the category is for income.
     */
    public function income(): static
    {
        return $this->state(fn () => [
            'type' => TransactionType::Income,
            'name' => 'Gaji & Pendapatan',
            'color' => '#10B981',
        ]);
    }

    /**
     * Indicate that the category is for expense.
     */
    public function expense(): static
    {
        return $this->state(fn () => [
            'type' => TransactionType::Expense,
            'name' => 'Kebutuhan Harian',
            'color' => '#F43F5E',
        ]);
    }
}
