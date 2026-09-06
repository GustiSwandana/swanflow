@extends('layouts.mobile')

@section('custom_header')
    <!-- Fintech Full-Bleed Header (Emerald in Light Mode, Sleek Slate in Dark Mode) -->
    <div class="bg-gradient-to-b from-emerald-600 via-emerald-600 to-emerald-700 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-white px-5 pb-8 relative border-b border-emerald-700 dark:border-slate-800/80 overflow-hidden transition-colors" style="padding-top: max(3rem, calc(var(--sat, 0px) + 0.75rem));">
        <!-- Subtle Glow & Mesh Highlights -->
        <div class="absolute -right-8 -top-8 w-44 h-44 bg-white/10 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-8 top-20 w-40 h-40 bg-white/10 dark:bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Top Bar: Greeting & Avatar -->
        <div class="relative z-10 flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <x-app-logo class="w-8 h-8" variant="badge" />
                <div>
                    <span class="text-[11px] font-semibold text-emerald-200/90 dark:text-slate-400 block -mb-0.5">SwanFlow</span>
                    <h1 class="text-lg font-extrabold text-white tracking-tight flex items-center gap-1.5">
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
                        class="min-w-[40px] min-h-[40px] w-10 h-10 flex items-center justify-center rounded-full text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/20 dark:text-slate-400 dark:hover:text-white dark:bg-slate-800/80 dark:hover:bg-slate-700/80 dark:border-slate-700/60 active:scale-95 transition-all">
                    <svg class="w-5 h-5 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg class="w-5 h-5 block dark:hidden text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
                <!-- Notification / History -->
                <a href="{{ route('transactions.index') }}" aria-label="Notifikasi" class="min-w-[40px] min-h-[40px] w-10 h-10 flex items-center justify-center rounded-full text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/20 dark:text-slate-400 dark:hover:text-white dark:bg-slate-800/80 dark:hover:bg-slate-700/80 dark:border-slate-700/60 active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </a>
                <!-- Circular Avatar -->
                <a href="{{ route('profile.edit') }}" class="min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Profil Pengguna">
                    <div class="w-10 h-10 rounded-full bg-white/20 dark:bg-slate-800 text-white font-extrabold text-sm flex items-center justify-center ring-2 ring-white/30 dark:ring-slate-700 border border-white/40 dark:border-slate-600 shadow-xs">
                        GS
                    </div>
                </a>
            </div>
        </div>

        <!-- Balance Section (Uang kamu tersisa) -->
        <div class="relative z-10 mb-5">
            <div class="flex items-center gap-2">
                <span class="text-xs text-emerald-100 dark:text-slate-400 font-medium">Uang kamu tersisa</span>
                <span class="sr-only">Total Saldo Aktif</span>
                <button type="button" 
                        onclick="toggleBalanceVisibility()" 
                        aria-label="Sembunyikan Saldo" 
                        class="text-emerald-100 hover:text-white dark:text-slate-400 dark:hover:text-white active:scale-95 transition-all">
                    <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            <p id="balance-display" class="text-3xl font-extrabold tracking-tight text-white font-sans mt-0.5">
                Rp {{ number_format($totalBalance ?? 0, 0, ',', '.') }}
            </p>
            <p id="hidden-balance" class="text-3xl font-extrabold tracking-tight text-white font-sans mt-0.5 hidden">
                ••••••••••
            </p>
        </div>

        <!-- Floating Cards: Pemasukan ↗ & Pengeluaran ↘ (Clickable) -->
        <div class="relative z-10 grid grid-cols-2 gap-3 mb-5">
            <!-- Pemasukan Card (Click to view income transactions, or click (+) to add income) -->
            <a href="{{ route('transactions.index', ['type' => 'income']) }}"
               class="bg-white/95 dark:bg-slate-900/90 rounded-2xl p-3.5 shadow-md border border-white/20 dark:border-slate-800 flex flex-col justify-between backdrop-blur-md active:scale-95 transition-all group hover:border-emerald-500/40 hover:shadow-lg cursor-pointer block"
               title="Klik untuk lihat riwayat pemasukan">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Pemasukan</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                onclick="event.preventDefault(); event.stopPropagation(); openTransactionModal('income')"
                                class="w-6 h-6 rounded-full bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 flex items-center justify-center active:scale-90 transition-all shadow-2xs cursor-pointer"
                                title="Catat Pemasukan Baru">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">↗</span>
                    </div>
                </div>
                <p class="text-sm sm:text-base font-extrabold text-emerald-600 dark:text-emerald-400 truncate mt-1">
                    +Rp {{ number_format($thisMonthIncome ?? 0, 0, ',', '.') }}
                </p>
                <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium pt-1 border-t border-slate-100 dark:border-slate-800/60">
                    <span>Lihat Rincian</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </div>
            </a>

            <!-- Pengeluaran Card (Click to view expense transactions, or click (+) to add expense) -->
            <a href="{{ route('transactions.index', ['type' => 'expense']) }}"
               class="bg-white/95 dark:bg-slate-900/90 rounded-2xl p-3.5 shadow-md border border-white/20 dark:border-slate-800 flex flex-col justify-between backdrop-blur-md active:scale-95 transition-all group hover:border-rose-500/40 hover:shadow-lg cursor-pointer block"
               title="Klik untuk lihat riwayat pengeluaran">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">Pengeluaran</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                onclick="event.preventDefault(); event.stopPropagation(); openTransactionModal('expense')"
                                class="w-6 h-6 rounded-full bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/80 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 flex items-center justify-center active:scale-90 transition-all shadow-2xs cursor-pointer"
                                title="Catat Pengeluaran Baru">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                        <span class="text-rose-600 dark:text-rose-400 font-bold text-sm">↘</span>
                    </div>
                </div>
                <p class="text-sm sm:text-base font-extrabold text-rose-600 dark:text-rose-400 truncate mt-1">
                    -Rp {{ number_format($thisMonthExpense ?? 0, 0, ',', '.') }}
                </p>
                <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium pt-1 border-t border-slate-100 dark:border-slate-800/60">
                    <span>Lihat Rincian</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </div>
            </a>
        </div>

        <!-- Anggaran Pengeluaran (Horizontal Donut Progress Cards matching Reference Screen 1) -->
        <div class="relative z-10 space-y-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white dark:text-slate-200 tracking-wide">Anggaran Pengeluaran</h3>
                <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-emerald-100 hover:text-white dark:text-emerald-400 dark:hover:text-emerald-300 underline-offset-2 hover:underline">Kelola & Detail</a>
            </div>
            <div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
                @forelse($categoryBudgets as $budget)
                    <a href="{{ route('budgets.index') }}" class="bg-white/95 dark:bg-slate-900/90 rounded-2xl p-3 min-w-[110px] flex flex-col items-center justify-center text-center shadow-md border border-white/20 dark:border-slate-800 shrink-0 backdrop-blur-md active:scale-95 transition-all group">
                        <!-- Circular Donut SVG Ring with Category Icon in Center -->
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
                        <span class="text-[10px] font-semibold {{ $budget->percent >= 100 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }} mt-0.5">
                            {{ $budget->percent ?? 0 }}%
                        </span>
                    </a>
                @empty
                    <div class="w-full py-4 px-3 text-center text-xs text-white/90 dark:text-slate-400 bg-white/15 dark:bg-slate-900/60 rounded-2xl border border-white/20 dark:border-slate-800">
                        Belum ada anggaran bulanan. <a href="{{ route('budgets.index') }}" class="underline font-bold text-white dark:text-emerald-400 ml-1">+ Atur Anggaran</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Bottom Sheet Container (rounded-t-[32px]) -->
<div class="bg-slate-50 dark:bg-slate-900 rounded-t-[32px] pt-4 px-4 pb-28 shadow-2xl -mt-4 relative z-10 border-t border-slate-200 dark:border-slate-800/80 flex-1 flex flex-col min-h-full space-y-6 text-slate-800 dark:text-white transition-colors">
    <!-- Drag Pill Indicator -->
    <div class="w-12 h-1 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-2"></div>

    <!-- 1. Quick Navigation Shortcuts (scrollable horizontal) -->
    <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1 pt-1 -mx-1 px-1">
        <!-- Scan Struk (AI / OCR) -->
        <button type="button" onclick="openReceiptScannerModal()" class="flex flex-col items-center gap-1.5 group shrink-0 w-14 cursor-pointer" title="Pindai Struk / Bukti Transfer">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500/20 via-emerald-500/25 to-teal-500/20 text-teal-700 dark:text-teal-300 flex items-center justify-center group-hover:from-teal-500/30 group-hover:to-emerald-500/40 active:scale-90 transition-all border border-teal-500/30 dark:border-teal-700/50 shadow-xs relative ring-2 ring-teal-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                </svg>
                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-teal-500"></span>
                </span>
            </div>
            <span class="text-[10px] font-bold text-teal-600 dark:text-teal-400 text-center leading-tight">Scan Struk</span>
        </button>

        <!-- Dompet -->
        <a href="{{ route('wallets.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 active:scale-90 transition-all border border-emerald-200 dark:border-emerald-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5m-1.5 0h18" /></svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Dompet</span>
        </a>

        <!-- SwanDrive (File Storage & Portal Drop) -->
        <a href="{{ route('drive.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14 relative">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 dark:bg-teal-950/40 dark:text-teal-400 flex items-center justify-center group-hover:bg-teal-200 dark:group-hover:bg-teal-900/50 active:scale-90 transition-all border border-teal-200 dark:border-teal-900/40 shadow-sm relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                </svg>
                @if(($activeLinksCount ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-emerald-500 text-white text-[10px] font-extrabold flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900 leading-none" title="{{ $activeLinksCount }} Link Aktif">
                        {{ $activeLinksCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-bold text-teal-700 dark:text-teal-400 text-center leading-tight">Drive</span>
        </a>

        <!-- Aktivitas -->
        <a href="{{ route('todos.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14 relative">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 active:scale-90 transition-all border border-emerald-200 dark:border-emerald-900/40 shadow-xs relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                @if(($pendingTodosCount ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-extrabold flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900 leading-none">
                        {{ $pendingTodosCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Aktivitas</span>
        </a>

        <!-- Kalkulator & Patungan -->
        <a href="{{ route('calculator.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 dark:bg-teal-950/40 dark:text-teal-400 flex items-center justify-center group-hover:bg-teal-200 dark:group-hover:bg-teal-900/50 active:scale-90 transition-all border border-teal-200 dark:border-teal-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm3-6h.008v.008H11.25v-.008zm0 3h.008v.008H11.25v-.008zm0 3h.008v.008H11.25v-.008zm3-6h.008v.008H14.25v-.008zm0 3h.008v.008H14.25v-.008zM4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Kalkulator</span>
        </a>

        <!-- Anggaran -->
        <a href="{{ route('budgets.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 dark:bg-teal-950/40 dark:text-teal-400 flex items-center justify-center group-hover:bg-teal-200 dark:group-hover:bg-teal-900/50 active:scale-90 transition-all border border-teal-200 dark:border-teal-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Anggaran</span>
        </a>

        <!-- Kategori -->
        <a href="{{ route('categories.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 active:scale-90 transition-all border border-purple-200 dark:border-purple-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Kategori</span>
        </a>

        <!-- Langganan -->
        <a href="{{ route('subscriptions.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 flex items-center justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 active:scale-90 transition-all border border-indigo-200 dark:border-indigo-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Langganan</span>
        </a>

        <!-- Utang/Piutang -->
        <a href="{{ route('debts.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 flex items-center justify-center group-hover:bg-amber-200 dark:group-hover:bg-amber-900/50 active:scale-90 transition-all border border-amber-200 dark:border-amber-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Utang</span>
        </a>

        <!-- Laporan -->
        <a href="{{ route('reports.index') }}" class="flex flex-col items-center gap-1.5 group shrink-0 w-14">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 flex items-center justify-center group-hover:bg-rose-200 dark:group-hover:bg-rose-900/50 active:scale-90 transition-all border border-rose-200 dark:border-rose-900/40 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 text-center leading-tight">Laporan</span>
        </a>
    </div>

    <!-- BANNER SCAN STRUK & BUKTI PEMBAYARAN -->
    <div onclick="openReceiptScannerModal()" class="bg-gradient-to-r from-teal-500/15 via-emerald-500/10 to-teal-500/15 dark:from-teal-950/50 dark:via-emerald-950/30 dark:to-teal-950/50 rounded-2xl p-3.5 border border-teal-300/70 dark:border-teal-800/70 shadow-2xs flex items-center justify-between cursor-pointer active:scale-98 transition-all group">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-2xl bg-teal-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-teal-500/30 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white truncate">Pindai Struk & Bukti Transfer</h3>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-md bg-teal-500/20 text-teal-700 dark:text-teal-300">AI</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                    Foto nota belanja atau bukti transfer untuk pencatatan otomatis
                </p>
            </div>
        </div>
        <span class="px-2.5 py-1.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs shadow-xs active:scale-95 transition-all shrink-0 ml-2">
            Pindai ➔
        </span>
    </div>

    <!-- 2. DOMPET & REKENING (Dynamic Wallets List) -->
    <div class="space-y-2.5 pt-2 border-t border-slate-200 dark:border-slate-800/80">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Dompet & Rekening</h2>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    ({{ count($wallets ?? []) }})
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('wallets.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Kelola
                </a>
                <button type="button" 
                        onclick="openDashboardAddWalletModal()" 
                        class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-[11px] active:scale-95 transition-all">
                    + Tambah
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2.5 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
            @forelse($wallets ?? [] as $wallet)
                <a href="{{ route('wallets.index') }}" class="min-w-[125px] bg-white dark:bg-slate-800/60 p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col justify-between min-h-[86px] active:scale-98 hover:border-emerald-500/50 transition-all shrink-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $wallet->type }}</span>
                        <span class="w-2 h-2 rounded-full {{ $wallet->type === 'bank' ? 'bg-emerald-500' : ($wallet->type === 'ewallet' ? 'bg-cyan-500' : 'bg-amber-500') }}"></span>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $wallet->name }}</h3>
                        <p class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 mt-0.5">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="w-full py-4 text-center text-xs text-slate-400 bg-white dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800">
                    Belum ada dompet terdaftar.
                </div>
            @endforelse

            <button type="button" 
                    onclick="openDashboardAddWalletModal()" 
                    class="min-w-[100px] min-h-[86px] rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 flex flex-col items-center justify-center gap-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 shrink-0 active:scale-95 transition-all cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <span class="text-[10px] font-bold">+ Dompet</span>
            </button>
        </div>
    </div>

    <!-- PINTASAN SWANDRIVE VAULT -->
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-2.5 transition-colors">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">SwanDrive Vault</h2>
            </div>
            <a href="{{ route('drive.index') }}" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                Buka Drive ➔
            </a>
        </div>
        <div class="flex items-center justify-between p-3 rounded-xl bg-teal-50/70 dark:bg-teal-950/30 border border-teal-200/70 dark:border-teal-900/40">
            <a href="{{ route('drive.index') }}" class="flex items-center gap-3 min-w-0 pr-2 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/15 dark:bg-teal-500/25 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
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
                <a href="{{ route('drive.index', ['tab' => 'drops']) }}" class="px-2.5 py-1.5 rounded-lg bg-teal-500/15 hover:bg-teal-500/25 text-teal-700 dark:text-teal-300 font-bold text-[11px] border border-teal-400/30 active:scale-95 transition-all" title="Kelola Link Drop">
                    Link Drop
                </a>
                <a href="{{ route('drive.index') }}" class="px-3 py-1.5 rounded-lg bg-teal-500 hover:bg-teal-600 text-white font-bold text-[11px] shadow-xs active:scale-95 transition-all">
                    Buka
                </a>
            </div>
        </div>
    </div>

    <!-- 3. WIDGET TAGIHAN BERLANGGANAN (Upcoming Subscriptions) -->
    @if(isset($upcomingSubscriptions) && $upcomingSubscriptions->isNotEmpty())
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 space-y-2.5 transition-colors shadow-2xs">
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
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700/60 text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8 h-8 rounded-lg bg-indigo-500/15 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
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

    <!-- 4. WIDGET UTANG & PIUTANG (Debts Summary) -->
    @if(isset($debtsSummary) && $debtsSummary['unpaidCount'] > 0)
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 space-y-2.5 transition-colors shadow-2xs">
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
                <div class="p-2.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-900/40">
                    <span class="text-[10px] text-slate-600 dark:text-slate-400 block font-semibold">Hak Piutang</span>
                    <span class="text-xs font-extrabold text-emerald-700 dark:text-emerald-400 block mt-0.5 truncate">
                        Rp {{ number_format($debtsSummary['receivables'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-2.5 rounded-xl bg-rose-50/80 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/40">
                    <span class="text-[10px] text-slate-600 dark:text-slate-400 block font-semibold">Beban Utang</span>
                    <span class="text-xs font-extrabold text-rose-700 dark:text-rose-400 block mt-0.5 truncate">
                        Rp {{ number_format($debtsSummary['debts'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- WIDGET AKTIVITAS HARI INI -->
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 space-y-2.5 transition-colors shadow-2xs">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Aktivitas Hari Ini</h2>
                @if(isset($todayTodos) && $todayTodos->count() > 0)
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
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
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700/60 border-l-[3px] {{ $accent }} text-xs">
                        <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                aria-label="{{ $todo->is_completed ? 'Tandai belum selesai' : 'Tandai selesai' }}"
                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all cursor-pointer shrink-0
                                    {{ $todo->is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 dark:border-slate-600 hover:border-emerald-400' }}">
                                @if($todo->is_completed)
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
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
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/70 dark:border-slate-700/60 flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada aktivitas untuk hari ini</span>
                <a href="{{ route('todos.index') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                    + Catat
                </a>
            </div>
        @endif
    </div>

    <!-- 5. SECTION TRANSAKSI TERAKHIR (Ditaruh Paling Bawah Sesuai Permintaan) -->
    <div class="space-y-3 pt-2 border-t border-slate-200 dark:border-slate-800/80">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Transaksi Terakhir</h2>
            <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Lihat semua
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
                    class="p-3 flex items-center justify-between rounded-2xl bg-white dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800 active:bg-slate-200/70 dark:active:bg-slate-700/60 cursor-pointer transition-colors border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Rounded Icon Box with Category Icon (No Blue) -->
                        <div class="w-12 h-12 rounded-2xl {{ $isIncome ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40' : ($isTransfer ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-400 border border-teal-200 dark:border-teal-900/40' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/60') }} flex items-center justify-center shrink-0 shadow-xs">
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
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 pl-2">
                        <span class="text-sm font-extrabold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-teal-600 dark:text-teal-400' : 'text-rose-600 dark:text-rose-400') }} block">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center space-y-2 bg-white dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada transaksi</p>
                    <p class="text-xs text-slate-500">Tekan tombol (+) untuk mencatat pengeluaran atau pemasukan baru.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- MODAL TAMBAH DOMPET DARI DASHBOARD -->
<div id="modal-dashboard-add-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="closeDashboardAddWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-200 dark:border-slate-800 pointer-events-auto overflow-y-auto no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeDashboardAddWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Tambah Rekening / Dompet Baru</h3>
                <button type="button" onclick="closeDashboardAddWalletModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">✕</button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Rekening / Dompet</label>
                    <input type="text" name="name" required placeholder="Contoh: BCA Utama, GoPay, Dompet Tunai" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jenis Akun</label>
                    <select name="type" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        <option value="bank">Rekening Bank (BCA, Mandiri, BNI, dll)</option>
                        <option value="ewallet">E-Wallet (GoPay, OVO, Dana)</option>
                        <option value="cash">Uang Tunai / Cash</option>
                        <option value="investment">Investasi (Bibit, Stockbit, Reksadana)</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Saldo Awal (Rp)</label>
                    <input type="number" name="balance" step="any" min="0" required placeholder="0" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30">
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
        document.getElementById('modal-dashboard-add-wallet').classList.remove('hidden');
    }
    function closeDashboardAddWalletModal() {
        document.getElementById('modal-dashboard-add-wallet').classList.add('hidden');
    }
</script>
@endsection
