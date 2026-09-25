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
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                        Dompet
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Kelola akun finansial</span>
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

        <div class="relative z-20 bg-white/10 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/30 dark:border-white/10 rounded-[30px] p-5 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-emerald-100 dark:text-slate-400 uppercase tracking-wider">Total Akumulasi Saldo</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-white/20 dark:bg-emerald-500/10 text-white dark:text-emerald-400 border border-white/10 dark:border-emerald-500/20 backdrop-blur-md">
                    {{ count($wallets) }} Dompet
                </span>
            </div>
            
            <h2 class="text-4xl sm:text-5xl font-black tracking-tight text-white drop-shadow-sm mb-4">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </h2>

            <!-- Quick Category Breakdown -->
            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-white/20 dark:border-white/10 text-center">
                <div class="bg-black/10 dark:bg-slate-800/50 rounded-[18px] p-2 backdrop-blur-md border border-white/10 dark:border-white/5">
                    <span class="text-[10px] font-semibold text-emerald-100 dark:text-slate-400 block">Bank</span>
                    <span class="text-xs font-black text-white dark:text-teal-400 block mt-1 truncate">Rp {{ number_format($byType['bank'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-black/10 dark:bg-slate-800/50 rounded-[18px] p-2 backdrop-blur-md border border-white/10 dark:border-white/5">
                    <span class="text-[10px] font-semibold text-emerald-100 dark:text-slate-400 block">E-Wallet</span>
                    <span class="text-xs font-black text-white dark:text-cyan-400 block mt-1 truncate">Rp {{ number_format($byType['ewallet'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-black/10 dark:bg-slate-800/50 rounded-[18px] p-2 backdrop-blur-md border border-white/10 dark:border-white/5">
                    <span class="text-[10px] font-semibold text-emerald-100 dark:text-slate-400 block">Tunai</span>
                    <span class="text-xs font-black text-white dark:text-emerald-400 block mt-1 truncate">Rp {{ number_format($byType['cash'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- 2. SECTION HEADER & ADD BUTTON -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Daftar Akun</h2>
            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Atur & pantau saldo</p>
        </div>
        <button type="button" 
                onclick="openAddWalletModal()" 
                class="h-9 px-4 rounded-xl bg-slate-900 dark:bg-emerald-600 text-white font-bold text-[11px] shadow-sm flex items-center gap-1.5 active:scale-95 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah</span>
        </button>
    </div>

    <!-- 3. WALLETS LIST CARDS -->
    <div class="space-y-3 pb-4">
        @forelse($wallets as $wallet)
            @php
                $typeIcons = [
                    'bank' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5m-1.5 0h18" /></svg>',
                    'ewallet' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>',
                    'cash' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5A2.25 2.25 0 014.5 3.75h15A2.25 2.25 0 0121.75 6v10.5a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 010 16.5V6zM9 12a3 3 0 106 0 3 3 0 00-6 0z" /></svg>',
                    'investment' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>',
                    'other' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                ];
                $colorClasses = [
                    'bank' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20',
                    'ewallet' => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-100 dark:border-cyan-500/20',
                    'cash' => 'bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-100 dark:border-teal-500/20',
                    'investment' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-500/20',
                    'other' => 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-white/10',
                ];
            @endphp
            <div class="liquid-card bg-white/80 dark:bg-slate-900/75 rounded-[24px] p-3.5 border border-white/60 dark:border-white/10 shadow-xs flex items-center justify-between transition-all group hover:bg-white dark:hover:bg-slate-800/80">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-12 h-12 rounded-[18px] border flex items-center justify-center shrink-0 shadow-xs {{ $colorClasses[$wallet->type] ?? $colorClasses['other'] }}">
                        {!! $typeIcons[$wallet->type] ?? $typeIcons['other'] !!}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $wallet->name }}</h3>
                            <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                {{ $wallet->type }}
                            </span>
                        </div>
                        <p class="text-sm font-black text-slate-900 dark:text-white mt-1">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <!-- Edit Button -->
                    <button type="button" 
                            onclick='openEditWalletModal(@json($wallet))'
                            class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 bg-slate-100/80 hover:bg-emerald-50 dark:bg-slate-800/80 dark:hover:bg-emerald-500/20 rounded-xl transition-all active:scale-95" 
                            aria-label="Edit Dompet">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </button>

                    <!-- Delete Button -->
                    @if(count($wallets) > 1)
                        <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" onsubmit="return confirm('Hapus dompet {{ $wallet->name }}? Transaksi terkait dompet ini juga akan terhapus.')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400 bg-slate-100/80 hover:bg-rose-50 dark:bg-slate-800/80 dark:hover:bg-rose-500/20 rounded-xl transition-all active:scale-95" 
                                    aria-label="Hapus Dompet">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white/80 dark:bg-slate-900/60 rounded-[28px] border border-slate-200/80 dark:border-white/10 p-6 flex flex-col items-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800/80 flex items-center justify-center text-slate-300 dark:text-slate-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5A2.25 2.25 0 014.5 3.75h15A2.25 2.25 0 0121.75 6v10.5a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 010 16.5V6z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Dompet</h3>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Tambahkan dompet/rekening pertama Anda.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH DOMPET -->
<div id="modal-add-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeAddWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeAddWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Tambah Dompet Baru</h3>
                <button type="button" onclick="closeAddWalletModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Rekening / Dompet</label>
                    <input type="text" name="name" required placeholder="Contoh: BCA Utama, GoPay" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Akun</label>
                    <select name="type" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        <option value="bank">Rekening Bank (BCA, Mandiri, dll)</option>
                        <option value="ewallet">E-Wallet (GoPay, OVO, dll)</option>
                        <option value="cash">Uang Tunai / Cash</option>
                        <option value="investment">Investasi (Bibit, dll)</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Saldo Awal (Rp)</label>
                    <input type="number" name="balance" step="any" min="0" required placeholder="0" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all">
                        Simpan Dompet Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT DOMPET -->
<div id="modal-edit-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeEditWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeEditWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Edit Rekening / Dompet</h3>
                <button type="button" onclick="closeEditWalletModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="form-edit-wallet" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Rekening / Dompet</label>
                    <input type="text" id="edit-wallet-name" name="name" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Akun</label>
                    <select id="edit-wallet-type" name="type" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        <option value="bank">Rekening Bank</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="cash">Uang Tunai / Cash</option>
                        <option value="investment">Investasi</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Penyesuaian Saldo (Rp)</label>
                    <input type="number" id="edit-wallet-balance" name="balance" step="any" min="0" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
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
    function openAddWalletModal() {
        window.openSheetModal('modal-add-wallet');
    }
    function closeAddWalletModal() {
        window.closeSheetModal('modal-add-wallet');
    }
    function openEditWalletModal(wallet) {
        document.getElementById('form-edit-wallet').action = '/wallets/' + wallet.id;
        document.getElementById('edit-wallet-name').value = wallet.name;
        document.getElementById('edit-wallet-type').value = wallet.type;
        document.getElementById('edit-wallet-balance').value = wallet.balance;
        window.openSheetModal('modal-edit-wallet');
    }
    function closeEditWalletModal() {
        window.closeSheetModal('modal-edit-wallet');
    }
</script>
@endsection
