<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Penyerahan File & Verifikasi Pembayaran</title>
  <meta name="description" content="Dashboard manajemen link penyerahan file klien dan verifikasi bukti transfer manual.">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>

  <!-- Admin Login Overlay (Visible when not logged in) -->
  <div id="loginOverlay" style="position: fixed; inset: 0; background: rgba(9,13,22,0.95); backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="glass-card" style="max-width: 420px; width: 100%; text-align: center;">
      <div class="brand-icon" style="margin: 0 auto 16px auto; width: 48px; height: 48px; font-size: 1.4rem;">
        🔒
      </div>
      <h2 style="font-size: 1.5rem; margin-bottom: 6px;">Admin Access</h2>
      <p style="color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 24px;">
        Masukkan PIN Master untuk mengelola proyek dan memverifikasi pembayaran klien.
      </p>

      <form id="loginForm">
        <div class="form-group" style="text-align: left;">
          <label class="form-label" for="pinInput">PIN Admin</label>
          <input type="password" id="pinInput" class="form-control" placeholder="Default PIN: 123456" required autofocus style="letter-spacing: 4px; font-size: 1.2rem; text-align: center;">
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" id="btnLoginSubmit" style="margin-top: 10px;">
          <span>Buka Dashboard</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Main Admin Interface -->
  <div id="adminApp" style="display: none;">

    <!-- Navbar -->
    <header class="navbar">
      <div class="brand-badge">
        <div class="brand-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
          </svg>
        </div>
        <div>
          <span id="navStudioName">Lensa Art Studio</span>
          <span style="font-size: 0.75rem; color: var(--accent-primary); font-weight: normal; margin-left: 6px; padding: 2px 8px; background: rgba(99,102,241,0.15); border-radius: 999px;">Admin Panel</span>
        </div>
      </div>
      <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="font-size: 0.88rem; padding: 8px 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
          <span>←</span> <span>SwanFlow</span>
        </a>
        <button class="btn btn-outline" id="btnOpenSettings" style="font-size: 0.88rem; padding: 8px 14px;">
          ⚙️ Pengaturan Rekening
        </button>
        <button class="btn btn-danger-outline" id="btnLogout" style="font-size: 0.88rem; padding: 8px 14px;">
          🚪 Keluar
        </button>
      </div>
    </header>

    <!-- Main Container -->
    <main class="container">

      <!-- Statistics Overview -->
      <section class="stats-grid">
        <div class="stat-card">
          <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Total Proyek</div>
          <div class="stat-value" id="statTotal">0</div>
        </div>

        <div class="stat-card stat-warning" id="cardStatPending">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #fbbf24; text-transform: uppercase;">Menunggu Verifikasi</div>
            <span class="status-pill status-pending" id="pendingAlertBadge" style="display: none; padding: 2px 8px; font-size: 0.7rem;">Action Needed</span>
          </div>
          <div class="stat-value" style="color: #fbbf24;" id="statPending">0</div>
        </div>

        <div class="stat-card">
          <div style="font-size: 0.82rem; font-weight: 600; color: #34d399; text-transform: uppercase;">Selesai (Disetujui)</div>
          <div class="stat-value" style="color: #34d399;" id="statApproved">0</div>
        </div>

        <div class="stat-card">
          <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Belum Dibayar</div>
          <div class="stat-value" style="color: #cbd5e1;" id="statUnpaid">0</div>
        </div>
      </section>

      <!-- Action & Filter Bar -->
      <section style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 24px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; flex: 1;">
          <input type="text" id="searchInput" class="form-control" placeholder="Cari nama klien atau judul proyek..." style="max-width: 320px; padding: 10px 14px; font-size: 0.9rem;">
          
          <div style="display: flex; gap: 6px;" id="filterTabs">
            <button class="btn btn-primary" data-filter="all" style="padding: 8px 14px; font-size: 0.85rem;">Semua</button>
            <button class="btn btn-outline" data-filter="PENDING_VERIFICATION" style="padding: 8px 14px; font-size: 0.85rem;">Menunggu Review</button>
            <button class="btn btn-outline" data-filter="APPROVED" style="padding: 8px 14px; font-size: 0.85rem;">Selesai</button>
            <button class="btn btn-outline" data-filter="UNPAID" style="padding: 8px 14px; font-size: 0.85rem;">Belum Bayar</button>
          </div>
        </div>

        <button class="btn btn-primary" id="btnOpenCreateModal" style="padding: 10px 20px;">
          <span style="font-size: 1.1rem;">+</span>
          <span>Buat Link Klien Baru</span>
        </button>
      </section>

      <!-- Projects List Container -->
      <section id="projectsList">
        <!-- Dynamic project cards rendered via JS -->
      </section>

      <!-- Empty State -->
      <div id="emptyState" class="glass-card" style="display: none; text-align: center; padding: 60px 20px;">
        <div style="font-size: 3rem; margin-bottom: 12px;">📁</div>
        <h3 style="font-size: 1.3rem;">Belum Ada Proyek</h3>
        <p style="color: var(--text-secondary); margin-top: 6px; font-size: 0.92rem;">
          Klik tombol <strong>"Buat Link Klien Baru"</strong> di atas untuk membuat tautan penyerahan file pertama Anda.
        </p>
      </div>

    </main>

  </div>

  <!-- DIALOG: Buat Proyek Baru -->
  <dialog id="createProjectModal">
    <div class="modal-header">
      <h3 style="font-size: 1.3rem;">Buat Link Klien Baru</h3>
      <button type="button" class="modal-close-btn" id="btnCloseCreateModal">✕</button>
    </div>

    <form id="createProjectForm">
      <div class="form-group">
        <label class="form-label" for="clientNameInput">Nama Klien <span style="color: #ef4444;">*</span></label>
        <input type="text" id="clientNameInput" class="form-control" placeholder="Contoh: Sarah & Dimas" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="clientPhoneInput">No. WhatsApp Klien (Opsional)</label>
        <input type="tel" id="clientPhoneInput" class="form-control" placeholder="Contoh: 081234567890 (untuk kirim WA otomatis)">
      </div>

      <div class="form-group">
        <label class="form-label" for="projectTitleInput">Judul Dokumentasi / File <span style="color: #ef4444;">*</span></label>
        <input type="text" id="projectTitleInput" class="form-control" placeholder="Contoh: Foto & Video Wedding 2026" required>
      </div>

      <!-- Toggle Akses Langsung / Tanpa Tagihan -->
      <div class="form-group" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: var(--radius-sm); padding: 12px 14px; margin-bottom: 16px;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; margin-bottom: 0;">
          <input type="checkbox" id="isFreeInput" style="width: 18px; height: 18px; accent-color: var(--accent-primary); cursor: pointer;">
          <span style="font-weight: 700; font-size: 0.92rem; color: #fff; display: flex; align-items: center; gap: 6px;">
            <span>⚡ Akses Langsung (Tanpa Tagihan / Biaya)</span>
          </span>
        </label>
        <small style="display: block; color: var(--text-secondary); margin-top: 6px; font-size: 0.8rem; line-height: 1.4;">
          Aktifkan jika ingin memberikan link langsung terbuka tanpa proses pembayaran. Pada tampilan klien, <strong>tidak akan ada keterangan gratis maupun nominal Rp 0</strong>.
        </small>
      </div>

      <!-- Pricing fields container (disembunyikan saat Akses Langsung aktif) -->
      <div id="pricingFieldsContainer">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label class="form-label" for="amountInput">Harga Paket / Normal (Rp) <span style="color: #ef4444;">*</span></label>
            <input type="number" id="amountInput" class="form-control" placeholder="Contoh: 2500000" min="0" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="discountInput">Potongan / Diskon (Rp)</label>
            <input type="number" id="discountInput" class="form-control" placeholder="Contoh: 500000" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="discountLabelInput">Keterangan / Nama Promo (Opsional)</label>
          <input type="text" id="discountLabelInput" class="form-control" placeholder="Contoh: Diskon Early Bird / Promo DP Lunas">
        </div>

        <!-- Realtime Final Total Preview -->
        <div style="background: rgba(16, 185, 129, 0.08); border: 1px dashed var(--success-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.85rem; color: var(--text-secondary);">Total Akhir yang Harus Dibayar:</span>
          <span id="previewFinalAmount" style="font-size: 1.15rem; font-weight: 800; color: #34d399; font-family: var(--font-heading);">Rp 0</span>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="gdriveUrlInput">
          Link Google Drive / SwanFlow (Folder/File Rahasia) <span style="color: #ef4444;">*</span>
        </label>
        <input type="url" id="gdriveUrlInput" class="form-control" placeholder="https://drive.google.com/drive/folders/... atau link SwanFlow" required>
        <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 4px;">
          🔒 Link ini dienkripsi di server dan <strong>TIDAK AKAN DITAMPILKAN</strong> ke klien sebelum bukti transfer disetujui.
        </small>
      </div>

      <div class="form-group">
        <label class="form-label" for="notesInput">Catatan Khusus (Opsional)</label>
        <textarea id="notesInput" class="form-control" placeholder="Contoh: File video format 4K, link aktif selama 30 hari..."></textarea>
      </div>

      <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
        <button type="button" class="btn btn-outline" id="btnCancelCreate">Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitCreate">
          <span>Simpan & Buat Link</span>
        </button>
      </div>
    </form>
  </dialog>

  <!-- DIALOG: Edit Data & Link Proyek -->
  <dialog id="editProjectModal" style="max-width: 620px;">
    <div class="modal-header">
      <h3 style="font-size: 1.3rem; display: flex; align-items: center; gap: 8px;">
        <span>✏️</span>
        <span>Edit Data & Link Proyek</span>
      </h3>
      <button type="button" class="modal-close-btn" id="btnCloseEditModal">✕</button>
    </div>

    <form id="editProjectForm">
      <input type="hidden" id="editProjectId">

      <div class="form-group">
        <label class="form-label" for="editClientNameInput">Nama Klien <span style="color: #ef4444;">*</span></label>
        <input type="text" id="editClientNameInput" class="form-control" placeholder="Contoh: Sarah & Dimas" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="editClientPhoneInput">No. WhatsApp Klien (Opsional)</label>
        <input type="tel" id="editClientPhoneInput" class="form-control" placeholder="Contoh: 081234567890">
      </div>

      <div class="form-group">
        <label class="form-label" for="editProjectTitleInput">Judul Dokumentasi / File <span style="color: #ef4444;">*</span></label>
        <input type="text" id="editProjectTitleInput" class="form-control" placeholder="Contoh: Foto & Video Wedding 2026" required>
      </div>

      <!-- Kustomisasi Link / Slug / Token -->
      <div class="form-group" style="background: rgba(15, 23, 42, 0.6); border: 1px dashed var(--border-glass); border-radius: var(--radius-sm); padding: 12px 14px;">
        <label class="form-label" for="editTokenInput" style="display: flex; justify-content: space-between;">
          <span>🔗 Kustomisasi Akhiran Tautan Link</span>
          <span style="font-size: 0.78rem; color: var(--accent-primary);">Dapat diubah</span>
        </label>
        <div style="display: flex; align-items: center; gap: 6px;">
          <span style="color: var(--text-muted); font-size: 0.88rem; font-family: monospace;">.../p/</span>
          <input type="text" id="editTokenInput" class="form-control" placeholder="custom-slug" required style="font-family: monospace; font-size: 0.95rem;">
        </div>
        <small style="color: var(--text-secondary); font-size: 0.78rem; display: block; margin-top: 4px;">
          Gunakan huruf kecil, angka, atau tanda strip (-) misal: <em>wedding-sarah-dimas</em>.
        </small>
      </div>

      <!-- Toggle Akses Langsung / Tanpa Tagihan -->
      <div class="form-group" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: var(--radius-sm); padding: 12px 14px; margin-bottom: 16px;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; margin-bottom: 0;">
          <input type="checkbox" id="editIsFreeInput" style="width: 18px; height: 18px; accent-color: var(--accent-primary); cursor: pointer;">
          <span style="font-weight: 700; font-size: 0.92rem; color: #fff; display: flex; align-items: center; gap: 6px;">
            <span>⚡ Akses Langsung (Tanpa Tagihan / Biaya)</span>
          </span>
        </label>
        <small style="display: block; color: var(--text-secondary); margin-top: 6px; font-size: 0.8rem; line-height: 1.4;">
          Jika aktif, klien langsung dapat mengunduh file tanpa tagihan, tanpa rincian nominal Rp 0, dan tanpa keterangan gratis.
        </small>
      </div>

      <!-- Pricing Fields Container -->
      <div id="editPricingFieldsContainer">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label class="form-label" for="editAmountInput">Harga Paket / Normal (Rp) <span style="color: #ef4444;">*</span></label>
            <input type="number" id="editAmountInput" class="form-control" placeholder="Contoh: 2500000" min="0">
          </div>
          <div class="form-group">
            <label class="form-label" for="editDiscountInput">Potongan / Diskon (Rp)</label>
            <input type="number" id="editDiscountInput" class="form-control" placeholder="Contoh: 500000" min="0" value="0">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="editDiscountLabelInput">Keterangan / Nama Promo (Opsional)</label>
          <input type="text" id="editDiscountLabelInput" class="form-control" placeholder="Contoh: Diskon Promo">
        </div>

        <!-- Realtime Final Total Preview -->
        <div style="background: rgba(16, 185, 129, 0.08); border: 1px dashed var(--success-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.85rem; color: var(--text-secondary);">Total Akhir yang Harus Dibayar:</span>
          <span id="editPreviewFinalAmount" style="font-size: 1.15rem; font-weight: 800; color: #34d399; font-family: var(--font-heading);">Rp 0</span>
        </div>
      </div>

      <!-- Status Proyek Dropdown -->
      <div class="form-group">
        <label class="form-label" for="editStatusSelect">Status Proyek</label>
        <select id="editStatusSelect" class="form-control" style="cursor: pointer;">
          <option value="UNPAID">Belum Bayar (UNPAID)</option>
          <option value="PENDING_VERIFICATION">Menunggu Review (PENDING_VERIFICATION)</option>
          <option value="APPROVED">Disetujui / Rilis (APPROVED)</option>
          <option value="REJECTED">Bukti Ditolak (REJECTED)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="editGdriveUrlInput">
          Link Google Drive / SwanFlow (Folder/File Rahasia) <span style="color: #ef4444;">*</span>
        </label>
        <input type="url" id="editGdriveUrlInput" class="form-control" placeholder="https://drive.google.com/drive/folders/... atau link SwanFlow" required>
        <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 4px;">
          🔒 Link ini hanya dapat diakses klien jika status telah Disetujui / Akses Langsung.
        </small>
      </div>

      <div class="form-group">
        <label class="form-label" for="editNotesInput">Catatan Khusus (Opsional)</label>
        <textarea id="editNotesInput" class="form-control" placeholder="Catatan untuk klien..."></textarea>
      </div>

      <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
        <button type="button" class="btn btn-outline" id="btnCancelEdit">Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
          <span>💾 Simpan Perubahan</span>
        </button>
      </div>
    </form>
  </dialog>

  <!-- DIALOG: Zoom Bukti Transfer -->
  <dialog id="zoomProofModal" style="max-width: 650px;">
    <div class="modal-header">
      <h3 style="font-size: 1.2rem;">Periksa Bukti Pembayaran</h3>
      <button type="button" class="modal-close-btn" id="btnCloseZoomModal">✕</button>
    </div>
    <div style="text-align: center;">
      <div style="background: rgba(0,0,0,0.5); padding: 12px; border-radius: var(--radius-md); max-height: 480px; overflow-y: auto;">
        <img id="zoomModalImg" src="" alt="Bukti Transfer" style="max-width: 100%; border-radius: var(--radius-sm);">
      </div>
      <p id="zoomProofFileName" style="font-size: 0.82rem; color: var(--text-muted); margin-top: 8px;"></p>
    </div>

    <!-- Quick action inside zoom modal -->
    <div style="display: flex; gap: 10px; justify-content: space-between; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-glass);">
      <button type="button" class="btn btn-danger-outline" id="btnZoomReject">
        ❌ Tolak Bukti Ini
      </button>
      <button type="button" class="btn btn-success" id="btnZoomApprove">
        ✅ Setujui & Rilis Link Google Drive
      </button>
    </div>
  </dialog>

  <!-- DIALOG: Alasan Penolakan -->
  <dialog id="rejectModal">
    <div class="modal-header">
      <h3 style="font-size: 1.2rem; color: #f87171;">Tolak Bukti Pembayaran</h3>
      <button type="button" class="modal-close-btn" id="btnCloseRejectModal">✕</button>
    </div>
    <div>
      <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 12px;">
        Berikan alasan kepada klien mengapa bukti transfer ditolak agar mereka dapat memperbaikinya.
      </p>

      <div class="form-group">
        <label class="form-label" for="rejectReasonInput">Alasan Penolakan</label>
        <textarea id="rejectReasonInput" class="form-control" placeholder="Contoh: Nominal transfer kurang Rp 50.000 atau foto struk buram/tidak terbaca..."></textarea>
      </div>

      <!-- Quick Templates -->
      <div style="margin-bottom: 20px;">
        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px;">Pilih Template Cepat:</div>
        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
          <button type="button" class="btn btn-outline" style="font-size: 0.78rem; padding: 4px 10px;" onclick="document.getElementById('rejectReasonInput').value = 'Foto bukti transfer buram atau tidak terbaca.'">
            Foto Buram
          </button>
          <button type="button" class="btn btn-outline" style="font-size: 0.78rem; padding: 4px 10px;" onclick="document.getElementById('rejectReasonInput').value = 'Nominal transfer tidak sesuai dengan total tagihan.'">
            Nominal Kurang
          </button>
          <button type="button" class="btn btn-outline" style="font-size: 0.78rem; padding: 4px 10px;" onclick="document.getElementById('rejectReasonInput').value = 'Rekening tujuan atau nama penerima tidak sesuai.'">
            Salah Rekening
          </button>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-outline" id="btnCancelReject">Batal</button>
        <button type="button" class="btn btn-danger-outline" id="btnConfirmReject">Konfirmasi Tolak</button>
      </div>
    </div>
  </dialog>

  <!-- DIALOG: Pengaturan Rekening & Studio -->
  <dialog id="settingsModal">
    <div class="modal-header">
      <h3 style="font-size: 1.3rem;">Pengaturan Studio & Rekening</h3>
      <button type="button" class="modal-close-btn" id="btnCloseSettingsModal">✕</button>
    </div>

    <div style="max-height: 75vh; overflow-y: auto; padding-right: 4px;">
      
      <!-- Section: Multiple Bank Accounts -->
      <div style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-glass);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
          <h4 style="font-size: 1.05rem; color: #fff;">Daftar Rekening & E-Wallet</h4>
          <span style="font-size: 0.8rem; color: var(--text-muted);">Bisa lebih dari 1 rekening</span>
        </div>

        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 14px;">
          Klien dapat memilih transfer ke salah satu rekening atau e-wallet di bawah ini:
        </p>

        <!-- Current Accounts List -->
        <div id="adminBankAccountsList" style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
          <!-- Rendered via admin.js -->
        </div>

        <!-- Add New Bank Form Box -->
        <div style="background: rgba(15, 23, 42, 0.6); padding: 14px; border-radius: var(--radius-md); border: 1px dashed var(--border-glass);">
          <div style="font-size: 0.88rem; font-weight: 600; color: var(--accent-primary); margin-bottom: 10px;">
            + Tambah Rekening / E-Wallet Baru
          </div>
          <form id="addBankForm">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
              <div>
                <input type="text" id="newBankName" class="form-control" placeholder="Nama Bank/E-Wallet (cth: BCA, Dana)" required style="padding: 8px 12px; font-size: 0.85rem;">
              </div>
              <div>
                <input type="text" id="newAccountNumber" class="form-control" placeholder="Nomor Rekening / No. HP" required style="padding: 8px 12px; font-size: 0.85rem;">
              </div>
            </div>
            <div style="display: flex; gap: 10px;">
              <input type="text" id="newAccountName" class="form-control" placeholder="Atas Nama Pemilik (cth: LENSA ART)" required style="padding: 8px 12px; font-size: 0.85rem; flex: 1;">
              <button type="submit" class="btn btn-primary" id="btnAddBank" style="padding: 8px 16px; font-size: 0.85rem; white-space: nowrap;">
                + Tambah
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Section: General Studio Settings -->
      <form id="settingsForm">
        <h4 style="font-size: 1.05rem; color: #fff; margin-bottom: 12px;">Pengaturan Studio & Umum</h4>

        <div class="form-group">
          <label class="form-label" for="settingsStudioName">Nama Studio / Bisnis</label>
          <input type="text" id="settingsStudioName" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="settingsBankInstructions">Instruksi Pembayaran untuk Klien</label>
          <textarea id="settingsBankInstructions" class="form-control" placeholder="Petunjuk khusus sebelum/sesudah transfer..."></textarea>
        </div>

        <!-- Midtrans Configuration Section -->
        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: var(--radius-sm); padding: 14px; margin-top: 14px; margin-bottom: 14px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; margin: 0;">
              <input type="checkbox" id="settingsMidtransEnabled" style="width: 18px; height: 18px; accent-color: #10b981; cursor: pointer;">
              <span style="font-weight: 700; font-size: 0.92rem; color: #34d399;">⚡ Pembayaran Otomatis (Midtrans)</span>
            </label>
          </div>
          <small style="color: var(--text-secondary); font-size: 0.78rem; display: block; margin-bottom: 12px;">
            Aktifkan agar klien dapat langsung membayar via QRIS, GoPay, ShopeePay, dan Virtual Account bank secara otomatis.
          </small>

          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="settingsMidtransServerKey" style="font-size: 0.8rem;">Midtrans Server Key</label>
            <input type="password" id="settingsMidtransServerKey" class="form-control" placeholder="SB-Mid-server-... atau Mid-server-..." style="font-family: monospace; font-size: 0.85rem;">
          </div>

          <div class="form-group" style="margin-bottom: 10px;">
            <label class="form-label" for="settingsMidtransClientKey" style="font-size: 0.8rem;">Midtrans Client Key</label>
            <input type="text" id="settingsMidtransClientKey" class="form-control" placeholder="SB-Mid-client-... atau Mid-client-..." style="font-family: monospace; font-size: 0.85rem;">
          </div>

          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <label for="settingsMidtransIsProduction" style="font-size: 0.82rem; color: var(--text-secondary); cursor: pointer; margin: 0;">
              Mode Produksi (Live)
            </label>
            <input type="checkbox" id="settingsMidtransIsProduction" style="width: 16px; height: 16px; accent-color: #10b981; cursor: pointer;">
          </div>

          <div style="background: rgba(0,0,0,0.3); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-glass);">
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 2px;">Webhook Notification URL:</div>
            <code style="font-size: 0.78rem; color: #34d399; font-family: monospace; word-break: break-all; user-select: all;">{{ url('/api/midtrans/webhook') }}</code>
          </div>
        </div>

        <div class="form-group" style="padding-top: 10px; border-top: 1px solid var(--border-glass);">
          <label class="form-label" for="settingsNewPin">Ganti PIN Admin (Opsional)</label>
          <input type="password" id="settingsNewPin" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah PIN">
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
          <button type="button" class="btn btn-outline" id="btnCancelSettings">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btnSaveSettings">Simpan Pengaturan</button>
        </div>
      </form>

    </div>
  </dialog>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- Admin Script -->
  <script src="/js/admin.js"></script>
</body>
</html>
