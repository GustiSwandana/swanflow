<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user profile edit screen.
     */
    public function edit(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $walletsCount = $user->wallets()->count();
        $transactionsCount = $user->transactions()->count();
        $biometricCredentials = $user->biometricCredentials()->latest()->get();

        return view('profile.edit', [
            'user' => $user,
            'walletsCount' => $walletsCount,
            'transactionsCount' => $transactionsCount,
            'biometricCredentials' => $biometricCredentials,
        ]);
    }

    /**
     * Update the user's name and email profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password Anda berhasil diperbarui!');
    }

    /**
     * Update the user's 6-digit security PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $rules = [
            'pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/', 'confirmed'],
        ];

        if ($user->hasPin()) {
            $rules['current_pin'] = ['required', 'string', 'size:6'];
        }

        $validated = $request->validate($rules, [
            'current_pin.required' => 'PIN saat ini wajib diisi.',
            'current_pin.size' => 'PIN saat ini harus 6 digit angka.',
            'pin.required' => 'PIN baru wajib diisi.',
            'pin.size' => 'PIN baru harus terdiri dari 6 digit angka.',
            'pin.regex' => 'PIN baru hanya boleh berisi angka.',
            'pin.confirmed' => 'Konfirmasi PIN baru tidak cocok.',
        ]);

        if ($user->hasPin() && ! $user->verifyPin($validated['current_pin'])) {
            return back()->withErrors(['current_pin' => 'PIN saat ini tidak cocok.']);
        }

        $user->update([
            'pin' => Hash::make($validated['pin']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'PIN Keamanan 6-Digit Anda berhasil diperbarui!');
    }
}
