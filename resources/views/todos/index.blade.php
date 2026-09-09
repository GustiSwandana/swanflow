@extends('layouts.mobile')

@section('title', 'Aktivitas')

@section('custom_header')
{{-- Emerald gradient header (konsisten dengan halaman lain) --}}
<div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 pb-4" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.75rem));">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-48 h-48 bg-white rounded-full -translate-y-24 translate-x-24"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full translate-y-16 -translate-x-16"></div>
    </div>
    <div class="relative px-5 pb-2">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dashboard') }}" 
                   class="min-w-[40px] min-h-[40px] -ml-2 flex items-center justify-center text-white/80 hover:text-white rounded-full hover:bg-white/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 active:scale-95 transition-all" 
                   aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-white tracking-tight leading-tight">Aktivitas & Tugas</h1>
                    <p class="text-xs text-white/70 dark:text-slate-400">Catatan kegiatan & to-do list</p>
                </div>
            </div>

            <!-- Tombol Tambah di Header -->
            <button type="button" 
                    onclick="openAddModal()" 
                    aria-label="Tambah Aktivitas"
                    class="px-3.5 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-white font-bold text-xs backdrop-blur-sm border border-white/30 flex items-center gap-1.5 active:scale-95 transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah</span>
            </button>
        </div>

        {{-- Summary badges (Interactive Filter Cards) --}}
        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('todos.index', ['tab' => 'today']) }}"
               class="rounded-2xl p-2.5 sm:p-3 border transition-all duration-200 flex flex-col justify-between active:scale-95 {{ $tab === 'today' ? 'bg-white/30 border-white/60 shadow-md ring-2 ring-white/40' : 'bg-white/15 hover:bg-white/20 border-white/20' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-white/90">Hari Ini</span>
                    <span class="w-2 h-2 rounded-full bg-amber-400 {{ $todayTodos->count() > 0 ? 'animate-pulse' : '' }}"></span>
                </div>
                <p class="text-xl sm:text-2xl font-black text-amber-300 tracking-tight mt-1">{{ $todayTodos->count() }}</p>
            </a>

            <a href="{{ route('todos.index', ['tab' => 'upcoming']) }}"
               class="rounded-2xl p-2.5 sm:p-3 border transition-all duration-200 flex flex-col justify-between active:scale-95 {{ $tab === 'upcoming' ? 'bg-white/30 border-white/60 shadow-md ring-2 ring-white/40' : 'bg-white/15 hover:bg-white/20 border-white/20' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-white/90">Mendatang</span>
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                </div>
                <p class="text-xl sm:text-2xl font-black text-sky-300 tracking-tight mt-1">{{ $upcomingTodos->count() }}</p>
            </a>

            <a href="{{ route('todos.index', ['tab' => 'completed']) }}"
               class="rounded-2xl p-2.5 sm:p-3 border transition-all duration-200 flex flex-col justify-between active:scale-95 {{ $tab === 'completed' ? 'bg-white/30 border-white/60 shadow-md ring-2 ring-white/40' : 'bg-white/15 hover:bg-white/20 border-white/20' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-white/90">Selesai</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                </div>
                <p class="text-xl sm:text-2xl font-black text-emerald-300 tracking-tight mt-1">{{ $completedTodos->count() }}</p>
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="bg-slate-50 dark:bg-slate-900 rounded-t-[32px] pt-4 px-4 pb-[max(6rem,calc(5.25rem+var(--sab,0px)))] shadow-2xl -mt-4 relative z-10 border-t border-slate-200 dark:border-slate-800/80 flex-1 flex flex-col min-h-full space-y-4 text-slate-800 dark:text-white transition-colors animate-swan-in">
    {{-- Quick Add Card --}}
    <div>
        <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center justify-between gap-3 transition-colors">
            <button type="button" onclick="openAddModal()" class="flex items-center gap-3 min-w-0 flex-1 text-left group cursor-pointer">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div class="truncate">
                    <span class="text-xs font-bold text-slate-800 dark:text-white block group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">Tulis aktivitas atau agenda baru...</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Jadwalkan agenda & deadline</span>
                </div>
            </button>
            <button type="button" onclick="openAddModal()" aria-label="Catat Aktivitas Baru" class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold shadow-xs active:scale-95 transition-all cursor-pointer">
                + Catat
            </button>
        </div>
    </div>

    {{-- Tab filter (Segmented Control) --}}
    <div class="sticky top-0 z-10 bg-slate-50/95 dark:bg-slate-900/95 backdrop-blur-md py-1">
        <div class="flex bg-slate-200/70 dark:bg-slate-800/70 rounded-2xl p-1 gap-1 border border-slate-300/40 dark:border-slate-700/60">
            <a href="{{ route('todos.index', ['tab' => 'today']) }}"
                class="flex-1 py-2 text-center text-xs rounded-xl transition-all flex items-center justify-center gap-1.5 {{ $tab === 'today' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 font-extrabold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }}">
                <span>Hari Ini</span>
                @if($todayTodos->count() > 0)
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ $tab === 'today' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-slate-300/80 dark:bg-slate-600 text-slate-700 dark:text-slate-200' }}">{{ $todayTodos->count() }}</span>
                @endif
            </a>
            <a href="{{ route('todos.index', ['tab' => 'upcoming']) }}"
                class="flex-1 py-2 text-center text-xs rounded-xl transition-all flex items-center justify-center gap-1.5 {{ $tab === 'upcoming' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 font-extrabold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }}">
                <span>Mendatang</span>
                @if($upcomingTodos->count() > 0)
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ $tab === 'upcoming' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-slate-300/80 dark:bg-slate-600 text-slate-700 dark:text-slate-200' }}">{{ $upcomingTodos->count() }}</span>
                @endif
            </a>
            <a href="{{ route('todos.index', ['tab' => 'completed']) }}"
                class="flex-1 py-2 text-center text-xs rounded-xl transition-all flex items-center justify-center gap-1.5 {{ $tab === 'completed' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 font-extrabold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }}">
                <span>Selesai</span>
                @if($completedTodos->count() > 0)
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ $tab === 'completed' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-slate-300/80 dark:bg-slate-600 text-slate-700 dark:text-slate-200' }}">{{ $completedTodos->count() }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Content area --}}
    <div class="space-y-3">

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
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeAddModal()"></div>

    {{-- Sheet Container --}}
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
        {{-- Drag handle --}}
        <div class="flex justify-center pt-3 pb-1 cursor-pointer" onclick="closeAddModal()">
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
        </div>

        {{-- Modal Header --}}
        <div class="px-5 pt-2 pb-3 flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Aktivitas</h3>
                    <p class="text-[11px] text-slate-400">Jadwalkan kegiatan atau target Anda</p>
                </div>
            </div>
            <button type="button" onclick="closeAddModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white active:scale-90 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('todos.store') }}" method="POST" class="px-5 pt-4 pb-[max(2rem,calc(var(--sab)+1.5rem))] space-y-4">
            @csrf
            <input type="hidden" name="_redirect_tab" value="{{ $tab }}">

            {{-- Judul Aktivitas --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Judul Aktivitas <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" required placeholder="Contoh: Meeting dengan klien"
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition-all shadow-2xs">
            </div>

            {{-- Prioritas & Kategori --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Prioritas</label>
                    <div class="relative">
                        <select name="priority"
                            class="w-full px-3.5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm appearance-none pr-8 transition-all shadow-2xs">
                            <option value="medium">⚡ Sedang</option>
                            <option value="high">🔴 Tinggi</option>
                            <option value="low">⬇️ Rendah</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kategori</label>
                    <div class="relative">
                        <select name="category"
                            class="w-full px-3.5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm appearance-none pr-8 transition-all shadow-2xs">
                            <option value="">📌 Umum</option>
                            <option value="kerja">💼 Kerja</option>
                            <option value="pribadi">👤 Pribadi</option>
                            <option value="belanja">🛒 Belanja</option>
                            <option value="keuangan">💰 Keuangan</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tanggal Deadline & Quick Chips --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tanggal Deadline</label>
                    <span class="text-[11px] text-slate-400 font-normal">Opsional</span>
                </div>
                <input type="date" name="due_date" id="addDueDate"
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition-all shadow-2xs">
                
                {{-- Quick Date Preset Chips --}}
                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                    <button type="button" onclick="setQuickDate('addDueDate', 'today')" class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/20 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 active:scale-95 transition-all">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setQuickDate('addDueDate', 'tomorrow')" class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/20 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 active:scale-95 transition-all">
                        Besok
                    </button>
                    <button type="button" onclick="setQuickDate('addDueDate', 'next_week')" class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/20 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 active:scale-95 transition-all">
                        +1 Minggu
                    </button>
                    <button type="button" onclick="clearDate('addDueDate')" class="px-2 py-1 text-slate-400 hover:text-rose-500 text-[11px] font-medium transition-all ml-auto">
                        Bersihkan
                    </button>
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan (opsional)</label>
                <textarea name="description" rows="2" placeholder="Tambahkan catatan atau detail aktivitas..."
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none transition-all shadow-2xs"></textarea>
            </div>

            {{-- Submit Action --}}
            <button type="submit"
                class="w-full py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm transition-all shadow-lg shadow-emerald-500/25 active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Simpan Aktivitas</span>
            </button>
        </form>
    </div>
    </div>
</div>

{{-- ===== MODAL EDIT AKTIVITAS ===== --}}
<div id="editModal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0" onclick="closeEditModal()"></div>

    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 overflow-y-auto no-scrollbar">
        <div class="flex justify-center pt-3 pb-1 cursor-pointer" onclick="closeEditModal()">
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
        </div>

        <div class="px-5 pt-2 pb-3 flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Aktivitas</h3>
                    <p class="text-[11px] text-slate-400">Perbarui rincian aktivitas</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white active:scale-90 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editForm" method="POST" class="px-5 pt-4 pb-[max(2rem,calc(var(--sab)+1.5rem))] space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_redirect_tab" value="{{ $tab }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Judul Aktivitas <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" id="editTitle" required
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition-all shadow-2xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Prioritas</label>
                    <div class="relative">
                        <select name="priority" id="editPriority"
                            class="w-full px-3.5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm appearance-none pr-8 transition-all shadow-2xs">
                            <option value="medium">⚡ Sedang</option>
                            <option value="high">🔴 Tinggi</option>
                            <option value="low">⬇️ Rendah</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kategori</label>
                    <div class="relative">
                        <select name="category" id="editCategory"
                            class="w-full px-3.5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm appearance-none pr-8 transition-all shadow-2xs">
                            <option value="">📌 Umum</option>
                            <option value="kerja">💼 Kerja</option>
                            <option value="pribadi">👤 Pribadi</option>
                            <option value="belanja">🛒 Belanja</option>
                            <option value="keuangan">💰 Keuangan</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tanggal Deadline</label>
                    <span class="text-[11px] text-slate-400 font-normal">Opsional</span>
                </div>
                <input type="date" name="due_date" id="editDueDate"
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition-all shadow-2xs">
                
                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                    <button type="button" onclick="setQuickDate('editDueDate', 'today')" class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/20 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 active:scale-95 transition-all">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setQuickDate('editDueDate', 'tomorrow')" class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/20 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 active:scale-95 transition-all">
                        Besok
                    </button>
                    <button type="button" onclick="setQuickDate('editDueDate', 'next_week')" class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/20 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 active:scale-95 transition-all">
                        +1 Minggu
                    </button>
                    <button type="button" onclick="clearDate('editDueDate')" class="px-2 py-1 text-slate-400 hover:text-rose-500 text-[11px] font-medium transition-all ml-auto">
                        Bersihkan
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan (opsional)</label>
                <textarea name="description" id="editDescription" rows="2" placeholder="Tambahkan catatan detail..."
                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none transition-all shadow-2xs"></textarea>
            </div>

            <button type="submit"
                class="w-full py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm transition-all shadow-lg shadow-emerald-500/25 active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <span>Simpan Perubahan</span>
            </button>
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

