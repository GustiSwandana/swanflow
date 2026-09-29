<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        // Retrieve budgets for the user:
        // Either custom date range active for the selected month, or matching month, or recurring defaults (month is null and start_date is null)
        $budgets = Budget::where('user_id', $user->id)
            ->where(function ($q) use ($selectedMonth, $startOfMonth, $endOfMonth) {
                $q->where('month', $selectedMonth)
                    ->orWhere(function ($sub) use ($startOfMonth, $endOfMonth) {
                        $sub->whereNotNull('start_date')
                            ->whereNotNull('end_date')
                            ->where('start_date', '<=', $endOfMonth)
                            ->where('end_date', '>=', $startOfMonth);
                    })
                    ->orWhere(function ($sub) {
                        $sub->whereNull('month')
                            ->whereNull('start_date');
                    });
            })
            ->with('category')
            ->get();

        // Calculate actual expense spending per category based on each budget's specific timeframe
        $budgets = $budgets->map(function ($b) use ($user, $startOfMonth, $endOfMonth) {
            $rangeStart = $b->start_date ? $b->start_date->toDateString() : $startOfMonth;
            $rangeEnd = $b->end_date ? $b->end_date->toDateString() : $endOfMonth;

            $spent = (float) Transaction::where('user_id', $user->id)
                ->where('type', TransactionType::Expense)
                ->where('category_id', $b->category_id)
                ->whereBetween('date', [$rangeStart, $rangeEnd])
                ->sum('amount');

            $b->spent = $spent;
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
        if ($request->has('amount')) {
            $request->merge(['amount' => $this->cleanNumericInput($request->input('amount'))]);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'period_type' => 'nullable|in:monthly,custom_range,recurring',
            'month' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:255',
        ]);

        $periodType = $validated['period_type'] ?? 'monthly';
        $month = null;
        $startDate = null;
        $endDate = null;

        if ($periodType === 'custom_range') {
            $startDate = $validated['start_date'] ?? null;
            $endDate = $validated['end_date'] ?? null;
            if ($startDate && ! $endDate) {
                $endDate = Carbon::parse($startDate)->addDays(30)->toDateString();
            }
        } elseif ($periodType === 'monthly') {
            $month = ! empty($validated['month']) ? $validated['month'] : now()->format('Y-m');
        } // recurring keeps all null

        Budget::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'month' => $month,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Anggaran bulanan berhasil disimpan!');
    }

    /**
     * Update the specified budget.
     */
    public function update(Request $request, Budget $budget): RedirectResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403);

        if ($request->has('amount')) {
            $request->merge(['amount' => $this->cleanNumericInput($request->input('amount'))]);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:255',
        ]);

        $budget->update([
            'amount' => $validated['amount'],
            'start_date' => ! empty($validated['start_date']) ? $validated['start_date'] : null,
            'end_date' => ! empty($validated['end_date']) ? $validated['end_date'] : null,
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
