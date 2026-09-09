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
                Langganan & Tagihan Rutin
            </h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">Pantau beban berulang & auto catat</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. MONTHLY RECURRING COMMITMENT OVERVIEW CARD -->
    <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-900 rounded-3xl p-5 text-white shadow-xl border border-indigo-900/50 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Beban Rutin Bulanan</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    {{ count($subscriptions->where('status', 'active')) }} Tagihan Aktif
                </span>
            </div>
            <h2 class="text-2xl font-extrabold text-white tracking-tight mt-1">
                Rp {{ number_format($monthlyTotal, 0, ',', '.') }}
                <span class="text-xs font-medium text-slate-400">/ bulan</span>
            </h2>
            <p class="text-[11px] text-indigo-200/80 mt-2">
                Estimasi pengeluaran wajib otomatis yang jatuh tempo tiap bulan.
            </p>
        </div>
    </div>

    <!-- 2. SECTION TITLE & ADD BUTTON -->
    <div class="flex items-center justify-between pt-1">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Langganan Aktif</h2>
        <button type="button" 
                onclick="openAddSubscriptionModal()" 
                class="min-h-[40px] px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm shadow-indigo-600/30 flex items-center gap-1.5 active:scale-95 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Tagihan</span>
        </button>
    </div>

    <!-- 3. SUBSCRIPTIONS LIST -->
    <div class="space-y-3">
        @forelse($subscriptions as $sub)
            @php
                $dueDate = \Carbon\Carbon::parse($sub->next_due_date);
                $isOverdue = $dueDate->isPast() && !$dueDate->isToday();
                $isDueToday = $dueDate->isToday();
                $isDueSoon = $dueDate->diffInDays(now()) <= 3 && !$isOverdue;
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-800/80 space-y-3 transition-colors">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Icon Circle -->
                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center shrink-0 shadow-2xs">
                            <x-category-icon :name="$sub->name" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ $sub->name }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs font-extrabold text-slate-900 dark:text-slate-100">
                                    Rp {{ number_format($sub->amount, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    • {{ $sub->cycle === 'yearly' ? 'Tahunan' : ($sub->cycle === 'weekly' ? 'Mingguan' : 'Bulanan') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Tag -->
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $sub->status === 'active' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                        {{ $sub->status === 'active' ? 'Aktif' : 'Dijeda' }}
                    </span>
                </div>

                <!-- Info & Due Date -->
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Jatuh tempo:</span>
                        <span class="text-[11px] font-bold {{ $isOverdue ? 'text-rose-500' : ($isDueToday ? 'text-amber-500' : 'text-slate-700 dark:text-slate-200') }}">
                            {{ $dueDate->translatedFormat('d F Y') }}
                            @if($isOverdue) (Terlewat) @elseif($isDueToday) (Hari ini!) @endif
                        </span>
                    </div>

                    @if($sub->wallet)
                        <span class="text-[10px] font-semibold text-slate-400 truncate max-w-[90px]">
                            💳 {{ $sub->wallet->name }}
                        </span>
                    @endif
                </div>

                <!-- Actions: Bayar & Catat / Edit / Hapus -->
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-1">
                        <button type="button" 
                                onclick='openEditSubscriptionModal(@json($sub))'
                                class="text-xs text-slate-500 dark:text-slate-400 hover:text-emerald-500 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Edit
                        </button>
                        <form action="{{ route('subscriptions.destroy', $sub) }}" method="POST" onsubmit="return confirm('Hapus langganan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 px-2 py-1.5 rounded-lg transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>

                    <button type="button" 
                            onclick='openPaySubscriptionModal(@json($sub))'
                            class="min-h-[38px] px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs flex items-center gap-1.5 active:scale-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Bayar & Catat</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6">
                <p class="text-xs text-slate-400">Belum ada langganan atau pembayaran rutin tercatat.</p>
                <button type="button" onclick="openAddSubscriptionModal()" class="mt-3 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    + Tambah Langganan Pertama Anda
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH LANGGANAN -->
<div id="modal-add-subscription" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeAddSubscriptionModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddSubscriptionModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tambah Tagihan Rutin / Langganan</h3>
                <button type="button" onclick="closeAddSubscriptionModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Layanan / Tagihan</label>
                    <input type="text" name="name" required placeholder="Contoh: Netflix Premium, Listrik PLN, Spotify" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1" step="any" required placeholder="186000" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Siklus</label>
                        <select name="cycle" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                            <option value="monthly">Bulanan</option>
                            <option value="weekly">Mingguan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tanggal Tagihan (1-31)</label>
                        <input type="number" name="billing_date" min="1" max="31" value="{{ date('j') }}" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jatuh Tempo Berikutnya</label>
                        <input type="date" name="next_due_date" value="{{ date('Y-m-d') }}" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Dompet Default</label>
                        <select name="wallet_id" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Kategori</label>
                        <select name="category_id" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30">
                        Simpan Tagihan Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT LANGGANAN -->
<div id="modal-edit-subscription" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeEditSubscriptionModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditSubscriptionModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Edit Langganan / Tagihan</h3>
                <button type="button" onclick="closeEditSubscriptionModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-subscription" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Layanan / Tagihan</label>
                    <input type="text" id="edit-sub-name" name="name" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nominal (Rp)</label>
                        <input type="number" id="edit-sub-amount" name="amount" min="1" step="any" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Siklus</label>
                        <select id="edit-sub-cycle" name="cycle" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                            <option value="monthly">Bulanan</option>
                            <option value="weekly">Mingguan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tanggal Tagihan (1-31)</label>
                        <input type="number" id="edit-sub-billing-date" name="billing_date" min="1" max="31" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jatuh Tempo Berikutnya</label>
                        <input type="date" id="edit-sub-next-due-date" name="next_due_date" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Dompet Pembayaran</label>
                        <select id="edit-sub-wallet-id" name="wallet_id" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                            <option value="">-- Pilih --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Status</label>
                        <select id="edit-sub-status" name="status" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-indigo-500">
                            <option value="active">Aktif</option>
                            <option value="paused">Dijeda</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BAYAR SEKARANG -->
<div id="modal-pay-subscription" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closePaySubscriptionModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closePaySubscriptionModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Konfirmasi Pembayaran Tagihan</h3>
                    <p id="pay-sub-name-display" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5"></p>
                </div>
                <button type="button" onclick="closePaySubscriptionModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-pay-subscription" method="POST" class="space-y-4">
                @csrf
                <div class="p-3.5 bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 rounded-2xl">
                    <span class="text-[11px] text-slate-400 block uppercase tracking-wider">Jumlah yang Akan Dibayar:</span>
                    <span id="pay-sub-amount-display" class="text-xl font-extrabold text-slate-900 dark:text-white block mt-0.5"></span>
                    <span class="text-[11px] text-slate-400 block mt-1">Saldo dompet akan dipotong otomatis & tanggal jatuh tempo berikutnya akan dimajukan.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Gunakan Saldo Dari Dompet</label>
                    <select id="pay-sub-wallet-select" name="wallet_id" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Tanggal Transaksi</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Konfirmasi Pembayaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddSubscriptionModal() {
        window.openSheetModal('modal-add-subscription');
    }
    function closeAddSubscriptionModal() {
        window.closeSheetModal('modal-add-subscription');
    }
    function openEditSubscriptionModal(sub) {
        document.getElementById('form-edit-subscription').action = '/subscriptions/' + sub.id;
        document.getElementById('edit-sub-name').value = sub.name;
        document.getElementById('edit-sub-amount').value = sub.amount;
        document.getElementById('edit-sub-cycle').value = sub.cycle;
        document.getElementById('edit-sub-billing-date').value = sub.billing_date;
        document.getElementById('edit-sub-next-due-date').value = sub.next_due_date ? sub.next_due_date.substring(0, 10) : '';
        document.getElementById('edit-sub-wallet-id').value = sub.wallet_id || '';
        document.getElementById('edit-sub-status').value = sub.status || 'active';
        window.openSheetModal('modal-edit-subscription');
    }
    function closeEditSubscriptionModal() {
        window.closeSheetModal('modal-edit-subscription');
    }
    function openPaySubscriptionModal(sub) {
        document.getElementById('form-pay-subscription').action = '/subscriptions/' + sub.id + '/pay';
        document.getElementById('pay-sub-name-display').textContent = sub.name;
        document.getElementById('pay-sub-amount-display').textContent = 'Rp ' + Number(sub.amount).toLocaleString('id-ID');
        if (sub.wallet_id) {
            document.getElementById('pay-sub-wallet-select').value = sub.wallet_id;
        }
        window.openSheetModal('modal-pay-subscription');
    }
    function closePaySubscriptionModal() {
        window.closeSheetModal('modal-pay-subscription');
    }
</script>
@endsection
