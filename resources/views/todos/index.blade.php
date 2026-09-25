@extends('layouts.mobile')

@section('title', 'Aktivitas')

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
                        Aktivitas
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Daftar tugas & kegiatan</span>
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
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(7.5rem,calc(6.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    {{-- 1. Summary Cards (Apple Liquid Glass Cards) --}}
    <div class="grid grid-cols-3 gap-3">
        <a href="{{ route('todos.index', ['tab' => 'today']) }}"
           class="overflow-hidden rounded-[24px] p-4 border transition-all flex flex-col justify-between active:scale-95 shadow-sm {{ $tab === 'today' ? 'bg-amber-500 text-white border-amber-600 shadow-amber-500/20' : 'liquid-card bg-white/80 dark:bg-slate-900/75 hover:bg-white dark:hover:bg-slate-800 border-white/60 dark:border-white/10 backdrop-blur-2xl' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $tab === 'today' ? 'text-amber-100' : 'text-slate-500 dark:text-slate-400' }}">Hari Ini</span>
                <span class="w-2 h-2 rounded-full {{ $tab === 'today' ? 'bg-white' : 'bg-amber-400' }} {{ $todayTodos->count() > 0 ? 'animate-pulse' : '' }}"></span>
            </div>
            <p class="text-2xl font-black tracking-tight mt-2 {{ $tab === 'today' ? 'text-white' : 'text-slate-800 dark:text-white' }}">{{ $todayTodos->count() }}</p>
        </a>

        <a href="{{ route('todos.index', ['tab' => 'upcoming']) }}"
           class="overflow-hidden rounded-[24px] p-4 border transition-all flex flex-col justify-between active:scale-95 shadow-sm {{ $tab === 'upcoming' ? 'bg-sky-500 text-white border-sky-600 shadow-sky-500/20' : 'liquid-card bg-white/80 dark:bg-slate-900/75 hover:bg-white dark:hover:bg-slate-800 border-white/60 dark:border-white/10 backdrop-blur-2xl' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $tab === 'upcoming' ? 'text-sky-100' : 'text-slate-500 dark:text-slate-400' }}">Mendatang</span>
                <span class="w-2 h-2 rounded-full {{ $tab === 'upcoming' ? 'bg-white' : 'bg-sky-400' }}"></span>
            </div>
            <p class="text-2xl font-black tracking-tight mt-2 {{ $tab === 'upcoming' ? 'text-white' : 'text-slate-800 dark:text-white' }}">{{ $upcomingTodos->count() }}</p>
        </a>

        <a href="{{ route('todos.index', ['tab' => 'completed']) }}"
           class="overflow-hidden rounded-[24px] p-4 border transition-all flex flex-col justify-between active:scale-95 shadow-sm {{ $tab === 'completed' ? 'bg-emerald-500 text-white border-emerald-600 shadow-emerald-500/20' : 'liquid-card bg-white/80 dark:bg-slate-900/75 hover:bg-white dark:hover:bg-slate-800 border-white/60 dark:border-white/10 backdrop-blur-2xl' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $tab === 'completed' ? 'text-emerald-100' : 'text-slate-500 dark:text-slate-400' }}">Selesai</span>
                <span class="w-2 h-2 rounded-full {{ $tab === 'completed' ? 'bg-white' : 'bg-emerald-400' }}"></span>
            </div>
            <p class="text-2xl font-black tracking-tight mt-2 {{ $tab === 'completed' ? 'text-white' : 'text-slate-800 dark:text-white' }}">{{ $completedTodos->count() }}</p>
        </a>
    </div>

    {{-- 2. Quick Add Card --}}
    <div class="liquid-card p-2 rounded-[24px] bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-xs flex items-center justify-between gap-3 transition-colors backdrop-blur-2xl">
        <button type="button" onclick="openAddModal()" class="flex items-center gap-3 pl-2 min-w-0 flex-1 text-left group cursor-pointer">
            <div class="w-10 h-10 rounded-[16px] bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-400 flex items-center justify-center shrink-0 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-500/10 group-hover:text-emerald-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <div class="truncate">
                <span class="text-sm font-bold text-slate-600 dark:text-slate-400 block truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Catat agenda baru...</span>
            </div>
        </button>
        <button type="button" onclick="openAddModal()" aria-label="Catat Aktivitas Baru" class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm shadow-emerald-600/20 active:scale-95 transition-all cursor-pointer">
            Tambah
        </button>
    </div>

    {{-- 3. Content List Area --}}
    <div class="space-y-3 flex-1 flex flex-col pt-2 pb-4">
        @if($tab === 'today')
            @forelse($todayTodos as $todo)
                @include('todos._card', ['todo' => $todo])
            @empty
                @include('todos._empty', ['message' => 'Tidak ada aktivitas untuk hari ini', 'hint' => 'Tap tombol + Tambah untuk mencatat aktivitas baru'])
            @endforelse

        @elseif($tab === 'upcoming')
            @forelse($upcomingTodos as $todo)
                @include('todos._card', ['todo' => $todo])
            @empty
                @include('todos._empty', ['message' => 'Tidak ada aktivitas mendatang', 'hint' => 'Semua rencana kegiatan Anda tertata rapi!'])
            @endforelse

        @else
            @forelse($completedTodos as $todo)
                @include('todos._card', ['todo' => $todo, 'showCompleted' => true])
            @empty
                @include('todos._empty', ['message' => 'Belum ada aktivitas yang selesai', 'hint' => 'Aktivitas yang diselesaikan akan tersimpan di sini'])
            @endforelse
        @endif
    </div>
</div>
@endsection

@push('modals')
{{-- ===== MODAL TAMBAH AKTIVITAS ===== --}}
<div id="addModal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">

    {{-- Backdrop --}}
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeAddModal()"></div>

    {{-- Sheet Container --}}
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeAddModal()"></div>

            {{-- Modal Header --}}
            <div class="pb-4 flex items-center justify-between border-b border-slate-100 dark:border-white/5">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Tambah Aktivitas</h3>
                <button type="button" onclick="closeAddModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('todos.store') }}" method="POST" class="pt-4 pb-[max(1rem,calc(var(--sab)+0.5rem))] space-y-4">
                @csrf
                <input type="hidden" name="_redirect_tab" value="{{ $tab }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Aktivitas <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Meeting dengan klien"
                        class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Prioritas</label>
                        <select name="priority"
                            class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 appearance-none transition-all">
                            <option value="medium">⚡ Sedang</option>
                            <option value="high">🔴 Tinggi</option>
                            <option value="low">⬇️ Rendah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                        <select name="category"
                            class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 appearance-none transition-all">
                            <option value="">📌 Umum</option>
                            <option value="kerja">💼 Kerja</option>
                            <option value="pribadi">👤 Pribadi</option>
                            <option value="belanja">🛒 Belanja</option>
                            <option value="keuangan">💰 Keuangan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tanggal Deadline</label>
                        <span class="text-[11px] text-slate-400 font-semibold">Opsional</span>
                    </div>
                    <input type="date" name="due_date" id="addDueDate"
                        class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 transition-all">
                    
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <button type="button" onclick="setQuickDate('addDueDate', 'today')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-300 text-[11px] font-bold active:scale-95 transition-all">Hari Ini</button>
                        <button type="button" onclick="setQuickDate('addDueDate', 'tomorrow')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-300 text-[11px] font-bold active:scale-95 transition-all">Besok</button>
                        <button type="button" onclick="setQuickDate('addDueDate', 'next_week')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-300 text-[11px] font-bold active:scale-95 transition-all">+1 Minggu</button>
                        <button type="button" onclick="clearDate('addDueDate')" class="px-2 py-1 text-slate-400 hover:text-rose-500 text-[11px] font-bold transition-all ml-auto">Bersihkan</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan (opsional)</label>
                    <textarea name="description" rows="2" placeholder="Detail aktivitas..."
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 resize-none transition-all"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        Simpan Aktivitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT AKTIVITAS ===== --}}
<div id="editModal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeEditModal()"></div>

    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div class="modal-panel modal-sheet-safe w-full max-w-md bg-white/95 dark:bg-slate-900/95 backdrop-blur-3xl rounded-t-[36px] shadow-2xl p-6 border-t border-slate-200/80 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto max-h-[85vh] no-scrollbar">
            <div class="w-10 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" onclick="closeEditModal()"></div>
            
            <div class="pb-4 flex items-center justify-between border-b border-slate-100 dark:border-white/5 mb-5">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Edit Aktivitas</h3>
                <button type="button" onclick="closeEditModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="editForm" method="POST" class="pt-0 pb-[max(1rem,calc(var(--sab)+0.5rem))] space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_redirect_tab" value="{{ $tab }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Aktivitas <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" id="editTitle" required
                        class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Prioritas</label>
                        <select name="priority" id="editPriority"
                            class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 appearance-none transition-all">
                            <option value="medium">⚡ Sedang</option>
                            <option value="high">🔴 Tinggi</option>
                            <option value="low">⬇️ Rendah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                        <select name="category" id="editCategory"
                            class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 appearance-none transition-all">
                            <option value="">📌 Umum</option>
                            <option value="kerja">💼 Kerja</option>
                            <option value="pribadi">👤 Pribadi</option>
                            <option value="belanja">🛒 Belanja</option>
                            <option value="keuangan">💰 Keuangan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tanggal Deadline</label>
                        <span class="text-[11px] text-slate-400 font-semibold">Opsional</span>
                    </div>
                    <input type="date" name="due_date" id="editDueDate"
                        class="w-full h-12 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 transition-all">
                    
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <button type="button" onclick="setQuickDate('editDueDate', 'today')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-300 text-[11px] font-bold active:scale-95 transition-all">Hari Ini</button>
                        <button type="button" onclick="setQuickDate('editDueDate', 'tomorrow')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-300 text-[11px] font-bold active:scale-95 transition-all">Besok</button>
                        <button type="button" onclick="setQuickDate('editDueDate', 'next_week')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-300 text-[11px] font-bold active:scale-95 transition-all">+1 Minggu</button>
                        <button type="button" onclick="clearDate('editDueDate')" class="px-2 py-1 text-slate-400 hover:text-rose-500 text-[11px] font-bold transition-all ml-auto">Bersihkan</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan (opsional)</label>
                    <textarea name="description" id="editDescription" rows="2" placeholder="Detail aktivitas..."
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 resize-none transition-all"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
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

function openAddModal() {
    window.openSheetModal('addModal');
}
function closeAddModal() {
    window.closeSheetModal('addModal');
}

function openEditModal(target) {
    const form = document.getElementById('editForm');
    if (target instanceof HTMLElement) {
        const data = target.dataset;
        form.action = `/todos/${data.id}`;
        document.getElementById('editTitle').value = data.title || '';
        document.getElementById('editPriority').value = data.priority || 'medium';
        document.getElementById('editCategory').value = data.category || '';
        document.getElementById('editDueDate').value = data.dueDate || '';
        document.getElementById('editDescription').value = data.description || '';
    } else {
        const [id, title, priority, category, dueDate, description] = arguments;
        form.action = `/todos/${id}`;
        document.getElementById('editTitle').value = title || '';
        document.getElementById('editPriority').value = priority || 'medium';
        document.getElementById('editCategory').value = category || '';
        document.getElementById('editDueDate').value = dueDate || '';
        document.getElementById('editDescription').value = description || '';
    }
    window.openSheetModal('editModal');
}
function closeEditModal() {
    window.closeSheetModal('editModal');
}

function setQuickDate(inputId, type) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const d = new Date();
    if (type === 'tomorrow') {
        d.setDate(d.getDate() + 1);
    } else if (type === 'next_week') {
        d.setDate(d.getDate() + 7);
    }
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    input.value = `${yyyy}-${mm}-${dd}`;
}

function clearDate(inputId) {
    const input = document.getElementById(inputId);
    if (input) input.value = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

if (new URLSearchParams(window.location.search).has('open_add')) {
    openAddModal();
}
</script>
@endpush
