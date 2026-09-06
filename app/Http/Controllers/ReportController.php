<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display financial reports and category breakdown.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $selectedMonth = $request->query('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedMonth);

        $selectedType = $request->query('type', 'expense');
        if (! in_array($selectedType, ['expense', 'income'])) {
            $selectedType = 'expense';
        }

        $totals = Transaction::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
            ")
            ->first();

        $income = (float) ($totals->total_income ?? 0);
        $expense = (float) ($totals->total_expense ?? 0);
        $netCashflow = $income - $expense;

        $targetTotal = $selectedType === 'income' ? $income : $expense;
        $targetEnum = $selectedType === 'income' ? TransactionType::Income : TransactionType::Expense;

        // Default palette colors (fintech dark theme with emerald, rose, amber, teal, violet, cyan)
        $palette = ['#10b981', '#f43f5e', '#f59e0b', '#06b6d4', '#8b5cf6', '#14b8a6', '#ec4899'];

        // Breakdown by category for selected type
        $categoryBreakdown = Transaction::where('transactions.user_id', $user->id)
            ->where('transactions.type', $targetEnum)
            ->whereYear('transactions.date', $year)
            ->whereMonth('transactions.date', $month)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                'categories.icon',
                'categories.color',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.icon', 'categories.color')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($item, $index) use ($targetTotal, $palette) {
                $item->percentage = $targetTotal > 0 ? round(($item->total_amount / $targetTotal) * 100, 1) : 0;
                $item->chartColor = $item->color ?: ($palette[$index % count($palette)]);

                return $item;
            });

        // Transactions list for the selected month and type
        $monthTransactions = Transaction::with(['wallet', 'category'])
            ->where('user_id', $user->id)
            ->where('type', $targetEnum)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->latest('date')
            ->latest('id')
            ->take(8)
            ->get();

        return view('reports.index', [
            'user' => $user,
            'selectedMonth' => $selectedMonth,
            'selectedType' => $selectedType,
            'income' => $income,
            'expense' => $expense,
            'targetTotal' => $targetTotal,
            'netCashflow' => $netCashflow,
            'categoryBreakdown' => $categoryBreakdown,
            'monthTransactions' => $monthTransactions,
        ]);
    }
}
