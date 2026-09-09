<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Investment;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_investments_page(): void
    {
        $user = User::factory()->create();
        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bibit Reksadana Obligasi',
            'platform' => 'Bibit',
            'type' => 'mutual_fund',
            'initial_amount' => 5000000,
            'current_value' => 5500000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertSee('Bibit Reksadana Obligasi');
        $response->assertSee('5.500.000');
        $response->assertSee('Portofolio');
    }

    public function test_user_can_create_investment_with_wallet_deduction(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000000,
        ]);

        Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Investasi & Dividen',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->post('/investments', [
            'name' => 'Saham BBCA',
            'platform' => 'Stockbit',
            'type' => 'stock',
            'initial_amount' => 3000000,
            'current_value' => 3000000,
            'target_amount' => 6000000,
            'wallet_id' => $wallet->id,
            'deduct_wallet' => '1',
            'date' => now()->toDateString(),
            'notes' => 'Beli saham perbankan',
        ]);

        $response->assertRedirect('/investments');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'name' => 'Saham BBCA',
            'platform' => 'Stockbit',
            'type' => 'stock',
            'initial_amount' => 3000000,
            'current_value' => 3000000,
            'status' => 'active',
        ]);

        // Assert wallet balance was deducted by 3,000,000
        $this->assertEquals(7000000, $wallet->fresh()->balance);

        // Assert transaction log was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 3000000,
            'type' => 'expense',
        ]);

        // Assert investment transaction log was created
        $this->assertDatabaseHas('investment_transactions', [
            'type' => 'topup',
            'amount' => 3000000,
            'affects_wallet' => 1,
        ]);
    }

    public function test_user_can_create_investment_without_wallet_deduction(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000000,
        ]);

        $response = $this->actingAs($user)->post('/investments', [
            'name' => 'Emas Batangan Antam',
            'platform' => 'Fisik',
            'type' => 'gold',
            'initial_amount' => 1200000,
            'current_value' => 1350000,
            'wallet_id' => $wallet->id,
            'deduct_wallet' => '0',
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/investments');

        // Wallet should remain unchanged
        $this->assertEquals(5000000, $wallet->fresh()->balance);

        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'name' => 'Emas Batangan Antam',
            'initial_amount' => 1200000,
            'current_value' => 1350000,
        ]);
    }

    public function test_user_can_topup_investment_and_deduct_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000000,
        ]);

        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'name' => 'Bibit Reksadana',
            'initial_amount' => 1000000,
            'current_value' => 1000000,
        ]);

        $response = $this->actingAs($user)->post("/investments/{$investment->id}/topup", [
            'amount' => 500000,
            'date' => now()->toDateString(),
            'wallet_id' => $wallet->id,
            'deduct_wallet' => '1',
            'update_current_value' => '1',
            'notes' => 'Top up tambahan',
        ]);

        $response->assertRedirect('/investments');

        // Wallet deducted by 500,000
        $this->assertEquals(4500000, $wallet->fresh()->balance);

        // Investment current value incremented to 1,500,000
        $this->assertEquals(1500000, $investment->fresh()->current_value);

        // Investment transaction log recorded
        $this->assertDatabaseHas('investment_transactions', [
            'investment_id' => $investment->id,
            'type' => 'topup',
            'amount' => 500000,
            'affects_wallet' => 1,
        ]);
    }

    public function test_user_can_withdraw_investment_and_credit_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000,
        ]);

        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'name' => 'Kripto Bitcoin',
            'initial_amount' => 2000000,
            'current_value' => 2500000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post("/investments/{$investment->id}/withdraw", [
            'amount' => 1000000,
            'date' => now()->toDateString(),
            'wallet_id' => $wallet->id,
            'add_to_wallet' => '1',
            'close_investment' => '0',
            'notes' => 'Take profit sebagian',
        ]);

        $response->assertRedirect('/investments');

        // Wallet credited by 1,000,000 -> balance becomes 2,000,000
        $this->assertEquals(2000000, $wallet->fresh()->balance);

        // Investment current value decreased from 2,500,000 to 1,500,000
        $this->assertEquals(1500000, $investment->fresh()->current_value);

        // Transaction log created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => 1000000,
            'type' => 'income',
        ]);
    }

    public function test_user_can_update_current_market_value(): void
    {
        $user = User::factory()->create();
        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'initial_amount' => 1000000,
            'current_value' => 1000000,
        ]);

        $response = $this->actingAs($user)->patch("/investments/{$investment->id}/value", [
            'current_value' => 1250000,
        ]);

        $response->assertRedirect('/investments');
        $this->assertEquals(1250000, $investment->fresh()->current_value);
        $this->assertEquals(250000, $investment->fresh()->profit_loss);
        $this->assertEquals(25.0, $investment->fresh()->roi_percentage);
    }

    public function test_user_cannot_modify_other_users_investment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $investment = Investment::factory()->create([
            'user_id' => $user1->id,
            'current_value' => 1000000,
        ]);

        $response = $this->actingAs($user2)->patch("/investments/{$investment->id}/value", [
            'current_value' => 9999999,
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_investment(): void
    {
        $user = User::factory()->create();
        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'name' => 'Aset Yang Dihapus',
        ]);

        $response = $this->actingAs($user)->delete("/investments/{$investment->id}");

        $response->assertRedirect('/investments');
        $this->assertDatabaseMissing('investments', [
            'id' => $investment->id,
        ]);
    }

    public function test_initial_amount_is_not_double_counted_in_total_invested(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/investments', [
            'name' => 'Reksa Dana Pasar Uang',
            'type' => 'mutual_fund',
            'initial_amount' => 150000,
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/investments');

        $investment = Investment::where('name', 'Reksa Dana Pasar Uang')->firstOrFail();
        $this->assertEquals(150000, $investment->initial_amount);
        $this->assertEquals(150000, $investment->current_value);
        $this->assertEquals(150000, $investment->total_invested);
        $this->assertEquals(0, $investment->profit_loss);
        $this->assertEquals(0.0, $investment->roi_percentage);
    }
}
