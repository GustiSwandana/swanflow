<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineTransactionSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_offline_transaction_can_be_stored_with_client_uuid_via_regular_store(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $clientUuid = 'sf_offline_test_'.uniqid();

        $response = $this->actingAs($user)->postJson('/transactions', [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50000.00,
            'date' => now()->toDateString(),
            'description' => 'Makan siang offline',
            'client_uuid' => $clientUuid,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'client_uuid' => $clientUuid,
            'amount' => 50000.00,
            'description' => 'Makan siang offline',
        ]);

        $wallet->refresh();
        $this->assertEquals(450000.00, (float) $wallet->balance);
    }

    public function test_duplicate_client_uuid_is_idempotent_in_store(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $clientUuid = 'sf_offline_idempotent_'.uniqid();

        // First post
        $response1 = $this->actingAs($user)->postJson('/transactions', [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50000.00,
            'date' => now()->toDateString(),
            'description' => 'Beli kopi offline',
            'client_uuid' => $clientUuid,
        ]);
        $response1->assertOk();

        // Second post with identical client_uuid (simulating retry or re-sync)
        $response2 = $this->actingAs($user)->postJson('/transactions', [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50000.00,
            'date' => now()->toDateString(),
            'description' => 'Beli kopi offline',
            'client_uuid' => $clientUuid,
        ]);

        $response2->assertOk();
        $response2->assertJson([
            'success' => true,
            'duplicate' => true,
        ]);

        // Assert database only has 1 record
        $this->assertEquals(1, Transaction::where('client_uuid', $clientUuid)->count());

        // Balance should be deducted ONLY once
        $wallet->refresh();
        $this->assertEquals(450000.00, (float) $wallet->balance);
    }

    public function test_batch_offline_transactions_sync_creates_transactions_and_updates_balances(): void
    {
        $user = User::factory()->create();
        $wallet1 = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000.00,
        ]);
        $wallet2 = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 200000.00,
        ]);
        $expenseCat = Category::factory()->expense()->create(['user_id' => $user->id]);
        $incomeCat = Category::factory()->income()->create(['user_id' => $user->id]);

        $uuid1 = 'sf_sync_exp_'.uniqid();
        $uuid2 = 'sf_sync_inc_'.uniqid();
        $uuid3 = 'sf_sync_trf_'.uniqid();

        $payload = [
            'transactions' => [
                [
                    'client_uuid' => $uuid1,
                    'wallet_id' => $wallet1->id,
                    'category_id' => $expenseCat->id,
                    'type' => 'expense',
                    'amount' => 100000,
                    'date' => now()->toDateString(),
                    'description' => 'Bensin motor offline',
                ],
                [
                    'client_uuid' => $uuid2,
                    'wallet_id' => $wallet1->id,
                    'category_id' => $incomeCat->id,
                    'type' => 'income',
                    'amount' => 500000,
                    'date' => now()->toDateString(),
                    'description' => 'Honor photoshoot cash offline',
                ],
                [
                    'client_uuid' => $uuid3,
                    'wallet_id' => $wallet1->id,
                    'target_wallet_id' => $wallet2->id,
                    'type' => 'transfer',
                    'amount' => 200000,
                    'admin_fee' => 2500,
                    'fee_payer' => 'source',
                    'date' => now()->toDateString(),
                    'description' => 'Pindah ke tabungan offline',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/transactions/sync', $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'synced_count' => 3,
            'duplicate_count' => 0,
            'failed_count' => 0,
        ]);

        $this->assertDatabaseHas('transactions', ['client_uuid' => $uuid1]);
        $this->assertDatabaseHas('transactions', ['client_uuid' => $uuid2]);
        $this->assertDatabaseHas('transactions', ['client_uuid' => $uuid3]);

        // Wallet 1: 1,000,000 - 100,000 + 500,000 - (200,000 + 2,500) = 1,197,500
        $wallet1->refresh();
        $this->assertEquals(1197500.00, (float) $wallet1->balance);

        // Wallet 2: 200,000 + 200,000 = 400,000
        $wallet2->refresh();
        $this->assertEquals(400000.00, (float) $wallet2->balance);
    }

    public function test_batch_offline_transactions_sync_is_idempotent_for_already_synced_transactions(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $uuidExisting = 'sf_existing_'.uniqid();
        $uuidNew = 'sf_new_'.uniqid();

        // Pre-create existing transaction
        Transaction::factory()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50000.00,
            'client_uuid' => $uuidExisting,
        ]);
        $wallet->decrement('balance', 50000.00); // 450,000

        $payload = [
            'transactions' => [
                [
                    'client_uuid' => $uuidExisting,
                    'wallet_id' => $wallet->id,
                    'category_id' => $category->id,
                    'type' => 'expense',
                    'amount' => 50000.00,
                    'date' => now()->toDateString(),
                    'description' => 'Kopi kemarin',
                ],
                [
                    'client_uuid' => $uuidNew,
                    'wallet_id' => $wallet->id,
                    'category_id' => $category->id,
                    'type' => 'expense',
                    'amount' => 25000.00,
                    'date' => now()->toDateString(),
                    'description' => 'Snack baru',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/transactions/sync', $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'synced_count' => 1,
            'duplicate_count' => 1,
            'failed_count' => 0,
        ]);

        $wallet->refresh();
        // 450,000 - 25,000 = 425,000 (existing 50,000 was NOT deducted again)
        $this->assertEquals(425000.00, (float) $wallet->balance);
    }
}
