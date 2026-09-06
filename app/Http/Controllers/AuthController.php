<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the mobile-first login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Ensure primary user Gusti Swandana exists in database
        $enrolledUser = User::getPrimaryUser();
        $hasBiometrics = $enrolledUser->biometricCredentials()->exists();

        return view('auth.login', [
            'enrolledUser' => $enrolledUser,
            'hasBiometrics' => $hasBiometrics,
        ]);
    }

    /**
     * Handle standard email & password login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, '.Auth::user()->name.'!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Handle 6-digit PIN verification (Mobile Banking quick unlock).
     */
    public function verifyPin(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.size' => 'PIN harus terdiri dari 6 digit angka.',
            'pin.regex' => 'PIN hanya boleh berisi angka.',
        ]);

        $user = User::getPrimaryUser();

        if (! $user->verifyPin($validated['pin'])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN yang Anda masukkan salah.',
                ], 422);
            }

            return back()->withErrors(['pin' => 'PIN yang Anda masukkan salah.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
                'message' => 'Selamat datang kembali, '.$user->name.'!',
            ]);
        }

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang kembali, '.$user->name.'!');
    }
}
