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
                Dompet & Rekening
            </h1>
            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Kelola akun finansial Gusti</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-5">

    <!-- 1. TOTAL BALANCE OVERVIEW CARD (APPLE WALLET LIQUID GLASS) -->
    <div class="relative overflow-hidden rounded-[32px] p-6 text-white shadow-2xl border border-white/20 dark:border-white/10 backdrop-blur-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Specular highlight line -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/40 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -left-8 -top-8 w-32 h-32 bg-teal-500/15 rounded-full blur-2xl pointer-events-none animate-liquid-orb-2"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Akumulasi Saldo</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-[14px] text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 backdrop-blur-xs">
                    {{ count($wallets) }} Dompet Aktif
                </span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight mt-1.5">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </h2>

            <!-- Quick Category Breakdown -->
            <div class="grid grid-cols-3 gap-2.5 mt-5 pt-4 border-t border-white/10 text-center">
                <div class="bg-white/10 dark:bg-white/5 rounded-[18px] p-2.5 backdrop-blur-md border border-white/10">
                    <span class="text-[10px] font-semibold text-slate-300 block">Bank</span>
                    <span class="text-xs font-black text-teal-300 block mt-0.5">Rp {{ number_format($byType['bank'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/10 dark:bg-white/5 rounded-[18px] p-2.5 backdrop-blur-md border border-white/10">
                    <span class="text-[10px] font-semibold text-slate-300 block">E-Wallet</span>
                    <span class="text-xs font-black text-cyan-300 block mt-0.5">Rp {{ number_format($byType['ewallet'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/10 dark:bg-white/5 rounded-[18px] p-2.5 backdrop-blur-md border border-white/10">
                    <span class="text-[10px] font-semibold text-slate-300 block">Tunai</span>
                    <span class="text-xs font-black text-emerald-300 block mt-0.5">Rp {{ number_format($byType['cash'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SECTION HEADER & ADD BUTTON -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Daftar Akun Keuangan</h2>
            <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Sentuh untuk melihat detail saldo</p>
        </div>
        <button type="button" 
                onclick="openAddWalletModal()" 
                class="min-h-[42px] px-4 py-2 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-lg shadow-emerald-500/25 border border-white/20 flex items-center gap-2 active:scale-95 transition-all ios-press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Dompet</span>
        </button>
    </div>

    <!-- 3. WALLETS LIST CARDS -->
    <div class="space-y-3">
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
                    'bank' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
                    'ewallet' => 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-500/20',
                    'cash' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                    'investment' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                    'other' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                ];
            @endphp
            <div class="liquid-card rounded-[26px] p-4.5 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm hover:shadow-md backdrop-blur-2xl flex items-center justify-between transition-all">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-12 h-12 rounded-[20px] border flex items-center justify-center shrink-0 shadow-xs {{ $colorClasses[$wallet->type] ?? $colorClasses['other'] }}">
                        {!! $typeIcons[$wallet->type] ?? $typeIcons['other'] !!}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-extrabold text-slate-800 dark:text-white truncate">{{ $wallet->name }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[10px] text-[9px] font-black uppercase tracking-wider bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border border-slate-200/50 dark:border-slate-700/50">
                                {{ $wallet->type }}
                            </span>
                        </div>
                        <p class="text-base font-black text-slate-900 dark:text-slate-100 mt-0.5">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </p>
                        <span class="text-[10px] font-medium text-slate-400 block mt-0.5">
                            {{ $wallet->transactions_count }} transaksi tercatat
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- Edit Button -->
                    <button type="button" 
                            onclick='openEditWalletModal(@json($wallet))'
                            class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 flex items-center justify-center text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 rounded-[16px] liquid-glass border border-white/60 dark:border-white/10 shadow-xs active:scale-95 transition-all ios-press" 
                            aria-label="Edit Dompet">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </button>

                    <!-- Delete Button -->
                    @if(count($wallets) > 1)
                        <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" onsubmit="return confirm('Hapus dompet {{ $wallet->name }}? Transaksi terkait dompet ini juga akan terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 flex items-center justify-center text-slate-400 hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400 rounded-[16px] liquid-glass border border-white/60 dark:border-white/10 shadow-xs active:scale-95 transition-all ios-press" 
                                    aria-label="Hapus Dompet">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 liquid-card rounded-[28px] bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 p-6 backdrop-blur-2xl">
                <div class="w-12 h-12 mx-auto rounded-[20px] liquid-glass flex items-center justify-center text-slate-400 mb-2 border border-white/50 dark:border-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5A2.25 2.25 0 014.5 3.75h15A2.25 2.25 0 0121.75 6v10.5a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 010 16.5V6zM9 12a3 3 0 106 0 3 3 0 00-6 0z" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-400">Belum ada dompet atau rekening.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- MODAL TAMBAH DOMPET -->
<div id="modal-add-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeAddWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Tambah Rekening / Dompet Baru</h3>
                <button type="button" onclick="closeAddWalletModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Rekening / Dompet</label>
                    <input type="text" name="name" required placeholder="Contoh: BCA Utama, GoPay, Dompet Tunai" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Akun</label>
                    <select name="type" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                        <option value="bank">Rekening Bank (BCA, Mandiri, BNI, BRI, dll)</option>
                        <option value="ewallet">E-Wallet (GoPay, OVO, Dana, ShopeePay)</option>
                        <option value="cash">Uang Tunai / Cash</option>
                        <option value="investment">Investasi (Bibit, Stockbit, Reksadana)</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Saldo Awal (Rp)</label>
                    <input type="number" name="balance" step="any" min="0" required placeholder="0" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 active:scale-98 transition-all ios-press">
                        Simpan Dompet Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT DOMPET -->
<div id="modal-edit-wallet" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeEditWalletModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditWalletModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Edit Rekening / Dompet</h3>
                <button type="button" onclick="closeEditWalletModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-wallet" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Rekening / Dompet</label>
                    <input type="text" id="edit-wallet-name" name="name" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Akun</label>
                    <select id="edit-wallet-type" name="type" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                        <option value="bank">Rekening Bank</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="cash">Uang Tunai / Cash</option>
                        <option value="investment">Investasi</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Penyesuaian Saldo (Rp)</label>
                    <input type="number" id="edit-wallet-balance" name="balance" step="any" min="0" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 active:scale-98 transition-all ios-press">
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

