@extends('layouts.mobile')

@section('custom_header')
    <!-- Apple iOS Liquid Glass Header -->
    <div class="relative overflow-hidden bg-gradient-to-b from-emerald-600/95 via-emerald-600/85 to-teal-700/90 dark:from-slate-900/95 dark:via-emerald-950/90 dark:to-slate-950/95 text-white px-5 pb-8 border-b border-white/20 dark:border-white/10 rounded-b-[36px] shadow-2xl backdrop-blur-3xl transition-colors duration-200" style="padding-top: max(3.5rem, calc(var(--sat, 0px) + 0.85rem));">
        <!-- Specular Rim -->
        <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

        <!-- Ambient Liquid Orbs -->
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none animate-liquid-orb-1"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-teal-500/20 rounded-full blur-3xl pointer-events-none animate-liquid-orb-2"></div>

        <!-- Top Navigation Bar -->
        <div class="relative z-10 flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-[18px] liquid-glass bg-white/15 hover:bg-white/25 active:scale-95 flex items-center justify-center transition-all border border-white/30 shrink-0 shadow-xs ios-press" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                        Riwayat Transaksi
                    </h1>
                    <span class="text-[11px] font-semibold text-emerald-300/80">Kelola riwayat finansial</span>
                </div>
            </div>

            <!-- Header Action / Theme Toggle -->
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
@endsection

@section('content')
<div class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-2xl rounded-t-[36px] pt-5 px-4 pb-[max(6.5rem,calc(5.5rem+var(--sab,0px)))] shadow-2xl -mt-5 relative z-10 border-t border-slate-200/80 dark:border-white/10 flex-1 flex flex-col min-h-full space-y-6 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- Grab Handle -->
    <div class="w-10 h-1.5 bg-slate-300/80 dark:bg-slate-700/80 rounded-full mx-auto mb-1"></div>

    <!-- Finance Modules Grid / Scroll (Liquid Glass Style) -->
    <div class="flex overflow-x-auto no-scrollbar gap-4 pb-2 -mx-1 px-1 snap-x">
        @php
            $financeModules = [
                ['name' => 'Laporan', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'route' => 'reports.index', 'classes' => 'text-indigo-500 group-hover:bg-indigo-500 dark:text-indigo-400'],
                ['name' => 'Dompet', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'route' => 'wallets.index', 'classes' => 'text-blue-500 group-hover:bg-blue-500 dark:text-blue-400'],
                ['name' => 'Anggaran', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z', 'route' => 'budgets.index', 'classes' => 'text-orange-500 group-hover:bg-orange-500 dark:text-orange-400'],
                ['name' => 'Langganan', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'route' => 'subscriptions.index', 'classes' => 'text-purple-500 group-hover:bg-purple-500 dark:text-purple-400'],
                ['name' => 'Hutang', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'route' => 'debts.index', 'classes' => 'text-rose-500 group-hover:bg-rose-500 dark:text-rose-400'],
                ['name' => 'Investasi', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'route' => 'investments.index', 'classes' => 'text-emerald-500 group-hover:bg-emerald-500 dark:text-emerald-400'],
                ['name' => 'Kalkulator', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'route' => 'calculator.index', 'classes' => 'text-teal-500 group-hover:bg-teal-500 dark:text-teal-400'],
                ['name' => 'Kategori', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z', 'route' => 'categories.index', 'classes' => 'text-amber-500 group-hover:bg-amber-500 dark:text-amber-400'],
            ];
        @endphp
        @foreach($financeModules as $mod)
            <a href="{{ route($mod['route']) }}" class="flex flex-col items-center gap-2 min-w-[72px] shrink-0 group active:scale-95 transition-all snap-start cursor-pointer">
                <div class="w-[60px] h-[60px] rounded-[22px] bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/60 dark:border-white/10 shadow-xs flex items-center justify-center {{ $mod['classes'] }} group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mod['icon'] }}"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $mod['name'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Filter & Tools Section -->
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-2">
            <!-- Month Picker -->
            <form method="GET" action="{{ route('transactions.index') }}" class="flex items-center w-full min-w-0" id="month-form">
                @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
                @if(request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
                @if(request('wallet_id'))<input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">@endif
                <div class="relative w-full min-w-0">
                    <input type="month" 
                           name="month" 
                           value="{{ request('month', now()->format('Y-m')) }}" 
                           onchange="document.getElementById('month-form').submit()" 
                           class="relative w-full h-11 pl-8 pr-1.5 appearance-none bg-white/90 dark:bg-slate-900/90 border border-slate-200/80 dark:border-white/10 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-100 shadow-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all cursor-pointer [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-clear-button]:hidden"
                           onclick="try { this.showPicker() } catch(e) {}">
                    <div class="absolute left-2.5 top-1/2 -translate-y-1/2 text-emerald-500 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </form>

            <!-- Wallet Dropdown -->
            <form method="GET" action="{{ route('transactions.index') }}" class="flex items-center w-full min-w-0">
                @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
                @if(request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
                @if(request('month'))<input type="hidden" name="month" value="{{ request('month') }}">@endif
                <div class="relative w-full min-w-0">
                    <select name="wallet_id" onchange="this.form.submit()" class="w-full h-11 pl-8 pr-5.5 appearance-none bg-white/90 dark:bg-slate-900/90 border border-slate-200/80 dark:border-white/10 rounded-2xl text-[11px] font-bold text-slate-800 dark:text-slate-100 shadow-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all cursor-pointer truncate" style="-webkit-appearance: none; appearance: none;">
                        <option value="">Semua Dompet</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}" {{ $currentWalletId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-2.5 top-1/2 -translate-y-1/2 text-cyan-500 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </form>
        </div>

        <!-- AI Receipt Scanner -->
        <button type="button" 
                onclick="openReceiptScannerModal()" 
                class="w-full relative overflow-hidden bg-gradient-to-r from-teal-500 to-emerald-500 p-4 rounded-2xl shadow-lg shadow-emerald-500/20 text-white flex items-center justify-between group active:scale-95 transition-all cursor-pointer">
            <div class="absolute -right-4 -top-8 w-24 h-24 bg-white/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                </div>
                <div class="text-left">
                    <span class="block text-sm font-bold leading-tight">Pindai Struk Otomatis (AI)</span>
                    <span class="block text-[10px] text-teal-50 mt-0.5">Ekstrak otomatis nominal & kategori</span>
                </div>
            </div>
            <svg class="w-5 h-5 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- Filters: Type & Category -->
    <div class="space-y-3.5 pt-1">
        <!-- Type Segmented Control -->
        <div class="ios-segmented-track p-1 rounded-2xl flex items-stretch w-full border border-slate-200/80 dark:border-white/10 shadow-inner">
            <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
               class="flex-1 min-w-0 py-2.5 text-center text-xs font-bold rounded-xl transition-all flex items-center justify-center ios-press {{ $currentType === 'all' ? 'ios-segmented-thumb bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-black ring-1 ring-black/5 dark:ring-white/15' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'expense'])) }}"
               class="flex-1 min-w-0 py-2.5 text-center text-xs font-bold rounded-xl transition-all flex items-center justify-center ios-press {{ $currentType === 'expense' ? 'bg-rose-500 text-white shadow-sm font-black ring-1 ring-rose-400/30' : 'text-slate-500 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400' }}">
                Keluar
            </a>
            <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'income'])) }}"
               class="flex-1 min-w-0 py-2.5 text-center text-xs font-bold rounded-xl transition-all flex items-center justify-center ios-press {{ $currentType === 'income' ? 'bg-emerald-500 text-white shadow-sm font-black ring-1 ring-emerald-400/30' : 'text-slate-500 hover:text-emerald-500 dark:text-slate-400 dark:hover:text-emerald-400' }}">
                Masuk
            </a>
            <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'transfer'])) }}"
               class="flex-1 min-w-0 py-2.5 text-center text-xs font-bold rounded-xl transition-all flex items-center justify-center ios-press {{ $currentType === 'transfer' ? 'bg-indigo-500 text-white shadow-sm font-black ring-1 ring-indigo-400/30' : 'text-slate-500 hover:text-indigo-400 dark:text-slate-400 dark:hover:text-indigo-300' }}">
                Transfer
            </a>
        </div>

        <!-- Category Horizontal Scroll -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1 snap-x">
            @php $isAllActive = empty($currentCategoryId); @endphp
            <a href="{{ route('transactions.index', array_merge(request()->except('category_id', 'page'), [])) }}"
               class="h-8 px-4 rounded-full text-xs font-bold transition-all shrink-0 flex items-center justify-center ios-press snap-start {{ $isAllActive ? 'bg-emerald-600 dark:bg-emerald-500 text-white shadow-sm shadow-emerald-600/30 border border-emerald-500/40' : 'bg-white/90 dark:bg-slate-900/90 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>Semua</span>
            </a>
            @foreach($categories as $cat)
                @php $isActive = (string)$currentCategoryId === (string)$cat->id; @endphp
                <a href="{{ route('transactions.index', array_merge(request()->except('category_id', 'page'), ['category_id' => $cat->id])) }}"
                   class="h-8 px-3.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 ios-press snap-start {{ $isActive ? 'bg-emerald-600 dark:bg-emerald-500 text-white shadow-sm shadow-emerald-600/30 border border-emerald-500/40' : 'bg-white/90 dark:bg-slate-900/90 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    @if(!empty($cat->color))
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $cat->color }};"></span>
                    @endif
                    <span>{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Transaction List grouped by Date -->
    <div class="space-y-5 flex-1 pt-1">
        @php $currentDate = null; @endphp
        @forelse($transactions as $tx)
            @php
                $isIncome = $tx->type === \App\Enums\TransactionType::Income || $tx->type === 'income';
                $isTransfer = $tx->type === \App\Enums\TransactionType::Transfer || $tx->type === 'transfer';
                $txData = [
                    'id' => $tx->id,
                    'type' => is_string($tx->type) ? $tx->type : $tx->type->value,
                    'amount' => (float) $tx->amount,
                    'wallet_id' => $tx->wallet_id,
                    'target_wallet_id' => $tx->target_wallet_id,
                    'category_id' => $tx->category_id,
                    'category_name' => $tx->category->name ?? '',
                    'date' => \Carbon\Carbon::parse($tx->date)->format('Y-m-d'),
                    'date_formatted' => \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y'),
                    'description' => $tx->description ?: ($tx->category->name ?? ($isTransfer ? 'Transfer Antar Dompet' : 'Transaksi')),
                ];
            @endphp
            
            @if($currentDate !== $txData['date_formatted'])
                @if($currentDate !== null) </div></div> @endif
                @php $currentDate = $txData['date_formatted']; @endphp
                <div class="relative">
                    <div class="sticky top-[140px] z-20 py-1 inline-block bg-slate-100/90 dark:bg-slate-950/90 backdrop-blur-md mb-2 rounded-full px-3">
                        <span class="text-[10px] font-black tracking-wider text-slate-500 dark:text-slate-400 uppercase">{{ $currentDate }}</span>
                    </div>
                    <div class="liquid-card bg-white/90 dark:bg-slate-900/85 backdrop-blur-2xl rounded-[26px] border border-slate-200/80 dark:border-white/10 shadow-xs overflow-hidden divide-y divide-slate-100 dark:divide-white/5">
            @endif

            <div data-transaction-row="{{ $tx->id }}" 
                 data-tx="{{ json_encode($txData) }}"
                 class="p-3.5 flex items-center justify-between hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                
                <!-- Left: Icon & Info -->
                <div class="flex items-center gap-3.5 min-w-0 flex-1 cursor-pointer" onclick="openEditFromDataset(this, event)">
                    <!-- Icon Box -->
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 border 
                        {{ $isIncome 
                            ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20' 
                            : ($isTransfer 
                                ? 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-100 dark:border-cyan-500/20' 
                                : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-100 dark:border-slate-700/60') }}">
                        <x-category-icon :category="$tx->category" :type="$tx->type" :name="$tx->description ?: ($tx->category->name ?? '')" class="w-5 h-5" />
                    </div>
                    
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                            @if($isTransfer)
                                {{ $tx->description ?: 'Transfer Antar Dompet' }}
                            @else
                                {{ $tx->description ?: ($tx->category->name ?? 'Transaksi') }}
                            @endif
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">
                                @if($isTransfer)
                                    {{ $tx->wallet->name ?? 'Dompet' }} ➔ {{ $tx->targetWallet->name ?? 'Dompet' }}
                                @else
                                    {{ $tx->wallet->name ?? 'Dompet' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Amount & Actions -->
                <div class="flex items-center gap-2 shrink-0 pl-3">
                    <div class="text-right cursor-pointer" onclick="openEditFromDataset(this, event)">
                        <span class="text-xs sm:text-[13px] font-black tracking-tight {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-cyan-600 dark:text-cyan-400' : 'text-slate-900 dark:text-white') }}">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1 border-l border-slate-200/80 dark:border-white/10 pl-2 ml-1">
                        <!-- Edit -->
                        <button type="button" onclick="openEditFromDataset(this, event)" aria-label="Edit transaksi" 
                                class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-emerald-500 bg-slate-50 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-500/20 rounded-lg transition-colors ios-press">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                        </button>
                        <!-- Delete -->
                        <button type="button" onclick="openDeleteFromDataset(this, event)" aria-label="Hapus transaksi" 
                                class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-500 bg-slate-50 hover:bg-rose-50 dark:bg-slate-800 dark:hover:bg-rose-500/20 rounded-lg transition-colors ios-press">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="liquid-card bg-white/90 dark:bg-slate-900/85 backdrop-blur-2xl rounded-[28px] border border-slate-200/80 dark:border-white/10 p-8 text-center flex flex-col items-center justify-center min-h-[220px]">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 flex items-center justify-center mb-4 mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Riwayat Kosong</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Belum ada transaksi pada kriteria ini.</p>
            </div>
        @endforelse
        @if($currentDate !== null)
            </div></div>
        @endif
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
