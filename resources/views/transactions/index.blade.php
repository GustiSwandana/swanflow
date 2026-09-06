@extends('layouts.mobile')

@section('header_left')
    <div class="flex items-center gap-2.5">
        <a href="{{ route('dashboard') }}" class="min-w-[40px] min-h-[40px] -ml-2 flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition-all" aria-label="Kembali ke Beranda">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                Transaksi Terakhir
            </h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">Riwayat Transaksi</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    <!-- Screen 2: Top Bar with Month Filter & Quick Search -->
    <div class="flex items-center justify-between gap-2">
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
            <div class="relative flex items-center">
                <input type="month" 
                       name="month" 
                       value="{{ request('month', now()->format('Y-m')) }}" 
                       onchange="document.getElementById('month-form').submit()" 
                       class="min-h-[42px] pl-9 pr-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-bold text-slate-800 dark:text-slate-100 shadow-2xs focus:outline-hidden focus:border-emerald-500 cursor-pointer">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
            <select name="wallet_id" onchange="this.form.submit()" class="min-h-[42px] text-xs font-bold text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl px-3 py-2 shadow-2xs focus:outline-hidden focus:border-emerald-500">
                <option value="">Semua Dompet</option>
                @foreach($wallets as $w)
                    <option value="{{ $w->id }}" {{ $currentWalletId == $w->id ? 'selected' : '' }}>
                        {{ $w->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Screen 2: Horizontal Category Filter Bar (Semua, Makanan, Transportasi, Hiburan, dll) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
        <!-- Tab: Semua -->
        @php
            $isAllActive = empty($currentCategoryId);
        @endphp
        <a href="{{ route('transactions.index', array_merge(request()->except('category_id', 'page'), [])) }}"
           class="min-h-[40px] px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center {{ $isAllActive ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800' }}">
            Semua
        </a>

        @foreach($categories as $cat)
            @php
                $isActive = (string)$currentCategoryId === (string)$cat->id;
            @endphp
            <a href="{{ route('transactions.index', array_merge(request()->except('category_id', 'page'), ['category_id' => $cat->id])) }}"
               class="min-h-[40px] px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 {{ $isActive ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800' }}">
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>

    <!-- Type Sub-filter Pills (Semua, Pengeluaran, Pemasukan, Transfer) -->
    <div class="flex items-center gap-1.5">
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
           class="px-3 py-1 rounded-xl text-[11px] font-semibold transition-colors {{ $currentType === 'all' ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400' }}">
            Semua Tipe
        </a>
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'expense'])) }}"
           class="px-3 py-1 rounded-xl text-[11px] font-semibold transition-colors {{ $currentType === 'expense' ? 'bg-rose-500 text-white' : 'text-slate-500 hover:text-rose-500 dark:text-slate-400' }}">
            Pengeluaran
        </a>
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'income'])) }}"
           class="px-3 py-1 rounded-xl text-[11px] font-semibold transition-colors {{ $currentType === 'income' ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:text-emerald-500 dark:text-slate-400' }}">
            Pemasukan
        </a>
        <a href="{{ route('transactions.index', array_merge(request()->except('type', 'page'), ['type' => 'transfer'])) }}"
           class="px-3 py-1 rounded-xl text-[11px] font-semibold transition-colors {{ $currentType === 'transfer' ? 'bg-slate-700 text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400' }}">
            Transfer
        </a>
    </div>

    <!-- Screen 2: Transaction List Container -->
    <div class="space-y-2.5">
        @forelse($transactions as $tx)
            @php
                $isIncome = $tx->type === \App\Enums\TransactionType::Income || $tx->type === 'income';
                $isTransfer = $tx->type === \App\Enums\TransactionType::Transfer || $tx->type === 'transfer';
            @endphp
            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-2xs flex items-center justify-between hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Icon Square Container (Dark themed, no blue) -->
                    <div class="w-12 h-12 rounded-2xl {{ $isIncome ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40' : ($isTransfer ? 'bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-400 border border-teal-200 dark:border-teal-900/40' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700/60') }} flex items-center justify-center shrink-0 shadow-xs">
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
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                            {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }} • 
                            @if($isTransfer)
                                <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $tx->wallet->name ?? 'Dompet' }} ➔ {{ $tx->targetWallet->name ?? 'Dompet' }}</span>
                            @else
                                <span class="font-medium text-slate-600 dark:text-slate-400">{{ $tx->wallet->name ?? 'Dompet' }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 pl-2">
                    <div class="text-right">
                        <span class="text-sm font-extrabold {{ $isIncome ? 'text-emerald-500 dark:text-emerald-400' : ($isTransfer ? 'text-slate-700 dark:text-slate-300' : 'text-rose-500 dark:text-rose-400') }} block">
                            {{ $isIncome ? '+ ' : ($isTransfer ? '' : '- ') }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Edit Button -->
                    <button type="button" 
                            onclick='openEditTransactionModal({
                                id: {{ $tx->id }},
                                type: "{{ is_string($tx->type) ? $tx->type : $tx->type->value }}",
                                amount: {{ $tx->amount }},
                                wallet_id: {{ $tx->wallet_id }},
                                target_wallet_id: {{ $tx->target_wallet_id ?: "null" }},
                                category_id: {{ $tx->category_id ?: "null" }},
                                date: "{{ \Carbon\Carbon::parse($tx->date)->format("Y-m-d") }}",
                                description: @json($tx->description ?? "")
                            })'
                            aria-label="Edit transaksi" 
                            class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </button>

                    <!-- Delete Button -->
                    <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Saldo dompet akan disesuaikan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" aria-label="Hapus transaksi" class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center text-slate-300 dark:text-slate-600 hover:text-rose-500 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 active:scale-95 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center space-y-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800">
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
