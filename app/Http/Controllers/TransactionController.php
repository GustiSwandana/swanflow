<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a paginated list of transactions with filtering.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $query = Transaction::with(['wallet', 'targetWallet', 'category'])
            ->where('user_id', $user->id);

        if ($request->filled('type') && in_array($request->query('type'), ['income', 'expense', 'transfer'])) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('wallet_id')) {
            $walletId = $request->query('wallet_id');
            $query->where(function ($q) use ($walletId) {
                $q->where('wallet_id', $walletId)
                    ->orWhere('target_wallet_id', $walletId);
            });
        }

        $currentMonth = $request->query('month', now()->format('Y-m'));
        if ($request->filled('month')) {
            $monthVal = $request->query('month');
            if (str_contains($monthVal, '-')) {
                [$y, $m] = explode('-', $monthVal);
                $query->whereYear('date', $y)->whereMonth('date', $m);
            } else {
                $query->whereMonth('date', $monthVal);
            }
        }

        $transactions = $query->latest('date')->latest('id')->paginate(15)->withQueryString();
        $wallets = Wallet::where('user_id', $user->id)->get();
        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->get();

        return view('transactions.index', [
            'user' => $user,
            'transactions' => $transactions,
            'wallets' => $wallets,
            'categories' => $categories,
            'currentType' => $request->query('type', 'all'),
            'currentCategoryId' => $request->query('category_id'),
            'currentWalletId' => $request->query('wallet_id'),
            'currentMonth' => $currentMonth,
        ]);
    }

    /**
     * Store a newly created transaction and update wallet balance atomically.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $type = $validated['type'];

        DB::transaction(function () use ($user, $validated, $amount, $type) {
            $wallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);

            if ($type === TransactionType::Transfer->value) {
                $targetWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['target_wallet_id']);

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'target_wallet_id' => $targetWallet->id,
                    'category_id' => $validated['category_id'] ?? null,
                    'amount' => $amount,
                    'type' => $type,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                ]);

                $wallet->decrement('balance', $amount);
                $targetWallet->increment('balance', $amount);
            } else {
                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'target_wallet_id' => null,
                    'category_id' => $validated['category_id'],
                    'amount' => $amount,
                    'type' => $type,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                ]);

                if ($type === TransactionType::Income->value) {
                    $wallet->increment('balance', $amount);
                } else {
                    $wallet->decrement('balance', $amount);
                }
            }
        });

        $message = $type === TransactionType::Transfer->value
            ? 'Transfer saldo berhasil disimpan!'
            : 'Transaksi berhasil disimpan!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update the specified transaction and adjust wallet balances atomically.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        if ($transaction->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validated();
        $newAmount = (float) $validated['amount'];
        $newType = $validated['type'];

        DB::transaction(function () use ($user, $transaction, $validated, $newAmount, $newType) {
            // 1. Revert previous transaction effects on old wallet(s)
            if ($transaction->type === TransactionType::Transfer || $transaction->type === 'transfer') {
                $transaction->wallet?->increment('balance', (float) $transaction->amount);
                $transaction->targetWallet?->decrement('balance', (float) $transaction->amount);
            } elseif ($transaction->type === TransactionType::Income || $transaction->type === 'income') {
                $transaction->wallet?->decrement('balance', (float) $transaction->amount);
            } else {
                $transaction->wallet?->increment('balance', (float) $transaction->amount);
            }

            // 2. Fetch fresh new wallet instance(s)
            $newWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);

            if ($newType === TransactionType::Transfer->value) {
                $newTargetWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['target_wallet_id']);

                $transaction->update([
                    'wallet_id' => $newWallet->id,
                    'target_wallet_id' => $newTargetWallet->id,
                    'category_id' => null,
                    'amount' => $newAmount,
                    'type' => $newType,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                ]);

                $newWallet->decrement('balance', $newAmount);
                $newTargetWallet->increment('balance', $newAmount);
            } else {
                $transaction->update([
                    'wallet_id' => $newWallet->id,
                    'target_wallet_id' => null,
                    'category_id' => $validated['category_id'],
                    'amount' => $newAmount,
                    'type' => $newType,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                ]);

                if ($newType === TransactionType::Income->value) {
                    $newWallet->increment('balance', $newAmount);
                } else {
                    $newWallet->decrement('balance', $newAmount);
                }
            }
        });

        return back()->with('success', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Remove the specified transaction and revert the wallet balance.
     */
    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        if ($transaction->user_id !== $user->id) {
            abort(403);
        }

        DB::transaction(function () use ($transaction) {
            $isTransfer = $transaction->type === TransactionType::Transfer || $transaction->type === 'transfer' || ($transaction->type instanceof TransactionType && $transaction->type === TransactionType::Transfer);
            $isIncome = $transaction->type === TransactionType::Income || $transaction->type === 'income' || ($transaction->type instanceof TransactionType && $transaction->type === TransactionType::Income);

            if ($isTransfer) {
                $transaction->wallet?->increment('balance', (float) $transaction->amount);
                $transaction->targetWallet?->decrement('balance', (float) $transaction->amount);
            } else {
                $wallet = $transaction->wallet;

                if ($wallet) {
                    if ($isIncome) {
                        $wallet->decrement('balance', (float) $transaction->amount);
                    } else {
                        $wallet->increment('balance', (float) $transaction->amount);
                    }
                }
            }

            $transaction->delete();
        });

        return back()->with('success', 'Transaksi berhasil dihapus!');
    }
}
