<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_budgets(): void
    {
        $response = $this->get(route('budgets.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_budgets_page(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->expense()->create([
            'user_id' => $user->id,
            'name' => 'Makanan & Minuman',
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1500000,
            'month' => now()->format('Y-m'),
            'notes' => 'Anggaran jajan bulanan',
        ]);

        $response = $this->actingAs($user)->get(route('budgets.index'));

        $response->assertOk();
        $response->assertSee('Anggaran Bulanan');
        $response->assertSee('Makanan & Minuman');
        $response->assertSee('Rp 1.500.000');
        $response->assertSee('Anggaran jajan bulanan');
    }

    public function test_user_can_create_a_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('budgets.store'), [
            'category_id' => $category->id,
            'amount' => 2000000,
            'month' => now()->format('Y-m'),
            'notes' => 'Batas belanja bulanan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Anggaran bulanan berhasil disimpan!');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 2000000,
            'notes' => 'Batas belanja bulanan',
        ]);
    }

    public function test_user_can_update_a_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000000,
            'month' => now()->format('Y-m'),
            'notes' => 'Catatan lama',
        ]);

        $response = $this->actingAs($user)->put(route('budgets.update', $budget), [
            'amount' => 1750000,
            'notes' => 'Catatan baru diperbarui',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Anggaran berhasil diperbarui!');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'amount' => 1750000,
            'notes' => 'Catatan baru diperbarui',
        ]);
    }

    public function test_user_can_delete_a_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000000,
            'month' => now()->format('Y-m'),
        ]);

        $response = $this->actingAs($user)->delete(route('budgets.destroy', $budget));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Anggaran berhasil dihapus!');

        $this->assertDatabaseMissing('budgets', [
            'id' => $budget->id,
        ]);
    }

    public function test_user_cannot_update_another_users_budget(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $category = Category::factory()->expense()->create(['user_id' => $userA->id]);

        $budget = Budget::create([
            'user_id' => $userA->id,
            'category_id' => $category->id,
            'amount' => 1000000,
            'month' => now()->format('Y-m'),
        ]);

        $response = $this->actingAs($userB)->put(route('budgets.update', $budget), [
            'amount' => 2000000,
        ]);

        $response->assertForbidden();
    }

    public function test_budget_spending_and_percentage_calculation(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id, 'name' => 'Transportasi']);

        // Set budget Rp 500.000 for this month
        $currentMonth = now()->format('Y-m');
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 500000,
            'month' => $currentMonth,
        ]);

        // Create transaction of Rp 250.000 (50%)
        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 250000,
            'date' => now()->startOfMonth()->addDays(2),
        ]);

        $response = $this->actingAs($user)->get(route('budgets.index', ['month' => $currentMonth]));

        $response->assertOk();
        $response->assertSee('50%');
        $response->assertSee('Rp 250.000');
        $response->assertSee('Sisa Rp 250.000');
    }

    public function test_dashboard_displays_real_category_budgets(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id, 'name' => 'Kopi & Snack']);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 300000,
            'month' => now()->format('Y-m'),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 150000,
            'date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Anggaran Pengeluaran');
        $response->assertSee('Kopi &amp; Snack', false);
        $response->assertSee('50%');
        $response->assertSee(route('budgets.index'));
    }
}
