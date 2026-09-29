@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass SwanFlow Header -->
    <div class="relative overflow-hidden bg-gradient-to-b from-teal-600/95 via-teal-600/85 to-emerald-700/90 dark:from-slate-900/95 dark:via-teal-950/90 dark:to-slate-950/95 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-200" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Highlight Line -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-teal-400/20 dark:bg-teal-500/15 rounded-full blur-3xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-2xl pointer-events-none animate-liquid-orb-2"></div>

        <!-- Top Navigation Bar -->
        <div class="relative z-10 flex items-center justify-between mb-5 gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Dashboard">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h1 class="text-base font-black tracking-tight text-white leading-tight">Portal Klien</h1>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-teal-400/25 text-teal-200 border border-teal-300/30 shadow-2xs backdrop-blur-md shrink-0">Paywall</span>
                    </div>
                    <span class="text-[11px] text-teal-200/80 font-semibold truncate mt-0.5">{{ $settings->studio_name ?? 'Studio Creative' }} • Penyerahan File</span>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('drive.index') }}" 
                   class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 text-white shadow-xs border border-white/30 flex items-center justify-center transition-all cursor-pointer ios-press" 
                   title="Buka SwanDrive" 
                   aria-label="Buka SwanDrive">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                    </svg>
                </a>
                <button type="button" 
                        onclick="openSettingsModal()" 
                        class="w-10 h-10 flex items-center justify-center rounded-[18px] liquid-glass text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/30 active:scale-95 transition-all shadow-xs ios-press"
                        title="Pengaturan Studio & Rekening">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Revenue & Stats Card -->
        <div class="relative z-20 bg-white/12 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/20 dark:border-white/10 rounded-[30px] p-5 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-teal-100 dark:text-slate-400 uppercase tracking-wider">
                    Total Pembayaran Diterima
                </span>
                <div class="flex items-center gap-1.5">
                    @if($waitingCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-[12px] text-[10px] font-black bg-amber-400/30 text-amber-100 border border-amber-300/40 animate-pulse">
                            {{ $waitingCount }} Butuh Verifikasi
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-[12px] text-[10px] font-extrabold bg-white/20 dark:bg-teal-500/20 text-white border border-white/30">
                        {{ $totalProjects }} Proyek
                    </span>
                </div>
            </div>

            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h2>
                <div class="flex items-center gap-4 mt-2 text-[11px] text-teal-100/90 font-medium">
                    <span>⏳ Menunggu: <strong>{{ $unpaidCount }}</strong></span>
                    <span>•</span>
                    <span>✅ Lunas: <strong>{{ $totalProjects - $unpaidCount - $waitingCount }}</strong></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-5 px-1 pb-24">

    <!-- Search & Filter Bar -->
    <div class="flex items-center justify-between gap-2.5 pt-1">
        <form method="GET" action="{{ route('orders.index') }}" class="relative flex-1">
            <input type="text" 
                   name="q" 
                   value="{{ request('q') }}" 
                   placeholder="Cari nama klien / proyek..." 
                   class="w-full bg-slate-100 dark:bg-slate-800/80 text-slate-800 dark:text-white pl-9 pr-4 py-2.5 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500/50 border border-slate-200/80 dark:border-white/10 transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>

        <button type="button" 
                onclick="openAddOrderModal()" 
                class="bg-gradient-to-tr from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 active:scale-95 text-white font-bold text-xs px-3.5 py-2.5 rounded-2xl flex items-center gap-1.5 shadow-lg shadow-emerald-500/25 transition-all shrink-0 cursor-pointer ios-press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>+ Buat Link</span>
        </button>
    </div>

    <!-- Status Filter Pills -->
    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 -mx-1 px-1">
        @php
            $statuses = [
                'all' => 'Semua',
                'waiting_verification' => '⏳ Butuh Verifikasi',
                'unpaid' => 'Belum Lunas',
                'verified' => '✅ Lunas',
                'rejected' => 'Ditolak',
                'free' => 'Akses Langsung',
            ];
        @endphp
        @foreach($statuses as $k => $label)
            @php
                $active = ($statusFilter === $k);
            @endphp
            <a href="{{ route('orders.index', array_merge(request()->query(), ['status' => $k])) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $active ? 'bg-teal-600 text-white shadow-xs shadow-teal-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Orders Cards List -->
    <div class="space-y-3.5">
        @forelse($orders as $order)
            <div class="bg-white/90 dark:bg-slate-900/90 rounded-[28px] p-4.5 border border-slate-200/80 dark:border-white/10 shadow-xs relative overflow-hidden transition-all backdrop-blur-xl">
                
                <!-- Status & Tag Ribbon -->
                <div class="flex items-center justify-between gap-2 mb-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        @if($order->is_free)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-teal-500/15 text-teal-700 dark:text-teal-300 border border-teal-500/30">
                                AKSES LANGSUNG
                            </span>
                        @elseif($order->status === 'verified')
                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LUNAS
                            </span>
                        @elseif($order->status === 'waiting_verification')
                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-500/40 animate-pulse flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> BUTUH VERIFIKASI
                            </span>
                        @elseif($order->status === 'rejected')
                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                DITOLAK
                            </span>
                        @else
                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-200 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-300/50 dark:border-slate-600/50">
                                BELUM BAYAR
                            </span>
                        @endif

                        <span class="text-[11px] text-slate-400 dark:text-slate-500 truncate font-mono">
                            /p/{{ $order->token }}
                        </span>
                    </div>

                    <!-- Client Direct Link / WA -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ $order->client_url }}" 
                           target="_blank" 
                           class="p-1.5 rounded-xl bg-teal-500/10 dark:bg-teal-400/10 text-teal-600 dark:text-teal-400 hover:bg-teal-500/20 active:scale-90 transition-all text-xs flex items-center gap-1 font-bold"
                           title="Buka Portal Klien (Uji Coba Snap Pop-up)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span class="text-[10px]">Buka</span>
                        </a>

                        <button type="button" 
                                onclick="copyClientUrl('{{ $order->client_url }}')" 
                                class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 active:scale-90 transition-all text-xs flex items-center gap-1 cursor-pointer"
                                title="Salin Link Klien">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span class="text-[10px] font-bold">Salin</span>
                        </button>

                        @if($order->whatsapp_share_url)
                            <a href="{{ $order->whatsapp_share_url }}" 
                               target="_blank" 
                               class="p-1.5 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/25 active:scale-90 transition-all text-xs flex items-center gap-1 font-bold"
                               title="Kirim ke WhatsApp Klien">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                                </svg>
                                <span class="text-[10px]">WA</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Main Project Title & Client -->
                <div class="mb-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                        {{ $order->project_title }}
                    </h3>
                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 dark:text-slate-400">
                        <span>Klien: <strong class="text-slate-700 dark:text-slate-200">{{ $order->client_name }}</strong></span>
                        @if($order->client_phone)
                            <span>•</span>
                            <span>{{ $order->client_phone }}</span>
                        @endif
                    </div>
                </div>

                <!-- Deliverable Connection Badge -->
                @if($order->folder)
                    <div class="mb-3 px-3 py-1.5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200/60 dark:border-teal-800/40 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span>📁</span>
                            <span class="font-bold text-teal-800 dark:text-teal-200 truncate">Folder SwanDrive: {{ $order->folder->name }}</span>
                        </div>
                        <a href="{{ route('drive.index', ['folder_id' => $order->folder_id]) }}" class="text-[10px] font-black text-teal-600 dark:text-teal-400 hover:underline shrink-0">Buka di Drive ↗</a>
                    </div>
                @elseif($order->storedFile)
                    <div class="mb-3 px-3 py-1.5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200/60 dark:border-teal-800/40 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span>📄</span>
                            <span class="font-bold text-teal-800 dark:text-teal-200 truncate">Berkas SwanDrive: {{ $order->storedFile->title }}</span>
                        </div>
                        <span class="text-[10px] text-teal-600 dark:text-teal-400 font-semibold shrink-0">{{ $order->storedFile->formatted_size }}</span>
                    </div>
                @elseif($order->gdrive_url)
                    <div class="mb-3 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-white/5 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span>🌐</span>
                            <span class="font-medium text-slate-600 dark:text-slate-300 truncate">Tautan Cloud Eksternal</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono shrink-0 truncate max-w-[140px]">{{ parse_url($order->gdrive_url, PHP_URL_HOST) ?? 'Cloud Link' }}</span>
                    </div>
                @endif

                <!-- Price Breakdown Box -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 mb-3 border border-slate-100 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tagihan</span>
                        <div class="text-sm font-black text-slate-900 dark:text-white">
                            @if($order->is_free)
                                <span class="text-teal-600 dark:text-teal-400">Akses Langsung (Bebas Biaya)</span>
                            @else
                                Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                @if($order->discount > 0)
                                    <span class="text-[10px] text-rose-500 font-semibold ml-1 line-through">
                                        Rp {{ number_format($order->amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($order->wallet)
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dompet Masuk</span>
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400">
                                💳 {{ $order->wallet->name }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Action Button Grid -->
                <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-100 dark:border-white/5">
                    @if($order->status === 'waiting_verification')
                        <button type="button" 
                                onclick="openVerifyModal({{ $order->id }}, '{{ addslashes($order->client_name) }}', '{{ addslashes($order->project_title) }}', {{ $order->final_amount }}, '{{ $order->payment_proof_path ? Storage::url($order->payment_proof_path) : '' }}', {{ $order->wallet_id ?? 'null' }})" 
                                class="flex-1 py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs text-center transition-all shadow-sm flex items-center justify-center gap-1.5 animate-pulse cursor-pointer">
                            <span>👁️ Periksa Bukti</span>
                        </button>
                    @elseif($order->payment_proof_path)
                        <button type="button" 
                                onclick="openProofZoomModal('{{ Storage::url($order->payment_proof_path) }}', '{{ addslashes($order->client_name) }}')" 
                                class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition-all cursor-pointer">
                            Lihat Struk
                        </button>
                    @endif

                    <button type="button" 
                            onclick="openEditOrderModalFromBtn(this)" 
                            data-order='@json($order)'
                            class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition-all cursor-pointer">
                        Edit
                    </button>

                    <form method="POST" action="{{ route('orders.toggle-free', $order) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-teal-600 dark:hover:text-teal-400 font-bold text-xs transition-all cursor-pointer"
                                title="{{ $order->is_free ? 'Ubah Jadi Berbayar' : 'Jadikan Akses Langsung' }}">
                            {{ $order->is_free ? 'Pasang Tarif?' : 'Bebas Tagihan?' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Hapus proyek {{ addslashes($order->project_title) }}? Link klien akan mati.')" class="inline ml-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-500 transition-all cursor-pointer" title="Hapus Proyek">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center py-12 px-4 bg-white dark:bg-slate-900 rounded-[28px] border border-dashed border-slate-200 dark:border-white/10">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl">
                    💼
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1">Belum Ada Proyek Klien</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto mb-4">
                    Buat link penyerahan file pertama Anda untuk klien. Hubungkan langsung dengan folder/file SwanDrive atau cloud link.
                </p>
                <button type="button" 
                        onclick="openAddOrderModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold text-xs shadow-md shadow-emerald-500/25 transition-all cursor-pointer">
                    <span>+ Buat Link Klien Baru</span>
                </button>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="pt-2">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL 1: Buat Proyek Baru -->
<div id="modal-add-order" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeAddOrderModal()"></div>
    <div class="min-h-screen px-4 text-center flex items-end sm:items-center justify-center p-0">
        <div class="modal-sheet-safe w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-[36px] sm:rounded-[36px] p-6 text-left shadow-2xl transform transition-transform duration-300 translate-y-full border border-slate-200/80 dark:border-white/10 relative z-30 max-h-[92vh] overflow-y-auto no-scrollbar">
            <div class="w-12 h-1 bg-slate-300 dark:bg-slate-700 rounded-full mx-auto mb-5 sm:hidden"></div>

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Buat Link Klien Baru</h3>
                <button type="button" onclick="closeAddOrderModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Klien *</label>
                    <input type="text" name="client_name" required placeholder="Contoh: Sarah & Dimas" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WhatsApp Klien</label>
                        <input type="tel" name="client_phone" placeholder="081234567890" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slug URL Kustom</label>
                        <input type="text" name="custom_token" placeholder="opsional: wedding-sarah" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-mono font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Proyek *</label>
                    <input type="text" name="project_title" id="add_project_title" required placeholder="Contoh: Foto & Video Wedding 2026" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Checkbox Bebas Tagihan (Akses Langsung) -->
                <div class="flex items-center gap-2 p-3 bg-teal-50 dark:bg-teal-950/30 rounded-2xl border border-teal-200 dark:border-teal-800/30">
                    <input type="checkbox" name="is_free" id="add_is_free" value="1" onchange="toggleAddFreeFields(this)" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 cursor-pointer">
                    <label for="add_is_free" class="text-xs font-bold text-teal-900 dark:text-teal-300 cursor-pointer">
                        Akses Langsung (Bebas Biaya, File Langsung Terbuka untuk Klien)
                    </label>
                </div>

                <!-- Billing Fields -->
                <div id="add_billing_fields" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nominal Tagihan (Rp)</label>
                            <input type="number" name="amount" id="add_amount" placeholder="2500000" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Diskon (Rp)</label>
                            <input type="number" name="discount" id="add_discount" placeholder="0" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Label Diskon (Opsional)</label>
                        <input type="text" name="discount_label" placeholder="Contoh: Promo Early Bird / DP Lunas" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Dompet Pemasukan SwanFlow</label>
                        <select name="wallet_id" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">-- Pilih Dompet Penerima Otomatis --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Saat pembayaran diverifikasi, saldo dompet ini akan otomatis bertambah.</p>
                    </div>
                </div>

                <!-- Deliverable Selector: Tabbed Integration with SwanDrive -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-white/10 space-y-3">
                    <label class="block text-xs font-black text-slate-800 dark:text-white">
                        🔗 Sumber Berkas untuk Klien
                    </label>

                    <!-- Tabs Header -->
                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-200/70 dark:bg-slate-900 rounded-xl text-xs font-bold">
                        <button type="button" 
                                onclick="switchAddDeliverableTab('folder')" 
                                id="tab-add-folder-btn"
                                class="py-2 text-[11px] font-bold rounded-lg transition-all bg-white dark:bg-slate-800 text-teal-700 dark:text-teal-300 shadow-xs cursor-pointer">
                            📁 Folder Drive
                        </button>
                        <button type="button" 
                                onclick="switchAddDeliverableTab('file')" 
                                id="tab-add-file-btn"
                                class="py-2 text-[11px] font-bold rounded-lg transition-all text-slate-600 dark:text-slate-400 cursor-pointer">
                            📄 Berkas Drive
                        </button>
                        <button type="button" 
                                onclick="switchAddDeliverableTab('external')" 
                                id="tab-add-external-btn"
                                class="py-2 text-[11px] font-bold rounded-lg transition-all text-slate-600 dark:text-slate-400 cursor-pointer">
                            🌐 Link Luar
                        </button>
                    </div>

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" name="folder_id" id="add_folder_id" value="">
                    <input type="hidden" name="stored_file_id" id="add_stored_file_id" value="">

                    <!-- Tab 1: Folder SwanDrive Selector -->
                    <div id="tab-add-folder-content" class="space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Pilih folder hasil karya:</span>
                            <span id="add-folder-selected-label" class="font-bold text-teal-600 dark:text-teal-400">Belum dipilih</span>
                        </div>
                        <div class="max-h-44 overflow-y-auto space-y-1.5 pr-1 no-scrollbar">
                            @forelse($folders as $f)
                                <div onclick="selectAddFolder({{ $f->id }}, '{{ addslashes($f->name) }}')"
                                     id="add-folder-card-{{ $f->id }}"
                                     class="add-folder-item p-2.5 rounded-xl border border-slate-200 dark:border-white/10 hover:border-teal-400 bg-white dark:bg-slate-800 flex items-center justify-between cursor-pointer transition-all">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-xl">📁</span>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 dark:text-white truncate block">{{ $f->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $f->files_count ?? 0 }} berkas tersimpan</span>
                                        </div>
                                    </div>
                                    <span class="add-folder-check hidden w-5 h-5 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-black shrink-0">✓</span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-400">
                                    Belum ada folder di SwanDrive. <a href="{{ route('drive.index') }}" class="text-teal-600 font-bold underline">Buat folder di Drive</a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab 2: File SwanDrive Selector -->
                    <div id="tab-add-file-content" class="hidden space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Pilih berkas tunggal / zip:</span>
                            <span id="add-file-selected-label" class="font-bold text-teal-600 dark:text-teal-400">Belum dipilih</span>
                        </div>
                        <div class="max-h-44 overflow-y-auto space-y-1.5 pr-1 no-scrollbar">
                            @forelse($storedFiles as $fl)
                                <div onclick="selectAddFile({{ $fl->id }}, '{{ addslashes($fl->title) }}')"
                                     id="add-file-card-{{ $fl->id }}"
                                     class="add-file-item p-2.5 rounded-xl border border-slate-200 dark:border-white/10 hover:border-teal-400 bg-white dark:bg-slate-800 flex items-center justify-between cursor-pointer transition-all">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-xs font-black px-1.5 py-0.5 rounded bg-teal-500/20 text-teal-700 dark:text-teal-300 uppercase shrink-0">{{ substr($fl->extension, 0, 4) }}</span>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 dark:text-white truncate block">{{ $fl->title }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $fl->formatted_size }}</span>
                                        </div>
                                    </div>
                                    <span class="add-file-check hidden w-5 h-5 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-black shrink-0">✓</span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-400">
                                    Belum ada berkas di SwanDrive.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab 3: External URL -->
                    <div id="tab-add-external-content" class="hidden space-y-1.5">
                        <label class="block text-[11px] text-slate-500 dark:text-slate-400">
                            Masukkan URL Google Drive, Dropbox, atau Cloud Link:
                        </label>
                        <input type="url" name="gdrive_url" id="add_gdrive_url" placeholder="https://drive.google.com/drive/folders/..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan untuk Klien</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: File format Master 4K & Foto Hi-Res, link aktif 30 hari..." class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-500/25 transition-all cursor-pointer ios-press">
                        Simpan & Buat Tautan Klien
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: Edit Proyek -->
<div id="modal-edit-order" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeEditOrderModal()"></div>
    <div class="min-h-screen px-4 text-center flex items-end sm:items-center justify-center p-0">
        <div class="modal-sheet-safe w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-[36px] sm:rounded-[36px] p-6 text-left shadow-2xl transform transition-transform duration-300 translate-y-full border border-slate-200/80 dark:border-white/10 relative z-30 max-h-[92vh] overflow-y-auto no-scrollbar">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-900 dark:text-white" id="edit_modal_title">Edit Proyek Klien</h3>
                <button type="button" onclick="closeEditOrderModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <form id="edit_order_form" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Klien *</label>
                    <input type="text" name="client_name" id="edit_client_name" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WhatsApp Klien</label>
                        <input type="tel" name="client_phone" id="edit_client_phone" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slug URL Klien *</label>
                        <input type="text" name="token" id="edit_token" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-mono font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Proyek *</label>
                    <input type="text" name="project_title" id="edit_project_title" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Proyek</label>
                    <select name="status" id="edit_status" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                        <option value="unpaid">Belum Bayar</option>
                        <option value="waiting_verification">Menunggu Verifikasi Bukti</option>
                        <option value="verified">Lunas / Terverifikasi</option>
                        <option value="rejected">Bukti Ditolak</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" id="edit_amount" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Diskon (Rp)</label>
                        <input type="number" name="discount" id="edit_discount" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <!-- Deliverable Selection in Edit -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-white/10 space-y-3">
                    <label class="block text-xs font-black text-slate-800 dark:text-white">
                        🔗 Sumber Berkas untuk Klien
                    </label>

                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-200/70 dark:bg-slate-900 rounded-xl text-xs font-bold">
                        <button type="button" onclick="switchEditDeliverableTab('folder')" id="tab-edit-folder-btn" class="py-2 text-[11px] font-bold rounded-lg transition-all bg-white dark:bg-slate-800 text-teal-700 dark:text-teal-300 shadow-xs cursor-pointer">
                            📁 Folder Drive
                        </button>
                        <button type="button" onclick="switchEditDeliverableTab('file')" id="tab-edit-file-btn" class="py-2 text-[11px] font-bold rounded-lg transition-all text-slate-600 dark:text-slate-400 cursor-pointer">
                            📄 Berkas Drive
                        </button>
                        <button type="button" onclick="switchEditDeliverableTab('external')" id="tab-edit-external-btn" class="py-2 text-[11px] font-bold rounded-lg transition-all text-slate-600 dark:text-slate-400 cursor-pointer">
                            🌐 Link Luar
                        </button>
                    </div>

                    <input type="hidden" name="folder_id" id="edit_folder_id" value="">
                    <input type="hidden" name="stored_file_id" id="edit_stored_file_id" value="">

                    <div id="tab-edit-folder-content" class="space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Pilih folder hasil karya:</span>
                            <span id="edit-folder-selected-label" class="font-bold text-teal-600 dark:text-teal-400">Belum dipilih</span>
                        </div>
                        <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1 no-scrollbar">
                            @foreach($folders as $f)
                                <div onclick="selectEditFolder({{ $f->id }}, '{{ addslashes($f->name) }}')"
                                     id="edit-folder-card-{{ $f->id }}"
                                     class="edit-folder-item p-2.5 rounded-xl border border-slate-200 dark:border-white/10 hover:border-teal-400 bg-white dark:bg-slate-800 flex items-center justify-between cursor-pointer transition-all">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-xl">📁</span>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 dark:text-white truncate block">{{ $f->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $f->files_count ?? 0 }} berkas</span>
                                        </div>
                                    </div>
                                    <span class="edit-folder-check hidden w-5 h-5 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-black shrink-0">✓</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="tab-edit-file-content" class="hidden space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Pilih berkas SwanDrive:</span>
                            <span id="edit-file-selected-label" class="font-bold text-teal-600 dark:text-teal-400">Belum dipilih</span>
                        </div>
                        <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1 no-scrollbar">
                            @foreach($storedFiles as $fl)
                                <div onclick="selectEditFile({{ $fl->id }}, '{{ addslashes($fl->title) }}')"
                                     id="edit-file-card-{{ $fl->id }}"
                                     class="edit-file-item p-2.5 rounded-xl border border-slate-200 dark:border-white/10 hover:border-teal-400 bg-white dark:bg-slate-800 flex items-center justify-between cursor-pointer transition-all">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-xs font-black px-1.5 py-0.5 rounded bg-teal-500/20 text-teal-700 dark:text-teal-300 uppercase shrink-0">{{ substr($fl->extension, 0, 4) }}</span>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 dark:text-white truncate block">{{ $fl->title }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $fl->formatted_size }}</span>
                                        </div>
                                    </div>
                                    <span class="edit-file-check hidden w-5 h-5 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-black shrink-0">✓</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="tab-edit-external-content" class="hidden space-y-1.5">
                        <input type="url" name="gdrive_url" id="edit_gdrive_url" placeholder="https://drive.google.com/..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
                    <textarea name="notes" id="edit_notes" rows="2" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-500/25 transition-all cursor-pointer ios-press">
                        Perbarui Data Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: Verifikasi Bukti Pembayaran -->
<div id="modal-verify-proof" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeVerifyModal()"></div>
    <div class="min-h-screen px-4 text-center flex items-end sm:items-center justify-center p-0">
        <div class="modal-sheet-safe w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-[36px] sm:rounded-[36px] p-6 text-left shadow-2xl transform transition-transform duration-300 translate-y-full border border-slate-200/80 dark:border-white/10 relative z-30 max-h-[92vh] overflow-y-auto no-scrollbar">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Verifikasi Bukti Transfer</h3>
                <button type="button" onclick="closeVerifyModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <div class="mb-4">
                <div class="text-xs text-slate-500 dark:text-slate-400" id="verify_client_title">Klien: Sarah & Dimas</div>
                <div class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-0.5" id="verify_amount_display">Rp 2.500.000</div>
            </div>

            <!-- Preview Image -->
            <div class="bg-slate-100 dark:bg-slate-950 rounded-2xl p-2 mb-4 border border-slate-200 dark:border-white/10 text-center">
                <img id="verify_proof_img" src="" alt="Bukti Transfer" class="max-h-72 mx-auto rounded-xl object-contain cursor-pointer" onclick="zoomCurrentProof()">
                <p class="text-[10px] text-slate-400 mt-1.5">Ketuk gambar untuk membuka ukuran penuh</p>
            </div>

            <!-- Action: Approve Form -->
            <form id="approve_form" method="POST" action="" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Masuk ke Dompet SwanFlow</label>
                    <select name="wallet_id" id="verify_wallet_select" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 dark:text-white">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 py-1">
                    <input type="checkbox" name="record_transaction" id="verify_record_tx" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="verify_record_tx" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        Otomatis catat transaksi pemasukan ke SwanFlow
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-black text-xs shadow-lg shadow-emerald-500/25 transition-all cursor-pointer ios-press">
                    ✅ Setujui Pembayaran & Buka File Klien
                </button>
            </form>

            <!-- Action: Reject Button & Form -->
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-white/10">
                <button type="button" onclick="toggleRejectBox()" class="text-xs font-bold text-rose-500 hover:text-rose-600 block w-full text-center py-1 cursor-pointer">
                    Tolak Bukti Transfer Ini ✕
                </button>

                <form id="reject_form" method="POST" action="" class="hidden mt-3 space-y-2">
                    @csrf
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Penolakan untuk Klien *</label>
                    <textarea name="rejection_reason" required rows="2" placeholder="Contoh: Nominal transfer kurang atau bukti tidak terbaca..." class="w-full bg-slate-50 dark:bg-slate-800 border border-rose-300 dark:border-rose-900/50 rounded-2xl p-3 text-xs text-slate-900 dark:text-white"></textarea>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs cursor-pointer">
                        Konfirmasi Tolak Bukti
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 4: Pengaturan Studio & Rekening -->
<div id="modal-settings" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="modal-backdrop fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity duration-300 opacity-0" onclick="closeSettingsModal()"></div>
    <div class="min-h-screen px-4 text-center flex items-end sm:items-center justify-center p-0">
        <div class="modal-sheet-safe w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-[36px] sm:rounded-[36px] p-6 text-left shadow-2xl transform transition-transform duration-300 translate-y-full border border-slate-200/80 dark:border-white/10 relative z-30 max-h-[92vh] overflow-y-auto no-scrollbar">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Pengaturan Studio & Pembayaran</h3>
                <button type="button" onclick="closeSettingsModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <!-- Studio Info Form -->
            <form action="{{ route('orders.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3 pb-4 border-b border-slate-100 dark:border-white/10">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Studio / Brand *</label>
                    <input type="text" name="studio_name" value="{{ $settings->studio_name ?? '' }}" required class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Petunjuk Pembayaran untuk Klien</label>
                    <textarea name="bank_instructions" rows="2" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-900 dark:text-white">{{ $settings->bank_instructions ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Upload Gambar Barcode QRIS (Opsional)</label>
                    <input type="file" name="qris_image" accept="image/*" class="w-full text-xs text-slate-500">
                    @if($settings->qris_image_path)
                        <span class="text-[10px] text-teal-600 dark:text-teal-400 font-bold block mt-1">✓ Barcode QRIS aktif</span>
                    @endif
                </div>

                <!-- Midtrans Payment Gateway Section -->
                <div class="p-3.5 rounded-2xl bg-teal-50/60 dark:bg-teal-950/20 border border-teal-200 dark:border-teal-800/50 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-teal-900 dark:text-teal-200 flex items-center gap-1.5">
                            <span>⚡</span> Payment Gateway Otomatis (Midtrans)
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="midtrans_enabled" value="1" {{ !empty($settings->midtrans_enabled) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-teal-500"></div>
                        </label>
                    </div>

                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Otomatis memverifikasi pembayaran klien melalui QRIS, GoPay, ShopeePay, dan Virtual Account bank tanpa verifikasi manual.
                    </p>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Midtrans Server Key</label>
                        <input type="password" name="midtrans_server_key" value="{{ $settings->midtrans_server_key ?? '' }}" placeholder="SB-Mid-server-... atau Mid-server-..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs font-mono text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Midtrans Client Key</label>
                        <input type="text" name="midtrans_client_key" value="{{ $settings->midtrans_client_key ?? '' }}" placeholder="SB-Mid-client-... atau Mid-client-..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs font-mono text-slate-900 dark:text-white">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Mode Produksi (Live)
                        </label>
                        <input type="checkbox" name="midtrans_is_production" value="1" {{ !empty($settings->midtrans_is_production) ? 'checked' : '' }} class="rounded text-teal-600 focus:ring-teal-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-[10px] space-y-1">
                        <span class="font-bold text-slate-500 dark:text-slate-400 block">Webhook Notification URL:</span>
                        <code class="block font-mono text-teal-600 dark:text-teal-400 select-all break-all">{{ url('/api/midtrans/webhook') }}</code>
                        <span class="text-slate-400 block">Salin URL di atas ke Midtrans Dashboard > Settings > Configuration > Payment Notification URL</span>
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs cursor-pointer">
                    Simpan Pengaturan Studio
                </button>
            </form>

            <!-- Bank Accounts Management -->
            <div class="pt-4 space-y-3">
                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Rekening Tujuan Klien</h4>
                
                <div class="space-y-2">
                    @forelse($bankAccounts as $acc)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-white/5 text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $acc->bank_name }} - {{ $acc->account_number }}</span>
                                <div class="text-[10px] text-slate-400">a.n {{ $acc->account_name }}</div>
                            </div>
                            <form method="POST" action="{{ route('orders.bank-accounts.destroy', $acc) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 p-1 text-xs font-bold cursor-pointer" title="Hapus">✕</button>
                            </form>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400">Belum ada rekening transfer.</div>
                    @endforelse
                </div>

                <!-- Add New Account Form -->
                <form action="{{ route('orders.bank-accounts.store') }}" method="POST" class="pt-2 space-y-2">
                    @csrf
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block">+ Tambah Rekening / E-Wallet</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="bank_name" placeholder="BCA / Mandiri / Dana" required class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
                        <input type="text" name="account_number" placeholder="Nomor Rekening" required class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="account_name" placeholder="Atas Nama Pemilik" required class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
                        <select name="wallet_id" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
                            <option value="">-- Link Dompet --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white font-bold text-xs cursor-pointer">
                        + Tambahkan Rekening
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    async function copyClientUrl(url) {
        const handleSuccess = () => {
            if (typeof showSwanToast === 'function') {
                showSwanToast('Link klien berhasil disalin!', 'success');
            } else {
                alert('Link klien berhasil disalin!');
            }
        };

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(url);
                handleSuccess();
                return;
            }
        } catch (e) {}

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
        } catch (err) {}

        prompt('Salin link ini:', url);
    }

    function openAddOrderModal() {
        window.openSheetModal('modal-add-order');
    }
    function closeAddOrderModal() {
        window.closeSheetModal('modal-add-order');
    }

    function toggleAddFreeFields(chk) {
        const bf = document.getElementById('add_billing_fields');
        if (bf) {
            bf.style.display = chk.checked ? 'none' : 'block';
        }
    }

    // Deliverable Tab Switching in Create Modal
    function switchAddDeliverableTab(tab) {
        const tabs = ['folder', 'file', 'external'];
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-add-${t}-btn`);
            const content = document.getElementById(`tab-add-${t}-content`);
            if (t === tab) {
                btn.className = 'py-2 text-[11px] font-bold rounded-lg transition-all bg-white dark:bg-slate-800 text-teal-700 dark:text-teal-300 shadow-xs cursor-pointer';
                content.classList.remove('hidden');
            } else {
                btn.className = 'py-2 text-[11px] font-bold rounded-lg transition-all text-slate-600 dark:text-slate-400 cursor-pointer';
                content.classList.add('hidden');
            }
        });
    }

    function selectAddFolder(id, name) {
        document.getElementById('add_folder_id').value = id;
        document.getElementById('add_stored_file_id').value = '';
        document.getElementById('add_gdrive_url').value = '';
        document.getElementById('add-folder-selected-label').innerText = name;

        document.querySelectorAll('.add-folder-item').forEach(el => {
            el.classList.remove('border-teal-500', 'ring-2', 'ring-teal-500/20');
            el.querySelector('.add-folder-check')?.classList.add('hidden');
        });

        const activeCard = document.getElementById(`add-folder-card-${id}`);
        if (activeCard) {
            activeCard.classList.add('border-teal-500', 'ring-2', 'ring-teal-500/20');
            activeCard.querySelector('.add-folder-check')?.classList.remove('hidden');
        }

        const titleInput = document.getElementById('add_project_title');
        if (titleInput && (!titleInput.value || titleInput.value.startsWith('Dokumentasi - '))) {
            titleInput.value = 'Dokumentasi - ' + name;
        }
    }

    function selectAddFile(id, title) {
        document.getElementById('add_stored_file_id').value = id;
        document.getElementById('add_folder_id').value = '';
        document.getElementById('add_gdrive_url').value = '';
        document.getElementById('add-file-selected-label').innerText = title;

        document.querySelectorAll('.add-file-item').forEach(el => {
            el.classList.remove('border-teal-500', 'ring-2', 'ring-teal-500/20');
            el.querySelector('.add-file-check')?.classList.add('hidden');
        });

        const activeCard = document.getElementById(`add-file-card-${id}`);
        if (activeCard) {
            activeCard.classList.add('border-teal-500', 'ring-2', 'ring-teal-500/20');
            activeCard.querySelector('.add-file-check')?.classList.remove('hidden');
        }

        const titleInput = document.getElementById('add_project_title');
        if (titleInput && (!titleInput.value || titleInput.value.startsWith('Dokumentasi - '))) {
            titleInput.value = 'Dokumentasi - ' + title;
        }
    }

    // Deliverable Tab Switching in Edit Modal
    function switchEditDeliverableTab(tab) {
        const tabs = ['folder', 'file', 'external'];
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-edit-${t}-btn`);
            const content = document.getElementById(`tab-edit-${t}-content`);
            if (t === tab) {
                btn.className = 'py-2 text-[11px] font-bold rounded-lg transition-all bg-white dark:bg-slate-800 text-teal-700 dark:text-teal-300 shadow-xs cursor-pointer';
                content.classList.remove('hidden');
            } else {
                btn.className = 'py-2 text-[11px] font-bold rounded-lg transition-all text-slate-600 dark:text-slate-400 cursor-pointer';
                content.classList.add('hidden');
            }
        });
    }

    function selectEditFolder(id, name) {
        document.getElementById('edit_folder_id').value = id;
        document.getElementById('edit_stored_file_id').value = '';
        document.getElementById('edit_gdrive_url').value = '';
        document.getElementById('edit-folder-selected-label').innerText = name;

        document.querySelectorAll('.edit-folder-item').forEach(el => {
            el.classList.remove('border-teal-500', 'ring-2', 'ring-teal-500/20');
            el.querySelector('.edit-folder-check')?.classList.add('hidden');
        });

        const activeCard = document.getElementById(`edit-folder-card-${id}`);
        if (activeCard) {
            activeCard.classList.add('border-teal-500', 'ring-2', 'ring-teal-500/20');
            activeCard.querySelector('.edit-folder-check')?.classList.remove('hidden');
        }
    }

    function selectEditFile(id, title) {
        document.getElementById('edit_stored_file_id').value = id;
        document.getElementById('edit_folder_id').value = '';
        document.getElementById('edit_gdrive_url').value = '';
        document.getElementById('edit-file-selected-label').innerText = title;

        document.querySelectorAll('.edit-file-item').forEach(el => {
            el.classList.remove('border-teal-500', 'ring-2', 'ring-teal-500/20');
            el.querySelector('.edit-file-check')?.classList.add('hidden');
        });

        const activeCard = document.getElementById(`edit-file-card-${id}`);
        if (activeCard) {
            activeCard.classList.add('border-teal-500', 'ring-2', 'ring-teal-500/20');
            activeCard.querySelector('.edit-file-check')?.classList.remove('hidden');
        }
    }

    function openEditOrderModalFromBtn(btn) {
        if (!btn) return;
        const data = JSON.parse(btn.dataset.order);
        document.getElementById('edit_modal_title').innerText = 'Edit Proyek: ' + data.project_title;
        document.getElementById('edit_client_name').value = data.client_name || '';
        document.getElementById('edit_client_phone').value = data.client_phone || '';
        document.getElementById('edit_token').value = data.token || '';
        document.getElementById('edit_project_title').value = data.project_title || '';
        document.getElementById('edit_status').value = data.status || 'unpaid';
        document.getElementById('edit_amount').value = Math.round(Number(data.amount) || 0);
        document.getElementById('edit_discount').value = Math.round(Number(data.discount) || 0);
        document.getElementById('edit_gdrive_url').value = data.gdrive_url || '';
        document.getElementById('edit_notes').value = data.notes || '';
        document.getElementById('edit_order_form').action = '/orders/' + data.id;

        if (data.folder_id) {
            switchEditDeliverableTab('folder');
            selectEditFolder(data.folder_id, data.folder ? data.folder.name : 'Folder #' + data.folder_id);
        } else if (data.stored_file_id) {
            switchEditDeliverableTab('file');
            selectEditFile(data.stored_file_id, data.stored_file ? data.stored_file.title : 'Berkas #' + data.stored_file_id);
        } else {
            switchEditDeliverableTab('external');
        }

        window.openSheetModal('modal-edit-order');
    }
    function closeEditOrderModal() {
        window.closeSheetModal('modal-edit-order');
    }

    function openVerifyModal(orderId, clientName, projectTitle, finalAmount, proofUrl, walletId) {
        document.getElementById('verify_client_title').innerText = 'Klien: ' + clientName + ' • ' + projectTitle;
        document.getElementById('verify_amount_display').innerText = 'Rp ' + Number(finalAmount).toLocaleString('id-ID');
        document.getElementById('verify_proof_img').src = proofUrl;
        if (walletId) {
            document.getElementById('verify_wallet_select').value = walletId;
        }
        document.getElementById('approve_form').action = '/orders/' + orderId + '/verify';
        document.getElementById('reject_form').action = '/orders/' + orderId + '/reject';
        document.getElementById('reject_form').classList.add('hidden');

        window.openSheetModal('modal-verify-proof');
    }
    function closeVerifyModal() {
        window.closeSheetModal('modal-verify-proof');
    }

    function toggleRejectBox() {
        const rf = document.getElementById('reject_form');
        rf.classList.toggle('hidden');
    }

    function zoomCurrentProof() {
        const img = document.getElementById('verify_proof_img');
        if (img && img.src) {
            window.open(img.src, '_blank');
        }
    }

    function openProofZoomModal(url, clientName) {
        window.open(url, '_blank');
    }

    function openSettingsModal() {
        window.openSheetModal('modal-settings');
    }
    function closeSettingsModal() {
        window.closeSheetModal('modal-settings');
    }

    // Auto-open modal if redirected from SwanDrive
    window.addEventListener('DOMContentLoaded', () => {
        const preselectedFolderId = "{{ $preselectedFolderId ?? '' }}";
        const preselectedFileId = "{{ $preselectedFileId ?? '' }}";
        const autoCreate = {{ !empty($autoCreate) ? 'true' : 'false' }};

        if (preselectedFolderId) {
            openAddOrderModal();
            switchAddDeliverableTab('folder');
            const folderCard = document.getElementById(`add-folder-card-${preselectedFolderId}`);
            if (folderCard) {
                folderCard.click();
            }
        } else if (preselectedFileId) {
            openAddOrderModal();
            switchAddDeliverableTab('file');
            const fileCard = document.getElementById(`add-file-card-${preselectedFileId}`);
            if (fileCard) {
                fileCard.click();
            }
        } else if (autoCreate) {
            openAddOrderModal();
        }
    });
</script>
@endsection
