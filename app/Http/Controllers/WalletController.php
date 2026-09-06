<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the user's wallets and accounts.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $wallets = $user->wallets()->withCount('transactions')->latest()->get();
        $totalBalance = $wallets->sum('balance');

        $byType = [
            'bank' => $wallets->where('type', 'bank')->sum('balance'),
            'ewallet' => $wallets->where('type', 'ewallet')->sum('balance'),
            'cash' => $wallets->where('type', 'cash')->sum('balance'),
            'other' => $wallets->whereNotIn('type', ['bank', 'ewallet', 'cash'])->sum('balance'),
        ];

        return view('wallets.index', [
            'user' => $user,
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
            'byType' => $byType,
        ]);
    }

    /**
     * Store a newly created wallet in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:bank,ewallet,cash,investment,other'],
            'balance' => ['required', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Nama rekening/dompet wajib diisi.',
            'type.required' => 'Jenis dompet wajib dipilih.',
            'type.in' => 'Jenis dompet tidak valid.',
            'balance.required' => 'Saldo awal wajib diisi.',
            'balance.min' => 'Saldo awal tidak boleh bernilai negatif.',
        ]);

        $user->wallets()->create($validated);

        return redirect()->back()->with('success', 'Rekening/Dompet baru berhasil ditambahkan!');
    }

    /**
     * Update the specified wallet in storage.
     */
    public function update(Request $request, Wallet $wallet): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($wallet->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:bank,ewallet,cash,investment,other'],
            'balance' => ['required', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Nama rekening/dompet wajib diisi.',
            'type.required' => 'Jenis dompet wajib dipilih.',
            'balance.required' => 'Saldo wajib diisi.',
            'balance.min' => 'Saldo tidak boleh bernilai negatif.',
        ]);

        $wallet->update($validated);

        return redirect()->back()->with('success', 'Data dompet berhasil diperbarui!');
    }

    /**
     * Remove the specified wallet from storage.
     */
    public function destroy(Request $request, Wallet $wallet): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($wallet->user_id !== $user->id, 403, 'Akses tidak diizinkan.');

        if ($user->wallets()->count() <= 1) {
            return redirect()->back()->withErrors(['wallet' => 'Anda harus memiliki minimal satu rekening/dompet aktif.']);
        }

        $wallet->delete();

        return redirect()->back()->with('success', 'Rekening/Dompet berhasil dihapus.');
    }
}
