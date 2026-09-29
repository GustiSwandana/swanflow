<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-slate-100 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#020617" id="meta-theme-color-dark" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" id="meta-theme-color-light" media="(prefers-color-scheme: light)">
    <title>{{ $order->project_title ?? 'Portal Penyerahan File Klien' }} - {{ $settings->studio_name ?? 'SwanFlow' }}</title>
    <meta name="description" content="Portal resmi penyerahan dan pengunduhan file foto dan video klien.">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sat: env(safe-area-inset-top, 0px);
            --sab: env(safe-area-inset-bottom, 0px);
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        html:not(.dark) .theme-icon-dark { display: none !important; }
        html.dark .theme-icon-light { display: none !important; }

        @keyframes liquid-orb-float-1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(15px, 20px) scale(1.08); }
        }
        @keyframes liquid-orb-float-2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -15px) scale(1.06); }
        }
        .animate-liquid-orb-1 {
            animation: liquid-orb-float-1 9s ease-in-out infinite;
        }
        .animate-liquid-orb-2 {
            animation: liquid-orb-float-2 11s ease-in-out infinite;
        }
        .ios-press:active {
            transform: scale(0.96);
            transition: transform 0.15s ease-out;
        }
    </style>

    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('swanflow_theme');
                if (savedTheme === 'light') {
                    document.documentElement.classList.remove('dark');
                } else {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();

        function toggleSwanFlowTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('swanflow_theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('swanflow_theme', 'dark');
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-[#0b0c10] text-slate-800 dark:text-slate-100 relative overflow-x-hidden selection:bg-teal-500 selection:text-white transition-colors duration-200">

    <!-- Ambient Glowing Background Orbs -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-[450px] pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-24 -left-16 w-80 h-80 rounded-full bg-teal-500/20 dark:bg-teal-500/15 blur-[90px] animate-liquid-orb-1"></div>
        <div class="absolute top-10 -right-20 w-80 h-80 rounded-full bg-emerald-500/20 dark:bg-emerald-500/10 blur-[90px] animate-liquid-orb-2"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 max-w-xl mx-auto px-4 pt-4 pb-16 flex flex-col min-h-screen" style="padding-top: max(1.25rem, var(--sat));">
        
        <!-- Top Navigation Header -->
        <header class="flex items-center justify-between gap-3 mb-6 p-2 rounded-2xl backdrop-blur-xl bg-white/60 dark:bg-slate-900/60 border border-white/60 dark:border-white/10 shadow-sm">
            <!-- Studio Brand Badge -->
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center font-black text-sm shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-xs font-black text-slate-900 dark:text-white truncate" id="studioNameDisplay">
                        {{ $settings->studio_name ?? 'Lensa Art Studio' }}
                    </span>
                    <span class="text-[10px] text-teal-600 dark:text-teal-400 font-semibold truncate">Portal Berkas Resmi</span>
                </div>
            </div>

            <!-- Right Controls: Status Badge + Theme Toggle -->
            <div class="flex items-center gap-2 shrink-0">
                <div id="statusBadgeContainer">
                    @php
                        $isApproved = ($order->status === 'verified' || $order->is_free);
                        $isWaiting = ($order->status === 'waiting_verification');
                        $isRejected = ($order->status === 'rejected');
                    @endphp
                    @if($isApproved)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span id="headerStatusText">TERVERIFIKASI</span>
                        </span>
                    @elseif($isWaiting)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-500/40 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            <span id="headerStatusText">VERIFIKASI</span>
                        </span>
                    @elseif($isRejected)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span id="headerStatusText">DITOLAK</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-slate-500/15 text-slate-700 dark:text-slate-300 border border-slate-500/30">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span id="headerStatusText">BELUM BAYAR</span>
                        </span>
                    @endif
                </div>

                <!-- Dark/Light Mode Button -->
                <button type="button" 
                        onclick="toggleSwanFlowTheme()" 
                        class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer ios-press"
                        aria-label="Ubah Tema">
                    <svg class="w-4 h-4 theme-icon-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg class="w-4 h-4 theme-icon-light" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Project Hero Card -->
        <section class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-2xl rounded-[32px] p-6 border border-white/60 dark:border-white/10 shadow-xl mb-5 transition-all">
            <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
                <div class="min-w-0 flex-1">
                    <span class="text-[11px] font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block">
                        Dokumentasi Proyek
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-tight mt-1" id="projectTitle">
                        {{ $order->project_title }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Klien: <strong class="text-slate-800 dark:text-slate-200" id="clientName">{{ $order->client_name }}</strong>
                    </p>
                </div>

                <!-- Price Box (Hidden if is_free) -->
                <div id="billingSection" class="{{ $order->is_free ? 'hidden' : 'text-right' }}">
                    @if($order->discount > 0)
                        <div class="flex items-center justify-end gap-1.5 mb-0.5">
                            <span class="text-xs text-slate-400 line-through">Rp {{ number_format($order->amount, 0, ',', '.') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                -Rp {{ number_format($order->discount, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($order->discount_label)
                            <span class="text-[11px] font-semibold text-teal-600 dark:text-teal-400 block mb-1">
                                {{ $order->discount_label }}
                            </span>
                        @endif
                    @endif

                    <div class="flex items-baseline justify-end gap-1.5">
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total:</span>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight" id="amountDisplay">
                            Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            @if($order->notes)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-200/80 dark:border-white/5 text-xs text-slate-600 dark:text-slate-300">
                    <strong class="text-teal-600 dark:text-teal-400 block mb-0.5">Catatan Studio:</strong>
                    <p id="projectNotesText">{{ $order->notes }}</p>
                </div>
            @endif
        </section>

        <!-- UNLOCKED SECTION (Displayed when APPROVED / FREE) -->
        <section id="approvedSection" class="{{ $isApproved ? 'block' : 'hidden' }} bg-gradient-to-b from-teal-500/10 via-white/90 to-white/80 dark:from-teal-950/40 dark:via-slate-900/90 dark:to-slate-900/85 backdrop-blur-2xl rounded-[32px] p-6 border border-emerald-400/50 dark:border-emerald-500/30 shadow-2xl text-center space-y-5 mb-5 transition-all">
            <div class="w-16 h-16 rounded-[24px] bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/30 text-3xl">
                🎉
            </div>
            
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight" id="approvedHeroTitle">
                    File Anda Telah Siap Diunduh!
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1 leading-relaxed" id="approvedHeroDesc">
                    Terima kasih atas kepercayaannya. Seluruh hasil karya foto & video telah selesai dan siap diakses.
                </p>
            </div>

            <!-- Deliverable Info & Download CTAs -->
            <div class="space-y-3 pt-1">
                
                <!-- Deliverable: SwanDrive Folder -->
                <div id="unlockedFolderBox" class="{{ $deliverableType === 'folder' ? 'block' : 'hidden' }} space-y-3">
                    <div class="p-4 rounded-2xl bg-teal-50/80 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60 text-left flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-3xl shrink-0">📁</span>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-slate-900 dark:text-white truncate block" id="unlockedFolderName">
                                    {{ $folderName ?? 'Folder Dokumentasi' }}
                                </span>
                                <span class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold" id="unlockedFolderCount">
                                    {{ $fileCount ?? 0 }} Berkas • SwanDrive Vault
                                </span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 shrink-0">
                            Aman
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <a id="folderViewBtn" href="{{ $downloadUrl ?? '#' }}" target="_blank" rel="noopener noreferrer" 
                           class="py-3.5 px-4 rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white text-xs sm:text-sm font-black flex items-center justify-center gap-2 shadow-lg shadow-teal-500/25 transition-all ios-press cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            <span>Buka di SwanDrive</span>
                        </a>

                        @if($zipUrl)
                            <a id="folderZipBtn" href="{{ $zipUrl }}" 
                               class="py-3.5 px-4 rounded-2xl bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 active:scale-[0.98] text-white text-xs sm:text-sm font-black flex items-center justify-center gap-2 border border-slate-700 dark:border-white/10 shadow-md transition-all ios-press cursor-pointer">
                                <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <span>Unduh Semua (ZIP)</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Deliverable: Single Stored File -->
                <div id="unlockedSingleFileBox" class="{{ $deliverableType === 'file' ? 'block' : 'hidden' }} space-y-3">
                    <div class="p-4 rounded-2xl bg-teal-50/80 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60 text-left flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-3xl shrink-0">📄</span>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-slate-900 dark:text-white truncate block" id="unlockedFileName">
                                    {{ $fileName ?? 'Berkas SwanDrive' }}
                                </span>
                                <span class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold" id="unlockedFileSize">
                                    {{ $fileSizeFormatted ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <a id="fileDownloadBtn" href="{{ $downloadUrl ?? '#' }}" target="_blank" rel="noopener noreferrer" 
                       class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white text-xs sm:text-sm font-black flex items-center justify-center gap-2 shadow-lg shadow-teal-500/25 transition-all ios-press cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Berkas Sekarang</span>
                    </a>
                </div>

                <!-- Deliverable: External Google Drive / Cloud Link -->
                <div id="unlockedExternalBox" class="{{ $deliverableType === 'external' ? 'block' : 'hidden' }}">
                    <a id="externalCloudBtn" href="{{ $downloadUrl ?? '#' }}" target="_blank" rel="noopener noreferrer" 
                       class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-600 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white text-xs sm:text-sm font-black flex items-center justify-center gap-2 shadow-lg shadow-teal-500/25 transition-all ios-press cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        <span>Buka File di Google Drive / Cloud</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- LOCKED SECTION: Bank Transfer, QRIS & Proof Upload -->
        <div id="lockedPaywallSection" class="{{ $isApproved ? 'hidden' : 'space-y-5' }}">
            
            <!-- Rejection Notice Banner -->
            <div id="rejectedNoticeBox" class="{{ $isRejected ? 'block' : 'hidden' }} p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 space-y-1">
                <div class="flex items-center gap-2 font-black text-xs">
                    <span>⚠️</span>
                    <span>Bukti Transfer Ditolak</span>
                </div>
                <p class="text-xs" id="rejectReasonText">
                    {{ $order->rejection_reason ?? 'Mohon periksa kembali kesesuaian nominal atau kejelasan foto bukti transfer Anda.' }}
                </p>
            </div>

            <!-- Pending Verification Banner -->
            <div id="pendingVerificationBox" class="{{ $isWaiting ? 'block' : 'hidden' }} p-4 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-800 dark:text-amber-200 text-center space-y-2 animate-pulse">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 flex items-center justify-center mx-auto text-xl">
                    ⏳
                </div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Sedang Diverifikasi oleh Studio</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-sm mx-auto">
                    Bukti transfer Anda telah diterima dan sedang diperiksa. Halaman ini akan otomatis membuka akses file setelah diverifikasi.
                </p>
            </div>

            <!-- Payment Destination Accounts Card -->
            <section class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-2xl rounded-[32px] p-6 border border-white/60 dark:border-white/10 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Rekening Tujuan Pembayaran</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Transfer sesuai nominal tagihan di atas</p>
                    </div>
                    <span class="text-xs font-bold text-teal-600 dark:text-teal-400">Verifikasi Cepat</span>
                </div>

                <!-- Bank Accounts List -->
                <div class="space-y-2.5" id="bankAccountsContainer">
                    @forelse($bankAccounts as $acc)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-white/5 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-black text-slate-900 dark:text-white">{{ $acc->bank_name }}</span>
                                    <span class="text-[10px] text-slate-400">• a.n {{ $acc->account_name }}</span>
                                </div>
                                <div class="font-mono text-sm sm:text-base font-black text-teal-700 dark:text-teal-300 mt-0.5 tracking-wider">
                                    {{ $acc->account_number }}
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="copyBankNumber('{{ $acc->account_number }}', this)"
                                    class="py-1.5 px-3 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-700 dark:text-teal-300 text-xs font-bold transition-all shrink-0 cursor-pointer active:scale-95">
                                Salin
                            </button>
                        </div>
                    @empty
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-center text-xs text-slate-400">
                            Silakan hubungi admin studio untuk detail nomor rekening pembayaran.
                        </div>
                    @endforelse
                </div>

                <!-- QRIS Button / Container -->
                @if($settings->qris_image_path)
                    <div class="pt-2 border-t border-slate-100 dark:border-white/5">
                        <button type="button" 
                                onclick="toggleQrisDisplay()" 
                                class="w-full py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center gap-2 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.008v.008H6.75V6.75zM6.75 16.5h.008v.008H6.75V16.5zM16.5 6.75h.008v.008H16.5V6.75zM13.5 13.5h.008v.008H13.5V13.5zM13.5 19.5h.008v.008H13.5V19.5zM19.5 13.5h.008v.008H19.5V13.5zM19.5 19.5h.008v.008H19.5V19.5zM16.5 16.5h.008v.008H16.5V16.5z" />
                            </svg>
                            <span id="qrisBtnText">Tampilkan Barcode QRIS</span>
                        </button>

                        <div id="qrisCard" class="hidden mt-3 p-4 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/10 text-center space-y-2">
                            <img src="{{ Storage::url($settings->qris_image_path) }}" alt="QRIS Studio" class="w-48 h-48 mx-auto object-contain rounded-xl">
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Pindai menggunakan BCA, Mandiri, GoPay, OVO, Dana, dll.</p>
                        </div>
                    </div>
                @endif
            </section>

            <!-- Proof Upload Card -->
            <section class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-2xl rounded-[32px] p-6 border border-white/60 dark:border-white/10 shadow-xl space-y-4">
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Unggah Bukti Pembayaran</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Kirim foto struk / resi transfer untuk membuka akses unduh file</p>
                </div>

                <form id="proofUploadForm" onsubmit="handleProofSubmit(event)" class="space-y-3">
                    <div id="dropzone" 
                         onclick="document.getElementById('proofFileInput').click()"
                         class="p-6 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-teal-500 dark:hover:border-teal-400 bg-slate-50/50 dark:bg-slate-800/30 text-center cursor-pointer transition-all space-y-2">
                        <input type="file" id="proofFileInput" accept="image/*,application/pdf" class="hidden" onchange="previewSelectedProof(this)">
                        
                        <div id="dropzonePrompt" class="space-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto text-xl">
                                📤
                            </div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">
                                Ketuk atau Tarik Foto Bukti Transfer ke Sini
                            </span>
                            <span class="text-[10px] text-slate-400 block">Mendukung format JPG, PNG, WEBP, PDF (Maks. 25MB)</span>
                        </div>

                        <!-- Image Preview -->
                        <div id="dropzonePreview" class="hidden space-y-2">
                            <img id="previewImage" src="" alt="Pratinjau Bukti" class="max-h-48 mx-auto rounded-xl object-contain shadow-xs">
                            <span id="previewFilename" class="text-xs font-bold text-teal-600 dark:text-teal-400 block truncate"></span>
                            <span class="text-[10px] text-slate-400 underline block">Ketuk untuk ganti foto</span>
                        </div>
                    </div>

                    <button type="submit" 
                            id="btnSubmitProof"
                            disabled
                            class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition-all opacity-50 cursor-not-allowed flex items-center justify-center gap-2 ios-press">
                        <svg class="w-4 h-4 hidden animate-spin" id="uploadSpinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span id="btnSubmitText">Kirim Bukti Pembayaran</span>
                    </button>
                </form>
            </section>

        </div>

        <!-- Footer -->
        <footer class="mt-auto pt-8 text-center text-xs text-slate-400 space-y-1">
            <p>© {{ date('Y') }} {{ $settings->studio_name ?? 'Studio & Creative' }} • Didukung oleh <strong class="text-teal-600 dark:text-teal-400">SwanFlow</strong></p>
            <p class="text-[10px] text-slate-400/80">Sistem Penyerahan Berkas Digital & Paywall Aman</p>
            <p class="text-[10px] text-slate-400/80">Aplikasi ini dibuat oleh <strong class="font-bold text-slate-500 dark:text-slate-400">Gusti Swandana</strong></p>
        </footer>

    </div>

    <!-- Toast Notification Element -->
    <div id="clientToast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-2xl bg-slate-900/90 text-white text-xs font-bold shadow-2xl backdrop-blur-xl border border-white/10 transition-all duration-300 opacity-0 pointer-events-none transform translate-y-3">
        Tersalin ke clipboard!
    </div>

    <script>
        const TOKEN = "{{ $token }}";
        let isPolling = true;
        let lastKnownStatus = "{{ $isApproved ? 'APPROVED' : ($isWaiting ? 'PENDING_VERIFICATION' : ($isRejected ? 'REJECTED' : 'UNPAID')) }}";

        function showToast(msg) {
            const toast = document.getElementById('clientToast');
            if (!toast) return;
            toast.innerText = msg;
            toast.classList.remove('opacity-0', 'translate-y-3', 'pointer-events-none');
            toast.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-3', 'pointer-events-none');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 2500);
        }

        function copyBankNumber(num, btn) {
            navigator.clipboard.writeText(num).then(() => {
                showToast(`Nomor rekening ${num} berhasil disalin!`);
                if (btn) {
                    const originalText = btn.innerText;
                    btn.innerText = 'Tersalin!';
                    setTimeout(() => btn.innerText = originalText, 1500);
                }
            });
        }

        function toggleQrisDisplay() {
            const card = document.getElementById('qrisCard');
            const btnText = document.getElementById('qrisBtnText');
            if (card.classList.contains('hidden')) {
                card.classList.remove('hidden');
                btnText.innerText = 'Sembunyikan Barcode QRIS';
            } else {
                card.classList.add('hidden');
                btnText.innerText = 'Tampilkan Barcode QRIS';
            }
        }

        function previewSelectedProof(input) {
            const file = input.files[0];
            const btn = document.getElementById('btnSubmitProof');
            if (!file) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            document.getElementById('dropzonePrompt').classList.add('hidden');
            document.getElementById('dropzonePreview').classList.remove('hidden');
            document.getElementById('previewFilename').innerText = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('previewImage').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('previewImage').classList.add('hidden');
            }

            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        async function handleProofSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('proofFileInput');
            if (!input.files || !input.files[0]) return;

            const btn = document.getElementById('btnSubmitProof');
            const spinner = document.getElementById('uploadSpinner');
            const btnText = document.getElementById('btnSubmitText');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.innerText = 'Mengunggah Bukti...';

            const formData = new FormData();
            formData.append('proof', input.files[0]);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/api/p/${TOKEN}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.error || 'Gagal mengunggah bukti.');
                }

                showToast('Bukti pembayaran berhasil dikirim!');
                applyProjectData(data);
            } catch (err) {
                alert(err.message || 'Terjadi kesalahan saat mengunggah.');
            } finally {
                spinner.classList.add('hidden');
                btnText.innerText = 'Kirim Bukti Pembayaran';
                btn.disabled = false;
            }
        }

        function triggerConfetti() {
            if (typeof confetti === 'function') {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        }

        function applyProjectData(data) {
            if (!data || !data.project) return;
            const p = data.project;

            // Check if status transitioned to APPROVED
            if (p.status === 'APPROVED' && lastKnownStatus !== 'APPROVED') {
                triggerConfetti();
            }
            lastKnownStatus = p.status;

            // Header status text
            const headerText = document.getElementById('headerStatusText');
            if (headerText) {
                headerText.innerText = p.status === 'APPROVED' ? 'TERVERIFIKASI' : (p.status === 'PENDING_VERIFICATION' ? 'VERIFIKASI' : (p.status === 'REJECTED' ? 'DITOLAK' : 'BELUM BAYAR'));
            }

            if (p.status === 'APPROVED') {
                document.getElementById('approvedSection').classList.remove('hidden');
                document.getElementById('lockedPaywallSection').classList.add('hidden');

                // Deliverable specific updates
                if (p.deliverable_type === 'folder') {
                    document.getElementById('unlockedFolderBox').classList.remove('hidden');
                    document.getElementById('unlockedSingleFileBox').classList.add('hidden');
                    document.getElementById('unlockedExternalBox').classList.add('hidden');

                    if (p.folder_name) document.getElementById('unlockedFolderName').innerText = p.folder_name;
                    if (p.file_count !== undefined) document.getElementById('unlockedFolderCount').innerText = `${p.file_count} Berkas • SwanDrive Vault`;
                    if (p.gdrive_url) document.getElementById('folderViewBtn').href = p.gdrive_url;
                    if (p.zip_url) {
                        const zipBtn = document.getElementById('folderZipBtn');
                        if (zipBtn) zipBtn.href = p.zip_url;
                    }
                } else if (p.deliverable_type === 'file') {
                    document.getElementById('unlockedFolderBox').classList.add('hidden');
                    document.getElementById('unlockedSingleFileBox').classList.remove('hidden');
                    document.getElementById('unlockedExternalBox').classList.add('hidden');

                    if (p.file_name) document.getElementById('unlockedFileName').innerText = p.file_name;
                    if (p.file_size_formatted) document.getElementById('unlockedFileSize').innerText = p.file_size_formatted;
                    if (p.gdrive_url) document.getElementById('fileDownloadBtn').href = p.gdrive_url;
                } else {
                    document.getElementById('unlockedFolderBox').classList.add('hidden');
                    document.getElementById('unlockedSingleFileBox').classList.add('hidden');
                    document.getElementById('unlockedExternalBox').classList.remove('hidden');

                    if (p.gdrive_url) document.getElementById('externalCloudBtn').href = p.gdrive_url;
                }
            } else {
                document.getElementById('approvedSection').classList.add('hidden');
                document.getElementById('lockedPaywallSection').classList.remove('hidden');

                const pendingBox = document.getElementById('pendingVerificationBox');
                const rejectedBox = document.getElementById('rejectedNoticeBox');

                if (p.status === 'PENDING_VERIFICATION') {
                    pendingBox.classList.remove('hidden');
                    rejectedBox.classList.add('hidden');
                } else if (p.status === 'REJECTED') {
                    pendingBox.classList.add('hidden');
                    rejectedBox.classList.remove('hidden');
                    if (p.reject_reason) {
                        document.getElementById('rejectReasonText').innerText = p.reject_reason;
                    }
                } else {
                    pendingBox.classList.add('hidden');
                    rejectedBox.classList.add('hidden');
                }
            }
        }

        // Realtime Polling
        async function pollProjectStatus() {
            if (!isPolling) return;
            try {
                const res = await fetch(`/api/p/${TOKEN}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    applyProjectData(data);
                    if (data.project && data.project.status === 'APPROVED') {
                        isPolling = false; // Stop polling once unlocked
                    }
                }
            } catch (e) {
                // Silently retry
            }
        }

        setInterval(pollProjectStatus, 4000);
    </script>
</body>
</html>
