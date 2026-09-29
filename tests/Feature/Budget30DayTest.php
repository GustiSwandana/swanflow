<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class Budget30DayTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_budget_with_30_day_period(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => TransactionType::Expense]);

        $startDate = '2026-09-28';
        $endDate = '2026-10-28';

        $response = $this->actingAs($user)->post('/budgets', [
            'category_id' => $category->id,
            'amount' => 1500000,
            'period_type' => 'custom_range',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => 'Anggaran 30 hari',
        ]);

        $response->assertSessionHas('success');

        $budget = Budget::where('user_id', $user->id)->first();
        $this->assertNotNull($budget);
        $this->assertEquals(1500000, (float) $budget->amount);
        $this->assertEquals('2026-09-28', $budget->start_date->format('Y-m-d'));
        $this->assertEquals('2026-10-28', $budget->end_date->format('Y-m-d'));
    }

    public function test_30_day_budget_calculates_expenses_strictly_within_date_range(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id, 'type' => TransactionType::Expense]);
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);

        $startDate = '2026-09-28';
        $endDate = '2026-10-28';

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 2000000,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // Transaction before range (e.g. 2026-09-27)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 500000,
            'type' => 'expense',
            'date' => '2026-09-27',
        ]);

        // Transaction inside range (e.g. 2026-10-05)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 750000,
            'type' => 'expense',
            'date' => '2026-10-05',
        ]);

        // Transaction after range (e.g. 2026-10-29)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'amount' => 400000,
            'type' => 'expense',
            'date' => '2026-10-29',
        ]);

        // View index for October (month=2026-10)
        $response = $this->actingAs($user)->get('/budgets?month=2026-10');
        $response->assertStatus(200);

        /** @var Collection $budgets */
        $budgets = $response->viewData('budgets');
        $targetBudget = $budgets->firstWhere('category_id', $category->id);

        $this->assertNotNull($targetBudget);
        // Only the 750,000 inside the 30-day range should be counted
        $this->assertEquals(750000, $targetBudget->spent);
        $this->assertEquals(1250000, $targetBudget->remaining);
    }
}
