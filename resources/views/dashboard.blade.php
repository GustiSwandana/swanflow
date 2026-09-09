@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass Hero Header -->
    <div class="relative overflow-hidden bg-gradient-to-b from-emerald-600/95 via-emerald-600/85 to-teal-700/90 dark:from-slate-900/95 dark:via-slate-900/85 dark:to-slate-950/95 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-all" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.75rem));">
        <!-- Specular Highlight Top Edge -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Blobs Inside Header -->
        <div class="absolute -right-12 -top-12 w-56 h-56 bg-white/20 dark:bg-emerald-500/15 rounded-full blur-3xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -left-12 top-24 w-48 h-48 bg-teal-300/20 dark:bg-teal-500/15 rounded-full blur-2xl pointer-events-none animate-liquid-orb-2"></div>

        <!-- Top Bar: Greeting & Liquid Action Pills -->
        <div class="relative z-10 flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="ios-press">
                    <x-app-logo class="w-8.5 h-8.5" variant="badge" />
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 dark:bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] font-semibold text-emerald-100/90 dark:text-slate-400 block tracking-wide uppercase">SwanFlow</span>
                    </div>
                    <h1 class="text-lg font-black text-white tracking-tight flex items-center gap-1.5">
                        Hi {{ explode(' ', $user->name ?? 'Gusti')[0] }}
                        <span class="sr-only">{{ $user->name ?? 'Gusti Swandana' }}</span>
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Theme Toggle Button (Dark / Light Mode) -->
                <button type="button" 
                        id="theme-toggle-btn"
                        onclick="toggleSwanFlowTheme()" 
                        aria-label="Ganti Tema Gelap atau Terang" 
                        class="min-w-[42px] min-h-[42px] w-10.5 h-10.5 flex items-center justify-center rounded-[18px] text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/25 dark:text-slate-300 dark:hover:text-amber-300 dark:bg-slate-800/80 dark:border-white/10 backdrop-blur-xl shadow-xs ios-press transition-all">
                    <svg class="w-5 h-5 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg class="w-5 h-5 block dark:hidden text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                <!-- History / Notification Action Button -->
                <a href="{{ route('transactions.index') }}" aria-label="Riwayat Transaksi" class="min-w-[42px] min-h-[42px] w-10.5 h-10.5 flex items-center justify-center rounded-[18px] text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/25 dark:text-slate-300 dark:hover:text-white dark:bg-slate-800/80 dark:border-white/10 backdrop-blur-xl shadow-xs ios-press transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </a>

                <!-- Profile Squircle Avatar -->
                <a href="{{ route('profile.edit') }}" class="min-w-[44px] min-h-[44px] flex items-center justify-center ios-press" aria-label="Profil Pengguna">
                    <div class="w-10.5 h-10.5 rounded-[18px] bg-white/20 dark:bg-slate-800 text-white font-extrabold text-xs flex items-center justify-center ring-2 ring-white/35 dark:ring-slate-700/80 border border-white/40 dark:border-white/10 shadow-sm">
                        GS
                    </div>
                </a>
            </div>
        </div>

        <!-- Apple Wallet Fluid Balance Hero Card -->
        <div class="relative z-10 mb-5 p-5 rounded-[28px] bg-white/15 dark:bg-slate-800/40 border border-white/25 dark:border-white/10 backdrop-blur-2xl shadow-xl overflow-hidden group">
            <!-- Glass Shimmer Overlay -->
            <div class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/5 to-white/15 pointer-events-none"></div>

            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-emerald-100 dark:text-slate-300 tracking-wide">Uang kamu tersisa</span>
                    <span class="sr-only">Total Saldo Aktif</span>
                </div>
                <button type="button" 
                        onclick="toggleBalanceVisibility()" 
                        aria-label="Sembunyikan Saldo" 
                        class="p-1 rounded-full text-emerald-100 hover:text-white dark:text-slate-300 dark:hover:text-white ios-press transition-all cursor-pointer">
                    <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>

            <div class="relative z-10 mt-1.5">
                <p id="balance-display" class="text-3xl sm:text-4xl font-black tracking-tight text-white font-sans drop-shadow-xs">
                    Rp {{ number_format($totalBalance ?? 0, 0, ',', '.') }}
                </p>
                <p id="hidden-balance" class="text-3xl sm:text-4xl font-black tracking-tight text-white font-sans mt-0.5 hidden">
                    ••••••••••
                </p>
            </div>
        </div>

        <!-- Twin Liquid Cards: Pemasukan ↗ & Pengeluaran ↘ -->
        <div class="relative z-10 grid grid-cols-2 gap-3 mb-5">
            <!-- Pemasukan Card -->
            <a href="{{ route('transactions.index', ['type' => 'income']) }}"
               class="liquid-card p-3.5 rounded-[24px] bg-white/90 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/40 dark:border-white/10 shadow-lg flex flex-col justify-between ios-press transition-all group cursor-pointer block"
               title="Lihat riwayat pemasukan">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Pemasukan</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                onclick="event.preventDefault(); event.stopPropagation(); openTransactionModal('income')"
                                class="w-6.5 h-6.5 rounded-full bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 flex items-center justify-center ios-press shadow-xs cursor-pointer"
                                title="Catat Pemasukan Baru">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                        <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-sm">↗</span>
                    </div>
                </div>
                <p class="text-sm sm:text-base font-black text-emerald-600 dark:text-emerald-400 truncate mt-1.5">
                    +Rp {{ number_format($thisMonthIncome ?? 0, 0, ',', '.') }}
                </p>
                <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-semibold pt-1 border-t border-slate-100 dark:border-slate-800/70">
                    <span>Lihat Rincian</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </div>
            </a>

            <!-- Pengeluaran Card -->
            <a href="{{ route('transactions.index', ['type' => 'expense']) }}"
               class="liquid-card p-3.5 rounded-[24px] bg-white/90 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/40 dark:border-white/10 shadow-lg flex flex-col justify-between ios-press transition-all group cursor-pointer block"
               title="Lihat riwayat pengeluaran">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-bold group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">Pengeluaran</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                onclick="event.preventDefault(); event.stopPropagation(); openTransactionModal('expense')"
                                class="w-6.5 h-6.5 rounded-full bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/80 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 flex items-center justify-center ios-press shadow-xs cursor-pointer"
                                title="Catat Pengeluaran Baru">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                        <span class="text-rose-600 dark:text-rose-400 font-extrabold text-sm">↘</span>
                    </div>
                </div>
                <p class="text-sm sm:text-base font-black text-rose-600 dark:text-rose-400 truncate mt-1.5">
                    -Rp {{ number_format($thisMonthExpense ?? 0, 0, ',', '.') }}
                </p>
                <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-semibold pt-1 border-t border-slate-100 dark:border-slate-800/70">
                    <span>Lihat Rincian</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </div>
            </a>
        </div>

        <!-- Anggaran Pengeluaran (Apple Fitness Activity Rings) -->
        <div class="relative z-10 space-y-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white dark:text-slate-200 tracking-wide">Anggaran Pengeluaran</h3>
                <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-emerald-100 hover:text-white dark:text-emerald-400 dark:hover:text-emerald-300 underline-offset-2 hover:underline">Kelola & Detail</a>
            </div>
            <div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
                @forelse($categoryBudgets as $budget)
                    <a href="{{ route('budgets.index') }}" class="liquid-card rounded-[24px] p-3.5 min-w-[115px] bg-white/90 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/40 dark:border-white/10 flex flex-col items-center justify-center text-center shadow-md ios-press shrink-0 group">
                        <!-- Activity Donut Ring -->
                        <div class="relative w-14 h-14 flex items-center justify-center">
                            <svg class="w-14 h-14 transform -rotate-90" viewBox="0 0 36 36">
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
                        <span class="text-[10px] font-extrabold {{ $budget->percent >= 100 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }} mt-0.5">
                            {{ $budget->percent ?? 0 }}%
                        </span>
                    </a>
                @empty
                    <div class="w-full py-4 px-3 text-center text-xs text-white/90 dark:text-slate-400 bg-white/15 dark:bg-slate-900/60 rounded-[24px] border border-white/20 dark:border-slate-800 backdrop-blur-xl">
                        Belum ada anggaran bulanan. <a href="{{ route('budgets.index') }}" class="underline font-bold text-white dark:text-emerald-400 ml-1">+ Atur Anggaran</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- iOS Liquid Container -->
<div class="pt-4 px-4 pb-[max(6rem,calc(5.25rem+var(--sab,0px)))] relative z-10 flex-1 flex flex-col min-h-full space-y-6 text-slate-800 dark:text-white transition-colors animate-swan-in">

    <!-- 1. Apple Control Center Quick Hub (8-Grid Squircles) -->
    <div class="grid grid-cols-4 gap-2.5 py-1">
        <!-- 1. Dompet -->
        <a href="{{ route('wallets.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer ios-press" aria-label="Dompet dan Rekening">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-emerald-400 to-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5m-1.5 0h18" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Dompet</span>
        </a>

        <!-- 2. Laporan Keuangan -->
        <a href="{{ route('reports.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer ios-press" aria-label="Laporan dan Analisis Keuangan">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-indigo-400 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Laporan</span>
        </a>

        <!-- 3. Investasi -->
        <a href="{{ route('investments.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer relative ios-press" aria-label="Portofolio Investasi">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-teal-400 to-emerald-600 text-white flex items-center justify-center shadow-md shadow-teal-500/25 group-hover:scale-105 transition-transform duration-200 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                @if(($investmentSummary['count'] ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-emerald-500 text-white text-[10px] font-black flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900 leading-none">
                        {{ $investmentSummary['count'] }}
                    </span>
                @endif
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Investasi</span>
        </a>

        <!-- 4. SwanDrive -->
        <a href="{{ route('drive.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer relative ios-press" aria-label="SwanDrive Cloud Storage">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-sky-400 to-cyan-600 text-white flex items-center justify-center shadow-md shadow-sky-500/25 group-hover:scale-105 transition-transform duration-200 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                </svg>
                @if(($activeLinksCount ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-sky-500 text-white text-[10px] font-black flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900 leading-none">
                        {{ $activeLinksCount }}
                    </span>
                @endif
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Drive</span>
        </a>

        <!-- 5. Aktivitas -->
        <a href="{{ route('todos.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer relative ios-press" aria-label="Aktivitas dan Tugas">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-violet-400 to-purple-600 text-white flex items-center justify-center shadow-md shadow-violet-500/25 group-hover:scale-105 transition-transform duration-200 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                @if(($pendingTodosCount ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900 leading-none">
                        {{ $pendingTodosCount }}
                    </span>
                @endif
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Aktivitas</span>
        </a>

        <!-- 6. Anggaran -->
        <a href="{{ route('budgets.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer ios-press" aria-label="Kelola Anggaran Pengeluaran">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Anggaran</span>
        </a>

        <!-- 7. Kalkulator -->
        <a href="{{ route('calculator.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer ios-press" aria-label="Kalkulator Finansial & Patungan">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm3-6h.008v.008H11.25v-.008zm0 3h.008v.008H11.25v-.008zm0 3h.008v.008H11.25v-.008zm3-6h.008v.008H14.25v-.008zm0 3h.008v.008H14.25v-.008zM4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Kalkulator</span>
        </a>

        <!-- 8. Utang & Piutang -->
        <a href="{{ route('debts.index') }}" class="liquid-card p-3 rounded-[24px] bg-white/80 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex flex-col items-center gap-1.5 group cursor-pointer ios-press" aria-label="Catatan Utang dan Piutang">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-rose-400 to-red-600 text-white flex items-center justify-center shadow-md shadow-rose-500/25 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 text-center leading-tight tracking-tight">Utang</span>
        </a>
    </div>

    <!-- APPLE INTELLIGENCE SCAN STRUK BANNER -->
    <div onclick="openReceiptScannerModal()" class="liquid-card p-4 rounded-[26px] bg-gradient-to-r from-teal-500/15 via-emerald-500/10 to-teal-500/15 dark:from-teal-950/40 dark:via-emerald-950/30 dark:to-teal-950/40 border border-teal-300/50 dark:border-teal-700/50 shadow-md backdrop-blur-2xl flex items-center justify-between cursor-pointer ios-press group">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-11 h-11 rounded-[18px] bg-gradient-to-br from-teal-400 to-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-teal-500/30 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white truncate">Pindai Struk & Bukti Transfer</h3>
                    <span class="text-[9px] font-black px-1.5 py-0.2 rounded-md bg-teal-500/20 text-teal-700 dark:text-teal-300 border border-teal-500/30">AI</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                    Foto nota belanja atau transfer untuk pencatatan otomatis
                </p>
            </div>
        </div>
        <span class="px-3 py-1.5 rounded-full bg-teal-500 hover:bg-teal-600 text-white font-extrabold text-xs shadow-xs ios-press shrink-0 ml-2">
            Pindai ➔
        </span>
    </div>

    <!-- 2. DOMPET & REKENING (Liquid Cards) -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Dompet & Rekening</h2>
                <span class="text-xs font-medium text-slate-400">
                    ({{ count($wallets ?? []) }})
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('wallets.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Kelola
                </a>
                <button type="button" 
                        onclick="openDashboardAddWalletModal()" 
                        class="px-2.5 py-1 rounded-full bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-[11px] ios-press transition-all">
                    + Tambah
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2.5 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
            @forelse($wallets ?? [] as $wallet)
                <a href="{{ route('wallets.index') }}" class="liquid-card min-w-[130px] bg-white/80 dark:bg-slate-900/70 p-3.5 rounded-[22px] border border-white/60 dark:border-white/10 shadow-xs flex flex-col justify-between min-h-[90px] ios-press hover:border-emerald-500/40 transition-all shrink-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $wallet->type }}</span>
                        <span class="w-2 h-2 rounded-full {{ $wallet->type === 'bank' ? 'bg-emerald-500' : ($wallet->type === 'ewallet' ? 'bg-cyan-500' : 'bg-amber-500') }}"></span>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $wallet->name }}</h3>
                        <p class="text-[11px] font-extrabold text-slate-700 dark:text-slate-200 mt-0.5">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="w-full py-4 text-center text-xs text-slate-400 bg-white/60 dark:bg-slate-900/60 rounded-[22px] border border-white/60 dark:border-slate-800 backdrop-blur-xl">
                    Belum ada dompet terdaftar.
                </div>
            @endforelse

            <button type="button" 
                    onclick="openDashboardAddWalletModal()" 
                    class="min-w-[105px] min-h-[90px] rounded-[22px] border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 flex flex-col items-center justify-center gap-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 shrink-0 ios-press transition-all cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <span class="text-[10px] font-bold">+ Dompet</span>
            </button>
        </div>
    </div>

    <!-- PINTASAN SWANDRIVE VAULT (Liquid Glass Card) -->
    <div class="liquid-card bg-white/80 dark:bg-slate-900/70 rounded-[26px] p-4 border border-white/60 dark:border-white/10 shadow-xs space-y-3 transition-colors backdrop-blur-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">SwanDrive Vault</h2>
            </div>
            <a href="{{ route('drive.index') }}" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                Buka Drive ➔
            </a>
        </div>
        <div class="flex items-center justify-between p-3.5 rounded-[20px] bg-teal-50/60 dark:bg-teal-950/30 border border-teal-200/60 dark:border-teal-900/40">
            <a href="{{ route('drive.index') }}" class="flex items-center gap-3 min-w-0 pr-2 group">
                <div class="w-10 h-10 rounded-[16px] bg-gradient-to-br from-teal-400 to-teal-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-teal-500/25 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-800 dark:text-white truncate">Penyimpanan Berkas</span>
                        @if(($activeLinksCount ?? 0) > 0)
                            <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-md bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-400/30 shrink-0 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>{{ $activeLinksCount }} Link Aktif</span>
                            </span>
                        @else
                            <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-md bg-teal-500/20 text-teal-700 dark:text-teal-300 border border-teal-400/30 shrink-0">
                                {{ $storedFilesCount ?? 0 }} Berkas
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                        @if(($activeLinksCount ?? 0) > 0)
                            Tautan terima / transfer berkas sedang aktif
                        @else
                            Simpan privat & kelola link terima file
                        @endif
                    </p>
                </div>
            </a>
            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('drive.index', ['tab' => 'drops']) }}" class="px-2.5 py-1.5 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-700 dark:text-teal-300 font-bold text-[11px] border border-teal-400/30 ios-press transition-all" title="Kelola Link Drop">
                    Link Drop
                </a>
                <a href="{{ route('drive.index') }}" class="px-3 py-1.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-[11px] shadow-xs ios-press transition-all">
                    Buka
                </a>
            </div>
        </div>
    </div>

    <!-- PINTASAN PORTOFOLIO INVESTASI -->
    @if(($investmentSummary['count'] ?? 0) > 0)
    <div class="liquid-card bg-white/80 dark:bg-slate-900/70 rounded-[26px] p-4 border border-white/60 dark:border-white/10 shadow-xs space-y-3 transition-colors backdrop-blur-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Portofolio Investasi</h2>
            </div>
            <a href="{{ route('investments.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Kelola ➔
            </a>
        </div>
        <a href="{{ route('investments.index') }}" class="flex items-center justify-between p-3.5 rounded-[20px] bg-gradient-to-r from-emerald-50/70 to-teal-50/70 dark:from-emerald-950/30 dark:to-teal-950/30 border border-emerald-200/60 dark:border-emerald-900/40 group hover:border-emerald-400 transition-all block ios-press">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-[16px] bg-gradient-to-br from-emerald-400 to-teal-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-800 dark:text-white truncate">Total Nilai Investasi</span>
                        <span class="text-[10px] font-black px-1.5 py-0.2 rounded-md {{ ($investmentSummary['profitLoss'] ?? 0) >= 0 ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300' }}">
                            {{ ($investmentSummary['profitLoss'] ?? 0) >= 0 ? '+' : '' }}{{ $investmentSummary['roi'] ?? 0 }}%
                        </span>
                    </div>
                    <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5">
                        Rp {{ number_format($investmentSummary['totalValue'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[10px] font-semibold text-slate-400 block">{{ $investmentSummary['count'] ?? 0 }} Aset</span>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Lihat ➔</span>
            </div>
        </a>
    </div>
    @endif

    <!-- 3. WIDGET TAGIHAN BERLANGGANAN -->
    @if(isset($upcomingSubscriptions) && $upcomingSubscriptions->isNotEmpty())
        <div class="liquid-card bg-white/80 dark:bg-slate-900/70 rounded-[26px] p-4 border border-white/60 dark:border-white/10 space-y-3 transition-colors shadow-xs backdrop-blur-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Tagihan Terdekat</h2>
                </div>
                <a href="{{ route('subscriptions.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Semua Tagihan
                </a>
            </div>

            <div class="space-y-2">
                @foreach($upcomingSubscriptions as $sub)
                    <div class="flex items-center justify-between p-3 rounded-[18px] bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60 text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8.5 h-8.5 rounded-[12px] bg-indigo-500/15 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                <x-category-icon :name="$sub->name" class="w-4 h-4" />
                            </span>
                            <div class="min-w-0">
                                <span class="font-bold text-slate-800 dark:text-slate-100 block truncate">{{ $sub->name }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">Jatuh tempo: {{ \Carbon\Carbon::parse($sub->next_due_date)->translatedFormat('d M') }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-extrabold text-slate-800 dark:text-slate-100 block">
                                Rp {{ number_format($sub->amount, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('subscriptions.index') }}" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                Bayar ➔
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. WIDGET UTANG & PIUTANG -->
    @if(isset($debtsSummary) && $debtsSummary['unpaidCount'] > 0)
        <div class="liquid-card bg-white/80 dark:bg-slate-900/70 rounded-[26px] p-4 border border-white/60 dark:border-white/10 space-y-3 transition-colors shadow-xs backdrop-blur-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Ringkasan Utang Piutang</h2>
                </div>
                <a href="{{ route('debts.index') }}" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                    Rincian
                </a>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="p-3 rounded-[18px] bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40">
                    <span class="text-[10px] text-slate-600 dark:text-slate-400 block font-semibold">Hak Piutang</span>
                    <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 block mt-0.5 truncate">
                        Rp {{ number_format($debtsSummary['receivables'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3 rounded-[18px] bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-900/40">
                    <span class="text-[10px] text-slate-600 dark:text-slate-400 block font-semibold">Beban Utang</span>
                    <span class="text-xs font-black text-rose-700 dark:text-rose-400 block mt-0.5 truncate">
                        Rp {{ number_format($debtsSummary['debts'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- WIDGET AKTIVITAS HARI INI -->
    <div class="liquid-card bg-white/80 dark:bg-slate-900/70 rounded-[26px] p-4 border border-white/60 dark:border-white/10 space-y-3 transition-colors shadow-xs backdrop-blur-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Aktivitas Hari Ini</h2>
                @if(isset($todayTodos) && $todayTodos->count() > 0)
                    <span class="text-[10px] font-semibold text-slate-400">
                        ({{ $todayTodos->where('is_completed', false)->count() }})
                    </span>
                @endif
            </div>
            <a href="{{ route('todos.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Semua Aktivitas
            </a>
        </div>

        @if(isset($todayTodos) && $todayTodos->isNotEmpty())
            <div class="space-y-2">
                @foreach($todayTodos->take(3) as $todo)
                    @php
                        $accent = match($todo->priority) {
                            'high' => 'border-l-rose-500',
                            'low' => 'border-l-slate-400',
                            default => 'border-l-amber-500',
                        };
                        if ($todo->is_completed) $accent = 'border-l-emerald-500/50 opacity-70';
                    @endphp
                    <div class="flex items-center gap-3 p-3 rounded-[18px] bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60 border-l-[3px] {{ $accent }} text-xs">
                        <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                aria-label="{{ $todo->is_completed ? 'Tandai belum selesai' : 'Tandai selesai' }}"
                                class="w-5.5 h-5.5 rounded-full border-2 flex items-center justify-center ios-press transition-all cursor-pointer shrink-0
                                    {{ $todo->is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 dark:border-slate-600 hover:border-emerald-400' }}">
                                @if($todo->is_completed)
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 dark:text-slate-100 truncate {{ $todo->is_completed ? 'line-through text-slate-400 dark:text-slate-500' : '' }}">
                                {{ $todo->title }}
                            </p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @php $pb = $todo->priorityBadge(); @endphp
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $pb['color'] }}">{{ $pb['label'] }}</span>
                                @if($todo->category)
                                    @php $cb = $todo->categoryBadge(); @endphp
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md bg-slate-200/70 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300">{{ $cb['label'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-3.5 rounded-[18px] bg-slate-50/70 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada aktivitas untuk hari ini</span>
                <a href="{{ route('todos.index') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                    + Catat
                </a>
            </div>
        @endif
    </div>

    <!-- 5. SECTION TRANSAKSI TERAKHIR (Apple Inset Grouped Container) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Transaksi Terakhir</h2>
            <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Lihat semua
            </a>
        </div>

        <div class="liquid-card rounded-[28px] bg-white/85 dark:bg-slate-900/80 border border-white/60 dark:border-white/10 shadow-md divide-y divide-slate-100/80 dark:divide-slate-800/70 overflow-hidden backdrop-blur-2xl">
            @forelse($recentTransactions ?? [] as $tx)
                @php
                    $isIncome = $tx->type === \App\Enums\TransactionType::Income || $tx->type === 'income';
                    $isTransfer = $tx->type === \App\Enums\TransactionType::Transfer || $tx->type === 'transfer';
                    $txData = [
                        'id' => $tx->id,
                        'type' => is_string($tx->type) ? $tx->type : $tx->type->value,
                        'amount' => (float) $tx->amount,
                        'wallet_id' => $tx->wallet_id,
                        'target_wallet_id' => $tx->target_wallet_id,
                        'category_id' => $tx->category_id,
                        'category_name' => $tx->category->name ?? '',
                        'date' => \Carbon\Carbon::parse($tx->date)->format('Y-m-d'),
                        'date_formatted' => \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y'),
                        'description' => $tx->description ?: ($tx->category->name ?? ($isTransfer ? 'Transfer Antar Dompet' : 'Transaksi')),
                    ];
                @endphp
                <div data-transaction-row="{{ $tx->id }}"
                     data-tx="{{ json_encode($txData) }}"
                     class="p-3.5 flex items-center justify-between hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer"
                         onclick="openEditFromDataset(this, event)">
                        <!-- Squircle Category Icon Box -->
                        <div class="w-11 h-11 rounded-[18px] {{ $isIncome ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-900/40' : ($isTransfer ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/40' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/60') }} flex items-center justify-center shrink-0 shadow-2xs">
                            <x-category-icon :category="$tx->category" :type="$tx->type" :name="$tx->description ?: ($tx->category->name ?? '')" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                @if($isTransfer)
                                    {{ $tx->description ?: 'Transfer Antar Dompet' }}
                                @else
                                    {{ $tx->description ?: ($tx->category->name ?? 'Transaksi') }}
                                @endif
                            </p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 pl-2">
                        <div class="text-right cursor-pointer" onclick="openEditFromDataset(this, event)">
                            <span class="text-sm font-black {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-teal-600 dark:text-teal-400' : 'text-rose-600 dark:text-rose-400') }} block">
                                {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <!-- Quick Delete Button on Dashboard -->
                        <button type="button" 
                                onclick="openDeleteFromDataset(this, event)"
                                aria-label="Hapus transaksi" 
                                class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center text-slate-300 dark:text-slate-600 hover:text-rose-500 dark:hover:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 ios-press transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center space-y-2">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada transaksi</p>
                    <p class="text-xs text-slate-400">Tekan tombol (+) untuk mencatat pengeluaran atau pemasukan baru.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- MODAL TAMBAH DOMPET DARI DASHBOARD (Apple Liquid Glass Sheet) -->
<div id="modal-dashboard-add-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeDashboardAddWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-5 modal-sheet-safe border-t border-white/40 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar text-slate-800 dark:text-slate-100">
            <!-- iOS Grab Handle -->
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeDashboardAddWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Tambah Rekening / Dompet</h3>
                <button type="button" onclick="closeDashboardAddWalletModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center ios-press transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Rekening / Dompet</label>
                    <input type="text" name="name" required placeholder="Contoh: BCA Utama, GoPay, Dompet Tunai" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jenis Akun</label>
                    <select name="type" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        <option value="bank">Rekening Bank (BCA, Mandiri, BNI, dll)</option>
                        <option value="ewallet">E-Wallet (GoPay, OVO, Dana)</option>
                        <option value="cash">Uang Tunai / Cash</option>
                        <option value="investment">Investasi (Bibit, Stockbit, Reksadana)</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Saldo Awal (Rp)</label>
                    <input type="number" name="balance" step="any" min="0" required placeholder="0" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-md shadow-emerald-600/30 ios-press">
                        Simpan Dompet Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let isBalanceHidden = false;
    function toggleBalanceVisibility() {
        isBalanceHidden = !isBalanceHidden;
        const display = document.getElementById('balance-display');
        const hidden = document.getElementById('hidden-balance');
        if (isBalanceHidden) {
            display.classList.add('hidden');
            hidden.classList.remove('hidden');
        } else {
            display.classList.remove('hidden');
            hidden.classList.add('hidden');
        }
    }

    function openDashboardAddWalletModal() {
        window.openSheetModal('modal-dashboard-add-wallet');
    }
    function closeDashboardAddWalletModal() {
        window.closeSheetModal('modal-dashboard-add-wallet');
    }
</script>
@endsection

