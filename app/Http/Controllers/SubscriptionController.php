<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions and recurring bills.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $subscriptions = $user->subscriptions()
            ->with(['wallet', 'category'])
            ->orderBy('next_due_date', 'asc')
            ->get();

        $monthlyTotal = $subscriptions->where('status', 'active')->reduce(function ($carry, $sub) {
            $amt = (float) $sub->amount;

            return $carry + match ($sub->cycle) {
                'weekly' => $amt * 4.33,
                'yearly' => $amt / 12,
                default => $amt,
            };
        }, 0.0);

        $wallets = $user->wallets()->get();
        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->where('type', TransactionType::Expense)->get();

        return view('subscriptions.index', [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'monthlyTotal' => $monthlyTotal,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1'],
            'cycle' => ['required', 'string', 'in:monthly,weekly,yearly'],
            'billing_date' => ['required', 'integer', 'min:1', 'max:31'],
            'next_due_date' => ['required', 'date'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', 'string', 'in:active,paused'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama langganan/tagihan wajib diisi.',
            'amount.required' => 'Nominal tagihan wajib diisi.',
            'amount.min' => 'Nominal tagihan minimal Rp 1.',
            'cycle.required' => 'Siklus tagihan wajib dipilih.',
            'billing_date.required' => 'Tanggal jatuh tempo wajib diisi.',
            'next_due_date.required' => 'Tanggal tagihan berikutnya wajib diisi.',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';

        $user->subscriptions()->create($validated);

        return redirect()->back()->with('success', 'Tagihan rutin/langganan berhasil ditambahkan!');
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($subscription->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1'],
            'cycle' => ['required', 'string', 'in:monthly,weekly,yearly'],
            'billing_date' => ['required', 'integer', 'min:1', 'max:31'],
            'next_due_date' => ['required', 'date'],
            'wallet_id' => ['nullable', 'exists:wallets,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'string', 'in:active,paused'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama langganan/tagihan wajib diisi.',
            'amount.required' => 'Nominal tagihan wajib diisi.',
            'amount.min' => 'Nominal tagihan minimal Rp 1.',
        ]);

        $subscription->update($validated);

        return redirect()->back()->with('success', 'Rincian langganan berhasil diperbarui!');
    }

    /**
     * Pay the bill now and record a transaction.
     */
    public function pay(Request $request, Subscription $subscription): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($subscription->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'wallet_id' => ['required', 'exists:wallets,id'],
            'date' => ['nullable', 'date'],
        ], [
            'wallet_id.required' => 'Dompet sumber pembayaran wajib dipilih.',
        ]);

        $subscription->markAsPaid($validated['wallet_id'], $validated['date'] ?? null);

        return redirect()->back()->with('success', "Tagihan '{$subscription->name}' berhasil dibayar dan transaksi pengeluaran telah dicatat!");
    }

    /**
     * Remove the specified subscription from storage.
     */
    public function destroy(Request $request, Subscription $subscription): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($subscription->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $subscription->delete();

        return redirect()->back()->with('success', 'Langganan berhasil dihapus.');
    }
}
