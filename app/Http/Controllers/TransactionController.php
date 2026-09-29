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
use Illuminate\Http\JsonResponse;
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
    public function store(StoreTransactionRequest $request): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $type = $validated['type'];
        $clientUuid = $validated['client_uuid'] ?? null;

        // Idempotency check for offline sync: avoid double insertion
        if (! empty($clientUuid)) {
            $existing = Transaction::where('user_id', $user->id)
                ->where('client_uuid', $clientUuid)
                ->first();

            if ($existing) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Transaksi sudah tersinkronisasi sebelumnya.',
                        'duplicate' => true,
                        'transaction_id' => $existing->id,
                        'client_uuid' => $existing->client_uuid,
                    ]);
                }

                return back()->with('success', 'Transaksi sudah tersinkronisasi sebelumnya.');
            }
        }

        DB::transaction(function () use ($user, $validated, $amount, $type, $clientUuid) {
            $wallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);

            if ($type === TransactionType::Transfer->value) {
                $targetWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['target_wallet_id']);
                $adminFee = isset($validated['admin_fee']) ? (float) $validated['admin_fee'] : 0.0;
                $feePayer = $validated['fee_payer'] ?? 'source';

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'target_wallet_id' => $targetWallet->id,
                    'category_id' => $validated['category_id'] ?? null,
                    'amount' => $amount,
                    'admin_fee' => $adminFee,
                    'fee_payer' => $feePayer,
                    'type' => $type,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                    'client_uuid' => $clientUuid,
                ]);

                if ($feePayer === 'destination') {
                    $wallet->decrement('balance', $amount);
                    $targetWallet->increment('balance', max(0, $amount - $adminFee));
                } else {
                    $wallet->decrement('balance', $amount + $adminFee);
                    $targetWallet->increment('balance', $amount);
                }
            } else {
                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'target_wallet_id' => null,
                    'category_id' => $validated['category_id'],
                    'amount' => $amount,
                    'admin_fee' => 0,
                    'fee_payer' => 'source',
                    'type' => $type,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                    'client_uuid' => $clientUuid,
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
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse|JsonResponse
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
            $isOldTransfer = $transaction->type === TransactionType::Transfer || $transaction->type === 'transfer' || ($transaction->type instanceof TransactionType && $transaction->type === TransactionType::Transfer);
            $isOldIncome = $transaction->type === TransactionType::Income || $transaction->type === 'income' || ($transaction->type instanceof TransactionType && $transaction->type === TransactionType::Income);

            if ($isOldTransfer) {
                $oldAmount = (float) $transaction->amount;
                $oldFee = (float) ($transaction->admin_fee ?? 0);
                $oldFeePayer = $transaction->fee_payer ?? 'source';

                if ($oldFeePayer === 'destination') {
                    $transaction->wallet?->increment('balance', $oldAmount);
                    $transaction->targetWallet?->decrement('balance', max(0, $oldAmount - $oldFee));
                } else {
                    $transaction->wallet?->increment('balance', $oldAmount + $oldFee);
                    $transaction->targetWallet?->decrement('balance', $oldAmount);
                }
            } elseif ($isOldIncome) {
                $transaction->wallet?->decrement('balance', (float) $transaction->amount);
            } else {
                $transaction->wallet?->increment('balance', (float) $transaction->amount);
            }

            // 2. Fetch fresh new wallet instance(s)
            $newWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);

            if ($newType === TransactionType::Transfer->value) {
                $newTargetWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['target_wallet_id']);
                $newFee = isset($validated['admin_fee']) ? (float) $validated['admin_fee'] : 0.0;
                $newFeePayer = $validated['fee_payer'] ?? 'source';

                $transaction->update([
                    'wallet_id' => $newWallet->id,
                    'target_wallet_id' => $newTargetWallet->id,
                    'category_id' => null,
                    'amount' => $newAmount,
                    'admin_fee' => $newFee,
                    'fee_payer' => $newFeePayer,
                    'type' => $newType,
                    'date' => $validated['date'],
                    'description' => $validated['description'] ?? null,
                ]);

                if ($newFeePayer === 'destination') {
                    $newWallet->decrement('balance', $newAmount);
                    $newTargetWallet->increment('balance', max(0, $newAmount - $newFee));
                } else {
                    $newWallet->decrement('balance', $newAmount + $newFee);
                    $newTargetWallet->increment('balance', $newAmount);
                }
            } else {
                $transaction->update([
                    'wallet_id' => $newWallet->id,
                    'target_wallet_id' => null,
                    'category_id' => $validated['category_id'],
                    'amount' => $newAmount,
                    'admin_fee' => 0,
                    'fee_payer' => 'source',
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

        $message = 'Transaksi berhasil diperbarui!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'transaction' => $transaction->fresh(['wallet', 'targetWallet', 'category']),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove the specified transaction and revert the wallet balance.
     */
    public function destroy(Request $request, Transaction $transaction): RedirectResponse|JsonResponse
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
                $amount = (float) $transaction->amount;
                $fee = (float) ($transaction->admin_fee ?? 0);
                $feePayer = $transaction->fee_payer ?? 'source';

                if ($feePayer === 'destination') {
                    $transaction->wallet?->increment('balance', $amount);
                    $transaction->targetWallet?->decrement('balance', max(0, $amount - $fee));
                } else {
                    $transaction->wallet?->increment('balance', $amount + $fee);
                    $transaction->targetWallet?->decrement('balance', $amount);
                }
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

        $message = 'Transaksi berhasil dihapus!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Batch synchronize offline transactions queue idempotently.
     */
    public function sync(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $transactionsData = $request->input('transactions', []);
        if (! is_array($transactionsData) || empty($transactionsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrean transaksi untuk disinkronkan.',
                'synced_count' => 0,
            ], 422);
        }

        $syncedCount = 0;
        $duplicateCount = 0;
        $failedCount = 0;
        $syncedUuids = [];
        $errors = [];

        foreach ($transactionsData as $index => $item) {
            $clientUuid = $item['client_uuid'] ?? null;

            if ($clientUuid) {
                $alreadyExists = Transaction::where('user_id', $user->id)
                    ->where('client_uuid', $clientUuid)
                    ->exists();

                if ($alreadyExists) {
                    $duplicateCount++;
                    $syncedUuids[] = $clientUuid;

                    continue;
                }
            }

            try {
                DB::transaction(function () use ($user, $item, $clientUuid) {
                    $wallet = Wallet::where('user_id', $user->id)->findOrFail($item['wallet_id']);
                    $type = $item['type'] ?? TransactionType::Expense->value;

                    // Clean amount if string formatted
                    $rawAmount = $item['amount'] ?? 0;
                    if (is_string($rawAmount)) {
                        $rawAmount = preg_replace('/[^0-9\.]/', '', $rawAmount);
                    }
                    $amount = (float) $rawAmount;

                    if ($amount <= 0) {
                        throw new \InvalidArgumentException('Nominal transaksi harus lebih dari 0.');
                    }

                    if ($type === TransactionType::Transfer->value) {
                        $targetWallet = Wallet::where('user_id', $user->id)->findOrFail($item['target_wallet_id']);
                        $rawFee = $item['admin_fee'] ?? 0;
                        if (is_string($rawFee)) {
                            $rawFee = preg_replace('/[^0-9\.]/', '', $rawFee);
                        }
                        $adminFee = (float) $rawFee;
                        $feePayer = $item['fee_payer'] ?? 'source';

                        Transaction::create([
                            'user_id' => $user->id,
                            'wallet_id' => $wallet->id,
                            'target_wallet_id' => $targetWallet->id,
                            'category_id' => $item['category_id'] ?? null,
                            'amount' => $amount,
                            'admin_fee' => $adminFee,
                            'fee_payer' => $feePayer,
                            'type' => $type,
                            'date' => $item['date'] ?? now()->toDateString(),
                            'description' => $item['description'] ?? null,
                            'client_uuid' => $clientUuid,
                        ]);

                        if ($feePayer === 'destination') {
                            $wallet->decrement('balance', $amount);
                            $targetWallet->increment('balance', max(0, $amount - $adminFee));
                        } else {
                            $wallet->decrement('balance', $amount + $adminFee);
                            $targetWallet->increment('balance', $amount);
                        }
                    } else {
                        Transaction::create([
                            'user_id' => $user->id,
                            'wallet_id' => $wallet->id,
                            'target_wallet_id' => null,
                            'category_id' => $item['category_id'] ?? null,
                            'amount' => $amount,
                            'admin_fee' => 0,
                            'fee_payer' => 'source',
                            'type' => $type,
                            'date' => $item['date'] ?? now()->toDateString(),
                            'description' => $item['description'] ?? null,
                            'client_uuid' => $clientUuid,
                        ]);

                        if ($type === TransactionType::Income->value) {
                            $wallet->increment('balance', $amount);
                        } else {
                            $wallet->decrement('balance', $amount);
                        }
                    }
                });

                $syncedCount++;
                if ($clientUuid) {
                    $syncedUuids[] = $clientUuid;
                }
            } catch (\Throwable $e) {
                $failedCount++;
                $errors[] = [
                    'index' => $index,
                    'client_uuid' => $clientUuid,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sinkronisasi selesai: {$syncedCount} transaksi tersimpan, {$duplicateCount} sudah tercatat sebelumnya, {$failedCount} gagal.",
            'synced_count' => $syncedCount,
            'duplicate_count' => $duplicateCount,
            'failed_count' => $failedCount,
            'synced_uuids' => $syncedUuids,
            'errors' => $errors,
        ]);
    }
}
