<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            return;
        }

        $wallets = [
            ['name' => 'Dompet Utama (Cash)', 'type' => 'cash', 'balance' => 850000],
            ['name' => 'Bank BCA', 'type' => 'bank', 'balance' => 12500000],
            ['name' => 'GoPay / OVO', 'type' => 'ewallet', 'balance' => 1500000],
        ];

        foreach ($wallets as $wallet) {
            Wallet::firstOrCreate(
                ['user_id' => $user->id, 'name' => $wallet['name']],
                ['type' => $wallet['type'], 'balance' => $wallet['balance']]
            );
        }
    }
}
