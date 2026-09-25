@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 dark:from-slate-900 dark:via-indigo-950/90 dark:to-slate-950 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-200" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Rim -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 dark:bg-indigo-500/10 rounded-full blur-[40px] pointer-events-none"></div>
        <div class="absolute -left-12 bottom-0 w-48 h-48 bg-purple-400/20 dark:bg-purple-500/10 rounded-full blur-[40px] pointer-events-none"></div>

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
                        Langganan & Rutin
                    </h1>
                    <span class="text-[11px] font-semibold text-indigo-200/80">Pantau beban berulang & tagihan</span>
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

        <div class="relative z-20 bg-white/12 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/20 dark:border-white/10 rounded-[30px] p-5 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-indigo-200 dark:text-slate-400 uppercase tracking-wider">Beban Rutin Bulanan</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-white/20 dark:bg-indigo-500/10 text-white dark:text-indigo-400 border border-white/10 dark:border-indigo-500/20 backdrop-blur-md">
                    {{ count($subscriptions->where('status', 'active')) }} Tagihan Aktif
                </span>
            </div>
            
            <h2 class="text-4xl sm:text-5xl font-black tracking-tight text-white drop-shadow-sm mb-2">
                Rp {{ number_format($monthlyTotal, 0, ',', '.') }}
                <span class="text-base font-semibold text-indigo-200/80">/ bulan</span>
            </h2>
            <p class="text-xs text-indigo-100 dark:text-slate-400 font-medium pt-2 border-t border-white/20 dark:border-white/10">
                Estimasi pengeluaran wajib otomatis yang jatuh tempo tiap bulan.
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-4 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- 2. SECTION TITLE & ADD BUTTON -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Daftar Langganan</h2>
            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Tagihan & pengeluaran rutin</p>
        </div>
        <button type="button" 
                onclick="openAddSubscriptionModal()" 
                class="h-9 px-4 rounded-xl bg-slate-900 dark:bg-emerald-600 text-white font-bold text-[11px] shadow-sm flex items-center gap-1.5 active:scale-95 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah</span>
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
            <div class="liquid-card rounded-[26px] p-4 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-xs backdrop-blur-2xl space-y-3.5 transition-all">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Icon Squircle -->
                        <div class="w-11 h-11 rounded-[16px] bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-500/25 flex items-center justify-center shrink-0 shadow-xs">
                            <x-category-icon :name="$sub->name" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $sub->name }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs font-black text-slate-900 dark:text-slate-100">
                                    Rp {{ number_format($sub->amount, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400">
                                    • {{ $sub->cycle === 'yearly' ? 'Tahunan' : ($sub->cycle === 'weekly' ? 'Mingguan' : 'Bulanan') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Tag -->
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold shrink-0 {{ $sub->status === 'active' ? 'text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 bg-emerald-500/15' : 'text-slate-400 border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-slate-800' }}">
                        {{ $sub->status === 'active' ? 'Aktif' : 'Dijeda' }}
                    </span>
                </div>

                <!-- Info & Due Date -->
                <div class="p-3 rounded-2xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-white/10 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Jatuh tempo:</span>
                        <span class="text-[11px] font-black {{ $isOverdue ? 'text-rose-500' : ($isDueToday ? 'text-amber-500' : 'text-slate-800 dark:text-slate-200') }}">
                            {{ $dueDate->translatedFormat('d F Y') }}
                            @if($isOverdue) (Terlewat) @elseif($isDueToday) (Hari ini!) @endif
                        </span>
                    </div>

                    @if($sub->wallet)
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 truncate max-w-[100px]">
                            💳 {{ $sub->wallet->name }}
                        </span>
                    @endif
                </div>

                <!-- Actions: Bayar & Catat / Edit / Hapus -->
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                onclick='openEditSubscriptionModal(@json($sub))'
                                aria-label="Edit Langganan"
                                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 bg-slate-100/80 hover:bg-emerald-50 dark:bg-slate-800/80 dark:hover:bg-emerald-500/20 rounded-xl transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>
                        <form action="{{ route('subscriptions.destroy', $sub) }}" method="POST" onsubmit="return confirm('Hapus langganan ini?')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    aria-label="Hapus Langganan"
                                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400 bg-slate-100/80 hover:bg-rose-50 dark:bg-slate-800/80 dark:hover:bg-rose-500/20 rounded-xl transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <button type="button" 
                            onclick='openPaySubscriptionModal(@json($sub))'
                            class="h-8 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 active:scale-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Bayar &amp; Catat</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12 liquid-card bg-white/80 dark:bg-slate-900/60 rounded-[26px] border border-slate-200/80 dark:border-white/10 p-6 space-y-2">
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada langganan atau pembayaran rutin tercatat.</p>
                <button type="button" onclick="openAddSubscriptionModal()" class="mt-2 text-xs font-black text-emerald-600 dark:text-emerald-400 hover:underline">
                    + Tambah Langganan Pertama Anda
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH LANGGANAN -->
<div id="modal-add-subscription" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeAddSubscriptionModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeAddSubscriptionModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Tambah Tagihan Rutin</h3>
                <button type="button" onclick="closeAddSubscriptionModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Layanan / Tagihan</label>
                    <input type="text" name="name" required placeholder="Contoh: Netflix Premium, Listrik PLN, Spotify" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1" step="any" required placeholder="186000" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Siklus</label>
                        <select name="cycle" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                            <option value="monthly">Bulanan</option>
                            <option value="weekly">Mingguan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Tagihan (1-31)</label>
                        <input type="number" name="billing_date" min="1" max="31" value="{{ date('j') }}" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jatuh Tempo</label>
                        <input type="date" name="next_due_date" value="{{ date('Y-m-d') }}" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Dompet Default</label>
                        <select name="wallet_id" class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                            <option value="">-- Pilih --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                        <select name="category_id" class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                            <option value="">-- Pilih --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all">
                        Simpan Tagihan Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT LANGGANAN -->
<div id="modal-edit-subscription" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeEditSubscriptionModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeEditSubscriptionModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Edit Langganan / Tagihan</h3>
                <button type="button" onclick="closeEditSubscriptionModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-subscription" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Layanan / Tagihan</label>
                    <input type="text" id="edit-sub-name" name="name" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nominal (Rp)</label>
                        <input type="number" id="edit-sub-amount" name="amount" min="1" step="any" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Siklus</label>
                        <select id="edit-sub-cycle" name="cycle" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                            <option value="monthly">Bulanan</option>
                            <option value="weekly">Mingguan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Tagihan (1-31)</label>
                        <input type="number" id="edit-sub-billing-date" name="billing_date" min="1" max="31" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jatuh Tempo</label>
                        <input type="date" id="edit-sub-next-due-date" name="next_due_date" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Dompet Pembayaran</label>
                        <select id="edit-sub-wallet-id" name="wallet_id" class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                            <option value="">-- Pilih --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Status</label>
                        <select id="edit-sub-status" name="status" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                            <option value="active">Aktif</option>
                            <option value="paused">Dijeda</option>
                        </select>
                    </div>
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

<!-- MODAL BAYAR SEKARANG -->
<div id="modal-pay-subscription" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closePaySubscriptionModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closePaySubscriptionModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Konfirmasi Pembayaran</h3>
                    <p id="pay-sub-name-display" class="text-xs text-indigo-600 dark:text-indigo-400 font-bold mt-0.5"></p>
                </div>
                <button type="button" onclick="closePaySubscriptionModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-pay-subscription" method="POST" class="space-y-4">
                @csrf
                <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-900/40">
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block uppercase tracking-wider font-bold">Jumlah yang Akan Dibayar:</span>
                    <span id="pay-sub-amount-display" class="text-2xl font-black text-slate-900 dark:text-white block mt-1"></span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium block mt-1">Saldo dompet akan dipotong otomatis &amp; tanggal jatuh tempo berikutnya akan dimajukan.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Gunakan Saldo Dari Dompet</label>
                    <select id="pay-sub-wallet-select" name="wallet_id" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Transaksi</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition-all">
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
