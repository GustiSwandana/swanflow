<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isCompleted = fake()->boolean(30);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4, true),
            'description' => fake()->optional()->sentence(),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'category' => fake()->randomElement(['kerja', 'pribadi', 'belanja', 'keuangan', null]),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? fake()->dateTimeThisMonth() : null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => today(),
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }
}
