@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass Header -->
    <div class="relative overflow-hidden bg-gradient-to-b from-emerald-600/95 via-emerald-600/85 to-teal-700/90 dark:from-slate-900/95 dark:via-emerald-950/90 dark:to-slate-950/95 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-200" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Rim -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-teal-500/20 rounded-full blur-3xl pointer-events-none animate-liquid-orb-2"></div>

        <!-- Top Navigation Bar -->
        <div class="relative z-10 flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                        Kategori Transaksi
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Kelola pos keuangan Anda</span>
                </div>
            </div>

            <!-- Theme Toggle -->
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
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-4 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Pull Handle Indicator -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- 1. TABS & ADD ACTION -->
    <div class="flex items-center justify-between gap-2.5">
        <div class="ios-segmented-track p-1 rounded-[22px] flex flex-1 border border-white/20 dark:border-white/10 shadow-inner backdrop-blur-xl bg-slate-200/60 dark:bg-slate-900/60">
            <button type="button" 
                    id="tab-btn-expense" 
                    onclick="switchCategoryTab('expense')" 
                    class="flex-1 min-h-[40px] py-2 px-3 rounded-[18px] text-xs font-black transition-all ios-segmented-thumb bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-sm ios-press cursor-pointer">
                Pengeluaran ({{ count($expenseCategories) }})
            </button>
            <button type="button" 
                    id="tab-btn-income" 
                    onclick="switchCategoryTab('income')" 
                    class="flex-1 min-h-[40px] py-2 px-3 rounded-[18px] text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all ios-press cursor-pointer">
                Pemasukan ({{ count($incomeCategories) }})
            </button>
        </div>

        <button type="button" 
                onclick="openAddCategoryModal()" 
                class="min-h-[44px] px-4 py-2 rounded-[20px] bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-xs shadow-lg shadow-emerald-500/25 flex items-center gap-1.5 shrink-0 ios-press active:scale-95 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah</span>
        </button>
    </div>

    <!-- 2. SEARCH BAR -->
    <div class="relative">
        <input type="text" 
               id="category-search" 
               oninput="filterCategories(this.value)" 
               placeholder="Cari kategori..." 
               class="w-full h-11 pl-10 pr-4 bg-white/70 dark:bg-slate-900/70 border border-slate-200/80 dark:border-white/10 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition-all">
        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
    </div>

    <!-- 3. EXPENSE CATEGORIES LIST -->
    <div id="section-expense-categories" class="space-y-2.5">
        <div class="space-y-2.5" id="expense-list">
            @forelse($expenseCategories as $cat)
                <div class="category-card liquid-card rounded-[22px] p-3.5 bg-white/85 dark:bg-slate-900/85 border border-white/70 dark:border-white/10 shadow-xs backdrop-blur-2xl flex items-center justify-between transition-all" data-name="{{ strtolower($cat->name) }}">
                    <div class="flex items-center gap-3 min-w-0 pr-2">
                        <div class="w-11 h-11 rounded-[16px] flex items-center justify-center shrink-0 shadow-2xs border border-black/5 dark:border-white/5"
                             style="background-color: {{ $cat->color ?? '#F43F5E' }}22; color: {{ $cat->color ?? '#F43F5E' }};">
                            <x-category-icon :category="$cat" :name="$cat->name" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight leading-tight">{{ $cat->name }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">{{ $cat->transactions_count }} transaksi</span>
                                <span class="text-[10px] text-slate-300 dark:text-slate-600">•</span>
                                <span class="text-[10px] font-bold text-rose-500/90 dark:text-rose-400">Pengeluaran</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($cat->user_id)
                            <button type="button"
                                    data-id="{{ $cat->id }}"
                                    data-name="{{ $cat->name }}"
                                    data-type="expense"
                                    data-color="{{ $cat->color ?? '#F43F5E' }}"
                                    data-icon="{{ $cat->icon ?? 'tag' }}"
                                    onclick="openEditCategoryModalFromBtn(this)" 
                                    class="w-8.5 h-8.5 flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 ios-press active:scale-95 transition-all cursor-pointer"
                                    title="Edit Kategori">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                            </button>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ addslashes($cat->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8.5 h-8.5 flex items-center justify-center text-slate-400 hover:text-rose-600 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 ios-press active:scale-95 transition-all cursor-pointer"
                                        title="Hapus Kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200/60 dark:border-white/5 px-2.5 py-1 rounded-xl">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <span>Bawaan</span>
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-xs font-semibold text-slate-400 liquid-card bg-white/70 dark:bg-slate-900/60 rounded-[24px]">
                    Belum ada kategori pengeluaran.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 4. INCOME CATEGORIES LIST (Hidden by default) -->
    <div id="section-income-categories" class="space-y-2.5 hidden">
        <div class="space-y-2.5" id="income-list">
            @forelse($incomeCategories as $cat)
                <div class="category-card liquid-card rounded-[22px] p-3.5 bg-white/85 dark:bg-slate-900/85 border border-white/70 dark:border-white/10 shadow-xs backdrop-blur-2xl flex items-center justify-between transition-all" data-name="{{ strtolower($cat->name) }}">
                    <div class="flex items-center gap-3 min-w-0 pr-2">
                        <div class="w-11 h-11 rounded-[16px] flex items-center justify-center shrink-0 shadow-2xs border border-black/5 dark:border-white/5"
                             style="background-color: {{ $cat->color ?? '#10B981' }}22; color: {{ $cat->color ?? '#10B981' }};">
                            <x-category-icon :category="$cat" :name="$cat->name" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight leading-tight">{{ $cat->name }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">{{ $cat->transactions_count }} transaksi</span>
                                <span class="text-[10px] text-slate-300 dark:text-slate-600">•</span>
                                <span class="text-[10px] font-bold text-emerald-500/90 dark:text-emerald-400">Pemasukan</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($cat->user_id)
                            <button type="button"
                                    data-id="{{ $cat->id }}"
                                    data-name="{{ $cat->name }}"
                                    data-type="income"
                                    data-color="{{ $cat->color ?? '#10B981' }}"
                                    data-icon="{{ $cat->icon ?? 'tag' }}"
                                    onclick="openEditCategoryModalFromBtn(this)" 
                                    class="w-8.5 h-8.5 flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 ios-press active:scale-95 transition-all cursor-pointer"
                                    title="Edit Kategori">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                            </button>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ addslashes($cat->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8.5 h-8.5 flex items-center justify-center text-slate-400 hover:text-rose-600 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 ios-press active:scale-95 transition-all cursor-pointer"
                                        title="Hapus Kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200/60 dark:border-white/5 px-2.5 py-1 rounded-xl">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <span>Bawaan</span>
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-xs font-semibold text-slate-400 liquid-card bg-white/70 dark:bg-slate-900/60 rounded-[24px]">
                    Belum ada kategori pemasukan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- 5. MODAL TAMBAH KATEGORI -->
<div id="modal-add-category" class="fixed inset-0 z-50 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeAddCategoryModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-5 border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[88vh] no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddCategoryModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80 mb-4">
                <div>
                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block -mb-0.5">Kategori Baru</span>
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight">Tambah Kategori</h3>
                </div>
                <button type="button" onclick="closeAddCategoryModal()" aria-label="Tutup modal" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors ios-press cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="icon" id="add-category-icon" value="utensils">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Kategori</label>
                    <select name="type" id="add-category-type" onchange="onAddTypeChange(this.value)" required class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Belanja Bulanan, Langganan, Gym" class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <!-- Pilihan Ikon Preset -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Simbol Ikon</label>
                    <div class="grid grid-cols-6 gap-2" id="add-icon-grid">
                        @php
                            $availableIcons = [
                                ['id' => 'utensils', 'label' => 'Makan', 'type' => 'food'],
                                ['id' => 'coffee', 'label' => 'Kopi', 'type' => 'coffee'],
                                ['id' => 'car', 'label' => 'Transport', 'type' => 'transport'],
                                ['id' => 'shopping-bag', 'label' => 'Belanja', 'type' => 'shopping'],
                                ['id' => 'film', 'label' => 'Hiburan', 'type' => 'entertainment'],
                                ['id' => 'receipt', 'label' => 'Tagihan', 'type' => 'bills'],
                                ['id' => 'heart-pulse', 'label' => 'Kesehatan', 'type' => 'health'],
                                ['id' => 'banknotes', 'label' => 'Gaji', 'type' => 'salary'],
                                ['id' => 'arrow-trending-up', 'label' => 'Investasi', 'type' => 'investment'],
                                ['id' => 'sparkles', 'label' => 'Bonus', 'type' => 'bonus'],
                                ['id' => 'debt', 'label' => 'Hutang', 'type' => 'debt'],
                                ['id' => 'tag', 'label' => 'Lainnya', 'type' => 'default'],
                            ];
                        @endphp
                        @foreach($availableIcons as $ai)
                            <button type="button" 
                                    onclick="selectCategoryIcon('add', '{{ $ai['id'] }}')" 
                                    data-icon="{{ $ai['id'] }}"
                                    class="icon-btn-add p-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col items-center justify-center hover:bg-emerald-500/10 transition-all cursor-pointer {{ $loop->first ? 'ring-2 ring-emerald-500 border-emerald-500 bg-emerald-500/10' : '' }}"
                                    title="{{ $ai['label'] }}">
                                <x-category-icon :category="new \App\Models\Category(['icon' => $ai['id']])" class="w-5 h-5 text-slate-700 dark:text-slate-200" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Pilihan Warna & Presets -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Warna Penanda</label>
                    <div class="flex items-center gap-2">
                        @php
                            $colorPresets = ['#F43F5E', '#F59E0B', '#10B981', '#06B6D4', '#3B82F6', '#6366F1', '#8B5CF6', '#EC4899'];
                        @endphp
                        @foreach($colorPresets as $cp)
                            <button type="button" 
                                    onclick="selectCategoryColor('add', '{{ $cp }}')" 
                                    style="background-color: {{ $cp }};" 
                                    class="w-7 h-7 rounded-full border-2 border-white dark:border-slate-900 shadow-xs cursor-pointer active:scale-95 transition-transform" 
                                    title="{{ $cp }}"></button>
                        @endforeach
                        <input type="color" name="color" id="add-category-color" value="#F43F5E" class="w-8 h-8 rounded-full border border-slate-200 dark:border-white/10 p-0.5 cursor-pointer bg-slate-50 dark:bg-slate-800 ml-auto">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-3.5 rounded-2xl active:scale-95 transition-all shadow-lg shadow-emerald-500/25 shrink-0 ios-press cursor-pointer">
                        Simpan Kategori Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 6. MODAL EDIT KATEGORI -->
<div id="modal-edit-category" class="fixed inset-0 z-50 hidden transition-all duration-300" style="z-index: 9999;" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeEditCategoryModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-5 border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[88vh] no-scrollbar text-slate-800 dark:text-slate-100">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditCategoryModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80 mb-4">
                <div>
                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block -mb-0.5">Ubah Kategori</span>
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight">Edit Kategori</h3>
                </div>
                <button type="button" onclick="closeEditCategoryModal()" aria-label="Tutup modal" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors ios-press cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-category" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="icon" id="edit-category-icon" value="tag">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Kategori</label>
                    <select id="edit-category-type" name="type" required class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Kategori</label>
                    <input type="text" id="edit-category-name" name="name" required class="w-full h-11 px-3.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <!-- Pilihan Ikon Preset -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Simbol Ikon</label>
                    <div class="grid grid-cols-6 gap-2" id="edit-icon-grid">
                        @foreach($availableIcons as $ai)
                            <button type="button" 
                                    onclick="selectCategoryIcon('edit', '{{ $ai['id'] }}')" 
                                    data-icon="{{ $ai['id'] }}"
                                    class="icon-btn-edit p-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col items-center justify-center hover:bg-emerald-500/10 transition-all cursor-pointer"
                                    title="{{ $ai['label'] }}">
                                <x-category-icon :category="new \App\Models\Category(['icon' => $ai['id']])" class="w-5 h-5 text-slate-700 dark:text-slate-200" />
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Pilihan Warna & Presets -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Warna Penanda</label>
                    <div class="flex items-center gap-2">
                        @foreach($colorPresets as $cp)
                            <button type="button" 
                                    onclick="selectCategoryColor('edit', '{{ $cp }}')" 
                                    style="background-color: {{ $cp }};" 
                                    class="w-7 h-7 rounded-full border-2 border-white dark:border-slate-900 shadow-xs cursor-pointer active:scale-95 transition-transform" 
                                    title="{{ $cp }}"></button>
                        @endforeach
                        <input type="color" name="color" id="edit-category-color" class="w-8 h-8 rounded-full border border-slate-200 dark:border-white/10 p-0.5 cursor-pointer bg-slate-50 dark:bg-slate-800 ml-auto">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-3.5 rounded-2xl active:scale-95 transition-all shadow-lg shadow-emerald-500/25 shrink-0 ios-press cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    let activeTab = 'expense';

    function switchCategoryTab(type) {
        activeTab = type;
        const expenseSection = document.getElementById('section-expense-categories');
        const incomeSection = document.getElementById('section-income-categories');
        const expenseBtn = document.getElementById('tab-btn-expense');
        const incomeBtn = document.getElementById('tab-btn-income');

        if (type === 'expense') {
            expenseSection.classList.remove('hidden');
            incomeSection.classList.add('hidden');
            expenseBtn.className = 'flex-1 min-h-[40px] py-2 px-3 rounded-[18px] text-xs font-black transition-all ios-segmented-thumb bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-sm ios-press cursor-pointer';
            incomeBtn.className = 'flex-1 min-h-[40px] py-2 px-3 rounded-[18px] text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all ios-press cursor-pointer';
        } else {
            expenseSection.classList.add('hidden');
            incomeSection.classList.remove('hidden');
            incomeBtn.className = 'flex-1 min-h-[40px] py-2 px-3 rounded-[18px] text-xs font-black transition-all ios-segmented-thumb bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm ios-press cursor-pointer';
            expenseBtn.className = 'flex-1 min-h-[40px] py-2 px-3 rounded-[18px] text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all ios-press cursor-pointer';
        }
        
        // Re-filter if search is active
        const searchVal = document.getElementById('category-search').value;
        if (searchVal) filterCategories(searchVal);
    }

    function filterCategories(query) {
        const q = query.toLowerCase().trim();
        const activeContainer = activeTab === 'expense' ? document.getElementById('expense-list') : document.getElementById('income-list');
        if (!activeContainer) return;
        
        const cards = activeContainer.querySelectorAll('.category-card');
        cards.forEach(card => {
            const name = card.dataset.name || '';
            if (name.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function selectCategoryIcon(context, iconId) {
        const hiddenInput = document.getElementById(context + '-category-icon');
        if (hiddenInput) hiddenInput.value = iconId;

        const btns = document.querySelectorAll('.icon-btn-' + context);
        btns.forEach(btn => {
            if (btn.dataset.icon === iconId) {
                btn.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500', 'bg-emerald-500/10');
            } else {
                btn.classList.remove('ring-2', 'ring-emerald-500', 'border-emerald-500', 'bg-emerald-500/10');
            }
        });
    }

    function selectCategoryColor(context, hex) {
        const colorInput = document.getElementById(context + '-category-color');
        if (colorInput) colorInput.value = hex;
    }

    function onAddTypeChange(type) {
        if (type === 'income') {
            selectCategoryColor('add', '#10B981');
            selectCategoryIcon('add', 'banknotes');
        } else {
            selectCategoryColor('add', '#F43F5E');
            selectCategoryIcon('add', 'utensils');
        }
    }

    function openAddCategoryModal() {
        // Set type default according to active tab
        const typeSelect = document.getElementById('add-category-type');
        if (typeSelect) {
            typeSelect.value = activeTab;
            onAddTypeChange(activeTab);
        }
        window.openSheetModal('modal-add-category');
    }

    function closeAddCategoryModal() {
        window.closeSheetModal('modal-add-category');
    }

    function openEditCategoryModalFromBtn(btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const type = btn.dataset.type;
        const color = btn.dataset.color || (type === 'income' ? '#10B981' : '#F43F5E');
        const icon = btn.dataset.icon || 'tag';

        document.getElementById('form-edit-category').action = '/categories/' + id;
        document.getElementById('edit-category-name').value = name;
        document.getElementById('edit-category-type').value = type;
        document.getElementById('edit-category-color').value = color;

        selectCategoryIcon('edit', icon);

        window.openSheetModal('modal-edit-category');
    }

    function closeEditCategoryModal() {
        window.closeSheetModal('modal-edit-category');
    }
</script>
@endpush
