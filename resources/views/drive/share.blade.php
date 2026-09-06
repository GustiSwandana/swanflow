<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Unduh Berkas: {{ $file->title }} - SwanDrive</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-full flex flex-col justify-between selection:bg-teal-500 selection:text-white antialiased">

    <!-- Ambient Gradient Background (Teal/Emerald only, NO blue) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 flex-1 flex flex-col items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 text-center">
            
            <!-- Branding Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-400 text-xs font-extrabold tracking-wide">
                <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                <span>SwanDrive File Transfer</span>
            </div>

            <!-- File Icon & Extension Badge -->
            @php
                $meta = $file->categoryMeta();
            @endphp
            <div class="relative mx-auto w-20 h-20 rounded-2xl {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }} flex items-center justify-center shadow-lg">
                <span class="text-2xl font-black uppercase">{{ substr($file->extension, 0, 4) }}</span>
                <span class="absolute -bottom-2 -right-2 px-2 py-0.5 rounded-md bg-slate-950 text-slate-300 text-[10px] font-bold border border-slate-800">
                    {{ strtoupper($file->extension) }}
                </span>
            </div>

            <!-- File Details -->
            <div class="space-y-1.5">
                <h1 class="text-lg sm:text-xl font-extrabold text-white leading-tight break-words" title="{{ $file->title }}">
                    {{ $file->title }}
                </h1>
                <p class="text-xs text-slate-400 truncate max-w-xs mx-auto">
                    {{ $file->original_name }}
                </p>
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-teal-400/90 pt-1">
                    <span>{{ $file->formatted_size }}</span>
                    <span>•</span>
                    <span>{{ $file->created_at->format('d M Y') }}</span>
                </div>
            </div>

            @if(!empty($file->notes))
                <div class="text-xs text-slate-300 bg-slate-950/70 p-3 rounded-2xl border border-slate-800 text-left italic">
                    <span class="font-bold text-slate-400 not-italic block mb-0.5 text-[10px]">Catatan Pengirim:</span>
                    "{{ $file->notes }}"
                </div>
            @endif

            @php
                $cleanExt = strtolower($file->extension);
                $isImage = in_array($cleanExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico']);
                $isAudio = in_array($cleanExt, ['mp3', 'wav', 'ogg', 'm4a', 'aac']);
                $isVideo = in_array($cleanExt, ['mp4', 'webm', 'ogg', 'mov']);
                $isPdf = $cleanExt === 'pdf';
            @endphp

            @if($isImage)
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 max-h-72 flex items-center justify-center p-1.5 shadow-inner">
                    <img src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" alt="{{ $file->title }}" class="max-h-64 max-w-full rounded-xl object-contain">
                </div>
            @elseif($isAudio)
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                    <audio controls class="w-full rounded-xl" src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}">
                        Browser Anda tidak mendukung audio player.
                    </audio>
                </div>
            @elseif($isVideo)
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-black max-h-72 flex items-center justify-center shadow-inner">
                    <video controls playsinline class="max-h-68 w-full object-contain" src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                </div>
            @elseif($isPdf)
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-white h-72 shadow-inner">
                    <iframe src="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}#toolbar=0" class="w-full h-full" title="PDF Preview"></iframe>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-2 pt-2">
                <a href="{{ route('drive.shared.download', ['token' => $file->share_token]) }}"
                   class="w-full py-3.5 px-6 rounded-2xl bg-teal-500 hover:bg-teal-600 active:scale-[0.98] text-white font-extrabold text-sm shadow-lg shadow-teal-500/25 flex items-center justify-center gap-2 transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Berkas Sekarang</span>
                </a>

                <a href="{{ route('drive.shared.preview', ['token' => $file->share_token]) }}" target="_blank"
                   class="w-full py-2.5 px-4 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold border border-slate-700/80 flex items-center justify-center gap-1.5 active:scale-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Lihat Berkas di Tab Baru</span>
                </a>

                <p class="text-[11px] text-slate-500">
                    File aman, diakses langsung dari server pribadi SwanDrive.
                </p>
            </div>

        </div>
    </div>

    <!-- Minimalist Footer -->
    <footer class="relative z-10 text-center py-4 text-[11px] text-slate-600">
        SwanDrive &copy; {{ date('Y') }} • Powered by SwanFlow
    </footer>

</body>
</html>
