@extends('layouts.mobile')

@section('header_left')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="min-w-[42px] min-h-[42px] w-10.5 h-10.5 flex items-center justify-center text-slate-700 dark:text-slate-200 rounded-[18px] liquid-glass border border-white/60 dark:border-white/10 ios-press transition-all shadow-xs" aria-label="Kembali ke Dashboard">
            <svg class="w-5 h-5 text-slate-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div class="flex flex-col">
            <h1 class="text-base font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                Investasi & Portofolio
            </h1>
            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Kelola aset, modal & pertumbuhan uang</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-5">

    <!-- 1. HERO CARD: TOTAL DANA TERSIMPAN & MUTASI SALDO (APPLE WALLET LIQUID GLASS) -->
    <div class="relative overflow-hidden rounded-[32px] p-6 text-white shadow-2xl border border-white/20 dark:border-white/10 backdrop-blur-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Specular highlight line -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/40 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -left-8 -top-8 w-32 h-32 bg-teal-500/15 rounded-full blur-2xl pointer-events-none animate-liquid-orb-2"></div>

        <div class="relative z-10 space-y-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] font-bold text-emerald-300 uppercase tracking-wider">Total Dana Tersimpan</span>
                </div>
                <span class="px-2.5 py-1 rounded-[14px] text-[10px] font-extrabold bg-white/10 text-slate-200 border border-white/10 backdrop-blur-xs">
                    {{ $activeCount }} Catatan Aktif
                </span>
            </div>

            <!-- Large Total Value -->
            <div>
                <p class="text-3xl font-black text-white tracking-tight">
                    Rp {{ number_format($totalPortfolioValue, 0, ',', '.') }}
                </p>
                <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Akumulasi seluruh saldo tabungan & aset investasi</p>
            </div>

            <!-- 2-Column Metric Grid: Total Disetor vs Total Ditarik -->
            <div class="grid grid-cols-2 gap-2.5 pt-3 border-t border-white/10">
                <!-- Total Setor (Uang Masuk) -->
                <div class="bg-white/10 dark:bg-white/5 rounded-[18px] p-2.5 border border-white/10 backdrop-blur-md">
                    <span class="text-[10px] font-semibold text-slate-300 block">Total Disetor (Masuk)</span>
                    <p class="text-xs sm:text-sm font-black text-emerald-300 mt-0.5 truncate">
                        +Rp {{ number_format($totalDeposited, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Total Ditarik (Uang Keluar) -->
                <div class="bg-white/10 dark:bg-white/5 rounded-[18px] p-2.5 border border-white/10 backdrop-blur-md">
                    <span class="text-[10px] font-semibold text-slate-300 block">Total Ditarik (Keluar)</span>
                    <p class="text-xs sm:text-sm font-black text-amber-300 mt-0.5 truncate">
                        -Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. FILTER CAROUSEL & ADD BUTTON -->
    <div class="flex items-center justify-between gap-2.5 pt-0.5">
        <!-- Scrollable Type Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
            @php
                $types = [
                    'all' => 'Semua',
                    'mutual_fund' => 'Reksadana',
                    'stock' => 'Saham',
                    'crypto' => 'Kripto',
                    'gold' => 'Emas',
                    'deposit' => 'Deposito',
                    'bond' => 'SBN',
                ];
            @endphp
            @foreach($types as $key => $label)
                <a href="{{ route('investments.index', ['type' => $key, 'status' => $currentStatus]) }}" 
                   class="min-h-[38px] px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $currentType === $key ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25 border border-emerald-500' : 'liquid-glass text-slate-600 dark:text-slate-300 border border-white/60 dark:border-white/10' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Tambah Investasi Button -->
        <button type="button" 
                onclick="openSheetModal('modal-add-investment')" 
                class="min-h-[42px] px-4 py-2 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-lg shadow-emerald-500/25 border border-white/20 flex items-center gap-2 shrink-0 active:scale-95 transition-all cursor-pointer ios-press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah</span>
        </button>
    </div>

    <!-- 3. INVESTMENTS LIST -->
    <div class="space-y-3">
        @forelse($investments as $item)
            @php
                $isProfit = $item->is_profit;
                $pnl = $item->profit_loss;
                $roi = $item->roi_percentage;
                $badge = $item->type_badge;
                $isClosed = $item->status === 'closed';
            @endphp
            <div class="liquid-card rounded-[26px] p-4.5 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm hover:shadow-md backdrop-blur-2xl space-y-3.5 transition-all {{ $isClosed ? 'opacity-70' : '' }}">
                <!-- Header: Icon, Name, Platform, Type Badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Type Icon Box -->
                        <div class="w-12 h-12 rounded-[20px] flex items-center justify-center font-bold text-xs shrink-0 shadow-xs {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                            @if($item->type === 'stock')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                            @elseif($item->type === 'crypto')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($item->type === 'gold')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                            @elseif($item->type === 'deposit')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5m-1.5 0h18" /></svg>
                            @elseif($item->type === 'bond')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>
                            @endif
                        </div>

                        <!-- Name & Platform -->
                        <div class="min-w-0">
                            <h3 class="text-sm font-extrabold text-slate-800 dark:text-white truncate leading-snug">
                                {{ $item->name }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                @if($item->platform)
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-[8px] bg-slate-100/80 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700/50">
                                        {{ $item->platform }}
                                    </span>
                                @endif
                                <span class="text-[10px] font-semibold text-slate-400">
                                    {{ $item->type_label }}
                                </span>
                                @if($item->wallet)
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" /></svg>
                                        {{ $item->wallet->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        @if($isClosed)
                            <span class="px-2.5 py-0.5 rounded-[12px] text-[9px] font-black bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200/60 dark:border-slate-700/60">
                                Ditutup
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-[12px] text-[9px] font-black bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Saldo Terkumpul & Ringkasan Mutasi (Pendataan Bersih) -->
                <div class="p-4 bg-slate-100/60 dark:bg-slate-800/50 rounded-[20px] border border-slate-200/60 dark:border-slate-700/50 space-y-2.5 backdrop-blur-xs">
                    <div class="flex items-baseline justify-between gap-2">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Saldo Terkumpul</span>
                        <span class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                            Rp {{ number_format($item->current_value, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] pt-2.5 border-t border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">↓ Setor:</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">Rp {{ number_format($item->total_deposited, 0, ',', '.') }}</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="text-amber-600 dark:text-amber-400 font-bold">↑ Tarik:</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">Rp {{ number_format($item->total_withdrawn, 0, ',', '.') }}</span>
                        </span>
                    </div>
                </div>

                <!-- Target Progress Bar (If target_amount is set) -->
                @if($item->target_amount && $item->target_amount > 0)
                    @php
                        $targetPercent = min(100, round(($item->current_value / $item->target_amount) * 100));
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                            <span>Target: Rp {{ number_format($item->target_amount, 0, ',', '.') }}</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400">{{ $targetPercent }}% Tercapai</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500" style="width: {{ $targetPercent }}%"></div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons: Setor, Tarik, Sesuaikan, Opsi -->
                <div class="flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-slate-800/80">
                    <!-- Setor / Tambah Saldo -->
                    <button type="button" 
                            onclick="openTopupModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->wallet_id ?? 'null' }})"
                            class="flex-1 min-h-[38px] py-1.5 px-2.5 rounded-[16px] bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-700 dark:text-emerald-300 font-black text-xs border border-emerald-500/25 active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer ios-press">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>+ Setor</span>
                    </button>

                    <!-- Tarik Dana -->
                    <button type="button" 
                            onclick="openWithdrawModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->current_value }}, {{ $item->wallet_id ?? 'null' }})"
                            class="flex-1 min-h-[38px] py-1.5 px-2.5 rounded-[16px] bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 font-black text-xs border border-amber-500/25 active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer ios-press">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" /></svg>
                        <span>- Tarik</span>
                    </button>

                    <!-- Sesuaikan Saldo Terkini -->
                    <button type="button" 
                            onclick="openUpdateValueModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->current_value }})"
                            class="min-h-[38px] px-3 rounded-[16px] liquid-glass text-slate-700 dark:text-slate-200 font-bold text-xs border border-white/60 dark:border-white/10 active:scale-95 transition-all flex items-center justify-center gap-1 cursor-pointer ios-press"
                            title="Sesuaikan Saldo Terkini">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        <span class="hidden sm:inline">Sesuaikan</span>
                    </button>

                    <!-- Edit / Opsi Dropdown Toggle -->
                    <button type="button" 
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-platform="{{ $item->platform }}"
                            data-type="{{ $item->type }}"
                            data-target="{{ $item->target_amount }}"
                            data-status="{{ $item->status }}"
                            data-wallet-id="{{ $item->wallet_id }}"
                            data-notes="{{ $item->notes }}"
                            onclick="openEditInvestmentModalFromBtn(this)"
                            class="min-h-[38px] w-9.5 rounded-[16px] liquid-glass text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all border border-white/60 dark:border-white/10 cursor-pointer shrink-0 ios-press"
                            title="Edit atau Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="liquid-card rounded-[32px] p-10 text-center border border-white/60 dark:border-white/10 bg-white/80 dark:bg-slate-900/75 space-y-3.5 shadow-sm backdrop-blur-2xl">
                <div class="w-16 h-16 rounded-[24px] liquid-glass text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center border border-white/60 dark:border-white/10 shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white">Belum Ada Aset Investasi</h4>
                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs mx-auto mt-1 leading-relaxed">
                        Mulai bangun masa depan finansial Anda dengan mencatat reksadana, saham, kripto, atau emas Anda di sini.
                    </p>
                </div>
                <button type="button" 
                        onclick="openSheetModal('modal-add-investment')" 
                        class="min-h-[44px] px-5 py-2.5 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-lg shadow-emerald-500/25 border border-white/20 inline-flex items-center gap-2 active:scale-95 transition-all cursor-pointer ios-press">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Mulai Catat Investasi</span>
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH INVESTASI BARU (BOTTOM SHEET)                             -->
<!-- ========================================================================= -->
<div id="modal-add-investment" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeSheetModal('modal-add-investment')"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar max-h-[90vh]">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeSheetModal('modal-add-investment')"></div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-[16px] bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/25">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Tambah Aset / Tabungan</h3>
                        <p class="text-[11px] font-semibold text-slate-400">Catat saldo aset atau simpanan investasi</p>
                    </div>
                </div>
                <button type="button" onclick="closeSheetModal('modal-add-investment')" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('investments.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Produk / Aset -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Produk / Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: BBCA, Sucorinvest Sharia, Emas Antam" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <!-- Jenis & Platform (2 Columns) -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Aset <span class="text-rose-500">*</span></label>
                        <select name="type" required class="w-full min-h-[46px] px-3.5 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                            <option value="mutual_fund">Reksadana</option>
                            <option value="stock">Saham</option>
                            <option value="crypto">Kripto</option>
                            <option value="gold">Emas</option>
                            <option value="deposit">Deposito</option>
                            <option value="bond">SBN / Obligasi</option>
                            <option value="p2p">P2P Lending</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Platform / Penyedia</label>
                        <input type="text" name="platform" placeholder="Bibit, Stockbit..." class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <!-- Saldo Awal (Full Width) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Saldo Awal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="add-initial-amount" name="initial_amount" min="0" step="any" required placeholder="1000000" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <!-- Tanggal Mulai (Full Width) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="block w-full min-w-0 min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <!-- Catatan (Full Width) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Rincian opsional..." class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 flex items-center justify-center gap-2 active:scale-98 transition-all cursor-pointer ios-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan Catatan Aset</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: TOP UP / SETOR MODAL (BOTTOM SHEET)                              -->
<!-- ========================================================================= -->
<div id="modal-topup-investment" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeSheetModal('modal-topup-investment')"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeSheetModal('modal-topup-investment')"></div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Tambah Saldo / Setor</h3>
                    <p id="topup-investment-name" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeSheetModal('modal-topup-investment')" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="topup-form" method="POST" class="space-y-4">
                @csrf

                <!-- Nominal Top Up -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nominal Tambah Saldo / Setor (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" min="1" step="any" required placeholder="Contoh: 500000" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Transaksi</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <!-- Dompet Sumber & Checkboxes -->
                <div class="rounded-[22px] p-4 bg-slate-100/60 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700 space-y-2.5">
                    <label class="block text-xs font-black text-slate-800 dark:text-slate-200">
                        Dompet Sumber Dana
                    </label>
                    <select id="topup-wallet-select" name="wallet_id" class="w-full min-h-[44px] px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[18px] text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">-- Tanpa Potong Dompet (Catat Saja) --</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>

                    <label class="flex items-center gap-2.5 cursor-pointer pt-1">
                        <input type="checkbox" name="deduct_wallet" value="1" checked class="w-4.5 h-4.5 rounded-[6px] text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            Potong saldo dompet yang dipilih
                        </span>
                    </label>

                    <label class="flex items-center gap-2.5 cursor-pointer pt-0.5">
                        <input type="checkbox" name="update_current_value" value="1" checked class="w-4.5 h-4.5 rounded-[6px] text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            Otomatis tambahkan ke Saldo Terkumpul
                        </span>
                    </label>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Rincian setor..." class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 flex items-center justify-center gap-2 active:scale-98 transition-all cursor-pointer ios-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Simpan Setoran Saldo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: PERBARUI NILAI PASAR SAAT INI (BOTTOM SHEET)                     -->
<!-- ========================================================================= -->
<div id="modal-update-value" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeSheetModal('modal-update-value')"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeSheetModal('modal-update-value')"></div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Sesuaikan Saldo Terkini</h3>
                    <p id="value-investment-name" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeSheetModal('modal-update-value')" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="update-value-form" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Saldo Terkini (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="update-value-input" name="current_value" min="0" step="any" required placeholder="0" class="w-full min-h-[48px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-base font-black text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                    <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-1.5">
                        Masukkan saldo atau nilai terkini aset Anda jika ada perubahan nilai pasar, bagi hasil, atau dividen.
                    </p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 flex items-center justify-center gap-2 active:scale-98 transition-all cursor-pointer ios-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan Perubahan Saldo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: TARIK DANA / JUAL ASET (BOTTOM SHEET)                            -->
<!-- ========================================================================= -->
<div id="modal-withdraw-investment" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeSheetModal('modal-withdraw-investment')"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeSheetModal('modal-withdraw-investment')"></div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Tarik Dana / Saldo</h3>
                    <p id="withdraw-investment-name" class="text-xs text-rose-600 dark:text-rose-400 font-bold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeSheetModal('modal-withdraw-investment')" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="withdraw-form" method="POST" class="space-y-4">
                @csrf

                <!-- Nominal Penarikan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nominal Ditarik (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="withdraw-amount-input" name="amount" min="1" step="any" required placeholder="0" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                    <p id="withdraw-max-hint" class="text-[11px] font-semibold text-slate-400 mt-1"></p>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Penarikan</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                </div>

                <!-- Dompet Tujuan Pencairan -->
                <div class="rounded-[22px] p-4 bg-slate-100/60 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700 space-y-2.5">
                    <label class="block text-xs font-black text-slate-800 dark:text-slate-200">
                        Dompet Penerima Dana
                    </label>
                    <select id="withdraw-wallet-select" name="wallet_id" class="w-full min-h-[44px] px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[18px] text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-hidden focus:ring-2 focus:ring-rose-500/30">
                        <option value="">-- Tanpa Tambah Saldo Dompet (Catat Saja) --</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>

                    <label class="flex items-center gap-2.5 cursor-pointer pt-1">
                        <input type="checkbox" name="add_to_wallet" value="1" checked class="w-4.5 h-4.5 rounded-[6px] text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            Masukkan dana penarikan ke dompet yang dipilih
                        </span>
                    </label>

                    <label class="flex items-center gap-2.5 cursor-pointer pt-0.5">
                        <input type="checkbox" name="close_investment" value="1" class="w-4.5 h-4.5 rounded-[6px] text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-600">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            Tutup catatan aset ini (jika seluruh dana sudah dicairkan)
                        </span>
                    </label>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan</label>
                    <input type="text" name="notes" placeholder="Tarik dana untuk kebutuhan..." class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white font-black text-sm shadow-lg shadow-rose-600/30 border border-white/20 flex items-center justify-center gap-2 active:scale-98 transition-all cursor-pointer ios-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan Penarikan Dana</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 5: EDIT / HAPUS INVESTASI (BOTTOM SHEET)                            -->
<!-- ========================================================================= -->
<div id="modal-edit-investment" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeSheetModal('modal-edit-investment')"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar max-h-[90vh]">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeSheetModal('modal-edit-investment')"></div>

            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Ubah Data Aset / Tabungan</h3>
                    <p class="text-[11px] font-semibold text-slate-400">Sesuaikan informasi atau hapus catatan aset</p>
                </div>
                <button type="button" onclick="closeSheetModal('modal-edit-investment')" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="edit-investment-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Aset / Tabungan / Produk <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-name" name="name" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Aset</label>
                        <select id="edit-type" name="type" required class="w-full min-h-[46px] px-3.5 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                            <option value="mutual_fund">Reksadana</option>
                            <option value="stock">Saham</option>
                            <option value="crypto">Kripto</option>
                            <option value="gold">Emas</option>
                            <option value="deposit">Deposito</option>
                            <option value="bond">SBN / Obligasi</option>
                            <option value="p2p">P2P Lending</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Platform / Broker</label>
                        <input type="text" id="edit-platform" name="platform" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Nominal (Opsional)</label>
                        <input type="number" id="edit-target" name="target_amount" min="0" step="any" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                        <select id="edit-status" name="status" class="w-full min-h-[46px] px-3.5 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                            <option value="active">Aktif</option>
                            <option value="closed">Ditutup / Selesai</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Dompet Default</label>
                    <select id="edit-wallet-id" name="wallet_id" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                        <option value="">-- Tanpa Dompet --</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan</label>
                    <input type="text" id="edit-notes" name="notes" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 flex items-center justify-center gap-2 active:scale-98 transition-all cursor-pointer ios-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>

            <!-- Tombol Hapus Terpisah -->
            <form id="delete-investment-form" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-800 mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        onclick="return confirm('Apakah Anda yakin ingin menghapus instrumen investasi ini beserta seluruh riwayat transaksinya?')"
                        class="w-full min-h-[44px] py-2.5 rounded-[20px] bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-500/25 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer ios-press">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    <span>Hapus Aset Investasi Ini</span>
                </button>
            </form>
        </div>
    </div>
</div>


<script>
    function syncInitialCurrentValue(val) {
        const curInput = document.getElementById('add-current-value');
        if (curInput && (!curInput.value || curInput.dataset.manual !== 'true')) {
            curInput.value = val;
        }
    }

    document.getElementById('add-current-value')?.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    function openTopupModal(id, name, walletId) {
        document.getElementById('topup-form').action = `/investments/${id}/topup`;
        document.getElementById('topup-investment-name').textContent = name;
        if (walletId) {
            document.getElementById('topup-wallet-select').value = walletId;
        }
        openSheetModal('modal-topup-investment');
    }

    function openUpdateValueModal(id, name, currentValue) {
        document.getElementById('update-value-form').action = `/investments/${id}/value`;
        document.getElementById('value-investment-name').textContent = name;
        document.getElementById('update-value-input').value = currentValue;
        openSheetModal('modal-update-value');
    }

    function openWithdrawModal(id, name, currentValue, walletId) {
        document.getElementById('withdraw-form').action = `/investments/${id}/withdraw`;
        document.getElementById('withdraw-investment-name').textContent = name;
        document.getElementById('withdraw-amount-input').max = currentValue;
        document.getElementById('withdraw-max-hint').textContent = `Maksimal penarikan: Rp ${new Intl.NumberFormat('id-ID').format(currentValue)}`;
        if (walletId) {
            document.getElementById('withdraw-wallet-select').value = walletId;
        }
        openSheetModal('modal-withdraw-investment');
    }

    function openEditInvestmentModalFromBtn(btn) {
        const ds = btn.dataset;
        document.getElementById('edit-investment-form').action = `/investments/${ds.id}`;
        document.getElementById('delete-investment-form').action = `/investments/${ds.id}`;
        document.getElementById('edit-name').value = ds.name || '';
        document.getElementById('edit-platform').value = ds.platform || '';
        document.getElementById('edit-type').value = ds.type || 'mutual_fund';
        document.getElementById('edit-target').value = ds.target || '';
        document.getElementById('edit-status').value = ds.status || 'active';
        document.getElementById('edit-wallet-id').value = ds.walletId || '';
        document.getElementById('edit-notes').value = ds.notes || '';

        openSheetModal('modal-edit-investment');
    }

    function openEditInvestmentModal(item) {
        document.getElementById('edit-investment-form').action = `/investments/${item.id}`;
        document.getElementById('delete-investment-form').action = `/investments/${item.id}`;
        document.getElementById('edit-name').value = item.name || '';
        document.getElementById('edit-platform').value = item.platform || '';
        document.getElementById('edit-type').value = item.type || 'mutual_fund';
        document.getElementById('edit-target').value = item.target_amount || '';
        document.getElementById('edit-status').value = item.status || 'active';
        document.getElementById('edit-wallet-id').value = item.wallet_id || '';
        document.getElementById('edit-notes').value = item.notes || '';

        openSheetModal('modal-edit-investment');
    }
</script>
@endsection
