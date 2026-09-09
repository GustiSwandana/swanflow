@extends('layouts.mobile')

@section('header_left')
    <div class="flex items-center gap-2.5">
        <a href="{{ route('dashboard') }}" class="min-w-[42px] min-h-[42px] w-10.5 h-10.5 flex items-center justify-center text-slate-700 dark:text-slate-200 rounded-[18px] liquid-glass border border-white/60 dark:border-white/10 ios-press transition-all shadow-xs" aria-label="Kembali ke Beranda">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                Transaksi Terakhir
            </h1>
            <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Riwayat & Mutasi Keuangan</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4 pt-1">

    <!-- Top Bar with Month Filter & Wallet Dropdown -->
    <div class="space-y-2.5">
        <div class="grid grid-cols-2 gap-2">
            <!-- Month Picker Dropdown/Input Button -->
            <form method="GET" action="{{ route('transactions.index') }}" class="flex items-center" id="month-form">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                @if(request('wallet_id'))
                    <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
                @endif
                <div class="relative w-full flex items-center">
                    <input type="month" 
                           name="month" 
                           value="{{ request('month', now()->format('Y-m')) }}" 
                           onchange="document.getElementById('month-form').submit()" 
                           class="w-full min-h-[44px] pl-9.5 pr-2 py-2.5 bg-white/80 dark:bg-slate-900/70 border border-white/60 dark:border-white/10 rounded-[20px] text-xs font-bold text-slate-800 dark:text-slate-100 shadow-xs backdrop-blur-2xl focus:outline-hidden focus:border-emerald-500 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
            </form>

            <!-- Wallet Filter Dropdown -->
            <form method="GET" action="{{ route('transactions.index') }}" class="flex items-center">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                @if(request('month'))
                    <input type="hidden" name="month" value="{{ request('month') }}">
                @endif
                <select name="wallet_id" onchange="this.form.submit()" class="w-full min-h-[44px] text-xs font-bold text-slate-800 dark:text-slate-100 bg-white/80 dark:bg-slate-900/70 border border-white/60 dark:border-white/10 rounded-[20px] px-3.5 py-2.5 shadow-xs backdrop-blur-2xl focus:outline-hidden focus:border-emerald-500 truncate">
                    <option value="">Semua Dompet</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ $currentWalletId == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Quick AI Scan Struk Trigger Banner -->
        <button type="button" 
                onclick="openReceiptScannerModal()" 
                class="w-full py-3 px-3.5 rounded-[22px] bg-gradient-to-r from-teal-500/15 via-emerald-500/10 to-teal-500/15 dark:from-teal-950/40 dark:via-emerald-950/30 dark:to-teal-950/40 text-teal-700 dark:text-teal-300 border border-teal-300/50 dark:border-teal-700/50 flex items-center justify-between ios-press transition-all cursor-pointer shadow-xs backdrop-blur-2xl group"
                title="Pindai Struk / Bukti Bayar Otomatis">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-[14px] bg-gradient-to-br from-teal-400 to-emerald-600 text-white flex items-center justify-center font-bold shrink-0 shadow-md shadow-teal-500/25">
                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                </div>
                <div class="text-left min-w-0">
                    <span class="text-xs font-bold text-teal-800 dark:text-teal-200 block leading-tight truncate">📸 Pindai Struk / Bukti Bayar Otomatis</span>
                    <span class="text-[10px] text-teal-600 dark:text-teal-400 block truncate">Ekstrak otomatis nominal, tanggal & kategori</span>
                </div>
            </div>
            <span class="text-xs font-black text-teal-600 dark:text-teal-400 group-hover:translate-x-0.5 transition-transform shrink-0 ml-2">
                Scan AI ➔
            </span>
        </button>
    </div>

    <!-- Apple iOS Horizontal Category Filter Chips -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
        <!-- Tab: Semua -->
        @php
            $isAllActive = empty($currentCategoryId);
        @endphp
        <a href="{{ route('transactions.index', array_merge(request()->except('category_id', 'page'), [])) }}"
           class="min-h-[38px] px-4 py-2 rounded-[18px] text-xs font-extrabold transition-all shrink-0 flex items-center ios-press {{ $isAllActive ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'liquid-card bg-white/75 dark:bg-slate-900/65 text-slate-700 dark:text-slate-300 border border-white/60 dark:border-white/10 backdrop-blur-2xl' }}">
            Semua
        </a>

        @foreach($categories as $cat)
            @php
                $isActive = (string)$currentCategoryId === (string)$cat->id;
            @endphp
            <a href="{{ route('transactions.index', array_merge(request()->except('category_id', 'page'), ['category_id' => $cat->id])) }}"
               class="min-h-[38px] px-4 py-2 rounded-[18px] text-xs font-extrabold transition-all shrink-0 flex items-center gap-1.5 ios-press {{ $isActive ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'liquid-card bg-white/75 dark:bg-slate-900/65 text-slate-700 dark:text-slate-300 border border-white/60 dark:border-white/10 backdrop-blur-2xl' }}">
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>

    <!-- Apple iOS Segmented Control: Type Sub-filter Pills -->
    <div class="ios-segmented-track p-1 rounded-[20px] flex items-center gap-1 w-full">
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
           class="flex-1 py-1.5 px-2 text-center text-xs font-bold rounded-[16px] transition-all ios-press {{ $currentType === 'all' ? 'ios-segmented-thumb text-slate-900 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400' }}">
            Semua
        </a>
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'expense'])) }}"
           class="flex-1 py-1.5 px-2 text-center text-xs font-bold rounded-[16px] transition-all ios-press {{ $currentType === 'expense' ? 'bg-rose-500 text-white shadow-xs' : 'text-slate-500 hover:text-rose-500 dark:text-slate-400' }}">
            Keluar
        </a>
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'income'])) }}"
           class="flex-1 py-1.5 px-2 text-center text-xs font-bold rounded-[16px] transition-all ios-press {{ $currentType === 'income' ? 'bg-emerald-500 text-white shadow-xs' : 'text-slate-500 hover:text-emerald-500 dark:text-slate-400' }}">
            Masuk
        </a>
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'transfer'])) }}"
           class="flex-1 py-1.5 px-2 text-center text-xs font-bold rounded-[16px] transition-all ios-press {{ $currentType === 'transfer' ? 'bg-slate-700 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400' }}">
            Transfer
        </a>
    </div>

    <!-- Apple iOS Inset Grouped Transaction List -->
    <div class="liquid-card rounded-[28px] bg-white/85 dark:bg-slate-900/80 border border-white/60 dark:border-white/10 shadow-md divide-y divide-slate-100/80 dark:divide-slate-800/70 overflow-hidden backdrop-blur-2xl">
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
            <div data-transaction-row="{{ $tx->id }}" 
                 data-tx="{{ json_encode($txData) }}"
                 class="p-3.5 flex items-center justify-between hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors">
                <div class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer"
                     onclick="openEditFromDataset(this, event)">
                    <!-- Squircle Category Icon Box -->
                    <div class="w-11 h-11 rounded-[18px] {{ $isIncome ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-900/40' : ($isTransfer ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-400 border border-teal-200/80 dark:border-teal-900/40' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/60') }} flex items-center justify-center shrink-0 shadow-2xs">
                        <x-category-icon :category="$tx->category" :type="$tx->type" :name="$tx->description ?: ($tx->category->name ?? '')" class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-white truncate">
                            @if($isTransfer)
                                {{ $tx->description ?: 'Transfer Antar Dompet' }}
                            @else
                                {{ $tx->description ?: ($tx->category->name ?? 'Transaksi') }}
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                            {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }} • 
                            @if($isTransfer)
                                <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $tx->wallet->name ?? 'Dompet' }} ➔ {{ $tx->targetWallet->name ?? 'Dompet' }}</span>
                            @else
                                <span class="font-medium text-slate-600 dark:text-slate-400">{{ $tx->wallet->name ?? 'Dompet' }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0 pl-2">
                    <div class="text-right cursor-pointer"
                         onclick="openEditFromDataset(this, event)">
                        <span class="text-sm font-black {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : ($isTransfer ? 'text-teal-600 dark:text-teal-400' : 'text-rose-600 dark:text-rose-400') }} block">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Edit Button -->
                    <button type="button" 
                            onclick="openEditFromDataset(this, event)"
                            aria-label="Edit transaksi" 
                            class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 ios-press transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </button>

                    <!-- Delete Button with Confirmation Modal -->
                    <button type="button" 
                            onclick="openDeleteFromDataset(this, event)"
                            aria-label="Hapus transaksi" 
                            class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center text-slate-300 dark:text-slate-600 hover:text-rose-500 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 ios-press transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="p-8 text-center space-y-2">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tidak ada transaksi yang cocok</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">Coba ubah filter atau catat transaksi baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $transactions->links() }}
    </div>

</div>
@endsection

