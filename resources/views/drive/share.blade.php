<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#020617" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="color-scheme" content="light dark">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Unduh: {{ $file->title }} - SwanDrive</title>
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
                <span class="text-[10px] font-bold text-teal-700 dark:text-teal-300/80">SwanDrive File Transfer</span>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-700 dark:text-emerald-400 text-[11px] font-bold backdrop-blur-md">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Tautan Terverifikasi</span>
            </span>

            <!-- Theme Toggle Button (Dark / Light Mode) -->
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
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center p-4 sm:p-6 w-full max-w-lg mx-auto">
        <div class="w-full rounded-[32px] p-6 sm:p-8 shadow-2xl space-y-6 bg-white/85 dark:bg-slate-900/85 backdrop-blur-2xl border border-slate-200/80 dark:border-white/20 relative overflow-hidden animate-swan-in transition-colors duration-200">
            <!-- Specular Top Rim -->
            <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/80 dark:via-white/50 to-transparent"></div>
            
            @php
                $meta = $file->categoryMeta();
                $cleanExt = strtolower($file->extension);
                $isImage = in_array($cleanExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico']);
                $isAudio = in_array($cleanExt, ['mp3', 'wav', 'ogg', 'm4a', 'aac']);
                $isVideo = in_array($cleanExt, ['mp4', 'webm', 'ogg', 'mov']);
                $isPdf = $cleanExt === 'pdf';
            @endphp

            <!-- Category Header Badge -->
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-teal-500/10 dark:bg-teal-500/15 border border-teal-500/25 dark:border-teal-500/30 text-teal-700 dark:text-teal-400 text-xs font-black tracking-wide backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-teal-500 dark:bg-teal-400 animate-pulse"></span>
                    <span>Berkas Siap Diunduh</span>
                </div>
            </div>

            <!-- File Icon & Details -->
            <div class="flex flex-col items-center text-center space-y-3">
                <div class="relative w-20 h-20 rounded-2xl {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }} flex items-center justify-center shadow-xl">
                    <span class="text-2xl font-black uppercase">{{ substr($file->extension, 0, 4) }}</span>
                    <span class="absolute -bottom-2 -right-2 px-2 py-0.5 rounded-md bg-white dark:bg-slate-950 text-slate-700 dark:text-slate-300 text-[10px] font-bold border border-slate-200 dark:border-slate-800 shadow-sm">
                        {{ strtoupper($file->extension) }}
                    </span>
                </div>

                <div class="space-y-1 max-w-sm">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white leading-snug break-words" title="{{ $file->title }}">
                        {{ $file->title }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs mx-auto" title="{{ $file->original_name }}">
                        {{ $file->original_name }}
                    </p>
                </div>

                <!-- Parameters Badge Bar -->
                <div class="flex items-center justify-center gap-2 text-xs font-bold text-teal-700 dark:text-teal-400/90 bg-slate-100/90 dark:bg-slate-950/60 px-4 py-1.5 rounded-full border border-slate-200 dark:border-white/10 shadow-inner">
                    <span>{{ $file->formatted_size }}</span>
                    <span>•</span>
                    <span>{{ $file->created_at->format('d M Y') }}</span>
                    @if($file->download_count > 0)
                        <span>•</span>
                        <span class="text-slate-600 dark:text-slate-300">{{ $file->download_count }}x diunduh</span>
                    @endif
                </div>
            </div>

            <!-- Notes Section if Present -->
            @if(!empty($file->notes))
                <div class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/70 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 text-left relative overflow-hidden">
                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">
                        <svg class="w-3.5 h-3.5 text-teal-500 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <span>Pesan dari Pengunggah:</span>
                    </div>
                    <p class="italic text-slate-800 dark:text-slate-200 pl-1 border-l-2 border-teal-500/50">
                        "{{ $file->notes }}"
                    </p>
                </div>
            @endif

            <!-- Preview Card -->
            @if($isImage)
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 max-h-72 flex items-center justify-center p-1.5 shadow-inner group relative">
                    <img src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" alt="{{ $file->title }}" class="max-h-64 max-w-full rounded-xl object-contain">
                    <a href="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" target="_blank" class="absolute bottom-3 right-3 px-3 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-900 text-white text-[11px] font-bold border border-white/20 backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 shadow-lg">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                        <span>Perbesar</span>
                    </a>
                </div>
            @elseif($isAudio)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-inner space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold text-teal-600 dark:text-teal-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a.25.25 0 00.179-.24V7.5M9 13.5v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A.25.25 0 009 15.424V9" />
                            </svg>
                            <span>Audio Player</span>
                        </span>
                        <span class="font-semibold">{{ strtoupper($file->extension) }}</span>
                    </div>
                    <audio controls class="w-full rounded-xl" src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}">
                        Browser Anda tidak mendukung audio player.
                    </audio>
                </div>
            @elseif($isVideo)
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-900 dark:bg-black max-h-72 flex items-center justify-center shadow-inner">
                    <video controls playsinline class="max-h-68 w-full object-contain" src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                </div>
            @elseif($isPdf)
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white h-72 shadow-inner relative group">
                    <iframe src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}#toolbar=0" class="w-full h-full" title="PDF Preview"></iframe>
                    <a href="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" target="_blank" class="absolute top-3 right-3 px-3 py-1.5 rounded-xl bg-slate-900/90 hover:bg-slate-950 text-white text-[11px] font-bold border border-white/20 backdrop-blur-md flex items-center gap-1 shadow-lg transition-transform active:scale-95">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        <span>Buka Layar Penuh</span>
                    </a>
                </div>
            @endif

            <!-- Action Buttons Hub -->
            <div class="space-y-3 pt-2">
                <!-- Primary Download Button -->
                <a href="{{ route('drive.shared.download', ['token' => $file->share_token]) }}"
                   class="w-full py-4 px-6 rounded-[22px] bg-gradient-to-r from-teal-500 via-teal-400 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white font-black text-sm shadow-xl shadow-teal-500/25 flex items-center justify-center gap-2.5 border border-white/25 ios-press transition-all cursor-pointer">
                    <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Berkas ({{ $file->formatted_size }})</span>
                </a>

                <!-- Secondary Actions Grid -->
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="copyCurrentUrl()"
                            id="btn-copy-link"
                            class="py-2.5 px-3 rounded-[18px] bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800/80 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-white/10 flex items-center justify-center gap-1.5 ios-press transition-all cursor-pointer shadow-xs">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849a2.25 2.25 0 00-3.332 0l-4.5 4.5a2.25 2.25 0 003.182 3.182l1.5-1.5m4.5-4.5l1.5-1.5a2.25 2.25 0 013.182 3.182l-4.5 4.5a2.25 2.25 0 01-3.182 0" />
                        </svg>
                        <span id="copy-text">Salin Tautan</span>
                    </button>

                    <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Unduh berkas "' . $file->title . '" (' . $file->formatted_size . ') di SwanDrive: ' . url()->current()) }}"
                       target="_blank"
                       class="py-2.5 px-3 rounded-[18px] bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-600/30 dark:hover:bg-emerald-600/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center gap-1.5 ios-press transition-all cursor-pointer shadow-xs">
                        <svg class="w-4 h-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.284l-.707 2.582 2.658-.697c1.002.581 1.777.832 2.792.832 3.182 0 5.767-2.587 5.768-5.766 0-3.182-2.586-5.768-5.768-5.768zm3.364 8.169c-.145.408-.847.784-1.173.834-.325.051-.735.083-2.164-.509-1.428-.592-2.339-2.029-2.41-2.124-.071-.095-.572-.761-.572-1.451 0-.691.362-1.03.491-1.173.129-.143.282-.179.376-.179.094 0 .188.001.27.006.088.005.206-.033.322.247.123.298.421 1.027.458 1.102.037.075.061.163.012.261-.049.098-.073.159-.146.244-.073.085-.154.19-.22.256-.073.073-.149.153-.064.299.085.146.377.621.808 1.005.556.495 1.025.648 1.171.721.146.073.232.061.318-.037.086-.098.368-.428.466-.575.098-.147.196-.123.328-.074.132.049.837.395.981.467.144.072.24.108.276.17.036.062.036.357-.109.765z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>

                <!-- QR Code Toggle Button -->
                <button type="button" onclick="toggleQrModal()"
                        class="w-full py-2 px-3 rounded-[16px] text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 text-[11px] font-semibold flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    </svg>
                    <span>Pindai QR Code untuk Unduh di Ponsel</span>
                </button>
            </div>

            <!-- Trust Badge Footer -->
            <div class="pt-2 border-t border-slate-200 dark:border-white/10 text-center space-y-1">
                <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>File aman, diakses langsung dari server pribadi SwanDrive.</span>
                </p>
            </div>
        </div>
    </main>

    <!-- Modal QR Code -->
    <div id="qr-modal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div onclick="toggleQrModal()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="w-full max-w-xs bg-white dark:bg-slate-900 rounded-[28px] p-6 text-center shadow-2xl border border-slate-200 dark:border-white/20 pointer-events-auto space-y-4 animate-swan-in">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">QR Code Berkas</h3>
                    <button type="button" onclick="toggleQrModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center justify-center font-bold text-xs transition-colors cursor-pointer">✕</button>
                </div>
                <div class="bg-slate-50 dark:bg-white p-3 rounded-2xl mx-auto w-48 h-48 flex items-center justify-center border border-slate-200 dark:border-transparent shadow-inner">
                    <img id="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url()->current()) }}" alt="QR Code" class="w-full h-full object-contain">
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Arahkan kamera ponsel Anda ke QR code ini untuk membuka halaman unduhan langsung di smartphone.
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
        function toggleSwanFlowTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            const themeMeta = document.querySelector('meta[name="theme-color"]');
            localStorage.setItem('swanflow_theme', isDark ? 'dark' : 'light');
            if (themeMeta) {
                themeMeta.setAttribute('content', isDark ? '#020617' : '#ffffff');
            }
        }

        function copyCurrentUrl() {
            const url = window.location.href;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => showToast('Tautan berhasil disalin ke clipboard!'));
            } else {
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Tautan berhasil disalin ke clipboard!');
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
