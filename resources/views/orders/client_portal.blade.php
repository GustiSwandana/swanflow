<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $order->project_title }} - {{ $settings->studio_name ?? 'Portal Penyerahan File Klien' }}</title>
  <meta name="description" content="Portal resmi penyerahan dan pengunduhan file foto dan video klien.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <link rel="stylesheet" href="/css/pay-portal.css">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  <!-- Canvas Confetti for celebratory unlock effect -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
</head>
<body>

  <!-- Top Navbar -->
  <header class="navbar">
    <div class="brand-badge">
      <div class="brand-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
          <circle cx="12" cy="13" r="4"></circle>
        </svg>
      </div>
      <span id="studioNameDisplay">{{ $settings->studio_name ?? 'Studio Kreatif' }}</span>
    </div>
    <div id="statusBadgeContainer">
      @php
        $badgeClass = match($order->status) {
            'verified' => 'status-approved',
            'waiting_verification' => 'status-pending',
            'rejected' => 'status-rejected',
            default => $order->is_free ? 'status-approved' : 'status-unpaid',
        };
        $badgeLabel = match($order->status) {
            'verified' => 'Terverifikasi',
            'waiting_verification' => 'Menunggu Verifikasi',
            'rejected' => 'Bukti Ditolak',
            default => $order->is_free ? 'Siap Diunduh' : 'Belum Bayar',
        };
      @endphp
      <span class="status-pill {{ $badgeClass }}" id="headerStatusBadge">
        <span class="dot"></span>
        <span id="headerStatusText">{{ $badgeLabel }}</span>
      </span>
    </div>
  </header>

  <!-- Main Portal Content -->
  <main class="portal-container">
    
    <!-- Error State -->
    <div id="errorState" class="glass-card" style="display: none; text-align: center; padding: 48px 24px;">
      <div style="font-size: 2.5rem; margin-bottom: 16px;">⚠️</div>
      <h2 id="errorMessageTitle">Link Tidak Ditemukan</h2>
      <p id="errorMessageDesc" style="color: var(--text-secondary); margin-top: 8px;">Tautan ini mungkin sudah tidak aktif atau salah ketik. Silakan hubungi admin kami.</p>
    </div>

    <!-- Main Project Card -->
    <div id="projectContent">
      
      <!-- Project Intro Banner -->
      <section class="glass-card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
          <div>
            <span style="font-size: 0.85rem; font-weight: 600; color: var(--accent-primary); text-transform: uppercase; letter-spacing: 0.05em;">Proyek Klien</span>
            <h1 id="projectTitle" style="font-size: 1.65rem; margin-top: 4px;">{{ $order->project_title }}</h1>
            <p style="color: var(--text-secondary); margin-top: 4px;">Klien: <strong id="clientName" style="color: #fff;">{{ $order->client_name }}</strong></p>
          </div>
          
          <div id="billingSection" style="text-align: right; {{ $order->is_free ? 'display: none;' : '' }}">
            @if($order->discount > 0)
              <div id="discountBox" style="display: flex; flex-direction: column; align-items: flex-end; margin-bottom: 4px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span id="originalAmountDisplay" style="font-size: 0.95rem; text-decoration: line-through; color: var(--text-muted);">
                    Rp {{ number_format($order->amount, 0, ',', '.') }}
                  </span>
                  @php
                    $pct = $order->amount > 0 ? round(($order->discount / $order->amount) * 100) : 0;
                  @endphp
                  <span id="discountPill" style="font-size: 0.75rem; font-weight: 700; color: #f87171; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); padding: 2px 8px; border-radius: 999px;">
                    - Rp {{ number_format($order->discount, 0, ',', '.') }} ({{ $pct }}%)
                  </span>
                </div>
                @if($order->discount_label)
                  <div id="discountLabelText" style="font-size: 0.78rem; color: #34d399; font-weight: 600; margin-top: 2px;">
                    🎉 Promo: {{ $order->discount_label }}
                  </div>
                @endif
              </div>
            @endif

            <div style="display: flex; align-items: baseline; justify-content: flex-end; gap: 6px;">
              <span style="font-size: 0.85rem; color: var(--text-secondary);">Total Tagihan:</span>
              <div id="amountDisplay" style="font-size: 1.8rem; font-weight: 800; color: #34d399; font-family: var(--font-heading);">
                Rp {{ number_format($order->final_amount, 0, ',', '.') }}
              </div>
            </div>
          </div>
        </div>

        @if($order->notes)
          <div id="projectNotesBox" style="padding: 12px 16px; background: rgba(255,255,255,0.03); border-radius: var(--radius-sm); border-left: 3px solid var(--accent-primary); margin-top: 12px; font-size: 0.92rem; color: var(--text-secondary);">
            <strong>Catatan:</strong> <span id="projectNotesText">{{ $order->notes }}</span>
          </div>
        @endif
      </section>

      <!-- UNLOCKED HERO: Displayed ONLY when status === 'verified' or is_free -->
      @php
        $isUnlocked = ($order->status === 'verified' || $order->is_free);
      @endphp
      <section id="approvedSection" class="unlock-hero" style="{{ $isUnlocked ? 'display: block;' : 'display: none;' }}">
        <div style="font-size: 3.5rem; margin-bottom: 10px;">🎉</div>
        <h2 id="approvedHeroTitle" style="font-size: 1.85rem; color: #34d399;">
          {{ $order->is_free ? 'File Dokumentasi Siap Diunduh' : 'Pembayaran Terverifikasi!' }}
        </h2>
        <p id="approvedHeroDesc" style="color: var(--text-secondary); max-width: 480px; margin: 8px auto 0;">
          {{ $order->is_free
              ? 'Semua file foto & video Anda telah selesai diproses dan siap diunduh melalui tombol di bawah ini.'
              : 'Terima kasih banyak atas kerjasamanya. Semua file foto & video Anda telah siap dan dapat diunduh sekarang.' }}
        </p>

        <div>
          <a id="gdriveButton" href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer" class="gdrive-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Buka File / Download Dokumentasi
          </a>
        </div>

        <!-- Download Instructions -->
        <div style="margin-top: 32px; text-align: left; background: rgba(0,0,0,0.3); border-radius: var(--radius-md); padding: 18px; border: 1px solid rgba(255,255,255,0.06);">
          <h4 style="font-size: 0.95rem; margin-bottom: 8px; color: #fff;">💡 Petunjuk Pengunduhan:</h4>
          <ul style="color: var(--text-secondary); font-size: 0.88rem; padding-left: 20px; line-height: 1.6;">
            <li>Disarankan menggunakan Laptop atau PC saat mendownload folder zip berukuran besar.</li>
            <li>Pastikan koneksi internet stabil sebelum mulai mengunduh file master.</li>
            <li>Anda dapat menyimpan tautan halaman ini untuk mengakses kembali file di kemudian hari.</li>
          </ul>
        </div>
      </section>

      <!-- PENDING VERIFICATION NOTICE -->
      <section id="pendingSection" class="glass-card" style="{{ $order->status === 'waiting_verification' && !$order->is_free ? 'display: block;' : 'display: none;' }} border-color: var(--warning-border); background: linear-gradient(145deg, rgba(245,158,11,0.06) 0%, rgba(18,24,38,0.9) 100%); margin-bottom: 24px;">
        <div style="display: flex; gap: 16px; align-items: center;">
          <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--warning-bg); border: 2px solid var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
            ⏳
          </div>
          <div>
            <h3 style="color: #fbbf24; font-size: 1.2rem;">Bukti Pembayaran Sedang Diverifikasi</h3>
            <p style="color: var(--text-secondary); font-size: 0.92rem; margin-top: 4px;">
              Bukti transfer Anda telah tersimpan dan sedang diperiksa oleh admin. Halaman ini akan <strong>otomatis terbuka</strong> begitu verifikasi selesai.
            </p>
          </div>
        </div>

        <div style="margin-top: 18px; display: flex; align-items: center; justify-content: space-between; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.85rem; color: var(--text-muted);">
          <span>Pemeriksaan otomatis aktif...</span>
          <button class="btn btn-outline" id="btnRefreshStatus" style="padding: 6px 12px; font-size: 0.8rem;">
            🔄 Cek Sekarang
          </button>
        </div>
      </section>

      <!-- REJECTED NOTICE -->
      <section id="rejectedSection" class="glass-card" style="{{ $order->status === 'rejected' && !$order->is_free ? 'display: block;' : 'display: none;' }} border-color: var(--danger-border); background: linear-gradient(145deg, rgba(239,68,68,0.08) 0%, rgba(18,24,38,0.9) 100%); margin-bottom: 24px;">
        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--danger-bg); border: 2px solid var(--danger); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
            ❌
          </div>
          <div>
            <h3 style="color: #f87171; font-size: 1.15rem;">Bukti Pembayaran Belum Sesuai</h3>
            <p id="rejectedReasonText" style="color: #fca5a5; font-size: 0.92rem; margin-top: 4px; background: rgba(0,0,0,0.25); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px dashed rgba(239,68,68,0.3);">
              Alasan: {{ $order->rejection_reason ?? 'Bukti transfer tidak terbaca atau nominal tidak sesuai.' }}
            </p>
            <p style="color: var(--text-secondary); font-size: 0.88rem; margin-top: 8px;">
              Silakan periksa kembali dan unggah ulang bukti transfer yang benar di bawah ini.
            </p>
          </div>
        </div>
      </section>

      <!-- PAYMENT & UPLOAD FORM (Shown when UNPAID or REJECTED) -->
      @php
        $showPaymentForm = (!$isUnlocked && $order->status !== 'waiting_verification');
      @endphp
      <div id="paymentFormSection" style="{{ $showPaymentForm ? 'display: block;' : 'display: none;' }}">
        
        <!-- Bank Details Card -->
        <section class="glass-card bank-info-box">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-primary); text-transform: uppercase; letter-spacing: 0.05em;">Pilihan Rekening Pembayaran</span>
            <span id="bankCountBadge" style="font-size: 0.78rem; font-weight: 600; color: #94a3b8; background: rgba(255,255,255,0.08); padding: 2px 8px; border-radius: 999px;">
              {{ $bankAccounts->count() }} Pilihan Rekening / E-Wallet
            </span>
          </div>

          <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 12px;" id="bankInstructionText">
            {{ $settings->bank_instructions ?? 'Silakan lakukan transfer ke salah satu rekening atau e-wallet resmi berikut:' }}
          </p>

          <!-- Dynamic List of Multiple Bank Accounts -->
          <div id="bankAccountsList" style="display: flex; flex-direction: column; gap: 10px;">
            @forelse($bankAccounts as $acc)
              <div class="bank-row">
                <div>
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #ffffff; background: rgba(99,102,241,0.25); border: 1px solid rgba(99,102,241,0.4); padding: 2px 10px; border-radius: 4px;">
                      {{ $acc->bank_name }}
                    </span>
                  </div>
                  <div class="account-number" style="margin-top: 6px; font-size: 1.25rem;">{{ $acc->account_number }}</div>
                  <div style="font-size: 0.82rem; color: var(--text-secondary); margin-top: 2px;">
                    a.n <strong style="color: #fff;">{{ $acc->account_name }}</strong>
                  </div>
                </div>
                <div>
                  <button type="button" class="btn btn-outline btn-copy-acc" data-num="{{ $acc->account_number }}" data-bank="{{ $acc->bank_name }}" style="padding: 8px 14px; font-size: 0.85rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span>Salin</span>
                  </button>
                </div>
              </div>
            @empty
              <div style="color: var(--text-muted); font-size: 0.85rem; padding: 12px; text-align: center;">
                Belum ada rekening yang ditambahkan oleh admin.
              </div>
            @endforelse
          </div>

          <!-- QRIS Button if available -->
          @if($settings->qris_image_path)
            <div id="qrisContainer" style="margin-top: 14px; text-align: center;">
              <button type="button" class="btn btn-outline" id="btnShowQris" style="width: 100%; border-color: rgba(99, 102, 241, 0.4);">
                📲 Tampilkan Kode QRIS Pembayaran
              </button>
            </div>
          @endif
        </section>

        <!-- Proof Upload Box -->
        <section class="glass-card">
          <h2 style="font-size: 1.25rem; margin-bottom: 6px;">Unggah Bukti Pembayaran</h2>
          <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">
            Setelah melakukan transfer, silakan lampirkan tangkapan layar (screenshot) struk pembayaran untuk diverifikasi.
          </p>

          <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" id="proofFileInput" name="proof" accept="image/*,.pdf,application/pdf" style="position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none;">

            <label for="proofFileInput" class="dropzone" id="dropzoneBox">
              <span class="dropzone-icon">📷</span>
              <h4 style="font-size: 1.15rem; margin-bottom: 6px; color: #fff;">Ketuk untuk Pilih Foto Bukti Transfer</h4>
              <p style="font-size: 0.85rem; color: var(--text-secondary);">Mendukung format JPG, PNG, WEBP, atau PDF (Maks. 10MB)</p>
            </label>

            <!-- File Selected Preview Container -->
            <div id="uploadPreviewBox" class="upload-preview-box" style="display: none;">
              <img id="previewImage" class="upload-preview-img" src="" alt="Preview Bukti">
              <div style="overflow: hidden;">
                <div id="previewFileName" style="font-weight: 600; font-size: 0.92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">
                  bukti.jpg
                </div>
                <div id="previewFileSize" style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                  0 KB
                </div>
              </div>
              <button type="button" class="remove-preview-btn" id="btnRemovePreview" title="Ganti foto">
                ✕
              </button>
            </div>

            <!-- Upload Progress Indicator Bar -->
            <div id="uploadProgressBox" style="display: none; margin-top: 18px; background: rgba(0,0,0,0.3); border-radius: var(--radius-md); padding: 16px; border: 1px solid var(--border-color);">
              <div style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 6px;">
                <span id="uploadProgressStatusText" style="font-weight: 600; color: #fff;">Mengunggah berkas bukti...</span>
                <span id="uploadProgressPercent" style="font-weight: 700; color: #34d399;">0%</span>
              </div>
              <div class="progress-bar-container">
                <div id="uploadProgressBar" class="progress-bar-fill"></div>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">
                <span id="uploadProgressBytes">0 MB / 0 MB</span>
                <span id="uploadProgressSub">Mohon jangan menutup halaman ini</span>
              </div>
            </div>

            <div style="margin-top: 20px;">
              <button type="button" class="btn btn-primary btn-full btn-lg" id="btnSubmitProof" style="cursor: pointer; touch-action: manipulation;">
                Kirim Bukti Pembayaran Sekarang
              </button>
            </div>
          </form>
        </section>

      </div>
    </div>
  </main>

  <!-- QRIS Fullscreen Modal -->
  @if($settings->qris_image_path)
    <div id="qrisModal" class="modal-overlay" style="display: none;">
      <div class="modal-content" style="text-align: center;">
        <button type="button" class="modal-close-btn" id="btnCloseQrisModal">✕</button>
        <h3 style="margin-bottom: 4px; font-size: 1.25rem;">QRIS Pembayaran</h3>
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 16px;">
          Pindai kode QR di bawah ini menggunakan aplikasi M-Banking atau E-Wallet apa pun.
        </p>
        <div style="background: #fff; padding: 16px; border-radius: var(--radius-md); display: inline-block;">
          <img id="qrisModalImg" src="{{ Storage::url($settings->qris_image_path) }}" alt="QRIS" style="max-width: 240px; height: auto; display: block;">
        </div>
      </div>
    </div>
  @endif

  <!-- Toast Notification Container -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- Client Script Logic -->
  <script>
    const TOKEN = "{{ $token }}";
    let isUploading = false;
    let selectedFile = null;
    let pollInterval = null;

    // Toast feedback
    function showToast(msg, type = 'info') {
      const container = document.getElementById('toastContainer');
      const toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      toast.textContent = msg;
      container.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    }

    // Copy Bank Account
    document.querySelectorAll('.btn-copy-acc').forEach(btn => {
      btn.addEventListener('click', () => {
        const num = btn.dataset.num;
        const bank = btn.dataset.bank;
        navigator.clipboard.writeText(num).then(() => {
          const label = btn.querySelector('span');
          const orig = label.textContent;
          label.textContent = 'Tersalin! ✓';
          btn.style.borderColor = 'var(--success)';
          showToast(`Nomor ${bank} (${num}) berhasil disalin!`, 'success');
          setTimeout(() => {
            label.textContent = orig;
            btn.style.borderColor = '';
          }, 2000);
        });
      });
    });

    // File Selection & Preview
    const fileInput = document.getElementById('proofFileInput');
    const previewBox = document.getElementById('uploadPreviewBox');
    const previewImg = document.getElementById('previewImage');
    const previewName = document.getElementById('previewFileName');
    const previewSize = document.getElementById('previewFileSize');
    const btnRemove = document.getElementById('btnRemovePreview');
    const dropzone = document.getElementById('dropzoneBox');
    const btnSubmit = document.getElementById('btnSubmitProof');

    fileInput.addEventListener('change', (e) => {
      if (e.target.files && e.target.files[0]) {
        selectedFile = e.target.files[0];
        previewName.textContent = selectedFile.name;
        previewSize.textContent = (selectedFile.size / 1024).toFixed(1) + ' KB';

        if (selectedFile.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (ev) => {
            previewImg.src = ev.target.result;
            previewImg.style.display = 'block';
          };
          reader.readAsDataURL(selectedFile);
        } else {
          previewImg.src = '';
          previewImg.style.display = 'none';
        }

        previewBox.style.display = 'flex';
        dropzone.style.display = 'none';
      }
    });

    btnRemove.addEventListener('click', () => {
      selectedFile = null;
      fileInput.value = '';
      previewBox.style.display = 'none';
      dropzone.style.display = 'flex';
    });

    // Submit Upload via AJAX
    btnSubmit.addEventListener('click', () => {
      if (!selectedFile) {
        showToast('Pilih foto bukti pembayaran terlebih dahulu.', 'error');
        fileInput.click();
        return;
      }

      if (isUploading) return;
      isUploading = true;
      btnSubmit.disabled = true;

      const progressBox = document.getElementById('uploadProgressBox');
      const progressBar = document.getElementById('uploadProgressBar');
      const progressPercent = document.getElementById('uploadProgressPercent');
      const progressBytes = document.getElementById('uploadProgressBytes');
      const progressStatus = document.getElementById('uploadProgressStatusText');

      progressBox.style.display = 'block';
      progressBar.style.width = '0%';

      const formData = new FormData();
      formData.append('proof', selectedFile);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', `/api/p/${TOKEN}/upload`, true);
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      if (csrfMeta) xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);

      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          const pct = Math.min(100, Math.round((e.loaded / e.total) * 100));
          progressBar.style.width = pct + '%';
          progressPercent.textContent = pct + '%';
          progressBytes.textContent = (e.loaded / 1024 / 1024).toFixed(2) + ' MB / ' + (e.total / 1024 / 1024).toFixed(2) + ' MB';
          if (pct >= 100) {
            progressStatus.textContent = 'Memverifikasi berkas di server... ⏳';
          }
        }
      });

      xhr.onload = function() {
        isUploading = false;
        btnSubmit.disabled = false;
        let res = {};
        try { res = JSON.parse(xhr.responseText); } catch(err) {}

        if (xhr.status >= 200 && xhr.status < 300 && res.success) {
          showToast('Bukti pembayaran berhasil diunggah! Menunggu verifikasi admin.', 'success');
          setTimeout(() => window.location.reload(), 1000);
        } else {
          progressBox.style.display = 'none';
          showToast(res.error || 'Gagal mengunggah bukti pembayaran.', 'error');
        }
      };

      xhr.onerror = function() {
        isUploading = false;
        btnSubmit.disabled = false;
        progressBox.style.display = 'none';
        showToast('Koneksi internet bermasalah saat mengunggah.', 'error');
      };

      xhr.send(formData);
    });

    // QRIS Modal Toggle
    const btnShowQris = document.getElementById('btnShowQris');
    const qrisModal = document.getElementById('qrisModal');
    const btnCloseQris = document.getElementById('btnCloseQrisModal');
    if (btnShowQris && qrisModal) {
      btnShowQris.addEventListener('click', () => qrisModal.style.display = 'flex');
      if (btnCloseQris) btnCloseQris.addEventListener('click', () => qrisModal.style.display = 'none');
      qrisModal.addEventListener('click', (e) => {
        if (e.target === qrisModal) qrisModal.style.display = 'none';
      });
    }

    // Auto-poll status if waiting verification
    const currentStatus = "{{ $order->status }}";
    if (currentStatus === 'waiting_verification') {
      pollInterval = setInterval(async () => {
        try {
          const r = await fetch(`/api/p/${TOKEN}`);
          if (r.ok) {
            const data = await r.json();
            if (data.project && data.project.status === 'APPROVED') {
              clearInterval(pollInterval);
              showToast('Pembayaran Anda telah diverifikasi! Membuka file...', 'success');
              if (typeof confetti === 'function') {
                confetti({ particleCount: 100, spread: 80, origin: { y: 0.6 } });
              }
              setTimeout(() => window.location.reload(), 1200);
            }
          }
        } catch(e) {}
      }, 5000);
    }

    const btnRefresh = document.getElementById('btnRefreshStatus');
    if (btnRefresh) {
      btnRefresh.addEventListener('click', () => window.location.reload());
    }
  </script>
</body>
</html>
