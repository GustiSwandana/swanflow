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
                Kategori Transaksi
            </h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">Sesuaikan pos pengeluaran & pemasukan</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. TABS & ADD BUTTON -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex p-1 bg-slate-200/70 dark:bg-slate-800 rounded-2xl gap-1 flex-1">
            <button type="button" 
                    id="tab-btn-expense" 
                    onclick="switchCategoryTab('expense')" 
                    class="flex-1 min-h-[40px] py-1.5 px-3 rounded-xl text-xs font-bold bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs transition-all">
                Pengeluaran ({{ count($expenseCategories) }})
            </button>
            <button type="button" 
                    id="tab-btn-income" 
                    onclick="switchCategoryTab('income')" 
                    class="flex-1 min-h-[40px] py-1.5 px-3 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 transition-all">
                Pemasukan ({{ count($incomeCategories) }})
            </button>
        </div>

        <button type="button" 
                onclick="openAddCategoryModal()" 
                class="min-h-[44px] px-3.5 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm shadow-emerald-600/30 flex items-center gap-1.5 shrink-0 active:scale-95 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah</span>
        </button>
    </div>

    <!-- 2. EXPENSE CATEGORIES LIST -->
    <div id="section-expense-categories" class="space-y-2.5">
        <div class="grid grid-cols-2 gap-2.5">
            @forelse($expenseCategories as $cat)
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 shadow-sm border border-slate-100 dark:border-slate-800/80 flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background-color: {{ $cat->color ?? '#F43F5E' }}20; color: {{ $cat->color ?? '#F43F5E' }};">
                            <x-category-icon :category="$cat" :name="$cat->name" class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $cat->name }}</h3>
                            <span class="text-[10px] text-slate-400 block">{{ $cat->transactions_count }} transaksi</span>
                        </div>
                    </div>

                    @if($cat->user_id)
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button"
                                    data-id="{{ $cat->id }}"
                                    data-name="{{ $cat->name }}"
                                    data-type="{{ $cat->type }}"
                                    data-color="{{ $cat->color ?? '#F43F5E' }}"
                                    onclick="openEditCategoryModalFromBtn(this)" 
                                    class="p-1.5 text-slate-400 hover:text-emerald-500 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                            </button>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md">Bawaan</span>
                    @endif
                </div>
            @empty
                <div class="col-span-2 text-center py-6 text-xs text-slate-400">Belum ada kategori pengeluaran.</div>
            @endforelse
        </div>
    </div>

    <!-- 3. INCOME CATEGORIES LIST (Hidden by default) -->
    <div id="section-income-categories" class="space-y-2.5 hidden">
        <div class="grid grid-cols-2 gap-2.5">
            @forelse($incomeCategories as $cat)
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 shadow-sm border border-slate-100 dark:border-slate-800/80 flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background-color: {{ $cat->color ?? '#10B981' }}20; color: {{ $cat->color ?? '#10B981' }};">
                            <x-category-icon :category="$cat" :name="$cat->name" class="w-4 h-4" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $cat->name }}</h3>
                            <span class="text-[10px] text-slate-400 block">{{ $cat->transactions_count }} transaksi</span>
                        </div>
                    </div>

                    @if($cat->user_id)
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button"
                                    data-id="{{ $cat->id }}"
                                    data-name="{{ $cat->name }}"
                                    data-type="{{ $cat->type }}"
                                    data-color="{{ $cat->color ?? '#10B981' }}"
                                    onclick="openEditCategoryModalFromBtn(this)" 
                                    class="p-1.5 text-slate-400 hover:text-emerald-500 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                            </button>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md">Bawaan</span>
                    @endif
                </div>
            @empty
                <div class="col-span-2 text-center py-6 text-xs text-slate-400">Belum ada kategori pemasukan.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- MODAL TAMBAH KATEGORI -->
<div id="modal-add-category" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeAddCategoryModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeAddCategoryModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tambah Kategori Baru</h3>
                <button type="button" onclick="closeAddCategoryModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="icon" value="tag">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jenis Kategori</label>
                    <select name="type" id="add-category-type" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Belanja Bulanan, Langganan, Hobi" class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Warna Label</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="color" value="#F43F5E" class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 p-1 cursor-pointer bg-slate-50 dark:bg-slate-800">
                        <span class="text-xs text-slate-400">Pilih warna penanda kategori</span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30">
                        Simpan Kategori Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT KATEGORI -->
<div id="modal-edit-category" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeEditCategoryModal()"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800/80 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-4 cursor-pointer" onclick="closeEditCategoryModal()"></div>
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Edit Kategori</h3>
                <button type="button" onclick="closeEditCategoryModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-category" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="icon" value="tag">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jenis Kategori</label>
                    <select id="edit-category-type" name="type" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Kategori</label>
                    <input type="text" id="edit-category-name" name="name" required class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Warna Label</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="edit-category-color" name="color" class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 p-1 cursor-pointer bg-slate-50 dark:bg-slate-800">
                        <span class="text-xs text-slate-400">Pilih warna penanda</span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full min-h-[44px] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30">
                        Simpan Perubahan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function switchCategoryTab(type) {
        const expenseSection = document.getElementById('section-expense-categories');
        const incomeSection = document.getElementById('section-income-categories');
        const expenseBtn = document.getElementById('tab-btn-expense');
        const incomeBtn = document.getElementById('tab-btn-income');

        if (type === 'expense') {
            expenseSection.classList.remove('hidden');
            incomeSection.classList.add('hidden');
            expenseBtn.className = 'flex-1 min-h-[40px] py-1.5 px-3 rounded-xl text-xs font-bold bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs transition-all';
            incomeBtn.className = 'flex-1 min-h-[40px] py-1.5 px-3 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 transition-all';
        } else {
            expenseSection.classList.add('hidden');
            incomeSection.classList.remove('hidden');
            incomeBtn.className = 'flex-1 min-h-[40px] py-1.5 px-3 rounded-xl text-xs font-bold bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs transition-all';
            expenseBtn.className = 'flex-1 min-h-[40px] py-1.5 px-3 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 transition-all';
        }
    }

    function openAddCategoryModal() {
        window.openSheetModal('modal-add-category');
    }
    function closeAddCategoryModal() {
        window.closeSheetModal('modal-add-category');
    }
    function openEditCategoryModalFromBtn(btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const type = btn.dataset.type;
        const color = btn.dataset.color || '#F43F5E';

        document.getElementById('form-edit-category').action = '/categories/' + id;
        document.getElementById('edit-category-name').value = name;
        document.getElementById('edit-category-type').value = type;
        document.getElementById('edit-category-color').value = color;
        window.openSheetModal('modal-edit-category');
    }
    function openEditCategoryModal(cat) {
        document.getElementById('form-edit-category').action = '/categories/' + cat.id;
        document.getElementById('edit-category-name').value = cat.name;
        document.getElementById('edit-category-type').value = cat.type;
        document.getElementById('edit-category-color').value = cat.color || '#F43F5E';
        window.openSheetModal('modal-edit-category');
    }
    function closeEditCategoryModal() {
        window.closeSheetModal('modal-edit-category');
    }
</script>
@endsection
