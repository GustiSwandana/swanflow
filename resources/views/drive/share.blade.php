<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#020617" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#f8fafc" media="(prefers-color-scheme: light)">
    <meta name="color-scheme" content="light dark">
    <meta name="mobile-web-app-capable" content="yes">
    <title>{{ $file->title }} - SwanDrive</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
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
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-dvh flex flex-col justify-between selection:bg-teal-500 selection:text-white antialiased transition-colors duration-200">

    <!-- Dynamic Island & iOS Status Bar Scrim -->
    <div id="status-bar-scrim" class="fixed top-0 left-0 right-0 z-40 pointer-events-none transition-opacity duration-200 opacity-0 bg-slate-100/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-black/5 dark:border-white/5" style="height: env(safe-area-inset-top, 0px);"></div>

    <!-- Ambient Liquid Orbs Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute top-12 left-1/2 -translate-x-1/2 w-[36rem] h-[36rem] bg-teal-400/15 dark:bg-teal-500/10 rounded-full blur-3xl animate-liquid-orb-1 transform-gpu"></div>
        <div class="absolute -bottom-24 left-1/2 -translate-x-1/2 w-[32rem] h-[32rem] bg-emerald-400/15 dark:bg-emerald-500/10 rounded-full blur-3xl animate-liquid-orb-2 transform-gpu"></div>
    </div>

    <!-- Top Navigation Header (Dynamic Island & iPhone Safe Area Aware) -->
    <header class="relative z-20 w-full max-w-xl mx-auto px-4 sm:px-6 pt-[calc(env(safe-area-inset-top,0px)+16px)] pb-3 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 flex items-center justify-center text-white shadow-lg shadow-teal-500/30 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
            </div>
            <div>
                <span class="text-sm sm:text-base font-black text-slate-900 dark:text-white tracking-tight block leading-tight">SwanDrive</span>
                <span class="text-[11px] font-bold text-teal-600 dark:text-teal-400">SwanDrive File Transfer</span>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/25 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold backdrop-blur-md shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                <span>Tautan Terverifikasi</span>
            </span>

            <!-- Share Button (Native Share Sheet / Copy) -->
            <button type="button" 
                    id="header-share-btn"
                    onclick="sharePageNative()" 
                    aria-label="Bagikan Halaman Ini" 
                    title="Bagikan Tautan Berkas"
                    class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white/90 dark:bg-slate-800/90 hover:bg-white dark:hover:bg-slate-700 text-teal-600 dark:text-teal-400 border border-slate-200 dark:border-white/10 active:scale-95 transition-all shadow-xs cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                </svg>
            </button>

            <!-- Theme Toggle Button -->
            <button type="button" 
                    id="theme-toggle-btn"
                    onclick="toggleSwanFlowTheme()" 
                    aria-label="Ganti Tema Gelap atau Terang" 
                    class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white/90 dark:bg-slate-800/90 hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-amber-300 border border-slate-200 dark:border-white/10 active:scale-95 transition-all shadow-xs cursor-pointer">
                <svg class="theme-icon-dark w-5 h-5 text-amber-300 transition-transform duration-300 transform rotate-0 hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="theme-icon-light w-5 h-5 text-slate-700 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </div>
    </header>

    @php
        $meta = $file->categoryMeta();
        $cleanExt = strtolower($file->extension);
        $isImage = in_array($cleanExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'heic']);
        $isAudio = in_array($cleanExt, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac']);
        $isVideo = in_array($cleanExt, ['mp4', 'webm', 'ogg', 'mov', 'm4v']);
        $isPdf = $cleanExt === 'pdf';

        $cleanTitle = trim($file->title);
        $cleanOrig = trim($file->original_name);
        $nameWithoutExt = pathinfo($cleanOrig, PATHINFO_FILENAME);
        $isTitleSameAsName = (strcasecmp($cleanTitle, $cleanOrig) === 0 || strcasecmp($cleanTitle, $nameWithoutExt) === 0);
    @endphp

    <!-- Main Content Container -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center p-4 sm:p-6 w-full max-w-xl mx-auto">
        <div class="w-full rounded-3xl p-5 sm:p-7 shadow-2xl space-y-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur-2xl border border-slate-200/80 dark:border-white/15 relative overflow-hidden transition-colors duration-200">
            <!-- Specular Top Rim -->
            <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-teal-400/50 dark:via-white/40 to-transparent"></div>

            <!-- Header Info: Status & Badges -->
            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-white/5 pb-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/25 dark:border-teal-500/30 text-teal-700 dark:text-teal-400 text-xs font-black tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-teal-500 dark:bg-teal-400 animate-pulse"></span>
                    <span>Berkas Siap Diunduh</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span class="px-2 py-0.5 rounded-md bg-teal-500/10 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 uppercase font-black tracking-wider text-[10px] border border-teal-500/25">
                        {{ strtoupper($file->extension) }}
                    </span>
                    <span>{{ $file->formatted_size }}</span>
                </div>
            </div>

            <!-- FILE TITLE & METADATA (Prominently placed at the top of the card) -->
            <div class="space-y-1.5 text-center sm:text-left">
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight leading-snug break-words">
                    {{ $cleanTitle }}
                </h1>
                
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    @if(! $isTitleSameAsName)
                        <span class="truncate max-w-[200px]" title="{{ $file->original_name }}">
                            {{ $file->original_name }}
                        </span>
                        <span>•</span>
                    @endif
                    <span>Diunggah {{ $file->created_at->format('d M Y') }}</span>
                    @if($file->download_count > 0)
                        <span>•</span>
                        <span class="text-teal-600 dark:text-teal-400 font-bold">{{ $file->download_count }}x diunduh</span>
                    @endif
                </div>
            </div>

            <!-- MEDIA PREVIEW SECTION (Clean, Unobstructed, Responsive) -->
            @if($isImage)
                <!-- Clean Image Hero Preview -->
                <div class="relative rounded-2xl overflow-hidden bg-slate-950/90 dark:bg-black/90 border border-slate-200 dark:border-white/10 shadow-inner group cursor-pointer" onclick="openLightbox()">
                    <!-- Subtle ambient blurred backdrop -->
                    <div class="absolute inset-0 bg-cover bg-center filter blur-xl opacity-25 scale-110 pointer-events-none" style="background-image: url('{{ route('drive.shared.preview', ['token' => $file->share_token]) }}');"></div>
                    
                    <div class="relative z-10 w-full flex items-center justify-center p-2 min-h-[180px] max-h-[280px] sm:max-h-[320px]">
                        <img src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" 
                             alt="{{ $file->title }}" 
                             loading="lazy"
                             class="max-h-[260px] sm:max-h-[300px] w-auto max-w-full rounded-xl object-contain shadow-xl transition-transform duration-300 group-hover:scale-[1.01]">
                    </div>

                    <!-- Clean Zoom/Expand Pill in Bottom Corner -->
                    <button type="button" 
                            onclick="event.stopPropagation(); openLightbox()"
                            class="absolute bottom-2.5 right-2.5 z-20 px-2.5 py-1 rounded-xl bg-slate-900/80 hover:bg-slate-900 text-white text-[11px] font-bold border border-white/20 backdrop-blur-md shadow-md flex items-center gap-1.5 transition-transform active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                        <span>Perbesar</span>
                    </button>
                </div>

            @elseif($isVideo)
                <!-- Video Player -->
                <div class="rounded-2xl overflow-hidden border border-slate-200/80 dark:border-white/10 bg-black shadow-lg relative">
                    <video controls playsinline class="max-h-[300px] w-full object-contain mx-auto" src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                </div>

            @elseif($isAudio)
                <!-- Audio Player -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-teal-500/10 via-emerald-500/5 to-slate-100 dark:to-slate-950 border border-teal-500/20 shadow-inner space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 flex items-center justify-center text-white shadow-md">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a.25.25 0 00.179-.24V7.5M9 13.5v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A.25.25 0 009 15.424V9" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400 block">Pemutar Audio SwanDrive</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white truncate max-w-xs">{{ $file->title }}</span>
                        </div>
                    </div>
                    <audio controls class="w-full rounded-xl" src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}">
                        Browser Anda tidak mendukung audio player.
                    </audio>
                </div>

            @elseif($isPdf)
                <!-- PDF Viewer -->
                <div class="rounded-2xl overflow-hidden border border-slate-200/80 dark:border-white/10 bg-slate-900 h-72 shadow-lg relative group">
                    <iframe src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}#toolbar=0" class="w-full h-full" title="PDF Preview"></iframe>
                    <a href="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" target="_blank" class="absolute top-2.5 right-2.5 px-3 py-1.5 rounded-xl bg-slate-900/90 hover:bg-slate-950 text-white text-xs font-bold border border-white/20 backdrop-blur-md flex items-center gap-1.5 shadow-lg transition-transform active:scale-95">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        <span>Layar Penuh</span>
                    </a>
                </div>

            @else
                <!-- Document / Archive Icon -->
                <div class="py-4 flex flex-col items-center justify-center text-center">
                    <div class="relative w-20 h-20 rounded-2xl {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }} flex flex-col items-center justify-center shadow-lg mb-1">
                        <svg class="w-8 h-8 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-wider">{{ strtoupper(substr($file->extension, 0, 4)) }}</span>
                    </div>
                </div>
            @endif

            <!-- Notes Section if Present -->
            @if(!empty($file->notes))
                <div class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/80 p-3 rounded-2xl border border-slate-200 dark:border-white/10 text-left relative overflow-hidden">
                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">
                        <svg class="w-3.5 h-3.5 text-teal-500 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <span>Pesan dari Pengunggah:</span>
                    </div>
                    <p class="italic text-slate-800 dark:text-slate-200 pl-2 border-l-2 border-teal-500/50">
                        "{{ $file->notes }}"
                    </p>
                </div>
            @endif

            <!-- ACTION BUTTONS HUB -->
            <div class="space-y-2.5 pt-1">
                <!-- Primary Download Button -->
                <a href="{{ route('drive.shared.download', ['token' => $file->share_token]) }}"
                   class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white font-black text-sm sm:text-base shadow-xl shadow-teal-500/25 hover:shadow-teal-500/35 flex items-center justify-center gap-2.5 border border-white/20 transition-all cursor-pointer">
                    <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Berkas ({{ $file->formatted_size }})</span>
                </a>

                <!-- Native Share Sheet CTA -->
                <button type="button" 
                        onclick="sharePageNative()"
                        id="btn-native-share-cta"
                        class="w-full py-3 px-4 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 dark:bg-teal-500/15 dark:hover:bg-teal-500/25 text-teal-700 dark:text-teal-300 border border-teal-500/30 text-xs sm:text-sm font-black flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    <span>Bagikan Tautan Berkas (Share Sheet)</span>
                </button>

                <!-- Secondary Actions Grid -->
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            onclick="copyCurrentUrl()"
                            id="btn-copy-link"
                            class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800/90 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-white/10 flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer shadow-xs">
                        <svg id="copy-icon" class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849a2.25 2.25 0 00-3.332 0l-4.5 4.5a2.25 2.25 0 003.182 3.182l1.5-1.5m4.5-4.5l1.5-1.5a2.25 2.25 0 013.182 3.182l-4.5 4.5a2.25 2.25 0 01-3.182 0" />
                        </svg>
                        <span id="copy-text">Salin Tautan</span>
                    </button>

                    <a href="https://wa.me/?text={{ urlencode('Halo! Unduh berkas "' . $file->title . '" (' . $file->formatted_size . ') di SwanDrive: ' . url()->current()) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.768 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>

                <!-- QR Code Toggle Button -->
                <button type="button" 
                        onclick="toggleQrModal()"
                        class="w-full py-2 px-3 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    </svg>
                    <span>Pindai QR Code untuk Unduh di Ponsel</span>
                </button>
            </div>

            <!-- Trust Badge Footer -->
            <div class="pt-2 border-t border-slate-200/80 dark:border-white/10 text-center">
                <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>File aman, diakses langsung dari server pribadi SwanDrive.</span>
                </p>
            </div>
        </div>
    </main>

    <!-- FULLSCREEN IMAGE LIGHTBOX MODAL -->
    @if($isImage)
        <div id="lightbox-modal" class="fixed inset-0 z-50 hidden transition-all duration-300">
            <!-- Dark Backdrop -->
            <div onclick="closeLightbox()" class="fixed inset-0 bg-black/90 backdrop-blur-md"></div>
            
            <div class="fixed inset-0 flex flex-col justify-between p-4 sm:p-6 pointer-events-none z-10">
                <!-- Lightbox Top Bar -->
                <div class="w-full max-w-4xl mx-auto flex items-center justify-between pointer-events-auto">
                    <div class="text-white text-xs font-bold truncate max-w-xs sm:max-w-md drop-shadow">
                        {{ $file->title }}
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('drive.shared.download', ['token' => $file->share_token]) }}"
                           class="px-3.5 py-1.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-white text-xs font-bold flex items-center gap-1.5 shadow-lg transition-transform active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>Unduh</span>
                        </a>
                        <button type="button" 
                                onclick="closeLightbox()" 
                                class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center font-bold text-sm transition-colors cursor-pointer">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Lightbox Center Image -->
                <div class="flex-1 flex items-center justify-center my-2 pointer-events-auto" onclick="closeLightbox()">
                    <img src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" 
                         alt="{{ $file->title }}" 
                         onclick="event.stopPropagation()"
                         class="max-h-[82vh] max-w-[92vw] sm:max-w-[85vw] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
                </div>

                <!-- Lightbox Bottom Caption -->
                <div class="w-full text-center text-white/70 text-[11px] font-medium pointer-events-auto">
                    Klik di luar gambar atau tekan <kbd class="px-1.5 py-0.5 rounded bg-white/20 text-white text-[10px]">Esc</kbd> untuk menutup
                </div>
            </div>
        </div>
    @endif

    <!-- QR CODE MODAL -->
    <div id="qr-modal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div onclick="toggleQrModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-xs bg-white dark:bg-slate-900 rounded-3xl p-6 text-center shadow-2xl border border-slate-200 dark:border-white/20 pointer-events-auto space-y-4 animate-swan-in">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">QR Code Berkas</h3>
                    <button type="button" onclick="toggleQrModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center justify-center font-bold text-xs transition-colors cursor-pointer">✕</button>
                </div>
                <div class="bg-white p-3 rounded-2xl mx-auto w-48 h-48 flex items-center justify-center border border-slate-200 dark:border-slate-800 shadow-inner">
                    <img id="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url()->current()) }}" alt="QR Code" class="w-full h-full object-contain">
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Arahkan kamera ponsel Anda ke QR code ini untuk membuka halaman unduhan langsung di smartphone.
                </p>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-full bg-slate-900/95 dark:bg-slate-800/95 text-white text-xs font-bold border border-slate-700 dark:border-white/20 shadow-2xl backdrop-blur-xl opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
        <span id="toast-message">Tautan berhasil disalin!</span>
    </div>

    <!-- Minimalist Footer (iOS Safe Area Aware) -->
    <footer class="relative z-10 text-center py-4 pb-[calc(env(safe-area-inset-bottom,0px)+16px)] text-[11px] text-slate-500 dark:text-slate-400">
        SwanDrive &copy; {{ date('Y') }} &bull; Dibuat oleh <strong class="font-bold text-slate-700 dark:text-slate-300">Gusti Swandana</strong>
    </footer>

    <script>
        function toggleSwanFlowTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            const themeMeta = document.querySelector('meta[name="theme-color"]');
            localStorage.setItem('swanflow_theme', isDark ? 'dark' : 'light');
            if (themeMeta) {
                themeMeta.setAttribute('content', isDark ? '#020617' : '#f8fafc');
            }
        }

        async function sharePageNative() {
            const url = window.location.href;
            const title = @json($file->title);
            const size = @json($file->formatted_size);

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: title,
                        text: `Unduh berkas "${title}" (${size}) di SwanDrive:`,
                        url: url
                    });
                    return;
                } catch (err) {
                    if (err.name === 'AbortError') return;
                }
            }
            copyCurrentUrl();
        }

        async function copyCurrentUrl() {
            const url = window.location.href;
            const btnText = document.getElementById('copy-text');

            const handleSuccess = () => {
                showToast('Tautan berhasil disalin ke clipboard!');
                if (btnText) btnText.innerText = 'Tersalin!';
                setTimeout(() => {
                    if (btnText) btnText.innerText = 'Salin Tautan';
                }, 2000);
            };

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(url);
                    handleSuccess();
                    return;
                }
            } catch (e) {
                // fallback below
            }

            try {
                const textarea = document.createElement('textarea');
                textarea.value = url;
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

            prompt('Salin link ini:', url);
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

        function openLightbox() {
            const modal = document.getElementById('lightbox-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeLightbox() {
            const modal = document.getElementById('lightbox-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
                const qrModal = document.getElementById('qr-modal');
                if (qrModal && !qrModal.classList.contains('hidden')) {
                    qrModal.classList.add('hidden');
                }
            }
        });

        // Dynamic Island status bar scrim on scroll
        window.addEventListener('scroll', () => {
            const scrim = document.getElementById('status-bar-scrim');
            if (scrim) {
                if (window.scrollY > 15) {
                    scrim.classList.remove('opacity-0');
                    scrim.classList.add('opacity-100');
                } else {
                    scrim.classList.remove('opacity-100');
                    scrim.classList.add('opacity-0');
                }
            }
        }, { passive: true });
    </script>
</body>
</html>
