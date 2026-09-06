<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
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

        $wallets = Wallet::where('user_id', $user->id)->get();
        if ($wallets->isEmpty()) {
            return;
        }

        $bca = $wallets->firstWhere('type', 'bank') ?? $wallets->first();
        $cash = $wallets->firstWhere('type', 'cash') ?? $wallets->first();
        $ewallet = $wallets->firstWhere('type', 'ewallet') ?? $wallets->first();

        $foodCat = Category::where('name', 'Makanan & Minuman')->first();
        $transportCat = Category::where('name', 'Transportasi')->first();
        $salaryCat = Category::where('name', 'Gaji Pokok')->first();
        $groceryCat = Category::where('name', 'Belanja Harian')->first();

        $transactions = [
            [
                'user_id' => $user->id,
                'wallet_id' => $bca->id,
                'category_id' => $salaryCat?->id,
                'amount' => 10000000,
                'type' => TransactionType::Income,
                'date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'description' => 'Gaji Bulan Ini',
            ],
            [
                'user_id' => $user->id,
                'wallet_id' => $cash->id,
                'category_id' => $foodCat?->id,
                'amount' => 45000,
                'type' => TransactionType::Expense,
                'date' => Carbon::today()->format('Y-m-d'),
                'description' => 'Makan Siang Nasi Padang',
            ],
            [
                'user_id' => $user->id,
                'wallet_id' => $ewallet->id,
                'category_id' => $transportCat?->id,
                'amount' => 25000,
                'type' => TransactionType::Expense,
                'date' => Carbon::today()->format('Y-m-d'),
                'description' => 'Ongkos Ojek Online',
            ],
            [
                'user_id' => $user->id,
                'wallet_id' => $bca->id,
                'category_id' => $groceryCat?->id,
                'amount' => 320000,
                'type' => TransactionType::Expense,
                'date' => Carbon::yesterday()->format('Y-m-d'),
                'description' => 'Belanja Mingguan Supermarket',
            ],
            [
                'user_id' => $user->id,
                'wallet_id' => $bca->id,
                'target_wallet_id' => $ewallet->id,
                'category_id' => null,
                'amount' => 500000,
                'type' => TransactionType::Transfer,
                'date' => Carbon::today()->format('Y-m-d'),
                'description' => 'Top-up Saldo GoPay dari BCA',
            ],
        ];

        foreach ($transactions as $data) {
            Transaction::create($data);
        }
    }
}
