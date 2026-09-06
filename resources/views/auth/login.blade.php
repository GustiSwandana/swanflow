<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#020617">
    <meta name="color-scheme" content="light dark">
    <meta name="description" content="Masuk ke SwanFlow - Personal Financial Tracker Gusti Swandana">

    <!-- Instant Dark Mode Script -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('swanflow_theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#020617');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#ffffff');
                }
            } catch (e) {}
        })();
    </script>

    <!-- PWA Icons -->
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="alternate icon" type="image/png" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <title>Masuk - SwanFlow Tracker</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sat: env(safe-area-inset-top, 0px);
            --sab: env(safe-area-inset-bottom, 0px);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Plus Jakarta Sans', system-ui, sans-serif;
            min-height: 100dvh;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="min-h-full bg-slate-950 flex justify-center text-slate-800 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Frame Container -->
    <div class="w-full max-w-md min-h-full min-h-[100dvh] bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 relative flex flex-col justify-between p-6 shadow-2xl border-x border-slate-200/80 dark:border-slate-800/80 transition-colors duration-200" style="padding-top: max(1.5rem, calc(var(--sat) + 1rem)); padding-bottom: max(1.5rem, calc(var(--sab) + 1rem));">

        <!-- Top Header & Theme Switcher -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2.5">
                <x-app-logo class="w-9 h-9" variant="badge" />
                <span class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">SwanFlow</span>
            </div>

            <button type="button" 
                    onclick="toggleSwanFlowTheme()" 
                    aria-label="Toggle Mode Gelap atau Terang" 
                    class="min-w-[44px] min-h-[44px] w-11 h-11 flex items-center justify-center rounded-full text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-amber-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/80 active:scale-95 transition-all">
                <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="w-5 h-5 block dark:hidden text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </div>

        <!-- Flash Message -->
        @if(session('success'))
            <div class="mb-4 bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-rose-500/15 border border-rose-500/30 text-rose-700 dark:text-rose-300 px-4 py-3 rounded-2xl text-xs font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. BANKING LOCK SCREEN (Default view for enrolled user) -->
        <div id="banking-lock-screen" class="flex-1 flex flex-col justify-between my-auto">
            <!-- User Profile Avatar & Greeting -->
            <div class="text-center pt-2">
                <div class="relative inline-block mx-auto mb-3">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-950 to-slate-800 text-emerald-400 font-extrabold text-xl flex items-center justify-center shadow-lg ring-4 ring-emerald-500/25 border border-slate-700">
                        GS
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white dark:ring-slate-950"></span>
                </div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Selamat Datang</span>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5 tracking-tight">
                    {{ $enrolledUser->name ?? 'Gusti Swandana' }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukkan 6-digit PIN Anda untuk membuka</p>
            </div>

            <!-- PIN Indicator Dots -->
            <div class="py-2">
                <div id="pin-dots-container" class="flex items-center justify-center gap-3.5 my-2 transition-transform">
                    <div id="pin-dot-0" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-transparent transition-all duration-150"></div>
                    <div id="pin-dot-1" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-transparent transition-all duration-150"></div>
                    <div id="pin-dot-2" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-transparent transition-all duration-150"></div>
                    <div id="pin-dot-3" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-transparent transition-all duration-150"></div>
                    <div id="pin-dot-4" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-transparent transition-all duration-150"></div>
                    <div id="pin-dot-5" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 dark:border-slate-600 bg-transparent transition-all duration-150"></div>
                </div>
                <div id="pin-error-message" class="text-xs font-semibold text-rose-500 text-center min-h-[18px]"></div>
            </div>

            <!-- Mobile Banking Numeric Keypad -->
            <div class="grid grid-cols-3 gap-2.5 max-w-[290px] mx-auto w-full select-none">
                <button type="button" onclick="appendPinDigit('1')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">1</button>
                <button type="button" onclick="appendPinDigit('2')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">2</button>
                <button type="button" onclick="appendPinDigit('3')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">3</button>

                <button type="button" onclick="appendPinDigit('4')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">4</button>
                <button type="button" onclick="appendPinDigit('5')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">5</button>
                <button type="button" onclick="appendPinDigit('6')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">6</button>

                <button type="button" onclick="appendPinDigit('7')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">7</button>
                <button type="button" onclick="appendPinDigit('8')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">8</button>
                <button type="button" onclick="appendPinDigit('9')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">9</button>

                <!-- Face ID Biometric Quick Trigger Button -->
                <button type="button" 
                        onclick="authenticateWithFaceId()" 
                        aria-label="Buka dengan Face ID" 
                        class="min-h-[56px] rounded-2xl bg-emerald-500/10 hover:bg-emerald-500/20 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 shadow-xs active:scale-90 transition-all flex flex-col items-center justify-center gap-0.5">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                        <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                        <path d="M16 20h2a2 2 0 0 0 2-2v-2" />
                        <path d="M9 10h.01" />
                        <path d="M15 10h.01" />
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                        <path d="M12 11v2" />
                    </svg>
                    <span class="text-[9px] font-bold">Face ID</span>
                </button>

                <button type="button" onclick="appendPinDigit('0')" class="min-h-[56px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xl font-bold text-slate-800 dark:text-white shadow-xs active:scale-90 active:bg-slate-100 dark:active:bg-slate-800 transition-all">0</button>

                <!-- Backspace Key -->
                <button type="button" 
                        onclick="removePinDigit()" 
                        aria-label="Hapus angka" 
                        class="min-h-[56px] rounded-2xl bg-transparent border border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 shadow-none active:scale-90 transition-all flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33z" />
                    </svg>
                </button>
            </div>

            <!-- Switch to Password Login -->
            <div class="pt-4 text-center">
                <button type="button" onclick="toggleLoginView('password')" class="text-xs font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors">
                    Masuk dengan Email & Password
                </button>
            </div>
        </div>

        <!-- 2. EMAIL & PASSWORD SCREEN (Alternative view) -->
        <div id="email-password-screen" class="hidden flex-1 flex flex-col justify-center my-auto">
            <div class="mb-4">
                <button type="button" onclick="toggleLoginView('pin')" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline mb-2">
                    ← Kembali ke PIN & Face ID
                </button>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">Masuk Akun</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukkan kredensial akun Anda.</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Email</label>
                    <div class="relative">
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', 'gustiswandana@swanflow.com') }}" 
                               required 
                               autocomplete="email"
                               placeholder="nama@swanflow.com" 
                               class="w-full min-h-[48px] px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-600 dark:text-slate-300">Password</label>
                    </div>
                    <div class="relative">
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••" 
                               class="w-full min-h-[48px] px-3.5 py-2.5 pr-12 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                aria-label="Lihat Password" 
                                class="absolute right-2 top-1/2 -translate-y-1/2 min-w-[44px] min-h-[44px] flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded-md border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/30">
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Ingat Saya di Perangkat Ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-sm shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Masuk dengan Akun
                    </button>
                </div>
            </form>

            <!-- Face ID Alternate Button -->
            <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-800">
                <button type="button" onclick="authenticateWithFaceId()" class="w-full min-h-[44px] py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                    <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                        <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                        <path d="M16 20h2a2 2 0 0 0 2-2v-2" />
                        <path d="M9 10h.01" />
                        <path d="M15 10h.01" />
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                        <path d="M12 11v2" />
                    </svg>
                    <span>Buka dengan Face ID</span>
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-6 text-[11px] text-slate-400">
            SwanFlow Personal Finance &bull; Versi 1.2.0 PWA
        </div>

    </div>

    <!-- Face ID Scanning Overlay Modal -->
    <div id="face-id-modal" class="fixed inset-0 z-50 hidden transition-all duration-300 flex items-center justify-center p-4" aria-modal="true" role="dialog">
        <div onclick="closeFaceIdModal()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>
        <div class="relative z-10 w-full max-w-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-2xl text-center space-y-4 transform transition-transform">
            <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                <div id="face-id-pulse" class="absolute inset-0 rounded-2xl bg-emerald-500/20 animate-ping"></div>
                <div class="relative w-20 h-20 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-emerald-500 shadow-inner border border-slate-200 dark:border-slate-700">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                        <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                        <path d="M16 20h2a2 2 0 0 0 2-2v-2" />
                        <path d="M9 10h.01" />
                        <path d="M15 10h.01" />
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                        <path d="M12 11v2" />
                    </svg>
                </div>
            </div>

            <div>
                <h3 id="face-id-status-title" class="text-sm font-bold text-slate-800 dark:text-white">Face ID</h3>
                <p id="face-id-status-text" class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                    Menghubungkan ke sensor biometrik...
                </p>
            </div>

            <div class="pt-2">
                <button type="button" onclick="closeFaceIdModal()" class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold active:scale-98 transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentPin = '';
        const maxPinLength = 6;

        function appendPinDigit(digit) {
            if (currentPin.length < maxPinLength) {
                currentPin += digit;
                updatePinDots();
                clearPinError();
                if (currentPin.length === maxPinLength) {
                    submitPin();
                }
            }
        }

        function removePinDigit() {
            if (currentPin.length > 0) {
                currentPin = currentPin.slice(0, -1);
                updatePinDots();
                clearPinError();
            }
        }

        function updatePinDots() {
            for (let i = 0; i < maxPinLength; i++) {
                const dot = document.getElementById('pin-dot-' + i);
                if (dot) {
                    if (i < currentPin.length) {
                        dot.classList.add('bg-emerald-500', 'border-emerald-500', 'scale-110');
                        dot.classList.remove('bg-transparent', 'border-slate-300', 'dark:border-slate-600');
                    } else {
                        dot.classList.remove('bg-emerald-500', 'border-emerald-500', 'scale-110');
                        dot.classList.add('bg-transparent', 'border-slate-300', 'dark:border-slate-600');
                    }
                }
            }
        }

        function clearPinError() {
            const errorEl = document.getElementById('pin-error-message');
            if (errorEl) {
                errorEl.textContent = '';
            }
        }

        async function submitPin() {
            const dotsContainer = document.getElementById('pin-dots-container');
            const errorEl = document.getElementById('pin-error-message');

            try {
                const res = await fetch('{{ route("auth.pin.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ pin: currentPin })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    window.location.href = data.redirect || '/';
                } else {
                    if (dotsContainer) dotsContainer.classList.add('animate-shake');
                    if (errorEl) {
                        errorEl.textContent = data.message || 'PIN yang Anda masukkan salah.';
                    }
                    setTimeout(() => {
                        if (dotsContainer) dotsContainer.classList.remove('animate-shake');
                        currentPin = '';
                        updatePinDots();
                    }, 500);
                }
            } catch (err) {
                if (errorEl) {
                    errorEl.textContent = 'Gagal memverifikasi PIN. Silakan coba lagi.';
                }
                currentPin = '';
                updatePinDots();
            }
        }

        function toggleLoginView(view) {
            const lockScreen = document.getElementById('banking-lock-screen');
            const passwordScreen = document.getElementById('email-password-screen');
            if (view === 'password') {
                lockScreen.classList.add('hidden');
                passwordScreen.classList.remove('hidden');
            } else {
                passwordScreen.classList.add('hidden');
                lockScreen.classList.remove('hidden');
            }
        }

        // Keyboard listener for physical keyboard or desktop testing
        document.addEventListener('keydown', (e) => {
            const lockScreen = document.getElementById('banking-lock-screen');
            if (lockScreen && !lockScreen.classList.contains('hidden')) {
                if (e.key >= '0' && e.key <= '9') {
                    appendPinDigit(e.key);
                } else if (e.key === 'Backspace') {
                    removePinDigit();
                }
            }
        });

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        function toggleSwanFlowTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            const themeMeta = document.querySelector('meta[name="theme-color"]');
            if (isDark) {
                localStorage.setItem('swanflow_theme', 'dark');
                if (themeMeta) themeMeta.setAttribute('content', '#020617');
            } else {
                localStorage.setItem('swanflow_theme', 'light');
                if (themeMeta) themeMeta.setAttribute('content', '#ffffff');
            }
        }

        function hexToBytes(hex) {
            const bytes = new Uint8Array(hex.length / 2);
            for (let i = 0; i < hex.length; i += 2) {
                bytes[i / 2] = parseInt(hex.substr(i, 2), 16);
            }
            return bytes;
        }

        function bufferToBase64(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            for (let i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return btoa(binary);
        }

        function closeFaceIdModal() {
            const modal = document.getElementById('face-id-modal');
            if (modal) modal.classList.add('hidden');
        }

        async function authenticateWithFaceId() {
            const modal = document.getElementById('face-id-modal');
            const statusTitle = document.getElementById('face-id-status-title');
            const statusText = document.getElementById('face-id-status-text');
            const pulse = document.getElementById('face-id-pulse');

            modal.classList.remove('hidden');
            if (pulse) pulse.classList.remove('hidden');
            statusTitle.textContent = 'Memindai Face ID...';
            statusText.textContent = 'Menghubungkan ke sensor keamanan iPhone...';

            try {
                // 1. Request challenge
                const res = await fetch('{{ route("faceid.login.challenge") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!res.ok) {
                    throw new Error('Gagal mendapatkan sesi verifikasi server.');
                }

                const data = await res.json();

                if (!data.registered || !data.credentials || data.credentials.length === 0) {
                    if (pulse) pulse.classList.add('hidden');
                    statusTitle.textContent = 'Face ID Belum Aktif';
                    statusText.textContent = 'Face ID belum didaftarkan pada akun ini. Silakan masuk terlebih dahulu dengan email & password, lalu aktifkan Face ID di menu Profil.';
                    return;
                }

                statusText.textContent = 'Arahkan wajah Anda ke kamera iPhone...';

                // Check WebAuthn support
                if (!window.PublicKeyCredential) {
                    if (pulse) pulse.classList.add('hidden');
                    statusTitle.textContent = !window.isSecureContext ? 'Perlu Koneksi HTTPS' : 'Tidak Didukung';
                    statusText.textContent = !window.isSecureContext
                        ? 'Sensor biometrik Face ID dinonaktifkan browser karena web dibuka via HTTP biasa (bukan HTTPS). Gunakan koneksi aman HTTPS atau masuk dengan PIN 6-Digit.'
                        : 'Peramban ini tidak mendukung sensor biometrik Face ID WebAuthn. Gunakan PIN 6-Digit untuk masuk cepat.';
                    return;
                }

                const challengeBytes = hexToBytes(data.challenge);
                const allowCredentials = data.credentials.map(credId => {
                    let idBytes;
                    try {
                        const bin = atob(credId.replace(/-/g, '+').replace(/_/g, '/'));
                        idBytes = Uint8Array.from(bin, c => c.charCodeAt(0));
                    } catch (e) {
                        idBytes = new TextEncoder().encode(credId);
                    }
                    return {
                        type: 'public-key',
                        id: idBytes
                    };
                });

                const assertion = await navigator.credentials.get({
                    publicKey: {
                        challenge: challengeBytes,
                        rpId: data.rpId || window.location.hostname,
                        allowCredentials: allowCredentials,
                        userVerification: 'required',
                        timeout: 60000
                    }
                });

                if (assertion) {
                    statusTitle.textContent = 'Wajah Dikenali!';
                    statusText.textContent = 'Mengonfirmasi otentikasi...';

                    let clientDataJson = '';
                    if (assertion.response && assertion.response.clientDataJSON) {
                        clientDataJson = bufferToBase64(assertion.response.clientDataJSON);
                    }

                    const verifyRes = await fetch('{{ route("faceid.login.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            credential_id: assertion.id,
                            client_data_json: clientDataJson
                        })
                    });

                    const result = await verifyRes.json();

                    if (verifyRes.ok && result.success) {
                        statusTitle.textContent = 'Berhasil Masuk!';
                        statusText.textContent = 'Membuka SwanFlow...';
                        window.location.href = result.redirect || '/';
                    } else {
                        throw new Error(result.message || 'Verifikasi biometrik tidak cocok.');
                    }
                }
            } catch (err) {
                if (pulse) pulse.classList.add('hidden');
                if (err.name === 'NotAllowedError') {
                    statusTitle.textContent = 'Pemindaian Dibatalkan';
                    statusText.textContent = 'Anda membatalkan pemindaian Face ID atau waktu habis.';
                } else {
                    statusTitle.textContent = 'Verifikasi Gagal';
                    statusText.textContent = err.message || 'Terjadi kesalahan pada verifikasi Face ID.';
                }
            }
        }
    </script>
</body>
</html>
