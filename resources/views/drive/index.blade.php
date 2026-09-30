@extends('layouts.mobile')

@section('title', 'SwanDrive - Penyimpanan & Transfer File')

@section('custom_header')
    <!-- Top Header: Apple iOS Liquid Glass SwanDrive Header -->
    <div class="relative overflow-hidden bg-gradient-to-b from-teal-600/95 via-teal-600/85 to-emerald-700/90 dark:from-slate-900/95 dark:via-teal-950/90 dark:to-slate-950/95 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-200" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Highlight Line at the Top -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-teal-400/20 dark:bg-teal-500/15 rounded-full blur-3xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-2xl pointer-events-none animate-liquid-orb-2"></div>

        <!-- Top Navigation Bar (Spacious & iOS Status Bar Safe) -->
        <div class="relative z-10 flex items-center justify-between mb-4 gap-2">
            <!-- Left: Back Button & Brand Title -->
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h1 class="text-base font-black tracking-tight text-white leading-tight">SwanDrive</h1>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-teal-400/25 text-teal-200 border border-teal-300/30 shadow-2xs backdrop-blur-md shrink-0">Vault</span>
                    </div>
                    <p class="text-[11px] text-teal-200/80 font-semibold truncate mt-0.5">Penyimpanan Cloud</p>
                </div>
            </div>

            <!-- Right: Action Buttons (Folder, Upload & Theme Toggle) -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Buat Folder Baru -->
                <button type="button" 
                        onclick="openCreateFolderModal()" 
                        class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 text-white shadow-xs border border-white/30 flex items-center justify-center transition-all cursor-pointer ios-press" 
                        title="Buat Folder Baru" 
                        aria-label="Buat Folder Baru">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3h-6M3 7.5a2.25 2.25 0 012.25-2.25h4.125c.621 0 1.217.246 1.657.686l1.371 1.372c.44.44 1.036.686 1.657.686H18.75A2.25 2.25 0 0121 10.5v8.25A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75V7.5z" />
                    </svg>
                </button>

                <!-- Upload Button (Sleek Apple Action Icon) -->
                <button type="button" 
                        onclick="document.getElementById('upload-input').click()" 
                        class="w-10 h-10 rounded-[18px] bg-gradient-to-tr from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 active:scale-95 text-white shadow-lg shadow-emerald-500/30 border border-white/25 flex items-center justify-center transition-all cursor-pointer ios-press" 
                        title="Upload Berkas" 
                        aria-label="Upload Berkas">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                </button>

                <!-- Theme Toggle Button -->
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

        <!-- Storage Summary Card with Apple Liquid Glassmorphism -->
        <div class="relative z-10 liquid-glass rounded-[26px] p-4 border border-white/25 shadow-xl space-y-3 bg-white/10 backdrop-blur-2xl">
            <!-- Row 1: Label & Ubah Ukuran button -->
            <div class="flex items-center justify-between gap-2 min-w-0">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-teal-200/90 truncate">Kapasitas Penyimpanan</span>
                <div class="flex items-center gap-1.5 shrink-0">
                    @if(! $isGoogleConnected)
                    <a href="{{ route('drive.connect') }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white text-[10px] font-black border border-white/30 active:scale-95 transition-all cursor-pointer shadow-md ios-press backdrop-blur-md animate-pulse" title="Sambungkan Akun Google Drive">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                        </svg>
                        <span>Sambung Google</span>
                    </a>
                    @else
                    <form action="{{ route('drive.sync-google-quota') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-500/25 hover:bg-emerald-500/35 text-emerald-200 text-[10px] font-black border border-emerald-400/40 active:scale-95 transition-all cursor-pointer shadow-xs" title="Sinkronkan kapasitas dengan Google Drive">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Google {{ $formattedQuotaSize }}</span>
                            <svg class="w-2.5 h-2.5 ml-0.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                        </button>
                    </form>
                    @endif
                    <button type="button" onclick="openQuotaModal()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/15 hover:bg-white/25 text-teal-100 hover:text-white text-[10px] font-black border border-white/20 active:scale-95 transition-all cursor-pointer shadow-xs ios-press backdrop-blur-md" title="Ubah Kapasitas Kuota">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                        <span>Ubah</span>
                    </button>
                </div>
            </div>

            <!-- Row 2: Numbers & Percentage Badge -->
            <div class="flex items-baseline justify-between gap-2 min-w-0">
                <div class="flex items-baseline gap-1.5 min-w-0 truncate">
                    <span class="text-2xl sm:text-3xl font-black text-white tracking-tight truncate">{{ $formattedTotalSize }}</span>
                    <span class="text-xs font-bold text-teal-200/80 shrink-0">/ {{ $formattedQuotaSize }}</span>
                </div>
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <span class="text-[10px] text-teal-200/80 font-bold">{{ $totalFiles }} Berkas</span>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full border shadow-2xs {{ $storagePercent >= 90 ? 'bg-rose-500/30 text-rose-200 border-rose-400/40' : ($storagePercent >= 75 ? 'bg-amber-500/30 text-amber-200 border-amber-400/40' : 'bg-teal-400/30 text-teal-100 border-teal-300/40') }}">
                        {{ $storagePercent }}%
                    </span>
                </div>
            </div>

            <!-- Row 3: Visual Progress Track -->
            <div>
                <div class="w-full bg-slate-950/60 rounded-full h-2.5 overflow-hidden p-0.5 border border-white/15">
                    <div class="h-full rounded-full transition-all duration-500 {{ $storagePercent >= 90 ? 'bg-gradient-to-r from-rose-500 to-amber-500' : ($storagePercent >= 75 ? 'bg-gradient-to-r from-amber-400 to-yellow-400' : 'bg-gradient-to-r from-teal-400 via-emerald-400 to-teal-300') }}"
                         style="width: {{ max(3, $storagePercent) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-teal-200/80 mt-1 font-semibold">
                    <span>{{ $totalBytes >= $quotaBytes ? 'Kapasitas Penuh' : 'Tersisa ' . $formattedRemainingSize }}</span>
                    <span>Batas: {{ $formattedQuotaSize }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(12rem,calc(11rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Pull handle indicator for authentic iOS sheet aesthetic -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    @if(! $isGoogleConnected)
    <!-- Google Drive Connection Card -->
    <div class="relative overflow-hidden rounded-[26px] p-4 bg-gradient-to-br from-amber-500/15 via-orange-500/10 to-amber-500/5 dark:from-amber-950/40 dark:via-orange-950/20 dark:to-slate-900/40 border border-amber-500/30 dark:border-amber-400/30 shadow-lg backdrop-blur-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-[18px] bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-orange-500/25 border border-white/30">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white leading-tight">Sambungkan Google Drive</h4>
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-500 text-white shadow-xs">Kapasitas Fleksibel</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                        Sambungkan akun Google Anda agar kapasitas SwanDrive otomatis menyesuaikan ukuran Google Drive Anda (15 GB / 2 TB / 5 TB).
                    </p>
                </div>
            </div>
            <a href="{{ route('drive.connect') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-[18px] bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white text-xs font-black shadow-lg shadow-orange-500/25 border border-white/20 active:scale-95 transition-all cursor-pointer shrink-0 ios-press">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                </svg>
                <span>Sambung Sekarang</span>
            </a>
        </div>
    </div>
    @else
    <!-- Google Drive Connected Badge -->
    <div class="flex items-center justify-between px-4 py-2.5 rounded-[20px] bg-emerald-500/10 dark:bg-emerald-950/30 border border-emerald-500/25 shadow-xs text-xs">
        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 font-bold">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span>Google Drive Terhubung (Kapasitas: {{ $formattedQuotaSize }})</span>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('drive.sync-google-quota') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-[11px] font-extrabold text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300 cursor-pointer" title="Perbarui kapasitas sesuai kuota Google Drive">
                    Sinkronkan Kuota
                </button>
            </form>
            <span class="text-slate-300 dark:text-slate-600">|</span>
            <a href="{{ route('drive.connect') }}" class="text-[11px] font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-300 underline" title="Hubungkan ulang akun jika perlu">
                Hubungkan Ulang
            </a>
        </div>
    </div>
    @endif

    <!-- TAB SWITCHER: Berkas Saya vs Link Terima File (iOS Segmented Control) -->
    <div class="ios-segmented-track p-1 rounded-[22px] flex items-center gap-1 w-full bg-slate-200/60 dark:bg-white/5 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-inner">
        <a href="{{ route('drive.index', ['tab' => 'files']) }}"
           class="flex-1 py-2 rounded-[18px] text-xs font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $activeTab !== 'drops' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 shadow-md ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
            </svg>
            <span>Berkas Saya</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-black {{ $activeTab !== 'drops' ? 'bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $totalFiles }}</span>
        </a>
        <a href="{{ route('drive.index', ['tab' => 'drops']) }}"
           class="flex-1 py-2 rounded-[18px] text-xs font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $activeTab === 'drops' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 shadow-md ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
            </svg>
            <span>Link Terima File</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-black {{ $activeTab === 'drops' ? 'bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $uploadLinks->count() }}</span>
        </a>
    </div>

    @if($activeTab !== 'drops')
        <!-- TAB 1: BERKAS SAYA -->

        <!-- 1. FORM UPLOAD FILE (Interactive Dropzone) -->
        <div class="liquid-card rounded-[26px] bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm backdrop-blur-2xl p-4">
            <form id="upload-form" action="{{ route('drive.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3" novalidate onsubmit="event.preventDefault(); startQueueUpload();">
                @csrf
                
                <!-- Hidden File Input (Supports multiple files) -->
                <input type="file" id="upload-input" name="files[]" multiple class="hidden" onchange="handleFileSelect(this)">

                <!-- Dropzone Box -->
                <div id="dropzone" onclick="document.getElementById('upload-input').click()"
                     class="border-2 border-dashed border-teal-400/50 dark:border-teal-500/40 rounded-[20px] p-4 sm:p-5 flex flex-col items-center justify-center text-center cursor-pointer bg-teal-50/40 dark:bg-teal-500/10 hover:bg-teal-50/80 dark:hover:bg-teal-500/20 active:scale-[0.99] transition-all ios-press">
                    <div class="w-10 h-10 rounded-[16px] bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                    </div>
                    <span id="dropzone-text" class="text-xs font-black text-slate-800 dark:text-slate-100">
                        Sentuh untuk upload berkas atau seret ke sini
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-0.5">
                        PDF, Dokumen, Excel, Gambar, ZIP (Bisa pilih banyak berkas sekaligus)
                    </span>
                </div>

                <!-- Upload Details Panel (Appears once file is chosen) -->
                <div id="upload-details" class="hidden space-y-3 pt-1">
                    <!-- Selected Files Queue Card -->
                    <div class="rounded-[20px] bg-slate-100/90 dark:bg-slate-800/90 p-3 border border-white/60 dark:border-white/10 space-y-2.5 backdrop-blur-xl shadow-xs">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5 font-black text-slate-800 dark:text-slate-100">
                                <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span id="queue-summary-text">0 Berkas Dipilih</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="queue-total-size" class="text-[11px] font-extrabold text-teal-600 dark:text-teal-400 px-2 py-0.5 rounded-full bg-teal-500/10 border border-teal-500/20">0 B</span>
                                <button type="button" onclick="document.getElementById('upload-input').click()" class="text-[10px] font-bold text-teal-600 hover:text-teal-700 dark:text-teal-400 hover:underline cursor-pointer flex items-center gap-0.5">
                                    <span>+ Tambah</span>
                                </button>
                            </div>
                        </div>

                        <!-- Scrollable Queue Items List -->
                        <div id="queue-items-list" class="space-y-1 max-h-48 overflow-y-auto pr-1 divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Options Grid (Title if 1 file, Target Folder, Notes) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div id="single-title-container">
                            <label for="file-title" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Judul / Label</label>
                            <input type="text" id="file-title" name="title" placeholder="Nama berkas..." class="w-full px-3.5 py-2.5 text-xs rounded-[16px] bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                        </div>
                        <div>
                            <label for="upload-target-folder" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Simpan di Folder</label>
                            <select id="upload-target-folder" name="folder_id" class="w-full px-3.5 py-2.5 text-xs rounded-[16px] bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                                <option value="">📁 Root (Tanpa Folder)</option>
                                @foreach($allUserFolders as $uf)
                                    <option value="{{ $uf->id }}" {{ ($currentFolder && $currentFolder->id === $uf->id) ? 'selected' : '' }}>
                                        📁 {{ $uf->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="file-notes" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" id="file-notes" name="notes" placeholder="Catatan untuk berkas yang diunggah..." class="w-full px-3.5 py-2.5 text-xs rounded-[16px] bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-1" id="upload-action-buttons">
                        <button type="button" id="btn-submit-upload" onclick="startQueueUpload()" class="flex-1 py-2.5 px-4 rounded-[18px] bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white text-xs font-black shadow-md shadow-teal-500/25 flex items-center justify-center gap-2 transition-all cursor-pointer ios-press">
                            <svg id="btn-upload-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <svg id="btn-upload-spinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="btn-upload-text">Simpan ke SwanDrive</span>
                        </button>
                        <button type="button" id="btn-cancel-upload" onclick="cancelUpload()" class="py-2.5 px-3.5 rounded-[18px] bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all ios-press cursor-pointer">
                            Batal
                        </button>
                    </div>

                    <!-- Progress Bar & Loading Status Container (Apple Liquid Glass) -->
                    <div id="upload-progress-container" class="hidden pt-2 space-y-2.5 rounded-[22px] bg-teal-50/70 dark:bg-teal-950/40 p-3.5 border border-teal-200/80 dark:border-teal-800/60 backdrop-blur-xl">
                        <!-- Top status line -->
                        <div class="flex items-center justify-between text-xs">
                            <span id="upload-progress-status" class="font-black text-teal-700 dark:text-teal-300 flex items-center gap-1.5 min-w-0 pr-2">
                                <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="upload-status-text" class="truncate">Menyiapkan pengunggahan...</span>
                            </span>
                            <span id="upload-progress-percent" class="font-black text-teal-700 dark:text-teal-300 shrink-0">0%</span>
                        </div>

                        <!-- Overall Progress Track -->
                        <div class="w-full bg-slate-200/80 dark:bg-slate-900/90 rounded-full h-3 overflow-hidden p-0.5 border border-teal-300/40 dark:border-teal-700/40 shadow-inner">
                            <div id="upload-progress-bar" class="bg-gradient-to-r from-teal-500 via-emerald-400 to-teal-500 h-full rounded-full transition-all duration-200 ease-out shadow-xs" style="width: 0%"></div>
                        </div>

                        <!-- Sub-info: Current File Status & Chunk Indicator -->
                        <div class="space-y-1 pt-0.5">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                <span id="current-file-name-label" class="truncate pr-2">Berkas saat ini...</span>
                                <span id="current-file-chunk-label" class="shrink-0 text-teal-600 dark:text-teal-400 font-extrabold text-[10px]">0%</span>
                            </div>
                            <!-- Sub Progress Track for individual file -->
                            <div class="w-full bg-slate-200/60 dark:bg-slate-800/60 rounded-full h-1.5 overflow-hidden">
                                <div id="current-file-progress-bar" class="bg-teal-400 dark:bg-teal-300 h-full rounded-full transition-all duration-150" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 font-semibold pt-1 border-t border-teal-200/50 dark:border-teal-800/40">
                            <span id="upload-progress-bytes">0 KB / 0 KB</span>
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Chunked Streaming Anti-Putus</span>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick Drop Link Banner -->
        <div class="flex items-center justify-between p-3.5 rounded-[24px] liquid-card bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 backdrop-blur-2xl shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 min-w-0 pr-2">
                <div class="w-9 h-9 rounded-[16px] bg-teal-500/20 dark:bg-teal-500/30 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-black text-slate-800 dark:text-slate-200 leading-tight truncate">
                        Minta file dari orang lain?
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">
                        Buat link khusus dengan batas waktu & ukuran file
                    </p>
                </div>
            </div>
            <button type="button" onclick="openCreateDropModal()" class="h-9 px-3.5 rounded-[16px] bg-teal-500 hover:bg-teal-400 active:scale-95 text-white text-xs font-black shrink-0 shadow-xs transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap ios-press">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="whitespace-nowrap">Buat Link</span>
            </button>
        </div>

        @if($currentFolder)
            <!-- BREADCRUMB & CURRENT FOLDER BANNER -->
            <div class="liquid-card rounded-[24px] p-4 bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm backdrop-blur-2xl space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <a href="{{ $currentFolder->parent_id ? route('drive.index', ['folder_id' => $currentFolder->parent_id]) : route('drive.index') }}"
                           class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all shrink-0 active:scale-95" title="Kembali ke folder sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                        </a>
                        <div class="min-w-0">
                            <!-- Breadcrumbs Trail -->
                            <div class="flex items-center gap-1 text-[11px] text-slate-400 font-semibold overflow-x-auto no-scrollbar">
                                <a href="{{ route('drive.index') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">SwanDrive</a>
                                @foreach($breadcrumbs as $crumb)
                                    <span>/</span>
                                    @if($crumb->id === $currentFolder->id)
                                        <span class="text-teal-600 dark:text-teal-400 font-black truncate max-w-[120px]">{{ $crumb->name }}</span>
                                    @else
                                        <a href="{{ route('drive.index', ['folder_id' => $crumb->id]) }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors truncate max-w-[100px]">{{ $crumb->name }}</a>
                                    @endif
                                @endforeach
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <h2 class="text-base font-black text-slate-900 dark:text-white truncate">{{ $currentFolder->name }}</h2>
                                <div id="current-folder-share-badge">
                                    @if($currentFolder->is_public)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Dibagikan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center gap-1">
                                            Privat
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Folder Action Controls -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button"
                                onclick="openFolderShareModal('{{ $currentFolder->id }}', '{{ addslashes($currentFolder->name) }}', '{{ $currentFolder->formatted_size }}', '{{ $currentFolder->share_url }}', {{ $currentFolder->is_public ? 'true' : 'false' }}, '{{ route('drive.folders.share.toggle', $currentFolder) }}')"
                                class="p-2 rounded-xl liquid-glass bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 border border-teal-500/20 active:scale-95 transition-all cursor-pointer"
                                title="Bagikan Folder Ini">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                            </svg>
                        </button>
                        <button type="button"
                                onclick="openEditFolderModal('{{ $currentFolder->id }}', '{{ addslashes($currentFolder->name) }}', '{{ $currentFolder->color }}', '{{ addslashes($currentFolder->description ?? '') }}')"
                                class="p-2 rounded-xl liquid-glass bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-white/40 active:scale-95 transition-all cursor-pointer"
                                title="Edit Folder">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>
                        <button type="button"
                                onclick="confirmDeleteFolder('{{ $currentFolder->id }}', '{{ addslashes($currentFolder->name) }}', '{{ route('drive.folders.destroy', $currentFolder) }}')"
                                class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-900/40 active:scale-95 transition-all cursor-pointer"
                                title="Hapus Folder Ini">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>

                @if(!empty($currentFolder->description))
                    <p class="text-xs text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                        {{ $currentFolder->description }}
                    </p>
                @endif
            </div>
        @endif

        <!-- 2. SEARCH & FILTER BAR -->
        <div class="space-y-2.5">
            <!-- Search Input Form -->
            <form method="GET" action="{{ route('drive.index') }}" class="relative">
                <input type="hidden" name="tab" value="files">
                @if($currentFolder)
                    <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                @endif
                <input type="hidden" name="category" value="{{ $activeCategory }}">
                <input type="hidden" name="source" value="{{ $activeSource }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama file, ekstensi, pengirim, atau catatan..."
                       class="w-full pl-9 pr-8 py-2.5 text-xs rounded-[20px] liquid-card bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs backdrop-blur-2xl font-medium">
                <span class="absolute left-3.5 top-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                @if(!empty($search))
                    <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'category' => $activeCategory, 'folder_id' => $currentFolder?->id])) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-bold">
                        ✕
                    </a>
                @endif
            </form>

            <!-- Sumber Berkas (Ownership / Origin Filter) -->
            <div class="ios-segmented-track p-1 rounded-[20px] flex items-center gap-1 w-full bg-slate-200/70 dark:bg-white/5 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-inner">
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => 'all', 'category' => $activeCategory !== 'received' ? $activeCategory : null, 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="flex-1 py-1.5 rounded-[16px] text-[11px] font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $activeSource === 'all' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
                    <span>🌐 Semua</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded-full font-black {{ $activeSource === 'all' ? 'bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $sourceCounts['all'] }}</span>
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => 'my', 'category' => $activeCategory !== 'received' ? $activeCategory : null, 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="flex-1 py-1.5 rounded-[16px] text-[11px] font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $activeSource === 'my' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
                    <span>👤 Berkas Saya</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded-full font-black {{ $activeSource === 'my' ? 'bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $sourceCounts['my'] }}</span>
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => 'received', 'category' => $activeCategory !== 'received' ? $activeCategory : null, 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="flex-1 py-1.5 rounded-[16px] text-[11px] font-black text-center transition-all flex items-center justify-center gap-1.5 ios-press {{ $activeSource === 'received' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold' }}">
                    <span>📥 Dari Pihak Luar</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded-full font-black {{ $activeSource === 'received' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300' : 'bg-slate-300/60 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $sourceCounts['received'] }}</span>
                </a>
            </div>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 -mx-1 px-1">
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => $activeSource !== 'all' ? $activeSource : null, 'category' => 'all', 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $activeCategory === 'all' ? 'bg-teal-500 text-white shadow-sm ring-1 ring-teal-400/30' : 'liquid-glass bg-white/70 dark:bg-slate-900/75 text-slate-600 dark:text-slate-400 border border-white/50 dark:border-white/10 hover:bg-white/90' }}">
                    Semua ({{ $categoryCounts['all'] }})
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => $activeSource !== 'all' ? $activeSource : null, 'category' => 'shared', 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $activeCategory === 'shared' ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-400/40' : 'liquid-glass bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-300/50 dark:border-emerald-700/50 hover:bg-emerald-100/80' }}">
                    🔗 Dibagikan ({{ $categoryCounts['shared'] ?? 0 }})
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => $activeSource !== 'all' ? $activeSource : null, 'category' => 'document', 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $activeCategory === 'document' ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-400/30' : 'liquid-glass bg-white/70 dark:bg-slate-900/75 text-slate-600 dark:text-slate-400 border border-white/50 dark:border-white/10 hover:bg-white/90' }}">
                    📄 Dokumen ({{ $categoryCounts['document'] }})
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => $activeSource !== 'all' ? $activeSource : null, 'category' => 'image', 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $activeCategory === 'image' ? 'bg-purple-600 text-white shadow-sm ring-1 ring-purple-400/30' : 'liquid-glass bg-white/70 dark:bg-slate-900/75 text-slate-600 dark:text-slate-400 border border-white/50 dark:border-white/10 hover:bg-white/90' }}">
                    🖼️ Gambar ({{ $categoryCounts['image'] }})
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => $activeSource !== 'all' ? $activeSource : null, 'category' => 'archive', 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $activeCategory === 'archive' ? 'bg-amber-600 text-white shadow-sm ring-1 ring-amber-400/30' : 'liquid-glass bg-white/70 dark:bg-slate-900/70 text-slate-600 dark:text-slate-400 border border-white/50 dark:border-white/10 hover:bg-white/90' }}">
                    📦 Arsip ({{ $categoryCounts['archive'] }})
                </a>
                <a href="{{ route('drive.index', array_filter(['tab' => 'files', 'source' => $activeSource !== 'all' ? $activeSource : null, 'category' => 'other', 'search' => $search, 'folder_id' => $currentFolder?->id])) }}"
                   class="px-3.5 py-1.5 rounded-[16px] text-xs font-black shrink-0 transition-all ios-press {{ $activeCategory === 'other' ? 'bg-slate-700 text-white shadow-sm ring-1 ring-slate-400/30' : 'liquid-glass bg-white/70 dark:bg-slate-900/70 text-slate-600 dark:text-slate-400 border border-white/50 dark:border-white/10 hover:bg-white/90' }}">
                    📎 Lainnya ({{ $categoryCounts['other'] }})
                </a>
            </div>
        </div>

        <!-- 3. FOLDERS SECTION -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span>{{ $currentFolder ? 'Sub-Folder' : 'Folder Saya' }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold">{{ $folders->count() }}</span>
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            id="btn-toggle-multi-select"
                            onclick="toggleMultiSelectMode()" 
                            class="px-2.5 py-1 rounded-full bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center gap-1.5 transition-all ios-press cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="btn-toggle-multi-select-text">Pilih Banyak</span>
                    </button>
                    <button type="button" onclick="openCreateFolderModal()" class="text-xs font-black text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1 ios-press cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Folder Baru</span>
                    </button>
                </div>
            </div>

            @if($folders->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @foreach($folders as $fld)
                        @php
                            $fldColor = $fld->colorMeta();
                            $isDropFolder = $fld->isDropFolder();
                        @endphp
                        <div id="folder-card-{{ $fld->id }}" class="liquid-card rounded-[22px] bg-white/80 dark:bg-slate-900/75 border {{ $isDropFolder ? 'border-amber-400/60 dark:border-amber-500/50 shadow-xs shadow-amber-500/10' : 'border-white/60 dark:border-white/10 shadow-xs' }} hover:shadow-md hover:border-teal-400/40 p-3 space-y-2.5 transition-all group relative">
                            <!-- Multi-Select Checkbox for Folder -->
                            <div class="batch-select-checkbox-container hidden absolute top-2 right-2 z-20">
                                <label class="relative flex items-center justify-center p-1 cursor-pointer">
                                    <input type="checkbox" 
                                           name="batch_folders[]" 
                                           value="{{ $fld->id }}" 
                                           data-type="folder"
                                           data-name="{{ addslashes($fld->name) }}"
                                           onchange="handleItemSelect(this)" 
                                           class="w-5 h-5 rounded-lg border-2 border-teal-500 text-teal-600 focus:ring-teal-500/20 bg-white/90 dark:bg-slate-800 cursor-pointer shadow-sm">
                                </label>
                            </div>

                            <a href="{{ route('drive.index', ['folder_id' => $fld->id]) }}" class="block space-y-2">
                                <div class="flex items-start justify-between gap-1.5">
                                    <div class="w-10 h-10 rounded-[14px] {{ $isDropFolder ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400' : $fldColor['iconBg'] }} flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                        @if($isDropFolder)
                                            <span class="text-lg">📥</span>
                                        @else
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-1.5V9a3 3 0 00-3-3h-3.379a3 3 0 01-2.121-.879L8.379 4.04A3 3 0 006.257 3.16H4.5A3 3 0 001.5 6.16v11.84a3 3 0 003 3h15z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @if($isDropFolder)
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-300/60 dark:border-amber-700/60">
                                                Drop
                                            </span>
                                        @endif
                                        <div id="folder-share-badge-{{ $fld->id }}">
                                            @if($fld->is_public)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-700/60 shadow-2xs" title="Folder dibagikan publik">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span>🔗 Dibagikan</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 border border-slate-200/60 dark:border-slate-700/60" title="Folder privat">
                                                    <span>🔒 Privat</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-black text-slate-800 dark:text-white truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors" title="{{ $fld->name }}">
                                        {{ $fld->name }}
                                    </h3>
                                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                        {{ $fld->files_count }} berkas
                                    </p>
                                </div>
                            </a>

                            <!-- Folder Quick Actions Menu -->
                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800/80">
                                <a href="{{ route('orders.index', ['folder_id' => $fld->id, 'create' => 1]) }}"
                                   class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors cursor-pointer"
                                   title="Kirim ke Klien (Buat Paywall)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v1.5c0 .414.336.75.75.75h.75m0 0h12m-12 0a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25h12a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25m-12 0h12" />
                                    </svg>
                                </a>
                                <button type="button"
                                        id="folder-share-btn-{{ $fld->id }}"
                                        onclick="openFolderShareModal('{{ $fld->id }}', '{{ addslashes($fld->name) }}', '{{ $fld->formatted_size }}', '{{ $fld->share_url }}', {{ $fld->is_public ? 'true' : 'false' }}, '{{ route('drive.folders.share.toggle', $fld) }}')"
                                        class="p-1.5 rounded-lg {{ $fld->is_public ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 ring-1 ring-emerald-400/30' : 'text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-colors cursor-pointer"
                                        title="{{ $fld->is_public ? 'Folder Dibagikan (Tautan Aktif)' : 'Bagikan Folder' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="openEditFolderModal('{{ $fld->id }}', '{{ addslashes($fld->name) }}', '{{ $fld->color }}', '{{ addslashes($fld->description ?? '') }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                        title="Ubah Nama/Warna">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="confirmDeleteFolder('{{ $fld->id }}', '{{ addslashes($fld->name) }}', '{{ route('drive.folders.destroy', $fld) }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer"
                                        title="Hapus Folder">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 3. LIST OF STORED FILES -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Daftar Berkas Tersimpan ({{ $files->count() }})
                </h2>
                <span class="text-[11px] text-teal-600 dark:text-teal-400 font-bold">Tersimpan Aman</span>
            </div>

            @forelse($files as $file)
                @php
                    $meta = $file->categoryMeta();
                    $isReceivedFromOther = !empty($file->upload_link_id) || !empty($file->uploader_name);
                @endphp
                <div id="file-{{ $file->id }}" data-file-id="{{ $file->id }}" data-is-public="{{ $file->is_public ? 'true' : 'false' }}" @if($isReceivedFromOther) data-received-from-other="1" @endif class="liquid-card rounded-[24px] {{ $isReceivedFromOther ? 'bg-gradient-to-br from-amber-500/10 via-white/85 to-white/80 dark:from-amber-950/30 dark:via-slate-900/80 dark:to-slate-900/75 border-amber-400/50 dark:border-amber-500/40 shadow-xs ring-1 ring-amber-400/20' : ($file->is_public ? 'bg-white/85 dark:bg-slate-900/80 border border-emerald-300/50 dark:border-emerald-500/30 shadow-sm ring-1 ring-emerald-500/15' : 'bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm') }} hover:shadow-md hover:border-teal-400/40 backdrop-blur-2xl p-4 space-y-3 transition-all duration-300">
                    
                    @if($isReceivedFromOther)
                        <!-- Distinct Header Banner for Files Received from Others -->
                        <div class="flex items-center justify-between pb-2.5 border-b border-amber-200/60 dark:border-amber-800/40 text-[11px] font-bold text-amber-800 dark:text-amber-300">
                            <div class="flex items-center gap-1.5 truncate pr-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                <span class="truncate">📥 Diterima dari: <strong class="text-amber-900 dark:text-amber-200">{{ $file->uploader_name ?? 'Pihak Luar' }}</strong></span>
                            </div>
                            <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-300/60 dark:border-amber-700/60 shrink-0">
                                Berkas Masuk
                            </span>
                        </div>
                    @endif

                    <div class="flex items-start justify-between gap-3">
                        <!-- Icon + Name (Click to preview) -->
                        <div onclick="openPreviewModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ addslashes($file->original_name) }}', '{{ $file->formatted_size }}', '{{ strtolower($file->extension) }}', '{{ route('drive.preview', $file) }}', '{{ route('drive.download', $file) }}', '{{ $meta['label'] }}', '{{ addslashes($file->notes ?? '') }}', '{{ $file->created_at->format('d M Y, H:i') }}', '{{ route('drive.destroy', $file) }}', '{{ $file->share_url }}', {{ $file->is_public ? 'true' : 'false' }}, '{{ route('drive.share.toggle', $file) }}', '{{ $file->folder_id }}')"
                             class="flex items-start gap-3 min-w-0 flex-1 cursor-pointer group">
                            <div class="relative shrink-0">
                                <div class="w-10 h-10 rounded-[18px] {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }} flex items-center justify-center font-black text-xs uppercase shadow-2xs group-hover:scale-105 transition-transform overflow-hidden">
                                    @if($file->category === 'image')
                                        <img src="{{ route('drive.preview', ['file' => $file->id, 'thumb' => 1]) }}" alt="{{ $file->title }}" class="w-full h-full object-cover rounded-[18px]" loading="lazy" onerror="this.style.display='none'">
                                    @else
                                        {{ substr($file->extension, 0, 4) }}
                                    @endif
                                </div>
                                <div id="file-icon-share-{{ $file->id }}" class="absolute -bottom-1 -right-1 z-10 pointer-events-none">
                                    @if($file->is_public)
                                        <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900" title="Dibagikan (Tautan Publik Aktif)">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="w-4 h-4 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900" title="Privat (Hanya Anda)">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
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
                                    @if($file->folder_id && !$currentFolder)
                                        <a href="{{ route('drive.index', ['folder_id' => $file->folder_id]) }}" class="inline-flex items-center gap-0.5 font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-2 py-0.5 rounded-md border border-teal-200/60 dark:border-teal-800/60 hover:underline" onclick="event.stopPropagation();">
                                            📁 {{ $file->folder?->name }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge & Multi-Select Checkbox -->
                        <div class="shrink-0 flex flex-col items-end gap-1.5">
                            <!-- Multi-Select Checkbox for File -->
                            <div class="batch-select-checkbox-container hidden">
                                <label class="relative flex items-center justify-center cursor-pointer p-0.5">
                                    <input type="checkbox" 
                                           name="batch_files[]" 
                                           value="{{ $file->id }}" 
                                           data-type="file"
                                           data-name="{{ addslashes($file->title) }}"
                                           onchange="handleItemSelect(this)" 
                                           class="w-5 h-5 rounded-lg border-2 border-teal-500 text-teal-600 focus:ring-teal-500/20 bg-white/90 dark:bg-slate-800 cursor-pointer shadow-sm">
                                </label>
                            </div>

                            <div id="file-share-badge-{{ $file->id }}">
                                @if($file->is_public)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-300/70 dark:border-emerald-700/60 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                        </svg>
                                        <span>Dibagikan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/80">
                                        <svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                        <span>Privat</span>
                                    </span>
                                @endif
                            </div>

                            @if($file->upload_link_id)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800" title="Diterima dari link drop: {{ $file->uploadLink?->title }}">
                                    📥 {{ Str::limit($file->uploadLink?->title ?? 'Drop Link', 14) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($file->notes))
                        @if($isReceivedFromOther)
                            <div class="text-[11px] text-amber-900 dark:text-amber-200 bg-amber-50/80 dark:bg-amber-950/40 px-3 py-2 rounded-[16px] border border-amber-200/60 dark:border-amber-800/40 flex items-start gap-1.5">
                                <span class="shrink-0 text-amber-600 dark:text-amber-400 font-bold">💬 Pesan:</span>
                                <span class="italic">"{{ $file->notes }}"</span>
                            </div>
                        @else
                            <div class="text-[11px] text-slate-600 dark:text-slate-300 bg-slate-50/80 dark:bg-slate-900/60 px-3 py-1.5 rounded-[16px] border border-slate-100 dark:border-slate-800/80 italic">
                                "{{ $file->notes }}"
                            </div>
                        @endif
                    @endif

                    <!-- Action Bar (Clean 2-Tier iOS Structure) -->
                    <div class="pt-2.5 border-t border-slate-100 dark:border-white/5 space-y-2">
                        <!-- Tier 1: Primary Actions (Lihat & Unduh) -->
                        <div class="grid grid-cols-2 gap-2">
                            <!-- View (Preview) -->
                            <button type="button"
                                    onclick="openPreviewModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ addslashes($file->original_name) }}', '{{ $file->formatted_size }}', '{{ strtolower($file->extension) }}', '{{ route('drive.preview', $file) }}', '{{ route('drive.download', $file) }}', '{{ $meta['label'] }}', '{{ addslashes($file->notes ?? '') }}', '{{ $file->created_at->format('d M Y, H:i') }}', '{{ route('drive.destroy', $file) }}', '{{ $file->share_url }}', {{ $file->is_public ? 'true' : 'false' }}, '{{ route('drive.share.toggle', $file) }}', '{{ $file->folder_id }}')"
                                    class="w-full py-2.5 px-3 rounded-[16px] bg-teal-500 hover:bg-teal-400 active:scale-[0.98] text-white text-xs font-black flex items-center justify-center gap-1.5 shadow-sm transition-all cursor-pointer ios-press"
                                    title="Lihat Pratinjau Berkas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Lihat File</span>
                            </button>

                            <!-- Download -->
                            <a href="{{ route('drive.download', $file) }}"
                               class="w-full py-2.5 px-3 rounded-[16px] bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200/70 dark:border-white/10 flex items-center justify-center gap-1.5 active:scale-[0.98] transition-all ios-press"
                               title="Unduh ke Perangkat">
                                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <span>Unduh</span>
                            </a>
                        </div>

                        <!-- Tier 2: Secondary Utilities (Transfer / Pindah / Hapus) -->
                        <div class="flex items-center gap-2">
                            <!-- Share Transfer -->
                            <button type="button"
                                    id="file-share-btn-{{ $file->id }}"
                                    onclick="openShareModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ $file->formatted_size }}', '{{ $file->share_url }}', {{ $file->is_public ? 'true' : 'false' }}, '{{ route('drive.share.toggle', $file) }}')"
                                    class="flex-1 py-2 px-3 rounded-[16px] {{ $file->is_public ? 'bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800/80' : 'bg-slate-100/80 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200/70 dark:border-white/10' }} text-xs font-bold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-all cursor-pointer ios-press"
                                    title="{{ $file->is_public ? 'Tautan Aktif (Bisa Diunduh Publik)' : 'Bagikan Link (Privat)' }}">
                                <svg class="w-3.5 h-3.5 {{ $file->is_public ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                                </svg>
                                <span>{{ $file->is_public ? '🔗 Tautan Aktif' : '🔒 Bagikan' }}</span>
                            </button>

                            <!-- Move to Folder Trigger Button -->
                            <button type="button"
                                    onclick="openMoveFileModal('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ $file->folder_id }}')"
                                    class="w-9 h-9 shrink-0 rounded-[14px] bg-slate-100/80 hover:bg-teal-50 dark:bg-slate-800/80 dark:hover:bg-teal-950/40 text-slate-500 hover:text-teal-600 dark:hover:text-teal-400 border border-slate-200/70 dark:border-white/10 flex items-center justify-center active:scale-90 transition-all cursor-pointer ios-press"
                                    title="Pindahkan ke Folder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                                </svg>
                            </button>

                            <!-- Paywall Client Release Button -->
                            <a href="{{ route('orders.index', ['file_id' => $file->id, 'create' => 1]) }}"
                               class="w-9 h-9 shrink-0 rounded-[14px] bg-slate-100/80 hover:bg-emerald-50 dark:bg-slate-800/80 dark:hover:bg-emerald-950/40 text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/70 dark:border-white/10 flex items-center justify-center active:scale-90 transition-all cursor-pointer ios-press"
                               title="Kirim ke Klien (Buat Paywall)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v1.5c0 .414.336.75.75.75h.75m0 0h12m-12 0a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25h12a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25m-12 0h12" />
                                </svg>
                            </a>

                            <!-- Safe In-App Delete Button -->
                            <button type="button"
                                    onclick="confirmDeleteFile('{{ $file->id }}', '{{ addslashes($file->title) }}', '{{ $file->formatted_size }}', '{{ route('drive.destroy', $file) }}')"
                                    class="w-9 h-9 shrink-0 rounded-[14px] bg-slate-100/80 hover:bg-rose-50 dark:bg-slate-800/80 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200/70 dark:border-white/10 flex items-center justify-center active:scale-90 transition-all cursor-pointer ios-press"
                                    title="Hapus Berkas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 liquid-card rounded-[28px] bg-white/70 dark:bg-slate-900/75 border border-dashed border-slate-300 dark:border-white/10 space-y-3 backdrop-blur-xl">
                    <div class="w-14 h-14 rounded-[22px] bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center shadow-xs">
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
                    <button type="button" onclick="document.getElementById('upload-input').click()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-[18px] bg-teal-500 hover:bg-teal-400 active:scale-95 text-white text-xs font-black shadow-md shadow-teal-500/20 transition-all ios-press">
                        + Unggah Berkas Pertama
                    </button>
                </div>
            @endforelse
        </div>
    @else
        <!-- TAB 2: LINK TERIMA FILE (DROP LINKS) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between liquid-card rounded-[24px] bg-white/80 dark:bg-slate-900/75 p-4 border border-white/60 dark:border-white/10 shadow-xs backdrop-blur-2xl">
                <div class="min-w-0 pr-2">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Link Terima Berkas ({{ $uploadLinks->count() }})
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                        Tautan publik agar orang lain bisa mengunggah berkas ke drive Anda.
                    </p>
                </div>
                <button type="button" onclick="openCreateDropModal()" class="px-4 py-2 rounded-[18px] bg-teal-500 hover:bg-teal-400 text-white text-xs font-black shadow-md shadow-teal-500/20 active:scale-95 transition-all shrink-0 flex items-center gap-1.5 cursor-pointer ios-press">
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
                <div class="liquid-card rounded-[24px] bg-white/80 dark:bg-slate-900/75 border border-white/60 dark:border-white/10 shadow-sm hover:shadow-md hover:border-teal-400/40 backdrop-blur-2xl p-4 space-y-3 transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-sm font-black text-slate-900 dark:text-white truncate">
                                {{ $link->title }}
                            </h3>
                            @if(!empty($link->description))
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 font-medium">
                                    {{ $link->description }}
                                </p>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $status['bg'] }} {{ $status['text'] }} border {{ $status['border'] }} shrink-0 shadow-2xs">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <!-- Parameter Badges Grid -->
                    <div class="grid grid-cols-3 gap-2 bg-slate-50/80 dark:bg-slate-900/60 p-3 rounded-[18px] border border-slate-100 dark:border-slate-800/80 text-center text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Batas Ukuran</span>
                            <span class="font-black text-teal-600 dark:text-teal-400">{{ $link->max_file_size_mb }} MB</span>
                        </div>
                        <div class="border-x border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-semibold">Kuota Berkas</span>
                            <span class="font-black text-slate-700 dark:text-slate-200">{{ $link->uploaded_files_count }} / {{ $link->max_files }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Batas Waktu</span>
                            @if($link->expires_at)
                                <span class="font-black {{ $link->isExpired() ? 'text-rose-500' : 'text-amber-500' }}" title="{{ $link->expires_at->format('d M Y H:i') }}">
                                    {{ $link->isExpired() ? 'Habis' : $link->expires_at->diffForHumans(['parts' => 1]) }}
                                </span>
                            @else
                                <span class="font-black text-slate-500">Selamanya</span>
                            @endif
                        </div>
                    </div>

                    <!-- Dedicated Folder Info & Navigation -->
                    @if($link->folder)
                        <div class="flex items-center justify-between p-2.5 rounded-[18px] bg-teal-50/70 dark:bg-teal-950/40 border border-teal-200/70 dark:border-teal-800/60 text-xs">
                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                <div class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-slate-400 font-semibold block leading-tight">Folder Penyimpanan Khusus:</span>
                                    <span class="font-black text-teal-800 dark:text-teal-200 truncate block">{{ $link->folder->name }}</span>
                                </div>
                            </div>
                            <a href="{{ route('drive.index', ['tab' => 'files', 'folder_id' => $link->folder_id]) }}" class="px-2.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] shrink-0 active:scale-95 transition-all shadow-xs flex items-center gap-1">
                                <span>Buka Folder</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
                        <!-- URL Input Display + Copy Button -->
                        <div class="flex items-center gap-1.5">
                            <div class="relative flex-1 min-w-0">
                                <input type="text" readonly value="{{ $link->public_url }}"
                                       onclick="this.select(); copyToClipboard('{{ $link->public_url }}', this)"
                                       class="w-full pl-7 pr-3 py-2 text-[11px] font-mono rounded-[16px] bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 truncate cursor-pointer focus:outline-none focus:ring-1 focus:ring-teal-500 font-medium"
                                       title="Klik untuk memilih tautan">
                                <span class="absolute left-2.5 top-2.5 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                    </svg>
                                </span>
                            </div>
                            <button type="button"
                                    onclick="copyToClipboard('{{ $link->public_url }}', this)"
                                    class="px-3.5 py-2 rounded-[16px] bg-teal-500 hover:bg-teal-400 text-white text-xs font-black shrink-0 flex items-center gap-1.5 shadow-xs active:scale-95 transition-all cursor-pointer ios-press"
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
                                   class="px-3 py-1.5 rounded-[16px] bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold flex items-center gap-1.5 active:scale-95 transition-all cursor-pointer ios-press"
                                   title="Bagikan via WhatsApp">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.768 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>

                                <a href="{{ $link->public_url }}" target="_blank"
                                   class="px-3 py-1.5 rounded-[16px] liquid-glass bg-slate-100/80 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold border border-white/50 dark:border-white/10 flex items-center gap-1 active:scale-95 transition-all cursor-pointer ios-press"
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
                                    <button type="submit" class="px-3 py-1.5 rounded-[16px] text-xs font-bold border active:scale-95 transition-all cursor-pointer ios-press {{ $link->is_active ? 'bg-slate-100 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200' : 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400' }}">
                                        {{ $link->is_active ? 'Tutup Link' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <button type="button"
                                        onclick="confirmDeleteDropLink('{{ $link->id }}', '{{ addslashes($link->title) }}', '{{ route('drive.drop-links.destroy', $link) }}')"
                                        class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-[14px] hover:bg-rose-50 dark:hover:bg-rose-950/30 active:scale-90 transition-all cursor-pointer ios-press"
                                        title="Hapus Link Drop">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 liquid-card rounded-[28px] bg-white/70 dark:bg-slate-900/75 border border-dashed border-slate-300 dark:border-white/10 space-y-3 backdrop-blur-xl">
                    <div class="w-14 h-14 rounded-[22px] bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center shadow-xs">
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
                    <button type="button" onclick="openCreateDropModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[18px] bg-teal-500 hover:bg-teal-400 active:scale-95 text-white text-xs font-black shadow-md shadow-teal-500/20 transition-all cursor-pointer ios-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>+ Buat Tautan Terima Berkas</span>
                    </button>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Floating Batch Multi-Select Action Bar (Sticky Inline - Above Folders) -->
    <div id="batch-action-bar" class="sticky top-0 z-30 -mx-4 px-4 py-2 hidden transition-all duration-300 ease-out" style="margin-top: -0.25rem;">
        <div class="rounded-[24px] bg-slate-900/95 dark:bg-slate-950/95 text-white p-3 shadow-2xl backdrop-blur-3xl border border-teal-500/40 ring-1 ring-white/15 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-full bg-teal-500/30 border border-teal-400/50 text-teal-300 flex items-center justify-center text-xs font-black shrink-0 shadow-inner" id="batch-selected-count">
                    0
                </span>
                <div class="min-w-0">
                    <span class="text-xs font-black text-white block leading-tight truncate">Item Dipilih</span>
                    <button type="button" onclick="selectAllBatchItems()" class="text-[10px] text-teal-300 font-bold hover:underline" id="batch-select-all-btn">
                        Pilih Semua
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" 
                        onclick="exitMultiSelectMode()" 
                        class="px-3 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold active:scale-95 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        id="btn-trigger-batch-delete"
                        onclick="openBatchDeleteModal()" 
                        class="px-4 py-2 rounded-2xl bg-rose-500 hover:bg-rose-600 active:scale-95 text-white text-xs font-black shadow-lg shadow-rose-500/40 flex items-center gap-1.5 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- App Attribution Footer & Generous Bottom Spacer for Mobile Dock Clearance -->
    <div class="pt-8 pb-3 text-center">
        <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
            SwanDrive &bull; Cloud Storage Pribadi & Aman
        </p>
    </div>
    <div class="h-44 w-full shrink-0" aria-hidden="true"></div>
</div>
@endsection

@push('modals')
<!-- MODAL 1: SHARE & TRANSFER FILE (AJAX-Enabled & Native Share Supported) -->
<div id="share-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="share-backdrop" onclick="closeShareModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="share-panel" class="w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] backdrop-blur-3xl shadow-2xl p-5 modal-sheet-safe border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-3 cursor-pointer" onclick="closeShareModal()"></div>
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/60 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Bagikan & Transfer Berkas</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[200px] sm:max-w-xs" id="modal-file-info">Informasi berkas...</p>
                    </div>
                </div>
                <button type="button" onclick="closeShareModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Public Access Status Toggle Card -->
            <div onclick="toggleFileShareStatus()" class="p-3.5 rounded-2xl bg-teal-500/5 dark:bg-teal-500/10 border border-teal-500/20 flex items-center justify-between gap-3 cursor-pointer select-none hover:bg-teal-500/10 dark:hover:bg-teal-500/15 active:scale-[0.99] transition-all">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-black text-slate-900 dark:text-white">Akses Berbagi Publik</span>
                        <span id="file-share-badge" class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                            Publik
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed block" id="share-status-desc">
                        Siapa saja yang memiliki tautan dapat membuka & mengunduh berkas ini langsung tanpa login.
                    </span>
                </div>
                <button type="button" id="btn-file-share-switch" aria-label="Toggle akses berbagi publik" class="relative inline-flex items-center shrink-0 cursor-pointer focus:outline-none">
                    <input type="checkbox" id="file-share-toggle" class="sr-only pointer-events-none">
                    <div id="file-share-track" class="w-11 h-6 bg-slate-300 dark:bg-slate-700 rounded-full transition-colors relative pointer-events-none">
                        <div id="file-share-knob" class="w-5 h-5 bg-white rounded-full shadow-md absolute top-0.5 left-0.5 transition-transform duration-200 ease-out pointer-events-none"></div>
                    </div>
                </button>
            </div>

            <!-- Active Public Share Section -->
            <div id="active-share-section" class="space-y-3">
                <!-- Native Share Sheet CTA -->
                <button type="button" 
                        id="btn-native-share"
                        onclick="shareCurrentFileNative()" 
                        class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white text-xs sm:text-sm font-black flex items-center justify-center gap-2 shadow-lg shadow-teal-500/25 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    <span>Bagikan Tautan (Share Sheet)</span>
                </button>

                <!-- URL Copy Input Box -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Tautan Unduhan Langsung</label>
                    <div class="flex items-center gap-1.5">
                        <input type="text" id="share-url-input" readonly class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-mono select-all focus:outline-none">
                        <button type="button" id="btn-copy-file-url" onclick="copyToClipboard(document.getElementById('share-url-input').value, this)" class="px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold active:scale-95 transition-all shrink-0 flex items-center gap-1 shadow-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849a2.25 2.25 0 00-3.332 0l-4.5 4.5a2.25 2.25 0 003.182 3.182l1.5-1.5m4.5-4.5l1.5-1.5a2.25 2.25 0 013.182 3.182l-4.5 4.5a2.25 2.25 0 01-3.182 0" />
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                </div>

                <!-- Shortcuts (WhatsApp & Open Link) -->
                <div class="grid grid-cols-2 gap-2 pt-0.5">
                    <a id="btn-wa-share" href="#" target="_blank" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all shadow-xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.768 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                    <a id="btn-preview-share" href="#" target="_blank" class="py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                        <span>Buka Link ↗</span>
                    </a>
                </div>

                <!-- Buat Paywall Klien CTA -->
                <a id="btn-paywall-share" href="#"
                   class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black flex items-center justify-center gap-2 active:scale-95 transition-all text-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v1.5c0 .414.336.75.75.75h.75m0 0h12m-12 0a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25h12a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25m-12 0h12" />
                    </svg>
                    <span>💼 Buat Paywall Klien (Kirim Link Berkas)</span>
                </a>

                <!-- QR Code Toggle Button -->
                <button type="button" id="btn-toggle-share-qr" onclick="toggleShareQrCode()" class="w-full py-2.5 px-3 rounded-xl bg-teal-50 dark:bg-teal-950/40 hover:bg-teal-100 dark:hover:bg-teal-900/50 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/80 text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.008v.008H6.75V6.75zM6.75 16.5h.008v.008H6.75V16.5zM16.5 6.75h.008v.008H16.5V6.75zM13.5 13.5h.008v.008H13.5V13.5zM13.5 19.5h.008v.008H13.5V19.5zM19.5 13.5h.008v.008H19.5V13.5zM19.5 19.5h.008v.008H19.5V19.5zM16.5 16.5h.008v.008H16.5V16.5z" />
                    </svg>
                    <span id="btn-qr-label">Pindai / Tampilkan QR Code</span>
                </button>

                <!-- Expandable QR Code Card -->
                <div id="share-qr-container" class="hidden transition-all duration-300 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <div class="inline-block p-3 rounded-2xl bg-white shadow-md border border-slate-100 dark:border-slate-800">
                        <img id="share-qr-image" src="" alt="QR Code Berkas" class="w-36 h-36 mx-auto object-contain">
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Arahkan kamera smartphone ke QR Code untuk membuka atau mengunduh berkas langsung.
                    </p>
                    <div class="flex items-center justify-center gap-2 pt-0.5">
                        <a id="btn-download-share-qr" href="#" target="_blank" download="qrcode-berkas.png" class="px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            <span>Unduh QR</span>
                        </a>
                        <button type="button" onclick="copyToClipboard(document.getElementById('share-url-input').value, this)" class="px-3.5 py-1.5 rounded-xl bg-teal-500 text-white hover:bg-teal-600 text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.599m7.332 0c.055.194.084.4.084.615v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.215.03-.42.084-.615m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                            <span>Salin Link</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Inactive Private Share Notice with 1-Click Activate & Share Button -->
            <div id="inactive-share-notice" class="hidden p-4 bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200 space-y-3">
                <div>
                    <span class="font-bold flex items-center gap-1.5 mb-1 text-amber-800 dark:text-amber-300">
                        <span>🔒</span> File Bersifat Privat
                    </span>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-relaxed">
                        Tautan transfer berkas saat ini dinonaktifkan. Aktifkan akses publik agar penerima dapat melihat atau mengunduh berkas tanpa perlu akun.
                    </p>
                </div>
                <button type="button" 
                        onclick="toggleFileShareStatus(true)" 
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-white font-black text-xs flex items-center justify-center gap-2 active:scale-95 transition-all shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span>Aktifkan & Bagikan Tautan Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2: BUAT LINK TERIMA FILE (DROP LINK SETTINGS) -->
<div id="create-drop-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="drop-backdrop" onclick="closeCreateDropModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="drop-panel" class="w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] backdrop-blur-3xl shadow-2xl p-5 modal-sheet-safe border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-3 cursor-pointer" onclick="closeCreateDropModal()"></div>
            
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pengaturan Link Terima File</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Atur batasan sebelum membagikan link ke orang lain</p>
                </div>
                <button type="button" onclick="closeCreateDropModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
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
                <div class="pt-3 pb-2 flex items-center gap-2 sticky bottom-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md -mx-5 px-5 -mb-2 border-t border-slate-100 dark:border-slate-800/80">
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
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="quota-panel" class="w-full max-w-md bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] backdrop-blur-3xl shadow-2xl p-5 modal-sheet-safe border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar text-slate-800 dark:text-white">
            <!-- Drag Handle -->
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-3 cursor-pointer" onclick="closeQuotaModal()"></div>

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
                <button type="button" onclick="closeQuotaModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($isGoogleConnected)
            <!-- Google Drive Direct Sync Banner in Modal -->
            <div class="p-3 rounded-2xl bg-gradient-to-r from-teal-500/10 via-emerald-500/10 to-teal-500/5 border border-teal-500/25 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-black text-slate-800 dark:text-white truncate">Google Drive Terhubung</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Kapasitas aktual akun Google Anda</div>
                    </div>
                </div>
                <form action="{{ route('drive.sync-google-quota') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-black shadow-xs active:scale-95 transition-all cursor-pointer">
                        Sinkronkan
                    </button>
                </form>
            </div>
            @endif

            <!-- Form Update Quota -->
            <form action="{{ route('drive.quota.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <!-- Quick Presets Grid -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Kapasitas Cepat:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="setQuotaValue(500, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 500 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            500 MB
                        </button>
                        <button type="button" onclick="setQuotaValue(1024, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 1024 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            1 GB
                        </button>
                        <button type="button" onclick="setQuotaValue(5120, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 5120 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            5 GB
                        </button>
                        <button type="button" onclick="setQuotaValue(15360, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 15360 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            15 GB (Google)
                        </button>
                        <button type="button" onclick="setQuotaValue(102400, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 102400 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            100 GB
                        </button>
                        <button type="button" onclick="setQuotaValue(1048576, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 1048576 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            1 TB
                        </button>
                        <button type="button" onclick="setQuotaValue(2097152, event)" class="quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 2097152 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            2 TB
                        </button>
                        <button type="button" onclick="setQuotaValue(5242880, event)" class="col-span-2 quota-preset-btn py-2 px-2.5 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer {{ $quotaMb == 5242880 ? 'bg-teal-500 text-white border-teal-500 shadow-xs' : 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-teal-500' }}">
                            5 TB (Google Workspace)
                        </button>
                    </div>
                </div>

                <!-- Custom Input -->
                <div class="space-y-1.5">
                    <label for="quota-input-mb" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Atau Masukkan Kapasitas Khusus (MB):
                    </label>
                    <div class="relative">
                        <input type="number" id="quota-input-mb" name="storage_quota_mb" value="{{ $quotaMb }}" min="10" max="5242880" required
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
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="preview-panel" class="w-full max-w-lg bg-white/95 dark:bg-slate-900/95 rounded-t-[36px] backdrop-blur-3xl shadow-2xl p-4 sm:p-5 modal-sheet-safe border-t border-white/60 dark:border-white/10 pointer-events-auto transform translate-y-full transition-transform duration-300 flex flex-col max-h-[92vh] text-slate-800 dark:text-white">
            <!-- Drag Handle -->
            <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-3 cursor-pointer shrink-0" onclick="closePreviewModal()"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 gap-3">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div id="pv-ext-badge" class="w-10 h-10 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center font-black text-xs uppercase shrink-0 shadow-2xs">
                        TXT
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <h2 id="pv-title" class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate leading-tight">
                                Nama Berkas
                            </h2>
                            <div id="pv-share-badge" class="shrink-0"></div>
                        </div>
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
                    <button type="button" onclick="closePreviewModal()" aria-label="Tutup modal" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
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
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="closePreviewModal()" class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" id="pv-delete-btn" onclick="openDeleteModalFromPreview()" class="py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/50 text-xs font-bold active:scale-95 transition-all cursor-pointer flex items-center gap-1.5" title="Hapus Berkas Ini">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                    <button type="button" id="pv-move-btn" onclick="openMoveModalFromPreview()" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-bold active:scale-95 transition-all cursor-pointer flex items-center gap-1.5" title="Pindahkan ke Folder">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                        <span>Pindah</span>
                    </button>
                </div>
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <button type="button" id="pv-share-btn" onclick="openShareFromPreview()" class="py-2.5 px-3 rounded-xl bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/80 text-xs font-bold active:scale-95 transition-all cursor-pointer flex items-center gap-1.5 shadow-xs" title="Bagi / Transfer Berkas">
                        <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                        </svg>
                        <span>Transfer</span>
                    </button>
                    <a id="pv-download-btn" href="#" class="py-2.5 px-3.5 sm:px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-white text-xs font-extrabold shadow-md shadow-teal-500/25 flex items-center gap-1.5 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 5: KONFIRMASI HAPUS BERKAS / DROP LINK (IN-APP NATIVE BOTTOM-SHEET) -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="delete-backdrop" onclick="closeDeleteModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="delete-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 text-slate-800 dark:text-white">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer" onclick="closeDeleteModal()"></div>
            
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/60 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white" id="delete-modal-title">Hapus Berkas dari SwanDrive?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed" id="delete-modal-desc">
                        Berkas ini akan dihapus secara permanen dari server dan ruang penyimpanan Anda.
                    </p>
                </div>
            </div>

            <!-- Target Item Summary Card -->
            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="min-w-0 flex-1 mr-2">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate" id="delete-target-name">nama-file.pdf</span>
                    <span class="text-[10px] text-slate-400 font-medium" id="delete-target-info">0 KB</span>
                </div>
                <span class="px-2 py-0.5 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 text-[10px] font-bold shrink-0">
                    Permanen
                </span>
            </div>

            <form id="delete-form" method="POST" action="" onsubmit="handleDeleteSubmit(event)">
                @csrf
                @method('DELETE')
                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" id="btn-confirm-delete" class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white text-xs font-extrabold shadow-md shadow-rose-600/25 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                        <svg class="w-4 h-4" id="delete-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <svg class="w-4 h-4 animate-spin hidden" id="delete-spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span id="delete-btn-text">Ya, Hapus File</span>
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: KONFIRMASI HAPUS BANYAK (BATCH DELETE MODAL) -->
<div id="batch-delete-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="batch-delete-backdrop" onclick="closeBatchDeleteModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="batch-delete-panel" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 text-slate-800 dark:text-white">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-2 cursor-pointer" onclick="closeBatchDeleteModal()"></div>
            
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/60 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white" id="batch-delete-modal-title">Hapus Semua Item Terpilih?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                        Folder dan berkas yang dipilih akan dihapus secara permanen beserta seluruh isinya.
                    </p>
                </div>
            </div>

            <!-- Target Summary -->
            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-600 dark:text-slate-400">Total Item Terpilih:</span>
                    <span class="text-rose-600 dark:text-rose-400 font-black" id="batch-delete-summary-text">0 item</span>
                </div>
                <div id="batch-delete-items-list" class="max-h-32 overflow-y-auto no-scrollbar space-y-1 text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="button" id="btn-confirm-batch-delete" onclick="executeBatchDelete()" class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white text-xs font-extrabold shadow-md shadow-rose-600/25 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                    <svg class="w-4 h-4" id="batch-delete-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <svg class="w-4 h-4 animate-spin hidden" id="batch-delete-spinner" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="batch-delete-btn-text">Ya, Hapus Semua Terpilih</span>
                </button>
                <button type="button" onclick="closeBatchDeleteModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: BUAT FOLDER BARU -->
<div id="create-folder-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="create-folder-backdrop" onclick="closeCreateFolderModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="create-folder-panel" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 text-slate-800 dark:text-white max-h-[90vh] overflow-y-auto">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-1 cursor-pointer" onclick="closeCreateFolderModal()"></div>

            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/60 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Buat Folder Baru</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Lokasi: <span class="font-bold text-teal-600 dark:text-teal-400">{{ $currentFolder ? $currentFolder->name : 'Root SwanDrive' }}</span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateFolderModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('drive.folders.store') }}" method="POST" class="space-y-4" onsubmit="handleCreateFolderSubmit(event)">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $currentFolder?->id ?? '' }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Folder <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="folder-name-input" required maxlength="100" placeholder="Contoh: Dokumen Klien, Aset UI"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold placeholder:text-slate-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Warna Aksen Folder
                    </label>
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                        @foreach([
                            'teal' => ['bg' => 'bg-teal-500', 'name' => 'Teal'],
                            'indigo' => ['bg' => 'bg-indigo-500', 'name' => 'Indigo'],
                            'rose' => ['bg' => 'bg-rose-500', 'name' => 'Rose'],
                            'amber' => ['bg' => 'bg-amber-500', 'name' => 'Amber'],
                            'emerald' => ['bg' => 'bg-emerald-500', 'name' => 'Emerald'],
                            'sky' => ['bg' => 'bg-sky-500', 'name' => 'Sky'],
                            'purple' => ['bg' => 'bg-purple-500', 'name' => 'Purple'],
                            'slate' => ['bg' => 'bg-slate-500', 'name' => 'Slate'],
                        ] as $colorKey => $colorItem)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="{{ $colorKey }}" class="peer hidden" {{ $colorKey === 'teal' ? 'checked' : '' }}>
                                <div class="p-2 rounded-xl border-2 border-transparent peer-checked:border-teal-500 peer-checked:bg-teal-500/10 flex flex-col items-center gap-1 transition-all">
                                    <span class="w-5 h-5 rounded-full {{ $colorItem['bg'] }} shadow-xs block"></span>
                                    <span class="text-[9px] font-bold text-slate-500 capitalize">{{ $colorItem['name'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <input type="text" name="description" maxlength="255" placeholder="Contoh: Kumpulan berkas proposal 2026"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium placeholder:text-slate-400">
                </div>

                <!-- Toggle Public Share -->
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Aktifkan Share Link Publik Langsung</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Folder dapat langsung diakses & diunduh via tautan publik yang aman.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="is_public" value="1" class="sr-only peer">
                        <div class="w-10 h-5 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-500"></div>
                    </label>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" id="btn-create-folder" class="flex-1 py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-400 active:scale-[0.98] text-white text-xs font-black shadow-md shadow-teal-500/25 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                        <span id="create-folder-text">Buat Folder</span>
                        <svg class="w-4 h-4 animate-spin hidden" id="create-folder-spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </button>
                    <button type="button" onclick="closeCreateFolderModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT FOLDER -->
<div id="edit-folder-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="edit-folder-backdrop" onclick="closeEditFolderModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="edit-folder-panel" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 text-slate-800 dark:text-white max-h-[90vh] overflow-y-auto">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-1 cursor-pointer" onclick="closeEditFolderModal()"></div>

            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/60 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Edit Folder</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Perbarui nama, warna, atau catatan folder</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditFolderModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="edit-folder-form" action="" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Folder <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit-folder-name" required maxlength="100"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Warna Aksen Folder
                    </label>
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                        @foreach([
                            'teal' => ['bg' => 'bg-teal-500', 'name' => 'Teal'],
                            'indigo' => ['bg' => 'bg-indigo-500', 'name' => 'Indigo'],
                            'rose' => ['bg' => 'bg-rose-500', 'name' => 'Rose'],
                            'amber' => ['bg' => 'bg-amber-500', 'name' => 'Amber'],
                            'emerald' => ['bg' => 'bg-emerald-500', 'name' => 'Emerald'],
                            'sky' => ['bg' => 'bg-sky-500', 'name' => 'Sky'],
                            'purple' => ['bg' => 'bg-purple-500', 'name' => 'Purple'],
                            'slate' => ['bg' => 'bg-slate-500', 'name' => 'Slate'],
                        ] as $colorKey => $colorItem)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" id="edit-color-{{ $colorKey }}" value="{{ $colorKey }}" class="peer hidden">
                                <div class="p-2 rounded-xl border-2 border-transparent peer-checked:border-teal-500 peer-checked:bg-teal-500/10 flex flex-col items-center gap-1 transition-all">
                                    <span class="w-5 h-5 rounded-full {{ $colorItem['bg'] }} shadow-xs block"></span>
                                    <span class="text-[9px] font-bold text-slate-500 capitalize">{{ $colorItem['name'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <input type="text" name="description" id="edit-folder-desc" maxlength="255"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-400 active:scale-[0.98] text-white text-xs font-black shadow-md shadow-teal-500/25 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeEditFolderModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: BAGIKAN LINK FOLDER -->
<div id="folder-share-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="folder-share-backdrop" onclick="closeFolderShareModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="folder-share-panel" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 text-slate-800 dark:text-white max-h-[90vh] overflow-y-auto">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-1 cursor-pointer" onclick="closeFolderShareModal()"></div>

            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/60 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Bagikan Folder</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400" id="folder-share-summary">0 berkas tersimpan</p>
                    </div>
                </div>
                <button type="button" onclick="closeFolderShareModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Folder Identity Header Card -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-2xl">📁</span>
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white truncate" id="folder-share-name">Nama Folder</h4>
                        <span class="text-[10px] text-slate-400 font-semibold" id="folder-share-details">SwanDrive Shared Folder</span>
                    </div>
                </div>
                <span id="folder-share-badge" class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider shrink-0 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                    Publik
                </span>
            </div>

            <!-- Public Access Toggle Card -->
            <div class="p-3.5 rounded-2xl bg-teal-500/5 dark:bg-teal-500/10 border border-teal-500/20 flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-black text-slate-900 dark:text-white block">Akses Berbagi Publik</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed block mt-0.5">
                        Siapapun yang memiliki tautan dapat membuka folder, melihat preview berkas, mengunduh satuan atau ZIP.
                    </span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" id="folder-share-toggle" onchange="toggleFolderShareStatus()" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                </label>
            </div>

            <!-- Share URL Container (Visible when public) -->
            <div id="folder-share-url-container" class="space-y-3">
                <!-- Native Share Sheet CTA -->
                <button type="button" 
                        id="btn-native-folder-share"
                        onclick="shareFolderNative()" 
                        class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white text-xs sm:text-sm font-black flex items-center justify-center gap-2 shadow-lg shadow-teal-500/25 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    <span>Bagikan Tautan Folder (Share Sheet)</span>
                </button>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1.5">
                        Tautan Berbagi Folder
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input type="text" id="folder-share-url-input" readonly
                               class="flex-1 px-3 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-mono select-all focus:outline-none">
                        <button type="button" onclick="copyFolderShareLink()" id="btn-copy-folder-link"
                                class="px-3.5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 active:scale-95 text-white text-xs font-bold flex items-center gap-1.5 transition-all shrink-0 cursor-pointer shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.599m7.332 0c.055.194.084.4.084.615v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.215.03-.42.084-.615m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                            </svg>
                            <span id="copy-folder-btn-text">Salin</span>
                        </button>
                    </div>
                </div>

                <!-- Action Shortcut Buttons -->
                <div class="grid grid-cols-2 gap-2">
                    <a id="folder-share-open-btn" href="#" target="_blank"
                       class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-all text-center">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        <span>Buka Tautan</span>
                    </a>
                    <a id="folder-share-wa-btn" href="#" target="_blank"
                       class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-all text-center shadow-xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.073-.509-1.633-.674-2.686-2.339-2.767-2.449-.081-.11-.662-.881-.662-1.68 0-.798.419-1.192.569-1.353.15-.16.329-.2.438-.2.11 0 .22.001.317.006.103.005.241-.039.378.291.144.346.49 1.198.533 1.286.043.088.072.191.014.306-.058.115-.088.187-.174.288-.087.102-.184.227-.263.305-.088.087-.18.181-.077.358.103.177.46 1.082 1.344 1.868.514.457.946.598 1.08.686.134.088.212.077.291-.014.079-.092.34-.395.431-.531.092-.136.183-.114.306-.068.123.045.783.369.917.436.134.067.224.1.257.156.033.056.033.568-.111.973z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>

                <!-- Buat Paywall Klien CTA -->
                <a id="folder-share-paywall-btn" href="#"
                   class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black flex items-center justify-center gap-2 active:scale-95 transition-all text-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v1.5c0 .414.336.75.75.75h.75m0 0h12m-12 0a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25h12a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25m-12 0h12" />
                    </svg>
                    <span>💼 Buat Paywall Klien (Kirim Link Folder)</span>
                </a>

                <!-- QR Code Toggle Button -->
                <button type="button" id="btn-toggle-folder-share-qr" onclick="toggleFolderShareQrCode()" class="w-full py-2.5 px-3 rounded-xl bg-teal-50 dark:bg-teal-950/40 hover:bg-teal-100 dark:hover:bg-teal-900/50 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/80 text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.008v.008H6.75V6.75zM6.75 16.5h.008v.008H6.75V16.5zM16.5 6.75h.008v.008H16.5V6.75zM13.5 13.5h.008v.008H13.5V13.5zM13.5 19.5h.008v.008H13.5V19.5zM19.5 13.5h.008v.008H19.5V13.5zM19.5 19.5h.008v.008H19.5V19.5zM16.5 16.5h.008v.008H16.5V16.5z" />
                    </svg>
                    <span id="btn-folder-qr-label">Pindai / Tampilkan QR Code</span>
                </button>

                <!-- Expandable QR Code Card -->
                <div id="folder-share-qr-container" class="hidden transition-all duration-300 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <div class="inline-block p-3 rounded-2xl bg-white shadow-md border border-slate-100 dark:border-slate-800">
                        <img id="folder-share-qr-image" src="" alt="QR Code Folder" class="w-36 h-36 mx-auto object-contain">
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Arahkan kamera smartphone ke QR Code untuk membuka atau mengunduh folder langsung.
                    </p>
                    <div class="flex items-center justify-center gap-2 pt-0.5">
                        <a id="btn-download-folder-share-qr" href="#" target="_blank" download="qrcode-folder.png" class="px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            <span>Unduh QR</span>
                        </a>
                        <button type="button" onclick="copyFolderShareLink()" class="px-3.5 py-1.5 rounded-xl bg-teal-500 text-white hover:bg-teal-600 text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.599m7.332 0c.055.194.084.4.084.615v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.215.03-.42.084-.615m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                            <span>Salin Link</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Disabled State Notice (When private) with 1-Click Activate Button -->
            <div id="folder-share-private-notice" class="hidden p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 space-y-3">
                <div>
                    <span class="text-xs font-bold flex items-center gap-1.5 mb-1 text-amber-800 dark:text-amber-300">
                        <span>📁</span> Folder ini Sedang Privat
                    </span>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-relaxed">
                        Aktifkan akses publik agar siapa saja yang memiliki tautan dapat membuka atau mengunduh isi folder ini.
                    </p>
                </div>
                <button type="button" 
                        onclick="toggleFolderShareStatus(true)" 
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-white font-black text-xs flex items-center justify-center gap-2 active:scale-95 transition-all shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span>Aktifkan & Bagikan Folder Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: PINDAHKAN BERKAS KE FOLDER -->
<div id="move-file-modal" class="fixed inset-0 z-50 hidden transition-all duration-300" aria-modal="true" role="dialog">
    <div id="move-file-backdrop" onclick="closeMoveFileModal()" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity duration-300 opacity-0"></div>
    <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center pointer-events-none">
        <div id="move-file-panel" class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl p-5 modal-sheet-safe border-t border-slate-100 dark:border-slate-800 pointer-events-auto transform translate-y-full transition-transform duration-300 space-y-4 text-slate-800 dark:text-white max-h-[90vh] overflow-y-auto">
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-1 cursor-pointer" onclick="closeMoveFileModal()"></div>

            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/60 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pindahkan Berkas</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih folder tujuan untuk berkas ini</p>
                    </div>
                </div>
                <button type="button" onclick="closeMoveFileModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Target File Info -->
            <div class="p-3 bg-slate-50 dark:bg-slate-950/70 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-2.5">
                <span class="text-xl">📄</span>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate" id="move-file-title">Nama Berkas</span>
                    <span class="text-[10px] text-slate-400">Pindahkan ke folder di bawah</span>
                </div>
            </div>

            <form id="move-file-form" action="" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Pilih Folder Tujuan:
                    </label>
                    <div class="space-y-1.5 max-h-[220px] overflow-y-auto p-1 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/50">
                        <!-- Root Option -->
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 cursor-pointer transition-all">
                            <input type="radio" name="folder_id" value="" id="move-folder-root" class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <span class="text-base">📁</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">SwanDrive Root (Tanpa Folder)</span>
                            </div>
                        </label>

                        @foreach($allUserFolders as $uf)
                            <label class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 cursor-pointer transition-all">
                                <input type="radio" name="folder_id" value="{{ $uf->id }}" id="move-folder-{{ $uf->id }}" class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <span class="w-3.5 h-3.5 rounded-full {{ $uf->colorMeta()['bg'] }} shrink-0"></span>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $uf->name }}</span>
                                    @if($uf->parent)
                                        <span class="text-[10px] text-slate-400 truncate">di dalam {{ $uf->parent->name }}</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-400 active:scale-[0.98] text-white text-xs font-black shadow-md shadow-teal-500/25 flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                        Pindahkan Sekarang
                    </button>
                    <button type="button" onclick="closeMoveFileModal()" class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold active:scale-95 transition-all cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // ==========================================
    // SwanDrive Multi-File Queue & Chunk Engine
    // ==========================================
    let uploadQueue = [];
    let isUploading = false;
    let isUploadCancelled = false;
    let currentUploadAbortController = null;
    let currentActiveFileUuid = null;

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
        return bytes + ' B';
    }

    // Handle File Selection (Append to Queue)
    function handleFileSelect(input) {
        if (!input.files || input.files.length === 0) return;
        addFilesToQueue(input.files);
        // Clear input value so selecting the same files again triggers onchange
        input.value = '';
    }

    function addFilesToQueue(fileList) {
        for (let i = 0; i < fileList.length; i++) {
            const f = fileList[i];
            const exists = uploadQueue.some(item => item.name === f.name && item.size === f.size);
            if (!exists) {
                const ext = f.name.split('.').pop() || 'FILE';
                uploadQueue.push({
                    id: 'q_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7),
                    file: f,
                    name: f.name,
                    size: f.size,
                    ext: ext.toUpperCase().substring(0, 5),
                    status: 'pending',
                    progress: 0,
                    errorMsg: ''
                });
            }
        }
        renderQueueUI();
    }

    function removeFileFromQueue(id) {
        if (isUploading) return;
        uploadQueue = uploadQueue.filter(item => item.id !== id);
        renderQueueUI();
    }

    function renderQueueUI() {
        const queueDetails = document.getElementById('upload-details');
        const queueList = document.getElementById('queue-items-list');
        const summaryText = document.getElementById('queue-summary-text');
        const totalSizeBadge = document.getElementById('queue-total-size');
        const dropzone = document.getElementById('dropzone');
        const singleTitleContainer = document.getElementById('single-title-container');
        const fileTitleInput = document.getElementById('file-title');
        const btnUploadText = document.getElementById('btn-upload-text');

        if (!queueDetails || !queueList) return;

        if (uploadQueue.length === 0) {
            queueDetails.classList.add('hidden');
            if (dropzone) dropzone.classList.remove('border-teal-500', 'bg-teal-50/90');
            return;
        }

        queueDetails.classList.remove('hidden');
        if (dropzone) dropzone.classList.add('border-teal-500', 'bg-teal-50/90');

        let totalBytes = 0;
        uploadQueue.forEach(item => totalBytes += item.size);

        if (summaryText) {
            summaryText.innerText = `${uploadQueue.length} Berkas Dipilih`;
        }
        if (totalSizeBadge) {
            totalSizeBadge.innerText = formatBytes(totalBytes);
        }
        if (btnUploadText) {
            btnUploadText.innerText = uploadQueue.length > 1
                ? `Simpan ${uploadQueue.length} Berkas ke SwanDrive`
                : 'Simpan ke SwanDrive';
        }

        if (uploadQueue.length === 1) {
            if (singleTitleContainer) singleTitleContainer.classList.remove('hidden');
            if (fileTitleInput && !fileTitleInput.value) {
                const nameWithoutExt = uploadQueue[0].name.substring(0, uploadQueue[0].name.lastIndexOf('.')) || uploadQueue[0].name;
                fileTitleInput.value = nameWithoutExt;
            }
        } else {
            if (singleTitleContainer) singleTitleContainer.classList.add('hidden');
        }

        queueList.innerHTML = '';
        uploadQueue.forEach(item => {
            const row = document.createElement('div');
            row.id = `item-row-${item.id}`;
            row.className = 'flex items-center justify-between py-1.5 px-2 rounded-xl hover:bg-white/50 dark:hover:bg-slate-700/50 transition-colors text-xs';

            let statusIconHtml = '';
            if (item.status === 'completed') {
                statusIconHtml = '<span class="text-emerald-500 font-black text-xs shrink-0 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> Selesai</span>';
            } else if (item.status === 'uploading') {
                statusIconHtml = `<span class="text-teal-600 dark:text-teal-400 font-bold text-[11px] shrink-0 flex items-center gap-1"><svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ${item.progress}%</span>`;
            } else if (item.status === 'error') {
                statusIconHtml = `<span class="text-rose-500 font-bold text-[10px] shrink-0" title="${item.errorMsg}">⚠️ Gagal</span>`;
            } else {
                statusIconHtml = isUploading 
                    ? '<span class="text-slate-400 text-[10px] shrink-0">⏳ Antrean</span>'
                    : `<button type="button" onclick="removeFileFromQueue('${item.id}')" class="text-slate-400 hover:text-rose-500 p-1 transition-colors cursor-pointer shrink-0" title="Hapus dari antrean"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
            }

            row.innerHTML = `
                <div class="flex items-center gap-2 truncate pr-2 min-w-0">
                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md bg-teal-500/15 text-teal-700 dark:text-teal-300 border border-teal-500/25 shrink-0 uppercase">${item.ext}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 truncate">${item.name}</span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">${formatBytes(item.size)}</span>
                    <div id="item-status-${item.id}">
                        ${statusIconHtml}
                    </div>
                </div>
            `;
            queueList.appendChild(row);
        });
    }

    function cancelUpload() {
        if (isUploading) {
            if (!confirm('Apakah Anda yakin ingin membatalkan proses pengunggahan berkas ini?')) {
                return;
            }
            isUploadCancelled = true;
            if (currentUploadAbortController) {
                currentUploadAbortController.abort();
            }
            if (currentActiveFileUuid) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('input[name="_token"]')?.value;
                const fd = new FormData();
                fd.append('file_uuid', currentActiveFileUuid);
                fetch("{{ route('drive.upload-chunk.abort') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                }).catch(() => {});
            }
            resetUploadState();
            return;
        }

        uploadQueue = [];
        renderQueueUI();
        document.getElementById('file-title').value = '';
        document.getElementById('file-notes').value = '';
        const progressContainer = document.getElementById('upload-progress-container');
        if (progressContainer) progressContainer.classList.add('hidden');
    }

    function resetUploadState() {
        isUploading = false;
        isUploadCancelled = false;
        currentUploadAbortController = null;
        currentActiveFileUuid = null;

        const btnSubmit = document.getElementById('btn-submit-upload');
        const btnCancel = document.getElementById('btn-cancel-upload');
        const btnIcon = document.getElementById('btn-upload-icon');
        const btnSpinner = document.getElementById('btn-upload-spinner');
        const btnText = document.getElementById('btn-upload-text');
        const progressContainer = document.getElementById('upload-progress-container');

        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-80', 'cursor-not-allowed');
        }
        if (btnCancel) {
            btnCancel.disabled = false;
            btnCancel.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        if (btnIcon) btnIcon.classList.remove('hidden');
        if (btnSpinner) btnSpinner.classList.add('hidden');
        if (btnText) {
            btnText.innerText = uploadQueue.length > 1
                ? `Simpan ${uploadQueue.length} Berkas ke SwanDrive`
                : 'Simpan ke SwanDrive';
        }
        if (progressContainer) progressContainer.classList.add('hidden');

        renderQueueUI();
    }

    window.addEventListener('beforeunload', function(e) {
        if (isUploading) {
            e.preventDefault();
            e.returnValue = 'Pengunggahan berkas sedang berlangsung. Yakin ingin keluar?';
            return e.returnValue;
        }
    });

    async function startQueueUpload() {
        if (isUploading) return;
        if (!uploadQueue || uploadQueue.length === 0) {
            alert('Silakan pilih berkas yang ingin diunggah terlebih dahulu.');
            return;
        }

        isUploading = true;
        isUploadCancelled = false;

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
        const currentFileLabel = document.getElementById('current-file-name-label');
        const currentChunkLabel = document.getElementById('current-file-chunk-label');
        const currentFileProgressBar = document.getElementById('current-file-progress-bar');

        const folderId = document.getElementById('upload-target-folder')?.value || '';
        const customTitle = document.getElementById('file-title')?.value || '';
        const notes = document.getElementById('file-notes')?.value || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '{{ csrf_token() }}';

        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-80', 'cursor-not-allowed');
        if (btnIcon) btnIcon.classList.add('hidden');
        if (btnSpinner) btnSpinner.classList.remove('hidden');
        if (btnText) btnText.innerText = 'Mengunggah Berkas...';

        if (progressContainer) {
            progressContainer.classList.remove('hidden');
            progressBar.style.width = '0%';
            progressPercent.innerText = '0%';
            statusText.innerText = 'Menyiapkan proses pengunggahan...';
        }

        const totalQueueBytes = uploadQueue.reduce((acc, f) => acc + f.size, 0);
        let completedQueueBytes = 0;
        const CHUNK_SIZE = 2 * 1024 * 1024; // 2MB chunk (safe under all mobile & server conditions)
        let hasErrors = false;

        for (let i = 0; i < uploadQueue.length; i++) {
            if (isUploadCancelled) break;

            const item = uploadQueue[i];
            item.status = 'uploading';
            renderQueueUI();

            const totalChunks = Math.max(1, Math.ceil(item.size / CHUNK_SIZE));
            const fileUuid = 'sf_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
            currentActiveFileUuid = fileUuid;

            if (currentFileLabel) {
                currentFileLabel.innerText = `📄 ${item.name} (${formatBytes(item.size)})`;
            }
            if (statusText) {
                statusText.innerText = `Mengunggah berkas ${i + 1} dari ${uploadQueue.length}...`;
            }

            let fileUploadFailed = false;

            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                if (isUploadCancelled) break;

                const start = chunkIndex * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, item.size);
                const chunkBlob = item.file.slice(start, end, item.file.type || 'application/octet-stream');

                const chunkFormData = new FormData();
                chunkFormData.append('_token', csrfToken);
                chunkFormData.append('chunk', chunkBlob, item.name);
                chunkFormData.append('chunk_index', chunkIndex);
                chunkFormData.append('total_chunks', totalChunks);
                chunkFormData.append('file_uuid', fileUuid);
                chunkFormData.append('file_name', item.name);
                if (folderId) chunkFormData.append('folder_id', folderId);
                if (uploadQueue.length === 1 && customTitle) {
                    chunkFormData.append('title', customTitle);
                }
                if (notes) chunkFormData.append('notes', notes);

                // Retry logic: try up to 3 times per chunk for network resilience
                let attempt = 0;
                let chunkSuccess = false;
                let lastError = null;

                while (attempt < 3 && !chunkSuccess && !isUploadCancelled) {
                    attempt++;
                    try {
                        currentUploadAbortController = new AbortController();
                        const response = await fetch("{{ route('drive.upload-chunk') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: chunkFormData,
                            signal: currentUploadAbortController.signal
                        });

                        const text = await response.text();
                        let result = {};
                        try {
                            result = JSON.parse(text);
                        } catch (e) {
                            result = { error: 'Server mengembalikan respons tidak valid (' + response.status + ')' };
                        }

                        if (!response.ok) {
                            let errorMsg = result.error || result.message;
                            if (!errorMsg && result.errors) {
                                const k = Object.keys(result.errors)[0];
                                errorMsg = result.errors[k][0];
                            }
                            throw new Error(errorMsg || `Gagal mengunggah potongan berkas (${response.status})`);
                        }

                        chunkSuccess = true;
                    } catch (err) {
                        lastError = err;
                        if (isUploadCancelled) break;
                        if (attempt < 3) {
                            if (statusText) {
                                statusText.innerText = `Koneksi tersendat, mencoba ulang potongan ${chunkIndex + 1}/${totalChunks}... (${attempt}/3)`;
                            }
                            await new Promise(r => setTimeout(r, 1000 * attempt));
                        }
                    }
                }

                if (!chunkSuccess) {
                    fileUploadFailed = true;
                    item.status = 'error';
                    item.errorMsg = lastError ? lastError.message : 'Gagal mengirim potongan';
                    hasErrors = true;
                    renderQueueUI();
                    break;
                }

                const chunkPercent = Math.min(100, Math.round(((chunkIndex + 1) / totalChunks) * 100));
                item.progress = chunkPercent;

                if (currentFileProgressBar) currentFileProgressBar.style.width = chunkPercent + '%';
                if (currentChunkLabel) currentChunkLabel.innerText = `${chunkPercent}% (${chunkIndex + 1}/${totalChunks})`;

                const currentUploadedTotal = completedQueueBytes + end;
                const overallPercent = Math.min(99, Math.round((currentUploadedTotal / Math.max(1, totalQueueBytes)) * 100));
                if (progressBar) progressBar.style.width = overallPercent + '%';
                if (progressPercent) progressPercent.innerText = overallPercent + '%';
                if (progressBytes) progressBytes.innerText = `${formatBytes(currentUploadedTotal)} / ${formatBytes(totalQueueBytes)}`;

                if (chunkIndex + 1 === totalChunks) {
                    if (statusText) statusText.innerText = `Menyimpan & memproses ${item.name}...`;
                }
            }

            if (!fileUploadFailed && !isUploadCancelled) {
                item.status = 'completed';
                item.progress = 100;
                completedQueueBytes += item.size;
                renderQueueUI();
            }
        }

        if (isUploadCancelled) {
            resetUploadState();
            return;
        }

        if (!hasErrors) {
            if (progressBar) progressBar.style.width = '100%';
            if (progressPercent) progressPercent.innerText = '100%';
            if (statusText) statusText.innerText = '🎉 Semua berkas berhasil disimpan!';

            if (btnText) btnText.innerText = '✅ Berkas Berhasil Disimpan!';
            if (btnSpinner) btnSpinner.classList.add('hidden');
            if (btnIcon) btnIcon.classList.remove('hidden');

            const uploadMsg = uploadQueue.length > 1
                ? `🎉 ${uploadQueue.length} berkas berhasil disimpan ke SwanDrive!`
                : `🎉 Berkas "${uploadQueue[0]?.name || 'File'}" berhasil disimpan ke SwanDrive!`;

            if (typeof showSwanToast === 'function') {
                showSwanToast(uploadMsg, 'success', 5000);
            }

            try {
                if (navigator.vibrate) navigator.vibrate([40, 60, 40]);
            } catch (e) {}

            setTimeout(() => {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('uploaded', uploadQueue.length);
                if (uploadQueue.length === 1 && uploadQueue[0]?.name) {
                    urlParams.set('uploaded_name', encodeURIComponent(uploadQueue[0].name));
                }
                if (folderId) urlParams.set('folder_id', folderId);
                window.location.href = `{{ route('drive.index') }}?${urlParams.toString()}`;
            }, 1000);
        } else {
            const failedItem = uploadQueue.find(it => it.status === 'error');
            const errorReason = failedItem?.errorMsg ? `: ${failedItem.errorMsg}` : '.';
            alert(`Gagal menyimpan berkas ke SwanDrive${errorReason}\nSilakan periksa kembali berkas atau koneksi Anda.`);
            resetUploadState();
        }
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
            if (files && files.length > 0) {
                addFilesToQueue(files);
            }
        });
    }

    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            startQueueUpload();
        });
    }

    // Share Modal
    let currentShareFile = null;

    function openShareModal(id, title, size, url, isPublic, toggleAction) {
        // Read dynamic status from card element dataset if available to avoid stale arguments
        const fileCard = document.getElementById(`file-${id}`);
        const livePublic = fileCard ? (fileCard.dataset.isPublic === 'true') : (isPublic === true || isPublic === 'true');

        currentShareFile = {
            id: id,
            title: title,
            size: size,
            url: url,
            isPublic: livePublic,
            toggleAction: toggleAction
        };

        document.getElementById('modal-file-info').innerText = `${title} • ${size}`;
        document.getElementById('share-url-input').value = url;
        const toggleCheckbox = document.getElementById('file-share-toggle');
        if (toggleCheckbox) toggleCheckbox.checked = livePublic;

        updateFileShareUI(livePublic, title, size, url);

        const paywallBtn = document.getElementById('btn-paywall-share');
        if (paywallBtn) {
            paywallBtn.href = "{{ route('orders.index') }}?file_id=" + id + "&create=1";
        }

        const modal = document.getElementById('share-modal');
        const backdrop = document.getElementById('share-backdrop');
        const panel = document.getElementById('share-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
        });
    }

    function updateFileShareUI(isPublic, title, size, url) {
        const badge = document.getElementById('file-share-badge');
        const activeSection = document.getElementById('active-share-section');
        const inactiveNotice = document.getElementById('inactive-share-notice');
        const statusDesc = document.getElementById('share-status-desc');
        const track = document.getElementById('file-share-track');
        const knob = document.getElementById('file-share-knob');
        const toggleCheckbox = document.getElementById('file-share-toggle');

        if (toggleCheckbox) toggleCheckbox.checked = !!isPublic;

        if (track && knob) {
            if (isPublic) {
                track.className = 'w-11 h-6 bg-teal-500 rounded-full transition-colors relative cursor-pointer';
                knob.style.transform = 'translateX(20px)';
            } else {
                track.className = 'w-11 h-6 bg-slate-300 dark:bg-slate-700 rounded-full transition-colors relative cursor-pointer';
                knob.style.transform = 'translateX(0px)';
            }
        }

        if (isPublic) {
            if (badge) {
                badge.innerText = 'Publik';
                badge.className = 'px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300';
            }
            if (statusDesc) {
                statusDesc.innerText = 'Tautan transfer aktif. Siapa saja dengan tautan dapat mengunduh berkas ini langsung.';
            }
            if (activeSection) activeSection.classList.remove('hidden');
            if (inactiveNotice) inactiveNotice.classList.add('hidden');

            const waText = encodeURIComponent(`Halo, berikut tautan berkas "${title}" (${size}) di SwanDrive:\n${url}`);
            const waBtn = document.getElementById('btn-wa-share');
            if (waBtn) waBtn.href = `https://wa.me/?text=${waText}`;

            const previewBtn = document.getElementById('btn-preview-share');
            if (previewBtn) previewBtn.href = url;

            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}`;
            const qrImg = document.getElementById('share-qr-image');
            if (qrImg) qrImg.src = qrUrl;
            const qrDownloadBtn = document.getElementById('btn-download-share-qr');
            if (qrDownloadBtn) qrDownloadBtn.href = qrUrl;
            const qrContainer = document.getElementById('share-qr-container');
            if (qrContainer) qrContainer.classList.add('hidden');
            const qrLabel = document.getElementById('btn-qr-label');
            if (qrLabel) qrLabel.innerText = 'Pindai / Tampilkan QR Code';
        } else {
            if (badge) {
                badge.innerText = 'Privat';
                badge.className = 'px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
            }
            if (statusDesc) {
                statusDesc.innerText = 'Tautan transfer sedang privat dan dinonaktifkan.';
            }
            if (activeSection) activeSection.classList.add('hidden');
            if (inactiveNotice) inactiveNotice.classList.remove('hidden');
        }
    }

    function updateCardShareStatus(id, isPublic, shareUrl) {
        // 0. Update fileCard dataset
        const fileCard = document.getElementById(`file-${id}`);
        if (fileCard) {
            fileCard.dataset.isPublic = isPublic ? 'true' : 'false';
            if (!fileCard.dataset.receivedFromOther) {
                if (isPublic) {
                    fileCard.classList.add('border-emerald-300/50', 'dark:border-emerald-500/30', 'ring-1', 'ring-emerald-500/15');
                } else {
                    fileCard.classList.remove('border-emerald-300/50', 'dark:border-emerald-500/30', 'ring-1', 'ring-emerald-500/15');
                }
            }
        }

        // 1. File icon overlay badge on thumbnail
        const iconShare = document.getElementById(`file-icon-share-${id}`);
        if (iconShare) {
            if (isPublic) {
                iconShare.innerHTML = `<span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900" title="Dibagikan (Tautan Publik Aktif)"><svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg></span>`;
            } else {
                iconShare.innerHTML = `<span class="w-4 h-4 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-900" title="Privat (Hanya Anda)"><svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg></span>`;
            }
        }

        // 2. Card header right badge
        const shareBadge = document.getElementById(`file-share-badge-${id}`);
        if (shareBadge) {
            if (isPublic) {
                shareBadge.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-300/70 dark:border-emerald-700/60 shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><svg class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg><span>Dibagikan</span></span>`;
            } else {
                shareBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800/90 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/80"><svg class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg><span>Privat</span></span>`;
            }
        }

        // 3. Card bottom action button
        const shareBtn = document.getElementById(`file-share-btn-${id}`);
        if (shareBtn) {
            if (isPublic) {
                shareBtn.className = "flex-1 py-2 px-3 rounded-[16px] bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800/80 text-xs font-bold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-all cursor-pointer ios-press";
                shareBtn.title = "Tautan Aktif (Bisa Diunduh Publik)";
                shareBtn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg><span>🔗 Tautan Aktif</span>`;
            } else {
                shareBtn.className = "flex-1 py-2 px-3 rounded-[16px] bg-slate-100/80 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200/70 dark:border-white/10 text-xs font-bold flex items-center justify-center gap-1.5 active:scale-[0.98] transition-all cursor-pointer ios-press";
                shareBtn.title = "Bagikan Link (Privat)";
                shareBtn.innerHTML = `<svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg><span>🔒 Bagikan</span>`;
            }
        }

        // 4. Update preview modal badge & share button if open
        if (typeof currentPreviewFile !== 'undefined' && currentPreviewFile && currentPreviewFile.id == id) {
            currentPreviewFile.isPublic = isPublic;
            currentPreviewFile.shareUrl = shareUrl;
            const pvShareBadge = document.getElementById('pv-share-badge');
            if (pvShareBadge) {
                if (isPublic) {
                    pvShareBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-300/70 dark:border-emerald-700/60 shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>🔗 Dibagikan</span>`;
                } else {
                    pvShareBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 shadow-2xs">🔒 Privat</span>`;
                }
            }
            const pvShareBtn = document.getElementById('pv-share-btn');
            if (pvShareBtn) {
                if (isPublic) {
                    pvShareBtn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg><span>🔗 Tautan Aktif</span>`;
                } else {
                    pvShareBtn.innerHTML = `<svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg><span>Bagikan</span>`;
                }
            }
        }
    }

    async function toggleFileShareStatus(andShareAfter = false) {
        if (!currentShareFile || !currentShareFile.toggleAction) return;

        const previousState = !!currentShareFile.isPublic;
        const targetState = andShareAfter ? true : !previousState;

        // Optimistically update toggle UI for snappy feel
        updateFileShareUI(targetState, currentShareFile.title, currentShareFile.size, currentShareFile.url);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value;

        try {
            const response = await fetch(currentShareFile.toggleAction, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_public: targetState })
            });
            const data = await response.json();
            if (data.success) {
                currentShareFile.isPublic = data.is_public;
                currentShareFile.url = data.share_url;
                const shareInput = document.getElementById('share-url-input');
                if (shareInput) shareInput.value = data.share_url;

                updateFileShareUI(data.is_public, currentShareFile.title, currentShareFile.size, data.share_url);
                updateCardShareStatus(currentShareFile.id, data.is_public, data.share_url);

                if (typeof showSwanToast === 'function') {
                    showSwanToast(data.message || 'Status berbagi berkas diperbarui.');
                }
                if (andShareAfter && data.is_public) {
                    shareCurrentFileNative();
                }
            } else {
                currentShareFile.isPublic = previousState;
                updateFileShareUI(previousState, currentShareFile.title, currentShareFile.size, currentShareFile.url);
                alert(data.message || 'Gagal mengubah status berbagi berkas.');
            }
        } catch (err) {
            currentShareFile.isPublic = previousState;
            updateFileShareUI(previousState, currentShareFile.title, currentShareFile.size, currentShareFile.url);
            alert('Terjadi kesalahan jaringan saat memperbarui status.');
        }
    }

    async function shareCurrentFileNative() {
        if (!currentShareFile) return;
        const url = currentShareFile.url;
        const title = currentShareFile.title;
        const size = currentShareFile.size;

        if (navigator.share) {
            try {
                await navigator.share({
                    title: title,
                    text: `Halo, berikut tautan berkas "${title}" (${size}) di SwanDrive:`,
                    url: url
                });
                return;
            } catch (err) {
                if (err.name === 'AbortError') return;
            }
        }
        copyToClipboard(url, document.getElementById('btn-copy-file-url'));
    }

    function toggleShareQrCode() {
        const container = document.getElementById('share-qr-container');
        const label = document.getElementById('btn-qr-label');
        if (!container) return;
        const isHidden = container.classList.contains('hidden');
        if (isHidden) {
            container.classList.remove('hidden');
            if (label) label.innerText = 'Tutup QR Code';
        } else {
            container.classList.add('hidden');
            if (label) label.innerText = 'Pindai / Tampilkan QR Code';
        }
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
        if (typeof pushModalHistoryState === 'function') {
            pushModalHistoryState('createDrop');
        }
        const modal = document.getElementById('create-drop-modal');
        const backdrop = document.getElementById('drop-backdrop');
        const panel = document.getElementById('drop-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
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
        if (typeof pushModalHistoryState === 'function') {
            pushModalHistoryState('quota');
        }
        const modal = document.getElementById('quota-modal');
        const backdrop = document.getElementById('quota-backdrop');
        const panel = document.getElementById('quota-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
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

    // Generic Copy to Clipboard Helper (Bulletproof for iOS Safari & Secure Contexts)
    async function copyToClipboard(text, btnElement) {
        if (!text) return;
        const handleSuccess = () => {
            if (typeof showSwanToast === 'function') {
                showSwanToast('Tautan berhasil disalin ke papan klip!');
            }
            if (btnElement) {
                const originalHtml = btnElement.innerHTML;
                btnElement.innerText = 'Tersalin!';
                setTimeout(() => {
                    btnElement.innerHTML = originalHtml;
                }, 2000);
            }
        };

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                handleSuccess();
                return;
            }
        } catch (e) {
            // fallback below
        }

        try {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            textarea.style.top = '0';
            textarea.setAttribute('readonly', '');
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            const successful = document.execCommand('copy');
            document.body.removeChild(textarea);
            if (successful) {
                handleSuccess();
                return;
            }
        } catch (err) {
            // fallback below
        }

        prompt('Salin link ini:', text);
    }

    // Preview Modal Handlers
    let currentPreviewAbortController = null;
    let currentLoadedPreviewText = '';
    let currentPreviewFile = null;

    function copyPreviewText(btn) {
        if (!currentLoadedPreviewText) return;
        copyToClipboard(currentLoadedPreviewText, btn);
    }

    function openShareFromPreview() {
        if (!currentPreviewFile || !currentPreviewFile.shareToggleUrl) return;
        const file = { ...currentPreviewFile };
        closePreviewModal();
        setTimeout(() => {
            openShareModal(file.id, file.title, file.size, file.shareUrl, file.isPublic, file.shareToggleUrl);
        }, 250);
    }

    function openMoveModalFromPreview() {
        if (!currentPreviewFile || !currentPreviewFile.id) return;
        const file = { ...currentPreviewFile };
        closePreviewModal();
        setTimeout(() => {
            openMoveFileModal(file.id, file.title, file.folderId);
        }, 250);
    }

    function openPreviewModal(id, title, originalName, size, ext, previewUrl, downloadUrl, label, notes, date, deleteUrl, shareUrl, isPublic, shareToggleUrl, folderId) {
        const modal = document.getElementById('preview-modal');
        const backdrop = document.getElementById('preview-backdrop');
        const panel = document.getElementById('preview-panel');

        currentPreviewFile = {
            id: id,
            title: title || originalName,
            size: size,
            deleteUrl: deleteUrl || '',
            shareUrl: shareUrl || '',
            isPublic: isPublic || false,
            shareToggleUrl: shareToggleUrl || '',
            folderId: folderId || ''
        };

        const deleteBtn = document.getElementById('pv-delete-btn');
        if (deleteBtn) {
            deleteBtn.style.display = deleteUrl ? '' : 'none';
        }

        const shareBtn = document.getElementById('pv-share-btn');
        if (shareBtn) {
            shareBtn.style.display = shareToggleUrl ? '' : 'none';
            if (isPublic) {
                shareBtn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg><span>🔗 Tautan Aktif</span>`;
            } else {
                shareBtn.innerHTML = `<svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg><span>Bagikan</span>`;
            }
        }

        // Populate header info
        document.getElementById('pv-title').innerText = title || originalName;
        const pvShareBadge = document.getElementById('pv-share-badge');
        if (pvShareBadge) {
            if (isPublic) {
                pvShareBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-300/70 dark:border-emerald-700/60 shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>🔗 Dibagikan</span>`;
            } else {
                pvShareBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 shadow-2xs">🔒 Privat</span>`;
            }
        }
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

    // In-App Safe Deletion Handlers
    function openDeleteModalFromPreview() {
        if (!currentPreviewFile || !currentPreviewFile.deleteUrl) return;
        const file = { ...currentPreviewFile };
        closePreviewModal();
        setTimeout(() => {
            confirmDeleteFile(file.id, file.title, file.size, file.deleteUrl);
        }, 250);
    }

    function confirmDeleteFile(id, title, size, url) {
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-backdrop');
        const panel = document.getElementById('delete-panel');
        const titleEl = document.getElementById('delete-modal-title');
        const descEl = document.getElementById('delete-modal-desc');
        const nameEl = document.getElementById('delete-target-name');
        const infoEl = document.getElementById('delete-target-info');
        const form = document.getElementById('delete-form');
        const btnText = document.getElementById('delete-btn-text');

        form.action = url;
        titleEl.innerText = 'Hapus Berkas dari SwanDrive?';
        descEl.innerText = 'Berkas ini akan dihapus secara permanen dari server dan ruang penyimpanan Anda.';
        nameEl.innerText = title;
        infoEl.innerText = size || '';
        btnText.innerText = 'Ya, Hapus File';

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function confirmDeleteDropLink(id, title, url) {
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-backdrop');
        const panel = document.getElementById('delete-panel');
        const titleEl = document.getElementById('delete-modal-title');
        const descEl = document.getElementById('delete-modal-desc');
        const nameEl = document.getElementById('delete-target-name');
        const infoEl = document.getElementById('delete-target-info');
        const form = document.getElementById('delete-form');
        const btnText = document.getElementById('delete-btn-text');

        form.action = url;
        titleEl.innerText = 'Hapus Tautan Terima Berkas?';
        descEl.innerText = 'Tautan pengumpulan ini akan dihapus permanen. Siapapun yang membuka link ini tidak akan bisa mengirim berkas lagi.';
        nameEl.innerText = title;
        infoEl.innerText = 'Tautan Drop Box';
        btnText.innerText = 'Ya, Hapus Tautan';

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-backdrop');
        const panel = document.getElementById('delete-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function handleDeleteSubmit(e) {
        const btn = document.getElementById('btn-confirm-delete');
        const icon = document.getElementById('delete-icon');
        const spinner = document.getElementById('delete-spinner');
        const btnText = document.getElementById('delete-btn-text');

        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (btnText) btnText.innerText = 'Menghapus...';
    }

    // ==========================================
    // FOLDER MANAGEMENT JAVASCRIPT FUNCTIONS
    // ==========================================

    // 1. Create Folder Modal
    function openCreateFolderModal() {
        const modal = document.getElementById('create-folder-modal');
        const backdrop = document.getElementById('create-folder-backdrop');
        const panel = document.getElementById('create-folder-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
                setTimeout(() => {
                    const nameInput = document.getElementById('folder-name-input');
                    if (nameInput) nameInput.focus();
                }, 150);
            });
        });
    }

    function closeCreateFolderModal() {
        const backdrop = document.getElementById('create-folder-backdrop');
        const panel = document.getElementById('create-folder-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('create-folder-modal').classList.add('hidden');
        }, 300);
    }

    function handleCreateFolderSubmit(e) {
        const btn = document.getElementById('btn-create-folder');
        const text = document.getElementById('create-folder-text');
        const spinner = document.getElementById('create-folder-spinner');

        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        if (text) text.innerText = 'Membuat Folder...';
        if (spinner) spinner.classList.remove('hidden');
    }

    // 2. Edit Folder Modal
    function openEditFolderModal(id, name, color, description, updateUrl) {
        document.getElementById('edit-folder-form').action = updateUrl;
        document.getElementById('edit-folder-name').value = name;
        document.getElementById('edit-folder-desc').value = description || '';

        // Select color radio
        const colorRadio = document.getElementById('edit-color-' + (color || 'teal'));
        if (colorRadio) {
            colorRadio.checked = true;
        }

        const modal = document.getElementById('edit-folder-modal');
        const backdrop = document.getElementById('edit-folder-backdrop');
        const panel = document.getElementById('edit-folder-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
        });
    }

    function closeEditFolderModal() {
        const backdrop = document.getElementById('edit-folder-backdrop');
        const panel = document.getElementById('edit-folder-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('edit-folder-modal').classList.add('hidden');
        }, 300);
    }

    // 3. Share Folder Modal
    let currentSharedFolder = null;

    function openFolderShareModal(id, name, info, url, isPublic, toggleUrl) {
        currentSharedFolder = {
            id: id,
            name: name,
            info: info,
            url: url,
            isPublic: isPublic,
            toggleUrl: toggleUrl
        };

        document.getElementById('folder-share-name').innerText = name;
        document.getElementById('folder-share-summary').innerText = info;
        document.getElementById('folder-share-url-input').value = url;
        document.getElementById('folder-share-toggle').checked = isPublic;

        updateFolderShareUI(isPublic, name, url);

        const paywallFolderBtn = document.getElementById('folder-share-paywall-btn');
        if (paywallFolderBtn) {
            paywallFolderBtn.href = "{{ route('orders.index') }}?folder_id=" + id + "&create=1";
        }

        const modal = document.getElementById('folder-share-modal');
        const backdrop = document.getElementById('folder-share-backdrop');
        const panel = document.getElementById('folder-share-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
        });
    }

    function updateFolderShareUI(isPublic, name, url) {
        const badge = document.getElementById('folder-share-badge');
        const urlContainer = document.getElementById('folder-share-url-container');
        const privateNotice = document.getElementById('folder-share-private-notice');
        const openBtn = document.getElementById('folder-share-open-btn');
        const waBtn = document.getElementById('folder-share-wa-btn');

        if (isPublic) {
            badge.innerText = 'Publik';
            badge.className = 'px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider shrink-0 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300';
            urlContainer.classList.remove('hidden');
            privateNotice.classList.add('hidden');

            openBtn.href = url;
            const waText = encodeURIComponent(`Halo, berikut tautan folder berkas "${name}" di SwanDrive:\n${url}`);
            waBtn.href = `https://wa.me/?text=${waText}`;

            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}`;
            const qrImg = document.getElementById('folder-share-qr-image');
            if (qrImg) qrImg.src = qrUrl;
            const qrDownloadBtn = document.getElementById('btn-download-folder-share-qr');
            if (qrDownloadBtn) qrDownloadBtn.href = qrUrl;
            const qrContainer = document.getElementById('folder-share-qr-container');
            if (qrContainer) qrContainer.classList.add('hidden');
            const qrLabel = document.getElementById('btn-folder-qr-label');
            if (qrLabel) qrLabel.innerText = 'Pindai / Tampilkan QR Code';
        } else {
            badge.innerText = 'Privat';
            badge.className = 'px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider shrink-0 bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
            urlContainer.classList.add('hidden');
            privateNotice.classList.remove('hidden');
        }
    }

    function toggleFolderShareQrCode() {
        const container = document.getElementById('folder-share-qr-container');
        const label = document.getElementById('btn-folder-qr-label');
        if (!container) return;
        const isHidden = container.classList.contains('hidden');
        if (isHidden) {
            container.classList.remove('hidden');
            if (label) label.innerText = 'Tutup QR Code';
        } else {
            container.classList.add('hidden');
            if (label) label.innerText = 'Pindai / Tampilkan QR Code';
        }
    }

    function closeFolderShareModal() {
        const backdrop = document.getElementById('folder-share-backdrop');
        const panel = document.getElementById('folder-share-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('folder-share-modal').classList.add('hidden');
        }, 300);
    }

    function updateFolderCardShareStatus(id, isPublic, shareUrl) {
        const badge = document.getElementById(`folder-share-badge-${id}`);
        if (badge) {
            if (isPublic) {
                badge.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-700/60 shadow-2xs" title="Folder dibagikan publik"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>🔗 Dibagikan</span></span>`;
            } else {
                badge.innerHTML = `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 border border-slate-200/60 dark:border-slate-700/60" title="Folder privat"><span>🔒 Privat</span></span>`;
            }
        }
        const shareBtn = document.getElementById(`folder-share-btn-${id}`);
        if (shareBtn) {
            if (isPublic) {
                shareBtn.className = "p-1.5 rounded-lg text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 ring-1 ring-emerald-400/30 transition-colors cursor-pointer";
                shareBtn.title = "Folder Dibagikan (Tautan Aktif)";
            } else {
                shareBtn.className = "p-1.5 rounded-lg text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer";
                shareBtn.title = "Bagikan Folder";
            }
        }
        const currentFolderBadge = document.getElementById('current-folder-share-badge');
        if (currentFolderBadge && currentSharedFolder && currentSharedFolder.id == '{{ $currentFolder?->id }}') {
            if (isPublic) {
                currentFolderBadge.innerHTML = `<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1 shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Dibagikan</span>`;
            } else {
                currentFolderBadge.innerHTML = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center gap-1">Privat</span>`;
            }
        }
    }

    async function toggleFolderShareStatus(andShareAfter = false) {
        if (!currentSharedFolder || !currentSharedFolder.toggleUrl) return;

        const toggleCheckbox = document.getElementById('folder-share-toggle');
        const previousState = currentSharedFolder.isPublic;
        const desiredState = andShareAfter ? true : (toggleCheckbox ? toggleCheckbox.checked : !previousState);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value;

        try {
            const res = await fetch(currentSharedFolder.toggleUrl, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                currentSharedFolder.isPublic = data.is_public;
                currentSharedFolder.url = data.share_url;
                const input = document.getElementById('folder-share-url-input');
                if (input) input.value = data.share_url;
                if (toggleCheckbox) toggleCheckbox.checked = data.is_public;

                updateFolderShareUI(data.is_public, currentSharedFolder.name, data.share_url);
                updateFolderCardShareStatus(currentSharedFolder.id, data.is_public, data.share_url);

                if (typeof showSwanToast === 'function') {
                    showSwanToast(data.message || 'Status berbagi folder diperbarui.');
                }
                if (andShareAfter && data.is_public) {
                    shareFolderNative();
                }
            } else {
                if (toggleCheckbox) toggleCheckbox.checked = previousState;
                alert('Gagal memperbarui status berbagi folder.');
            }
        } catch (err) {
            if (toggleCheckbox) toggleCheckbox.checked = previousState;
            alert('Terjadi kesalahan jaringan.');
        }
    }

    async function shareFolderNative() {
        if (!currentSharedFolder) return;
        const url = currentSharedFolder.url;
        const name = currentSharedFolder.name;
        const info = currentSharedFolder.info;

        if (navigator.share) {
            try {
                await navigator.share({
                    title: name,
                    text: `Halo, berikut tautan folder "${name}" (${info}) di SwanDrive:`,
                    url: url
                });
                return;
            } catch (err) {
                if (err.name === 'AbortError') return;
            }
        }
        copyFolderShareLink();
    }

    function copyFolderShareLink() {
        const input = document.getElementById('folder-share-url-input');
        if (!input || !input.value) return;
        const btn = document.getElementById('btn-copy-folder-link');
        copyToClipboard(input.value, btn);
    }

    // 4. Move File Modal
    function openMoveFileModal(fileId, fileTitle, currentFolderId) {
        document.getElementById('move-file-form').action = `/drive/${fileId}/move`;
        document.getElementById('move-file-title').innerText = fileTitle;

        // Reset radio checked
        document.querySelectorAll('input[name="folder_id"]').forEach(r => r.checked = false);

        if (!currentFolderId) {
            const rootRadio = document.getElementById('move-folder-root');
            if (rootRadio) rootRadio.checked = true;
        } else {
            const folderRadio = document.getElementById(`move-folder-${currentFolderId}`);
            if (folderRadio) folderRadio.checked = true;
        }

        const modal = document.getElementById('move-file-modal');
        const backdrop = document.getElementById('move-file-backdrop');
        const panel = document.getElementById('move-file-panel');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
        });
    }

    function closeMoveFileModal() {
        const backdrop = document.getElementById('move-file-backdrop');
        const panel = document.getElementById('move-file-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('move-file-modal').classList.add('hidden');
        }, 300);
    }

    // 5. Delete Folder Confirmation
    function confirmDeleteFolder(id, name, count, url) {
        const modal = document.getElementById('delete-modal');
        const backdrop = document.getElementById('delete-backdrop');
        const panel = document.getElementById('delete-panel');
        const titleEl = document.getElementById('delete-modal-title');
        const descEl = document.getElementById('delete-modal-desc');
        const nameEl = document.getElementById('delete-target-name');
        const infoEl = document.getElementById('delete-target-info');
        const form = document.getElementById('delete-form');
        const btnText = document.getElementById('delete-btn-text');

        form.action = url;
        titleEl.innerText = `Hapus Folder "${name}"?`;
        descEl.innerText = `Folder ini beserta ${count} berkas di dalamnya akan dihapus secara permanen dari server. Tindakan ini tidak dapat dibatalkan.`;
        nameEl.innerText = `📁 ${name}`;
        infoEl.innerText = `${count} Berkas`;
        btnText.innerText = 'Ya, Hapus Folder & Isinya';

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    // 6. Batch Multi-Select Mode & Simultaneous Deletion
    let isMultiSelectMode = false;
    const selectedFiles = new Set();
    const selectedFolders = new Set();
    const itemNamesMap = new Map();

    function toggleMultiSelectMode() {
        if (isMultiSelectMode) {
            exitMultiSelectMode();
        } else {
            enterMultiSelectMode();
        }
    }

    function enterMultiSelectMode() {
        isMultiSelectMode = true;
        const btn = document.getElementById('btn-toggle-multi-select');
        const btnText = document.getElementById('btn-toggle-multi-select-text');
        if (btn) {
            btn.classList.remove('bg-slate-200/80', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-200');
            btn.classList.add('bg-teal-500', 'text-white', 'shadow-xs');
        }
        if (btnText) btnText.innerText = 'Selesai';

        // Show all checkbox containers
        document.querySelectorAll('.batch-select-checkbox-container').forEach(el => {
            el.classList.remove('hidden');
        });

        // Show sticky batch action bar
        const bar = document.getElementById('batch-action-bar');
        if (bar) {
            bar.classList.remove('hidden');
            bar.style.opacity = '0';
            bar.style.transform = 'scale(0.95)';
            requestAnimationFrame(() => {
                bar.style.opacity = '1';
                bar.style.transform = 'scale(1)';
            });
        }

        updateBatchActionBar();
    }

    function exitMultiSelectMode() {
        isMultiSelectMode = false;
        const btn = document.getElementById('btn-toggle-multi-select');
        const btnText = document.getElementById('btn-toggle-multi-select-text');
        if (btn) {
            btn.classList.remove('bg-teal-500', 'text-white', 'shadow-xs');
            btn.classList.add('bg-slate-200/80', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-200');
        }
        if (btnText) btnText.innerText = 'Pilih Banyak';

        // Uncheck all and hide checkboxes
        document.querySelectorAll('input[name="batch_files[]"], input[name="batch_folders[]"]').forEach(chk => {
            chk.checked = false;
        });

        selectedFiles.clear();
        selectedFolders.clear();
        itemNamesMap.clear();

        document.querySelectorAll('.batch-select-checkbox-container').forEach(el => {
            el.classList.add('hidden');
        });

        // Remove card active rings
        document.querySelectorAll('[id^="file-"], [id^="folder-card-"]').forEach(el => {
            el.classList.remove('ring-2', 'ring-teal-500', 'dark:ring-teal-400');
        });

        // Hide sticky batch action bar
        const bar = document.getElementById('batch-action-bar');
        if (bar) {
            bar.style.opacity = '0';
            bar.style.transform = 'scale(0.95)';
            setTimeout(() => {
                bar.classList.add('hidden');
                bar.style.opacity = '';
                bar.style.transform = '';
            }, 300);
        }
    }

    function handleItemSelect(checkbox) {
        const type = checkbox.getAttribute('data-type');
        const id = parseInt(checkbox.value, 10);
        const name = checkbox.getAttribute('data-name') || '';

        const cardId = type === 'folder' ? `folder-card-${id}` : `file-${id}`;
        const card = document.getElementById(cardId);

        if (checkbox.checked) {
            if (type === 'folder') {
                selectedFolders.add(id);
                itemNamesMap.set(`folder-${id}`, `📁 ${name}`);
            } else {
                selectedFiles.add(id);
                itemNamesMap.set(`file-${id}`, `📄 ${name}`);
            }
            if (card) card.classList.add('ring-2', 'ring-teal-500', 'dark:ring-teal-400');
        } else {
            if (type === 'folder') {
                selectedFolders.delete(id);
                itemNamesMap.delete(`folder-${id}`);
            } else {
                selectedFiles.delete(id);
                itemNamesMap.delete(`file-${id}`);
            }
            if (card) card.classList.remove('ring-2', 'ring-teal-500', 'dark:ring-teal-400');
        }

        updateBatchActionBar();
    }

    function selectAllBatchItems() {
        const fileCheckboxes = document.querySelectorAll('input[name="batch_files[]"]');
        const folderCheckboxes = document.querySelectorAll('input[name="batch_folders[]"]');
        const totalAvailable = fileCheckboxes.length + folderCheckboxes.length;
        const currentSelected = selectedFiles.size + selectedFolders.size;

        const shouldCheckAll = currentSelected < totalAvailable;

        fileCheckboxes.forEach(chk => {
            chk.checked = shouldCheckAll;
            handleItemSelect(chk);
        });

        folderCheckboxes.forEach(chk => {
            chk.checked = shouldCheckAll;
            handleItemSelect(chk);
        });
    }

    function updateBatchActionBar() {
        const total = selectedFiles.size + selectedFolders.size;
        const countBadge = document.getElementById('batch-selected-count');
        const deleteBtn = document.getElementById('btn-trigger-batch-delete');
        const selectAllBtn = document.getElementById('batch-select-all-btn');

        if (countBadge) countBadge.innerText = total;

        if (deleteBtn) {
            deleteBtn.disabled = total === 0;
        }

        const fileCheckboxes = document.querySelectorAll('input[name="batch_files[]"]');
        const folderCheckboxes = document.querySelectorAll('input[name="batch_folders[]"]');
        const totalAvailable = fileCheckboxes.length + folderCheckboxes.length;

        if (selectAllBtn) {
            selectAllBtn.innerText = (total > 0 && total >= totalAvailable) ? 'Batalkan Pilihan' : 'Pilih Semua';
        }
    }

    function openBatchDeleteModal() {
        const total = selectedFiles.size + selectedFolders.size;
        if (total === 0) return;

        const modal = document.getElementById('batch-delete-modal');
        const backdrop = document.getElementById('batch-delete-backdrop');
        const panel = document.getElementById('batch-delete-panel');
        const summaryText = document.getElementById('batch-delete-summary-text');
        const itemsList = document.getElementById('batch-delete-items-list');

        let summaryParts = [];
        if (selectedFolders.size > 0) summaryParts.push(`${selectedFolders.size} folder`);
        if (selectedFiles.size > 0) summaryParts.push(`${selectedFiles.size} berkas`);
        if (summaryText) summaryText.innerText = summaryParts.join(' & ') + ` (${total} total)`;

        if (itemsList) {
            itemsList.innerHTML = '';
            Array.from(itemNamesMap.values()).slice(0, 15).forEach(name => {
                const div = document.createElement('div');
                div.className = 'truncate py-0.5';
                div.innerText = name;
                itemsList.appendChild(div);
            });
            if (itemNamesMap.size > 15) {
                const moreDiv = document.createElement('div');
                moreDiv.className = 'italic text-[10px] text-slate-400 pt-0.5';
                moreDiv.innerText = `...dan ${itemNamesMap.size - 15} item lainnya`;
                itemsList.appendChild(moreDiv);
            }
        }

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function closeBatchDeleteModal() {
        const backdrop = document.getElementById('batch-delete-backdrop');
        const panel = document.getElementById('batch-delete-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            document.getElementById('batch-delete-modal').classList.add('hidden');
        }, 300);
    }

    async function executeBatchDelete() {
        const total = selectedFiles.size + selectedFolders.size;
        if (total === 0) return;

        const btnConfirm = document.getElementById('btn-confirm-batch-delete');
        const icon = document.getElementById('batch-delete-icon');
        const spinner = document.getElementById('batch-delete-spinner');
        const btnText = document.getElementById('batch-delete-btn-text');

        if (btnConfirm) btnConfirm.disabled = true;
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (btnText) btnText.innerText = 'Menghapus...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        try {
            const response = await fetch("{{ route('drive.batch.destroy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    file_ids: Array.from(selectedFiles),
                    folder_ids: Array.from(selectedFolders),
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Remove elements from DOM smoothly
                selectedFiles.forEach(id => {
                    const card = document.getElementById(`file-${id}`);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                });

                selectedFolders.forEach(id => {
                    const card = document.getElementById(`folder-card-${id}`);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                });

                closeBatchDeleteModal();
                exitMultiSelectMode();

                if (typeof showSwanToast === 'function') {
                    showSwanToast(result.message || 'Item terpilih berhasil dihapus.');
                }

                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alert(result.message || 'Gagal menghapus beberapa item terpilih.');
                if (btnConfirm) btnConfirm.disabled = false;
                if (icon) icon.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');
                if (btnText) btnText.innerText = 'Ya, Hapus Semua Terpilih';
            }
        } catch (err) {
            alert('Terjadi kesalahan jaringan saat menghapus item.');
            if (btnConfirm) btnConfirm.disabled = false;
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            if (btnText) btnText.innerText = 'Ya, Hapus Semua Terpilih';
        }
    }

    // Auto-focus and open preview if file_id or hash is in the URL (e.g. from Dashboard recent files widget)
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Show toast if just uploaded
        if (urlParams.has('uploaded')) {
            const count = parseInt(urlParams.get('uploaded') || '1', 10);
            const uploadedName = urlParams.get('uploaded_name') ? decodeURIComponent(urlParams.get('uploaded_name')) : null;
            const notifMsg = (count > 1 || !uploadedName)
                ? `🎉 ${count} berkas berhasil diunggah ke SwanDrive!`
                : `🎉 Berkas "${uploadedName}" berhasil diunggah ke SwanDrive!`;

            setTimeout(() => {
                if (typeof showSwanToast === 'function') {
                    showSwanToast(notifMsg, 'success', 5000);
                }
            }, 300);

            // Clean up URL
            const newUrl = window.location.pathname + window.location.search
                .replace(new RegExp('[?&]uploaded=[^&]+'), '')
                .replace(new RegExp('[?&]uploaded_name=[^&]+'), '')
                .replace(/^&/, '?').replace(/\?$/, '');
            window.history.replaceState({}, document.title, newUrl || window.location.pathname);
        }

        const targetFileId = urlParams.get('file_id') || (window.location.hash ? window.location.hash.replace('#file-', '') : null);
        if (targetFileId) {
            const targetCard = document.getElementById(`file-${targetFileId}`);
            if (targetCard) {
                setTimeout(() => {
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Apple iOS pulse spotlight animation
                    targetCard.classList.add('ring-4', 'ring-teal-500/80', 'dark:ring-teal-400/80', 'shadow-xl', 'shadow-teal-500/25', 'scale-[1.02]');
                    setTimeout(() => {
                        targetCard.classList.remove('ring-4', 'ring-teal-500/80', 'dark:ring-teal-400/80', 'shadow-xl', 'shadow-teal-500/25', 'scale-[1.02]');
                    }, 4000);

                    // Automatically open file preview modal
                    const previewTrigger = targetCard.querySelector('[onclick^="openPreviewModal"]');
                    if (previewTrigger) {
                        previewTrigger.click();
                    }
                }, 400);
            }
        }
    });
</script>
@endpush
