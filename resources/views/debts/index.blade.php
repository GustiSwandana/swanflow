@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 dark:from-slate-900 dark:via-emerald-950/90 dark:to-slate-950 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-200" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Rim -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 dark:bg-emerald-500/10 rounded-full blur-[40px] pointer-events-none"></div>
        <div class="absolute -left-12 bottom-0 w-48 h-48 bg-teal-400/20 dark:bg-teal-500/10 rounded-full blur-[40px] pointer-events-none"></div>

        <!-- Top Navigation Bar -->
        <div class="relative z-10 flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Dashboard">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                        Utang & Piutang
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Kelola pinjaman & hak uang Anda</span>
                </div>
            </div>

            <!-- Header Action / Theme Toggle -->
            <button type="button" 
                    id="theme-toggle-btn"
                    onclick="toggleSwanFlowTheme()" 
                    aria-label="Ganti Tema Gelap atau Terang" 
                    class="w-10 h-10 flex items-center justify-center rounded-[18px] liquid-glass text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/30 dark:bg-slate-800/80 dark:border-white/10 active:scale-95 transition-all shadow-xs ios-press">
                <svg class="theme-icon-dark w-5 h-5 text-amber-300 transition-transform duration-300 transform rotate-0 hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="theme-icon-light w-5 h-5 text-white transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </div>

        <!-- 1. SUMMARY CARDS: PIUTANG VS UTANG -->
        <div class="relative z-20 grid grid-cols-2 gap-3">
            <!-- Piutang (Uang di orang lain) -->
            <div class="relative overflow-hidden rounded-[26px] p-4 text-white shadow-xl border border-white/20 dark:border-white/10 backdrop-blur-2xl bg-gradient-to-br from-emerald-600 to-teal-700 dark:from-emerald-900/60 dark:to-teal-900/60">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                        <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider">Piutang</span>
                    </div>
                    <h3 class="text-lg font-black tracking-tight mt-1 truncate">
                        Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <!-- Utang (Kewajiban saya) -->
            <div class="relative overflow-hidden rounded-[26px] p-4 text-white shadow-xl border border-white/20 dark:border-white/10 backdrop-blur-2xl bg-gradient-to-br from-rose-500 to-rose-700 dark:from-rose-900/60 dark:to-rose-950/60">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-rose-300"></span>
                        <span class="text-[10px] font-bold text-rose-100 uppercase tracking-wider">Utang</span>
                    </div>
                    <h3 class="text-lg font-black tracking-tight mt-1 truncate">
                        Rp {{ number_format($totalDebts, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-4 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- 2. FILTER BAR & ADD BUTTON -->
    <div class="flex items-center justify-between gap-2 pt-1">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
            <a href="{{ route('debts.index') }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-bold shrink-0 flex items-center justify-center transition-all {{ $currentType === 'all' && $currentStatus === 'all' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300' }}">
                Semua
            </a>
            <a href="{{ route('debts.index', ['type' => 'receivable', 'status' => 'unpaid']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-bold shrink-0 flex items-center justify-center transition-all {{ $currentType === 'receivable' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300' }}">
                Piutang
            </a>
            <a href="{{ route('debts.index', ['type' => 'debt', 'status' => 'unpaid']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-bold shrink-0 flex items-center justify-center transition-all {{ $currentType === 'debt' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300' }}">
                Utang
            </a>
            <a href="{{ route('debts.index', ['status' => 'paid']) }}" 
               class="h-9 px-3.5 rounded-xl text-xs font-bold shrink-0 flex items-center justify-center transition-all {{ $currentStatus === 'paid' ? 'bg-slate-700 dark:bg-slate-700 text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300' }}">
                Lunas
            </a>
        </div>

        <button type="button" 
                onclick="openAddDebtModal()" 
                class="h-9 px-4 rounded-xl bg-slate-900 dark:bg-emerald-600 text-white font-bold text-[11px] shadow-sm flex items-center gap-1.5 shrink-0 active:scale-95 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            <span>Catat</span>
        </button>
    </div>

    <!-- 3. DEBTS LIST -->
    <div class="space-y-3">
        @forelse($debts as $item)
            @php
                $isDebt = $item->type === 'debt';
                $isPaid = $item->status === 'paid';
                $colorClass = $isDebt ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400';
                $badgeClass = $isDebt 
                    ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' 
                    : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30';
            @endphp
            <div class="liquid-card rounded-[26px] p-4 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-xs backdrop-blur-2xl space-y-3.5 transition-all">
                <!-- Header: Person name & Type Badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-[16px] flex items-center justify-center font-black text-xs shrink-0 shadow-xs {{ $isDebt ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' }}">
                            {{ strtoupper(substr($item->person_name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item->person_name }}</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span class="font-medium">{{ $isDebt ? 'Saya berutang ke pihak ini' : 'Pihak ini berutang ke saya' }}</span>
                                @if($item->wallet)
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-600 dark:text-slate-300">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" /></svg>
                                        {{ $item->wallet->name }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $badgeClass }}">
                            {{ $isDebt ? 'Utang' : 'Piutang' }}
                        </span>
                        @if($isPaid)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-300">
                                LUNAS
                            </span>
                        @elseif($item->paid_amount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-500/20 text-amber-600 dark:text-amber-300">
                                DICICIL
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Amounts & Progress Bar -->
                <div class="p-3.5 rounded-[20px] bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-white/10 space-y-2">
                    <div class="flex items-center justify-between text-xs flex-wrap gap-1">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Total Nominal:</span>
                        <span class="font-black text-sm {{ $colorClass }} truncate">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($item->paid_amount > 0)
                        <div class="flex items-center justify-between text-[11px] flex-wrap gap-1">
                            <span class="text-slate-400">Sudah Terbayar:</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 truncate">
                                Rp {{ number_format($item->paid_amount, 0, ',', '.') }} ({{ $item->progress_percent }}%)
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-slate-200/60 dark:border-white/10 flex-wrap gap-1">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Sisa Tagihan:</span>
                        <span class="font-black {{ $isPaid ? 'text-emerald-500' : 'text-slate-900 dark:text-white' }} truncate">
                            {{ $isPaid ? 'Rp 0 (Lunas)' : 'Rp ' . number_format($item->remaining_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="w-full h-2 bg-slate-200/80 dark:bg-slate-700/80 rounded-full overflow-hidden">
                        <div class="h-full {{ $isPaid ? 'bg-emerald-500' : ($isDebt ? 'bg-rose-500' : 'bg-emerald-500') }} transition-all duration-300 rounded-full" style="width: {{ $item->progress_percent }}%"></div>
                    </div>

                    @if($item->due_date)
                        <div class="text-[10px] text-slate-400 pt-0.5 font-medium">
                            Jatuh tempo: {{ \Carbon\Carbon::parse($item->due_date)->translatedFormat('d F Y') }}
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                onclick='openEditDebtModal(@json($item))' 
                                aria-label="Edit Catatan"
                                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 bg-slate-100/80 hover:bg-emerald-50 dark:bg-slate-800/80 dark:hover:bg-emerald-500/20 rounded-xl transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>
                        <form action="{{ route('debts.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    aria-label="Hapus Catatan"
                                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400 bg-slate-100/80 hover:bg-rose-50 dark:bg-slate-800/80 dark:hover:bg-rose-500/20 rounded-xl transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    @if(!$isPaid)
                        <button type="button" 
                                onclick='openRepayDebtModal(@json($item))' 
                                class="h-8 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ $isDebt ? 'Bayar Cicilan' : 'Terima Uang' }}</span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 liquid-card bg-white/80 dark:bg-slate-900/60 rounded-[26px] border border-slate-200/80 dark:border-white/10 p-6 space-y-2">
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada catatan utang maupun piutang.</p>
                <button type="button" onclick="openAddDebtModal()" class="mt-2 text-xs font-black text-emerald-600 dark:text-emerald-400 hover:underline">
                    + Buat Catatan Pertama
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH UTANG / PIUTANG -->
<div id="modal-add-debt" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeAddDebtModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeAddDebtModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Catat Utang / Piutang Baru</h3>
                <button type="button" onclick="closeAddDebtModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('debts.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Catatan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="receivable" checked onchange="updateAddDebtType(this.value)" class="peer sr-only">
                            <div class="h-11 flex items-center justify-center text-xs font-bold rounded-2xl border border-slate-200 dark:border-white/10 peer-checked:bg-emerald-500/15 peer-checked:border-emerald-500 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 transition-all">
                                Piutang (Orang Pinjam)
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="debt" onchange="updateAddDebtType(this.value)" class="peer sr-only">
                            <div class="h-11 flex items-center justify-center text-xs font-bold rounded-2xl border border-slate-200 dark:border-white/10 peer-checked:bg-rose-500/15 peer-checked:border-rose-500 peer-checked:text-rose-600 dark:peer-checked:text-rose-400 transition-all">
                                Utang (Saya Pinjam)
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Orang / Pihak</label>
                    <input type="text" name="person_name" required placeholder="Contoh: Budi, Teman Kantor, Toko Elektronik" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1" step="any" required placeholder="500000" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div>
                    <label id="add-wallet-label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Dompet Sumber Dana (Uang Keluar)
                    </label>
                    <select id="add-debt-wallet" name="wallet_id" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    <p id="add-wallet-hint" class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 mt-1.5">
                        Saldo dompet ini akan berkurang sebesar nominal pinjaman, dan otomatis bertambah kembali saat dilunasi.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Keterangan / Catatan</label>
                    <input type="text" name="notes" placeholder="Contoh: Pinjam untuk perbaikan motor" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Simpan &amp; Sinkronkan Saldo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CATAT PEMBAYARAN / CICILAN -->
<div id="modal-repay-debt" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeRepayDebtModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeRepayDebtModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <div>
                    <h3 id="repay-title" class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Catat Pembayaran Cicilan</h3>
                    <p id="repay-person-display" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeRepayDebtModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-repay-debt" method="POST" class="space-y-4">
                @csrf
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-white/10">
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block uppercase tracking-wider font-bold">Sisa Tagihan Belum Lunas:</span>
                    <span id="repay-remaining-display" class="text-2xl font-black text-slate-900 dark:text-white block mt-1"></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nominal yang Dibayarkan (Rp)</label>
                    <input type="number" id="repay-amount-input" name="payment_amount" min="1" step="any" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Rekening / Dompet Terkait</label>
                    <select name="wallet_id" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Pembayaran</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan &amp; Sinkronkan Saldo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT UTANG / PIUTANG -->
<div id="modal-edit-debt" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeEditDebtModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeEditDebtModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Edit Catatan Utang / Piutang</h3>
                <button type="button" onclick="closeEditDebtModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-debt" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Orang / Pihak</label>
                    <input type="text" id="edit-debt-person" name="person_name" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Total Nominal (Rp)</label>
                        <input type="number" id="edit-debt-amount" name="amount" min="1" step="any" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jatuh Tempo</label>
                        <input type="date" id="edit-debt-due-date" name="due_date" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Rekening / Dompet Terkait</label>
                    <select id="edit-debt-wallet" name="wallet_id" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan</label>
                    <input type="text" id="edit-debt-notes" name="notes" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateAddDebtType(type) {
        const label = document.getElementById('add-wallet-label');
        const hint = document.getElementById('add-wallet-hint');
        if (type === 'receivable') {
            if (label) label.textContent = 'Dompet Sumber Dana (Uang Keluar)';
            if (hint) {
                hint.textContent = 'Saldo dompet ini akan berkurang saat pinjaman dicatat, dan otomatis bertambah kembali saat dilunasi.';
                hint.className = 'text-[11px] font-medium text-emerald-600 dark:text-emerald-400 mt-1.5';
            }
        } else {
            if (label) label.textContent = 'Dompet Penerima Pinjaman (Uang Masuk)';
            if (hint) {
                hint.textContent = 'Saldo dompet ini akan bertambah saat pinjaman dicatat, dan otomatis berkurang saat Anda membayar cicilan.';
                hint.className = 'text-[11px] font-medium text-rose-600 dark:text-rose-400 mt-1.5';
            }
        }
    }

    function openAddDebtModal() {
        window.openSheetModal('modal-add-debt');
    }
    function closeAddDebtModal() {
        window.closeSheetModal('modal-add-debt');
    }
    function openEditDebtModal(item) {
        document.getElementById('form-edit-debt').action = '/debts/' + item.id;
        document.getElementById('edit-debt-person').value = item.person_name;
        document.getElementById('edit-debt-amount').value = item.amount;
        document.getElementById('edit-debt-due-date').value = item.due_date ? item.due_date.substring(0, 10) : '';
        if (item.wallet_id) {
            document.getElementById('edit-debt-wallet').value = item.wallet_id;
        }
        document.getElementById('edit-debt-notes').value = item.notes || '';
        window.openSheetModal('modal-edit-debt');
    }
    function closeEditDebtModal() {
        window.closeSheetModal('modal-edit-debt');
    }
    function openRepayDebtModal(item) {
        document.getElementById('form-repay-debt').action = '/debts/' + item.id + '/repay';
        const isDebt = item.type === 'debt';
        document.getElementById('repay-title').textContent = isDebt ? 'Catat Pembayaran Utang' : 'Penerimaan Pembayaran Piutang';
        document.getElementById('repay-person-display').textContent = (isDebt ? 'Ke: ' : 'Dari: ') + item.person_name;
        if (item.wallet_id) {
            const select = document.querySelector('#form-repay-debt select[name="wallet_id"]');
            if (select) select.value = item.wallet_id;
        }
        const remaining = Number(item.amount) - Number(item.paid_amount);
        document.getElementById('repay-remaining-display').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
        const input = document.getElementById('repay-amount-input');
        input.max = remaining;
        input.value = remaining;
        window.openSheetModal('modal-repay-debt');
    }
    function closeRepayDebtModal() {
        window.closeSheetModal('modal-repay-debt');
    }
</script>
@endsection

