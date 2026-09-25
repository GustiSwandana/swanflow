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
                        Anggaran Bulanan
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Kontrol & pantau batas belanja</span>
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
                <h2 class="text-4xl sm:text-5xl font-black tracking-tight text-white drop-shadow-sm mb-4">
                    Rp {{ number_format($totalBudget, 0, ',', '.') }}
                </h2>
                <div class="flex items-end justify-between mt-1">
                    <div>
                        <span class="text-[10px] text-slate-300 dark:text-slate-500 font-semibold block">Total Terpakai</span>
                        <span class="text-xs font-black text-white dark:text-slate-300 block">Rp {{ number_format($totalSpent, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-300 dark:text-slate-500 font-semibold block">Sisa Keseluruhan</span>
                        <span class="text-xs font-black text-white dark:text-slate-300 block">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <!-- Main Progress Bar -->
                <div class="mt-4 h-2.5 w-full bg-black/20 dark:bg-slate-950/50 rounded-full overflow-hidden shadow-inner border border-white/5">
                    @php
                        $mainPercentage = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;
                        $mainBg = $mainPercentage >= 100 ? 'bg-rose-500' : ($mainPercentage >= 80 ? 'bg-amber-400' : 'bg-emerald-400');
                    @endphp
                    <div class="h-full rounded-full {{ $mainBg }} transition-all duration-1000 ease-out" style="width: {{ min(100, $mainPercentage) }}%"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- 2. SCREEN CONTROLS: Month Selector & Add Budget Button -->
    <div class="flex items-center justify-between gap-2.5 pt-1">
        <!-- Native Month Picker Pill -->
        <div class="relative shrink-0">
            <button type="button" 
                    onclick="document.getElementById('budgets-month-input').click()"
                    class="h-9 px-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 shadow-xs flex items-center gap-2 active:scale-95 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <span class="text-xs font-bold text-slate-800 dark:text-white capitalize">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }}
                </span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <input type="month" 
                   id="budgets-month-input"
                   value="{{ $selectedMonth }}" 
                   onchange="window.location.href = '{{ route('budgets.index') }}?month=' + this.value"
                   onclick="try { this.showPicker() } catch(e) {}"
                   class="absolute inset-0 opacity-0 pointer-events-auto cursor-pointer w-full h-full [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-clear-button]:hidden" 
                   aria-label="Pilih Bulan Anggaran">
        </div>

        <div class="flex items-center gap-1.5">
            <a href="{{ route('categories.index') }}" 
               class="h-9 px-3 rounded-xl bg-slate-100/90 dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-white/10 font-bold text-[11px] shadow-2xs flex items-center gap-1.5 active:scale-95 transition-all"
               title="Kelola Kategori">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z"/>
                </svg>
                <span>Kategori</span>
            </a>

            <button type="button" 
                    onclick="openAddBudgetModal()" 
                    class="h-9 px-3.5 rounded-xl bg-slate-900 dark:bg-emerald-600 text-white font-bold text-[11px] shadow-sm flex items-center gap-1.5 active:scale-95 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Atur Pos</span>
            </button>
        </div>
    </div>

    <!-- 3. SECTION TITLE -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h2 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Rincian Anggaran Kategori</h2>
            <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Progress penggunaan dana per pos pengeluaran</p>
        </div>
        <span class="text-xs font-bold text-slate-400">{{ count($budgets) }} dari {{ count($availableCategories) }} kategori</span>
    </div>

    <!-- 4. CATEGORY BUDGETS LIST -->
    <div class="space-y-3">
        @forelse($budgets as $b)
            @php
                $catColor = $b->category->color ?: '#10B981';
                $pct = $b->percentage;
                $barColor = $pct >= 100 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                $badgeBg = $pct >= 100 ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/25' : ($pct >= 75 ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/25' : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/25');
            @endphp
            <div class="liquid-card rounded-[26px] p-4 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-xs hover:shadow-md backdrop-blur-2xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Category Color/Icon Indicator -->
                        <div class="w-12 h-12 rounded-[18px] flex items-center justify-center shrink-0 border shadow-xs"
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
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black border {{ $badgeBg }}">
                            {{ $pct }}%
                        </span>

                        <!-- Edit Button -->
                        <button type="button" 
                                onclick='openEditBudgetModal(@json($b))'
                                aria-label="Edit Anggaran"
                                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400 bg-slate-100/80 hover:bg-emerald-50 dark:bg-slate-800/80 dark:hover:bg-emerald-500/20 rounded-xl transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('budgets.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus anggaran kategori ini?')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    aria-label="Hapus Anggaran"
                                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400 bg-slate-100/80 hover:bg-rose-50 dark:bg-slate-800/80 dark:hover:bg-rose-500/20 rounded-xl transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
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
<div id="modal-add-budget" class="fixed inset-0 z-50 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeAddBudgetModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeAddBudgetModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Atur Anggaran Bulanan Baru</h3>
                <button type="button" onclick="closeAddBudgetModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Kategori Pengeluaran</label>
                    <select name="category_id" required class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Target Batas Anggaran (Rp)</label>
                    <input type="number" name="amount" min="1" step="any" required placeholder="Contoh: 1500000" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Periode Berlaku</label>
                    <select name="month" class="w-full h-12 px-4 appearance-none bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        <option value="">Rutin Tiap Bulan (Default)</option>
                        <option value="{{ $selectedMonth }}" selected>Bulan Ini Saja ({{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Misal: Batas jajan mingguan & cafe" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4 pb-8">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all ios-press">
                        Simpan Anggaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. MODAL EDIT ANGGARAN -->
<div id="modal-edit-budget" class="fixed inset-0 z-50 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeEditBudgetModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeEditBudgetModal()"></div>
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 mb-5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight" id="edit-budget-title">Perbarui Anggaran</h3>
                <button type="button" onclick="closeEditBudgetModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-budget" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Target Batas Anggaran (Rp)</label>
                    <input type="number" id="edit-budget-amount" name="amount" min="1" step="any" required class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan</label>
                    <input type="text" id="edit-budget-notes" name="notes" class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="pt-4 pb-8">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all ios-press">
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

