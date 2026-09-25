<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#020617" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="color-scheme" content="light dark">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Folder: {{ $folder->name }} - SwanDrive</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html:not(.dark) .theme-icon-dark { display: none !important; }
        html.dark .theme-icon-light { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>

    <!-- Instant Dark/Light Mode Sync Script -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('swanflow_theme');
                if (savedTheme === 'light') {
                    document.documentElement.classList.remove('dark');
                } else {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {
                document.documentElement.classList.add('dark');
            }
        })();
        if (/Android/i.test(navigator.userAgent)) {
            document.documentElement.classList.add('is-android');
        }
    </script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-full flex flex-col justify-between selection:bg-teal-500 selection:text-white antialiased transition-colors duration-200">

    <!-- Ambient Liquid Orbs Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[34rem] h-[34rem] bg-teal-400/20 dark:bg-teal-500/15 rounded-full blur-3xl animate-liquid-orb-1"></div>
        <div class="absolute -bottom-32 left-1/2 -translate-x-1/2 w-[30rem] h-[30rem] bg-emerald-400/20 dark:bg-emerald-500/15 rounded-full blur-3xl animate-liquid-orb-2"></div>
    </div>

    <!-- Top Navigation Header -->
    <header class="relative z-10 w-full max-w-4xl mx-auto px-4 pt-4 sm:pt-6 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-[14px] bg-gradient-to-tr from-teal-500 to-emerald-400 flex items-center justify-center text-white shadow-lg shadow-teal-500/25 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
            </div>
            <div>
                <span class="text-sm font-black text-slate-900 dark:text-white tracking-tight block leading-none">SwanDrive</span>
                <span class="text-[10px] font-bold text-teal-700 dark:text-teal-300/80">Folder Bersama</span>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-700 dark:text-emerald-400 text-[11px] font-bold backdrop-blur-md">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Folder Publik</span>
            </span>

            <!-- Theme Toggle Button -->
            <button type="button" 
                    id="theme-toggle-btn"
                    onclick="toggleSwanFlowTheme()" 
                    aria-label="Ganti Tema Gelap atau Terang" 
                    class="w-9 h-9 flex items-center justify-center rounded-[14px] bg-white/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-amber-300 border border-slate-200 dark:border-white/10 active:scale-95 transition-all shadow-xs ios-press cursor-pointer">
                <svg class="theme-icon-dark w-4.5 h-4.5 text-amber-300 transition-transform duration-300 transform rotate-0 hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="theme-icon-light w-4.5 h-4.5 text-slate-700 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="relative z-10 flex-1 p-4 sm:p-6 w-full max-w-4xl mx-auto space-y-6">
        @php
            $colorMeta = $folder->colorMeta();
        @endphp

        <!-- Folder Hero Card -->
        <div class="w-full rounded-[32px] p-6 sm:p-8 shadow-2xl space-y-6 bg-white/85 dark:bg-slate-900/85 backdrop-blur-2xl border border-slate-200/80 dark:border-white/20 relative overflow-hidden animate-swan-in transition-colors duration-200">
            <!-- Specular Top Rim -->
            <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/80 dark:via-white/50 to-transparent"></div>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                <!-- Folder Icon Box -->
                <div class="w-20 h-20 rounded-[26px] {{ $colorMeta['iconBg'] }} border {{ $colorMeta['border'] }} flex items-center justify-center shrink-0 shadow-2xl">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-1.5V9a3 3 0 00-3-3h-3.379a3 3 0 01-2.121-.879L8.379 4.04A3 3 0 006.257 3.16H4.5A3 3 0 001.5 6.16v11.84a3 3 0 003 3h15z" />
                    </svg>
                </div>

                <!-- Title & Meta -->
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/25 dark:border-teal-500/30 text-teal-700 dark:text-teal-400 text-xs font-black tracking-wide">
                        <span>Folder Terbuka</span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight break-words">
                        {{ $folder->name }}
                    </h1>

                    @if(!empty($folder->description))
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-medium leading-relaxed max-w-xl">
                            {{ $folder->description }}
                        </p>
                    @endif

                    <!-- Stats Badges -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1 text-xs font-bold text-teal-700 dark:text-teal-400/90">
                        <span class="bg-slate-100/90 dark:bg-slate-950/60 px-3 py-1 rounded-full border border-slate-200 dark:border-white/10 shadow-xs">
                            📁 {{ $files->count() }} Berkas
                        </span>
                        <span class="bg-slate-100/90 dark:bg-slate-950/60 px-3 py-1 rounded-full border border-slate-200 dark:border-white/10 shadow-xs">
                            💾 {{ $folder->formatted_size }} Total
                        </span>
                        <span class="bg-slate-100/90 dark:bg-slate-950/60 px-3 py-1 rounded-full border border-slate-200 dark:border-white/10 shadow-xs">
                            📅 {{ $folder->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Grid -->
            <div class="flex flex-wrap items-center gap-2.5 pt-2 border-t border-slate-200 dark:border-white/10">
                @if($files->isNotEmpty())
                    <a href="{{ route('drive.shared.folder.zip', ['token' => $folder->share_token]) }}"
                       class="py-3 px-5 rounded-[18px] bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 active:scale-95 text-white font-black text-xs shadow-lg shadow-teal-500/25 flex items-center justify-center gap-2 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Semua (.ZIP)</span>
                    </a>
                @endif

                <button type="button" onclick="copyCurrentUrl()"
                        class="py-3 px-4 rounded-[18px] bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-white/15 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849a2.25 2.25 0 00-3.332 0l-4.5 4.5a2.25 2.25 0 003.182 3.182l1.5-1.5m4.5-4.5l1.5-1.5a2.25 2.25 0 013.182 3.182l-4.5 4.5a2.25 2.25 0 01-3.182 0" />
                    </svg>
                    <span>Salin Tautan Folder</span>
                </button>

                <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Buka folder "' . $folder->name . '" (' . $files->count() . ' berkas) di SwanDrive: ' . url()->current()) }}"
                   target="_blank"
                   class="py-3 px-4 rounded-[18px] bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-600/30 dark:hover:bg-emerald-600/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.768 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                    </svg>
                    <span>WhatsApp</span>
                </a>

                <button type="button" onclick="toggleQrModal()"
                        class="py-3 px-4 rounded-[18px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white text-xs font-bold border border-slate-200 dark:border-white/10 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    </svg>
                    <span>QR Code</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center justify-between">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <input type="text" id="file-search" oninput="filterFiles()" placeholder="Cari nama berkas dalam folder ini..."
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-[20px] bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-white/20 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm backdrop-blur-xl">
                    <span class="absolute left-3 top-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                </div>

                <!-- Category Filters -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                    <button type="button" onclick="setCategoryFilter('all')" data-cat="all" class="cat-pill px-3 py-1.5 rounded-[14px] text-xs font-black bg-teal-500 text-white shadow-xs">
                        Semua
                    </button>
                    <button type="button" onclick="setCategoryFilter('document')" data-cat="document" class="cat-pill px-3 py-1.5 rounded-[14px] text-xs font-bold bg-white/90 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-white/10 shadow-xs">
                        Dokumen
                    </button>
                    <button type="button" onclick="setCategoryFilter('image')" data-cat="image" class="cat-pill px-3 py-1.5 rounded-[14px] text-xs font-bold bg-white/90 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-white/10 shadow-xs">
                        Gambar
                    </button>
                    <button type="button" onclick="setCategoryFilter('archive')" data-cat="archive" class="cat-pill px-3 py-1.5 rounded-[14px] text-xs font-bold bg-white/90 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-white/10 shadow-xs">
                        Arsip
                    </button>
                    <button type="button" onclick="setCategoryFilter('other')" data-cat="other" class="cat-pill px-3 py-1.5 rounded-[14px] text-xs font-bold bg-white/90 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-white/10 shadow-xs">
                        Lainnya
                    </button>
                </div>
            </div>
        </div>

        <!-- Files List Container -->
        <div class="space-y-3" id="files-container">
            @forelse($files as $file)
                @php
                    $meta = $file->categoryMeta();
                    $previewUrl = route('drive.shared.folder.preview', ['token' => $folder->share_token, 'file' => $file]);
                    $downloadUrl = route('drive.shared.folder.download', ['token' => $folder->share_token, 'file' => $file]);
                @endphp
                <div class="file-item rounded-[22px] bg-white/85 dark:bg-slate-900/75 border border-slate-200/80 dark:border-white/15 hover:border-teal-500/40 p-4 space-y-3 shadow-md backdrop-blur-xl transition-all"
                     data-name="{{ strtolower($file->title . ' ' . $file->original_name) }}"
                     data-category="{{ $file->category }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-[16px] {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }} flex items-center justify-center shrink-0 font-black text-xs uppercase shadow-xs">
                                {{ substr($file->extension, 0, 4) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate leading-tight" title="{{ $file->title }}">
                                    {{ $file->title }}
                                </h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5" title="{{ $file->original_name }}">
                                    {{ $file->original_name }}
                                </p>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    <span class="font-bold text-teal-600 dark:text-teal-400">{{ $file->formatted_size }}</span>
                                    <span>•</span>
                                    <span>{{ $file->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button"
                                    onclick="openFilePreviewModal('{{ addslashes($file->title) }}', '{{ $file->formatted_size }}', '{{ strtolower($file->extension) }}', '{{ $previewUrl }}', '{{ $downloadUrl }}', '{{ addslashes($file->notes ?? '') }}')"
                                    class="py-2 px-3 rounded-[14px] bg-teal-500 hover:bg-teal-400 active:scale-95 text-white text-xs font-black flex items-center gap-1 transition-all shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Lihat</span>
                            </button>

                            <a href="{{ $downloadUrl }}"
                               class="py-2 px-3 rounded-[14px] bg-slate-100 hover:bg-slate-200/80 dark:bg-white/10 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-xs font-bold border border-slate-200 dark:border-white/15 flex items-center gap-1 active:scale-95 transition-all">
                                <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <span>Unduh</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 px-4 rounded-[28px] bg-white/70 dark:bg-slate-900/60 border border-dashed border-slate-300 dark:border-slate-800 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-teal-500/10 text-teal-500 dark:text-teal-400 mx-auto flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Folder Ini Masih Kosong</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                            Belum ada berkas yang diunggah atau dibagikan di dalam folder ini.
                        </p>
                    </div>
                </div>
            @endforelse

            <div id="no-filter-results" class="hidden text-center py-10 px-4 rounded-[24px] bg-white/70 dark:bg-slate-900/60 border border-dashed border-slate-300 dark:border-slate-800 space-y-2">
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada berkas yang cocok dengan filter atau kata kunci Anda.</p>
                <button type="button" onclick="resetFilters()" class="text-xs text-teal-600 dark:text-teal-400 font-bold hover:underline">Reset Filter</button>
            </div>
        </div>
    </main>

    <!-- Modal File Preview (Within Folder) -->
    <div id="folder-preview-modal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div onclick="closeFilePreviewModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-[32px] p-6 shadow-2xl border border-slate-200 dark:border-white/20 pointer-events-auto space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar animate-swan-in text-slate-900 dark:text-white">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-white/10">
                    <div class="min-w-0 pr-2">
                        <h3 id="pv-title" class="text-sm font-black truncate leading-tight text-slate-900 dark:text-white">Nama Berkas</h3>
                        <span id="pv-size" class="text-[11px] text-teal-600 dark:text-teal-400 font-bold">0 KB</span>
                    </div>
                    <button type="button" onclick="closeFilePreviewModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center justify-center shrink-0 font-bold text-xs transition-colors cursor-pointer">✕</button>
                </div>

                <!-- Preview Display Container -->
                <div id="pv-container" class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 min-h-[160px] flex items-center justify-center p-2">
                    <!-- Dynamic Preview Injected via JS -->
                </div>

                <div id="pv-notes" class="hidden text-xs text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-950/80 p-3 rounded-xl border border-slate-200 dark:border-slate-800 italic">
                </div>

                <!-- Action Download & Close -->
                <div class="flex items-center gap-2 pt-2">
                    <a id="pv-download-link" href="#" class="flex-1 py-3 px-4 rounded-[18px] bg-teal-500 hover:bg-teal-400 text-white text-xs font-black shadow-lg shadow-teal-500/25 flex items-center justify-center gap-2 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Berkas</span>
                    </a>
                    <a id="pv-external-link" href="#" target="_blank" class="py-3 px-3.5 rounded-[18px] bg-slate-100 hover:bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-xs font-bold border border-slate-200 dark:border-white/15 flex items-center justify-center">
                        Tab Baru ↗
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div id="qr-modal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div onclick="toggleQrModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-xs bg-white dark:bg-slate-900 rounded-[28px] p-6 text-center shadow-2xl border border-slate-200 dark:border-white/20 pointer-events-auto space-y-4 animate-swan-in">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">QR Code Folder</h3>
                    <button type="button" onclick="toggleQrModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center justify-center font-bold text-xs transition-colors cursor-pointer">✕</button>
                </div>
                <div class="bg-slate-50 dark:bg-white p-3 rounded-2xl mx-auto w-48 h-48 flex items-center justify-center border border-slate-200 dark:border-transparent shadow-inner">
                    <img id="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url()->current()) }}" alt="QR Code" class="w-full h-full object-contain">
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Arahkan kamera ponsel Anda ke QR code ini untuk membuka seluruh isi folder ini langsung di smartphone.
                </p>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-full bg-slate-900/90 dark:bg-slate-800/95 text-white text-xs font-bold border border-slate-700 dark:border-white/20 shadow-2xl backdrop-blur-xl opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
        <span id="toast-message">Tautan berhasil disalin!</span>
    </div>

    <!-- Minimalist Footer -->
    <footer class="relative z-10 text-center py-4 text-[11px] text-slate-500 dark:text-slate-400">
        SwanDrive &copy; {{ date('Y') }} &bull; Dibuat oleh <strong class="font-bold text-slate-700 dark:text-slate-300">Gusti Swandana</strong>
    </footer>

    <script>
        let currentCategory = 'all';

        function toggleSwanFlowTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            const themeMeta = document.querySelector('meta[name="theme-color"]');
            localStorage.setItem('swanflow_theme', isDark ? 'dark' : 'light');
            if (themeMeta) {
                themeMeta.setAttribute('content', isDark ? '#020617' : '#ffffff');
            }
        }

        function filterFiles() {
            const query = document.getElementById('file-search').value.toLowerCase().trim();
            const items = document.querySelectorAll('.file-item');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const cat = item.getAttribute('data-category');

                const matchesQuery = name.includes(query);
                const matchesCat = currentCategory === 'all' || cat === currentCategory;

                if (matchesQuery && matchesCat) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            const noResults = document.getElementById('no-filter-results');
            if (items.length > 0 && visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        function setCategoryFilter(cat) {
            currentCategory = cat;
            document.querySelectorAll('.cat-pill').forEach(btn => {
                if (btn.getAttribute('data-cat') === cat) {
                    btn.className = 'cat-pill px-3 py-1.5 rounded-[14px] text-xs font-black bg-teal-500 text-white shadow-xs';
                } else {
                    btn.className = 'cat-pill px-3 py-1.5 rounded-[14px] text-xs font-bold bg-white/90 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-white/10 shadow-xs';
                }
            });
            filterFiles();
        }

        function resetFilters() {
            document.getElementById('file-search').value = '';
            setCategoryFilter('all');
        }

        function openFilePreviewModal(title, size, ext, previewUrl, downloadUrl, notes) {
            document.getElementById('pv-title').innerText = title;
            document.getElementById('pv-size').innerText = size;
            document.getElementById('pv-download-link').href = downloadUrl;
            document.getElementById('pv-external-link').href = previewUrl;

            const container = document.getElementById('pv-container');
            const notesEl = document.getElementById('pv-notes');

            if (notes && notes.trim() !== '') {
                notesEl.innerText = `"${notes}"`;
                notesEl.classList.remove('hidden');
            } else {
                notesEl.classList.add('hidden');
            }

            container.innerHTML = '';
            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
            const audioExts = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
            const videoExts = ['mp4', 'webm', 'mov'];

            if (imageExts.includes(ext)) {
                container.innerHTML = `<img src="${previewUrl}" alt="${title}" class="max-h-72 max-w-full rounded-xl object-contain">`;
            } else if (audioExts.includes(ext)) {
                container.innerHTML = `<div class="w-full p-3"><audio controls class="w-full rounded-xl" src="${previewUrl}"></audio></div>`;
            } else if (videoExts.includes(ext)) {
                container.innerHTML = `<video controls playsinline class="max-h-72 w-full object-contain" src="${previewUrl}"></video>`;
            } else if (ext === 'pdf') {
                container.innerHTML = `<iframe src="${previewUrl}#toolbar=0" class="w-full h-72 rounded-xl" title="PDF"></iframe>`;
            } else {
                container.innerHTML = `<div class="p-6 text-center space-y-2"><div class="text-3xl uppercase font-black text-teal-400">${ext}</div><p class="text-xs text-slate-400">Pratinjau langsung tidak tersedia untuk format berkas ini. Klik unduh untuk membuka.</p></div>`;
            }

            document.getElementById('folder-preview-modal').classList.remove('hidden');
        }

        function closeFilePreviewModal() {
            document.getElementById('folder-preview-modal').classList.add('hidden');
            document.getElementById('pv-container').innerHTML = '';
        }

        function copyCurrentUrl() {
            const url = window.location.href;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => showToast('Tautan folder berhasil disalin!'));
            } else {
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Tautan folder berhasil disalin!');
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-message').innerText = msg;
            toast.classList.remove('opacity-0', 'pointer-events-none');
            toast.classList.add('opacity-100');
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0', 'pointer-events-none');
            }, 2500);
        }

        function toggleQrModal() {
            const modal = document.getElementById('qr-modal');
            modal.classList.toggle('hidden');
        }
    </script>
</body>
</html>
