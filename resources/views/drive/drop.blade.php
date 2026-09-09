<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>Kirim Berkas: {{ $link->title }} - SwanDrive</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-full flex flex-col justify-between selection:bg-teal-500 selection:text-white antialiased">

    <!-- Ambient Gradient Background (Teal/Emerald liquid orbs) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl animate-liquid-orb-1"></div>
        <div class="absolute -bottom-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl animate-liquid-orb-2"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 flex-1 flex flex-col items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-lg liquid-card rounded-[32px] p-6 sm:p-8 shadow-2xl space-y-6 bg-slate-900/85 backdrop-blur-2xl border border-white/20 relative overflow-hidden animate-swan-in">
            <!-- Specular Top Rim -->
            <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent"></div>
            
            <!-- Branding Badge -->
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full liquid-glass bg-teal-500/15 border border-teal-500/30 text-teal-400 text-xs font-black tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    <span>SwanDrive Drop Portal</span>
                </div>
            </div>

            <!-- Header Info -->
            <div class="text-center space-y-2">
                <h1 class="text-xl sm:text-2xl font-black text-white leading-tight">
                    {{ $link->title }}
                </h1>
                @if(!empty($link->description))
                    <p class="text-xs sm:text-sm font-semibold text-slate-300 max-w-md mx-auto leading-relaxed">
                        {{ $link->description }}
                    </p>
                @endif
            </div>

            <!-- Setting & Limit Parameters Grid -->
            <div class="grid grid-cols-3 gap-2 rounded-[22px] liquid-glass p-3.5 border border-white/15 backdrop-blur-xl text-center shadow-xs">
                <!-- Limit 1: Batas Ukuran -->
                <div class="space-y-0.5">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Batas Ukuran</span>
                    <span class="text-xs sm:text-sm font-black text-teal-400">
                        {{ $link->max_file_size_mb }} MB
                    </span>
                </div>

                <!-- Limit 2: Kuota File -->
                <div class="space-y-0.5 border-x border-white/10 px-1">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Sisa Kuota</span>
                    <span class="text-xs sm:text-sm font-black {{ $link->remainingSlots() > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $link->remainingSlots() }} / {{ $link->max_files }}
                    </span>
                </div>

                <!-- Limit 3: Batas Waktu -->
                <div class="space-y-0.5">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Batas Waktu</span>
                    @if($link->expires_at)
                        <span class="text-xs sm:text-sm font-black {{ $link->isExpired() ? 'text-rose-400' : 'text-amber-400' }}" title="{{ $link->expires_at->format('d M Y H:i') }}">
                            @if($link->isExpired())
                                Habis
                            @else
                                {{ $link->expires_at->diffForHumans(['parts' => 1]) }}
                            @endif
                        </span>
                    @else
                        <span class="text-xs sm:text-sm font-black text-slate-300">
                            Tak Terbatas
                        </span>
                    @endif
                </div>
            </div>

            <!-- Flash Error Notification -->
            @if($errors->any())
                <div class="p-3 bg-rose-950/40 border border-rose-800 rounded-2xl text-xs text-rose-200 space-y-1">
                    <span class="font-bold block">Gagal mengunggah:</span>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Flash Success Notification -->
            @if(session('drop_success'))
                <div class="p-4 bg-emerald-950/50 border border-emerald-800 rounded-2xl text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-emerald-300">
                        {{ session('drop_success') }}
                    </p>
                    <p class="text-[11px] text-slate-400">
                        Berkas telah aman tersimpan di SwanDrive penerima.
                    </p>
                </div>
            @endif

            <!-- Upload Form or Closed Notice -->
            @if($link->canAcceptUpload())
                <form id="drop-upload-form" action="{{ route('drive.drop.upload', ['token' => $link->token]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Hidden File Input -->
                    <input type="file" id="drop-file-input" name="file" class="hidden" onchange="handleFileChange(this)" required>

                    <!-- Interactive Drag & Drop Box -->
                    <div id="dropzone" onclick="document.getElementById('drop-file-input').click()"
                         class="border-2 border-dashed border-teal-500/40 hover:border-teal-400 rounded-[24px] p-6 flex flex-col items-center justify-center text-center cursor-pointer bg-slate-950/60 hover:bg-slate-950/90 ios-press active:scale-[0.99] transition-all">
                        <div class="w-12 h-12 rounded-[18px] bg-teal-500/15 text-teal-400 flex items-center justify-center mb-2.5 border border-teal-500/30 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                        </div>
                        <span id="dropzone-title" class="text-xs sm:text-sm font-black text-white tracking-tight">
                            Pilih atau Seret Berkas ke Sini
                        </span>
                        <span id="dropzone-subtitle" class="text-[11px] font-semibold text-slate-400 mt-0.5">
                            Maksimal {{ $link->max_file_size_mb }} MB
                        </span>
                    </div>

                    <!-- File Details Preview -->
                    <div id="file-preview-card" class="hidden p-3.5 bg-teal-950/40 border border-teal-500/30 rounded-[20px] flex items-center justify-between text-xs backdrop-blur-md">
                        <div class="truncate pr-2">
                            <span id="preview-name" class="font-bold text-white block truncate">Nama berkas</span>
                            <span id="preview-size" class="text-[10px] font-semibold text-teal-300">0 KB</span>
                        </div>
                        <button type="button" onclick="cancelSelection()" class="text-slate-400 hover:text-white p-1 text-xs font-bold">
                            ✕ Ganti
                        </button>
                    </div>

                    <!-- Uploader Metadata Inputs -->
                    <div class="space-y-3 pt-1">
                        <div>
                            <label for="uploader_name" class="block text-[11px] font-bold text-slate-300 mb-1.5">Nama Pengirim (Opsional)</label>
                            <input type="text" id="uploader_name" name="uploader_name" placeholder="Misal: Andi / Vendor Acara"
                                   class="w-full min-h-[46px] px-4 py-2.5 text-xs font-semibold rounded-[20px] bg-slate-950/80 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 backdrop-blur-md transition-all">
                        </div>
                        <div>
                            <label for="uploader_notes" class="block text-[11px] font-bold text-slate-300 mb-1.5">Catatan Tambahan (Opsional)</label>
                            <input type="text" id="uploader_notes" name="uploader_notes" placeholder="Misal: Revisi nota final tahap 2..."
                                   class="w-full min-h-[46px] px-4 py-2.5 text-xs font-semibold rounded-[20px] bg-slate-950/80 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 backdrop-blur-md transition-all">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit"
                            class="w-full min-h-[46px] py-3.5 px-6 rounded-[20px] bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 active:scale-[0.98] text-white font-black text-xs sm:text-sm shadow-xl shadow-teal-500/25 flex items-center justify-center gap-2 border border-white/20 ios-press transition-all cursor-pointer">
                        <svg id="btn-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        <svg id="btn-spinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text">Kirim Berkas Sekarang</span>
                    </button>

                    <!-- Upload Progress Bar Container -->
                    <div id="drop-progress-container" class="hidden pt-1 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span id="drop-progress-status" class="font-bold text-teal-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="drop-status-text">Mengunggah berkas...</span>
                            </span>
                            <span id="drop-progress-percent" class="font-extrabold text-white">0%</span>
                        </div>
                        <div class="w-full bg-slate-950 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-800">
                            <div id="drop-progress-bar" class="bg-gradient-to-r from-teal-500 via-emerald-400 to-teal-500 h-full rounded-full transition-all duration-150 ease-out" style="width: 0%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium">
                            <span id="drop-progress-bytes">0 KB / 0 KB</span>
                            <span>Mohon tunggu sebentar</span>
                        </div>
                    </div>
                </form>
            @else
                <!-- Inactive or Expired State Card -->
                <div class="p-6 bg-slate-950/70 border border-slate-800 rounded-2xl text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-rose-500/15 text-rose-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Tautan Pengunggahan Tidak Aktif</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                            @if(!$link->is_active)
                                Tautan ini telah ditutup secara manual oleh pemilik berkas.
                            @elseif($link->isExpired())
                                Batas waktu untuk mengirimkan berkas melalui tautan ini telah berakhir.
                            @elseif($link->isLimitReached())
                                Kuota penerimaan berkas telah terpenuhi (maksimal {{ $link->max_files }} berkas).
                            @endif
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Minimalist Footer -->
    <footer class="relative z-10 text-center py-4 text-[11px] text-slate-600">
        SwanDrive &copy; {{ date('Y') }} • Powered by SwanFlow
    </footer>

    <script>
        const maxBytes = {{ $link->max_file_size_mb * 1024 * 1024 }};

        function formatBytes(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
            return bytes + ' B';
        }

        function handleFileChange(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            if (file.size > maxBytes) {
                alert(`Ukuran berkas (${(file.size / 1048576).toFixed(1)} MB) melebihi batas maksimal {{ $link->max_file_size_mb }} MB.`);
                input.value = '';
                cancelSelection();
                return;
            }

            document.getElementById('preview-name').innerText = file.name;
            document.getElementById('preview-size').innerText = formatBytes(file.size);
            document.getElementById('file-preview-card').classList.remove('hidden');
            document.getElementById('dropzone').classList.add('border-teal-400', 'bg-teal-950/20');
        }

        function cancelSelection() {
            document.getElementById('drop-file-input').value = '';
            document.getElementById('file-preview-card').classList.add('hidden');
            document.getElementById('dropzone').classList.remove('border-teal-400', 'bg-teal-950/20');
            const progressContainer = document.getElementById('drop-progress-container');
            if (progressContainer) progressContainer.classList.add('hidden');
        }

        // Drag and drop handlers
        const dropzone = document.getElementById('dropzone');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-teal-400', 'bg-teal-950/30');
                }, false);
            });

            ['dragleave', 'drop'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('border-teal-400', 'bg-teal-950/30');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt.files && dt.files.length > 0) {
                    const input = document.getElementById('drop-file-input');
                    input.files = dt.files;
                    handleFileChange(input);
                }
            });
        }

        const uploadForm = document.getElementById('drop-upload-form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('drop-file-input');
                if (!fileInput.files || !fileInput.files[0]) return;

                const btn = document.getElementById('btn-submit');
                const btnIcon = document.getElementById('btn-icon');
                const btnSpinner = document.getElementById('btn-spinner');
                const btnText = document.getElementById('btn-text');
                const progressContainer = document.getElementById('drop-progress-container');
                const progressBar = document.getElementById('drop-progress-bar');
                const progressPercent = document.getElementById('drop-progress-percent');
                const progressBytes = document.getElementById('drop-progress-bytes');
                const statusText = document.getElementById('drop-status-text');

                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                if (btnIcon) btnIcon.classList.add('hidden');
                if (btnSpinner) btnSpinner.classList.remove('hidden');
                btnText.innerText = 'Mengunggah berkas...';

                if (progressContainer) {
                    progressContainer.classList.remove('hidden');
                    progressBar.style.width = '0%';
                    progressPercent.innerText = '0%';
                    statusText.innerText = 'Mengunggah berkas...';
                    progressBytes.innerText = `0 KB / ${formatBytes(fileInput.files[0].size)}`;
                }

                const formData = new FormData(uploadForm);
                const xhr = new XMLHttpRequest();

                xhr.open('POST', uploadForm.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                if (csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }

                xhr.upload.addEventListener('progress', function(event) {
                    if (event.lengthComputable) {
                        const percent = Math.min(99, Math.round((event.loaded / event.total) * 100));
                        if (progressBar) progressBar.style.width = percent + '%';
                        if (progressPercent) progressPercent.innerText = percent + '%';
                        if (progressBytes) progressBytes.innerText = `${formatBytes(event.loaded)} / ${formatBytes(event.total)}`;

                        if (percent >= 98 && statusText) {
                            statusText.innerText = 'Menyimpan & memproses berkas...';
                        }
                    }
                });

                xhr.addEventListener('load', function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        if (progressBar) progressBar.style.width = '100%';
                        if (progressPercent) progressPercent.innerText = '100%';
                        if (statusText) statusText.innerText = 'Unggahan berhasil!';

                        setTimeout(() => {
                            window.location.reload();
                        }, 400);
                    } else {
                        let errorMsg = 'Gagal mengirim berkas. Silakan coba lagi.';
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.message) errorMsg = json.message;
                        } catch(e) {}

                        alert(errorMsg);
                        btn.disabled = false;
                        btn.classList.remove('opacity-80', 'cursor-not-allowed');
                        if (btnIcon) btnIcon.classList.remove('hidden');
                        if (btnSpinner) btnSpinner.classList.add('hidden');
                        btnText.innerText = 'Kirim Berkas Sekarang';
                        if (progressContainer) progressContainer.classList.add('hidden');
                    }
                });

                xhr.addEventListener('error', function() {
                    alert('Terjadi kendala koneksi saat mengunggah berkas.');
                    btn.disabled = false;
                    btn.classList.remove('opacity-80', 'cursor-not-allowed');
                    if (btnIcon) btnIcon.classList.remove('hidden');
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                    btnText.innerText = 'Kirim Berkas Sekarang';
                    if (progressContainer) progressContainer.classList.add('hidden');
                });

                xhr.send(formData);
            });
        }
    </script>

</body>
</html>
