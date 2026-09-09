@extends('layouts.mobile')

@section('title', 'Kalkulator & Simulasi')

@section('custom_header')
    <!-- Fintech Header (Emerald in Light Mode, Slate in Dark Mode) -->
    <div class="bg-gradient-to-b from-emerald-600 to-emerald-700 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 border-b border-emerald-700 dark:border-slate-800/80 text-white px-5 pb-6 relative transition-colors" style="padding-top: max(3rem, calc(var(--sat, 0px) + 0.75rem));">
        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dashboard') }}" class="min-w-[40px] min-h-[40px] -ml-2 flex items-center justify-center text-white/80 hover:text-white rounded-full hover:bg-white/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 active:scale-95 transition-all" aria-label="Kembali ke Beranda">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg font-extrabold text-white tracking-tight leading-tight">
                        Kalkulator &amp; Simulasi
                    </h1>
                    <p class="text-[11px] text-emerald-100 dark:text-slate-400 font-medium leading-none mt-0.5">
                        Simulasi Skenario &amp; Patungan
                    </p>
                </div>
            </div>

            <!-- Header Actions: Reset & Theme Toggle -->
            <div class="flex items-center gap-1.5">
                <button type="button" 
                        onclick="resetCurrentTab()" 
                        title="Reset Kalkulator"
                        aria-label="Reset Kalkulator" 
                        class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center rounded-full text-white/80 hover:text-white bg-white/15 hover:bg-white/25 border border-white/20 dark:text-slate-300 dark:hover:text-white dark:bg-slate-800/80 dark:border-slate-700/60 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>
                <button type="button" 
                        id="theme-toggle-btn"
                        onclick="toggleSwanFlowTheme()" 
                        aria-label="Ganti Tema Gelap atau Terang" 
                        class="min-w-[36px] min-h-[36px] w-9 h-9 flex items-center justify-center rounded-full text-white/90 hover:text-white bg-white/15 hover:bg-white/25 border border-white/20 dark:text-slate-300 dark:hover:text-white dark:bg-slate-800/80 dark:border-slate-700/60 active:scale-95 transition-all">
                    <svg class="w-4 h-4 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Segmented Tab Navigation: [ Simulasi Skenario | Hitung Patungan ] -->
        <div class="mt-4 flex justify-center">
            <div class="bg-black/20 dark:bg-slate-900/90 p-1 rounded-2xl flex w-full max-w-sm border border-white/20 dark:border-slate-800 shadow-inner backdrop-blur-xs">
                <button type="button" 
                        id="tab-btn-simulasi" 
                        onclick="switchTab('simulasi')"
                        class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all bg-white text-emerald-700 dark:bg-emerald-500 dark:text-slate-950 font-extrabold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    Simulasi Skenario
                </button>
                <button type="button" 
                        id="tab-btn-patungan" 
                        onclick="switchTab('patungan')"
                        class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all text-white/80 hover:text-white dark:text-slate-400 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Hitung Patungan
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Main Sheet Container (rounded-t-[32px]) -->
<div class="bg-slate-50 dark:bg-slate-900 rounded-t-[32px] pt-5 px-4 pb-[max(5.5rem,calc(4.75rem+var(--sab)))] shadow-2xl -mt-4 relative z-10 border-t border-slate-200 dark:border-slate-800/80 flex-1 flex flex-col min-h-full space-y-5 text-slate-800 dark:text-white transition-colors animate-swan-in">
    <!-- ========================================== -->
    <!-- TAB 1: SIMULASI SKENARIO FINANSIAL        -->
    <!-- ========================================== -->
    <div id="section-simulasi" class="space-y-4">
        <!-- 1. Saldo Awal Card -->
        <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 border border-slate-200 dark:border-slate-700/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block leading-tight">Saldo Awal Simulasi</span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-400">Titik mula perhitungan skenario</span>
                    </div>
                </div>

                <!-- Quick Button to Reload Actual Balance -->
                <button type="button" 
                        onclick="useActualBalance()"
                        class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 px-2.5 py-1 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/60 active:scale-95 transition-all">
                    Gunakan Saldo Riil
                </button>
            </div>

            <div class="relative rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 p-2.5 flex items-center focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                <span class="text-sm font-extrabold text-slate-400 mr-2">Rp</span>
                <input id="sim-start-balance" 
                       type="number" 
                       inputmode="decimal" 
                       value="{{ (int) $totalBalance }}" 
                       oninput="recalculateSimulation()"
                       placeholder="0"
                       class="w-full text-lg font-black text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-400 dark:placeholder-slate-600">
            </div>

            @if($wallets->count() > 0)
                <div class="pt-1 flex items-center gap-1.5 overflow-x-auto no-scrollbar text-[11px] text-slate-500 dark:text-slate-400">
                    <span class="font-medium shrink-0">Saldo Riil:</span>
                    @foreach($wallets as $w)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-semibold shrink-0">
                            {{ $w->name }}: Rp {{ number_format($w->balance, 0, ',', '.') }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 2. Hero Projection Result Card -->
        <div class="rounded-3xl p-5 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 text-white shadow-xl border border-emerald-500/20 relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase tracking-wider font-extrabold text-emerald-300">
                        Proyeksi Saldo Akhir
                    </span>
                    <span id="sim-health-badge" class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Surplus Positif
                    </span>
                </div>

                <div class="flex items-baseline gap-1">
                    <span class="text-base font-bold text-white/70">Rp</span>
                    <h2 id="sim-projected-display" class="text-3xl font-black tracking-tight text-white leading-none">
                        {{ number_format($totalBalance, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- Simulation Metrics Grid -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-white/10 text-center">
                    <div class="bg-white/5 rounded-xl p-2">
                        <span class="text-[10px] text-white/60 block font-medium">Rencana Masuk</span>
                        <span id="sim-total-income-display" class="text-xs font-bold text-emerald-400 block mt-0.5">
                            +Rp 0
                        </span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-2">
                        <span class="text-[10px] text-white/60 block font-medium">Rencana Keluar</span>
                        <span id="sim-total-expense-display" class="text-xs font-bold text-rose-400 block mt-0.5">
                            -Rp 0
                        </span>
                    </div>
                    <div class="bg-white/5 rounded-xl p-2">
                        <span class="text-[10px] text-white/60 block font-medium">Net Skenario</span>
                        <span id="sim-net-diff-display" class="text-xs font-bold text-white block mt-0.5">
                            Rp 0
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Quick Scenario Presets -->
        <div class="space-y-1.5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pintasan Tambah Cepat</span>
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1">
                <button type="button" onclick="addQuickScenario('income', 'Gaji / Inflow', 5000000)" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 text-xs font-bold shrink-0 hover:bg-emerald-100 active:scale-95 transition-all">
                    + Gaji (5jt)
                </button>
                <button type="button" onclick="addQuickScenario('income', 'Freelance / Proyek', 1500000)" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50 text-xs font-bold shrink-0 hover:bg-emerald-100 active:scale-95 transition-all">
                    + Proyek (1.5jt)
                </button>
                <button type="button" onclick="addQuickScenario('expense', 'Belanja Bulanan', 1000000)" class="px-2.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50 text-xs font-bold shrink-0 hover:bg-rose-100 active:scale-95 transition-all">
                    - Belanja (1jt)
                </button>
                <button type="button" onclick="addQuickScenario('expense', 'Tagihan & Listrik', 500000)" class="px-2.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50 text-xs font-bold shrink-0 hover:bg-rose-100 active:scale-95 transition-all">
                    - Tagihan (500rb)
                </button>
                <button type="button" onclick="addQuickScenario('expense', 'Hangout / Makan', 250000)" class="px-2.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50 text-xs font-bold shrink-0 hover:bg-rose-100 active:scale-95 transition-all">
                    - Makan (250rb)
                </button>
                <button type="button" onclick="addQuickScenario('expense', 'Tabungan / Investasi', 1000000)" class="px-2.5 py-1.5 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 dark:bg-teal-950/40 dark:text-teal-400 dark:border-teal-900/50 text-xs font-bold shrink-0 hover:bg-teal-100 active:scale-95 transition-all">
                    - Investasi (1jt)
                </button>
            </div>
        </div>

        <!-- 4. Dynamic Simulation Items List -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700 dark:text-slate-200">
                    Daftar Pos Simulasi (<span id="sim-items-count">0</span>)
                </span>
                <button type="button" 
                        onclick="addSimulationItem()" 
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Pos
                </button>
            </div>

            <!-- Items Container -->
            <div id="sim-items-container" class="space-y-2.5">
                <!-- Javascript will inject simulation rows here -->
            </div>

            <!-- Empty State -->
            <div id="sim-empty-state" class="p-6 text-center bg-white dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-400 space-y-2">
                <svg class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs font-semibold">Belum ada pos simulasi yang ditambahkan.</p>
                <p class="text-[11px]">Gunakan tombol "Tambah Pos" atau pilih pintasan di atas untuk mulai menyimulasikan arus kas.</p>
            </div>
        </div>

        <!-- 5. Bottom Action Bar for Simulation -->
        <div class="pt-2 flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <!-- Copy Scenario Button -->
                <button type="button" 
                        onclick="copySimulationSummary()" 
                        class="flex-1 min-h-[46px] flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 rounded-xl text-xs font-extrabold active:scale-95 transition-all shadow-xs">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                    </svg>
                    Salin Rincian Simulasi
                </button>

                <!-- Record Net Difference Button -->
                <button type="button" 
                        onclick="recordNetDifference()" 
                        class="flex-1 min-h-[46px] flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold active:scale-95 transition-all shadow-md shadow-emerald-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Catat Selisih Net
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: KALKULATOR PATUNGAN (SPLIT BILL)    -->
    <!-- ========================================== -->
    <div id="section-patungan" class="hidden space-y-4">
        <!-- 1. Bill Input Card -->
        <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 border border-slate-200 dark:border-slate-700/80 shadow-xs space-y-3.5">
            <!-- Subtotal -->
            <div>
                <label for="split-subtotal" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Subtotal / Tagihan Kotor (Rp)
                </label>
                <div class="relative rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 p-2.5 flex items-center focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                    <span class="text-sm font-extrabold text-slate-400 mr-2">Rp</span>
                    <input id="split-subtotal" 
                           type="number" 
                           inputmode="decimal" 
                           value="200000" 
                           oninput="recalculateSplit()"
                           placeholder="0"
                           class="w-full text-xl font-black text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0 placeholder-slate-400 dark:placeholder-slate-600">
                </div>
            </div>

            <!-- Pajak (Tax / PB1 / PPN) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="split-tax-percent" class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                        Pajak (PB1 / PPN)
                    </label>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="setTaxPreset(0)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">0%</button>
                        <button type="button" onclick="setTaxPreset(10)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">10%</button>
                        <button type="button" onclick="setTaxPreset(11)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">11%</button>
                        <button type="button" onclick="setTaxPreset(12)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">12%</button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 px-3 py-2 flex items-center">
                        <input id="split-tax-percent" 
                               type="number" 
                               inputmode="decimal" 
                               value="10" 
                               oninput="recalculateSplit()"
                               class="w-full text-sm font-bold text-slate-800 dark:text-white bg-transparent border-none outline-hidden focus:ring-0">
                        <span class="text-xs font-extrabold text-slate-400">%</span>
                    </div>
                    <span id="split-tax-nominal-preview" class="text-xs font-semibold text-slate-500 dark:text-slate-400 min-w-[90px] text-right">
                        = Rp 20.000
                    </span>
                </div>
            </div>

            <!-- Service Charge -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="split-service-percent" class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                        Service Charge (Pelayanan)
                    </label>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="setServicePreset(0)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">0%</button>
                        <button type="button" onclick="setServicePreset(5)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">5%</button>
                        <button type="button" onclick="setServicePreset(7)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">7%</button>
                        <button type="button" onclick="setServicePreset(10)" class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">10%</button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 px-3 py-2 flex items-center">
                        <input id="split-service-percent" 
                               type="number" 
                               inputmode="decimal" 
                               value="5" 
                               oninput="recalculateSplit()"
                               class="w-full text-sm font-bold text-slate-800 dark:text-white bg-transparent border-none outline-hidden focus:ring-0">
                        <span class="text-xs font-extrabold text-slate-400">%</span>
                    </div>
                    <span id="split-service-nominal-preview" class="text-xs font-semibold text-slate-500 dark:text-slate-400 min-w-[90px] text-right">
                        = Rp 10.000
                    </span>
                </div>
            </div>

            <!-- Diskon & Ongkir / Biaya Lain -->
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label for="split-discount" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Diskon / Promo (-)
                    </label>
                    <div class="relative rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 px-3 py-2 flex items-center">
                        <span class="text-xs font-bold text-rose-500 mr-1">-Rp</span>
                        <input id="split-discount" 
                               type="number" 
                               inputmode="decimal" 
                               value="0" 
                               oninput="recalculateSplit()"
                               placeholder="0"
                               class="w-full text-xs font-bold text-slate-800 dark:text-white bg-transparent border-none outline-hidden focus:ring-0">
                    </div>
                </div>
                <div>
                    <label for="split-other-fee" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Ongkir / Biaya (+)
                    </label>
                    <div class="relative rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 px-3 py-2 flex items-center">
                        <span class="text-xs font-bold text-slate-400 mr-1">+Rp</span>
                        <input id="split-other-fee" 
                               type="number" 
                               inputmode="decimal" 
                               value="0" 
                               oninput="recalculateSplit()"
                               placeholder="0"
                               class="w-full text-xs font-bold text-slate-800 dark:text-white bg-transparent border-none outline-hidden focus:ring-0">
                    </div>
                </div>
            </div>

            <!-- Jumlah Orang (Stepper & Chips) -->
            <div class="space-y-2 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center justify-between">
                    <label for="split-people-count" class="text-xs font-bold text-slate-700 dark:text-slate-200">
                        Jumlah Orang / Peserta
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                onclick="changePeopleCount(-1)" 
                                aria-label="Kurangi orang"
                                class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-black active:scale-95 transition-all">
                            -
                        </button>
                        <input id="split-people-count" 
                               type="number" 
                               min="1" 
                               max="100" 
                               value="4" 
                               oninput="recalculateSplit()"
                               class="w-12 text-center text-sm font-black text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg py-1">
                        <button type="button" 
                                onclick="changePeopleCount(1)" 
                                aria-label="Tambah orang"
                                class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-black active:scale-95 transition-all">
                            +
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button type="button" onclick="setPeopleCount(2)" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">2 Orang</button>
                    <button type="button" onclick="setPeopleCount(3)" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">3 Orang</button>
                    <button type="button" onclick="setPeopleCount(4)" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">4 Orang</button>
                    <button type="button" onclick="setPeopleCount(5)" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">5 Orang</button>
                    <button type="button" onclick="setPeopleCount(6)" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 active:scale-95">6 Orang</button>
                </div>
            </div>

            <!-- Opsi Pembulatan (Rounding) -->
            <div class="space-y-1.5 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">
                    Opsi Pembulatan Rupiah
                </span>
                <div class="grid grid-cols-4 gap-1.5 text-center">
                    <button type="button" 
                            id="round-btn-0" 
                            onclick="setRounding(0)" 
                            class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all bg-emerald-600 text-white shadow-xs">
                        Pas
                    </button>
                    <button type="button" 
                            id="round-btn-100" 
                            onclick="setRounding(100)" 
                            class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200">
                        Rp 100
                    </button>
                    <button type="button" 
                            id="round-btn-500" 
                            onclick="setRounding(500)" 
                            class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200">
                        Rp 500
                    </button>
                    <button type="button" 
                            id="round-btn-1000" 
                            onclick="setRounding(1000)" 
                            class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200">
                        Rp 1.000
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Split Result Card -->
        <div class="rounded-3xl p-5 bg-gradient-to-br from-slate-900 via-teal-950 to-slate-900 text-white shadow-xl border border-teal-500/20 relative overflow-hidden space-y-4">
            <div class="relative z-10">
                <span class="text-[11px] uppercase tracking-wider font-extrabold text-teal-300 block">
                    Bagi Rata: Bagian Per Orang
                </span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-base font-bold text-white/70">Rp</span>
                    <h2 id="split-per-person-display" class="text-3xl font-black tracking-tight text-white leading-none">
                        57.500
                    </h2>
                </div>
            </div>

            <!-- Breakdown table -->
            <div class="space-y-1.5 text-xs pt-3 border-t border-white/10 text-white/80">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span id="split-summary-subtotal" class="font-bold text-white">Rp 200.000</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Pajak & Service</span>
                    <span id="split-summary-tax-service" class="font-bold text-teal-300">+Rp 30.000</span>
                </div>
                <div id="split-summary-discount-row" class="flex justify-between hidden text-rose-300">
                    <span>Diskon / Potongan</span>
                    <span id="split-summary-discount" class="font-bold">-Rp 0</span>
                </div>
                <div id="split-summary-fee-row" class="flex justify-between hidden text-amber-300">
                    <span>Ongkir / Biaya Tambahan</span>
                    <span id="split-summary-fee" class="font-bold">+Rp 0</span>
                </div>
                <div class="flex justify-between pt-1.5 border-t border-white/10 text-sm font-extrabold text-white">
                    <span>Total Tagihan Bersih</span>
                    <span id="split-summary-total">Rp 230.000</span>
                </div>
            </div>

            <!-- Gusti vs Friends Share -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/10">
                <div class="bg-white/5 rounded-xl p-2.5">
                    <span class="text-[10px] text-teal-200 block font-semibold">Porsi Gusti</span>
                    <span id="split-gusti-share" class="text-xs font-black text-white block mt-0.5">Rp 57.500</span>
                </div>
                <div class="bg-white/5 rounded-xl p-2.5">
                    <span class="text-[10px] text-amber-200 block font-semibold">Tagih Teman (<span id="split-friends-count">3</span> org)</span>
                    <span id="split-friends-total" class="text-xs font-black text-white block mt-0.5">Rp 172.500</span>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Action Bar for Split Bill -->
        <div class="pt-2 flex flex-col gap-2">
            <button type="button" 
                    onclick="copySplitWhatsAppMessage()" 
                    class="w-full min-h-[46px] flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold active:scale-95 transition-all shadow-md shadow-emerald-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v5.998z" />
                </svg>
                Salin Rincian untuk WhatsApp
            </button>

            <div class="grid grid-cols-2 gap-2">
                <button type="button" 
                        onclick="recordSplitMyShare()" 
                        class="min-h-[44px] flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold active:scale-95 transition-all shadow-xs hover:bg-slate-50">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Catat Porsi Saya
                </button>
                <button type="button" 
                        onclick="recordSplitFullBill()" 
                        class="min-h-[44px] flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold active:scale-95 transition-all shadow-xs hover:bg-slate-50">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Catat Total Tagihan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Copy Toast Notification -->
<div id="calc-toast" class="fixed top-5 inset-x-0 z-50 flex justify-center pointer-events-none transition-all duration-300 opacity-0 -translate-y-4">
    <div class="bg-slate-900/95 dark:bg-white/95 text-white dark:text-slate-900 px-4 py-2.5 rounded-2xl shadow-xl border border-white/10 dark:border-slate-200/20 text-xs font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
        <span id="calc-toast-text">Tersalin ke clipboard!</span>
    </div>
</div>

<script>
    // Initial global variables
    const ACTUAL_BALANCE = {{ (float) $totalBalance }};
    let activeTab = 'simulasi';
    let simulationItems = [];
    let splitRounding = 0;

    // Switch between tabs
    function switchTab(tab) {
        activeTab = tab;
        const btnSimulasi = document.getElementById('tab-btn-simulasi');
        const btnPatungan = document.getElementById('tab-btn-patungan');
        const secSimulasi = document.getElementById('section-simulasi');
        const secPatungan = document.getElementById('section-patungan');

        if (tab === 'simulasi') {
            btnSimulasi.className = 'flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all bg-white text-emerald-700 dark:bg-emerald-500 dark:text-slate-950 font-extrabold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer';
            btnPatungan.className = 'flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all text-white/80 hover:text-white dark:text-slate-400 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer';
            secSimulasi.classList.remove('hidden');
            secPatungan.classList.add('hidden');
        } else {
            btnPatungan.className = 'flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all bg-white text-teal-700 dark:bg-teal-400 dark:text-slate-950 font-extrabold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer';
            btnSimulasi.className = 'flex-1 py-2 text-center text-xs font-bold rounded-xl transition-all text-white/80 hover:text-white dark:text-slate-400 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer';
            secPatungan.classList.remove('hidden');
            secSimulasi.classList.add('hidden');
            recalculateSplit();
        }
    }

    function resetCurrentTab() {
        if (activeTab === 'simulasi') {
            simulationItems = [];
            document.getElementById('sim-start-balance').value = Math.round(ACTUAL_BALANCE);
            renderSimulationItems();
            recalculateSimulation();
            showToast('Simulasi telah direset');
        } else {
            document.getElementById('split-subtotal').value = 200000;
            document.getElementById('split-tax-percent').value = 10;
            document.getElementById('split-service-percent').value = 5;
            document.getElementById('split-discount').value = 0;
            document.getElementById('split-other-fee').value = 0;
            document.getElementById('split-people-count').value = 4;
            setRounding(0);
            recalculateSplit();
            showToast('Patungan telah direset');
        }
    }

    // ==========================================
    // SIMULATION ENGINE
    // ==========================================
    function useActualBalance() {
        document.getElementById('sim-start-balance').value = Math.round(ACTUAL_BALANCE);
        recalculateSimulation();
        showToast('Saldo riil diterapkan');
    }

    function addQuickScenario(type, label, amount) {
        simulationItems.push({
            id: Date.now() + Math.random(),
            type: type,
            label: label,
            amount: amount
        });
        renderSimulationItems();
        recalculateSimulation();
    }

    function addSimulationItem() {
        simulationItems.push({
            id: Date.now() + Math.random(),
            type: 'expense',
            label: '',
            amount: ''
        });
        renderSimulationItems();
        recalculateSimulation();
    }

    function removeSimulationItem(id) {
        simulationItems = simulationItems.filter(item => item.id !== id);
        renderSimulationItems();
        recalculateSimulation();
    }

    function toggleItemType(id) {
        const item = simulationItems.find(i => i.id === id);
        if (item) {
            item.type = item.type === 'income' ? 'expense' : 'income';
            renderSimulationItems();
            recalculateSimulation();
        }
    }

    function updateItemLabel(id, val) {
        const item = simulationItems.find(i => i.id === id);
        if (item) {
            item.label = val;
        }
    }

    function updateItemAmount(id, val) {
        const item = simulationItems.find(i => i.id === id);
        if (item) {
            item.amount = parseFloat(val) || 0;
            recalculateSimulation();
        }
    }

    function recordItemTransaction(id) {
        const item = simulationItems.find(i => i.id === id);
        if (item && item.amount > 0) {
            openTransactionModal(item.type, item.amount, item.label || 'Skenario Simulasi');
        } else {
            showToast('Isi nominal pos terlebih dahulu');
        }
    }

    function renderSimulationItems() {
        const container = document.getElementById('sim-items-container');
        const emptyState = document.getElementById('sim-empty-state');
        const countSpan = document.getElementById('sim-items-count');

        countSpan.textContent = simulationItems.length;

        if (simulationItems.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        container.innerHTML = simulationItems.map((item, index) => {
            const isIncome = item.type === 'income';
            return `
                <div class="p-3 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-xs flex flex-col gap-2.5">
                    <div class="flex items-center gap-2">
                        <!-- Type toggle pill -->
                        <button type="button" 
                                onclick="toggleItemType(${item.id})"
                                title="Klik untuk ubah jenis (+/-)"
                                class="w-8 h-8 rounded-xl font-black text-sm flex items-center justify-center transition-all active:scale-95 ${
                                    isIncome 
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800' 
                                        : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-300 dark:border-rose-800'
                                }">
                            ${isIncome ? '+' : '-'}
                        </button>

                        <!-- Label input -->
                        <input type="text" 
                               value="${escapeHtml(item.label)}" 
                               oninput="updateItemLabel(${item.id}, this.value)"
                               placeholder="Nama pos (misal: Gaji, Belanja)" 
                               class="flex-1 text-xs font-bold text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-2 outline-hidden focus:border-emerald-500 dark:focus:border-emerald-400">

                        <!-- Delete button -->
                        <button type="button" 
                                onclick="removeSimulationItem(${item.id})" 
                                aria-label="Hapus pos"
                                class="w-8 h-8 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-center active:scale-90 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Nominal input -->
                        <div class="relative flex-1 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1.5 flex items-center">
                            <span class="text-xs font-bold text-slate-400 mr-1.5">Rp</span>
                            <input type="number" 
                                   inputmode="decimal"
                                   value="${item.amount || ''}" 
                                   oninput="updateItemAmount(${item.id}, this.value)"
                                   placeholder="0" 
                                   class="w-full text-xs font-black text-slate-900 dark:text-white bg-transparent border-none outline-hidden focus:ring-0">
                        </div>

                        <!-- 1-Tap Catat Transaksi -->
                        <button type="button" 
                                onclick="recordItemTransaction(${item.id})" 
                                title="Catat langsung pos ini ke transaksi riil"
                                class="px-2.5 py-2 rounded-xl text-[11px] font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 shrink-0 active:scale-95 transition-all flex items-center gap-1">
                            <span>Catat</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function recalculateSimulation() {
        const startBalance = parseFloat(document.getElementById('sim-start-balance').value) || 0;
        let totalIncome = 0;
        let totalExpense = 0;

        simulationItems.forEach(item => {
            const amt = parseFloat(item.amount) || 0;
            if (item.type === 'income') {
                totalIncome += amt;
            } else {
                totalExpense += amt;
            }
        });

        const net = totalIncome - totalExpense;
        const projected = startBalance + net;

        document.getElementById('sim-projected-display').textContent = formatRupiah(projected);
        document.getElementById('sim-total-income-display').textContent = '+Rp ' + formatRupiah(totalIncome);
        document.getElementById('sim-total-expense-display').textContent = '-Rp ' + formatRupiah(totalExpense);

        const netDisplay = document.getElementById('sim-net-diff-display');
        const badge = document.getElementById('sim-health-badge');

        if (net >= 0) {
            netDisplay.textContent = '+Rp ' + formatRupiah(net);
            netDisplay.className = 'text-xs font-bold text-emerald-400 block mt-0.5';
            badge.textContent = 'Surplus +' + formatRupiah(net);
            badge.className = 'text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
        } else {
            netDisplay.textContent = '-Rp ' + formatRupiah(Math.abs(net));
            netDisplay.className = 'text-xs font-bold text-rose-400 block mt-0.5';
            badge.textContent = 'Defisit -' + formatRupiah(Math.abs(net));
            badge.className = 'text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30';
        }
    }

    function copySimulationSummary() {
        const startBalance = parseFloat(document.getElementById('sim-start-balance').value) || 0;
        let totalIncome = 0;
        let totalExpense = 0;
        let itemsText = '';

        simulationItems.forEach(item => {
            const amt = parseFloat(item.amount) || 0;
            const sign = item.type === 'income' ? '(+)' : '(-)';
            const label = item.label || (item.type === 'income' ? 'Pemasukan' : 'Pengeluaran');
            if (item.type === 'income') totalIncome += amt;
            else totalExpense += amt;
            itemsText += `${sign} ${label}: Rp ${formatRupiah(amt)}\n`;
        });

        const net = totalIncome - totalExpense;
        const projected = startBalance + net;
        const dateStr = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

        const summary = `📊 *Simulasi Finansial SwanFlow*\n📅 ${dateStr}\n\n` +
                        `Saldo Awal: Rp ${formatRupiah(startBalance)}\n` +
                        `---------------------------------\n` +
                        (itemsText || `(Belum ada pos simulasi)\n`) +
                        `---------------------------------\n` +
                        `Total Rencana Masuk: +Rp ${formatRupiah(totalIncome)}\n` +
                        `Total Rencana Keluar: -Rp ${formatRupiah(totalExpense)}\n` +
                        `Net Perubahan: ${net >= 0 ? '+' : '-'}Rp ${formatRupiah(Math.abs(net))}\n` +
                        `💰 *Proyeksi Saldo Akhir: Rp ${formatRupiah(projected)}*`;

        copyToClipboard(summary, 'Rincian simulasi berhasil disalin!');
    }

    function recordNetDifference() {
        let totalIncome = 0;
        let totalExpense = 0;

        simulationItems.forEach(item => {
            const amt = parseFloat(item.amount) || 0;
            if (item.type === 'income') totalIncome += amt;
            else totalExpense += amt;
        });

        const net = totalIncome - totalExpense;
        if (net === 0) {
            showToast('Selisih net Rp 0, tidak ada transaksi');
            return;
        }

        const type = net > 0 ? 'income' : 'expense';
        const amount = Math.abs(net);
        const desc = `Skenario Simulasi (${simulationItems.length} pos)`;

        openTransactionModal(type, amount, desc);
    }

    // ==========================================
    // SPLIT BILL ENGINE
    // ==========================================
    function setTaxPreset(val) {
        document.getElementById('split-tax-percent').value = val;
        recalculateSplit();
    }

    function setServicePreset(val) {
        document.getElementById('split-service-percent').value = val;
        recalculateSplit();
    }

    function changePeopleCount(delta) {
        const input = document.getElementById('split-people-count');
        let val = parseInt(input.value) || 1;
        val = Math.max(1, Math.min(100, val + delta));
        input.value = val;
        recalculateSplit();
    }

    function setPeopleCount(count) {
        document.getElementById('split-people-count').value = count;
        recalculateSplit();
    }

    function setRounding(rounding) {
        splitRounding = rounding;
        [0, 100, 500, 1000].forEach(r => {
            const btn = document.getElementById(`round-btn-${r}`);
            if (btn) {
                if (r === rounding) {
                    btn.className = 'py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all bg-emerald-600 text-white shadow-xs';
                } else {
                    btn.className = 'py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200';
                }
            }
        });
        recalculateSplit();
    }

    function recalculateSplit() {
        const subtotal = parseFloat(document.getElementById('split-subtotal').value) || 0;
        const taxPercent = parseFloat(document.getElementById('split-tax-percent').value) || 0;
        const servicePercent = parseFloat(document.getElementById('split-service-percent').value) || 0;
        const discount = parseFloat(document.getElementById('split-discount').value) || 0;
        const otherFee = parseFloat(document.getElementById('split-other-fee').value) || 0;
        const peopleCount = Math.max(1, parseInt(document.getElementById('split-people-count').value) || 1);

        const taxAmount = (subtotal * taxPercent) / 100;
        const serviceAmount = (subtotal * servicePercent) / 100;
        const totalTaxService = taxAmount + serviceAmount;

        const totalBeforeRounding = Math.max(0, subtotal + totalTaxService - discount + otherFee);

        // Calculate per person
        let perPerson = peopleCount > 0 ? (totalBeforeRounding / peopleCount) : 0;

        if (splitRounding > 0) {
            perPerson = Math.ceil(perPerson / splitRounding) * splitRounding;
        }

        const totalClean = perPerson * peopleCount;
        const friendsCount = Math.max(0, peopleCount - 1);
        const friendsTotal = perPerson * friendsCount;

        // Update displays
        document.getElementById('split-tax-nominal-preview').textContent = '= Rp ' + formatRupiah(taxAmount);
        document.getElementById('split-service-nominal-preview').textContent = '= Rp ' + formatRupiah(serviceAmount);

        document.getElementById('split-per-person-display').textContent = formatRupiah(perPerson);
        document.getElementById('split-summary-subtotal').textContent = 'Rp ' + formatRupiah(subtotal);
        document.getElementById('split-summary-tax-service').textContent = '+Rp ' + formatRupiah(totalTaxService);

        const discountRow = document.getElementById('split-summary-discount-row');
        if (discount > 0) {
            discountRow.classList.remove('hidden');
            document.getElementById('split-summary-discount').textContent = '-Rp ' + formatRupiah(discount);
        } else {
            discountRow.classList.add('hidden');
        }

        const feeRow = document.getElementById('split-summary-fee-row');
        if (otherFee > 0) {
            feeRow.classList.remove('hidden');
            document.getElementById('split-summary-fee').textContent = '+Rp ' + formatRupiah(otherFee);
        } else {
            feeRow.classList.add('hidden');
        }

        document.getElementById('split-summary-total').textContent = 'Rp ' + formatRupiah(totalBeforeRounding);
        document.getElementById('split-gusti-share').textContent = 'Rp ' + formatRupiah(perPerson);
        document.getElementById('split-friends-count').textContent = friendsCount;
        document.getElementById('split-friends-total').textContent = 'Rp ' + formatRupiah(friendsTotal);
    }

    function copySplitWhatsAppMessage() {
        const subtotal = parseFloat(document.getElementById('split-subtotal').value) || 0;
        const taxPercent = parseFloat(document.getElementById('split-tax-percent').value) || 0;
        const servicePercent = parseFloat(document.getElementById('split-service-percent').value) || 0;
        const discount = parseFloat(document.getElementById('split-discount').value) || 0;
        const otherFee = parseFloat(document.getElementById('split-other-fee').value) || 0;
        const peopleCount = Math.max(1, parseInt(document.getElementById('split-people-count').value) || 1);

        const taxAmount = (subtotal * taxPercent) / 100;
        const serviceAmount = (subtotal * servicePercent) / 100;
        const total = Math.max(0, subtotal + taxAmount + serviceAmount - discount + otherFee);

        let perPerson = peopleCount > 0 ? (total / peopleCount) : 0;
        if (splitRounding > 0) {
            perPerson = Math.ceil(perPerson / splitRounding) * splitRounding;
        }

        let breakdownDetails = `Subtotal: Rp ${formatRupiah(subtotal)}`;
        if (taxAmount > 0 || serviceAmount > 0) {
            breakdownDetails += ` | Pajak & Service: Rp ${formatRupiah(taxAmount + serviceAmount)}`;
        }
        if (discount > 0) {
            breakdownDetails += ` | Diskon: -Rp ${formatRupiah(discount)}`;
        }
        if (otherFee > 0) {
            breakdownDetails += ` | Biaya/Ongkir: +Rp ${formatRupiah(otherFee)}`;
        }

        const msg = `🧾 *Rincian Patungan Bareng*\n\n` +
                    `Total Tagihan: Rp ${formatRupiah(total)}\n` +
                    `(${breakdownDetails})\n\n` +
                    `👥 Dibagi untuk: *${peopleCount} orang*\n` +
                    `👉 *Nominal per Orang: Rp ${formatRupiah(perPerson)}*\n` +
                    `---------------------------------\n` +
                    `Bisa transfer via BCA / Jago / Gopay / QRIS Gusti.\n` +
                    `Terima kasih! 🙏`;

        copyToClipboard(msg, 'Format WhatsApp berhasil disalin!');
    }

    function recordSplitMyShare() {
        const perPersonText = document.getElementById('split-per-person-display').textContent.replace(/\./g, '');
        const amount = parseFloat(perPersonText) || 0;
        if (amount > 0) {
            openTransactionModal('expense', amount, 'Patungan Makan / Hangout (Porsi Saya)');
        } else {
            showToast('Nominal Rp 0, periksa tagihan');
        }
    }

    function recordSplitFullBill() {
        const totalText = document.getElementById('split-summary-total').textContent.replace(/[^0-9]/g, '');
        const amount = parseFloat(totalText) || 0;
        if (amount > 0) {
            openTransactionModal('expense', amount, 'Bayar Total Tagihan Barengan');
        } else {
            showToast('Nominal Rp 0, periksa tagihan');
        }
    }

    // ==========================================
    // UTILITIES
    // ==========================================
    function formatRupiah(num) {
        const val = Math.round(num);
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function copyToClipboard(text, message) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(message);
            }).catch(() => {
                fallbackCopy(text, message);
            });
        } else {
            fallbackCopy(text, message);
        }
    }

    function fallbackCopy(text, message) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-999999px';
        textarea.style.top = '-999999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
            document.execCommand('copy');
            showToast(message);
        } catch (err) {
            showToast('Gagal menyalin otomatis');
        }
        document.body.removeChild(textarea);
    }

    function showToast(msg) {
        const toast = document.getElementById('calc-toast');
        const text = document.getElementById('calc-toast-text');
        if (!toast || !text) return;

        text.textContent = msg;
        toast.classList.remove('opacity-0', '-translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');

        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', '-translate-y-4');
        }, 2200);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'patungan') {
            switchTab('patungan');
        } else {
            recalculateSimulation();
        }
        recalculateSplit();
    });
</script>
@endsection
