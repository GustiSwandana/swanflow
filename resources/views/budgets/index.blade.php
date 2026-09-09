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
                Anggaran Bulanan
            </h1>
            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Kontrol & pantau batas belanja</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-5">

    <!-- Screen Top Bar with Month Selector & Add Budget Button -->
    <div class="flex items-center justify-between gap-3">
        <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center" id="budget-month-form">
            <div class="relative flex items-center">
                <input type="month" 
                       name="month" 
                       value="{{ $selectedMonth }}" 
                       onchange="document.getElementById('budget-month-form').submit()" 
                       class="min-h-[42px] pl-10 pr-4 py-2 bg-white/80 dark:bg-slate-900/80 border border-white/60 dark:border-white/10 rounded-[20px] text-xs font-black text-slate-800 dark:text-slate-100 shadow-xs backdrop-blur-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </form>

        <button type="button" 
                onclick="openAddBudgetModal()" 
                class="min-h-[42px] px-4 py-2 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-lg shadow-emerald-500/25 border border-white/20 flex items-center gap-2 active:scale-95 transition-all ios-press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Atur Anggaran</span>
        </button>
    </div>

    <!-- 1. MONTHLY BUDGET OVERVIEW CARD (APPLE WALLET LIQUID GLASS) -->
    <div class="relative overflow-hidden rounded-[32px] p-6 text-white shadow-2xl border border-white/20 dark:border-white/10 backdrop-blur-2xl bg-gradient-to-br from-emerald-700 via-teal-800 to-slate-950 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
        <!-- Specular highlight line -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/40 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-emerald-400/20 rounded-full blur-2xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -left-8 -top-8 w-32 h-32 bg-teal-300/15 rounded-full blur-2xl pointer-events-none animate-liquid-orb-2"></div>

        <div class="relative z-10 space-y-3.5">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-100 dark:text-slate-400 uppercase tracking-wider">
                    Total Anggaran ({{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})
                </span>
                @if($overBudgetCount > 0)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-[14px] text-[10px] font-extrabold bg-rose-500/25 text-white dark:text-rose-300 border border-rose-400/30 backdrop-blur-xs">
                        {{ $overBudgetCount }} Kategori Melebihi
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-[14px] text-[10px] font-extrabold bg-white/20 dark:bg-emerald-500/20 text-white dark:text-emerald-300 border border-white/30 dark:border-emerald-500/30 backdrop-blur-xs">
                        {{ count($budgets) }} Kategori Terpantau
                    </span>
                @endif
            </div>

            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">
                    Rp {{ number_format($totalBudget, 0, ',', '.') }}
                </h2>
                <div class="flex items-center justify-between text-xs mt-1.5 font-semibold">
                    <span class="text-emerald-100 dark:text-slate-300">Terpakai: <strong class="text-white">Rp {{ number_format($totalSpent, 0, ',', '.') }}</strong></span>
                    <span class="font-extrabold {{ $totalRemaining == 0 && $totalSpent > $totalBudget ? 'text-rose-200 dark:text-rose-400' : 'text-emerald-200 dark:text-emerald-400' }}">
                        {{ $totalRemaining == 0 && $totalSpent > $totalBudget ? 'Overbudget' : 'Sisa Rp ' . number_format($totalRemaining, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div class="w-full bg-black/25 dark:bg-slate-700/60 rounded-full h-3 p-0.5 overflow-hidden backdrop-blur-xs">
                <div class="h-full rounded-full transition-all duration-700 {{ $overallPercentage >= 100 ? 'bg-rose-400 dark:bg-rose-500 shadow-xs' : ($overallPercentage >= 75 ? 'bg-amber-300 dark:bg-amber-400 shadow-xs' : 'bg-emerald-300 dark:bg-emerald-400 shadow-xs') }}"
                     style="width: {{ min(100, $overallPercentage) }}%"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] font-bold text-emerald-100/90 dark:text-slate-400 pt-0.5">
                <span>Penggunaan: <strong class="text-white dark:text-slate-200">{{ $overallPercentage }}%</strong></span>
                <span>Batas Maksimum 100%</span>
            </div>
        </div>
    </div>

    <!-- 2. SECTION TITLE -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Rincian Anggaran Kategori</h2>
            <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Progress penggunaan dana per pos pengeluaran</p>
        </div>
        <span class="text-xs font-bold text-slate-400">{{ count($budgets) }} dari {{ count($availableCategories) }} kategori</span>
    </div>

    <!-- 3. CATEGORY BUDGETS LIST -->
    <div class="space-y-3">
        @forelse($budgets as $b)
            @php
                $catColor = $b->category->color ?: '#10B981';
                $pct = $b->percentage;
                $barColor = $pct >= 100 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                $badgeBg = $pct >= 100 ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/25' : ($pct >= 75 ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/25' : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/25');
            @endphp
            <div class="liquid-card rounded-[26px] p-4.5 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm hover:shadow-md backdrop-blur-2xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Category Color/Icon Indicator -->
                        <div class="w-12 h-12 rounded-[20px] flex items-center justify-center shrink-0 border shadow-xs"
                             style="background-color: {{ $catColor }}18; color: {{ $catColor }}; border-color: {{ $catColor }}35;">
                            <x-category-icon :category="$b->category" :name="$b->category->name ?? ''" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-extrabold text-slate-800 dark:text-white truncate">
                                {{ $b->category->name ?? 'Kategori' }}
                            </h3>
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 block">
                                {{ $b->month ? 'Khusus ' . \Carbon\Carbon::createFromFormat('Y-m', $b->month)->translatedFormat('M Y') : 'Rutin Tiap Bulan' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-[12px] text-xs font-black border {{ $badgeBg }}">
                            {{ $pct }}%
                        </span>

                        <!-- Edit Button -->
                        <button type="button" 
                                onclick='openEditBudgetModal(@json($b))'
                                aria-label="Edit Anggaran"
                                class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 flex items-center justify-center text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 rounded-[16px] liquid-glass border border-white/60 dark:border-white/10 shadow-xs active:scale-95 transition-all ios-press">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('budgets.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus anggaran kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    aria-label="Hapus Anggaran"
                                    class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 flex items-center justify-center text-slate-400 hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400 rounded-[16px] liquid-glass border border-white/60 dark:border-white/10 shadow-xs active:scale-95 transition-all ios-press">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Spending Progress Bar -->
                <div class="space-y-2">
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $barColor }}"
                             style="width: {{ min(100, $pct) }}%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">
                            Terpakai <strong class="text-slate-800 dark:text-slate-200">Rp {{ number_format($b->spent, 0, ',', '.') }}</strong> dari Rp {{ number_format($b->amount, 0, ',', '.') }}
                        </span>
                        <span class="font-extrabold {{ $b->is_over ? 'text-rose-500' : 'text-emerald-500' }}">
                            @if($b->is_over)
                                +Rp {{ number_format($b->over_amount, 0, ',', '.') }} (Lewat)
                            @else
                                Sisa Rp {{ number_format($b->remaining, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 text-center space-y-3.5 liquid-card rounded-[28px] bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 backdrop-blur-2xl">
                <div class="w-14 h-14 mx-auto rounded-[22px] liquid-glass text-emerald-500 flex items-center justify-center border border-white/60 dark:border-white/10 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">Belum ada anggaran bulanan</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Atur batas belanja per kategori agar keuangan Anda terkontrol rapi.</p>
                </div>
                <button type="button" 
                        onclick="openAddBudgetModal()" 
                        class="px-5 py-2.5 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-black text-xs shadow-lg shadow-emerald-500/25 border border-white/20 inline-flex items-center gap-2 active:scale-95 transition-all ios-press">
                    + Buat Anggaran Pertama
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- 4. MODAL TAMBAH ANGGARAN -->
<div id="modal-add-budget" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeAddBudgetModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddBudgetModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Atur Anggaran Bulanan Baru</h3>
                <button type="button" onclick="closeAddBudgetModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Kategori Pengeluaran</label>
                    <select name="category_id" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Batas Anggaran (Rp)</label>
                    <input type="number" name="amount" min="1" step="any" required placeholder="Contoh: 1500000" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Periode Berlaku</label>
                    <select name="month" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                        <option value="">Rutin Tiap Bulan (Default)</option>
                        <option value="{{ $selectedMonth }}" selected>Bulan Ini Saja ({{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Misal: Batas jajan mingguan & cafe" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[46px] py-3 rounded-[20px] bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-lg shadow-emerald-500/30 border border-white/20 active:scale-98 transition-all ios-press">
                        Simpan Anggaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. MODAL EDIT ANGGARAN -->
<div id="modal-edit-budget" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeEditBudgetModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] shadow-2xl p-6 modal-sheet-safe border-t border-white/60 dark:border-white/10 backdrop-blur-3xl pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-12 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditBudgetModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight" id="edit-budget-title">Perbarui Anggaran</h3>
                <button type="button" onclick="closeEditBudgetModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/80 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-budget" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Batas Anggaran (Rp)</label>
                    <input type="number" id="edit-budget-amount" name="amount" min="1" step="any" required class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan</label>
                    <input type="text" id="edit-budget-notes" name="notes" class="w-full min-h-[46px] px-4 py-2.5 bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-[20px] text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-emerald-500">
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
    function openAddBudgetModal() {
        window.openSheetModal('modal-add-budget');
    }
    function closeAddBudgetModal() {
        window.closeSheetModal('modal-add-budget');
    }
    function openEditBudgetModal(budget) {
        document.getElementById('edit-budget-title').innerText = 'Perbarui Anggaran: ' + (budget.category?.name || 'Kategori');
        document.getElementById('edit-budget-amount').value = budget.amount;
        document.getElementById('edit-budget-notes').value = budget.notes || '';
        document.getElementById('form-edit-budget').action = '/budgets/' + budget.id;
        window.openSheetModal('modal-edit-budget');
    }
    function closeEditBudgetModal() {
        window.closeSheetModal('modal-edit-budget');
    }
</script>
@endsection

