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
                Anggaran Bulanan
            </h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">Kontrol & pantau batas belanja</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    <!-- Screen Top Bar with Month Selector & Add Budget Button -->
    <div class="flex items-center justify-between gap-2">
        <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center" id="budget-month-form">
            <div class="relative flex items-center">
                <input type="month" 
                       name="month" 
                       value="{{ $selectedMonth }}" 
                       onchange="document.getElementById('budget-month-form').submit()" 
                       class="min-h-[40px] pl-9 pr-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-bold text-slate-800 dark:text-slate-100 shadow-2xs focus:outline-hidden focus:border-emerald-500 cursor-pointer">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </form>

        <button type="button" 
                onclick="openAddBudgetModal()" 
                class="min-h-[40px] px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm shadow-emerald-600/30 flex items-center gap-1.5 active:scale-95 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Atur Anggaran</span>
        </button>
    </div>

    <!-- 1. MONTHLY BUDGET OVERVIEW CARD -->
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-3xl p-5 text-white shadow-xl border border-emerald-700 dark:border-slate-800/80 relative overflow-hidden transition-colors">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 dark:bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-100 dark:text-slate-300 uppercase tracking-wider">
                    Total Anggaran ({{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})
                </span>
                @if($overBudgetCount > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-white dark:text-rose-300 border border-rose-400/30">
                        {{ $overBudgetCount }} Kategori Melebihi
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 dark:bg-emerald-500/20 text-white dark:text-emerald-300 border border-white/30 dark:border-emerald-500/30">
                        {{ count($budgets) }} Kategori Terpantau
                    </span>
                @endif
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">
                    Rp {{ number_format($totalBudget, 0, ',', '.') }}
                </h2>
                <div class="flex items-center justify-between text-xs mt-1">
                    <span class="text-emerald-100 dark:text-slate-300">Terpakai: <strong class="text-white">Rp {{ number_format($totalSpent, 0, ',', '.') }}</strong></span>
                    <span class="font-bold {{ $totalRemaining == 0 && $totalSpent > $totalBudget ? 'text-rose-200 dark:text-rose-400' : 'text-emerald-200 dark:text-emerald-400' }}">
                        {{ $totalRemaining == 0 && $totalSpent > $totalBudget ? 'Overbudget' : 'Sisa Rp ' . number_format($totalRemaining, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div class="w-full bg-black/20 dark:bg-slate-700/60 rounded-full h-2.5 overflow-hidden">
                <div class="h-2.5 rounded-full transition-all duration-700 {{ $overallPercentage >= 100 ? 'bg-rose-400 dark:bg-rose-500' : ($overallPercentage >= 75 ? 'bg-amber-300 dark:bg-amber-400' : 'bg-emerald-300 dark:bg-emerald-500') }}"
                     style="width: {{ min(100, $overallPercentage) }}%"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-emerald-100 dark:text-slate-400 pt-1">
                <span>Penggunaan: <strong class="text-white dark:text-slate-200">{{ $overallPercentage }}%</strong></span>
                <span>Batas Maksimum</span>
            </div>
        </div>
    </div>

    <!-- 2. SECTION TITLE -->
    <div class="flex items-center justify-between pt-1">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Rincian Anggaran Kategori</h2>
        <span class="text-xs text-slate-400">{{ count($budgets) }} dari {{ count($availableCategories) }} kategori</span>
    </div>

    <!-- 3. CATEGORY BUDGETS LIST -->
    <div class="space-y-3">
        @forelse($budgets as $b)
            @php
                $catColor = $b->category->color ?: '#10B981';
                $pct = $b->percentage;
                $barColor = $pct >= 100 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                $badgeBg = $pct >= 100 ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20' : ($pct >= 75 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20');
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800 transition-colors space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Category Color/Icon Indicator -->
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 border shadow-2xs"
                             style="background-color: {{ $catColor }}18; color: {{ $catColor }}; border-color: {{ $catColor }}35;">
                            <x-category-icon :category="$b->category" :name="$b->category->name ?? ''" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                {{ $b->category->name ?? 'Kategori' }}
                            </h3>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block">
                                {{ $b->month ? 'Khusus ' . \Carbon\Carbon::createFromFormat('Y-m', $b->month)->translatedFormat('M Y') : 'Rutin Tiap Bulan' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-extrabold border {{ $badgeBg }}">
                            {{ $pct }}%
                        </span>

                        <!-- Edit Button -->
                        <button type="button" 
                                onclick='openEditBudgetModal(@json($b))'
                                aria-label="Edit Anggaran"
                                class="min-w-[34px] min-h-[34px] w-8 h-8 flex items-center justify-center text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition-all">
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
                                    class="min-w-[34px] min-h-[34px] w-8 h-8 flex items-center justify-center text-slate-300 dark:text-slate-600 hover:text-rose-500 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 active:scale-95 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Spending Progress Bar -->
                <div class="space-y-1.5">
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full transition-all duration-500 {{ $barColor }}"
                             style="width: {{ min(100, $pct) }}%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">
                            Terpakai <strong class="text-slate-800 dark:text-slate-200">Rp {{ number_format($b->spent, 0, ',', '.') }}</strong> dari Rp {{ number_format($b->amount, 0, ',', '.') }}
                        </span>
                        <span class="font-bold {{ $b->is_over ? 'text-rose-500' : 'text-emerald-500' }}">
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
            <div class="p-8 text-center space-y-3 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm shadow-emerald-600/30 inline-flex items-center gap-1.5 active:scale-95 transition-all">
                    + Buat Anggaran Pertama
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- 4. MODAL TAMBAH ANGGARAN -->
<div id="modal-add-budget" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeAddBudgetModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddBudgetModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Atur Anggaran Bulanan Baru</h3>
                <button type="button" onclick="closeAddBudgetModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Pilih Kategori Pengeluaran</label>
                    <select name="category_id" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Target Batas Anggaran (Rp)</label>
                    <input type="number" name="amount" min="1" step="any" required placeholder="Contoh: 1500000" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Periode Berlaku</label>
                    <select name="month" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        <option value="">Rutin Tiap Bulan (Default)</option>
                        <option value="{{ $selectedMonth }}" selected>Bulan Ini Saja ({{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Misal: Batas jajan mingguan & cafe" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30">
                        Simpan Anggaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. MODAL EDIT ANGGARAN -->
<div id="modal-edit-budget" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeEditBudgetModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditBudgetModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white" id="edit-budget-title">Perbarui Anggaran</h3>
                <button type="button" onclick="closeEditBudgetModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-budget" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Target Batas Anggaran (Rp)</label>
                    <input type="number" id="edit-budget-amount" name="amount" min="1" step="any" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Catatan</label>
                    <input type="text" id="edit-budget-notes" name="notes" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
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
