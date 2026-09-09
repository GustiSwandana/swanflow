<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    /**
     * Display a listing of investments.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $query = $user->investments()->with(['wallet', 'transactions']);

        // Filter by status (active/closed/all)
        $status = $request->query('status', 'active');
        if ($status !== 'all' && in_array($status, ['active', 'closed'])) {
            $query->where('status', $status);
        }

        // Filter by type
        $type = $request->query('type', 'all');
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $investments = $query->orderBy('status', 'asc')
            ->orderBy('current_value', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Calculate summary statistics for active investments
        $allActive = $user->investments()->where('status', 'active')->with('transactions')->get();
        $totalPortfolioValue = (float) $allActive->sum('current_value');
        $totalInvested = (float) $allActive->sum(fn ($inv) => $inv->total_invested);
        $totalDeposited = (float) $allActive->sum(fn ($inv) => $inv->total_deposited);
        $totalWithdrawn = (float) $allActive->sum(fn ($inv) => $inv->total_withdrawn);
        $totalProfitLoss = $totalPortfolioValue - $totalInvested;
        $totalRoiPercentage = $totalInvested > 0 ? round(($totalProfitLoss / $totalInvested) * 100, 2) : 0.0;

        $wallets = $user->wallets()->orderBy('name')->get();

        return view('investments.index', [
            'user' => $user,
            'investments' => $investments,
            'wallets' => $wallets,
            'totalPortfolioValue' => $totalPortfolioValue,
            'totalInvested' => $totalInvested,
            'totalDeposited' => $totalDeposited,
            'totalWithdrawn' => $totalWithdrawn,
            'totalProfitLoss' => $totalProfitLoss,
            'totalRoiPercentage' => $totalRoiPercentage,
            'activeCount' => $allActive->count(),
            'currentType' => $type,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Store a newly created investment.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'platform' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:mutual_fund,stock,crypto,gold,deposit,bond,p2p,other'],
            'initial_amount' => ['required', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'target_amount' => ['nullable', 'numeric', 'min:0'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'deduct_wallet' => ['nullable', 'boolean'],
            'date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama instrumen investasi wajib diisi.',
            'type.required' => 'Jenis instrumen investasi wajib dipilih.',
            'initial_amount.required' => 'Modal awal / pembelian wajib diisi.',
            'initial_amount.min' => 'Modal awal tidak boleh bernilai negatif.',
        ]);

        $initialAmount = (float) $validated['initial_amount'];
        $currentValue = $request->filled('current_value') && (float) $validated['current_value'] > 0
            ? (float) $validated['current_value']
            : $initialAmount;
        $date = $validated['date'] ?? now()->toDateString();
        $deductWallet = $request->boolean('deduct_wallet');

        DB::transaction(function () use ($user, $validated, $initialAmount, $currentValue, $date, $deductWallet) {
            $investment = $user->investments()->create([
                'wallet_id' => $validated['wallet_id'] ?? null,
                'name' => $validated['name'],
                'platform' => $validated['platform'] ?? null,
                'type' => $validated['type'],
                'initial_amount' => $initialAmount,
                'current_value' => $currentValue,
                'target_amount' => $validated['target_amount'] ?? null,
                'status' => 'active',
                'notes' => $validated['notes'] ?? null,
            ]);

            $affectsWallet = false;

            // Optional: deduct from selected wallet & record transaction
            if ($deductWallet && ! empty($validated['wallet_id']) && $initialAmount > 0) {
                $wallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);
                $wallet->decrement('balance', $initialAmount);

                $category = Category::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhereNull('user_id');
                })->where('name', 'like', '%Investasi%')->first();

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'category_id' => $category?->id,
                    'amount' => $initialAmount,
                    'type' => TransactionType::Expense,
                    'date' => $date,
                    'description' => "Investasi Awal: {$investment->name}".($investment->platform ? " ({$investment->platform})" : ''),
                ]);

                $affectsWallet = true;
            }

            // Record initial transaction log if initialAmount > 0
            if ($initialAmount > 0) {
                $investment->transactions()->create([
                    'wallet_id' => $validated['wallet_id'] ?? null,
                    'type' => 'topup',
                    'amount' => $initialAmount,
                    'date' => $date,
                    'notes' => 'Setoran modal awal',
                    'affects_wallet' => $affectsWallet,
                ]);
            }
        });

        return redirect()->route('investments.index')->with('success', 'Aset investasi baru berhasil ditambahkan!');
    }

    /**
     * Top-up / add capital to existing investment.
     */
    public function topup(Request $request, Investment $investment): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();
        abort_unless($investment->user_id === $user->id, 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'date' => ['required', 'date'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'deduct_wallet' => ['nullable', 'boolean'],
            'update_current_value' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.required' => 'Nominal top up wajib diisi.',
            'amount.min' => 'Nominal top up minimal Rp 1.',
            'date.required' => 'Tanggal transaksi wajib diisi.',
        ]);

        $amount = (float) $validated['amount'];
        $deductWallet = $request->boolean('deduct_wallet');
        $updateValue = $request->boolean('update_current_value', true);

        DB::transaction(function () use ($user, $investment, $validated, $amount, $deductWallet, $updateValue) {
            $affectsWallet = false;

            if ($deductWallet && ! empty($validated['wallet_id'])) {
                $wallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);
                $wallet->decrement('balance', $amount);

                $category = Category::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhereNull('user_id');
                })->where('name', 'like', '%Investasi%')->first();

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'category_id' => $category?->id,
                    'amount' => $amount,
                    'type' => TransactionType::Expense,
                    'date' => $validated['date'],
                    'description' => "Top Up Investasi: {$investment->name}",
                ]);

                $affectsWallet = true;
            }

            $investment->transactions()->create([
                'wallet_id' => $validated['wallet_id'] ?? null,
                'type' => 'topup',
                'amount' => $amount,
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? 'Top up dana investasi',
                'affects_wallet' => $affectsWallet,
            ]);

            if ($updateValue) {
                $investment->increment('current_value', $amount);
            }
        });

        return redirect()->route('investments.index')->with('success', "Top up dana ke {$investment->name} berhasil dicatat!");
    }

    /**
     * Withdraw funds / liquidate investment.
     */
    public function withdraw(Request $request, Investment $investment): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();
        abort_unless($investment->user_id === $user->id, 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'date' => ['required', 'date'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'add_to_wallet' => ['nullable', 'boolean'],
            'close_investment' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.required' => 'Nominal penarikan wajib diisi.',
            'amount.min' => 'Nominal penarikan minimal Rp 1.',
            'date.required' => 'Tanggal penarikan wajib diisi.',
        ]);

        $amount = (float) $validated['amount'];
        $addToWallet = $request->boolean('add_to_wallet');
        $closeInvestment = $request->boolean('close_investment');

        DB::transaction(function () use ($user, $investment, $validated, $amount, $addToWallet, $closeInvestment) {
            $affectsWallet = false;

            if ($addToWallet && ! empty($validated['wallet_id'])) {
                $wallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);
                $wallet->increment('balance', $amount);

                $category = Category::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhereNull('user_id');
                })->where('name', 'like', '%Investasi%')->first();

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'category_id' => $category?->id,
                    'amount' => $amount,
                    'type' => TransactionType::Income,
                    'date' => $validated['date'],
                    'description' => "Pencairan Investasi: {$investment->name}",
                ]);

                $affectsWallet = true;
            }

            $investment->transactions()->create([
                'wallet_id' => $validated['wallet_id'] ?? null,
                'type' => 'withdraw',
                'amount' => $amount,
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? 'Penarikan / pencairan dana',
                'affects_wallet' => $affectsWallet,
            ]);

            $newCurrentValue = max(0.0, (float) $investment->current_value - $amount);
            $investment->current_value = $newCurrentValue;

            if ($closeInvestment || $newCurrentValue <= 0) {
                $investment->status = 'closed';
            }

            $investment->save();
        });

        return redirect()->route('investments.index')->with('success', "Penarikan dana dari {$investment->name} berhasil dicatat!");
    }

    /**
     * Update current market value of the investment.
     */
    public function updateValue(Request $request, Investment $investment): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();
        abort_unless($investment->user_id === $user->id, 403);

        $validated = $request->validate([
            'current_value' => ['required', 'numeric', 'min:0'],
        ], [
            'current_value.required' => 'Nilai pasar saat ini wajib diisi.',
            'current_value.min' => 'Nilai pasar tidak boleh bernilai negatif.',
        ]);

        $investment->update([
            'current_value' => $validated['current_value'],
        ]);

        return redirect()->route('investments.index')->with('success', "Nilai pasar {$investment->name} berhasil diperbarui!");
    }

    /**
     * Update investment details.
     */
    public function update(Request $request, Investment $investment): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();
        abort_unless($investment->user_id === $user->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'platform' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:mutual_fund,stock,crypto,gold,deposit,bond,p2p,other'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'target_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,closed'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $investment->update($validated);

        return redirect()->route('investments.index')->with('success', "Rincian investasi {$investment->name} berhasil diperbarui!");
    }

    /**
     * Remove the specified investment from storage.
     */
    public function destroy(Request $request, Investment $investment): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();
        abort_unless($investment->user_id === $user->id, 403);

        $name = $investment->name;
        $investment->delete();

        return redirect()->route('investments.index')->with('success', "Aset investasi {$name} berhasil dihapus.");
    }
}
