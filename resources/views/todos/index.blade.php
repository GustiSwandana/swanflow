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
        <div class="relative z-10 flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <div class="flex items-center gap-1.5">
                        <h1 class="text-base font-black text-white tracking-tight leading-tight">Aktivitas</h1>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-400/25 text-emerald-200 border border-emerald-300/30 shadow-2xs backdrop-blur-md shrink-0">To-Do</span>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-200/80">Daftar tugas & target harian</span>
                </div>
            </div>

            <!-- Right Actions: Tambah & Theme Toggle -->
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" 
                        onclick="openAddModal()" 
                        class="w-10 h-10 rounded-[18px] bg-gradient-to-tr from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 active:scale-95 text-white shadow-lg shadow-emerald-500/30 border border-white/25 flex items-center justify-center transition-all cursor-pointer ios-press" 
                        title="Tambah Aktivitas" 
                        aria-label="Tambah Aktivitas">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
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

        @php
            $todayCount = $todayTodos->count();
            $completedCount = $completedTodos->count();
            $totalTracked = $todayCount + $completedCount;
            $progressPercent = $totalTracked > 0 ? round(($completedCount / $totalTracked) * 100) : 0;
        @endphp

        <!-- Hero Productivity Card (Liquid Glass - Matches SwanDrive & Dashboard layout) -->
        <div class="relative z-10 liquid-glass rounded-[26px] p-4 border border-white/25 shadow-xl space-y-3 bg-white/10 backdrop-blur-2xl">
            <div class="flex items-center justify-between gap-2 min-w-0">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-200/90 truncate">Status Produktivitas</span>
                <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full border shadow-2xs {{ $progressPercent >= 100 && $totalTracked > 0 ? 'bg-emerald-400/30 text-emerald-100 border-emerald-300/40' : 'bg-white/15 text-white border-white/20' }}">
                    {{ $progressPercent }}% Selesai
                </span>
            </div>

            <div class="flex items-baseline justify-between gap-2 min-w-0">
                <div class="flex items-baseline gap-1.5 min-w-0 truncate">
                    <span class="text-2xl sm:text-3xl font-black text-white tracking-tight truncate">{{ $todayCount }}</span>
                    <span class="text-xs font-bold text-emerald-200/80 shrink-0">Tugas Hari Ini</span>
                </div>
                <div class="flex items-center gap-2 shrink-0 text-[11px] text-emerald-100/90 font-bold">
                    <span>{{ $completedCount }} Selesai</span>
                    <span class="text-white/40">•</span>
                    <span>{{ $upcomingTodos->count() }} Mendatang</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div>
                <div class="w-full bg-slate-950/60 rounded-full h-2.5 overflow-hidden p-0.5 border border-white/15">
                    <div class="h-full rounded-full transition-all duration-500 bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-200"
                         style="width: {{ max(4, $progressPercent) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-emerald-200/80 mt-1 font-semibold">
                    <span>{{ $todayCount === 0 ? 'Semua tugas hari ini beres!' : $todayCount . ' tugas menunggu diselesaikan' }}</span>
                    <span>Target: 100%</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(11rem,calc(10rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-[calc(100dvh-12rem)] space-y-4 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    {{-- 1. TAB NAVIGATION (Apple iOS Segmented Control - matches Kategori & Drive) --}}
    <div class="ios-segmented-track p-1 rounded-[22px] flex items-center gap-1 w-full bg-slate-200/60 dark:bg-white/5 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-inner">
        <a href="{{ route('todos.index', ['tab' => 'today']) }}"
           class="flex-1 py-2 px-1.5 rounded-[18px] text-xs font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $tab === 'today' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
            <span>Hari Ini</span>
            <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black {{ $tab === 'today' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $todayTodos->count() }}</span>
        </a>

        <a href="{{ route('todos.index', ['tab' => 'upcoming']) }}"
           class="flex-1 py-2 px-1.5 rounded-[18px] text-xs font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $tab === 'upcoming' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
            <span>Mendatang</span>
            <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black {{ $tab === 'upcoming' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $upcomingTodos->count() }}</span>
        </a>

        <a href="{{ route('todos.index', ['tab' => 'completed']) }}"
           class="flex-1 py-2 px-1.5 rounded-[18px] text-xs font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $tab === 'completed' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
            <span>Selesai</span>
            <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black {{ $tab === 'completed' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $completedTodos->count() }}</span>
        </a>
    </div>

    {{-- 2. Content List Area --}}
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
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Tanggal Deadline</label>
                        <div class="flex items-center gap-1.5 relative z-20">
                            <button type="button" 
                                    data-date-target="addDueDate" 
                                    data-date-preset="today"
                                    onclick="setDatePreset('addDueDate', 'today')" 
                                    class="date-preset-btn text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Hari Ini
                            </button>
                            <button type="button" 
                                    data-date-target="addDueDate" 
                                    data-date-preset="tomorrow"
                                    onclick="setDatePreset('addDueDate', 'tomorrow')" 
                                    class="date-preset-btn text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Besok
                            </button>
                            <button type="button" 
                                    data-date-target="addDueDate" 
                                    data-date-preset="in_7_days"
                                    onclick="setDatePreset('addDueDate', 'in_7_days')" 
                                    class="date-preset-btn text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                +1 Mgg
                            </button>
                            <button type="button" 
                                    data-date-target="addDueDate" 
                                    data-date-preset="clear"
                                    onclick="setDatePreset('addDueDate', 'clear')" 
                                    class="date-preset-btn text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 transition-all cursor-pointer shadow-2xs">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="relative group">
                        <div id="addDueDate-display" class="flex items-center justify-between w-full min-h-[48px] px-3.5 py-2 bg-slate-50/90 dark:bg-slate-800/80 hover:bg-slate-100/90 dark:hover:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-2xl transition-all shadow-xs group-focus-within:border-emerald-500 group-focus-within:ring-2 group-focus-within:ring-emerald-500/20">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span id="addDueDate-label" class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                        Pilih Tanggal
                                    </span>
                                    <span id="addDueDate-sublabel" class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">
                                        Opsional
                                    </span>
                                </div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>

                        <input id="addDueDate" 
                               type="date" 
                               name="due_date" 
                               value="{{ now()->format('Y-m-d') }}" 
                               aria-label="Pilih Tanggal Deadline"
                               onchange="syncDateDisplay('addDueDate')"
                               onclick="try { this.showPicker(); } catch(e) {}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 [color-scheme:light] dark:[color-scheme:dark]">
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
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Tanggal Deadline</label>
                        <div class="flex items-center gap-1.5 relative z-20">
                            <button type="button" 
                                    data-date-target="editDueDate" 
                                    data-date-preset="today"
                                    onclick="setDatePreset('editDueDate', 'today')" 
                                    class="date-preset-btn text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Hari Ini
                            </button>
                            <button type="button" 
                                    data-date-target="editDueDate" 
                                    data-date-preset="tomorrow"
                                    onclick="setDatePreset('editDueDate', 'tomorrow')" 
                                    class="date-preset-btn text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Besok
                            </button>
                            <button type="button" 
                                    data-date-target="editDueDate" 
                                    data-date-preset="in_7_days"
                                    onclick="setDatePreset('editDueDate', 'in_7_days')" 
                                    class="date-preset-btn text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                +1 Mgg
                            </button>
                            <button type="button" 
                                    data-date-target="editDueDate" 
                                    data-date-preset="clear"
                                    onclick="setDatePreset('editDueDate', 'clear')" 
                                    class="date-preset-btn text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 transition-all cursor-pointer shadow-2xs">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="relative group">
                        <div id="editDueDate-display" class="flex items-center justify-between w-full min-h-[48px] px-3.5 py-2 bg-slate-50/90 dark:bg-slate-800/80 hover:bg-slate-100/90 dark:hover:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-2xl transition-all shadow-xs group-focus-within:border-emerald-500 group-focus-within:ring-2 group-focus-within:ring-emerald-500/20">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span id="editDueDate-label" class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                        Pilih Tanggal
                                    </span>
                                    <span id="editDueDate-sublabel" class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">
                                        Opsional
                                    </span>
                                </div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>

                        <input id="editDueDate" 
                               type="date" 
                               name="due_date" 
                               value="" 
                               aria-label="Pilih Tanggal Deadline"
                               onchange="syncDateDisplay('editDueDate')"
                               onclick="try { this.showPicker(); } catch(e) {}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 [color-scheme:light] dark:[color-scheme:dark]">
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
    const dueInput = document.getElementById('addDueDate');
    if (dueInput && !dueInput.value) {
        const now = new Date();
        dueInput.value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    }
    syncDateDisplay('addDueDate');
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
    syncDateDisplay('editDueDate');
    window.openSheetModal('editModal');
}
function closeEditModal() {
    window.closeSheetModal('editModal');
}

function setQuickDate(inputId, type) {
    if (type === 'today') {
        setDatePreset(inputId, 'today');
    } else if (type === 'tomorrow') {
        setDatePreset(inputId, 'tomorrow');
    } else if (type === 'next_week') {
        setDatePreset(inputId, 'in_7_days');
    }
}

function clearDate(inputId) {
    setDatePreset(inputId, 'clear');
}

document.addEventListener('DOMContentLoaded', function() {
    syncDateDisplay('addDueDate');
    syncDateDisplay('editDueDate');
});

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
