<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_renders_successfully_for_authenticated_user(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan');
        $response->assertSee('Pemasukan');
        $response->assertSee('Pengeluaran');
        $response->assertSee('Total Masuk');
        $response->assertSee('Total Keluar');
    }

    public function test_reports_displays_correct_expense_breakdown_and_donut_data(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);

        $catFood = Category::factory()->expense()->create([
            'user_id' => $user->id,
            'name' => 'Makanan & Minuman',
            'color' => '#f43f5e',
        ]);
        $catTransport = Category::factory()->expense()->create([
            'user_id' => $user->id,
            'name' => 'Transportasi',
            'color' => '#10b981',
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $catFood->id,
            'amount' => 300000,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $catTransport->id,
            'amount' => 100000,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', ['type' => 'expense']));

        $response->assertStatus(200);
        $response->assertSee('Makanan & Minuman');
        $response->assertSee('Transportasi');
        $response->assertSee('75%');
        $response->assertSee('25%');
        $response->assertSee('Rp 400.000');
    }

    public function test_reports_displays_income_breakdown_when_toggled(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);

        $catSalary = Category::factory()->income()->create([
            'user_id' => $user->id,
            'name' => 'Gaji Bulanan',
            'color' => '#10b981',
        ]);

        Transaction::factory()->income()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $catSalary->id,
            'amount' => 10000000,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', ['type' => 'income']));

        $response->assertStatus(200);
        $response->assertSee('Gaji Bulanan');
        $response->assertSee('100%');
        $response->assertSee('Rp 10.000.000');
    }

    public function test_reports_filters_by_selected_month(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000000]);

        $cat = Category::factory()->expense()->create([
            'user_id' => $user->id,
            'name' => 'Belanja Online',
        ]);

        // Transaction in last month
        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $cat->id,
            'amount' => 250000,
            'date' => Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d'),
        ]);

        $lastMonth = Carbon::now()->subMonths(1)->format('Y-m');

        $response = $this->actingAs($user)->get(route('reports.index', ['month' => $lastMonth, 'type' => 'expense']));

        $response->assertStatus(200);
        $response->assertSee('Belanja Online');
        $response->assertSee('Rp 250.000');
    }
}
