<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    /**
     * Display a listing of debts and receivables.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $query = $user->debts()->with('wallet');

        if ($request->filled('type') && in_array($request->query('type'), ['debt', 'receivable'])) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('status')) {
            if ($request->query('status') === 'unpaid') {
                $query->whereIn('status', ['unpaid', 'partially_paid']);
            } elseif ($request->query('status') === 'paid') {
                $query->where('status', 'paid');
            }
        }

        $debts = $query->orderBy('created_at', 'desc')->get();

        // Calculate summaries
        $allDebts = $user->debts()->get();
        $totalReceivables = $allDebts->where('type', 'receivable')
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->sum(fn ($item) => $item->remaining_amount);

        $totalDebts = $allDebts->where('type', 'debt')
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->sum(fn ($item) => $item->remaining_amount);

        $wallets = $user->wallets()->get();

        return view('debts.index', [
            'user' => $user,
            'debts' => $debts,
            'totalReceivables' => $totalReceivables,
            'totalDebts' => $totalDebts,
            'wallets' => $wallets,
            'currentType' => $request->query('type', 'all'),
            'currentStatus' => $request->query('status', 'all'),
        ]);
    }

    /**
     * Store a newly created debt/receivable in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:debt,receivable'],
            'person_name' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1'],
            'due_date' => ['nullable', 'date'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'type.required' => 'Jenis utang/piutang wajib dipilih.',
            'person_name.required' => 'Nama orang/pihak wajib diisi.',
            'amount.required' => 'Nominal jumlah uang wajib diisi.',
            'amount.min' => 'Nominal minimal Rp 1.',
            'wallet_id.required' => 'Dompet atau rekening wajib dipilih.',
            'wallet_id.exists' => 'Dompet yang dipilih tidak valid.',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);

        DB::transaction(function () use ($user, $wallet, $validated) {
            $validated['paid_amount'] = 0;
            $validated['status'] = 'unpaid';

            $user->debts()->create($validated);

            $amount = (float) $validated['amount'];

            if ($validated['type'] === 'receivable') {
                // Orang utang ke saya -> Uang keluar dari dompet saya -> Saldo berkurang
                $wallet->decrement('balance', $amount);

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'target_wallet_id' => null,
                    'category_id' => null,
                    'amount' => $amount,
                    'type' => 'expense',
                    'date' => now()->toDateString(),
                    'description' => 'Memberikan Pinjaman ke '.$validated['person_name'],
                ]);
            } else {
                // Saya utang ke orang lain -> Uang masuk ke dompet saya -> Saldo bertambah
                $wallet->increment('balance', $amount);

                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'target_wallet_id' => null,
                    'category_id' => null,
                    'amount' => $amount,
                    'type' => 'income',
                    'date' => now()->toDateString(),
                    'description' => 'Penerimaan Pinjaman dari '.$validated['person_name'],
                ]);
            }
        });

        $typeLabel = $validated['type'] === 'debt' ? 'Utang' : 'Piutang';

        return redirect()->back()->with('success', "Catatan {$typeLabel} baru berhasil disimpan dan saldo dompet telah disinkronkan!");
    }

    /**
     * Update the specified debt/receivable in storage.
     */
    public function update(Request $request, Debt $debt): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($debt->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'person_name' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:'.((float) $debt->paid_amount ?: 1)],
            'due_date' => ['nullable', 'date'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'person_name.required' => 'Nama orang/pihak wajib diisi.',
            'amount.required' => 'Nominal jumlah uang wajib diisi.',
            'amount.min' => 'Nominal tidak boleh lebih kecil dari jumlah yang sudah dibayar.',
            'wallet_id.required' => 'Dompet atau rekening wajib dipilih.',
        ]);

        $newWallet = Wallet::where('user_id', $user->id)->findOrFail($validated['wallet_id']);

        DB::transaction(function () use ($debt, $validated, $newWallet) {
            $oldAmount = (float) $debt->amount;
            $newAmount = (float) $validated['amount'];
            $oldWalletId = $debt->wallet_id;
            $newWalletId = $newWallet->id;

            if ($oldWalletId && $oldWalletId === $newWalletId) {
                $diff = $newAmount - $oldAmount;
                if ($diff != 0) {
                    if ($debt->type === 'receivable') {
                        $newWallet->decrement('balance', $diff);
                    } else {
                        $newWallet->increment('balance', $diff);
                    }
                }
            } elseif ($oldWalletId && $oldWalletId !== $newWalletId) {
                $oldWallet = Wallet::find($oldWalletId);
                if ($oldWallet) {
                    if ($debt->type === 'receivable') {
                        $oldWallet->increment('balance', $oldAmount);
                    } else {
                        $oldWallet->decrement('balance', $oldAmount);
                    }
                }
                if ($debt->type === 'receivable') {
                    $newWallet->decrement('balance', $newAmount);
                } else {
                    $newWallet->increment('balance', $newAmount);
                }
            }

            $paidAmount = (float) $debt->paid_amount;
            $validated['status'] = $paidAmount >= $newAmount ? 'paid' : ($paidAmount > 0 ? 'partially_paid' : 'unpaid');

            $debt->update($validated);
        });

        return redirect()->back()->with('success', 'Catatan utang/piutang berhasil diperbarui!');
    }

    /**
     * Record a repayment / installment payment.
     */
    public function repay(Request $request, Debt $debt): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($debt->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $remaining = $debt->remaining_amount;

        $validated = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:1', 'max:'.$remaining],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'payment_amount.required' => 'Nominal pembayaran cicilan wajib diisi.',
            'payment_amount.max' => 'Nominal cicilan tidak boleh melebihi sisa tagihan (Rp '.number_format($remaining, 0, ',', '.').').',
            'wallet_id.required' => 'Dompet rekening wajib dipilih.',
        ]);

        $debt->recordPayment(
            (float) $validated['payment_amount'],
            (int) $validated['wallet_id'],
            $validated['date'] ?? null,
            $validated['notes'] ?? null
        );

        $actionLabel = $debt->type === 'debt' ? 'Pembayaran utang' : 'Penerimaan piutang';

        return redirect()->back()->with('success', "{$actionLabel} berhasil dicatat ke saldo dompet!");
    }

    /**
     * Remove the specified debt from storage.
     */
    public function destroy(Request $request, Debt $debt): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($debt->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        DB::transaction(function () use ($debt) {
            // Restore remaining un-repaid balance to the wallet
            if ($debt->wallet_id && $debt->remaining_amount > 0) {
                $wallet = Wallet::find($debt->wallet_id);
                if ($wallet) {
                    if ($debt->type === 'receivable') {
                        $wallet->increment('balance', $debt->remaining_amount);
                    } else {
                        $wallet->decrement('balance', $debt->remaining_amount);
                    }
                }
            }

            $debt->delete();
        });

        return redirect()->back()->with('success', 'Catatan utang/piutang berhasil dihapus dan saldo telah disesuaikan.');
    }
}
