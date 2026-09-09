@extends('layouts.mobile')

@section('custom_header')
    <!-- Fintech Header (Emerald in Light Mode, Slate in Dark Mode) -->
    <div class="bg-gradient-to-b from-emerald-600 to-emerald-700 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 border-b border-emerald-700 dark:border-slate-800/80 text-white px-5 pb-8 relative transition-colors" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.75rem));">
        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dashboard') }}" class="min-w-[40px] min-h-[40px] -ml-2 flex items-center justify-center text-white/80 hover:text-white rounded-full hover:bg-white/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 active:scale-95 transition-all" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <h1 class="text-lg font-extrabold text-white tracking-tight leading-tight">
                    Laporan Keuangan
                </h1>
            </div>

            <!-- Header Action / Theme Toggle -->
            <button type="button" 
                    id="theme-toggle-btn"
                    onclick="toggleSwanFlowTheme()" 
                    aria-label="Ganti Tema Gelap atau Terang" 
                    class="min-w-[40px] min-h-[40px] w-10 h-10 flex items-center justify-center rounded-full text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/20 dark:text-slate-300 dark:hover:text-white dark:bg-slate-800/80 dark:border-slate-700/60 dark:hover:bg-slate-700 active:scale-95 transition-all">
                <svg class="w-5 h-5 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="w-5 h-5 block dark:hidden text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </div>

        <!-- Segmented Control Toggle: [ Pemasukan | Pengeluaran ] -->
        <div class="mt-4 flex justify-center">
            <div class="bg-black/20 dark:bg-slate-900/90 p-1 rounded-2xl flex w-full max-w-xs border border-white/20 dark:border-slate-800 shadow-inner backdrop-blur-xs">
                <!-- Pemasukan Option -->
                <a href="{{ route('reports.index', ['month' => $selectedMonth, 'type' => 'income']) }}"
                   class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all {{ $selectedType === 'income' ? 'bg-white text-emerald-700 dark:bg-emerald-500 dark:text-slate-950 font-extrabold shadow-sm' : 'text-white/80 hover:text-white dark:text-slate-400 dark:hover:text-white' }}">
                    Pemasukan
                </a>
                <!-- Pengeluaran Option -->
                <a href="{{ route('reports.index', ['month' => $selectedMonth, 'type' => 'expense']) }}"
                   class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all {{ $selectedType === 'expense' ? 'bg-rose-500 text-white font-extrabold shadow-sm' : 'text-white/80 hover:text-white dark:text-slate-400 dark:hover:text-white' }}">
                    Pengeluaran
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Main Sheet Container (rounded-t-[32px]) -->
<div class="bg-slate-50 dark:bg-slate-900 rounded-t-[32px] pt-5 px-4 pb-[max(6rem,calc(5.25rem+var(--sab,0px)))] shadow-2xl -mt-4 relative z-10 border-t border-slate-200 dark:border-slate-800/80 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Month Picker Filter Pill & Total Summary -->
    <div class="flex items-center justify-between gap-2">
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center" id="report-month-form">
            <input type="hidden" name="type" value="{{ $selectedType }}">
            <div class="relative flex items-center">
                <input type="month" 
                       name="month" 
                       value="{{ $selectedMonth }}" 
                       onchange="document.getElementById('report-month-form').submit()" 
                       class="min-h-[40px] pl-9 pr-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-800 dark:text-slate-100 shadow-2xs focus:outline-hidden focus:border-emerald-500 cursor-pointer">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </form>

        <div class="text-right">
            <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total {{ $selectedType === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span>
            <span class="text-sm font-extrabold {{ $selectedType === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                {{ $selectedType === 'income' ? '+' : '-' }}Rp {{ number_format($targetTotal, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Mini Cashflow Overview Cards (Masuk & Keluar) -->
    <div class="grid grid-cols-2 gap-2.5">
        <div class="p-2.5 rounded-2xl bg-white dark:bg-emerald-950/30 border border-slate-200/80 dark:border-emerald-900/40 shadow-2xs">
            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold">Total Masuk</span>
            <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 block mt-0.5">+Rp {{ number_format($income, 0, ',', '.') }}</span>
        </div>
        <div class="p-2.5 rounded-2xl bg-white dark:bg-rose-950/30 border border-slate-200/80 dark:border-rose-900/40 shadow-2xs">
            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold">Total Keluar</span>
            <span class="text-xs font-extrabold text-rose-600 dark:text-rose-400 block mt-0.5">-Rp {{ number_format($expense, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Interactive / Dynamic SVG Donut / Pie Chart (Screen 3 Matching) -->
    <div class="bg-white dark:bg-slate-800/40 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800/60 shadow-2xs">
        @if($categoryBreakdown->isNotEmpty() && $targetTotal > 0)
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <!-- Left: Donut Chart -->
                <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                    <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 42 42">
                        <!-- Base background circle -->
                        <circle cx="21" cy="21" r="15.9155" fill="none" stroke="currentColor" stroke-width="6.5" class="text-slate-100 dark:text-slate-700/50"></circle>
                        
                        @php
                            $cumulativePercent = 0;
                        @endphp
                        @foreach($categoryBreakdown as $cat)
                            @php
                                $dashArray = $cat->percentage . ' ' . (100 - $cat->percentage);
                                $dashOffset = - $cumulativePercent;
                                $cumulativePercent += $cat->percentage;
                            @endphp
                            <circle cx="21" cy="21" r="15.9155" 
                                    fill="none" 
                                    stroke="{{ $cat->chartColor }}" 
                                    stroke-width="6.5" 
                                    stroke-dasharray="{{ $dashArray }}" 
                                    stroke-dashoffset="{{ $dashOffset }}" 
                                    stroke-linecap="round"
                                    class="transition-all duration-700"></circle>
                        @endforeach
                    </svg>

                    <!-- Center Text Inside Donut Hole -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none px-1">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            {{ $selectedType === 'income' ? 'Masuk' : 'Keluar' }}
                        </span>
                        <span class="text-[11px] font-extrabold text-slate-900 dark:text-white px-1 truncate max-w-[85px]">
                            Rp {{ number_format($targetTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Right: Vertical Category Legend (Screen 3 Matching) -->
                <div class="flex-1 w-full min-w-0 space-y-2.5 max-h-56 overflow-y-auto no-scrollbar">
                    @foreach($categoryBreakdown as $cat)
                        <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-3 h-3 rounded-full shrink-0 shadow-2xs" style="background-color: {{ $cat->chartColor }};"></span>
                                <span class="text-xs font-bold text-slate-800 dark:text-white truncate">
                                    {{ $cat->name }}
                                </span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold text-slate-900 dark:text-slate-100 block">
                                    {{ $cat->percentage }}%
                                </span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block">
                                    Rp {{ number_format($cat->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State for Chart -->
            <div class="py-12 text-center space-y-2">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 3.75a6.75 6.75 0 00-6.75 6.75v.75a6.75 6.75 0 006.75 6.75h.75a6.75 6.75 0 006.75-6.75v-.75a6.75 6.75 0 00-6.75-6.75h-.75z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada data {{ $selectedType === 'income' ? 'pemasukan' : 'pengeluaran' }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">Catat transaksi di periode ini untuk melihat grafik komposisi.</p>
            </div>
        @endif
    </div>

    <!-- Screen 3: Sub-section Transaksi (Matching Reference Screen 3 bottom list) -->
    <div class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Transaksi</h2>
            <a href="{{ route('transactions.index', ['month' => $selectedMonth, 'type' => $selectedType]) }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Lihat semua
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse($monthTransactions as $tx)
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
                     onclick="openEditFromDataset(this, event)"
                     role="button"
                     tabindex="0"
                     class="p-3 bg-white dark:bg-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-800/80 active:scale-[0.99] flex items-center justify-between rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs cursor-pointer transition-all">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Icon Square Container (No blue) -->
                        <div class="w-11 h-11 rounded-2xl {{ $isIncome ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40' : ($isTransfer ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-400 border border-teal-200 dark:border-teal-900/40' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40') }} flex items-center justify-center shrink-0 shadow-2xs">
                            <x-category-icon :category="$tx->category" :type="$tx->type" :name="$tx->description ?: ($tx->category->name ?? '')" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                {{ $tx->description ?: ($tx->category->name ?? 'Transaksi') }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }} • {{ $tx->wallet->name ?? 'Dompet' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 pl-2">
                        <span class="text-sm font-extrabold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-slate-700 dark:text-slate-300' : 'text-rose-600 dark:text-rose-400') }} block">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center space-y-1 bg-white dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada transaksi di periode ini</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Transaksi baru akan otomatis tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
