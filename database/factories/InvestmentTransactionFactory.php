<?php

namespace Database\Factories;

use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvestmentTransaction>
 */
class InvestmentTransactionFactory extends Factory
{
    protected $model = InvestmentTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'investment_id' => Investment::factory(),
            'wallet_id' => Wallet::factory(),
            'type' => 'topup',
            'amount' => fake()->randomElement([100000, 250000, 500000, 1000000]),
            'date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
            'notes' => fake()->sentence(),
            'affects_wallet' => true,
        ];
    }
}
