<?php

namespace Database\Factories;

use App\Models\Investment;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Investment>
 */
class InvestmentFactory extends Factory
{
    protected $model = Investment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomElement([500000, 1000000, 2500000, 5000000, 10000000]);
        $multiplier = fake()->randomFloat(2, 0.90, 1.25);
        $currentValue = round($amount * $multiplier, 2);

        return [
            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
            'name' => fake()->randomElement([
                'Bibit - Reksadana Obligasi Manulife',
                'BBCA - Bank Central Asia',
                'BBRI - Bank Rakyat Indonesia',
                'Antam Emas Batangan 10g',
                'Bitcoin (BTC) Portofolio',
                'SBN ORI025',
                'Deposito Mandiri 1 Bulan',
            ]),
            'platform' => fake()->randomElement(['Bibit', 'Stockbit', 'Ajaib', 'Pluang', 'Bank Mandiri', 'BCA']),
            'type' => fake()->randomElement(['mutual_fund', 'stock', 'crypto', 'gold', 'deposit', 'bond']),
            'initial_amount' => $amount,
            'current_value' => $currentValue,
            'target_amount' => $amount * 2,
            'status' => 'active',
            'notes' => fake()->sentence(),
        ];
    }
}
