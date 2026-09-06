<?php

namespace Database\Factories;

use App\Models\UploadLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UploadLink>
 */
class UploadLinkFactory extends Factory
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
            'title' => fake()->words(3, true),
            'token' => Str::random(40),
            'description' => fake()->sentence(),
            'expires_at' => now()->addDays(3),
            'max_files' => 5,
            'uploaded_files_count' => 0,
            'max_file_size_mb' => 25,
            'is_active' => true,
        ];
    }
}
