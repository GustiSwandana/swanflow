@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass Header -->
    <div class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-emerald-950/90 to-slate-950/95 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-all" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Rim -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-teal-500/20 rounded-full blur-3xl pointer-events-none animate-liquid-orb-2"></div>

        <!-- Top Navigation Bar -->
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                        Laporan Keuangan
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Analisis arus kas & komposisi</span>
                </div>
            </div>

            <!-- Header Action / Theme Toggle -->
            <button type="button" 
                    id="theme-toggle-btn"
                    onclick="toggleSwanFlowTheme()" 
                    aria-label="Ganti Tema Gelap atau Terang" 
                    class="w-10 h-10 flex items-center justify-center rounded-[18px] liquid-glass text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/30 dark:bg-slate-800/80 dark:border-white/10 active:scale-95 transition-all shadow-xs ios-press">
                <svg class="w-5 h-5 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="w-5 h-5 block dark:hidden text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </div>

        <!-- Segmented Control Toggle: [ Pemasukan | Pengeluaran ] -->
        <div class="mt-4.5 flex justify-center relative z-10">
            <div class="ios-segmented-track p-1 rounded-[22px] flex w-full max-w-xs bg-black/30 backdrop-blur-xl border border-white/20 shadow-inner">
                <!-- Pemasukan Option -->
                <a href="{{ route('reports.index', ['month' => $selectedMonth, 'type' => 'income']) }}"
                   class="ios-press flex-1 py-2 text-center text-xs font-black rounded-[18px] transition-all {{ $selectedType === 'income' ? 'ios-segmented-thumb bg-white text-emerald-800 shadow-md ring-1 ring-black/5' : 'text-white/75 hover:text-white' }}">
                    Pemasukan
                </a>
                <!-- Pengeluaran Option -->
                <a href="{{ route('reports.index', ['month' => $selectedMonth, 'type' => 'expense']) }}"
                   class="ios-press flex-1 py-2 text-center text-xs font-black rounded-[18px] transition-all {{ $selectedType === 'expense' ? 'ios-segmented-thumb bg-rose-500 text-white shadow-md ring-1 ring-black/5' : 'text-white/75 hover:text-white' }}">
                    Pengeluaran
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Main Sheet Container (Apple connected sheet rounded-t-[36px]) -->
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-white/60 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- Month Picker Filter Pill & Total Summary -->
    <div class="flex items-center justify-between gap-3">
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center" id="report-month-form">
            <input type="hidden" name="type" value="{{ $selectedType }}">
            <div class="relative flex items-center">
                <input type="month" 
                       name="month" 
                       value="{{ $selectedMonth }}" 
                       onchange="document.getElementById('report-month-form').submit()" 
                       class="min-h-[42px] pl-10 pr-4 py-2 bg-white/80 dark:bg-slate-900/80 border border-white/60 dark:border-white/10 rounded-[20px] text-xs font-black text-slate-800 dark:text-slate-100 shadow-xs backdrop-blur-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </form>

        <div class="text-right">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Total {{ $selectedType === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span>
            <span class="text-base font-black {{ $selectedType === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} tracking-tight">
                {{ $selectedType === 'income' ? '+' : '-' }}Rp {{ number_format($targetTotal, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Mini Cashflow Overview Cards (Masuk & Keluar) -->
    <div class="grid grid-cols-2 gap-3">
        <div class="liquid-card p-3.5 rounded-[22px] bg-white/80 dark:bg-emerald-950/20 border border-white/60 dark:border-emerald-900/30 shadow-xs backdrop-blur-2xl">
            <span class="text-[10px] text-slate-400 dark:text-slate-500 block font-bold uppercase tracking-wider">Total Masuk</span>
            <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 block mt-0.5">+Rp {{ number_format($income, 0, ',', '.') }}</span>
        </div>
        <div class="liquid-card p-3.5 rounded-[22px] bg-white/80 dark:bg-rose-950/20 border border-white/60 dark:border-rose-900/30 shadow-xs backdrop-blur-2xl">
            <span class="text-[10px] text-slate-400 dark:text-slate-500 block font-bold uppercase tracking-wider">Total Keluar</span>
            <span class="text-sm font-black text-rose-600 dark:text-rose-400 block mt-0.5">-Rp {{ number_format($expense, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Interactive / Dynamic SVG Donut / Pie Chart (Apple Liquid Glass) -->
    <div class="liquid-card bg-white/80 dark:bg-slate-900/75 rounded-[30px] p-6 border border-white/60 dark:border-white/10 shadow-sm backdrop-blur-2xl">
        @if($categoryBreakdown->isNotEmpty() && $targetTotal > 0)
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Left: Donut Chart -->
                <div class="relative w-40 h-40 shrink-0 flex items-center justify-center">
                    <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 42 42">
                        <!-- Base background circle -->
                        <circle cx="21" cy="21" r="15.9155" fill="none" stroke="currentColor" stroke-width="6.5" class="text-slate-100 dark:text-slate-800/80"></circle>
                        
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
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none px-2">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            {{ $selectedType === 'income' ? 'Masuk' : 'Keluar' }}
                        </span>
                        <span class="text-[12px] font-black text-slate-900 dark:text-white px-1 truncate max-w-[95px]">
                            Rp {{ number_format($targetTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Right: Vertical Category Legend -->
                <div class="flex-1 w-full min-w-0 space-y-2.5 max-h-56 overflow-y-auto no-scrollbar">
                    @foreach($categoryBreakdown as $cat)
                        <div class="flex items-center justify-between gap-2.5 p-2 rounded-[16px] hover:bg-slate-100/60 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-xs ring-2 ring-white dark:ring-slate-800" style="background-color: {{ $cat->chartColor }};"></span>
                                <span class="text-xs font-bold text-slate-800 dark:text-white truncate">
                                    {{ $cat->name }}
                                </span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-black text-slate-900 dark:text-slate-100 block">
                                    {{ $cat->percentage }}%
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 block">
                                    Rp {{ number_format($cat->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State for Chart -->
            <div class="py-12 text-center space-y-2.5">
                <div class="w-14 h-14 mx-auto rounded-[22px] liquid-glass flex items-center justify-center text-slate-400 dark:text-slate-500 border border-white/60 dark:border-white/10 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 3.75a6.75 6.75 0 00-6.75 6.75v.75a6.75 6.75 0 006.75 6.75h.75a6.75 6.75 0 006.75-6.75v-.75a6.75 6.75 0 00-6.75-6.75h-.75z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum ada data {{ $selectedType === 'income' ? 'pemasukan' : 'pengeluaran' }}</p>
                <p class="text-xs font-medium text-slate-400 dark:text-slate-500">Catat transaksi di periode ini untuk melihat grafik komposisi.</p>
            </div>
        @endif
    </div>

    <!-- Sub-section Transaksi -->
    <div class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Rincian Transaksi</h2>
                <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Transaksi pada periode {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('transactions.index', ['month' => $selectedMonth, 'type' => $selectedType]) }}" class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 hover:underline">
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
                     class="liquid-card p-3.5 bg-white/80 dark:bg-slate-900/75 hover:bg-white dark:hover:bg-slate-800/80 flex items-center justify-between rounded-[24px] border border-white/60 dark:border-white/10 shadow-xs cursor-pointer transition-all ios-press">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Icon Square Container -->
                        <div class="w-11 h-11 rounded-[18px] {{ $isIncome ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : ($isTransfer ? 'bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/20' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20') }} flex items-center justify-center shrink-0 shadow-xs">
                            <x-category-icon :category="$tx->category" :type="$tx->type" :name="$tx->description ?: ($tx->category->name ?? '')" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-slate-800 dark:text-white truncate">
                                {{ $tx->description ?: ($tx->category->name ?? 'Transaksi') }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate font-medium">
                                {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }} • {{ $tx->wallet->name ?? 'Dompet' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 pl-2">
                        <span class="text-sm font-black {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-slate-700 dark:text-slate-300' : 'text-rose-600 dark:text-rose-400') }} block">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center space-y-2 liquid-card bg-white/80 dark:bg-slate-900/75 rounded-[26px] border border-white/60 dark:border-white/10">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada transaksi di periode ini</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Transaksi baru akan otomatis tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

