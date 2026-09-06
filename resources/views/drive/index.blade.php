@extends('layouts.mobile')

@section('title', 'SwanDrive - Penyimpanan & Transfer File')

@section('custom_header')
    <!-- Top Header: Gradient SwanDrive Header -->
    <div class="relative bg-gradient-to-br from-slate-900 via-teal-950 to-slate-900 px-4 pt-[max(0.75rem,calc(var(--sat)+0.5rem))] pb-7 text-white overflow-hidden shadow-lg border-b border-teal-900/40">
        <!-- Subtle Glow Effect (Emerald/Teal only) -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Top Navigation -->
        <div class="relative z-10 flex items-center justify-between mb-3.5 gap-2">
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 flex items-center justify-center transition-all border border-white/10 shrink-0" aria-label="Kembali ke Beranda">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h1 class="text-sm sm:text-base font-extrabold tracking-tight text-white leading-tight truncate">SwanDrive</h1>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-teal-500/30 text-teal-300 border border-teal-400/30 shrink-0">Vault</span>
                    </div>
                    <p class="text-[10px] text-teal-200/70 font-medium truncate mt-0.5">Penyimpanan & Terima Berkas</p>
                </div>
            </div>

            <!-- Top Action Buttons -->
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="openCreateDropModal()" class="h-8 px-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 active:scale-95 text-teal-100 hover:text-white text-xs font-bold flex items-center gap-1 transition-all shadow-xs cursor-pointer whitespace-nowrap" title="Buat Link Terima File">
                    <svg class="w-3.5 h-3.5 text-teal-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    <span class="text-[11px]">Drop Link</span>
                </button>
                <button type="button" onclick="document.getElementById('upload-input').click()" class="h-8 px-3 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-extrabold shadow-md shadow-teal-900/40 flex items-center gap-1 transition-all cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="text-[11px]">Upload</span>
                </button>
            </div>
        </div>

        <!-- Storage Summary Card with Clean Hierarchical Layout -->
        <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-2xl p-3.5 sm:p-4 border border-white/15 shadow-inner space-y-2.5">
            <!-- Row 1: Label & Ubah Ukuran button -->
            <div class="flex items-center justify-between gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-200/90">Kapasitas Penyimpanan</span>
                <button type="button" onclick="openQuotaModal()" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-teal-400/20 hover:bg-teal-400/35 text-teal-200 hover:text-white text-[10px] font-bold border border-teal-300/30 active:scale-95 transition-all cursor-pointer shadow-xs" title="Ubah Kapasitas Kuota">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                    <span>Ubah Ukuran</span>
                </button>
            </div>

            <!-- Row 2: Numbers & Percentage Badge -->
            <div class="flex items-baseline justify-between gap-2">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $formattedTotalSize }}</span>
                    <span class="text-xs font-semibold text-teal-200/80">/ {{ $formattedQuotaSize }}</span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[10px] text-teal-200/70 font-medium">{{ $totalFiles }} Berkas</span>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full border shadow-2xs {{ $storagePercent >= 90 ? 'bg-rose-500/30 text-rose-200 border-rose-400/40' : ($storagePercent >= 75 ? 'bg-amber-500/30 text-amber-200 border-amber-400/40' : 'bg-teal-500/30 text-teal-200 border-teal-400/30') }}">
                        {{ $storagePercent }}%
                    </span>
                </div>
            </div>

            <!-- Row 3: Visual Progress Track -->
            <div>
                <div class="w-full bg-slate-900/80 rounded-full h-2 overflow-hidden p-0.5 border border-white/10">
                    <div class="h-full rounded-full transition-all duration-500 {{ $storagePercent >= 90 ? 'bg-gradient-to-r from-rose-500 to-amber-500' : ($storagePercent >= 75 ? 'bg-gradient-to-r from-amber-400 to-yellow-400' : 'bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-300') }}"
                         style="width: {{ max(3, $storagePercent) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-teal-200/70 mt-1 font-medium">
                    <span>{{ $totalBytes >= $quotaBytes ? 'Kapasitas Penuh' : 'Tersisa ' . number_format(max(0, ($quotaBytes - $totalBytes)) / 1048576, 1, ',', '.') . ' MB' }}</span>
                    <span>Batas: {{ $formattedQuotaSize }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50 dark:bg-slate-900 rounded-t-[32px] pt-4 px-4 pb-28 shadow-2xl -mt-4 relative z-10 border-t border-slate-200 dark:border-slate-800 flex-1 flex flex-col min-h-full space-y-4 text-slate-800 dark:text-white transition-colors">
    <!-- Drag Handle -->
    <div class="w-12 h-1 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-1"></div>

    <!-- TAB SWITCHER: Berkas Saya vs Link Terima File -->
    <div class="flex p-1 bg-slate-200/80 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-inner">
        <a href="{{ route('drive.index', ['tab' => 'files']) }}"
           class="flex-1 py-2.5 rounded-xl text-xs font-extrabold text-center transition-all flex items-center justify-center gap-1.5 {{ $activeTab !== 'drops' ? 'bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 shadow-xs ring-1 ring-slate-200/50 dark:ring-slate-700/50' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
            </svg>
            <span>Berkas Saya</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $activeTab !== 'drops' ? 'bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300' : 'bg-slate-300 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $totalFiles }}</span>
        </a>
        <a href="{{ route('drive.index', ['tab' => 'drops']) }}"
           class="flex-1 py-2.5 rounded-xl text-xs font-extrabold text-center transition-all flex items-center justify-center gap-1.5 {{ $activeTab === 'drops' ? 'bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 shadow-xs ring-1 ring-slate-200/50 dark:ring-slate-700/50' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
            </svg>
            <span>Link Terima File</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $activeTab === 'drops' ? 'bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300' : 'bg-slate-300 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $uploadLinks->count() }}</span>
        </a>
    </div>

    @if($activeTab !== 'drops')
        <!-- TAB 1: BERKAS SAYA -->

        <!-- 1. FORM UPLOAD FILE (Interactive Dropzone) -->
        <div class="bg-white dark:bg-slate-950 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form id="upload-form" action="{{ route('drive.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                
                <!-- Hidden File Input -->
                <input type="file" id="upload-input" name="file" class="hidden" onchange="handleFileSelect(this)" required>

                <!-- Dropzone Box -->
                <div id="dropzone" onclick="document.getElementById('upload-input').click()"
                     class="border-2 border-dashed border-teal-400/50 dark:border-teal-600/40 rounded-xl p-4 flex flex-col items-center justify-center text-center cursor-pointer bg-teal-50/40 dark:bg-teal-950/20 hover:bg-teal-50/80 dark:hover:bg-teal-950/40 active:scale-[0.99] transition-all">
                    <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-1.5 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                    </div>
                    <span id="dropzone-text" class="text-xs font-bold text-slate-800 dark:text-slate-200">
                        Sentuh untuk upload berkas atau seret ke sini
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                        PDF, Dokumen, Excel, Gambar, ZIP (Maks 50 MB)
                    </span>
                </div>

                <!-- Upload Details Panel (Appears once file is chosen) -->
                <div id="upload-details" class="hidden space-y-2.5 pt-1">
                    <div class="flex items-center justify-between bg-slate-100 dark:bg-slate-900 px-3 py-2 rounded-xl text-xs">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <span class="font-bold text-teal-600 dark:text-teal-400" id="selected-ext">FILE</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200 truncate" id="selected-filename">Nama berkas</span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 shrink-0" id="selected-size">0 KB</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label for="file-title" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Judul / Label (Opsional)</label>
                            <input type="text" id="file-title" name="title" placeholder="Misal: Nota Pembelian MacBook" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label for="file-notes" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" id="file-notes" name="notes" placeholder="Catatan ringkas..." class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit" id="btn-submit-upload" class="flex-1 py-2.5 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-[0.98] text-white text-xs font-bold shadow-md shadow-teal-500/20 flex items-center justify-center gap-2 transition-all cursor-pointer">
                            <svg id="btn-upload-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <svg id="btn-upload-spinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="btn-upload-text">Simpan ke SwanDrive</span>
                        </button>
                        <button type="button" id="btn-cancel-upload" onclick="cancelUpload()" class="py-2.5 px-3 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all">
                            Batal
                        </button>
                    </div>

                    <!-- Progress Bar & Loading Status Container -->
                    <div id="upload-progress-container" class="hidden pt-2 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span id="upload-progress-status" class="font-bold text-teal-600 dark:text-teal-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="upload-status-text">Mengunggah berkas...</span>
                            </span>
                            <span id="upload-progress-percent" class="font-extrabold text-slate-800 dark:text-white">0%</span>
                        </div>
                        <!-- Progress Track -->
                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-300 dark:border-slate-700">
                            <div id="upload-progress-bar" class="bg-gradient-to-r from-teal-500 via-emerald-400 to-teal-500 h-full rounded-full transition-all duration-150 ease-out" style="width: 0%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                            <span id="upload-progress-bytes">0 KB / 0 KB</span>
                            <span>Mohon tidak menutup halaman</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick Drop Link Banner -->
        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-teal-50/80 dark:bg-teal-950/25 border border-teal-200/80 dark:border-teal-800/60 shadow-xs">
            <div class="flex items-center gap-3 min-w-0 pr-2">
                <div class="w-9 h-9 rounded-xl bg-teal-500/15 dark:bg-teal-500/25 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight truncate">
                        Minta file dari orang lain?
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                        Buat link khusus dengan batas waktu & ukuran file
                    </p>
                </div>
            </div>
            <button type="button" onclick="openCreateDropModal()" class="h-9 px-3.5 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-extrabold shrink-0 shadow-xs transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="whitespace-nowrap">Buat Link Drop</span>
            </button>
        </div>

        <!-- 2. SEARCH & FILTER BAR -->
        <div class="space-y-2.5">
            <!-- Search Input Form -->
            <form method="GET" action="{{ route('drive.index') }}" class="relative">
                <input type="hidden" name="tab" value="files">
                <input type="hidden" name="category" value="{{ $activeCategory }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama file, ekstensi, pengirim, atau catatan..."
                       class="w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs">
                <span class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                @if(!empty($search))
                    <a href="{{ route('drive.index', ['tab' => 'files', 'category' => $activeCategory]) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-bold">
                        ✕
                    </a>
                @endif
            </form>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 -mx-1 px-1">
                <a href="{{ route('drive.index', ['tab' => 'files', 'category' => 'all', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all {{ $activeCategory === 'all' ? 'bg-teal-500 text-white shadow-xs' : 'bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                    Semua ({{ $categoryCounts['all'] }})
                </a>
                <a href="{{ route('drive.index', ['tab' => 'files', 'category' => 'document', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all {{ $activeCategory === 'document' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                    📄 Dokumen ({{ $categoryCounts['document'] }})
                </a>
                <a href="{{ route('drive.index', ['tab' => 'files', 'category' => 'image', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all {{ $activeCategory === 'image' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                    🖼️ Gambar ({{ $categoryCounts['image'] }})
                </a>
                <a href="{{ route('drive.index', ['tab' => 'files', 'category' => 'archive', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all {{ $activeCategory === 'archive' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                    📦 Arsip ({{ $categoryCounts['archive'] }})
                </a>
                <a href="{{ route('drive.index', ['tab' => 'files', 'category' => 'other', 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all {{ $activeCategory === 'other' ? 'bg-slate-700 text-white shadow-xs' : 'bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                    📎 Lainnya ({{ $categoryCounts['other'] }})
                </a>
            </div>
        </div>

        <!-- 3. LIST OF STORED FILES -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Daftar Berkas Tersimpan ({{ $files->count() }})
                </h2>
                <span class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold">Tersimpan Aman</span>
            </div>

            @forelse($files as $file)
                @php
                    $meta = $file->categoryMeta();
                @endphp
                <div class="bg-white dark:bg-slate-950 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3 transition-all hover:border-teal-500/50">
                    <div class="flex items-start justify-between gap-3">
                        <!-- Icon + Name (Click to preview) -->
                        <div onclick="openPreviewModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ addslashes($file->original_name) }}', '{{ $file->formatted_size }}', '{{ strtolower($file->extension) }}', '{{ route('drive.preview', $file) }}', '{{ route('drive.download', $file) }}', '{{ $meta['label'] }}', '{{ addslashes($file->notes ?? '') }}', '{{ $file->created_at->format('d M Y, H:i') }}')"
                             class="flex items-start gap-3 min-w-0 flex-1 cursor-pointer group">
                            <div class="w-10 h-10 rounded-xl {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }} flex items-center justify-center shrink-0 font-extrabold text-xs uppercase shadow-2xs group-hover:scale-105 transition-transform">
                                {{ substr($file->extension, 0, 4) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate leading-tight group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors" title="{{ $file->title }}">
                                    {{ $file->title }}
                                </h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5" title="{{ $file->original_name }}">
                                    {{ $file->original_name }}
                                </p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1 text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                    <span>{{ $file->formatted_size }}</span>
                                    <span>•</span>
                                    <span>{{ $file->created_at->format('d M Y, H:i') }}</span>
                                    @if($file->download_count > 0)
                                        <span>•</span>
                                        <span class="text-teal-600 dark:text-teal-400 font-semibold">⬇ {{ $file->download_count }}x diunduh</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge (Public Share or Received via Drop) -->
                        <div class="shrink-0 flex flex-col items-end gap-1">
                            @if($file->is_public)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Berbagi Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    Privat
                                </span>
                            @endif

                            @if($file->upload_link_id)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800" title="Diterima dari link drop: {{ $file->uploadLink?->title }}">
                                    📥 Dari: {{ Str::limit($file->uploader_name ?? 'Drop Link', 14) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($file->notes))
                        <div class="text-[11px] text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800/80 italic">
                            "{{ $file->notes }}"
                        </div>
                    @endif

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/80 gap-2">
                        <!-- Left: View (Preview) & Download Buttons -->
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    onclick="openPreviewModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ addslashes($file->original_name) }}', '{{ $file->formatted_size }}', '{{ strtolower($file->extension) }}', '{{ route('drive.preview', $file) }}', '{{ route('drive.download', $file) }}', '{{ $meta['label'] }}', '{{ addslashes($file->notes ?? '') }}', '{{ $file->created_at->format('d M Y, H:i') }}')"
                                    class="px-3 py-1.5 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-bold flex items-center gap-1.5 transition-all shadow-xs cursor-pointer"
                                    title="Lihat Berkas Tanpa Mengunduh">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Lihat</span>
                            </button>

                            <a href="{{ route('drive.download', $file) }}"
                               class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200 dark:border-slate-800 flex items-center gap-1 active:scale-95 transition-all"
                               title="Unduh ke Perangkat">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <span>Unduh</span>
                            </a>
                        </div>

                        <!-- Right Group: Share Transfer & Delete -->
                        <div class="flex items-center gap-1.5">
                            <!-- Transfer / Share Trigger Button -->
                            <button type="button"
                                    onclick="openShareModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ $file->formatted_size }}', '{{ $file->share_url }}', {{ $file->is_public ? 'true' : 'false' }}, '{{ route('drive.share.toggle', $file) }}')"
                                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-800 flex items-center gap-1 active:scale-95 transition-all cursor-pointer"
                                    title="Bagi / Transfer">
                                <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                                </svg>
                                <span>Transfer</span>
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('drive.destroy', $file) }}" method="POST" onsubmit="return confirm('Hapus file {{ addslashes($file->title) }} dari SwanDrive?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 active:scale-90 transition-all cursor-pointer" title="Hapus Berkas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 bg-white dark:bg-slate-950 rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Berkas Tersimpan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                            Unggah berkas untuk disimpan privat atau bagikan tautan upload agar orang lain bisa mengirim berkas ke drive Anda.
                        </p>
                    </div>
                    <button type="button" onclick="document.getElementById('upload-input').click()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-bold shadow-md shadow-teal-500/20 transition-all">
                        + Unggah Berkas Pertama
                    </button>
                </div>
            @endforelse
        </div>
    @else
        <!-- TAB 2: LINK TERIMA FILE (DROP LINKS) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between bg-white dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="min-w-0 pr-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Link Terima Berkas ({{ $uploadLinks->count() }})
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Tautan publik agar orang lain bisa mengunggah berkas ke drive Anda.
                    </p>
                </div>
                <button type="button" onclick="openCreateDropModal()" class="px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-white text-xs font-extrabold shadow-sm active:scale-95 transition-all shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>+ Buat Link</span>
                </button>
            </div>

            @forelse($uploadLinks as $link)
                @php
                    $status = $link->statusMeta();
                @endphp
                <div class="bg-white dark:bg-slate-950 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs space-y-3 hover:border-teal-500/50 transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white truncate">
                                {{ $link->title }}
                            </h3>
                            @if(!empty($link->description))
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">
                                    {{ $link->description }}
                                </p>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $status['bg'] }} {{ $status['text'] }} border {{ $status['border'] }} shrink-0">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <!-- Parameter Badges Grid -->
                    <div class="grid grid-cols-3 gap-2 bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/80 text-center text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Batas Ukuran</span>
                            <span class="font-bold text-teal-600 dark:text-teal-400">{{ $link->max_file_size_mb }} MB</span>
                        </div>
                        <div class="border-x border-slate-200 dark:border-slate-800">
                            <span class="text-[10px] text-slate-400 block">Kuota Berkas</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $link->uploaded_files_count }} / {{ $link->max_files }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Batas Waktu</span>
                            @if($link->expires_at)
                                <span class="font-bold {{ $link->isExpired() ? 'text-rose-500' : 'text-amber-500' }}" title="{{ $link->expires_at->format('d M Y H:i') }}">
                                    {{ $link->isExpired() ? 'Habis' : $link->expires_at->diffForHumans(['parts' => 1]) }}
                                </span>
                            @else
                                <span class="font-bold text-slate-500">Selamanya</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
                        <!-- URL Input Display + Copy Button -->
                        <div class="flex items-center gap-1.5">
                            <div class="relative flex-1 min-w-0">
                                <input type="text" readonly value="{{ $link->public_url }}"
                                       onclick="this.select(); copyToClipboard('{{ $link->public_url }}', this)"
                                       class="w-full pl-7 pr-3 py-2 text-[11px] font-mono rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 truncate cursor-pointer focus:outline-none focus:ring-1 focus:ring-teal-500"
                                       title="Klik untuk memilih tautan">
                                <span class="absolute left-2.5 top-2.5 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                    </svg>
                                </span>
                            </div>
                            <button type="button"
                                    onclick="copyToClipboard('{{ $link->public_url }}', this)"
                                    class="px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-xs active:scale-95 transition-all cursor-pointer"
                                    title="Salin tautan ke papan klip">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                </svg>
                                <span>Salin</span>
                            </button>
                        </div>

                        <!-- Secondary Row: WhatsApp, Buka Preview, Status Toggle, Hapus -->
                        <div class="flex items-center justify-between gap-2 pt-0.5">
                            <div class="flex items-center gap-1.5">
                                @php
                                    $waMsg = urlencode("Halo, silakan unggah berkas '{$link->title}' melalui tautan aman ini (Maksimal {$link->max_file_size_mb} MB):\n{$link->public_url}");
                                @endphp
                                <a href="https://api.whatsapp.com/send?text={{ $waMsg }}" target="_blank"
                                   class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold flex items-center gap-1.5 active:scale-95 transition-all cursor-pointer"
                                   title="Bagikan via WhatsApp">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.768 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>

                                <a href="{{ $link->public_url }}" target="_blank"
                                   class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold flex items-center gap-1 active:scale-95 transition-all cursor-pointer"
                                   title="Lihat Tampilan Pengunggah">
                                    <span>Buka</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <form action="{{ route('drive.drop-links.toggle', $link) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl text-xs font-bold border active:scale-95 transition-all cursor-pointer {{ $link->is_active ? 'bg-slate-100 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200' : 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400' }}">
                                        {{ $link->is_active ? 'Tutup Link' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form action="{{ route('drive.drop-links.destroy', $link) }}" method="POST" onsubmit="return confirm('Hapus tautan pengumpulan berkas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 active:scale-90 transition-all cursor-pointer" title="Hapus Link Drop">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 bg-white dark:bg-slate-950 rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Tautan Terima Berkas</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                            Buat link khusus dengan batas waktu dan ukuran berkas agar orang lain bisa mengunggah berkas langsung ke drive Anda.
                        </p>
                    </div>
                    <button type="button" onclick="openCreateDropModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-extrabold shadow-md shadow-teal-500/20 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>+ Buat Tautan Terima Berkas</span>
                    </button>
                </div>
            @endforelse
        </div>
    @endif
</div>

<!-- MODAL 1: SHARE & TRANSFER FILE (Existing) -->
<div id="share-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="share-backdrop" onclick="closeShareModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div id="share-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer" onclick="closeShareModal()"></div>
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>Transfer & Bagikan Berkas</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400" id="modal-file-info">Informasi berkas...</p>
                </div>
                <button type="button" onclick="closeShareModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                    ✕
                </button>
            </div>

            <form id="toggle-share-form" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="bg-slate-50 dark:bg-slate-950 rounded-2xl p-3 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Tautan Transfer Publik</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400" id="share-status-desc">
                            Siapa saja dengan tautan dapat mengunduh berkas ini.
                        </span>
                    </div>
                    <button type="submit" id="toggle-btn" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all">
                        Ubah Status
                    </button>
                </div>
            </form>

            <div id="active-share-section" class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Tautan Unduhan Langsung</label>
                    <div class="flex items-center gap-1.5">
                        <input type="text" id="share-url-input" readonly class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-mono select-all">
                        <button type="button" onclick="copyToClipboard(document.getElementById('share-url-input').value, this)" class="px-3 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold active:scale-95 transition-all shrink-0 flex items-center gap-1 shadow-sm">
                            <span>Salin</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a id="btn-wa-share" href="#" target="_blank" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.766 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                    <a id="btn-preview-share" href="#" target="_blank" class="py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                        <span>Buka Link ↗</span>
                    </a>
                </div>
            </div>

            <div id="inactive-share-notice" class="hidden p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300">
                <span class="font-bold block mb-0.5">🔒 File bersifat privat</span>
                Aktifkan tombol "Ubah Status" di atas agar link transfer dapat diakses oleh penerima tanpa login.
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2: BUAT LINK TERIMA FILE (DROP LINK SETTINGS) -->
<div id="create-drop-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="drop-backdrop" onclick="closeCreateDropModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div id="drop-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer" onclick="closeCreateDropModal()"></div>
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pengaturan Link Terima File</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Atur batasan sebelum membagikan link ke orang lain</p>
                </div>
                <button type="button" onclick="closeCreateDropModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                    ✕
                </button>
            </div>

            <form action="{{ route('drive.drop-links.store') }}" method="POST" class="space-y-3.5">
                @csrf

                <!-- Judul Permintaan -->
                <div>
                    <label for="drop-title" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Judul / Tujuan Permintaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="drop-title" name="title" required placeholder="Misal: Pengumpulan Kuitansi Pembelian, Foto Proyek"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Deskripsi / Pesan -->
                <div>
                    <label for="drop-desc" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Pesan / Instruksi untuk Pengunggah (Opsional)
                    </label>
                    <textarea id="drop-desc" name="description" rows="2" placeholder="Misal: Mohon unggah foto nota asli dengan resolusi jelas..."
                              class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <!-- Batas Waktu Link (Expiry) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        ⏱️ Batas Waktu Tautan (Kedaluwarsa)
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="expiry_preset" value="1h" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                1 Jam
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="expiry_preset" value="24h" checked class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                24 Jam
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="expiry_preset" value="3d" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                3 Hari
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="expiry_preset" value="7d" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                7 Hari
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="expiry_preset" value="30d" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                30 Hari
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="expiry_preset" value="never" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                Selamanya
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Batas Jumlah File -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        📦 Batas Jumlah Berkas Maksimal
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="max_files" value="1" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                1 File
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="max_files" value="3" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                3 File
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="max_files" value="5" checked class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                5 File
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="max_files" value="10" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                10 File
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Batas Ukuran per Berkas (Max MB) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        📏 Batas Ukuran per Berkas
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="max_file_size_mb" value="5" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                5 MB
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="max_file_size_mb" value="10" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                10 MB
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="max_file_size_mb" value="25" checked class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                25 MB
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="max_file_size_mb" value="50" class="peer hidden">
                            <div class="p-2 rounded-xl text-center text-xs font-bold border border-slate-200 dark:border-slate-800 peer-checked:bg-teal-500 peer-checked:text-white peer-checked:border-teal-500 peer-checked:shadow-xs transition-all">
                                50 MB
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-[0.98] text-white text-xs font-extrabold shadow-md shadow-teal-500/20 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                        <span>Buat & Dapatkan Tautan</span>
                    </button>
                    <button type="button" onclick="closeCreateDropModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: UBAH KAPASITAS RUANG PENYIMPANAN -->
<div id="quota-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <!-- Backdrop -->
    <div id="quota-backdrop" onclick="closeQuotaModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>

    <!-- Panel Bottom-Sheet (Matches SwanFlow Mobile Sheet) -->
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div id="quota-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar text-slate-800 dark:text-white">
            <!-- Drag Handle -->
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer" onclick="closeQuotaModal()"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125M16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Ubah Batas Kapasitas</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Atur kuota ruang penyimpanan SwanDrive</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuotaModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                    ✕
                </button>
            </div>

            <!-- Form Update Quota -->
            <form action="{{ route('drive.quota.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <!-- Quick Presets Grid -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Kapasitas Cepat:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="setQuotaValue(250, event)" class="quota-preset-btn py-2 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 250 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            250 MB
                        </button>
                        <button type="button" onclick="setQuotaValue(500, event)" class="quota-preset-btn py-2 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 500 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            500 MB
                        </button>
                        <button type="button" onclick="setQuotaValue(1024, event)" class="quota-preset-btn py-2 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 1024 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            1 GB
                        </button>
                        <button type="button" onclick="setQuotaValue(2048, event)" class="quota-preset-btn py-2 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 2048 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            2 GB
                        </button>
                        <button type="button" onclick="setQuotaValue(5120, event)" class="quota-preset-btn py-2 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 5120 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            5 GB
                        </button>
                        <button type="button" onclick="setQuotaValue(10240, event)" class="quota-preset-btn py-2 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 10240 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            10 GB
                        </button>
                    </div>
                </div>

                <!-- Custom Input -->
                <div class="space-y-1.5">
                    <label for="quota-input-mb" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Atau Masukkan Kapasitas Khusus (MB):
                    </label>
                    <div class="relative">
                        <input type="number" id="quota-input-mb" name="storage_quota_mb" value="{{ $quotaMb }}" min="10" max="1048576" required
                               oninput="updateQuotaPreview(this.value)"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white font-extrabold text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <span class="absolute right-3.5 top-3 text-xs font-bold text-slate-400">MB</span>
                    </div>
                    <p class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold" id="quota-preview-text">
                        Setara dengan {{ $formattedQuotaSize }}
                    </p>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-[0.98] text-white text-xs font-extrabold shadow-md shadow-teal-500/20 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan Kapasitas Baru</span>
                    </button>
                    <button type="button" onclick="closeQuotaModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 4: LIHAT BERKAS (PREVIEW FILE TANPA DOWNLOAD) -->
<div id="preview-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <!-- Backdrop -->
    <div id="preview-backdrop" onclick="closePreviewModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Panel Bottom-Sheet (High-fidelity SwanFlow Mobile Sheet) -->
    <div class="fixed bottom-0 left-0 right-0 flex justify-center pointer-events-none">
        <div id="preview-panel" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-4 sm:p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 flex flex-col max-h-[92vh] text-slate-800 dark:text-white">
            <!-- Drag Handle -->
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer shrink-0" onclick="closePreviewModal()"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 gap-3">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div id="pv-ext-badge" class="w-10 h-10 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center font-black text-xs uppercase shrink-0 shadow-2xs">
                        TXT
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 id="pv-title" class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate leading-tight">
                            Nama Berkas
                        </h2>
                        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5 font-medium">
                            <span id="pv-size">0 KB</span>
                            <span>•</span>
                            <span id="pv-ext-label">Dokumen</span>
                            <span>•</span>
                            <span id="pv-date">Tanggal</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <a id="pv-open-tab" href="#" target="_blank" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all" title="Buka di Tab Baru">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                    <button type="button" onclick="closePreviewModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Optional File Note Box -->
            <div id="pv-notes-box" class="hidden my-2.5 p-2.5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60 text-xs text-teal-800 dark:text-teal-300 italic shrink-0">
                <span class="font-bold not-italic mr-1">Catatan:</span>
                <span id="pv-notes-text"></span>
            </div>

            <!-- Preview Content Area (Flexible scrolling body) -->
            <div id="pv-content-container" class="flex-1 my-3 overflow-y-auto overflow-x-hidden min-h-[220px] max-h-[58vh] rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-center relative p-2">
                <!-- Content will be injected dynamically: Image, PDF viewer, Audio, Video, Text/Code viewer, or fallback card -->
                <div id="pv-loading" class="flex flex-col items-center justify-center gap-2 p-6 text-slate-400">
                    <svg class="w-8 h-8 animate-spin text-teal-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="text-xs font-semibold">Memuat Pratinjau Berkas...</span>
                </div>
            </div>

            <!-- Footer Action Bar -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 shrink-0">
                <button type="button" onclick="closePreviewModal()" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                    Tutup
                </button>
                <div class="flex items-center gap-2">
                    <a id="pv-download-btn" href="#" class="py-2.5 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-extrabold shadow-md shadow-teal-500/25 flex items-center gap-1.5 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Berkas</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle File Selection
    function handleFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        const ext = file.name.split('.').pop().toUpperCase();
        document.getElementById('selected-ext').innerText = ext.substring(0, 5);
        document.getElementById('selected-filename').innerText = file.name;
        
        let sizeText = file.size + ' B';
        if (file.size >= 1048576) {
            sizeText = (file.size / 1048576).toFixed(1) + ' MB';
        } else if (file.size >= 1024) {
            sizeText = (file.size / 1024).toFixed(0) + ' KB';
        }
        document.getElementById('selected-size').innerText = sizeText;

        const titleInput = document.getElementById('file-title');
        if (!titleInput.value) {
            titleInput.value = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
        }

        document.getElementById('upload-details').classList.remove('hidden');
        document.getElementById('dropzone').classList.add('border-teal-500', 'bg-teal-50/90');
    }

    function cancelUpload() {
        document.getElementById('upload-input').value = '';
        document.getElementById('upload-details').classList.add('hidden');
        const progressContainer = document.getElementById('upload-progress-container');
        if (progressContainer) progressContainer.classList.add('hidden');
        document.getElementById('file-title').value = '';
        document.getElementById('file-notes').value = '';
        document.getElementById('dropzone').classList.remove('border-teal-500', 'bg-teal-50/90');
    }

    function formatBytes(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
        return bytes + ' B';
    }

    // Interactive Upload with Progress Bar & Loading Indicator
    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('upload-input');
            if (!fileInput.files || !fileInput.files[0]) {
                return;
            }

            const btnSubmit = document.getElementById('btn-submit-upload');
            const btnCancel = document.getElementById('btn-cancel-upload');
            const btnIcon = document.getElementById('btn-upload-icon');
            const btnSpinner = document.getElementById('btn-upload-spinner');
            const btnText = document.getElementById('btn-upload-text');
            const progressContainer = document.getElementById('upload-progress-container');
            const progressBar = document.getElementById('upload-progress-bar');
            const progressPercent = document.getElementById('upload-progress-percent');
            const progressBytes = document.getElementById('upload-progress-bytes');
            const statusText = document.getElementById('upload-status-text');

            // Disable buttons and show spinner
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-80', 'cursor-not-allowed');
            if (btnCancel) {
                btnCancel.disabled = true;
                btnCancel.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (btnIcon) btnIcon.classList.add('hidden');
            if (btnSpinner) btnSpinner.classList.remove('hidden');
            if (btnText) btnText.innerText = 'Mengunggah...';

            // Show progress bar
            if (progressContainer) {
                progressContainer.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressPercent.innerText = '0%';
                statusText.innerText = 'Mengunggah berkas...';
                progressBytes.innerText = `0 KB / ${formatBytes(fileInput.files[0].size)}`;
            }

            const formData = new FormData(uploadForm);
            const xhr = new XMLHttpRequest();

            xhr.open('POST', uploadForm.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }

            xhr.upload.addEventListener('progress', function(event) {
                if (event.lengthComputable) {
                    const percent = Math.min(99, Math.round((event.loaded / event.total) * 100));
                    if (progressBar) progressBar.style.width = percent + '%';
                    if (progressPercent) progressPercent.innerText = percent + '%';
                    if (progressBytes) progressBytes.innerText = `${formatBytes(event.loaded)} / ${formatBytes(event.total)}`;

                    if (percent >= 98 && statusText) {
                        statusText.innerText = 'Menyimpan & memproses berkas...';
                    }
                }
            });

            xhr.addEventListener('load', function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    if (progressBar) progressBar.style.width = '100%';
                    if (progressPercent) progressPercent.innerText = '100%';
                    if (statusText) statusText.innerText = 'Unggahan berhasil! Memuat...';

                    setTimeout(() => {
                        window.location.href = "{{ route('drive.index', ['tab' => 'files']) }}";
                    }, 400);
                } else {
                    let errorMsg = 'Gagal mengunggah berkas. Silakan periksa ukuran file atau coba lagi.';
                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json.message) errorMsg = json.message;
                        if (json.errors) {
                            const firstKey = Object.keys(json.errors)[0];
                            if (json.errors[firstKey][0]) errorMsg = json.errors[firstKey][0];
                        }
                    } catch(e) {}

                    alert(errorMsg);
                    resetUploadState();
                }
            });

            xhr.addEventListener('error', function() {
                alert('Terjadi kendala jaringan saat mengunggah berkas.');
                resetUploadState();
            });

            xhr.send(formData);

            function resetUploadState() {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-80', 'cursor-not-allowed');
                if (btnCancel) {
                    btnCancel.disabled = false;
                    btnCancel.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                if (btnIcon) btnIcon.classList.remove('hidden');
                if (btnSpinner) btnSpinner.classList.add('hidden');
                if (btnText) btnText.innerText = 'Simpan ke SwanDrive';
                if (progressContainer) progressContainer.classList.add('hidden');
            }
        });
    }

    // Drag and Drop support
    const dropzone = document.getElementById('dropzone');
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-teal-500', 'bg-teal-100/60');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-teal-500', 'bg-teal-100/60');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                document.getElementById('upload-input').files = files;
                handleFileSelect(document.getElementById('upload-input'));
            }
        });
    }

    // Share Modal
    function openShareModal(id, title, size, url, isPublic, toggleAction) {
        document.getElementById('modal-file-info').innerText = `${title} • ${size}`;
        document.getElementById('share-url-input').value = url;
        document.getElementById('toggle-share-form').action = toggleAction;

        const toggleBtn = document.getElementById('toggle-btn');
        const activeSection = document.getElementById('active-share-section');
        const inactiveNotice = document.getElementById('inactive-share-notice');
        const statusDesc = document.getElementById('share-status-desc');

        if (isPublic) {
            toggleBtn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-200';
            toggleBtn.innerText = 'Nonaktifkan';
            statusDesc.innerText = 'Tautan publik sedang aktif. Siapa saja dengan link dapat mengunduh.';
            activeSection.classList.remove('hidden');
            inactiveNotice.classList.add('hidden');

            const waText = encodeURIComponent(`Halo, berikut tautan berkas "${title}" (${size}):\n${url}`);
            document.getElementById('btn-wa-share').href = `https://api.whatsapp.com/send?text=${waText}`;
            document.getElementById('btn-preview-share').href = url;
        } else {
            toggleBtn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500 text-white shadow-xs hover:bg-emerald-600';
            toggleBtn.innerText = 'Aktifkan';
            statusDesc.innerText = 'Tautan transfer sedang privat dan dinonaktifkan.';
            activeSection.classList.add('hidden');
            inactiveNotice.classList.remove('hidden');
        }

        const modal = document.getElementById('share-modal');
        const backdrop = document.getElementById('share-backdrop');
        const panel = document.getElementById('share-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function closeShareModal() {
        const backdrop = document.getElementById('share-backdrop');
        const panel = document.getElementById('share-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('share-modal').classList.add('hidden');
        }, 300);
    }

    // Modal Create Drop Link
    function openCreateDropModal() {
        const modal = document.getElementById('create-drop-modal');
        const backdrop = document.getElementById('drop-backdrop');
        const panel = document.getElementById('drop-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function closeCreateDropModal() {
        const backdrop = document.getElementById('drop-backdrop');
        const panel = document.getElementById('drop-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('create-drop-modal').classList.add('hidden');
        }, 300);
    }

    // Quota Modal Handlers
    function openQuotaModal() {
        const modal = document.getElementById('quota-modal');
        const backdrop = document.getElementById('quota-backdrop');
        const panel = document.getElementById('quota-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function closeQuotaModal() {
        const backdrop = document.getElementById('quota-backdrop');
        const panel = document.getElementById('quota-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('quota-modal').classList.add('hidden');
        }, 300);
    }

    function setQuotaValue(mb, e) {
        const input = document.getElementById('quota-input-mb');
        if (input) {
            input.value = mb;
            updateQuotaPreview(mb);
        }

        // Update active styling on preset buttons
        document.querySelectorAll('.quota-preset-btn').forEach(btn => {
            btn.classList.remove('bg-teal-500', 'text-white', 'border-teal-500', 'shadow-xs');
            btn.classList.add('bg-slate-50', 'dark:bg-slate-800/80', 'border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-300');
        });
        const target = (e && e.currentTarget) || (typeof event !== 'undefined' && event && event.currentTarget);
        if (target) {
            target.classList.remove('bg-slate-50', 'dark:bg-slate-800/80', 'border-slate-200', 'dark:border-slate-700', 'text-slate-700', 'dark:text-slate-300');
            target.classList.add('bg-teal-500', 'text-white', 'border-teal-500', 'shadow-xs');
        }
    }

    function updateQuotaPreview(val) {
        const preview = document.getElementById('quota-preview-text');
        const num = parseFloat(val);
        if (isNaN(num) || num <= 0) {
            preview.innerText = 'Masukkan angka MB yang valid (minimal 10 MB)';
            return;
        }

        if (num >= 1048576) {
            preview.innerText = `Setara dengan ${(num / 1048576).toFixed(1)} TB`;
        } else if (num >= 1024) {
            const gb = num / 1024;
            preview.innerText = `Setara dengan ${Number.isInteger(gb) ? gb : gb.toFixed(1)} GB`;
        } else {
            preview.innerText = `Setara dengan ${num} MB`;
        }
    }

    // Generic Copy to Clipboard Helper
    function copyToClipboard(text, btnElement) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btnElement.innerHTML;
            btnElement.innerText = 'Tersalin!';
            setTimeout(() => {
                btnElement.innerHTML = originalHtml;
            }, 2000);
        }).catch(() => {
            prompt('Salin link ini:', text);
        });
    }

    // Preview Modal Handlers
    let currentPreviewAbortController = null;
    let currentLoadedPreviewText = '';

    function copyPreviewText(btn) {
        if (!currentLoadedPreviewText) return;
        copyToClipboard(currentLoadedPreviewText, btn);
    }

    function openPreviewModal(id, title, originalName, size, ext, previewUrl, downloadUrl, label, notes, date) {
        const modal = document.getElementById('preview-modal');
        const backdrop = document.getElementById('preview-backdrop');
        const panel = document.getElementById('preview-panel');

        // Populate header info
        document.getElementById('pv-title').innerText = title || originalName;
        document.getElementById('pv-size').innerText = size;
        document.getElementById('pv-ext-badge').innerText = (ext || 'FILE').toUpperCase().substring(0, 4);
        document.getElementById('pv-ext-label').innerText = label || (ext ? ext.toUpperCase() : 'BERKAS');
        document.getElementById('pv-date').innerText = date || '';
        document.getElementById('pv-open-tab').href = previewUrl;
        document.getElementById('pv-download-btn').href = downloadUrl;

        // Notes
        const notesBox = document.getElementById('pv-notes-box');
        const notesText = document.getElementById('pv-notes-text');
        if (notes && notes.trim().length > 0) {
            notesText.innerText = notes;
            notesBox.classList.remove('hidden');
        } else {
            notesBox.classList.add('hidden');
        }

        // Reset container and show loader
        const container = document.getElementById('pv-content-container');
        container.innerHTML = `
            <div id="pv-loading" class="flex flex-col items-center justify-center gap-2 p-6 text-slate-400">
                <svg class="w-8 h-8 animate-spin text-teal-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Pratinjau Berkas...</span>
            </div>
        `;

        // Open bottom-sheet animation
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });

        // Cancel any previous fetch
        if (currentPreviewAbortController) {
            currentPreviewAbortController.abort();
        }
        currentPreviewAbortController = new AbortController();

        const cleanExt = (ext || '').toLowerCase().trim();
        const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'avif'];
        const audioExts = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'];
        const videoExts = ['mp4', 'webm', 'ogg', 'mov'];
        const textExts = ['txt', 'csv', 'json', 'md', 'html', 'js', 'css', 'xml', 'log', 'php', 'py', 'sql', 'sh', 'env', 'yaml', 'yml'];

        if (imageExts.includes(cleanExt)) {
            // IMAGE PREVIEW
            const img = new Image();
            img.src = previewUrl;
            img.alt = title || originalName;
            img.className = 'max-h-[55vh] max-w-full object-contain rounded-xl shadow-xs transition-opacity duration-300 opacity-0';
            img.onload = () => {
                container.innerHTML = '';
                container.appendChild(img);
                requestAnimationFrame(() => img.classList.remove('opacity-0'));
            };
            img.onerror = () => {
                renderFallbackPreview(container, ext, originalName, downloadUrl, 'Gagal memuat gambar secara langsung');
            };
        } else if (cleanExt === 'pdf') {
            // PDF PREVIEW
            container.innerHTML = `
                <div class="w-full h-[55vh] flex flex-col">
                    <iframe src="${previewUrl}#toolbar=0" class="w-full h-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white" title="PDF Preview"></iframe>
                </div>
            `;
        } else if (audioExts.includes(cleanExt)) {
            // AUDIO PREVIEW
            container.innerHTML = `
                <div class="w-full p-4 flex flex-col items-center justify-center gap-4 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a.75.75 0 00.532-.72v-4.52m-9.5 4.5v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66A.75.75 0 008.25 15V9" />
                        </svg>
                    </div>
                    <div class="w-full max-w-sm">
                        <audio controls class="w-full rounded-xl focus:outline-none" src="${previewUrl}">
                            Browser Anda tidak mendukung audio player.
                        </audio>
                    </div>
                </div>
            `;
        } else if (videoExts.includes(cleanExt)) {
            // VIDEO PREVIEW
            container.innerHTML = `
                <div class="w-full flex items-center justify-center bg-black/90 rounded-xl overflow-hidden shadow-inner">
                    <video controls playsinline class="max-h-[55vh] w-full object-contain focus:outline-none" src="${previewUrl}">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                </div>
            `;
        } else if (textExts.includes(cleanExt)) {
            // TEXT / CODE PREVIEW (Async fetch inline)
            fetch(previewUrl, { signal: currentPreviewAbortController.signal })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal memuat teks');
                    return res.text();
                })
                .then(text => {
                    currentLoadedPreviewText = text;
                    const escaped = escapeHtml(text);
                    const lineCount = text.split('\n').length;
                    container.innerHTML = `
                        <div class="w-full h-full flex flex-col text-left">
                            <div class="flex items-center justify-between px-3 py-1.5 bg-slate-200/70 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 rounded-t-xl text-[10px] font-mono text-slate-600 dark:text-slate-400">
                                <span>.${cleanExt} (${lineCount} baris)</span>
                                <button type="button" onclick="copyPreviewText(this)" class="px-2 py-0.5 rounded bg-teal-500 hover:bg-teal-600 text-white font-sans font-bold active:scale-95 transition-all cursor-pointer">Salin Teks</button>
                            </div>
                            <pre class="flex-1 p-3 font-mono text-xs text-slate-800 dark:text-slate-200 overflow-auto whitespace-pre leading-relaxed select-text select-all bg-white dark:bg-slate-950 rounded-b-xl border border-slate-200 dark:border-slate-800">${escaped}</pre>
                        </div>
                    `;
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    renderFallbackPreview(container, ext, originalName, downloadUrl, 'Format berkas teks terlalu besar atau gagal dibaca.');
                });
        } else {
            // FALLBACK FOR OTHER FILE TYPES (ZIP, DOCX, XLSX, APK, etc.)
            renderFallbackPreview(container, ext, originalName, downloadUrl);
        }
    }

    function renderFallbackPreview(container, ext, originalName, downloadUrl, customMsg) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center p-6 text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center font-black text-lg uppercase border border-teal-500/20 shadow-xs">
                    ${(ext || 'FILE').toUpperCase().substring(0, 4)}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Pratinjau Langsung Tidak Tersedia</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                        ${customMsg || 'Format berkas ini tidak dapat dipratinjau langsung di browser, silakan unduh untuk membukanya.'}
                    </p>
                </div>
                <a href="${downloadUrl}" class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-bold flex items-center gap-1.5 transition-all shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Berkas Ini</span>
                </a>
            </div>
        `;
    }

    function escapeHtml(str) {
        if (typeof str !== 'string') return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function closePreviewModal() {
        if (currentPreviewAbortController) {
            currentPreviewAbortController.abort();
        }
        const modal = document.getElementById('preview-modal');
        const backdrop = document.getElementById('preview-backdrop');
        const panel = document.getElementById('preview-panel');
        const container = document.getElementById('pv-content-container');

        // Pause any active audio/video immediately to prevent background playback
        if (container) {
            const audio = container.querySelector('audio');
            if (audio) audio.pause();
            const video = container.querySelector('video');
            if (video) video.pause();
        }

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            modal.classList.add('hidden');
            if (container) container.innerHTML = '';
            currentLoadedPreviewText = '';
        }, 300);
    }
</script>
@endsection
