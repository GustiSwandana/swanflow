<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SwanFlow">
    <meta name="color-scheme" content="light dark">
    <meta name="description" content="Personal Financial Tracker Mobile App eksklusif Gusti Swandana">
    <meta name="format-detection" content="telephone=no">

    <!-- Instant Dark Mode Script (Prevents FOUC) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('swanflow_theme');
                if (savedTheme === 'light') {
                    document.documentElement.classList.remove('dark');
                    document.querySelectorAll('meta[name="theme-color"]').forEach(m => {
                        if (m.media?.includes('dark')) m.content = '#ffffff';
                    });
                } else {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- PWA Web App Manifest & App Icons -->
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="alternate icon" type="image/png" href="/icons/icon-192.png">

    <!-- Apple Touch Icons (iPhone Home Screen) -->
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">

    <!-- Apple Splash Screens (iPhone 15 series + older) -->
    <!-- iPhone 15 Pro Max / 15 Plus / 16 Plus (430x932 @3x = 1290x2796) -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/icons/splash/splash-1290x2796.png">
    <!-- iPhone 15 Pro / 15 / 16 (393x852 @3x = 1179x2556) -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/icons/splash/splash-1179x2556.png">
    <!-- iPhone 14 Plus / 13 Pro Max (428x926 @3x = 1284x2778) -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/icons/splash/splash-1284x2778.png">
    <!-- iPhone 14 / 13 / 12 (390x844 @3x = 1170x2532) -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="/icons/splash/splash-1170x2532.png">
    <!-- iPhone SE 3rd gen / 8 (375x667 @2x = 750x1334) -->
    <link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="/icons/splash/splash-750x1334.png">

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
            overflow-y: scroll;
            scrollbar-gutter: stable;
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            min-height: 100%;
            min-height: 100dvh;
            overscroll-behavior-y: none;
            -webkit-overflow-scrolling: touch;
        }

        /* Permanently Locked & Hardware-Accelerated Floating Dock */
        .bottom-nav-dock {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 40;
            display: flex;
            justify-content: center;
            pointer-events: none;
            padding-left: 1rem;
            padding-right: 1rem;
            padding-bottom: max(0.75rem, calc(var(--sab, 0px) + 0.35rem));
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        .bottom-nav-capsule {
            width: 100%;
            max-width: 364px;
            margin-left: auto;
            margin-right: auto;
            pointer-events: auto;
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
            padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.75rem));
            padding-left: max(1rem, calc(var(--sal, 0px) + 1rem));
            padding-right: max(1rem, calc(var(--sar, 0px) + 1rem));
        }
        .bottom-nav-safe {
            padding-bottom: max(0.75rem, calc(var(--sab, 0px) + 0.35rem));
            padding-left: max(0.5rem, calc(var(--sal, 0px) + 0.5rem));
            padding-right: max(0.5rem, calc(var(--sar, 0px) + 0.5rem));
        }
        .content-safe {
            padding-bottom: max(6rem, calc(5.25rem + var(--sab, 0px)));
            padding-left: max(1rem, calc(var(--sal, 0px) + 1rem));
            padding-right: max(1rem, calc(var(--sar, 0px) + 1rem));
        }
        .modal-sheet-safe {
            padding-bottom: max(1.75rem, calc(1.25rem + var(--sab, 0px)));
            max-height: calc(90dvh - var(--sat, 0px));
        }
        .banner-safe {
            bottom: max(5.5rem, calc(5rem + var(--sab, 0px)));
        }

        /* Fluent entrance animation */
        @keyframes swanFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-swan-in {
            animation: swanFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>

<body class="min-h-full min-h-[100dvh] bg-slate-100 dark:bg-slate-950 flex flex-col items-center text-slate-800 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white transition-colors duration-200">
    <!-- Mobile Frame Container (Fluid 100% on iPhones, Max-W-MD for desktop preview) -->
    <div class="w-full max-w-md flex-1 min-h-full min-h-[100dvh] bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 relative flex flex-col shadow-2xl border-x border-slate-200/80 dark:border-slate-800/80 transition-colors duration-200">


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

        @if(session('error'))
            <div id="flash-error-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-11/12 max-w-sm bg-rose-900 text-white px-4 py-3 rounded-2xl shadow-xl border border-rose-700 flex items-center justify-between transition-all duration-300">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-full bg-rose-500/20 text-rose-300 flex items-center justify-center shrink-0 font-bold">
                        ✕
                    </span>
                    <span class="text-xs font-semibold text-rose-100">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flash-error-toast').remove()" class="text-rose-300 hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('flash-error-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 4000);
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
            <main class="flex-1 pt-3 content-safe animate-swan-in">
                @yield('content')
            </main>
        @endif
    </div>
    <!-- End Mobile Frame Container -->

    <!-- 3. FLOATING DOCK BOTTOM NAVIGATION BAR (PERMANENTLY LOCKED POSITION) -->
    <nav class="bottom-nav-dock">
        <div class="bottom-nav-capsule bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border border-slate-200/80 dark:border-white/10 shadow-[0_12px_36px_rgba(0,0,0,0.12),0_2px_8px_rgba(0,0,0,0.04)] dark:shadow-[0_16px_48px_rgba(0,0,0,0.6)] rounded-full px-2 py-1.5 flex items-center justify-between transition-colors duration-200">

            {{-- Tab 1: Beranda --}}
            @php $isActive = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}"
               class="flex-1 h-12 flex flex-col items-center justify-center rounded-full relative active:scale-95 transition-colors duration-150 select-none group {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium' }}"
               aria-label="Beranda">
                @if($isActive)
                    <span class="absolute inset-x-1 inset-y-1 bg-emerald-500/12 dark:bg-emerald-400/15 rounded-full pointer-events-none"></span>
                @endif
                <svg class="relative w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="relative text-[10px] tracking-tight leading-none mt-1">Beranda</span>
            </a>

            {{-- Tab 2: Aktivitas --}}
            @php $isActive = request()->routeIs('todos.*'); @endphp
            <a href="{{ route('todos.index') }}"
               class="flex-1 h-12 flex flex-col items-center justify-center rounded-full relative active:scale-95 transition-colors duration-150 select-none group {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium' }}"
               aria-label="Aktivitas">
                @if($isActive)
                    <span class="absolute inset-x-1 inset-y-1 bg-emerald-500/12 dark:bg-emerald-400/15 rounded-full pointer-events-none"></span>
                @endif
                <svg class="relative w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="3.5" width="16" height="17" rx="3"/>
                    <path d="m8.5 12 2.5 2.5 4.5-5"/>
                    <path d="M9 3.5a1.5 1.5 0 0 1 3-0.5h0a1.5 1.5 0 0 1 3 0.5"/>
                </svg>
                <span class="relative text-[10px] tracking-tight leading-none mt-1">Aktivitas</span>
            </a>

            {{-- Center Action: Quick Add Transaction FAB (+) --}}
            <div class="flex items-center justify-center px-1 shrink-0">
                <button type="button"
                        onclick="openTransactionModal('expense')"
                        class="w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-500 via-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-[0_6px_20px_rgba(16,185,129,0.45)] hover:shadow-[0_8px_24px_rgba(16,185,129,0.6)] active:scale-90 transition-transform duration-150 cursor-pointer border-2 border-white dark:border-slate-800 shrink-0"
                        aria-label="Catat Transaksi Cepat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>
            </div>

            {{-- Tab 3: Riwayat Transaksi --}}
            @php $isActive = request()->routeIs('transactions.*'); @endphp
            <a href="{{ route('transactions.index') }}"
               class="flex-1 h-12 flex flex-col items-center justify-center rounded-full relative active:scale-95 transition-colors duration-150 select-none group {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium' }}"
               aria-label="Riwayat Transaksi">
                @if($isActive)
                    <span class="absolute inset-x-1 inset-y-1 bg-emerald-500/12 dark:bg-emerald-400/15 rounded-full pointer-events-none"></span>
                @endif
                <svg class="relative w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span class="relative text-[10px] tracking-tight leading-none mt-1">Riwayat</span>
            </a>

            {{-- Tab 4: Profil Pengguna --}}
            @php $isActive = request()->routeIs('profile.*'); @endphp
            <a href="{{ route('profile.edit') }}"
               class="flex-1 h-12 flex flex-col items-center justify-center rounded-full relative active:scale-95 transition-colors duration-150 select-none group {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium' }}"
               aria-label="Profil Pengguna">
                @if($isActive)
                    <span class="absolute inset-x-1 inset-y-1 bg-emerald-500/12 dark:bg-emerald-400/15 rounded-full pointer-events-none"></span>
                @endif
                <svg class="relative w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <span class="relative text-[10px] tracking-tight leading-none mt-1">Profil</span>
            </a>

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
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold shrink-0">
                                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div class="text-left min-w-0">
                                <span class="text-xs font-bold text-teal-800 dark:text-teal-200 block leading-tight truncate">📸 Pindai Struk / Bukti Bayar Otomatis</span>
                                <span class="text-[10px] text-teal-600 dark:text-teal-400 block truncate">Ekstrak otomatis nominal, tanggal & kategori</span>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold text-teal-600 dark:text-teal-400 group-hover:translate-x-0.5 transition-transform shrink-0 ml-2">
                            Scan AI ➔
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

                        <!-- Nominal Input (Large Display + Quick Pills) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="amount-input" class="text-xs font-medium text-slate-400 dark:text-slate-400">Nominal (Rp) <span class="text-rose-500">*</span></label>
                                <span id="amount-input-formatted" class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">Rp 0</span>
                            </div>
                            <div class="relative rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 p-3 transition-all">
                                <span class="text-sm font-bold text-slate-400 mr-1">Rp</span>
                                <input id="amount-input" 
                                       type="number" 
                                       name="amount" 
                                       step="any" 
                                       required 
                                       inputmode="decimal" 
                                       placeholder="0" 
                                       oninput="updateInputAmountPreview('amount-input', 'amount-input-formatted')"
                                       class="w-4/5 text-2xl font-extrabold text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-400 dark:placeholder-slate-600">
                            </div>
                            <!-- Quick Nominal Increment Pills -->
                            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1 pt-2 -mx-0.5 px-0.5">
                                <button type="button" onclick="adjustInputAmount('amount-input', 'amount-input-formatted', 10000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+10rb</button>
                                <button type="button" onclick="adjustInputAmount('amount-input', 'amount-input-formatted', 20000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+20rb</button>
                                <button type="button" onclick="adjustInputAmount('amount-input', 'amount-input-formatted', 50000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+50rb</button>
                                <button type="button" onclick="adjustInputAmount('amount-input', 'amount-input-formatted', 100000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+100rb</button>
                                <button type="button" onclick="roundInputAmount('amount-input', 'amount-input-formatted')" class="px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 active:scale-95 transition-all cursor-pointer shrink-0" title="Bulatkan nominal ke ribuan terdekat">Bulatkan</button>
                                <button type="button" onclick="clearInputAmount('amount-input', 'amount-input-formatted')" class="px-2 py-1 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 active:scale-95 transition-all cursor-pointer shrink-0">✕ Reset</button>
                            </div>
                        </div>

                        <!-- Pilihan Dompet (Asal) & Kategori / Dompet Tujuan (Grid 2-Kolom Kompak) -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <!-- Dompet (Asal) -->
                            <div>
                                <label id="wallet-label" for="wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1 truncate">Dompet / Rekening</label>
                                <select id="wallet-select" name="wallet_id" required class="w-full min-h-[44px] px-2.5 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach($modalWallets as $w)
                                        <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilihan Dompet Tujuan (Khusus Transfer) -->
                            <div id="target-wallet-container" class="hidden">
                                <label for="target-wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1 truncate">Dompet Tujuan (Ke)</label>
                                <select id="target-wallet-select" name="target_wallet_id" class="w-full min-h-[44px] px-2.5 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach($modalWallets as $index => $w)
                                        <option value="{{ $w->id }}" {{ $index === 1 ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilihan Kategori (Untuk Pengeluaran & Pemasukan) -->
                            <div id="category-container">
                                <label for="category-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1 truncate">Kategori</label>
                                <select id="category-select" name="category_id" required class="w-full min-h-[44px] px-2.5 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach($modalCategories as $cat)
                                        <option value="{{ $cat->id }}" data-type="{{ is_string($cat->type) ? $cat->type : $cat->type->value }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $cat->name }} ({{ is_string($cat->type) ? ucfirst($cat->type) : $cat->type->label() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tanggal (Full Width) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="date-input" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tanggal</label>
                                <button type="button" onclick="setDatePreset('date-input', 'today')" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">Hari Ini</button>
                            </div>
                            <input id="date-input" 
                                   type="date" 
                                   name="date" 
                                   value="{{ date('Y-m-d') }}" 
                                   required 
                                   class="block w-full max-w-full min-w-0 box-border min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <!-- Catatan (Full Width) -->
                        <div>
                            <label for="desc-input" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Catatan (Opsional)</label>
                            <input id="desc-input" 
                                   type="text" 
                                   name="description" 
                                   placeholder="Contoh: Makan siang" 
                                   class="block w-full max-w-full min-w-0 box-border min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
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

                    <!-- Quick Scan Receipt Button for Edit -->
                    <button type="button" onclick="openReceiptScannerModal('edit')" class="w-full mb-3 py-2.5 px-3 rounded-2xl bg-gradient-to-r from-teal-500/10 via-emerald-500/15 to-teal-500/10 hover:from-teal-500/20 hover:to-emerald-500/20 text-teal-700 dark:text-teal-300 border border-teal-200/70 dark:border-teal-800/70 flex items-center justify-between active:scale-[0.99] transition-all cursor-pointer shadow-2xs group">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold shrink-0">
                                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div class="text-left min-w-0">
                                <span class="text-xs font-bold text-teal-800 dark:text-teal-200 block leading-tight truncate">📸 Pindai Struk untuk Perbarui Data</span>
                                <span class="text-[10px] text-teal-600 dark:text-teal-400 block truncate">Perbarui otomatis nominal, tanggal & kategori</span>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold text-teal-600 dark:text-teal-400 group-hover:translate-x-0.5 transition-transform shrink-0 ml-2">
                            Scan AI ➔
                        </span>
                    </button>

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
                            <div class="flex items-center justify-between mb-1">
                                <label for="edit-amount-input" class="text-xs font-medium text-slate-400 dark:text-slate-400">Nominal (Rp) <span class="text-rose-500">*</span></label>
                                <span id="edit-amount-formatted" class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">Rp 0</span>
                            </div>
                            <div class="relative rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 p-3 transition-all">
                                <span class="text-sm font-bold text-slate-400 mr-1">Rp</span>
                                <input id="edit-amount-input" 
                                       type="number" 
                                       name="amount" 
                                       step="any" 
                                       required 
                                       inputmode="decimal" 
                                       placeholder="0" 
                                       oninput="updateInputAmountPreview('edit-amount-input', 'edit-amount-formatted')"
                                       class="w-4/5 text-2xl font-extrabold text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-400 dark:placeholder-slate-600">
                            </div>
                            <!-- Quick Nominal Increment Pills -->
                            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1 pt-2 -mx-0.5 px-0.5">
                                <button type="button" onclick="adjustInputAmount('edit-amount-input', 'edit-amount-formatted', 10000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+10rb</button>
                                <button type="button" onclick="adjustInputAmount('edit-amount-input', 'edit-amount-formatted', 20000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+20rb</button>
                                <button type="button" onclick="adjustInputAmount('edit-amount-input', 'edit-amount-formatted', 50000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+50rb</button>
                                <button type="button" onclick="adjustInputAmount('edit-amount-input', 'edit-amount-formatted', 100000)" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer shrink-0">+100rb</button>
                                <button type="button" onclick="roundInputAmount('edit-amount-input', 'edit-amount-formatted')" class="px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 active:scale-95 transition-all cursor-pointer shrink-0" title="Bulatkan nominal ke ribuan terdekat">Bulatkan</button>
                                <button type="button" onclick="clearInputAmount('edit-amount-input', 'edit-amount-formatted')" class="px-2 py-1 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 active:scale-95 transition-all cursor-pointer shrink-0">✕ Reset</button>
                            </div>
                        </div>

                        <!-- Dompet Asal & Kategori / Dompet Tujuan (Grid 2-Kolom Kompak) -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <!-- Dompet Asal -->
                            <div>
                                <label id="edit-wallet-label" for="edit-wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1 truncate">Dompet / Rekening</label>
                                <select id="edit-wallet-select" name="wallet_id" required class="w-full min-h-[44px] px-2.5 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach($modalWallets as $w)
                                        <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Dompet Tujuan (Khusus Transfer) -->
                            <div id="edit-target-wallet-container" class="hidden">
                                <label for="edit-target-wallet-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1 truncate">Dompet Tujuan (Ke)</label>
                                <select id="edit-target-wallet-select" name="target_wallet_id" class="w-full min-h-[44px] px-2.5 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach($modalWallets as $index => $w)
                                        <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Kategori -->
                            <div id="edit-category-container">
                                <label for="edit-category-select" class="block text-xs font-medium text-slate-400 dark:text-slate-400 mb-1 truncate">Kategori</label>
                                <select id="edit-category-select" name="category_id" required class="w-full min-h-[44px] px-2.5 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach($modalCategories as $cat)
                                        <option value="{{ $cat->id }}" data-type="{{ is_string($cat->type) ? $cat->type : $cat->type->value }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $cat->name }} ({{ is_string($cat->type) ? ucfirst($cat->type) : $cat->type->label() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tanggal (Full Width) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="edit-date-input" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tanggal</label>
                                <button type="button" onclick="setDatePreset('edit-date-input', 'today')" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">Hari Ini</button>
                            </div>
                            <input id="edit-date-input" 
                                   type="date" 
                                   name="date" 
                                   required 
                                   class="block w-full max-w-full min-w-0 box-border min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <!-- Catatan (Full Width) -->
                        <div>
                            <label for="edit-desc-input" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Catatan (Opsional)</label>
                            <input id="edit-desc-input" 
                                   type="text" 
                                   name="description" 
                                   placeholder="Catatan transaksi..." 
                                   class="block w-full max-w-full min-w-0 box-border min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-2 space-y-2">
                            <div id="edit-initial-buttons" class="space-y-2">
                                <button type="submit" id="edit-transaction-submit-btn" class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-sm shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Simpan Perubahan Transaksi
                                </button>

                                <button type="button" 
                                        onclick="showEditModalDeleteConfirm()" 
                                        class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-200 dark:border-rose-900/60 active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Hapus Transaksi Ini
                                </button>
                            </div>

                            <!-- Inline Delete Confirmation Box inside Edit Modal -->
                            <div id="edit-delete-confirm-box" class="hidden p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-center space-y-2.5">
                                <div class="w-10 h-10 mx-auto rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-rose-800 dark:text-rose-200">Hapus transaksi ini secara permanen?</p>
                                    <p class="text-[11px] text-rose-600/90 dark:text-rose-400/90 mt-0.5">Saldo dompet akan otomatis dikembalikan seperti semula.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="button" 
                                            onclick="hideEditModalDeleteConfirm()" 
                                            class="min-h-[40px] py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs border border-slate-200 dark:border-slate-700 active:scale-95 transition-all cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="button" 
                                            id="edit-modal-delete-confirm-btn"
                                            onclick="executeDeleteTransaction()" 
                                            class="min-h-[40px] py-2 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Ya, Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hidden Global Delete Transaction Form -->
        <form id="global-delete-transaction-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <!-- 5.b DELETE TRANSACTION CONFIRMATION BOTTOM SHEET MODAL -->
        <div id="delete-transaction-confirm-modal" class="fixed inset-0 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
            <!-- Backdrop -->
            <div id="delete-modal-backdrop" onclick="closeDeleteTransactionModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0" style="z-index: 9998;"></div>

            <!-- Bottom Sheet Panel -->
            <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none" style="z-index: 9999;">
                <div id="delete-modal-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300">
                    <!-- Drag Handle -->
                    <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeDeleteTransactionModal()"></div>

                    <div class="text-center space-y-3 pb-2">
                        <div class="w-14 h-14 mx-auto rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Hapus Transaksi?</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Transaksi ini akan dihapus permanen. Saldo dompet terkait akan otomatis disesuaikan kembali seperti semula.
                            </p>
                        </div>

                        <!-- Transaction Preview Card inside Modal -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 text-left space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 dark:text-slate-400">Nominal:</span>
                                <span id="delete-preview-amount" class="font-bold text-slate-900 dark:text-white">Rp 0</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 dark:text-slate-400">Tanggal:</span>
                                <span id="delete-preview-date" class="font-medium text-slate-700 dark:text-slate-300">-</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 dark:text-slate-400">Keterangan:</span>
                                <span id="delete-preview-desc" class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[200px]">-</span>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="grid grid-cols-2 gap-2.5 pt-2">
                            <button type="button" 
                                    onclick="closeDeleteTransactionModal()" 
                                    class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs active:scale-98 transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    id="confirm-delete-submit-btn"
                                    onclick="executeDeleteTransaction()" 
                                    class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/30 active:scale-98 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
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
                <div id="scanner-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-4 pt-3 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-3 max-h-[92vh] overflow-y-auto no-scrollbar text-slate-800 dark:text-white">
                    <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-1 cursor-pointer" onclick="closeReceiptScannerModal()"></div>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/30 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold shadow-2xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Pindai Struk & Bukti Bayar</h2>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Deteksi otomatis nominal, tanggal & kategori</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeReceiptScannerModal()" aria-label="Tutup modal pindai" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Hidden Inputs for Camera and File Picker -->
                    <input type="file" id="scanner-camera-input" accept="image/*" capture="environment" class="hidden" onchange="processReceiptFile(this)">
                    <input type="file" id="scanner-file-input" accept="image/*" class="hidden" onchange="processReceiptFile(this)">

                    <!-- State 1: Upload / Capture Selection -->
                    <div id="scanner-pick-state" class="space-y-3">
                        <!-- Guidance Info Card -->
                        <div class="p-3.5 rounded-2xl bg-gradient-to-br from-emerald-500/10 via-teal-500/10 to-emerald-500/5 dark:from-emerald-950/40 dark:via-teal-950/30 dark:to-emerald-950/20 border border-emerald-500/20 dark:border-emerald-800/40 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Pindai Struk & Bukti Bayar Otomatis</h4>
                                <p class="text-[11px] text-emerald-700/90 dark:text-emerald-300/80 mt-0.5 leading-relaxed">
                                    Foto struk belanja atau unggah tangkapan layar m-Banking. AI akan mendeteksi total belanja, tanggal, dan nama toko dalam hitungan detik.
                                </p>
                            </div>
                        </div>

                        <!-- Dual Interactive Action Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Option 1: Ambil Foto Kamera -->
                            <button type="button" 
                                    onclick="document.getElementById('scanner-camera-input').click()" 
                                    class="p-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-3 active:scale-[0.98] shadow-md shadow-emerald-600/25 transition-all text-left cursor-pointer group">
                                <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-black block text-white leading-tight">Buka Kamera</span>
                                    <span class="text-[10px] text-emerald-100 block mt-0.5">Foto fisik struk langsung</span>
                                </div>
                            </button>

                            <!-- Option 2: Unggah dari Galeri -->
                            <button type="button" 
                                    id="scanner-dropzone"
                                    onclick="document.getElementById('scanner-file-input').click()" 
                                    class="p-3.5 rounded-2xl bg-white dark:bg-slate-800/90 border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-400 text-slate-800 dark:text-slate-100 flex items-center gap-3 active:scale-[0.98] transition-all text-left cursor-pointer group">
                                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-black block text-slate-900 dark:text-white leading-tight">Galeri / File</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5">Pilih screenshot transfer</span>
                                </div>
                            </button>
                        </div>

                        <!-- Supported Brands Pill Bar -->
                        <div class="pt-1 text-center">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Mendukung: QRIS · Struk Kasir · BCA · Mandiri · BRI · GoPay · OVO · ShopeePay · DANA
                            </p>
                        </div>
                    </div>

                    <!-- State 2: Scanning & Processing State (With Laser Beam Animation) -->
                    <div id="scanner-processing-state" class="hidden space-y-3">
                        <div class="relative w-full h-44 rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 flex items-center justify-center shadow-inner">
                            <img id="scanner-preview-img" src="" alt="Struk Preview" class="max-h-full max-w-full object-contain opacity-80">
                            <!-- Laser Scanning Bar -->
                            <div class="scanner-laser-bar absolute left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-400 shadow-[0_0_15px_#10b981]"></div>
                        </div>

                        <div class="text-center p-3 space-y-2 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                            <div class="flex items-center justify-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span id="scanner-status-text">Memindai struk pembayaran...</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div id="scanner-progress-fill" class="bg-gradient-to-r from-teal-500 to-emerald-500 h-full w-1/3 transition-all duration-300"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-400" id="scanner-substatus-text">Membaca nominal, tanggal, dan nama toko...</p>
                        </div>

                        <button type="button" onclick="resetReceiptScannerState()" class="w-full py-2 text-xs font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 active:scale-95 transition-all cursor-pointer">
                            Batal Pindai
                        </button>
                    </div>

                    <!-- State 3: Result Card (Interactive Review & Edit Mode) -->
                    <div id="scanner-result-state" class="hidden space-y-2.5">
                        <!-- Top Status & Actions Banner -->
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800/60">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">Rincian Terdeteksi</span>
                                <span id="scan-source-badge" class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200 dark:border-slate-700 shrink-0">
                                    ⚡ Smart OCR
                                </span>
                            </div>
                            <button type="button" onclick="toggleScanReceiptImagePreview()" class="py-1 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-1 active:scale-95 transition-all cursor-pointer border border-slate-200 dark:border-slate-700 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span id="scan-preview-btn-text">Lihat Foto</span>
                            </button>
                        </div>

                        <!-- Collapsible Receipt Photo Preview -->
                        <div id="scanner-receipt-photo-container" class="hidden rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 max-h-40 flex items-center justify-center relative transition-all shadow-inner">
                            <img id="scanner-result-photo-img" src="" alt="Foto Struk" class="max-h-40 max-w-full object-contain">
                            <span class="absolute bottom-1 right-2 text-[9px] bg-black/70 text-white/90 px-2 py-0.5 rounded-md">Ketuk lagi untuk menutup</span>
                        </div>

                        <!-- Editable Total Nominal Card -->
                        <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 p-2.5 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="scan-edit-amount" class="text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-400 uppercase">Total Nominal (Rp) <span class="text-rose-500">*</span></label>
                                <span id="scan-amount-formatted" class="text-xs font-black text-emerald-600 dark:text-emerald-400">Rp 0</span>
                            </div>
                            <div class="relative flex items-center bg-white dark:bg-slate-900/90 rounded-xl px-3 py-1 border border-slate-200/80 dark:border-slate-700 focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20">
                                <span class="text-sm font-extrabold text-slate-400 dark:text-slate-500 mr-1.5 shrink-0">Rp</span>
                                <input id="scan-edit-amount" 
                                       type="number" 
                                       step="any" 
                                       inputmode="decimal" 
                                       placeholder="0" 
                                       required 
                                       oninput="updateInputAmountPreview('scan-edit-amount', 'scan-amount-formatted')"
                                       class="w-full text-xl font-black text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-300 dark:placeholder-slate-600 p-0">
                            </div>
                            <!-- Balanced 4-chip grid: Fits comfortably without cutting off -->
                            <div class="grid grid-cols-4 gap-1 pt-0.5">
                                <button type="button" onclick="adjustInputAmount('scan-edit-amount', 'scan-amount-formatted', 10000)" class="h-6 rounded-md text-[10px] font-bold bg-white dark:bg-slate-700/70 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/80 dark:border-slate-600 active:scale-95 transition-all cursor-pointer flex items-center justify-center">+10rb</button>
                                <button type="button" onclick="adjustInputAmount('scan-edit-amount', 'scan-amount-formatted', 50000)" class="h-6 rounded-md text-[10px] font-bold bg-white dark:bg-slate-700/70 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/80 dark:border-slate-600 active:scale-95 transition-all cursor-pointer flex items-center justify-center">+50rb</button>
                                <button type="button" onclick="adjustInputAmount('scan-edit-amount', 'scan-amount-formatted', 100000)" class="h-6 rounded-md text-[10px] font-bold bg-white dark:bg-slate-700/70 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/80 dark:border-slate-600 active:scale-95 transition-all cursor-pointer flex items-center justify-center">+100rb</button>
                                <button type="button" onclick="roundInputAmount('scan-edit-amount', 'scan-amount-formatted')" class="h-6 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800 active:scale-95 transition-all cursor-pointer flex items-center justify-center" title="Bulatkan nominal">Bulatkan</button>
                            </div>
                        </div>

                        <!-- Jenis Transaksi (Full-width Segmented Bar) -->
                        <div>
                            <div class="grid grid-cols-2 p-0.5 bg-slate-100 dark:bg-slate-800/90 rounded-xl gap-0.5 h-8.5 items-center border border-slate-200/60 dark:border-slate-700/60">
                                <button type="button" id="scan-type-btn-expense" onclick="setScanResultType('expense')" class="h-7.5 flex items-center justify-center text-xs font-bold rounded-lg transition-all bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs cursor-pointer">
                                    Pengeluaran
                                </button>
                                <button type="button" id="scan-type-btn-income" onclick="setScanResultType('income')" class="h-7.5 flex items-center justify-center text-xs font-bold rounded-lg transition-all text-slate-500 dark:text-slate-400 cursor-pointer">
                                    Pemasukan
                                </button>
                            </div>
                        </div>

                        <!-- Toko / Penerima (Full Width) -->
                        <div>
                            <label for="scan-edit-merchant" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-0.5">Toko / Penerima</label>
                            <input id="scan-edit-merchant" 
                                   type="text" 
                                   placeholder="Nama Toko atau Penerima (e.g. Bank, Supermarket)..." 
                                   class="w-full h-9 px-3 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500/20">
                        </div>

                        <!-- Tanggal (Full Width) -->
                        <div>
                            <label for="scan-edit-date" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-0.5">Tanggal</label>
                            <input id="scan-edit-date" 
                                   type="date" 
                                   class="block w-full max-w-full min-w-0 box-border h-9 px-3 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500/20">
                        </div>

                        <!-- Kategori & Dompet Grid (2 Columns) -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="scan-edit-category" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-0.5 truncate">Kategori</label>
                                <select id="scan-edit-category" class="w-full h-9 px-2.5 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500/20">
                                    @foreach($modalCategories as $cat)
                                        <option value="{{ $cat->id }}" data-type="{{ is_string($cat->type) ? $cat->type : $cat->type->value }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="scan-edit-wallet" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-0.5 truncate">Bayar Pakai (Dompet)</label>
                                <select id="scan-edit-wallet" class="w-full h-9 px-2.5 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 truncate focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500/20">
                                    @foreach($modalWallets as $w)
                                        <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                            {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Catatan Tambahan (Full Width) -->
                        <div>
                            <label for="scan-edit-notes" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-0.5">Catatan Tambahan (Opsional)</label>
                            <input id="scan-edit-notes" 
                                   type="text" 
                                   placeholder="Rincian barang, nomor referensi, dsb..." 
                                   class="w-full h-9 px-3 py-1 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500/20">
                        </div>

                        <!-- Bottom Action Buttons: Guaranteed Visible & Proportionally Balanced -->
                        <div class="pt-1 flex items-center gap-2">
                            <button type="button" onclick="applyScannedReceiptToForm()" class="flex-1 min-h-[44px] py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 active:scale-98 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                <span>Buka di Form</span>
                            </button>
                            <button type="button" id="scan-quick-save-btn" onclick="quickSaveScannedReceipt()" class="flex-[1.8] min-h-[44px] py-2 px-3 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <span>Simpan Transaksi</span>
                            </button>
                            <button type="button" onclick="resetReceiptScannerState()" class="w-11 min-h-[44px] rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 active:scale-95 transition-all flex items-center justify-center cursor-pointer border border-slate-200 dark:border-slate-700 shrink-0" title="Pindai Ulang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // SwanFlow UI/UX Helper Functions
            function formatRupiahDisplay(val) {
                const num = Number(val || 0);
                return 'Rp ' + Math.max(0, Math.round(num)).toLocaleString('id-ID');
            }

            function updateInputAmountPreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const prev = document.getElementById(previewId);
                if (input && prev) {
                    prev.innerText = formatRupiahDisplay(input.value);
                }
            }

            function adjustInputAmount(inputId, previewId, delta) {
                const el = document.getElementById(inputId);
                if (!el) return;
                const currentVal = Number(el.value || 0);
                const newVal = Math.max(0, currentVal + delta);
                el.value = newVal > 0 ? newVal : '';
                const prev = document.getElementById(previewId);
                if (prev) prev.innerText = formatRupiahDisplay(newVal);
                if (navigator.vibrate) {
                    try { navigator.vibrate(10); } catch(e) {}
                }
            }

            function roundInputAmount(inputId, previewId) {
                const el = document.getElementById(inputId);
                if (!el) return;
                const currentVal = Number(el.value || 0);
                if (currentVal <= 0) return;
                const rounded = Math.round(currentVal / 1000) * 1000;
                el.value = rounded;
                const prev = document.getElementById(previewId);
                if (prev) prev.innerText = formatRupiahDisplay(rounded);
                if (navigator.vibrate) {
                    try { navigator.vibrate(10); } catch(e) {}
                }
            }

            function clearInputAmount(inputId, previewId) {
                const el = document.getElementById(inputId);
                if (!el) return;
                el.value = '';
                const prev = document.getElementById(previewId);
                if (prev) prev.innerText = 'Rp 0';
                el.focus();
            }

            function setDatePreset(inputId, preset) {
                const el = document.getElementById(inputId);
                if (!el) return;
                const d = new Date();
                if (preset === 'yesterday') {
                    d.setDate(d.getDate() - 1);
                }
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                el.value = `${yyyy}-${mm}-${dd}`;
                if (navigator.vibrate) {
                    try { navigator.vibrate(10); } catch(e) {}
                }
            }

            function showSwanToast(message, type = 'success') {
                const existing = document.getElementById('swan-toast-container');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.id = 'swan-toast-container';
                toast.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-50 w-11/12 max-w-sm px-4 py-3 rounded-2xl shadow-2xl border flex items-center justify-between transition-all duration-300 transform -translate-y-4 opacity-0 pointer-events-auto ' +
                    (type === 'error' 
                        ? 'bg-rose-900/95 text-white border-rose-700 shadow-rose-950/40 backdrop-blur-md' 
                        : 'bg-slate-900/95 dark:bg-slate-800/95 text-white border-slate-700/80 shadow-slate-950/40 backdrop-blur-md');

                const iconHtml = type === 'error'
                    ? '<span class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center shrink-0 font-bold">!</span>'
                    : '<span class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg></span>';

                toast.innerHTML = `
                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                        ${iconHtml}
                        <span class="text-xs font-bold text-slate-100 truncate">${message}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white p-1 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                `;

                document.body.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.classList.remove('-translate-y-4', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                });

                setTimeout(() => {
                    if (toast && toast.parentElement) {
                        toast.classList.remove('translate-y-0', 'opacity-100');
                        toast.classList.add('-translate-y-4', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3200);
            }

            function openTransactionModal(defaultType = 'expense', defaultAmount = null, defaultDescription = null, defaultCategory = null, defaultDate = null, defaultWallet = null) {
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
                    updateInputAmountPreview('amount-input', 'amount-input-formatted');
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

                // Pre-fill wallet if provided
                if (defaultWallet) {
                    const walletSelect = document.getElementById('wallet-select');
                    if (walletSelect) {
                        walletSelect.value = defaultWallet;
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

            // Receipt Scanner State & Handlers
            let currentScannedReceipt = null;
            let receiptScannerTarget = 'new'; // 'new' | 'edit'
            let currentScanType = 'expense';

            function openReceiptScannerModal(target = 'new') {
                receiptScannerTarget = target;

                if (target === 'edit') {
                    const editModal = document.getElementById('edit-transaction-modal');
                    if (editModal) editModal.classList.add('hidden');
                } else {
                    closeTransactionModal();
                }

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
                document.getElementById('scanner-substatus-text').innerText = 'Mempersiapkan gambar untuk analisis...';
                document.getElementById('scanner-progress-fill').style.width = '20%';
                document.getElementById('scanner-receipt-photo-container').classList.add('hidden');
                document.getElementById('scan-preview-btn-text').innerText = 'Lihat Foto';
            }

            function toggleScanReceiptImagePreview() {
                const container = document.getElementById('scanner-receipt-photo-container');
                const btnText = document.getElementById('scan-preview-btn-text');
                if (container.classList.contains('hidden')) {
                    container.classList.remove('hidden');
                    btnText.innerText = 'Tutup Foto';
                } else {
                    container.classList.add('hidden');
                    btnText.innerText = 'Lihat Foto';
                }
            }

            function updateScanAmountPreview(val) {
                updateInputAmountPreview('scan-edit-amount', 'scan-amount-formatted');
            }

            function setScanResultType(type) {
                currentScanType = type;
                const expenseBtn = document.getElementById('scan-type-btn-expense');
                const incomeBtn = document.getElementById('scan-type-btn-income');

                if (type === 'income') {
                    incomeBtn.className = 'py-1.5 flex items-center justify-center text-xs font-bold rounded-md transition-all bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs cursor-pointer';
                    expenseBtn.className = 'py-1.5 flex items-center justify-center text-xs font-bold rounded-md transition-all text-slate-500 dark:text-slate-400 cursor-pointer';
                } else {
                    expenseBtn.className = 'py-1.5 flex items-center justify-center text-xs font-bold rounded-md transition-all bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs cursor-pointer';
                    incomeBtn.className = 'py-1.5 flex items-center justify-center text-xs font-bold rounded-md transition-all text-slate-500 dark:text-slate-400 cursor-pointer';
                }

                // Filter categories in dropdown
                const catSelect = document.getElementById('scan-edit-category');
                if (catSelect) {
                    let firstMatched = false;
                    for (let opt of catSelect.options) {
                        const optType = opt.getAttribute('data-type');
                        if (!optType || optType === type) {
                            opt.hidden = false;
                            opt.disabled = false;
                            opt.style.display = '';
                            if (!firstMatched && !opt.selected) {
                                opt.selected = true;
                                firstMatched = true;
                            }
                        } else {
                            opt.hidden = true;
                            opt.disabled = true;
                            opt.style.display = 'none';
                            if (opt.selected) opt.selected = false;
                        }
                    }
                }
            }

            // Client-side Canvas Image Compression
            function compressReceiptImage(file, maxDim = 1600, quality = 0.82) {
                return new Promise((resolve) => {
                    if (!file || !file.type || !file.type.startsWith('image/')) {
                        return resolve({ file, dataUrl: null });
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            let w = img.width;
                            let h = img.height;
                            if (w > maxDim || h > maxDim) {
                                if (w > h) {
                                    h = Math.round((h * maxDim) / w);
                                    w = maxDim;
                                } else {
                                    w = Math.round((w * maxDim) / h);
                                    h = maxDim;
                                }
                            }
                            const canvas = document.createElement('canvas');
                            canvas.width = w;
                            canvas.height = h;
                            const ctx = canvas.getContext('2d');
                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            ctx.drawImage(img, 0, 0, w, h);
                            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

                            canvas.toBlob((blob) => {
                                if (blob) {
                                    const compressedFile = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                    resolve({ file: compressedFile, dataUrl });
                                } else {
                                    resolve({ file, dataUrl: e.target.result });
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = () => resolve({ file, dataUrl: e.target.result });
                        img.src = e.target.result;
                    };
                    reader.onerror = () => resolve({ file, dataUrl: null });
                    reader.readAsDataURL(file);
                });
            }

            async function processReceiptFile(input) {
                if (!input.files || !input.files[0]) return;
                const rawFile = input.files[0];

                const pickState = document.getElementById('scanner-pick-state');
                const procState = document.getElementById('scanner-processing-state');
                const resState = document.getElementById('scanner-result-state');
                const previewImg = document.getElementById('scanner-preview-img');
                const resultPhotoImg = document.getElementById('scanner-result-photo-img');
                const statusText = document.getElementById('scanner-status-text');
                const substatusText = document.getElementById('scanner-substatus-text');
                const progressFill = document.getElementById('scanner-progress-fill');

                pickState.classList.add('hidden');
                procState.classList.remove('hidden');
                resState.classList.add('hidden');

                statusText.innerText = 'Mengoptimalkan Resolusi Gambar...';
                substatusText.innerText = 'Menyesuaikan ketajaman struk...';
                progressFill.style.width = '25%';

                const { file, dataUrl } = await compressReceiptImage(rawFile, 1600, 0.82);
                if (dataUrl) {
                    previewImg.src = dataUrl;
                    resultPhotoImg.src = dataUrl;
                } else {
                    const objectUrl = URL.createObjectURL(file);
                    previewImg.src = objectUrl;
                    resultPhotoImg.src = objectUrl;
                }

                statusText.innerText = 'Menganalisis Bukti Pembayaran...';
                substatusText.innerText = 'Mendeteksi total belanja, tanggal & pedagang...';
                progressFill.style.width = '45%';

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
                    statusText.innerText = 'Membaca Teks Struk (OCR Pintar)...';
                    substatusText.innerText = 'Mengekstrak karakter teks dari gambar...';
                    progressFill.style.width = '60%';

                    if (typeof Tesseract !== 'undefined') {
                        let ocrResult = null;
                        try {
                            ocrResult = await Tesseract.recognize(file, 'ind+eng', {
                                logger: m => {
                                    if (m.status === 'recognizing text') {
                                        const pct = Math.round(m.progress * 30) + 60;
                                        progressFill.style.width = pct + '%';
                                    }
                                }
                            });
                        } catch (e1) {
                            console.warn('ind+eng failed, trying eng:', e1);
                            ocrResult = await Tesseract.recognize(file, 'eng', {
                                logger: m => {
                                    if (m.status === 'recognizing text') {
                                        const pct = Math.round(m.progress * 30) + 60;
                                        progressFill.style.width = pct + '%';
                                    }
                                }
                            });
                        }

                        const recognizedText = ocrResult?.data?.text || '';
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
                    substatusText.innerText = 'Silakan coba lagi atau isi rincian secara manual.';
                    setTimeout(() => {
                        resetReceiptScannerState();
                    }, 2200);
                }
            }

            function showScanResults(data) {
                currentScannedReceipt = data;

                const procState = document.getElementById('scanner-processing-state');
                const resState = document.getElementById('scanner-result-state');

                procState.classList.add('hidden');
                resState.classList.remove('hidden');

                // Populate Editable Inputs
                const amountInput = document.getElementById('scan-edit-amount');
                amountInput.value = data.amount || '';
                updateScanAmountPreview(data.amount || 0);

                const merchantInput = document.getElementById('scan-edit-merchant');
                merchantInput.value = data.merchant || '';

                const dateInput = document.getElementById('scan-edit-date');
                dateInput.value = data.date || new Date().toISOString().split('T')[0];

                const notesInput = document.getElementById('scan-edit-notes');
                notesInput.value = data.notes || (data.items && data.items.length ? data.items.join(', ') : '');

                // Set Type
                setScanResultType(data.type === 'income' ? 'income' : 'expense');

                // Select Category
                const catSelect = document.getElementById('scan-edit-category');
                if (catSelect && data.category_id) {
                    catSelect.value = data.category_id;
                } else if (catSelect && data.category_name) {
                    for (let opt of catSelect.options) {
                        if (opt.text.toLowerCase().includes(data.category_name.toLowerCase())) {
                            opt.selected = true;
                            break;
                        }
                    }
                }

                // Source Badge
                const badge = document.getElementById('scan-source-badge');
                if (badge) {
                    if (data.source === 'gemini_ai') {
                        badge.innerText = '✨ AI Vision';
                        badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 uppercase border border-emerald-200/60 dark:border-emerald-800/60';
                    } else {
                        badge.innerText = '⚡ Smart OCR';
                        badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 uppercase border border-slate-200 dark:border-slate-700';
                    }
                }
            }

            function applyScannedReceiptToForm() {
                const amount = document.getElementById('scan-edit-amount').value;
                const merchant = document.getElementById('scan-edit-merchant').value;
                const date = document.getElementById('scan-edit-date').value;
                const categoryId = document.getElementById('scan-edit-category').value;
                const walletId = document.getElementById('scan-edit-wallet').value;
                const notes = document.getElementById('scan-edit-notes').value;
                const type = currentScanType;

                const finalDesc = notes ? `${merchant} (${notes})` : merchant;

                closeReceiptScannerModal();

                setTimeout(() => {
                    if (receiptScannerTarget === 'edit') {
                        // Apply directly to Edit Modal
                        const editAmount = document.getElementById('edit-amount-input');
                        if (editAmount) editAmount.value = amount;
                        const editDesc = document.getElementById('edit-desc-input');
                        if (editDesc) editDesc.value = finalDesc;
                        const editDate = document.getElementById('edit-date-input');
                        if (editDate) editDate.value = date;
                        const editCat = document.getElementById('edit-category-select');
                        if (editCat && categoryId) editCat.value = categoryId;
                        const editWallet = document.getElementById('edit-wallet-select');
                        if (editWallet && walletId) editWallet.value = walletId;

                        const radio = document.querySelector(`input[name="type"][id="edit-type-${type}"]`);
                        if (radio) {
                            radio.checked = true;
                            updateEditModalType(type);
                        }

                        // Re-open edit modal
                        const modal = document.getElementById('edit-transaction-modal');
                        const backdrop = document.getElementById('edit-modal-backdrop');
                        const panel = document.getElementById('edit-modal-panel');
                        if (modal) {
                            modal.classList.remove('hidden');
                            setTimeout(() => {
                                backdrop?.classList.remove('opacity-0');
                                backdrop?.classList.add('opacity-100');
                                panel?.classList.remove('translate-y-full');
                                panel?.classList.add('translate-y-0');
                            }, 10);
                        }
                    } else {
                        openTransactionModal(type, amount, finalDesc, categoryId, date, walletId);
                    }
                }, 250);
            }

            async function quickSaveScannedReceipt() {
                const amount = document.getElementById('scan-edit-amount').value;
                const merchant = document.getElementById('scan-edit-merchant').value;
                const date = document.getElementById('scan-edit-date').value;
                const categoryId = document.getElementById('scan-edit-category').value;
                const walletId = document.getElementById('scan-edit-wallet').value;
                const notes = document.getElementById('scan-edit-notes').value;
                const type = currentScanType;

                if (!amount || Number(amount) <= 0) {
                    showSwanToast('Harap isi nominal transaksi.', 'error');
                    document.getElementById('scan-edit-amount').focus();
                    return;
                }
                if (!walletId) {
                    showSwanToast('Harap pilih dompet pembayaran.', 'error');
                    return;
                }

                const saveBtn = document.getElementById('scan-quick-save-btn');
                const originalHtml = saveBtn.innerHTML;
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...';

                const finalDesc = notes ? `${merchant} (${notes})` : merchant;

                const formData = new FormData();
                formData.append('type', type);
                formData.append('amount', amount);
                formData.append('description', finalDesc);
                formData.append('category_id', categoryId);
                formData.append('wallet_id', walletId);
                formData.append('date', date);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const res = await fetch('{{ route('transactions.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (res.ok) {
                        showSwanToast('Transaksi berhasil dicatat!');
                        closeReceiptScannerModal();
                        setTimeout(() => {
                            window.location.reload();
                        }, 600);
                    } else {
                        const errData = await res.json().catch(() => ({}));
                        showSwanToast(errData.message || 'Gagal menyimpan transaksi. Periksa kembali data transaksi.', 'error');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalHtml;
                    }
                } catch (err) {
                    console.error('Save error:', err);
                    showSwanToast('Terjadi kesalahan jaringan.', 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalHtml;
                }
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

            let currentActiveTransaction = null;

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
                    if (categorySelect) {
                        categorySelect.required = false;
                        categorySelect.value = '';
                    }
                } else {
                    if (walletLabel) walletLabel.textContent = 'Dompet / Rekening';
                    if (targetWalletContainer) targetWalletContainer.classList.add('hidden');
                    if (targetWalletSelect) {
                        targetWalletSelect.required = false;
                        targetWalletSelect.value = '';
                    }
                    if (categoryContainer) categoryContainer.classList.remove('hidden');
                    if (categorySelect) {
                        categorySelect.required = true;
                        let firstValidOption = null;
                        for (let option of categorySelect.options) {
                            const optType = option.getAttribute('data-type');
                            if (!optType || optType === type) {
                                option.hidden = false;
                                option.disabled = false;
                                option.style.display = '';
                                if (!firstValidOption) {
                                    firstValidOption = option;
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
                        if (categorySelect.selectedOptions.length === 0 || categorySelect.selectedOptions[0].disabled) {
                            if (firstValidOption) {
                                firstValidOption.selected = true;
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

                currentActiveTransaction = data;
                const baseTxUrl = "{{ url('/transactions') }}";
                form.action = baseTxUrl + '/' + data.id;

                const type = data.type || 'expense';
                const radio = document.querySelector(`input[name="type"][id="edit-type-${type}"]`);
                if (radio) {
                    radio.checked = true;
                }
                updateEditModalType(type);

                const amountInput = document.getElementById('edit-amount-input');
                if (amountInput) {
                    amountInput.value = data.amount;
                    updateInputAmountPreview('edit-amount-input', 'edit-amount-formatted');
                }

                const walletSelect = document.getElementById('edit-wallet-select');
                if (walletSelect && data.wallet_id) walletSelect.value = data.wallet_id;

                const targetWalletSelect = document.getElementById('edit-target-wallet-select');
                if (targetWalletSelect) {
                    targetWalletSelect.value = data.target_wallet_id ? data.target_wallet_id : '';
                }

                const categorySelect = document.getElementById('edit-category-select');
                if (categorySelect && data.category_id) {
                    categorySelect.value = data.category_id;
                }

                const dateInput = document.getElementById('edit-date-input');
                if (dateInput && data.date) dateInput.value = data.date;

                const descInput = document.getElementById('edit-desc-input');
                if (descInput) descInput.value = data.description || '';

                hideEditModalDeleteConfirm();
                modal.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    panel.classList.remove('translate-y-full');
                    panel.classList.add('translate-y-0');
                }, 10);
            }

            function showEditModalDeleteConfirm() {
                const initBox = document.getElementById('edit-initial-buttons');
                const confirmBox = document.getElementById('edit-delete-confirm-box');
                if (initBox) initBox.classList.add('hidden');
                if (confirmBox) confirmBox.classList.remove('hidden');
            }

            function hideEditModalDeleteConfirm() {
                const initBox = document.getElementById('edit-initial-buttons');
                const confirmBox = document.getElementById('edit-delete-confirm-box');
                if (initBox) initBox.classList.remove('hidden');
                if (confirmBox) confirmBox.classList.add('hidden');
            }

            function closeEditTransactionModal() {
                const modal = document.getElementById('edit-transaction-modal');
                const backdrop = document.getElementById('edit-modal-backdrop');
                const panel = document.getElementById('edit-modal-panel');

                if (!modal) return;

                hideEditModalDeleteConfirm();
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function triggerDeleteFromEditModal() {
                showEditModalDeleteConfirm();
            }

            function openEditFromDataset(el, ev) {
                if (ev) ev.stopPropagation();
                try {
                    const raw = el.dataset.tx || el.closest('[data-tx]')?.dataset.tx;
                    if (!raw) return;
                    const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    openEditTransactionModal(data);
                } catch (e) {
                    console.error('Error parsing transaction data for edit:', e);
                }
            }

            function openDeleteFromDataset(el, ev) {
                if (ev) ev.stopPropagation();
                try {
                    const raw = el.dataset.tx || el.closest('[data-tx]')?.dataset.tx;
                    if (!raw) return;
                    const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    openDeleteTransactionModal(data);
                } catch (e) {
                    console.error('Error parsing transaction data for delete:', e);
                }
            }

            function openDeleteTransactionModal(data) {
                if (typeof data === 'string') {
                    try { data = JSON.parse(data); } catch(e) {}
                }
                if (!data || !data.id) return;
                currentActiveTransaction = data;

                const modal = document.getElementById('delete-transaction-confirm-modal');
                const backdrop = document.getElementById('delete-modal-backdrop');
                const panel = document.getElementById('delete-modal-panel');
                const deleteForm = document.getElementById('global-delete-transaction-form');

                if (!modal) return;

                if (deleteForm) {
                    deleteForm.action = '/transactions/' + data.id;
                }

                const amtEl = document.getElementById('delete-preview-amount');
                if (amtEl) {
                    const formattedAmt = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(data.amount || 0);
                    amtEl.textContent = 'Rp ' + formattedAmt;
                }

                const dateEl = document.getElementById('delete-preview-date');
                if (dateEl) {
                    dateEl.textContent = data.date_formatted || data.date || '-';
                }

                const descEl = document.getElementById('delete-preview-desc');
                if (descEl) {
                    descEl.textContent = data.description || (data.category_name || (data.type === 'transfer' ? 'Transfer Antar Dompet' : 'Transaksi'));
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    panel.classList.remove('translate-y-full');
                    panel.classList.add('translate-y-0');
                }, 10);
            }

            function closeDeleteTransactionModal() {
                const modal = document.getElementById('delete-transaction-confirm-modal');
                const backdrop = document.getElementById('delete-modal-backdrop');
                const panel = document.getElementById('delete-modal-panel');

                if (!modal) return;

                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            async function executeDeleteTransaction() {
                if (!currentActiveTransaction || !currentActiveTransaction.id) {
                    showSwanToast('ID transaksi tidak ditemukan', 'error');
                    return;
                }

                const targetId = currentActiveTransaction.id;
                const btns = [
                    document.getElementById('confirm-delete-submit-btn'),
                    document.getElementById('edit-modal-delete-confirm-btn')
                ].filter(Boolean);

                const origHtmls = btns.map(b => b.innerHTML);
                btns.forEach(b => {
                    b.disabled = true;
                    b.innerHTML = '<svg class="w-4 h-4 animate-spin inline-block mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
                });

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('#global-delete-transaction-form input[name="_token"]')?.value
                    || document.querySelector('input[name="_token"]')?.value;

                try {
                    const res = await fetch(`/transactions/${targetId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    });

                    const result = await res.json().catch(() => null);

                    if (res.ok && result && result.success) {
                        showSwanToast(result.message || 'Transaksi berhasil dihapus!', 'success');
                        closeDeleteTransactionModal();
                        closeEditTransactionModal();

                        // Instantly remove row from DOM
                        const rowEls = document.querySelectorAll(`[data-transaction-row="${targetId}"]`);
                        rowEls.forEach(rowEl => {
                            rowEl.style.transition = 'all 0.25s ease-out';
                            rowEl.style.opacity = '0';
                            rowEl.style.transform = 'scale(0.95)';
                            setTimeout(() => rowEl.remove(), 250);
                        });

                        // Cache-busting refresh so fresh server HTML with updated balances is fetched
                        setTimeout(() => {
                            const url = new URL(window.location.href);
                            url.searchParams.set('_t', Date.now().toString());
                            window.location.replace(url.toString());
                        }, 350);
                    } else {
                        const errMsg = (result && result.message) ? result.message : ('Gagal menghapus transaksi (Status ' + res.status + ')');
                        showSwanToast(errMsg, 'error');
                        btns.forEach((b, i) => {
                            b.disabled = false;
                            b.innerHTML = origHtmls[i] || 'Ya, Hapus';
                        });
                    }
                } catch (err) {
                    console.error('Fetch delete error, attempting form fallback:', err);
                    const deleteForm = document.getElementById('global-delete-transaction-form');
                    if (deleteForm) {
                        deleteForm.action = `/transactions/${targetId}`;
                        deleteForm.submit();
                    } else {
                        showSwanToast('Gagal menghapus transaksi. Periksa koneksi Anda.', 'error');
                        btns.forEach((b, i) => {
                            b.disabled = false;
                            b.innerHTML = origHtmls[i] || 'Ya, Hapus';
                        });
                    }
                }
            }

            // Universal Bottom-Sheet Modal Animation Helpers
            window.openSheetModal = function(modalId) {
                const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
                if (!modal) return;
                const backdrop = modal.querySelector('.modal-backdrop') || modal.querySelector('[data-backdrop]') || modal.children[0];
                const panel = modal.querySelector('.modal-sheet-safe') || modal.querySelector('.modal-panel') || (modal.children[1] ? (modal.children[1].firstElementChild || modal.children[1]) : null);
                
                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    if (backdrop) {
                        backdrop.classList.remove('opacity-0');
                        backdrop.classList.add('opacity-100');
                    }
                    if (panel) {
                        panel.classList.remove('translate-y-full');
                        panel.classList.add('translate-y-0');
                    }
                });
            };

            window.closeSheetModal = function(modalId) {
                const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
                if (!modal) return;
                const backdrop = modal.querySelector('.modal-backdrop') || modal.querySelector('[data-backdrop]') || modal.children[0];
                const panel = modal.querySelector('.modal-sheet-safe') || modal.querySelector('.modal-panel') || (modal.children[1] ? (modal.children[1].firstElementChild || modal.children[1]) : null);
                
                if (backdrop) {
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0');
                }
                if (panel) {
                    panel.classList.remove('translate-y-0');
                    panel.classList.add('translate-y-full');
                }
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };

            // Clipboard Paste Listener for Screenshots / Images
            window.addEventListener('paste', (e) => {
                const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
                if (!items) return;
                for (let item of items) {
                    if (item.type && item.type.indexOf('image') !== -1) {
                        const file = item.getAsFile();
                        if (file) {
                            e.preventDefault();
                            const scannerModal = document.getElementById('receipt-scanner-modal');
                            if (scannerModal && scannerModal.classList.contains('hidden')) {
                                openReceiptScannerModal('new');
                            }
                            processReceiptFile({ files: [file] });
                            showSwanToast('Gambar struk dari papan klip berhasil dimuat!');
                            break;
                        }
                    }
                }
            });

            // Drag and Drop for Scanner Dropzone & Global Esc Key
            document.addEventListener('DOMContentLoaded', () => {
                const dropzone = document.getElementById('scanner-dropzone');
                if (dropzone) {
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.classList.add('border-teal-500', 'bg-teal-50/60', 'dark:bg-teal-950/40');
                        }, false);
                    });
                    ['dragleave', 'drop'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.classList.remove('border-teal-500', 'bg-teal-50/60', 'dark:bg-teal-950/40');
                        }, false);
                    });
                    dropzone.addEventListener('drop', (e) => {
                        const dt = e.dataTransfer;
                        const files = dt?.files;
                        if (files && files.length > 0) {
                            processReceiptFile({ files });
                        }
                    }, false);
                }

                // Global Escape (Esc) key listener to dismiss open modals smoothly
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' || e.key === 'Esc') {
                        const scannerModal = document.getElementById('receipt-scanner-modal');
                        if (scannerModal && !scannerModal.classList.contains('hidden')) {
                            closeReceiptScannerModal();
                            return;
                        }
                        const txModal = document.getElementById('transaction-modal');
                        if (txModal && !txModal.classList.contains('hidden')) {
                            closeTransactionModal();
                            return;
                        }
                        const editModal = document.getElementById('edit-transaction-modal');
                        if (editModal && !editModal.classList.contains('hidden')) {
                            closeEditTransactionModal();
                            return;
                        }
                        if (typeof closePreviewModal === 'function') {
                            const previewModal = document.getElementById('preview-modal');
                            if (previewModal && !previewModal.classList.contains('hidden')) {
                                closePreviewModal();
                                return;
                            }
                        }
                        if (typeof closeQuotaModal === 'function') {
                            const quotaModal = document.getElementById('quota-modal');
                            if (quotaModal && !quotaModal.classList.contains('hidden')) {
                                closeQuotaModal();
                                return;
                            }
                        }
                        if (typeof closeShareModal === 'function') {
                            const shareModal = document.getElementById('share-modal');
                            if (shareModal && !shareModal.classList.contains('hidden')) {
                                closeShareModal();
                                return;
                            }
                        }
                        if (typeof closeDeleteModal === 'function') {
                            const deleteModal = document.getElementById('delete-modal');
                            if (deleteModal && !deleteModal.classList.contains('hidden')) {
                                closeDeleteModal();
                                return;
                            }
                        }
                        if (typeof closeCreateDropModal === 'function') {
                            const dropModal = document.getElementById('create-drop-modal');
                            if (dropModal && !dropModal.classList.contains('hidden')) {
                                closeCreateDropModal();
                                return;
                            }
                        }
                    }
                });
            });

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

    <!-- Dynamic Modal Stacks from Child Views -->
    @stack('modals')
</body>

{{-- Allow child views to inject additional scripts --}}
@stack('scripts')
</html>

