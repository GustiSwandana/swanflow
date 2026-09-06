<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomElement([150000, 300000, 500000, 1000000, 2500000]);

        return [
            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
            'type' => fake()->randomElement(['debt', 'receivable']),
            'person_name' => fake()->name(),
            'amount' => $amount,
            'paid_amount' => 0,
            'due_date' => now()->addDays(fake()->numberBetween(5, 60))->format('Y-m-d'),
            'status' => 'unpaid',
            'notes' => fake()->sentence(),
        ];
    }
}
