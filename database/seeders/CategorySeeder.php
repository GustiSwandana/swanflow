<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            // Expense Categories
            ['name' => 'Makanan & Minuman', 'type' => TransactionType::Expense, 'icon' => 'utensils', 'color' => '#F43F5E'],
            ['name' => 'Transportasi', 'type' => TransactionType::Expense, 'icon' => 'car', 'color' => '#F59E0B'],
            ['name' => 'Belanja Harian', 'type' => TransactionType::Expense, 'icon' => 'shopping-bag', 'color' => '#8B5CF6'],
            ['name' => 'Tagihan & Utilitas', 'type' => TransactionType::Expense, 'icon' => 'receipt', 'color' => '#EF4444'],
            ['name' => 'Hiburan', 'type' => TransactionType::Expense, 'icon' => 'film', 'color' => '#EC4899'],
            ['name' => 'Kesehatan', 'type' => TransactionType::Expense, 'icon' => 'heart-pulse', 'color' => '#06B6D4'],

            // Income Categories
            ['name' => 'Gaji Pokok', 'type' => TransactionType::Income, 'icon' => 'banknotes', 'color' => '#10B981'],
            ['name' => 'Freelance & Bonus', 'type' => TransactionType::Income, 'icon' => 'sparkles', 'color' => '#14B8A6'],
            ['name' => 'Investasi & Dividen', 'type' => TransactionType::Income, 'icon' => 'arrow-trending-up', 'color' => '#3B82F6'],
        ];

        foreach ($defaultCategories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type'], 'user_id' => null],
                ['icon' => $cat['icon'], 'color' => $cat['color']]
            );
        }
    }
}
