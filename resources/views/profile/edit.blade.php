@extends('layouts.mobile')

@section('header_left')
    <div class="flex items-center gap-2">
        <a href="{{ route('dashboard') }}" class="min-w-[44px] min-h-[44px] -ml-2 flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition-all" aria-label="Kembali ke Dashboard">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div class="flex flex-col">
            <h1 class="text-base font-bold text-slate-800 dark:text-white tracking-tight leading-tight">
                Profil Pengguna
            </h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">Pengaturan akun Gusti Swandana</span>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-5">

    <!-- 1. USER OVERVIEW CARD -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-5 text-white shadow-xl border border-slate-800/80 relative overflow-hidden">
        <!-- Glow Effect -->
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-950 to-slate-800 text-emerald-400 font-extrabold text-xl flex items-center justify-center shadow-md ring-4 ring-emerald-500/30 border border-slate-700 shrink-0">
                GS
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <h2 class="text-base font-bold text-white truncate">{{ $user->name }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Owner
                    </span>
                </div>
                <p class="text-xs text-slate-300 truncate mt-0.5">{{ $user->email }}</p>
                <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Bergabung {{ $user->created_at ? $user->created_at->translatedFormat('F Y') : '2026' }}
                </p>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-slate-700/60">
            <div class="bg-white/10 dark:bg-white/5 border border-white/10 rounded-2xl p-2.5 backdrop-blur-xs text-center">
                <span class="text-[10px] text-slate-300 uppercase tracking-wider block">Total Dompet</span>
                <span class="text-base font-bold text-white block mt-0.5">{{ $walletsCount }} Akun</span>
            </div>
            <div class="bg-white/10 dark:bg-white/5 border border-white/10 rounded-2xl p-2.5 backdrop-blur-xs text-center">
                <span class="text-[10px] text-slate-300 uppercase tracking-wider block">Total Transaksi</span>
                <span class="text-base font-bold text-emerald-400 block mt-0.5">{{ $transactionsCount }} Catatan</span>
            </div>
        </div>
    </div>

    <!-- Pintasan SwanDrive di Profil -->
    <a href="{{ route('drive.index') }}" class="bg-white dark:bg-slate-900 rounded-3xl p-4 shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:border-teal-500/50 transition-all active:scale-[0.99]">
        <div class="flex items-center gap-3 min-w-0 pr-2">
            <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-200/60 dark:border-teal-900/50 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021 18V9.75" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white truncate">SwanDrive Vault</h3>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-md bg-teal-500/15 text-teal-700 dark:text-teal-300 border border-teal-400/30 shrink-0">Penyimpanan</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">Kelola berkas privat & portal terima file</p>
            </div>
        </div>
        <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-400 group-hover:text-teal-600 dark:group-hover:text-teal-400 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </div>
    </a>

    <!-- 2. FORM EDIT DATA PRIBADI -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Informasi Pribadi</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Perbarui nama dan alamat email</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Lengkap</label>
                <input id="name" 
                       type="text" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Alamat Email</label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}" 
                       required 
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <div class="pt-1">
                <button type="submit" class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- 3. FORM GANTI PASSWORD -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Keamanan Akun</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Perbarui kata sandi login Anda</p>
            </div>
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Password Saat Ini</label>
                <input id="current_password" 
                       type="password" 
                       name="current_password" 
                       required 
                       placeholder="••••••••"
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
            </div>

            <div>
                <label for="new_password" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Password Baru</label>
                <input id="new_password" 
                       type="password" 
                       name="password" 
                       required 
                       placeholder="Minimal 6 karakter"
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Ulangi Password Baru</label>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       placeholder="Ketik ulang password baru"
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
            </div>

            <div class="pt-1">
                <button type="submit" class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>

    <!-- 4. SECTION PIN KEAMANAN 6-DIGIT (M-BANKING QUICK ACCESS) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.258M5.25 10.5a7.48 7.48 0 011.05-3.83m-1.393 1.957A8.966 8.966 0 003.75 10.5c0 4.12 2.766 7.6 6.578 8.742m2.422.258a9.022 9.022 0 01-4.25-.975M9 10.5a3 3 0 116 0 3 3 0 01-6 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">PIN Kunci Cepat (6-Digit)</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">PIN numerik ala M-Banking untuk membuka aplikasi</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $user->hasPin() ? 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' }}">
                {{ $user->hasPin() ? 'Aktif (6-Digit)' : 'Belum Diatur' }}
            </span>
        </div>

        <form action="{{ route('profile.pin.update') }}" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            @if($user->hasPin())
                <div>
                    <label for="current_pin" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">PIN Saat Ini</label>
                    <input id="current_pin" 
                           type="password" 
                           inputmode="numeric" 
                           pattern="[0-9]*" 
                           maxlength="6" 
                           name="current_pin" 
                           required 
                           placeholder="•••••• (Default: 123456)"
                           class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 tracking-widest focus:outline-hidden focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    @error('current_pin')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="profile_pin" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">PIN Baru (6 Digit Angka)</label>
                <input id="profile_pin" 
                       type="password" 
                       inputmode="numeric" 
                       pattern="[0-9]*" 
                       maxlength="6" 
                       name="pin" 
                       required 
                       placeholder="Contoh: 829104"
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 tracking-widest focus:outline-hidden focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                @error('pin')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="profile_pin_confirmation" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Ulangi PIN Baru</label>
                <input id="profile_pin_confirmation" 
                       type="password" 
                       inputmode="numeric" 
                       pattern="[0-9]*" 
                       maxlength="6" 
                       name="pin_confirmation" 
                       required 
                       placeholder="Ketik ulang 6 digit PIN baru"
                       class="w-full min-h-[44px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-slate-100 tracking-widest focus:outline-hidden focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 rounded-xl text-[11px] text-slate-500 dark:text-slate-400 flex items-start gap-2">
                <svg class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <span>PIN ini digunakan di Layar Kunci Keamanan saat membuka aplikasi SwanFlow di smartphone atau browser Anda.</span>
            </div>

            <div class="pt-1">
                <button type="submit" class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-bold text-xs shadow-md shadow-indigo-600/30 dark:shadow-indigo-500/20 active:scale-98 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan PIN Keamanan Baru
                </button>
            </div>
        </form>
    </div>

    <!-- 5. SECTION FACE ID / BIOMETRIK -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                        <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                        <path d="M16 20h2a2 2 0 0 0 2-2v-2" />
                        <path d="M9 10h.01" />
                        <path d="M15 10h.01" />
                        <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                        <path d="M12 11v2" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Face ID & Biometrik</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Masuk cepat menggunakan sensor wajah iPhone</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $biometricCredentials->isNotEmpty() ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                {{ $biometricCredentials->isNotEmpty() ? 'Aktif' : 'Belum Aktif' }}
            </span>
        </div>

        <!-- Registered Devices List -->
        @if($biometricCredentials->isNotEmpty())
            <div class="space-y-2 mb-4">
                <span class="text-[11px] font-semibold text-slate-400 block uppercase tracking-wider">Perangkat Terdaftar:</span>
                @foreach($biometricCredentials as $cred)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                                    <line x1="12" y1="18" x2="12.01" y2="18"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-100 block">{{ $cred->device_name }}</span>
                                <span class="text-[10px] text-slate-400 block">Terakhir dipakai: {{ $cred->last_used_at ? $cred->last_used_at->diffForHumans() : 'Belum pernah' }}</span>
                            </div>
                        </div>
                        <form action="{{ route('faceid.destroy', $cred) }}" method="POST" onsubmit="return confirm('Hapus kredensial Face ID untuk perangkat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Hapus Face ID" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg active:scale-95 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Register Face ID Button -->
        <div>
            <button type="button" 
                    id="btn-register-face-id"
                    onclick="registerFaceId()" 
                    class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-600/30 dark:shadow-emerald-500/20 active:scale-98 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                    <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                    <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                    <path d="M16 20h2a2 2 0 0 0 2-2v-2" />
                    <path d="M9 10h.01" />
                    <path d="M15 10h.01" />
                    <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                    <path d="M12 11v2" />
                </svg>
                <span>Daftarkan Face ID di Perangkat Ini</span>
            </button>
            <p id="face-id-register-feedback" class="text-[11px] text-center text-slate-400 mt-2 hidden"></p>
        </div>
    </div>

    <!-- 5. LOGOUT BUTTON -->
    <div class="pt-2">
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari SwanFlow?')">
            @csrf
            <button type="submit" class="w-full min-h-[48px] py-3 px-4 rounded-2xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-200/60 dark:border-rose-900/50 shadow-xs active:scale-98 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Keluar dari Akun
            </button>
        </form>
    </div>

</div>

<script>
    function hexToBytes(hex) {
        const bytes = new Uint8Array(hex.length / 2);
        for (let i = 0; i < hex.length; i += 2) {
            bytes[i / 2] = parseInt(hex.substr(i, 2), 16);
        }
        return bytes;
    }

    function bufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    async function registerFaceId() {
        const feedback = document.getElementById('face-id-register-feedback');
        const btn = document.getElementById('btn-register-face-id');

        feedback.classList.remove('hidden', 'text-rose-500', 'text-emerald-500');
        feedback.classList.add('text-slate-400');
        feedback.textContent = 'Menyiapkan Face ID...';

        if (!window.PublicKeyCredential) {
            feedback.classList.remove('text-slate-400');
            feedback.classList.add('text-rose-500');
            feedback.textContent = !window.isSecureContext
                ? 'Sensor Face ID dinonaktifkan browser karena web dibuka via HTTP biasa (bukan HTTPS). Buka via HTTPS atau gunakan PIN 6-Digit.'
                : 'Browser ini tidak mendukung sensor biometrik WebAuthn.';
            return;
        }

        try {
            // 1. Fetch registration challenge
            const res = await fetch('{{ route("faceid.register.challenge") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!res.ok) {
                throw new Error('Gagal mendapatkan sesi pendaftaran.');
            }

            const data = await res.json();
            feedback.textContent = 'Pindai wajah Anda saat dialog sensor iPhone muncul...';

            const challengeBytes = hexToBytes(data.challenge);
            const userIdBytes = new TextEncoder().encode(data.user.id);

            const credential = await navigator.credentials.create({
                publicKey: {
                    challenge: challengeBytes,
                    rp: data.rp,
                    user: {
                        id: userIdBytes,
                        name: data.user.name,
                        displayName: data.user.displayName
                    },
                    pubKeyCredParams: [
                        { type: 'public-key', alg: -7 },
                        { type: 'public-key', alg: -257 }
                    ],
                    authenticatorSelection: {
                        authenticatorAttachment: 'platform',
                        userVerification: 'required'
                    },
                    timeout: 60000
                }
            });

            if (credential) {
                feedback.textContent = 'Menyimpan kredensial biometrik...';

                let clientDataJson = '';
                if (credential.response && credential.response.clientDataJSON) {
                    clientDataJson = bufferToBase64(credential.response.clientDataJSON);
                }

                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                const deviceName = isIOS ? 'iPhone (Face ID)' : 'Perangkat Biometrik';

                const verifyRes = await fetch('{{ route("faceid.register.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        credential_id: credential.id,
                        device_name: deviceName,
                        client_data_json: clientDataJson
                    })
                });

                const result = await verifyRes.json();

                if (verifyRes.ok && result.success) {
                    feedback.classList.remove('text-slate-400');
                    feedback.classList.add('text-emerald-500');
                    feedback.textContent = 'Berhasil! Face ID aktif pada perangkat ini.';
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(result.message || 'Gagal menyimpan kredensial Face ID.');
                }
            }
        } catch (err) {
            feedback.classList.remove('text-slate-400');
            feedback.classList.add('text-rose-500');
            if (err.name === 'NotAllowedError') {
                feedback.textContent = 'Pendaftaran dibatalkan atau waktu habis.';
            } else {
                feedback.textContent = err.message || 'Terjadi kesalahan pada registrasi Face ID.';
            }
        }
    }
</script>
@endsection
