<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_an_income_transaction_and_wallet_balance_increases(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000.00,
        ]);
        $category = Category::factory()->income()->create(['user_id' => $user->id]);

        $payload = [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 500000.00,
            'date' => now()->toDateString(),
            'description' => 'Bonus proyek',
        ];

        $response = $this->actingAs($user)->post('/transactions', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil disimpan!');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 500000.00,
            'description' => 'Bonus proyek',
        ]);

        $wallet->refresh();
        $this->assertEquals(1500000.00, (float) $wallet->balance);
    }

    public function test_user_can_create_an_expense_transaction_and_wallet_balance_decreases(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $payload = [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 150000.00,
            'date' => now()->toDateString(),
            'description' => 'Belanja mingguan',
        ];

        $response = $this->actingAs($user)->post('/transactions', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil disimpan!');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 150000.00,
        ]);

        $wallet->refresh();
        $this->assertEquals(850000.00, (float) $wallet->balance);
    }

    public function test_user_can_delete_a_transaction_and_wallet_balance_reverts(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 850000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 150000.00,
        ]);

        $response = $this->actingAs($user)->delete("/transactions/{$transaction->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil dihapus!');

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);

        $wallet->refresh();
        $this->assertEquals(1000000.00, (float) $wallet->balance);
    }

    public function test_transactions_index_page_displays_filtered_transactions(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id, 'name' => 'Transportasi']);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 45000.00,
            'description' => 'Naik taksi',
        ]);

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Transaksi');
        $response->assertSee('Naik taksi');
        $response->assertSee('Transportasi');
        $response->assertSee('45.000');
    }

    public function test_reports_page_calculates_cashflow_and_breakdown(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $foodCat = Category::factory()->expense()->create(['user_id' => $user->id, 'name' => 'Makanan']);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $foodCat->id,
            'amount' => 200000.00,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/reports?month='.now()->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan');
        $response->assertSee('Makanan');
        $response->assertSee('200.000');
        $response->assertSee('100%');
    }

    public function test_validation_prevents_saving_invalid_transaction(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/transactions', [
            'amount' => -100,
            'wallet_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['amount', 'wallet_id', 'category_id', 'type', 'date']);
    }

    public function test_user_can_transfer_between_wallets_and_balances_update_atomically(): void
    {
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bank BCA',
            'balance' => 1000000.00,
        ]);
        $targetWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'balance' => 500000.00,
        ]);

        $payload = [
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'type' => 'transfer',
            'amount' => 300000.00,
            'date' => now()->toDateString(),
            'description' => 'Top-up GoPay dari BCA',
        ];

        $response = $this->actingAs($user)->post('/transactions', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transfer saldo berhasil disimpan!');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'type' => 'transfer',
            'amount' => 300000.00,
            'description' => 'Top-up GoPay dari BCA',
        ]);

        $sourceWallet->refresh();
        $targetWallet->refresh();

        $this->assertEquals(700000.00, (float) $sourceWallet->balance);
        $this->assertEquals(800000.00, (float) $targetWallet->balance);
    }

    public function test_transfer_validation_fails_if_source_and_destination_are_same(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $payload = [
            'wallet_id' => $wallet->id,
            'target_wallet_id' => $wallet->id,
            'type' => 'transfer',
            'amount' => 50000.00,
            'date' => now()->toDateString(),
        ];

        $response = $this->actingAs($user)->post('/transactions', $payload);

        $response->assertSessionHasErrors(['target_wallet_id']);
    }

    public function test_deleting_transfer_reverts_both_wallets_balances(): void
    {
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 700000.00,
        ]);
        $targetWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 800000.00,
        ]);

        $transfer = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'type' => 'transfer',
            'amount' => 300000.00,
            'date' => now()->toDateString(),
            'description' => 'Top-up GoPay',
        ]);

        $response = $this->actingAs($user)->delete("/transactions/{$transfer->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil dihapus!');

        $sourceWallet->refresh();
        $targetWallet->refresh();

        $this->assertEquals(1000000.00, (float) $sourceWallet->balance);
        $this->assertEquals(500000.00, (float) $targetWallet->balance);
        $this->assertDatabaseMissing('transactions', ['id' => $transfer->id]);
    }

    public function test_transfer_does_not_affect_monthly_income_and_expense_reports(): void
    {
        $user = User::factory()->create();
        $source = Wallet::factory()->create(['user_id' => $user->id]);
        $target = Wallet::factory()->create(['user_id' => $user->id]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $source->id,
            'target_wallet_id' => $target->id,
            'type' => 'transfer',
            'amount' => 500000.00,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/reports?month='.now()->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee('+Rp 0');
        $response->assertSee('-Rp 0');
    }

    public function test_transactions_index_displays_transfers(): void
    {
        $user = User::factory()->create();
        $source = Wallet::factory()->create(['user_id' => $user->id, 'name' => 'Bank BCA']);
        $target = Wallet::factory()->create(['user_id' => $user->id, 'name' => 'GoPay']);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $source->id,
            'target_wallet_id' => $target->id,
            'type' => 'transfer',
            'amount' => 125000.00,
            'date' => now()->toDateString(),
            'description' => 'Isi GoPay',
        ]);

        $response = $this->actingAs($user)->get('/transactions?type=transfer');

        $response->assertStatus(200);
        $response->assertSee('Isi GoPay');
        $response->assertSee('Bank BCA');
        $response->assertSee('GoPay');
        $response->assertSee('125.000');
        $response->assertSee('Transfer');
    }

    public function test_user_can_update_an_expense_transaction_and_wallet_balance_adjusts_correctly(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 850000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 150000.00,
            'description' => 'Belanja awal',
        ]);

        $updatePayload = [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 250000.00,
            'date' => now()->toDateString(),
            'description' => 'Belanja bertambah',
        ];

        $response = $this->actingAs($user)->put("/transactions/{$transaction->id}", $updatePayload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui!');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 250000.00,
            'description' => 'Belanja bertambah',
        ]);

        $wallet->refresh();
        // Old was 150,000 (850,000 + 150,000 = 1,000,000), new is 250,000 (1,000,000 - 250,000 = 750,000)
        $this->assertEquals(750000.00, (float) $wallet->balance);
    }

    public function test_user_can_update_transaction_wallet_and_both_wallets_adjust(): void
    {
        $user = User::factory()->create();
        $walletA = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 850000.00,
        ]);
        $walletB = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $walletA->id,
            'category_id' => $category->id,
            'amount' => 150000.00,
        ]);

        // Move to Wallet B with amount 200,000
        $updatePayload = [
            'wallet_id' => $walletB->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 200000.00,
            'date' => now()->toDateString(),
            'description' => 'Pindah dompet bayar',
        ];

        $response = $this->actingAs($user)->put("/transactions/{$transaction->id}", $updatePayload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui!');

        $walletA->refresh();
        $walletB->refresh();

        // Wallet A reverted: 850,000 + 150,000 = 1,000,000
        $this->assertEquals(1000000.00, (float) $walletA->balance);
        // Wallet B applied: 500,000 - 200,000 = 300,000
        $this->assertEquals(300000.00, (float) $walletB->balance);
    }

    public function test_user_can_update_transfer_transaction_and_balances_adjust(): void
    {
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 700000.00,
        ]);
        $targetWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 800000.00,
        ]);

        $transfer = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'type' => 'transfer',
            'amount' => 300000.00,
            'date' => now()->toDateString(),
            'description' => 'Transfer awal',
        ]);

        // Change amount from 300,000 to 100,000
        $updatePayload = [
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'type' => 'transfer',
            'amount' => 100000.00,
            'date' => now()->toDateString(),
            'description' => 'Transfer dikurangi',
        ];

        $response = $this->actingAs($user)->put("/transactions/{$transfer->id}", $updatePayload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui!');

        $sourceWallet->refresh();
        $targetWallet->refresh();

        // Source: was 700k + revert 300k = 1M; apply -100k => 900,000
        $this->assertEquals(900000.00, (float) $sourceWallet->balance);
        // Target: was 800k - revert 300k = 500k; apply +100k => 600,000
        $this->assertEquals(600000.00, (float) $targetWallet->balance);
    }

    public function test_user_can_update_income_transaction_and_wallet_balance_adjusts_correctly(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000.00,
        ]);
        $category = Category::factory()->income()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->income()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 200000.00,
            'description' => 'Gaji paruh waktu',
        ]);

        $updatePayload = [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 350000.00,
            'date' => now()->toDateString(),
            'description' => 'Gaji bertambah',
        ];

        $response = $this->actingAs($user)->put("/transactions/{$transaction->id}", $updatePayload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui!');

        $wallet->refresh();
        // 500k - 200k (revert) + 350k (new) = 650k
        $this->assertEquals(650000.00, (float) $wallet->balance);
    }

    public function test_user_can_update_transaction_via_ajax_and_receive_json(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 50000.00,
        ]);

        $updatePayload = [
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 75000.00,
            'date' => now()->toDateString(),
            'description' => 'Makan siang enak',
        ];

        $response = $this->actingAs($user)->putJson("/transactions/{$transaction->id}", $updatePayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui!',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 75000.00,
        ]);
    }

    public function test_user_can_delete_transaction_via_ajax_and_receive_json(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 450000.00,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 50000.00,
        ]);

        $response = $this->actingAs($user)->deleteJson("/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus!',
        ]);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $wallet->refresh();
        $this->assertEquals(500000.00, (float) $wallet->balance);
    }

    public function test_user_cannot_update_or_delete_other_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $walletA = Wallet::factory()->create(['user_id' => $userA->id]);
        $catA = Category::factory()->expense()->create(['user_id' => $userA->id]);

        $transactionA = Transaction::factory()->expense()->create([
            'user_id' => $userA->id,
            'wallet_id' => $walletA->id,
            'category_id' => $catA->id,
            'amount' => 100000.00,
        ]);

        // User B tries to update user A's transaction
        $updateResponse = $this->actingAs($userB)->put("/transactions/{$transactionA->id}", [
            'wallet_id' => $walletA->id,
            'category_id' => $catA->id,
            'type' => 'expense',
            'amount' => 200000.00,
            'date' => now()->toDateString(),
        ]);
        $updateResponse->assertStatus(403);

        // User B tries to delete user A's transaction
        $deleteResponse = $this->actingAs($userB)->delete("/transactions/{$transactionA->id}");
        $deleteResponse->assertStatus(403);
    }
}
