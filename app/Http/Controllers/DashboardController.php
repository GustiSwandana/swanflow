<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Todo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the mobile-first dashboard with dynamic financial metrics.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $wallets = Wallet::where('user_id', $user->id)->get();
        $totalBalance = (float) $wallets->sum('balance');

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

        $cashflowTotals = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$lastMonthStart, $endOfMonth])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' AND date >= ? AND date <= ? THEN amount ELSE 0 END), 0) as this_month_income,
                COALESCE(SUM(CASE WHEN type = 'expense' AND date >= ? AND date <= ? THEN amount ELSE 0 END), 0) as this_month_expense,
                COALESCE(SUM(CASE WHEN type = 'income' AND date >= ? AND date <= ? THEN amount ELSE 0 END), 0) as last_month_income
            ", [$startOfMonth, $endOfMonth, $startOfMonth, $endOfMonth, $lastMonthStart, $lastMonthEnd])
            ->first();

        $thisMonthIncome = (float) ($cashflowTotals->this_month_income ?? 0);
        $thisMonthExpense = (float) ($cashflowTotals->this_month_expense ?? 0);
        $lastMonthIncome = (float) ($cashflowTotals->last_month_income ?? 0);

        $growthPercentage = $lastMonthIncome > 0
            ? round((($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 1)
            : 0;

        $recentTransactions = Transaction::with(['wallet', 'targetWallet', 'category'])
            ->where('user_id', $user->id)
            ->latest('date')
            ->latest('id')
            ->take(6)
            ->get();

        $categories = Category::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })->get();

        $upcomingSubscriptions = $user->subscriptions()
            ->with(['wallet', 'category'])
            ->where('status', 'active')
            ->orderBy('next_due_date', 'asc')
            ->take(3)
            ->get();

        $activeDebts = $user->debts()->unpaid()->get();
        $debtsSummary = [
            'receivables' => $activeDebts->where('type', 'receivable')->sum(fn ($d) => $d->remaining_amount),
            'debts' => $activeDebts->where('type', 'debt')->sum(fn ($d) => $d->remaining_amount),
            'unpaidCount' => $activeDebts->count(),
        ];

        $currentMonth = now()->format('Y-m');

        // Anggaran Pengeluaran per Kategori (Dynamic Monthly Budgets)
        $configuredBudgets = Budget::where('user_id', $user->id)
            ->where(function ($q) use ($currentMonth) {
                $q->where('month', $currentMonth)
                    ->orWhereNull('month');
            })
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($group) => $group->firstWhere('month', $currentMonth) ?: $group->first())
            ->values();

        $expensesByCategory = Transaction::where('user_id', $user->id)
            ->where('type', TransactionType::Expense)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total_spent'))
            ->groupBy('category_id')
            ->pluck('total_spent', 'category_id');

        if ($configuredBudgets->isNotEmpty()) {
            $categoryBudgets = $configuredBudgets->map(function ($b) use ($expensesByCategory) {
                $spent = (float) ($expensesByCategory[$b->category_id] ?? 0);
                $amount = (float) $b->amount;
                $percent = $amount > 0 ? (int) round(($spent / $amount) * 100) : 0;

                return (object) [
                    'id' => $b->id,
                    'category_id' => $b->category_id,
                    'name' => $b->category->name ?? 'Kategori',
                    'color' => $b->category->color ?? '#10B981',
                    'spent' => $spent,
                    'amount' => $amount,
                    'remaining' => max(0, $amount - $spent),
                    'percent' => $percent,
                    'is_over' => $spent > $amount,
                    'is_configured' => true,
                ];
            })->sortByDesc('percent')->values();
        } else {
            // Fallback if no custom budget set yet: show actual category expenses
            $categoryBudgets = Transaction::where('transactions.user_id', $user->id)
                ->where('transactions.type', TransactionType::Expense)
                ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth])
                ->whereNotNull('transactions.category_id')
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->select(
                    'categories.id',
                    'categories.name',
                    'categories.icon',
                    'categories.color',
                    DB::raw('SUM(transactions.amount) as spent')
                )
                ->groupBy('categories.id', 'categories.name', 'categories.icon', 'categories.color')
                ->orderByDesc('spent')
                ->take(4)
                ->get()
                ->map(function ($cat) use ($thisMonthExpense) {
                    $spent = (float) $cat->spent;
                    $cat->percent = $thisMonthExpense > 0 ? min(100, (int) round(($spent / $thisMonthExpense) * 100)) : 0;
                    $cat->amount = 0;
                    $cat->is_over = false;
                    $cat->is_configured = false;

                    return $cat;
                });

            if ($categoryBudgets->isEmpty()) {
                $categoryBudgets = $categories->where('type', TransactionType::Expense)
                    ->take(3)
                    ->map(function ($cat) {
                        $cat->spent = 0;
                        $cat->amount = 0;
                        $cat->percent = 0;
                        $cat->is_over = false;
                        $cat->is_configured = false;

                        return $cat;
                    });
            }
        }

        $todayTodos = Todo::where('user_id', $user->id)
            ->whereDate('due_date', today())
            ->orderByRaw("is_completed ASC, CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->take(4)
            ->get();

        $pendingTodosCount = Todo::where('user_id', $user->id)
            ->where('is_completed', false)
            ->count();

        $storedFilesCount = $user->storedFiles()->count();
        $activeDropLinksCount = $user->uploadLinks()
            ->get()
            ->filter(fn ($link) => $link->canAcceptUpload())
            ->count();
        $activePublicSharesCount = $user->storedFiles()
            ->where('is_public', true)
            ->count();
        $activeLinksCount = $activeDropLinksCount + $activePublicSharesCount;

        return view('dashboard', [
            'user' => $user,
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'growthPercentage' => $growthPercentage,
            'recentTransactions' => $recentTransactions,
            'categories' => $categories,
            'upcomingSubscriptions' => $upcomingSubscriptions,
            'debtsSummary' => $debtsSummary,
            'categoryBudgets' => $categoryBudgets,
            'todayTodos' => $todayTodos,
            'pendingTodosCount' => $pendingTodosCount,
            'storedFilesCount' => $storedFilesCount,
            'activeLinksCount' => $activeLinksCount,
        ]);
    }
}
