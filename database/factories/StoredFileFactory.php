<?php

namespace Database\Factories;

use App\Models\StoredFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StoredFile>
 */
class StoredFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = fake()->randomElement(['pdf', 'png', 'docx', 'zip', 'xlsx', 'txt']);
        $category = StoredFile::detectCategory($extension);

        return [
            'user_id' => User::factory(),
            'title' => fake()->words(3, true),
            'original_name' => fake()->slug(2).'.'.$extension,
            'file_path' => 'drive/1/'.Str::random(40).'.'.$extension,
            'mime_type' => 'application/octet-stream',
            'extension' => $extension,
            'size_bytes' => fake()->numberBetween(1024, 15728640), // 1KB to 15MB
            'category' => $category,
            'share_token' => Str::random(40),
            'is_public' => false,
            'download_count' => 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
