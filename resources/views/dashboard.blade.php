@extends('layouts.mobile')

@php
    $dashboardModules = [
        ['name' => 'Transaksi', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'route' => 'transactions.index', 'classes' => 'text-emerald-500 group-hover:bg-emerald-500 dark:text-emerald-400'],
        ['name' => 'Laporan', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'route' => 'reports.index', 'classes' => 'text-indigo-500 group-hover:bg-indigo-500 dark:text-indigo-400'],
        ['name' => 'Dompet', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'route' => 'wallets.index', 'classes' => 'text-blue-500 group-hover:bg-blue-500 dark:text-blue-400'],
        ['name' => 'Anggaran', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z', 'route' => 'budgets.index', 'classes' => 'text-orange-500 group-hover:bg-orange-500 dark:text-orange-400'],
        ['name' => 'Langganan', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'route' => 'subscriptions.index', 'classes' => 'text-purple-500 group-hover:bg-purple-500 dark:text-purple-400'],
        ['name' => 'Hutang', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'route' => 'debts.index', 'classes' => 'text-rose-500 group-hover:bg-rose-500 dark:text-rose-400'],
        ['name' => 'Investasi', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'route' => 'investments.index', 'classes' => 'text-emerald-500 group-hover:bg-emerald-500 dark:text-emerald-400'],
        ['name' => 'Kalkulator', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'route' => 'calculator.index', 'classes' => 'text-teal-500 group-hover:bg-teal-500 dark:text-teal-400'],
        ['name' => 'Kategori', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z', 'route' => 'categories.index', 'classes' => 'text-amber-500 group-hover:bg-amber-500 dark:text-amber-400'],
    ];

    $localNow = now()->setTimezone('Asia/Makassar');
    $hour = (int) $localNow->format('H');
    $greetingTime = match(true) {
        $hour >= 4 && $hour < 11 => 'Selamat Pagi',
        $hour >= 11 && $hour < 15 => 'Selamat Siang',
        $hour >= 15 && $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };
    $greetingIcon = match(true) {
        $hour >= 4 && $hour < 11 => '🌅',
        $hour >= 11 && $hour < 15 => '☀️',
        $hour >= 15 && $hour < 18 => '🌤️',
        default => '🌙',
    };
    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $todayFormatted = $dayNames[$localNow->dayOfWeek] . ', ' . $localNow->day . ' ' . $monthNames[$localNow->month];
@endphp

@section('custom_header')
    <!-- Apple iOS Liquid Glass Hero Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 dark:from-slate-900 dark:via-emerald-950/90 dark:to-slate-950 text-white px-5 pt-10 pb-12 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-300 border-b border-white/20 dark:border-white/10" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.75rem));">
        
        <!-- Specular Rim -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Blobs -->
        <div class="absolute -right-12 -top-12 w-56 h-56 bg-white/20 dark:bg-emerald-500/15 rounded-full blur-[40px] pointer-events-none mix-blend-screen"></div>
        <div class="absolute -left-12 top-24 w-48 h-48 bg-teal-300/20 dark:bg-teal-500/10 rounded-full blur-[40px] pointer-events-none mix-blend-screen"></div>

        <!-- Top Bar: Greeting & Liquid Action Pills -->
        <div class="relative z-10 flex items-center justify-between mb-8">
            <div class="flex items-center gap-3.5 min-w-0">
                <!-- Swan Brand Icon: Luxury Glass Squircle with Pure Vector Swan & Ambient Halo -->
                <a href="{{ route('profile.edit') }}" aria-label="Profil & Pengaturan" class="relative group ios-press shrink-0 block">
                    <!-- Ambient Breathing Glow Behind Badge -->
                    <div class="absolute -inset-1 rounded-[20px] bg-gradient-to-tr from-emerald-400/50 via-teal-300/40 to-emerald-500/50 blur-md opacity-65 group-hover:opacity-95 transition duration-500 pointer-events-none"></div>
                    
                    <!-- Glass Squircle Container -->
                    <div class="relative w-12 h-12 rounded-[20px] bg-gradient-to-b from-white/30 via-white/15 to-white/10 dark:from-slate-800/90 dark:via-slate-900/90 dark:to-slate-950 backdrop-blur-2xl border border-white/40 dark:border-white/20 shadow-xl shadow-emerald-950/30 flex items-center justify-center">
                        <x-app-logo class="w-8 h-8" variant="icon" />
                    </div>
                </a>

                <!-- User Greeting & Name -->
                <div class="flex flex-col min-w-0">
                    <!-- Dynamic Time Pill -->
                    <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-0.5 rounded-full bg-white/15 dark:bg-white/10 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xs mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span id="dynamic-greeting-text" class="text-[10px] font-bold tracking-wider uppercase text-emerald-100 dark:text-emerald-300 select-none">
                            {{ $greetingTime }}
                        </span>
                        <span id="dynamic-greeting-icon" class="text-xs leading-none select-none">{{ $greetingIcon }}</span>
                    </div>

                    <!-- Personalized Name with Interactive Waving Hand -->
                    <h2 class="text-2xl font-black tracking-tight flex items-center gap-1.5 leading-tight">
                        <span class="bg-gradient-to-r from-white via-white to-emerald-100 bg-clip-text text-transparent drop-shadow-sm font-black">
                            {{ explode(' ', $user->name ?? 'Gusti')[0] }}
                        </span>
                        <span class="animate-wave-hand text-2xl origin-[70%_70%] select-none cursor-pointer hover:scale-125 transition-transform" 
                              title="Halo {{ explode(' ', $user->name ?? 'Gusti')[0] }}!" 
                              onclick="this.classList.remove('animate-wave-hand'); void this.offsetWidth; this.classList.add('animate-wave-hand');">👋</span>
                        <span class="sr-only">{{ $user->name ?? 'Gusti Swandana' }}</span>
                    </h2>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5">
                <!-- Theme Toggle Button -->
                <button type="button" 
                        id="theme-toggle-btn"
                        onclick="toggleSwanFlowTheme()" 
                        aria-label="Toggle Theme" 
                        class="w-10 h-10 flex items-center justify-center rounded-[14px] liquid-glass bg-white/15 hover:bg-white/25 border border-white/30 dark:bg-slate-800/80 dark:border-white/10 active:scale-95 transition-all shadow-xs ios-press">
                    <svg class="theme-icon-dark w-5 h-5 text-amber-300 transition-transform duration-300 transform rotate-0 hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg class="theme-icon-light w-5 h-5 text-white transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                <!-- Notification / History -->
                <a href="{{ route('transactions.index') }}" aria-label="Notifikasi" class="w-10 h-10 flex items-center justify-center rounded-[14px] liquid-glass text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/30 dark:bg-slate-800/80 dark:border-white/10 active:scale-95 transition-all shadow-xs ios-press">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span class="sr-only">Notifikasi</span>
                </a>

                <!-- User Avatar -->
                <a href="{{ route('profile.edit') }}" aria-label="Profil Pengguna" class="w-10 h-10 flex items-center justify-center rounded-[14px] liquid-glass bg-white/20 dark:bg-slate-800 text-white font-black text-xs border border-white/30 dark:border-white/10 shadow-xs ios-press">
                    GS
                </a>
            </div>
        </div>

        <!-- Centralized Finance Card -->
        <div class="relative z-20 bg-white/10 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/30 dark:border-white/10 rounded-[30px] p-5 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-emerald-100 dark:text-slate-400 uppercase tracking-wider">Total Saldo Aktif</span>
                <button type="button" 
                        onclick="toggleBalanceVisibility()" 
                        class="p-2 rounded-full text-emerald-100 hover:text-white dark:text-slate-300 dark:hover:text-white bg-white/10 hover:bg-white/20 dark:bg-slate-800/60 dark:hover:bg-slate-700/60 transition-all cursor-pointer">
                    <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            
            <div class="relative z-10 mb-6">
                <p id="balance-display" class="text-4xl sm:text-5xl font-black tracking-tight text-white drop-shadow-sm">
                    Rp {{ number_format($totalBalance ?? 0, 0, ',', '.') }}
                </p>
                <p id="hidden-balance" class="text-4xl sm:text-5xl font-black tracking-tight text-white/50 hidden">
                    ••••••••••
                </p>
            </div>

            <!-- Income/Expense Summary (Clickable to Add Transactions) -->
            <div class="flex items-center justify-between pt-4 border-t border-white/20 dark:border-white/10">
                <button type="button" 
                        onclick="openTransactionModal('income')"
                        class="flex items-center gap-3 text-left p-2 -m-2 rounded-2xl hover:bg-white/10 dark:hover:bg-white/5 active:scale-95 transition-all cursor-pointer group ios-press"
                        title="Catat Pemasukan Baru">
                    <div class="w-9 h-9 rounded-full bg-white/20 dark:bg-emerald-500/20 text-emerald-100 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-emerald-100/90 dark:text-slate-400 font-bold uppercase tracking-wider block">Pemasukan</span>
                            <span class="text-[10px] text-emerald-300 font-black opacity-60 group-hover:opacity-100 transition-opacity">+</span>
                        </div>
                        <span class="text-sm font-bold text-white">+Rp {{ number_format($thisMonthIncome ?? 0, 0, ',', '.') }}</span>
                    </div>
                </button>
                <button type="button" 
                        onclick="openTransactionModal('expense')"
                        class="flex items-center gap-3 text-left p-2 -m-2 rounded-2xl hover:bg-white/10 dark:hover:bg-white/5 active:scale-95 transition-all cursor-pointer group ios-press"
                        title="Catat Pengeluaran Baru">
                    <div class="w-9 h-9 rounded-full bg-white/20 dark:bg-rose-500/20 text-rose-100 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-500 group-hover:text-white transition-all shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-emerald-100/90 dark:text-slate-400 font-bold uppercase tracking-wider block">Pengeluaran</span>
                            <span class="text-[10px] text-rose-300 font-black opacity-60 group-hover:opacity-100 transition-opacity">+</span>
                        </div>
                        <span class="text-sm font-bold text-white">-Rp {{ number_format($thisMonthExpense ?? 0, 0, ',', '.') }}</span>
                    </div>
                </button>
            </div>

            <!-- Anggaran Pengeluaran (Horizontal Donut Progress Cards) -->
            <div class="relative z-10 space-y-2.5 pt-3 mt-3 border-t border-white/15 dark:border-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-white/90 dark:text-slate-200 tracking-wide uppercase">Anggaran Pengeluaran</h3>
                    <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-emerald-100 hover:text-white dark:text-emerald-400 dark:hover:text-emerald-300 underline-offset-2 hover:underline">Kelola & Detail</a>
                </div>
                <div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1 snap-x">
                    @forelse($categoryBudgets as $budget)
                        <a href="{{ route('budgets.index') }}" class="bg-white/95 dark:bg-slate-900/90 rounded-[20px] p-3 min-w-[110px] flex flex-col items-center justify-center text-center shadow-md border border-white/20 dark:border-white/10 shrink-0 backdrop-blur-md active:scale-95 transition-all group ios-press snap-start">
                            <!-- Circular Donut SVG Ring with Category Icon in Center -->
                            <div class="relative w-12 h-12 flex items-center justify-center">
                                <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-slate-100 dark:text-slate-800" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="{{ $budget->percent >= 100 ? 'text-rose-500' : ($budget->percent >= 75 ? 'text-amber-500' : 'text-emerald-500') }} transition-all duration-500" stroke-width="3.5" stroke-dasharray="{{ min(100, $budget->percent ?? 50) }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center text-slate-700 dark:text-slate-200 group-hover:scale-110 transition-transform">
                                    <x-category-icon :name="$budget->name" class="w-5 h-5" />
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-2 truncate max-w-[95px]">
                                {{ $budget->name }}
                            </span>
                            <span class="text-[10px] font-semibold {{ $budget->percent >= 100 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }} mt-0.5">
                                {{ $budget->percent ?? 0 }}%
                            </span>
                        </a>
                    @empty
                        <div class="w-full py-3 px-3 text-center text-xs text-white/90 dark:text-slate-400 bg-white/15 dark:bg-slate-900/60 rounded-2xl border border-white/20 dark:border-slate-800">
                            Belum ada anggaran bulanan. <a href="{{ route('budgets.index') }}" class="underline font-bold text-white dark:text-emerald-400 ml-1">+ Atur Anggaran</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(7.5rem,calc(6.75rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-6 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Pull handle indicator -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-2"></div>

    <!-- QUICK ACCESS MODULES (2 ROWS GRID) -->
    <section>
        <div class="flex items-center justify-between mb-2.5 px-0.5">
            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Akses Cepat</span>
            <button type="button" onclick="openShortcutModal()" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:opacity-80 transition-opacity flex items-center gap-1 active:scale-95 cursor-pointer" aria-label="Atur Pintasan">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"></path>
                </svg>
                <span>Atur</span>
            </button>
        </div>

        <div class="grid grid-cols-4 gap-y-3.5 gap-x-2">
            @foreach($dashboardModules as $mod)
                <a href="{{ route($mod['route']) }}" data-route="{{ $mod['route'] }}" class="dashboard-module flex flex-col items-center gap-1.5 group active:scale-95 transition-all cursor-pointer">
                    <div class="w-[58px] h-[58px] rounded-[22px] bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex items-center justify-center {{ $mod['classes'] }} group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mod['icon'] }}"></path>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors text-center truncate max-w-full">{{ $mod['name'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 2. DOMPET & REKENING (Dynamic Wallets List) -->
    <section class="space-y-3 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Dompet & Rekening</h3>
                <span class="text-xs font-semibold text-slate-400">
                    ({{ count($wallets ?? []) }})
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('wallets.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:opacity-80 transition-opacity">
                    Kelola
                </a>
                <button type="button" 
                        onclick="openDashboardAddWalletModal()" 
                        class="px-2.5 py-1 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-[11px] active:scale-95 transition-all ios-press">
                    + Tambah
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1 snap-x">
            @forelse($wallets ?? [] as $wallet)
                <a href="{{ route('wallets.index') }}" class="min-w-[135px] liquid-card bg-white/80 dark:bg-slate-900/75 p-3.5 rounded-[22px] border border-white/60 dark:border-white/10 shadow-xs flex flex-col justify-between min-h-[90px] snap-start shrink-0 ios-press hover:border-emerald-500/50 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">{{ $wallet->type }}</span>
                        <span class="w-2 h-2 rounded-full {{ $wallet->type === 'bank' ? 'bg-emerald-500' : ($wallet->type === 'ewallet' ? 'bg-teal-500' : 'bg-amber-500') }}"></span>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white truncate">{{ $wallet->name }}</h4>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 mt-0.5">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="w-full py-4 text-center text-xs text-slate-400 bg-white/60 dark:bg-slate-900/60 rounded-2xl border border-dashed border-slate-300 dark:border-slate-800">
                    Belum ada dompet terdaftar.
                </div>
            @endforelse

            <button type="button" 
                    onclick="openDashboardAddWalletModal()" 
                    class="min-w-[100px] min-h-[90px] rounded-[22px] border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 flex flex-col items-center justify-center gap-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 shrink-0 active:scale-95 transition-all cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <span class="text-[10px] font-bold">+ Dompet</span>
            </button>
        </div>
    </section>

    <!-- WIDGET AKTIVITAS (Todo Widget) -->
    <section>
        <div class="flex items-center justify-between mb-3.5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                Aktivitas Hari Ini
            </h3>
            <a href="{{ route('todos.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:opacity-80 transition-opacity">
                Lihat Semua
            </a>
        </div>

        @if(isset($todayTodos) && $todayTodos->isNotEmpty())
            <div class="space-y-2.5">
                @foreach($todayTodos->take(3) as $todo)
                    <label class="liquid-card flex items-center gap-3.5 p-3.5 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 rounded-2xl shadow-xs hover:shadow-sm cursor-pointer transition-all group">
                        <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="shrink-0 flex items-center" id="form-todo-{{ $todo->id }}">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox" onchange="document.getElementById('form-todo-{{ $todo->id }}').submit()" {{ $todo->is_completed ? 'checked' : '' }} class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600 text-emerald-500 focus:ring-emerald-500 checked:bg-emerald-500 transition-all cursor-pointer">
                        </form>
                        <span class="text-sm font-medium transition-colors {{ $todo->is_completed ? 'text-slate-400 dark:text-slate-500 line-through' : 'text-slate-700 dark:text-slate-200 group-hover:text-slate-950 dark:group-hover:text-white' }}">
                            {{ $todo->title }}
                        </span>
                    </label>
                @endforeach
            </div>
        @else
            <div class="py-5 text-center text-xs font-medium text-slate-500 dark:text-slate-400 bg-white/60 dark:bg-slate-900/60 rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 shadow-xs">
                Belum ada prioritas hari ini.<br>
                <a href="{{ route('todos.index') }}" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline mt-1.5 inline-block">Tambah Aktivitas</a>
            </div>
        @endif
    </section>

    <!-- DRIVE SECTION -->
    <section>
        <div class="flex items-center justify-between mb-3.5">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-4 bg-teal-500 rounded-full shadow-[0_0_8px_rgba(20,184,166,0.6)]"></span>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">SwanDrive Vault</h3>
                @if(($activeLinksCount ?? 0) > 0)
                    <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-400/30 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $activeLinksCount }} Link Aktif</span>
                    </span>
                @endif
            </div>
            <a href="{{ route('drive.index') }}" class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:opacity-80 transition-opacity">
                Buka Drive
            </a>
        </div>
        
        <div class="flex gap-4 overflow-x-auto no-scrollbar pb-3 snap-x px-1 -mx-1">
            <!-- Drive Storage Summary Card -->
            <div class="min-w-[140px] bg-gradient-to-br from-teal-500 to-emerald-600 text-white rounded-[24px] p-4 shadow-lg shadow-teal-500/20 snap-start shrink-0 flex flex-col justify-between">
                <svg class="w-6 h-6 opacity-90 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                </svg>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-teal-100">Storage</div>
                    <div class="text-xl font-black mt-0.5">{{ $storedFilesCount ?? 0 }} <span class="text-sm font-medium opacity-80">Berkas</span></div>
                </div>
            </div>

            <!-- Real Uploaded Files from User Storage -->
            @forelse($recentStoredFiles ?? [] as $stFile)
                @php
                    $isImage = in_array(strtolower($stFile->extension), ["jpg", "jpeg", "png", "gif", "webp", "svg"]);
                    $isPdf = strtolower($stFile->extension) === "pdf";
                @endphp
                <a href="{{ route('drive.index', $stFile->folder_id ? ['folder_id' => $stFile->folder_id] : []) }}" 
                   class="min-w-[130px] max-w-[150px] liquid-card bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 rounded-[24px] p-3.5 shadow-xs hover:shadow-md hover:border-teal-400/40 snap-start shrink-0 flex flex-col justify-between cursor-pointer transition-all ios-press group">
                    <div class="w-10 h-10 rounded-2xl {{ $isPdf ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400' : ($isImage ? 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400' : 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400') }} flex items-center justify-center mb-2.5 group-hover:scale-105 transition-transform">
                        @if($isPdf)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        @elseif($isImage)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors" title="{{ $stFile->original_name }}">
                            {{ $stFile->title ?: $stFile->original_name }}
                        </div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                            {{ $stFile->created_at->diffForHumans(['parts' => 1]) }}
                        </div>
                    </div>
                </a>
            @empty
                <a href="{{ route('drive.index') }}" 
                   class="min-w-[170px] liquid-card bg-white/70 dark:bg-slate-900/60 border border-dashed border-teal-400/40 dark:border-teal-500/30 rounded-[24px] p-3.5 snap-start shrink-0 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-teal-50/40 dark:hover:bg-teal-950/20 transition-all group ios-press">
                    <div class="w-9 h-9 rounded-2xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200">Unggah Berkas</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Mulai simpan file Anda</div>
                </a>
            @endforelse
        </div>
    </section>

    <!-- 5. SECTION TRANSAKSI TERAKHIR -->
    <section class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                Transaksi Terakhir
            </h2>
            <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:opacity-80 transition-opacity">
                Riwayat
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse($recentTransactions ?? [] as $tx)
                @php
                    $isIncome = $tx->type === \App\Enums\TransactionType::Income || $tx->type === 'income';
                    $isTransfer = $tx->type === \App\Enums\TransactionType::Transfer || $tx->type === 'transfer';
                @endphp
                <div onclick='openEditTransactionModal({
                        id: {{ $tx->id }},
                        type: "{{ is_string($tx->type) ? $tx->type : $tx->type->value }}",
                        amount: {{ $tx->amount }},
                        wallet_id: {{ $tx->wallet_id }},
                        target_wallet_id: {{ $tx->target_wallet_id ?: "null" }},
                        category_id: {{ $tx->category_id ?: "null" }},
                        date: "{{ \Carbon\Carbon::parse($tx->date)->format("Y-m-d") }}",
                        description: @json($tx->description ?? "")
                    })'
                    role="button"
                    tabindex="0"
                    class="liquid-card p-3.5 flex items-center justify-between rounded-[22px] bg-white/80 dark:bg-slate-900/75 hover:bg-white dark:hover:bg-slate-900 active:scale-[0.98] cursor-pointer transition-all border border-white/60 dark:border-white/10 shadow-xs ios-press">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-[16px] {{ $isIncome ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' : ($isTransfer ? 'bg-teal-50 text-teal-600 dark:bg-teal-500/15 dark:text-teal-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200') }} flex items-center justify-center shrink-0 shadow-2xs">
                            <x-category-icon :category="$tx->category" :type="$tx->type" :name="$tx->description ?: ($tx->category->name ?? '')" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-black text-slate-900 dark:text-white truncate">
                                @if($isTransfer)
                                    {{ $tx->description ?: 'Transfer Antar Dompet' }}
                                @else
                                    {{ $tx->description ?: ($tx->category->name ?? 'Transaksi') }}
                                @endif
                            </p>
                            <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 pl-2">
                        <span class="text-xs font-black {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-teal-600 dark:text-teal-400' : 'text-rose-600 dark:text-rose-400') }} block">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center space-y-1.5 liquid-card bg-white/60 dark:bg-slate-900/60 rounded-[22px] border border-dashed border-slate-300 dark:border-slate-800">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum ada transaksi</p>
                    <p class="text-[11px] text-slate-400">Tekan tombol (+) untuk mencatat transaksi baru.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- App Attribution Footer -->
    <div class="pt-6 pb-2 text-center">
        <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
            SwanFlow &bull; Dibuat oleh <strong class="font-bold text-slate-600 dark:text-slate-400">Gusti Swandana</strong>
        </p>
    </div>
</div>

@endsection

@push('modals')
<!-- Shortcut Management Modal -->
<div id="shortcutModal" class="fixed inset-0 z-50 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity opacity-0 duration-300 z-10" id="shortcutModalBg" onclick="closeShortcutModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none z-20">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] p-5 border-t border-white/60 dark:border-white/10 shadow-2xl transition-transform duration-300 transform translate-y-full flex flex-col max-h-[85vh] pointer-events-auto relative z-20" id="shortcutModalContent">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeShortcutModal()"></div>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80 mb-3">
                <div>
                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block -mb-0.5">Personalisasi</span>
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight">Atur Pintasan</h3>
                </div>
                <button type="button" onclick="closeShortcutModal()" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors ios-press cursor-pointer" aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Pilih modul yang ingin ditampilkan di beranda (Dashboard).</p>
            
            <div class="space-y-2 mb-4 overflow-y-auto no-scrollbar flex-1 pb-1">
                @foreach($dashboardModules as $mod)
                <label class="flex items-center justify-between p-3 border border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 rounded-2xl cursor-pointer hover:bg-slate-100/70 dark:hover:bg-slate-800/70 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center {{ $mod['classes'] }} shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mod['icon'] }}"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $mod['name'] }}</span>
                    </div>
                    <input type="checkbox" value="{{ $mod['route'] }}" class="shortcut-checkbox w-5 h-5 rounded-[6px] border-slate-300 dark:border-slate-600 text-emerald-500 focus:ring-emerald-500 dark:bg-slate-900 focus:ring-offset-0 cursor-pointer">
                </label>
                @endforeach
            </div>
            <button type="button" onclick="saveShortcuts()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-3.5 rounded-2xl active:scale-95 transition-all shadow-lg shadow-emerald-500/25 shrink-0 ios-press cursor-pointer">Simpan Pintasan</button>
        </div>
    </div>
</div>

<!-- Modal Tambah Dompet dari Dashboard -->
<div id="modal-dashboard-add-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0 z-10" id="modal-dashboard-add-wallet-bg" onclick="closeDashboardAddWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none z-20">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar flex flex-col text-slate-800 dark:text-slate-100 relative z-20" id="modal-dashboard-add-wallet-content">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeDashboardAddWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80 mb-4">
                <div>
                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block -mb-0.5">Kelola Keuangan</span>
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight">Tambah Dompet Baru</h3>
                </div>
                <button type="button" onclick="closeDashboardAddWalletModal()" aria-label="Tutup modal" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors ios-press cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Rekening / Dompet</label>
                    <input type="text" name="name" required placeholder="Contoh: BCA Utama, GoPay, Dompet Tunai" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Akun</label>
                    <div class="relative">
                        <select name="type" required class="w-full min-h-[46px] px-4 py-2.5 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all cursor-pointer">
                            <option value="bank">Rekening Bank (BCA, Mandiri, BNI, BRI, dll)</option>
                            <option value="ewallet">E-Wallet (GoPay, OVO, DANA, ShopeePay)</option>
                            <option value="cash">Uang Tunai / Cash Fisik</option>
                            <option value="investment">Investasi (Bibit, Stockbit, dll)</option>
                            <option value="other">Lainnya</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Saldo Awal (Rp)</label>
                    <input type="number" name="balance" step="any" min="0" required placeholder="0" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all ios-press cursor-pointer">
                        Simpan Dompet Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function openDashboardAddWalletModal() {
        const form = document.querySelector('#modal-dashboard-add-wallet form');
        if (form) form.reset();
        window.openSheetModal('modal-dashboard-add-wallet');
    }

    function closeDashboardAddWalletModal() {
        window.closeSheetModal('modal-dashboard-add-wallet');
    }

    let isBalanceHidden = false;
    function toggleBalanceVisibility() {
        isBalanceHidden = !isBalanceHidden;
        const display = document.getElementById('balance-display');
        const hidden = document.getElementById('hidden-balance');
        if (display && hidden) {
            if (isBalanceHidden) {
                display.classList.add('hidden');
                hidden.classList.remove('hidden');
            } else {
                display.classList.remove('hidden');
                hidden.classList.add('hidden');
            }
        }
    }
    const defaultShortcuts = [
        'transactions.index', 'reports.index', 'wallets.index', 
        'budgets.index', 'subscriptions.index', 'debts.index', 
        'investments.index', 'calculator.index'
    ];

    function renderShortcuts() {
        const saved = localStorage.getItem('swanflow_dashboard_shortcuts');
        let selected = defaultShortcuts;
        if (saved) {
            try {
                selected = JSON.parse(saved);
                if (!Array.isArray(selected) || selected.length === 0) {
                    selected = defaultShortcuts;
                }
            } catch (e) {
                selected = defaultShortcuts;
            }
        }
        
        document.querySelectorAll('.dashboard-module').forEach(el => {
            if (selected.includes(el.dataset.route)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    function openShortcutModal() {
        const saved = localStorage.getItem('swanflow_dashboard_shortcuts');
        let selected = defaultShortcuts;
        if (saved) {
            try {
                selected = JSON.parse(saved);
                if (!Array.isArray(selected) || selected.length === 0) {
                    selected = defaultShortcuts;
                }
            } catch (e) {
                selected = defaultShortcuts;
            }
        }

        document.querySelectorAll('.shortcut-checkbox').forEach(cb => {
            cb.checked = selected.includes(cb.value);
        });

        window.openSheetModal('shortcutModal');
    }

    function closeShortcutModal() {
        window.closeSheetModal('shortcutModal');
    }

    function saveShortcuts() {
        const checked = Array.from(document.querySelectorAll('.shortcut-checkbox:checked')).map(cb => cb.value);
        if (checked.length === 0) {
            alert('Pilih setidaknya 1 pintasan untuk beranda!');
            return;
        }
        localStorage.setItem('swanflow_dashboard_shortcuts', JSON.stringify(checked));
        renderShortcuts();
        closeShortcutModal();
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', renderShortcuts);
</script>
@endpush
