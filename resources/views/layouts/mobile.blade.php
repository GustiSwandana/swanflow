<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="color-scheme" content="light dark">
    <meta name="description" content="Personal Financial Tracker Mobile App eksklusif Gusti Swandana">

    <!-- Instant Dark Mode Script (Prevents FOUC) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('swanflow_theme');
                if (savedTheme === 'light') {
                    document.documentElement.classList.remove('dark');
                    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#ffffff');
                } else {
                    document.documentElement.classList.add('dark');
                    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#020617');
                }
            } catch (e) {}
        })();
    </script>

    <!-- PWA Web App Manifest & App Icons -->
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="alternate icon" type="image/png" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">

    <title>{{ $title ?? config('app.name', 'SwanFlow Tracker') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sat: env(safe-area-inset-top, 0px);
            --sab: env(safe-area-inset-bottom, 0px);
            --sal: env(safe-area-inset-left, 0px);
            --sar: env(safe-area-inset-right, 0px);
        }
        html {
            height: 100%;
            height: -webkit-fill-available;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Plus Jakarta Sans', system-ui, sans-serif;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            min-height: 100%;
            min-height: 100dvh;
            min-height: -webkit-fill-available;
            overscroll-behavior-y: none;
        }
        /* Custom scrollbar reset */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Dynamic iOS Safe Area Insets (iPhone 15, Dynamic Island & Home Indicator) */
        .header-safe {
            padding-top: max(3rem, calc(var(--sat, 0px) + 0.75rem));
            padding-left: max(1rem, calc(var(--sal) + 1rem));
            padding-right: max(1rem, calc(var(--sar) + 1rem));
        }
        .bottom-nav-safe {
            padding-bottom: max(0.5rem, calc(var(--sab) + 0.25rem));
            padding-left: max(0.5rem, calc(var(--sal) + 0.5rem));
            padding-right: max(0.5rem, calc(var(--sar) + 0.5rem));
        }
        .content-safe {
            padding-bottom: max(7.5rem, calc(6.75rem + var(--sab)));
            padding-left: max(1rem, calc(var(--sal) + 1rem));
            padding-right: max(1rem, calc(var(--sar) + 1rem));
        }
        .modal-sheet-safe {
            padding-bottom: max(1.75rem, calc(1.25rem + var(--sab)));
            max-height: calc(90dvh - var(--sat));
        }
        .banner-safe {
            bottom: max(5.25rem, calc(4.75rem + var(--sab)));
        }
    </style>
</head>
<body class="min-h-full bg-slate-100 dark:bg-slate-950 flex justify-center text-slate-800 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white transition-colors duration-200">
    <!-- Mobile Frame Container (Fluid 100% on iPhones, Max-W-MD for desktop preview) -->
    <div class="w-full max-w-md min-h-full min-h-[100dvh] bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 relative flex flex-col shadow-2xl border-x border-slate-200/80 dark:border-slate-800/80 overflow-x-hidden transition-colors duration-200">

        <!-- 1. TOP HEADER -->
        @hasSection('custom_header')
            @yield('custom_header')
        @else
        <header class="sticky top-0 z-30 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-100 dark:border-slate-800/80 header-safe pb-3 flex items-center justify-between transition-colors">
            <div class="flex items-center gap-2.5">
                @section('header_left')
                    <a href="{{ route('dashboard') }}" class="active:scale-95 transition-transform" aria-label="Beranda">
                        <x-app-logo class="w-8 h-8" variant="badge" />
                    </a>
                    <div class="flex flex-col">
                        <span class="text-xs font-medium text-slate-400 dark:text-slate-400">Selamat Datang 👋</span>
                        <h1 class="text-base font-bold text-slate-800 dark:text-white tracking-tight leading-tight">
                            {{ $pageTitle ?? 'Financial Tracker' }}
                        </h1>
                    </div>
                @show
            </div>

            <!-- Profile, Theme Toggle & Notification Area (Min 44x44px touch targets) -->
            <div class="flex items-center gap-1.5">
                <!-- Theme Toggle Button (Dark / Light Mode) -->
                <button type="button" 
                        id="theme-toggle-btn"
                        onclick="toggleSwanFlowTheme()" 
                        aria-label="Ganti Tema Gelap atau Terang" 
                        class="min-w-[44px] min-h-[44px] w-11 h-11 flex items-center justify-center rounded-full text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 active:scale-95 transition-all">
                    <!-- Sun Icon (visible in dark mode) -->
                    <svg class="w-5 h-5 hidden dark:block text-amber-400 transition-transform duration-300 transform rotate-0 hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <!-- Moon Icon (visible in light mode) -->
                    <svg class="w-5 h-5 block dark:hidden text-slate-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                <!-- Notification Button (Touch-friendly 44x44px) -->
                <button type="button" aria-label="Notifikasi" class="min-w-[44px] min-h-[44px] w-11 h-11 flex items-center justify-center rounded-full text-slate-400 hover:text-white hover:bg-slate-800/80 active:scale-95 transition-all">
                    <span class="relative flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-slate-900"></span>
                    </span>
                </button>

                <!-- User Profile Avatar (Gusti Swandana) -->
                <a href="{{ route('profile.edit') }}" class="min-w-[44px] min-h-[44px] flex items-center justify-center active:scale-95 transition-transform" title="Profil Gusti Swandana" aria-label="Profil Pengguna">
                    <div class="relative flex items-center justify-center">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-extrabold text-xs flex items-center justify-center shadow-xs ring-2 ring-slate-700 border border-slate-600">
                            GS
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                    </div>
                </a>
            </div>
        </header>
        @endif

        <!-- Toast Notification (Flash feedback) -->
        @if(session('success'))
            <div id="flash-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-11/12 max-w-sm bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-xl border border-slate-700 flex items-center justify-between transition-all duration-300">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </span>
                    <span class="text-xs font-semibold text-slate-100">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flash-toast').remove()" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('flash-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3500);
            </script>
        @endif

        @if($errors->any())
            <div id="error-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-11/12 max-w-sm bg-rose-900 text-white px-4 py-3 rounded-2xl shadow-xl border border-rose-700 flex items-start gap-2.5">
                <span class="w-6 h-6 rounded-full bg-rose-500/20 text-rose-300 flex items-center justify-center shrink-0 mt-0.5">
                    !
                </span>
                <div class="text-xs space-y-0.5">
                    <p class="font-bold">Periksa input Anda:</p>
                    <ul class="list-disc list-inside text-rose-200">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- 2. MAIN CONTENT -->
        @hasSection('custom_header')
            <main class="flex-1 flex flex-col">
                @yield('content')
            </main>
        @else
            <main class="flex-1 pt-3 content-safe">
                @yield('content')
            </main>
        @endif

        <!-- 3. FIXED BOTTOM NAVIGATION BAR -->
        <nav class="fixed bottom-0 left-0 right-0 z-40 flex justify-center pointer-events-none">
            <div class="w-full max-w-md pointer-events-auto bg-white/95 dark:bg-slate-900/95 backdrop-blur-lg border-t border-slate-200/80 dark:border-slate-800/80 px-2 pt-1 bottom-nav-safe shadow-xl transition-colors">
                <div class="flex items-center justify-between">

                    <!-- Tab 1: Beranda -->
                    <a href="{{ route('dashboard') }}"
                       class="flex-1 flex flex-col items-center justify-center py-1.5 gap-0.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}"
                       aria-label="Home">
                        <svg class="w-5 h-5" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('dashboard') ? '0' : '1.8' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span class="text-[9px] font-semibold leading-none">Beranda</span>
                    </a>

                    <!-- Tab 2: Laporan -->
                    <a href="{{ route('reports.index') }}"
                       class="flex-1 flex flex-col items-center justify-center py-1.5 gap-0.5 rounded-xl transition-all {{ request()->routeIs('reports.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}"
                       aria-label="Laporan">
                        <svg class="w-5 h-5" fill="{{ request()->routeIs('reports.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('reports.*') ? '0' : '1.8' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        <span class="text-[9px] font-semibold leading-none">Laporan</span>
                    </a>

                    <!-- Tab 3: Tombol (+) Tambah Transaksi -->
                    <div class="flex-1 flex justify-center relative -mt-4">
                        <button type="button"
                                onclick="openTransactionModal('expense')"
                                class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 active:scale-90 transition-all cursor-pointer ring-4 ring-white dark:ring-slate-950"
                                aria-label="Tambah Transaksi Cepat">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tab 4: Riwayat Transaksi -->
                    <a href="{{ route('transactions.index') }}"
                       class="flex-1 flex flex-col items-center justify-center py-1.5 gap-0.5 rounded-xl transition-all {{ request()->routeIs('transactions.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}"
                       aria-label="Riwayat Transaksi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('transactions.*') ? '2.2' : '1.8' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[9px] font-semibold leading-none">Riwayat</span>
                    </a>

                    <!-- Tab 5: Profil -->
                    <a href="{{ route('profile.edit') }}"
                       class="flex-1 flex flex-col items-center justify-center py-1.5 gap-0.5 rounded-xl transition-all {{ request()->routeIs('profile.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}"
                       aria-label="Profil">
                        <svg class="w-5 h-5" fill="{{ request()->routeIs('profile.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('profile.*') ? '0' : '1.8' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="text-[9px] font-semibold leading-none">Profil</span>
                    </a>

                </div>
            </div>
        </nav>


        <!-- 4. QUICK ADD TRANSACTION BOTTOM SHEET MODAL -->
        @php
            $modalUser = auth()->user() ?? \App\Models\User::first() ?? \App\Models\User::getPrimaryUser();
            $modalWallets = isset($wallets) ? $wallets : ($modalUser ? $modalUser->wallets : collect());
            $modalCategories = isset($categories) ? $categories : ($modalUser ? \App\Models\Category::where(function($q) use ($modalUser) { $q->where('user_id', $modalUser->id)->orWhereNull('user_id'); })->get() : collect());
        @endphp

        <div id="transaction-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
            <!-- Backdrop -->
            <div id="modal-backdrop" onclick="closeTransactionModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>

            <!-- Bottom Sheet Panel -->
            <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
                <div id="modal-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">

                    <!-- Drag Handle -->
                    <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeTransactionModal()"></div>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Catat Transaksi</h2>
                        <button type="button" onclick="closeTransactionModal()" aria-label="Tutup modal" class="min-w-[44px] min-h-[44px] -mr-2 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Quick Receipt Scanner Trigger Banner -->
                    <button type="button" onclick="openReceiptScannerModal()" class="w-full py-2.5 px-3 mb-3 rounded-2xl bg-gradient-to-r from-teal-500/10 via-emerald-500/15 to-teal-500/10 hover:from-teal-500/20 hover:to-emerald-500/20 text-teal-700 dark:text-teal-300 border border-teal-200/70 dark:border-teal-800/70 flex items-center justify-between active:scale-[0.99] transition-all cursor-pointer shadow-2xs group">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-bold text-teal-800 dark:text-teal-200 block leading-tight">📸 Pindai Struk / Bukti Bayar Otomatis</span>
                                <span class="text-[10px] text-teal-600 dark:text-teal-400">Ekstrak otomatis nominal, tanggal & kategori</span>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold text-teal-600 dark:text-teal-400 group-hover:translate-x-0.5 transition-transform">
                            Scan →
                        </span>
                    </button>

                    <!-- Form -->
                    <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Transaction Type Switcher (Pengeluaran vs Pemasukan vs Transfer) -->
                        <div class="grid grid-cols-3 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl gap-1">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="expense" checked class="peer sr-only" onchange="updateModalType('expense')">
                                <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-rose-600 dark:peer-checked:text-rose-400 peer-checked:shadow-xs transition-all">
                                    Pengeluaran
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="income" class="peer sr-only" onchange="updateModalType('income')">
                                <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 peer-checked:shadow-xs transition-all">
                                    Pemasukan
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="transfer" class="peer sr-only" onchange="updateModalType('transfer')">
                                <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-slate-800 dark:peer-checked:text-white peer-checked:shadow-xs transition-all">
                                    Transfer
                                </div>
                            </label>
                        </div>

                        <!-- Nominal Input (Large Display) -->
                        <div>
                            <label for="amount-input" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Nominal (Rp)</label>
                            <div class="relative rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 p-3 transition-all">
                                <span class="text-sm font-bold text-slate-400 mr-1">Rp</span>
                                <input id="amount-input" 
                                       type="number" 
                                       name="amount" 
                                       step="any" 
                                       required 
                                       inputmode="decimal" 
                                       placeholder="0" 
                                       class="w-4/5 text-2xl font-extrabold text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-400 dark:placeholder-slate-600">
                            </div>
                        </div>

                        <!-- Pilihan Dompet (Asal) -->
                        <div>
                            <label id="wallet-label" for="wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Dompet / Rekening</label>
                            <select id="wallet-select" name="wallet_id" required class="w-full min-h-[48px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                @foreach($modalWallets as $w)
                                    <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pilihan Dompet Tujuan (Khusus Transfer) -->
                        <div id="target-wallet-container" class="hidden">
                            <label for="target-wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Dompet Tujuan (Ke)</label>
                            <select id="target-wallet-select" name="target_wallet_id" class="w-full min-h-[48px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                @foreach($modalWallets as $index => $w)
                                    <option value="{{ $w->id }}" {{ $index === 1 ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pilihan Kategori (Untuk Pengeluaran & Pemasukan) -->
                        <div id="category-container">
                            <label for="category-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Kategori</label>
                            <select id="category-select" name="category_id" required class="w-full min-h-[48px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                @foreach($modalCategories as $cat)
                                    <option value="{{ $cat->id }}" data-type="{{ is_string($cat->type) ? $cat->type : $cat->type->value }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        {{ $cat->name }} ({{ is_string($cat->type) ? ucfirst($cat->type) : $cat->type->label() }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal & Keterangan (Grid) -->
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label for="date-input" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Tanggal</label>
                                <input id="date-input" 
                                       type="date" 
                                       name="date" 
                                       value="{{ date('Y-m-d') }}" 
                                       required 
                                       class="w-full min-h-[44px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label for="desc-input" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Catatan (Opsional)</label>
                                <input id="desc-input" 
                                       type="text" 
                                       name="description" 
                                       placeholder="Contoh: Makan siang bareng rekan kerja" 
                                       class="w-full min-h-[44px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <!-- Submit Button (Touch friendly min 48px) -->
                        <div class="pt-2">
                            <button type="submit" class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-sm shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Simpan Transaksi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- 5. EDIT TRANSACTION BOTTOM SHEET MODAL -->
        <div id="edit-transaction-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
            <!-- Backdrop -->
            <div id="edit-modal-backdrop" onclick="closeEditTransactionModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>

            <!-- Bottom Sheet Panel -->
            <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
                <div id="edit-modal-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">

                    <!-- Drag Handle -->
                    <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditTransactionModal()"></div>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Edit Transaksi</h2>
                        <button type="button" onclick="closeEditTransactionModal()" aria-label="Tutup modal" class="min-w-[44px] min-h-[44px] -mr-2 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="edit-transaction-form" action="" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Type Switcher -->
                        <div class="grid grid-cols-3 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl gap-1">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" id="edit-type-expense" value="expense" class="peer sr-only" onchange="updateEditModalType('expense')">
                                <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-rose-600 dark:peer-checked:text-rose-400 peer-checked:shadow-xs transition-all">
                                    Pengeluaran
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" id="edit-type-income" value="income" class="peer sr-only" onchange="updateEditModalType('income')">
                                <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 peer-checked:shadow-xs transition-all">
                                    Pemasukan
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" id="edit-type-transfer" value="transfer" class="peer sr-only" onchange="updateEditModalType('transfer')">
                                <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 peer-checked:bg-white dark:peer-checked:bg-slate-700 peer-checked:text-slate-800 dark:peer-checked:text-white peer-checked:shadow-xs transition-all">
                                    Transfer
                                </div>
                            </label>
                        </div>

                        <!-- Nominal Input -->
                        <div>
                            <label for="edit-amount-input" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Nominal (Rp)</label>
                            <div class="relative rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 p-3 transition-all">
                                <span class="text-sm font-bold text-slate-400 mr-1">Rp</span>
                                <input id="edit-amount-input" 
                                       type="number" 
                                       name="amount" 
                                       step="any" 
                                       required 
                                       inputmode="decimal" 
                                       placeholder="0" 
                                       class="w-4/5 text-2xl font-extrabold text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-400 dark:placeholder-slate-600">
                            </div>
                        </div>

                        <!-- Dompet Asal -->
                        <div>
                            <label id="edit-wallet-label" for="edit-wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Dompet / Rekening</label>
                            <select id="edit-wallet-select" name="wallet_id" required class="w-full min-h-[48px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                @foreach($modalWallets as $w)
                                    <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dompet Tujuan (Khusus Transfer) -->
                        <div id="edit-target-wallet-container" class="hidden">
                            <label for="edit-target-wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Dompet Tujuan (Ke)</label>
                            <select id="edit-target-wallet-select" name="target_wallet_id" class="w-full min-h-[48px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                @foreach($modalWallets as $index => $w)
                                    <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kategori -->
                        <div id="edit-category-container">
                            <label for="edit-category-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Kategori</label>
                            <select id="edit-category-select" name="category_id" required class="w-full min-h-[48px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                @foreach($modalCategories as $cat)
                                    <option value="{{ $cat->id }}" data-type="{{ is_string($cat->type) ? $cat->type : $cat->type->value }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                        {{ $cat->name }} ({{ is_string($cat->type) ? ucfirst($cat->type) : $cat->type->label() }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal & Catatan -->
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label for="edit-date-input" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Tanggal</label>
                                <input id="edit-date-input" 
                                       type="date" 
                                       name="date" 
                                       required 
                                       class="w-full min-h-[44px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label for="edit-desc-input" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1">Catatan (Opsional)</label>
                                <input id="edit-desc-input" 
                                       type="text" 
                                       name="description" 
                                       placeholder="Catatan transaksi..." 
                                       class="w-full min-h-[44px] px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-sm shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Simpan Perubahan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 6. SCAN STRUK & BUKTI PEMBAYARAN BOTTOM SHEET MODAL (AI + OCR) -->
        <style>
            @keyframes scannerLaser {
                0% { top: 4%; opacity: 0.8; }
                50% { top: 92%; opacity: 1; }
                100% { top: 4%; opacity: 0.8; }
            }
            .scanner-laser-bar {
                animation: scannerLaser 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

        <div id="receipt-scanner-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
            <div id="scanner-backdrop" onclick="closeReceiptScannerModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
            <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
                <div id="scanner-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar text-slate-800 dark:text-white">
                    <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer" onclick="closeReceiptScannerModal()"></div>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pindai Struk / Bukti Bayar</h2>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Deteksi otomatis nominal, tanggal & kategori</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeReceiptScannerModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                            ✕
                        </button>
                    </div>

                    <!-- Hidden Inputs for Camera and File Picker -->
                    <input type="file" id="scanner-camera-input" accept="image/*" capture="environment" class="hidden" onchange="processReceiptFile(this)">
                    <input type="file" id="scanner-file-input" accept="image/*" class="hidden" onchange="processReceiptFile(this)">

                    <!-- State 1: Upload / Capture Selection -->
                    <div id="scanner-pick-state" class="space-y-3">
                        <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 text-center bg-slate-50/60 dark:bg-slate-950/40 space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Foto Struk atau Bukti Transfer</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Mendukung struk toko, QRIS, mutasi transfer BCA, Mandiri, BRI, GoPay, OVO, ShopeePay, dll.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <button type="button" onclick="document.getElementById('scanner-camera-input').click()" class="py-3 px-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex flex-col items-center justify-center gap-1.5 active:scale-95 shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                                <span>Buka Kamera</span>
                            </button>
                            <button type="button" onclick="document.getElementById('scanner-file-input').click()" class="py-3 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs flex flex-col items-center justify-center gap-1.5 active:scale-95 border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span>Galeri / Berkas</span>
                            </button>
                        </div>
                    </div>

                    <!-- State 2: Scanning & Processing State (With Laser Beam Animation) -->
                    <div id="scanner-processing-state" class="hidden space-y-3">
                        <div class="relative w-full h-56 rounded-2xl overflow-hidden bg-slate-900 border border-slate-700 flex items-center justify-center">
                            <img id="scanner-preview-img" src="" alt="Struk Preview" class="max-h-full max-w-full object-contain opacity-75">
                            <!-- Laser Scanning Bar -->
                            <div class="scanner-laser-bar absolute left-0 right-0 h-1 bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-400 shadow-[0_0_15px_#10b981]"></div>
                        </div>

                        <div class="text-center p-3 space-y-2 bg-slate-50 dark:bg-slate-950/60 rounded-2xl border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span id="scanner-status-text">Memindai struk pembayaran...</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div id="scanner-progress-fill" class="bg-teal-500 h-full w-1/3 transition-all duration-300"></div>
                            </div>
                            <p class="text-[10px] text-slate-400" id="scanner-substatus-text">Membaca nominal, tanggal, dan nama toko...</p>
                        </div>
                    </div>

                    <!-- State 3: Result Card (Extracted Data Display) -->
                    <div id="scanner-result-state" class="hidden space-y-3">
                        <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/30 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1">
                                    <span>✓ Data Berhasil Diekstrak</span>
                                </span>
                                <span id="scan-source-badge" class="px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 uppercase">
                                    AI Scan
                                </span>
                            </div>

                            <!-- Nominal Display -->
                            <div class="bg-white dark:bg-slate-900 rounded-xl p-3 border border-slate-200/80 dark:border-slate-800">
                                <span class="text-[10px] text-slate-400 font-medium block">Total Nominal Terdeteksi</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-sm font-bold text-slate-400">Rp</span>
                                    <span id="scan-res-amount" class="text-2xl font-black text-slate-900 dark:text-white">0</span>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Toko / Penerima</span>
                                    <span id="scan-res-merchant" class="font-bold text-slate-800 dark:text-slate-200 truncate block mt-0.5">-</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Tanggal</span>
                                    <span id="scan-res-date" class="font-bold text-slate-800 dark:text-slate-200 truncate block mt-0.5">-</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Kategori Disarankan</span>
                                    <span id="scan-res-category" class="font-bold text-teal-600 dark:text-teal-400 truncate block mt-0.5">-</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Jenis Transaksi</span>
                                    <span id="scan-res-type" class="font-bold text-rose-600 dark:text-rose-400 truncate block mt-0.5">Pengeluaran</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" onclick="applyScannedReceiptToForm()" class="flex-1 py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-[0.98] text-white text-xs font-extrabold shadow-md shadow-teal-500/25 flex items-center justify-center gap-2 transition-all cursor-pointer">
                                <span>Terapkan ke Transaksi →</span>
                            </button>
                            <button type="button" onclick="resetReceiptScannerState()" class="py-3 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                                Ulangi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openTransactionModal(defaultType = 'expense', defaultAmount = null, defaultDescription = null, defaultCategory = null, defaultDate = null) {
                const modal = document.getElementById('transaction-modal');
                const backdrop = document.getElementById('modal-backdrop');
                const panel = document.getElementById('modal-panel');

                modal.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    panel.classList.remove('translate-y-full');
                    panel.classList.add('translate-y-0');
                }, 10);

                // Set type
                const radio = document.querySelector(`input[name="type"][value="${defaultType}"]`);
                if (radio) {
                    radio.checked = true;
                    updateModalType(defaultType);
                }

                // Pre-fill amount if provided
                const amountInput = document.getElementById('amount-input');
                if (amountInput) {
                    if (defaultAmount !== null && defaultAmount !== undefined && defaultAmount !== '') {
                        amountInput.value = defaultAmount;
                    } else {
                        amountInput.value = '';
                    }
                }

                // Pre-fill description if provided
                const descInput = document.getElementById('desc-input');
                if (descInput) {
                    if (defaultDescription !== null && defaultDescription !== undefined && defaultDescription !== '') {
                        descInput.value = defaultDescription;
                    } else {
                        descInput.value = '';
                    }
                }

                // Pre-fill date if provided
                if (defaultDate) {
                    const dateInput = document.querySelector('input[name="date"]');
                    if (dateInput) {
                        dateInput.value = defaultDate;
                    }
                }

                // Pre-fill category if provided
                if (defaultCategory) {
                    const categorySelect = document.getElementById('category-select');
                    if (categorySelect) {
                        for (let opt of categorySelect.options) {
                            if (opt.value == defaultCategory || opt.text.toLowerCase().includes(String(defaultCategory).toLowerCase())) {
                                opt.selected = true;
                                break;
                            }
                        }
                    }
                }

                // Focus amount or desc input
                setTimeout(() => {
                    if (defaultAmount !== null && defaultAmount !== undefined && defaultAmount !== '') {
                        document.getElementById('desc-input')?.focus();
                    } else {
                        document.getElementById('amount-input')?.focus();
                    }
                }, 300);
            }

            // Receipt Scanner Handlers (AI Vision + Local OCR)
            let currentScannedReceipt = null;

            function openReceiptScannerModal() {
                closeTransactionModal();

                const modal = document.getElementById('receipt-scanner-modal');
                const backdrop = document.getElementById('scanner-backdrop');
                const panel = document.getElementById('scanner-panel');

                resetReceiptScannerState();

                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    panel.classList.remove('translate-y-full');
                    panel.classList.add('translate-y-0');
                });
            }

            function closeReceiptScannerModal() {
                const modal = document.getElementById('receipt-scanner-modal');
                const backdrop = document.getElementById('scanner-backdrop');
                const panel = document.getElementById('scanner-panel');

                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function resetReceiptScannerState() {
                currentScannedReceipt = null;
                document.getElementById('scanner-pick-state').classList.remove('hidden');
                document.getElementById('scanner-processing-state').classList.add('hidden');
                document.getElementById('scanner-result-state').classList.add('hidden');
                document.getElementById('scanner-camera-input').value = '';
                document.getElementById('scanner-file-input').value = '';
                document.getElementById('scanner-status-text').innerText = 'Memindai struk pembayaran...';
                document.getElementById('scanner-progress-fill').style.width = '20%';
            }

            async function processReceiptFile(input) {
                if (!input.files || !input.files[0]) return;
                const file = input.files[0];

                const pickState = document.getElementById('scanner-pick-state');
                const procState = document.getElementById('scanner-processing-state');
                const resState = document.getElementById('scanner-result-state');
                const previewImg = document.getElementById('scanner-preview-img');
                const statusText = document.getElementById('scanner-status-text');
                const substatusText = document.getElementById('scanner-substatus-text');
                const progressFill = document.getElementById('scanner-progress-fill');

                pickState.classList.add('hidden');
                procState.classList.remove('hidden');
                resState.classList.add('hidden');

                const objectUrl = URL.createObjectURL(file);
                previewImg.src = objectUrl;

                statusText.innerText = 'Mengunggah & Menganalisis Bukti Bayar...';
                substatusText.innerText = 'Mendeteksi nominal, tanggal, dan pedagang...';
                progressFill.style.width = '40%';

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route('transactions.scan-receipt') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success && data.source === 'gemini_ai') {
                        progressFill.style.width = '100%';
                        showScanResults(data);
                        return;
                    }

                    // Fallback to client-side OCR
                    statusText.innerText = 'Membaca Teks dengan OCR Pintar...';
                    substatusText.innerText = 'Mengekstrak karakter dari struk...';
                    progressFill.style.width = '65%';

                    if (typeof Tesseract !== 'undefined') {
                        const ocrResult = await Tesseract.recognize(file, 'ind', {
                            logger: m => {
                                if (m.status === 'recognizing text') {
                                    const pct = Math.round(m.progress * 30) + 65;
                                    progressFill.style.width = pct + '%';
                                }
                            }
                        });

                        const recognizedText = ocrResult.data.text;
                        progressFill.style.width = '95%';

                        const parseRes = await fetch('{{ route('transactions.scan-receipt.parse-text') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ text: recognizedText })
                        });

                        const parsedData = await parseRes.json();
                        parsedData.source = 'ocr_local';
                        showScanResults(parsedData);
                    } else {
                        showScanResults({
                            amount: 0,
                            merchant: file.name.substring(0, file.name.lastIndexOf('.')) || 'Struk Belanja',
                            description: 'Pembayaran Struk',
                            date: new Date().toISOString().split('T')[0],
                            type: 'expense',
                            source: 'manual_assist'
                        });
                    }
                } catch (err) {
                    console.error('Scan error:', err);
                    statusText.innerText = 'Gagal memindai otomatis.';
                    substatusText.innerText = 'Silakan coba lagi atau catat manual.';
                    setTimeout(() => {
                        resetReceiptScannerState();
                    }, 2000);
                }
            }

            function showScanResults(data) {
                currentScannedReceipt = data;

                const procState = document.getElementById('scanner-processing-state');
                const resState = document.getElementById('scanner-result-state');

                procState.classList.add('hidden');
                resState.classList.remove('hidden');

                const formattedAmount = Number(data.amount || 0).toLocaleString('id-ID');
                document.getElementById('scan-res-amount').innerText = formattedAmount;
                document.getElementById('scan-res-merchant').innerText = data.merchant || 'Struk Pembelian';
                document.getElementById('scan-res-date').innerText = data.date || new Date().toISOString().split('T')[0];
                document.getElementById('scan-res-category').innerText = data.category_name || 'Belanja Harian';

                const typeEl = document.getElementById('scan-res-type');
                if (data.type === 'income') {
                    typeEl.innerText = 'Pemasukan';
                    typeEl.className = 'font-bold text-emerald-600 dark:text-emerald-400 truncate block mt-0.5';
                } else {
                    typeEl.innerText = 'Pengeluaran';
                    typeEl.className = 'font-bold text-rose-600 dark:text-rose-400 truncate block mt-0.5';
                }

                const badge = document.getElementById('scan-source-badge');
                if (data.source === 'gemini_ai') {
                    badge.innerText = '✨ AI Vision';
                    badge.className = 'px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 uppercase';
                } else {
                    badge.innerText = '⚡ Smart OCR';
                    badge.className = 'px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 uppercase';
                }
            }

            function applyScannedReceiptToForm() {
                if (!currentScannedReceipt) return;

                const receipt = { ...currentScannedReceipt };
                closeReceiptScannerModal();

                setTimeout(() => {
                    openTransactionModal(
                        receipt.type || 'expense',
                        receipt.amount || '',
                        receipt.description || receipt.merchant || '',
                        receipt.category_id || receipt.category_name || null,
                        receipt.date || null
                    );
                }, 250);
            }

            function closeTransactionModal() {
                const modal = document.getElementById('transaction-modal');
                const backdrop = document.getElementById('modal-backdrop');
                const panel = document.getElementById('modal-panel');

                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function updateModalType(type) {
                const walletLabel = document.getElementById('wallet-label');
                const targetWalletContainer = document.getElementById('target-wallet-container');
                const targetWalletSelect = document.getElementById('target-wallet-select');
                const categoryContainer = document.getElementById('category-container');
                const categorySelect = document.getElementById('category-select');

                if (type === 'transfer') {
                    if (walletLabel) walletLabel.textContent = 'Dompet Asal (Dari)';
                    if (targetWalletContainer) targetWalletContainer.classList.remove('hidden');
                    if (targetWalletSelect) targetWalletSelect.required = true;
                    if (categoryContainer) categoryContainer.classList.add('hidden');
                    if (categorySelect) categorySelect.required = false;
                } else {
                    if (walletLabel) walletLabel.textContent = 'Dompet / Rekening';
                    if (targetWalletContainer) targetWalletContainer.classList.add('hidden');
                    if (targetWalletSelect) targetWalletSelect.required = false;
                    if (categoryContainer) categoryContainer.classList.remove('hidden');
                    if (categorySelect) {
                        categorySelect.required = true;
                        let firstMatched = false;
                        for (let option of categorySelect.options) {
                            const optType = option.getAttribute('data-type');
                            if (!optType || optType === type) {
                                option.hidden = false;
                                option.disabled = false;
                                option.style.display = '';
                                if (!firstMatched) {
                                    option.selected = true;
                                    firstMatched = true;
                                }
                            } else {
                                option.hidden = true;
                                option.disabled = true;
                                option.style.display = 'none';
                                if (option.selected) {
                                    option.selected = false;
                                }
                            }
                        }
                    }
                }
            }

            function updateEditModalType(type) {
                const walletLabel = document.getElementById('edit-wallet-label');
                const targetWalletContainer = document.getElementById('edit-target-wallet-container');
                const targetWalletSelect = document.getElementById('edit-target-wallet-select');
                const categoryContainer = document.getElementById('edit-category-container');
                const categorySelect = document.getElementById('edit-category-select');

                if (type === 'transfer') {
                    if (walletLabel) walletLabel.textContent = 'Dompet Asal (Dari)';
                    if (targetWalletContainer) targetWalletContainer.classList.remove('hidden');
                    if (targetWalletSelect) targetWalletSelect.required = true;
                    if (categoryContainer) categoryContainer.classList.add('hidden');
                    if (categorySelect) categorySelect.required = false;
                } else {
                    if (walletLabel) walletLabel.textContent = 'Dompet / Rekening';
                    if (targetWalletContainer) targetWalletContainer.classList.add('hidden');
                    if (targetWalletSelect) targetWalletSelect.required = false;
                    if (categoryContainer) categoryContainer.classList.remove('hidden');
                    if (categorySelect) {
                        categorySelect.required = true;
                        let firstMatched = false;
                        for (let option of categorySelect.options) {
                            const optType = option.getAttribute('data-type');
                            if (!optType || optType === type) {
                                option.hidden = false;
                                option.disabled = false;
                                option.style.display = '';
                                if (!firstMatched && !option.selected) {
                                    option.selected = true;
                                    firstMatched = true;
                                }
                            } else {
                                option.hidden = true;
                                option.disabled = true;
                                option.style.display = 'none';
                                if (option.selected) {
                                    option.selected = false;
                                }
                            }
                        }
                    }
                }
            }

            function openEditTransactionModal(data) {
                const modal = document.getElementById('edit-transaction-modal');
                const backdrop = document.getElementById('edit-modal-backdrop');
                const panel = document.getElementById('edit-modal-panel');
                const form = document.getElementById('edit-transaction-form');

                if (!modal || !form) return;

                form.action = '/transactions/' + data.id;

                const type = data.type || 'expense';
                const radio = document.querySelector(`input[name="type"][id="edit-type-${type}"]`);
                if (radio) {
                    radio.checked = true;
                }
                updateEditModalType(type);

                const amountInput = document.getElementById('edit-amount-input');
                if (amountInput) amountInput.value = data.amount;

                const walletSelect = document.getElementById('edit-wallet-select');
                if (walletSelect && data.wallet_id) walletSelect.value = data.wallet_id;

                const targetWalletSelect = document.getElementById('edit-target-wallet-select');
                if (targetWalletSelect && data.target_wallet_id) targetWalletSelect.value = data.target_wallet_id;

                const categorySelect = document.getElementById('edit-category-select');
                if (categorySelect && data.category_id) categorySelect.value = data.category_id;

                const dateInput = document.getElementById('edit-date-input');
                if (dateInput && data.date) dateInput.value = data.date;

                const descInput = document.getElementById('edit-desc-input');
                if (descInput) descInput.value = data.description || '';

                modal.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    panel.classList.remove('translate-y-full');
                    panel.classList.add('translate-y-0');
                }, 10);
            }

            function closeEditTransactionModal() {
                const modal = document.getElementById('edit-transaction-modal');
                const backdrop = document.getElementById('edit-modal-backdrop');
                const panel = document.getElementById('edit-modal-panel');

                if (!modal) return;

                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Auto-open modal if URL has ?action=...
            window.addEventListener('DOMContentLoaded', () => {
                const urlParams = new URLSearchParams(window.location.search);
                const action = urlParams.get('action');
                if (action && ['expense', 'income', 'transfer'].includes(action)) {
                    setTimeout(() => openTransactionModal(action), 300);
                }
            });

            // Register PWA Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then((reg) => {
                            console.log('SwanFlow Service Worker registered');
                        })
                        .catch((err) => {
                            console.log('Service Worker registration skipped/failed:', err);
                        });
                });
            }

            // PWA Install Prompt listener
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                const banner = document.getElementById('pwa-install-banner');
                if (banner && !localStorage.getItem('swanflow_pwa_dismissed')) {
                    banner.classList.remove('hidden');
                    setTimeout(() => {
                        banner.classList.remove('translate-y-8', 'opacity-0');
                        banner.classList.add('translate-y-0', 'opacity-100');
                    }, 100);
                }
            });

            function installSwanFlowPWA() {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        dismissPwaBanner();
                        deferredPrompt = null;
                    });
                }
            }

            function dismissPwaBanner() {
                const banner = document.getElementById('pwa-install-banner');
                if (banner) {
                    banner.classList.remove('translate-y-0', 'opacity-100');
                    banner.classList.add('translate-y-8', 'opacity-0');
                    setTimeout(() => banner.classList.add('hidden'), 300);
                }
                localStorage.setItem('swanflow_pwa_dismissed', 'true');
            }

            // SwanFlow Theme Toggle (Dark / Light Mode)
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
        </script>

        <!-- PWA Install Prompt Card (Appears if browser supports installation) -->
        <div id="pwa-install-banner" class="hidden fixed banner-safe left-1/2 -translate-x-1/2 z-40 w-11/12 max-w-sm bg-slate-900/95 backdrop-blur-md text-white p-3.5 rounded-2xl shadow-2xl border border-slate-700/80 transition-all duration-300 transform translate-y-8 opacity-0">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="/icons/icon-192.png" alt="SwanFlow Icon" class="w-10 h-10 rounded-xl shrink-0 shadow-xs border border-emerald-500/30">
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-white truncate leading-tight">Pasang SwanFlow</h4>
                        <p class="text-[10px] text-slate-300 truncate">Tambahkan ke Layar Utama HP</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="installSwanFlowPWA()" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs active:scale-95 transition-all">
                        Pasang
                    </button>
                    <button type="button" onclick="dismissPwaBanner()" aria-label="Tutup" class="p-1.5 text-slate-400 hover:text-white rounded-lg active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </div>
</body>

{{-- Allow child views to inject additional scripts --}}
@stack('scripts')
</html>
