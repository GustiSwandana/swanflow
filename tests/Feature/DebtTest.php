<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_debts_page(): void
    {
        $user = User::factory()->create();
        Debt::factory()->create([
            'user_id' => $user->id,
            'person_name' => 'Budi Santoso',
            'type' => 'receivable',
            'amount' => 500000,
        ]);

        $response = $this->actingAs($user)->get('/debts');

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('Kelola pinjaman');
    }

    public function test_user_can_filter_debts_by_type(): void
    {
        $user = User::factory()->create();
        Debt::factory()->create([
            'user_id' => $user->id,
            'person_name' => 'Si Peminjam',
            'type' => 'receivable',
        ]);
        Debt::factory()->create([
            'user_id' => $user->id,
            'person_name' => 'Tempat Berutang',
            'type' => 'debt',
        ]);

        $response = $this->actingAs($user)->get('/debts?type=receivable');
        $response->assertOk();
        $response->assertSee('Si Peminjam');
        $response->assertDontSee('Tempat Berutang');
    }

    public function test_user_can_create_a_debt_record_and_wallet_balance_increases(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000,
        ]);

        $response = $this->actingAs($user)->post('/debts', [
            'type' => 'debt',
            'person_name' => 'Ahmad',
            'amount' => 1000000,
            'due_date' => now()->addDays(30)->toDateString(),
            'wallet_id' => $wallet->id,
            'notes' => 'Pinjam untuk servis motor',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('debts', [
            'user_id' => $user->id,
            'type' => 'debt',
            'person_name' => 'Ahmad',
            'amount' => 1000000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'wallet_id' => $wallet->id,
        ]);

        // Wallet balance must increase because user borrows money
        $wallet->refresh();
        $this->assertEquals(1500000, (float) $wallet->balance);

        // An income transaction must be recorded
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Penerimaan Pinjaman dari Ahmad',
        ]);
    }

    public function test_user_can_create_a_receivable_record_and_wallet_balance_decreases(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000,
        ]);

        $response = $this->actingAs($user)->post('/debts', [
            'type' => 'receivable',
            'person_name' => 'Doni',
            'amount' => 350000,
            'wallet_id' => $wallet->id,
            'notes' => 'Pinjam talangan tiket',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('debts', [
            'user_id' => $user->id,
            'type' => 'receivable',
            'person_name' => 'Doni',
            'amount' => 350000,
            'status' => 'unpaid',
            'wallet_id' => $wallet->id,
        ]);

        // Wallet balance must decrease because user lends money out
        $wallet->refresh();
        $this->assertEquals(650000, (float) $wallet->balance);

        // An expense transaction must be recorded
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 350000,
            'description' => 'Memberikan Pinjaman ke Doni',
        ]);
    }

    public function test_debt_creation_validation_fails(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/debts', [
            'type' => 'invalid_type',
            'person_name' => '',
            'amount' => -100,
            'wallet_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['type', 'person_name', 'amount', 'wallet_id']);
    }

    public function test_user_can_update_their_own_debt(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000,
        ]);

        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'type' => 'receivable',
            'wallet_id' => $wallet->id,
            'person_name' => 'Rian',
            'amount' => 500000,
            'paid_amount' => 0,
        ]);

        $response = $this->actingAs($user)->put("/debts/{$debt->id}", [
            'person_name' => 'Rian Updated',
            'amount' => 750000,
            'wallet_id' => $wallet->id,
            'notes' => 'Tambahan nominal',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $debt->refresh();
        $this->assertEquals('Rian Updated', $debt->person_name);
        $this->assertEquals(750000, (float) $debt->amount);

        // Wallet balance should adjust for additional 250,000 lent out
        $wallet->refresh();
        $this->assertEquals(750000, (float) $wallet->balance);
    }

    public function test_user_cannot_update_another_users_debt(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $wallet = Wallet::factory()->create(['user_id' => $user1->id]);
        $debt = Debt::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->put("/debts/{$debt->id}", [
            'person_name' => 'Hacked Debt',
            'amount' => 999999,
            'wallet_id' => $wallet->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_record_debt_repayment_and_wallet_decreases(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 2000000,
        ]);

        // User owes Rp 1.000.000 to Joko
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'type' => 'debt',
            'person_name' => 'Joko',
            'amount' => 1000000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'wallet_id' => $wallet->id,
        ]);

        // User repays Rp 400.000
        $response = $this->actingAs($user)->post("/debts/{$debt->id}/repay", [
            'payment_amount' => 400000,
            'wallet_id' => $wallet->id,
            'notes' => 'Cicilan pertama',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $debt->refresh();
        $this->assertEquals(400000, (float) $debt->paid_amount);
        $this->assertEquals('partially_paid', $debt->status);
        $this->assertEquals(600000, $debt->remaining_amount);

        // Wallet balance must decrease by 400.000
        $wallet->refresh();
        $this->assertEquals(1600000, (float) $wallet->balance);

        // Expense transaction created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 400000,
            'description' => 'Cicilan pertama',
        ]);
    }

    public function test_user_can_record_receivable_repayment_and_wallet_increases(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500000,
        ]);

        // Hendra owes Rp 300.000 to User
        $receivable = Debt::factory()->create([
            'user_id' => $user->id,
            'type' => 'receivable',
            'person_name' => 'Hendra',
            'amount' => 300000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'wallet_id' => $wallet->id,
        ]);

        // Hendra pays full Rp 300.000
        $response = $this->actingAs($user)->post("/debts/{$receivable->id}/repay", [
            'payment_amount' => 300000,
            'wallet_id' => $wallet->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $receivable->refresh();
        $this->assertEquals(300000, (float) $receivable->paid_amount);
        $this->assertEquals('paid', $receivable->status);
        $this->assertEquals(0, $receivable->remaining_amount);

        // Wallet balance must increase by 300.000
        $wallet->refresh();
        $this->assertEquals(800000, (float) $wallet->balance);

        // Income transaction created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 300000,
            'description' => 'Penerimaan Piutang dari Hendra',
        ]);
    }

    public function test_repayment_cannot_exceed_remaining_amount(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'amount' => 500000,
            'paid_amount' => 0,
        ]);

        $response = $this->actingAs($user)->post("/debts/{$debt->id}/repay", [
            'payment_amount' => 600000, // exceeds 500.000
            'wallet_id' => $wallet->id,
        ]);

        $response->assertSessionHasErrors(['payment_amount']);
    }

    public function test_user_cannot_repay_another_users_debt(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user1->id]);
        $debt = Debt::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->post("/debts/{$debt->id}/repay", [
            'payment_amount' => 50000,
            'wallet_id' => $wallet->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_own_debt_and_wallet_is_restored(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000,
        ]);

        // Receivable: user lent 300,000, paid_amount 0 -> remaining 300,000
        $receivable = Debt::factory()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'receivable',
            'amount' => 300000,
            'paid_amount' => 0,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($user)->delete("/debts/{$receivable->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('debts', ['id' => $receivable->id]);

        // Deleting unpaid receivable refunds the 300,000 back to wallet
        $wallet->refresh();
        $this->assertEquals(1300000, (float) $wallet->balance);
    }

    public function test_user_can_delete_their_own_borrowed_debt_and_wallet_is_deducted(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000,
        ]);

        // Debt: user borrowed 400,000, paid_amount 0 -> remaining 400,000
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'debt',
            'amount' => 400000,
            'paid_amount' => 0,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($user)->delete("/debts/{$debt->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('debts', ['id' => $debt->id]);

        // Deleting unpaid debt deducts the 400,000 from wallet
        $wallet->refresh();
        $this->assertEquals(600000, (float) $wallet->balance);
    }

    public function test_user_cannot_delete_another_users_debt(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $debt = Debt::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->delete("/debts/{$debt->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('debts', ['id' => $debt->id]);
    }
}
