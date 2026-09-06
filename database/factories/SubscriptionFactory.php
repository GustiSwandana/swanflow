<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
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
            'name' => fake()->randomElement(['Netflix Premium', 'Spotify Family', 'iCloud 200GB', 'Indihome 50Mbps', 'Listrik PLN']),
            'amount' => fake()->randomElement([54000, 86000, 186000, 350000, 500000]),
            'cycle' => 'monthly',
            'billing_date' => fake()->numberBetween(1, 28),
            'next_due_date' => now()->addDays(fake()->numberBetween(1, 25))->format('Y-m-d'),
            'status' => 'active',
            'notes' => fake()->sentence(),
        ];
    }
}
