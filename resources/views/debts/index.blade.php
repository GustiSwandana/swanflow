@extends('layouts.mobile')

@section('header_left')
    <div class="flex items-center gap-2">
        <a href="{{ route('dashboard') }}" class="min-w-[44px] min-h-[44px] -ml-2 flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition-all" aria-label="Kembali ke Dashboard">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div class="flex flex-col">
            <h1 class="text-base font-bold text-slate-800 dark:text-white tracking-tight leading-tight">
                Utang & Piutang
            </h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">Kelola pinjaman & hak uang Anda</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. SUMMARY CARDS: PIUTANG VS UTANG -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Piutang (Uang di orang lain) -->
        <div class="bg-gradient-to-br from-emerald-950/80 via-slate-900 to-slate-900 rounded-3xl p-4 text-white shadow-md border border-emerald-900/40 relative overflow-hidden">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider">Total Piutang</span>
            </div>
            <p class="text-xs text-slate-400">Uang yang akan kembali</p>
            <h3 class="text-lg font-extrabold text-emerald-400 tracking-tight mt-1.5 truncate">
                Rp {{ number_format($totalReceivables, 0, ',', '.') }}
            </h3>
        </div>

        <!-- Utang (Kewajiban saya) -->
        <div class="bg-gradient-to-br from-rose-950/80 via-slate-900 to-slate-900 rounded-3xl p-4 text-white shadow-md border border-rose-900/40 relative overflow-hidden">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                <span class="text-[10px] font-bold text-rose-300 uppercase tracking-wider">Total Utang</span>
            </div>
            <p class="text-xs text-slate-400">Kewajiban harus dibayar</p>
            <h3 class="text-lg font-extrabold text-rose-400 tracking-tight mt-1.5 truncate">
                Rp {{ number_format($totalDebts, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <!-- 2. FILTER BAR & ADD BUTTON -->
    <div class="flex items-center justify-between gap-2 pt-1">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
            <a href="{{ route('debts.index') }}" 
               class="min-h-[36px] px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $currentType === 'all' && $currentStatus === 'all' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                Semua
            </a>
            <a href="{{ route('debts.index', ['type' => 'receivable', 'status' => 'unpaid']) }}" 
               class="min-h-[36px] px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $currentType === 'receivable' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                Piutang
            </a>
            <a href="{{ route('debts.index', ['type' => 'debt', 'status' => 'unpaid']) }}" 
               class="min-h-[36px] px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $currentType === 'debt' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                Utang
            </a>
            <a href="{{ route('debts.index', ['status' => 'paid']) }}" 
               class="min-h-[36px] px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all {{ $currentStatus === 'paid' ? 'bg-slate-700 text-white shadow-xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                Lunas
            </a>
        </div>

        <button type="button" 
                onclick="openAddDebtModal()" 
                class="min-h-[38px] px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm shadow-emerald-600/30 flex items-center gap-1 shrink-0 active:scale-95 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
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
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-800/80 space-y-3 transition-colors">
                <!-- Header: Person name & Type Badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 {{ $isDebt ? 'bg-rose-100 dark:bg-rose-950/50 text-rose-600' : 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600' }}">
                            {{ strtoupper(substr($item->person_name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ $item->person_name }}</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span>{{ $isDebt ? 'Saya berutang ke pihak ini' : 'Pihak ini berutang ke saya' }}</span>
                                @if($item->wallet)
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 font-medium text-slate-500 dark:text-slate-300">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" /></svg>
                                        {{ $item->wallet->name }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">
                            {{ $isDebt ? 'Utang' : 'Piutang' }}
                        </span>
                        @if($isPaid)
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded-md text-[9px] font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-300">
                                LUNAS
                            </span>
                        @elseif($item->paid_amount > 0)
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded-md text-[9px] font-bold bg-amber-500/20 text-amber-600 dark:text-amber-300">
                                DICICIL
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Amounts & Progress Bar -->
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Total Nominal:</span>
                        <span class="font-extrabold {{ $colorClass }}">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($item->paid_amount > 0)
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Sudah Terbayar:</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">
                                Rp {{ number_format($item->paid_amount, 0, ',', '.') }} ({{ $item->progress_percent }}%)
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Sisa Tagihan:</span>
                        <span class="font-bold {{ $isPaid ? 'text-emerald-500' : 'text-slate-800 dark:text-white' }}">
                            {{ $isPaid ? 'Rp 0 (Lunas)' : 'Rp ' . number_format($item->remaining_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $isPaid ? 'bg-emerald-500' : ($isDebt ? 'bg-rose-500' : 'bg-emerald-500') }} transition-all duration-300" style="width: {{ $item->progress_percent }}%"></div>
                    </div>

                    @if($item->due_date)
                        <div class="text-[10px] text-slate-400 pt-0.5">
                            Jatuh tempo: {{ \Carbon\Carbon::parse($item->due_date)->translatedFormat('d F Y') }}
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-1">
                        <button type="button" 
                                onclick='openEditDebtModal(@json($item))' 
                                class="text-xs text-slate-500 dark:text-slate-400 hover:text-emerald-500 px-2 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            Edit
                        </button>
                        <form action="{{ route('debts.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 px-2 py-1.5 rounded-lg">
                                Hapus
                            </button>
                        </form>
                    </div>

                    @if(!$isPaid)
                        <button type="button" 
                                onclick='openRepayDebtModal(@json($item))' 
                                class="min-h-[38px] px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs flex items-center gap-1.5 active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ $isDebt ? 'Bayar Cicilan' : 'Terima Uang' }}</span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6">
                <p class="text-xs text-slate-400">Belum ada catatan utang maupun piutang.</p>
                <button type="button" onclick="openAddDebtModal()" class="mt-3 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                    + Buat Catatan Pertama
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH UTANG / PIUTANG -->
<div id="modal-add-debt" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="closeAddDebtModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddDebtModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Catat Utang / Piutang Baru</h3>
                <button type="button" onclick="closeAddDebtModal()" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
            </div>

            <form action="{{ route('debts.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jenis Catatan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="receivable" checked onchange="updateAddDebtType(this.value)" class="peer sr-only">
                            <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/40 peer-checked:border-emerald-500 peer-checked:text-emerald-600 transition-all">
                                Piutang (Orang Pinjam)
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="debt" onchange="updateAddDebtType(this.value)" class="peer sr-only">
                            <div class="min-h-[44px] flex items-center justify-center text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-950/40 peer-checked:border-rose-500 peer-checked:text-rose-600 transition-all">
                                Utang (Saya Pinjam)
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Orang / Pihak</label>
                    <input type="text" name="person_name" required placeholder="Contoh: Budi, Teman Kantor, Toko Elektronik" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1" step="any" required placeholder="500000" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label id="add-wallet-label" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Dompet Sumber Dana (Uang Keluar)
                    </label>
                    <select id="add-debt-wallet" name="wallet_id" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    <p id="add-wallet-hint" class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1">
                        Saldo dompet ini akan berkurang sebesar nominal pinjaman, dan otomatis bertambah kembali saat dilunasi.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                    <input type="text" name="notes" placeholder="Contoh: Pinjam untuk perbaikan motor" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Simpan & Sinkronkan Saldo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CATAT PEMBAYARAN / CICILAN -->
<div id="modal-repay-debt" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="closeRepayDebtModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeRepayDebtModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 id="repay-title" class="text-sm font-bold text-slate-800 dark:text-white">Catat Pembayaran Cicilan</h3>
                    <p id="repay-person-display" class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeRepayDebtModal()" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
            </div>

            <form id="form-repay-debt" method="POST" class="space-y-4">
                @csrf
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl">
                    <span class="text-[11px] text-slate-400 block uppercase tracking-wider">Sisa Tagihan yang Belum Lunas:</span>
                    <span id="repay-remaining-display" class="text-xl font-extrabold text-slate-900 dark:text-white block mt-0.5"></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nominal yang Dibayarkan (Rp)</label>
                    <input type="number" id="repay-amount-input" name="payment_amount" min="1" step="any" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Rekening / Dompet Terkait</label>
                    <select name="wallet_id" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tanggal Pembayaran</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan & Sinkronkan Saldo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT UTANG / PIUTANG -->
<div id="modal-edit-debt" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="closeEditDebtModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditDebtModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Edit Catatan Utang / Piutang</h3>
                <button type="button" onclick="closeEditDebtModal()" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
            </div>

            <form id="form-edit-debt" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Orang / Pihak</label>
                    <input type="text" id="edit-debt-person" name="person_name" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Total Nominal (Rp)</label>
                        <input type="number" id="edit-debt-amount" name="amount" min="1" step="any" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jatuh Tempo</label>
                        <input type="date" id="edit-debt-due-date" name="due_date" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Rekening / Dompet Terkait</label>
                    <select id="edit-debt-wallet" name="wallet_id" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Catatan</label>
                    <input type="text" id="edit-debt-notes" name="notes" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30">
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
                hint.className = 'text-[11px] text-emerald-600 dark:text-emerald-400 mt-1';
            }
        } else {
            if (label) label.textContent = 'Dompet Penerima Pinjaman (Uang Masuk)';
            if (hint) {
                hint.textContent = 'Saldo dompet ini akan bertambah saat pinjaman dicatat, dan otomatis berkurang saat Anda membayar cicilan.';
                hint.className = 'text-[11px] text-rose-600 dark:text-rose-400 mt-1';
            }
        }
    }

    function openAddDebtModal() {
        document.getElementById('modal-add-debt').classList.remove('hidden');
    }
    function closeAddDebtModal() {
        document.getElementById('modal-add-debt').classList.add('hidden');
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
        document.getElementById('modal-edit-debt').classList.remove('hidden');
    }
    function closeEditDebtModal() {
        document.getElementById('modal-edit-debt').classList.add('hidden');
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
        document.getElementById('modal-repay-debt').classList.remove('hidden');
    }
    function closeRepayDebtModal() {
        document.getElementById('modal-repay-debt').classList.add('hidden');
    }
</script>
@endsection

