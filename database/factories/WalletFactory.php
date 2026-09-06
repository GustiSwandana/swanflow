<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
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
            'name' => fake()->randomElement(['Dompet Utama', 'Bank BCA', 'Bank Mandiri', 'GoPay', 'OVO']),
            'type' => fake()->randomElement(['cash', 'bank', 'ewallet']),
            'balance' => fake()->randomFloat(2, 50000, 10000000),
        ];
    }
}
