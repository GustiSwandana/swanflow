<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetController extends Controller
{
    /**
     * Display a listing of monthly budgets and spending progress.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $selectedMonth = (string) $request->query('month', now()->format('Y-m'));

        try {
            $parsedDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception) {
            $selectedMonth = now()->format('Y-m');
            $parsedDate = now();
        }

        $startOfMonth = $parsedDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->toDateString();

        // Retrieve budgets for the specified month or recurring defaults (month is null)
        $budgets = Budget::where('user_id', $user->id)
            ->where(function ($q) use ($selectedMonth) {
                $q->where('month', $selectedMonth)
                    ->orWhereNull('month');
            })
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($group) use ($selectedMonth) {
                return $group->firstWhere('month', $selectedMonth) ?: $group->first();
            })
            ->values();

        // Calculate actual expense spending per category for this month
        $expensesByCategory = Transaction::where('user_id', $user->id)
            ->where('type', TransactionType::Expense)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total_spent'))
            ->groupBy('category_id')
            ->pluck('total_spent', 'category_id');

        $budgets = $budgets->map(function ($b) use ($expensesByCategory) {
            $b->spent = (float) ($expensesByCategory[$b->category_id] ?? 0);
            $b->remaining = max(0, (float) $b->amount - $b->spent);
            $b->is_over = $b->spent > (float) $b->amount;
            $b->over_amount = max(0, $b->spent - (float) $b->amount);
            $b->percentage = (float) $b->amount > 0 ? (int) round(($b->spent / (float) $b->amount) * 100) : 0;

            return $b;
        })->sortByDesc('percentage')->values();

        // Overall summary statistics
        $totalBudget = (float) $budgets->sum('amount');
        $totalSpent = (float) $budgets->sum('spent');
        $totalRemaining = max(0, $totalBudget - $totalSpent);
        $overallPercentage = $totalBudget > 0 ? (int) round(($totalSpent / $totalBudget) * 100) : 0;
        $overBudgetCount = $budgets->where('is_over', true)->count();

        // Available expense categories for adding a new budget
        $availableCategories = Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })
            ->where('type', TransactionType::Expense)
            ->orderBy('name')
            ->get();

        return view('budgets.index', [
            'selectedMonth' => $selectedMonth,
            'budgets' => $budgets,
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'totalRemaining' => $totalRemaining,
            'overallPercentage' => $overallPercentage,
            'overBudgetCount' => $overBudgetCount,
            'availableCategories' => $availableCategories,
        ]);
    }

    /**
     * Store a newly created or updated budget.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'month' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'notes' => 'nullable|string|max:255',
        ]);

        Budget::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'month' => ! empty($validated['month']) ? $validated['month'] : null,
            ],
            [
                'amount' => $validated['amount'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Anggaran bulanan berhasil disimpan!');
    }

    /**
     * Update the specified budget.
     */
    public function update(Request $request, Budget $budget): RedirectResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $budget->update([
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Anggaran berhasil diperbarui!');
    }

    /**
     * Remove the specified budget.
     */
    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403);

        $budget->delete();

        return redirect()->back()->with('success', 'Anggaran berhasil dihapus!');
    }
}
