document.addEventListener('DOMContentLoaded', () => {
  // State
  let projects = [];
  let currentSettings = {};
  let currentFilter = 'all';
  let searchQuery = '';
  let activeProjectId = null;
  let pollInterval = null;

  // DOM Elements
  const loginOverlay = document.getElementById('loginOverlay');
  const loginForm = document.getElementById('loginForm');
  const pinInput = document.getElementById('pinInput');
  const adminApp = document.getElementById('adminApp');
  const navStudioName = document.getElementById('navStudioName');
  const btnLogout = document.getElementById('btnLogout');

  const statTotal = document.getElementById('statTotal');
  const statPending = document.getElementById('statPending');
  const statApproved = document.getElementById('statApproved');
  const statUnpaid = document.getElementById('statUnpaid');
  const pendingAlertBadge = document.getElementById('pendingAlertBadge');

  const searchInput = document.getElementById('searchInput');
  const filterTabs = document.getElementById('filterTabs');
  const projectsList = document.getElementById('projectsList');
  const emptyState = document.getElementById('emptyState');

  // Modals
  const createProjectModal = document.getElementById('createProjectModal');
  const btnOpenCreateModal = document.getElementById('btnOpenCreateModal');
  const btnCloseCreateModal = document.getElementById('btnCloseCreateModal');
  const btnCancelCreate = document.getElementById('btnCancelCreate');
  const createProjectForm = document.getElementById('createProjectForm');

  const editProjectModal = document.getElementById('editProjectModal');
  const btnCloseEditModal = document.getElementById('btnCloseEditModal');
  const btnCancelEdit = document.getElementById('btnCancelEdit');
  const editProjectForm = document.getElementById('editProjectForm');
  const editProjectId = document.getElementById('editProjectId');
  const editClientNameInput = document.getElementById('editClientNameInput');
  const editClientPhoneInput = document.getElementById('editClientPhoneInput');
  const editProjectTitleInput = document.getElementById('editProjectTitleInput');
  const editTokenInput = document.getElementById('editTokenInput');
  const editIsFreeInput = document.getElementById('editIsFreeInput');
  const editPricingFieldsContainer = document.getElementById('editPricingFieldsContainer');
  const editAmountInput = document.getElementById('editAmountInput');
  const editDiscountInput = document.getElementById('editDiscountInput');
  const editDiscountLabelInput = document.getElementById('editDiscountLabelInput');
  const editPreviewFinalAmount = document.getElementById('editPreviewFinalAmount');
  const editStatusSelect = document.getElementById('editStatusSelect');
  const editGdriveUrlInput = document.getElementById('editGdriveUrlInput');
  const editNotesInput = document.getElementById('editNotesInput');

  const zoomProofModal = document.getElementById('zoomProofModal');
  const zoomModalImg = document.getElementById('zoomModalImg');
  const zoomProofFileName = document.getElementById('zoomProofFileName');
  const btnCloseZoomModal = document.getElementById('btnCloseZoomModal');
  const btnZoomApprove = document.getElementById('btnZoomApprove');
  const btnZoomReject = document.getElementById('btnZoomReject');

  const rejectModal = document.getElementById('rejectModal');
  const rejectReasonInput = document.getElementById('rejectReasonInput');
  const btnCloseRejectModal = document.getElementById('btnCloseRejectModal');
  const btnCancelReject = document.getElementById('btnCancelReject');
  const btnConfirmReject = document.getElementById('btnConfirmReject');

  const settingsModal = document.getElementById('settingsModal');
  const btnOpenSettings = document.getElementById('btnOpenSettings');
  const btnCloseSettingsModal = document.getElementById('btnCloseSettingsModal');
  const btnCancelSettings = document.getElementById('btnCancelSettings');
  const settingsForm = document.getElementById('settingsForm');

  // Format Currency
  function formatIDR(number) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(number);
  }

  // Format Date
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  // Toast Notification
  function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast';
    const icon = type === 'success' ? '✅' : type === 'error' ? '⚠️' : 'ℹ️';
    toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  }

  // Check Authentication
  async function checkAuth() {
    try {
      const res = await fetch('/api/admin/me');
      const data = await res.json();
      if (data.authenticated) {
        showDashboard();
      } else {
        showLogin();
      }
    } catch (err) {
      console.error(err);
      showLogin();
    }
  }

  function showLogin() {
    loginOverlay.style.display = 'flex';
    adminApp.style.display = 'none';
    if (pollInterval) clearInterval(pollInterval);
  }

  function showDashboard() {
    loginOverlay.style.display = 'none';
    adminApp.style.display = 'block';
    fetchSettings();
    fetchProjects();

    // Auto refresh projects every 12 seconds
    if (!pollInterval) {
      pollInterval = setInterval(() => {
        fetchProjects(true);
      }, 12000);
    }
  }

  // Login Handler
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const pin = pinInput.value.trim();
    if (!pin) return;

    try {
      const res = await fetch('/api/admin/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pin })
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'PIN admin salah.');
      }

      showToast('Login berhasil!', 'success');
      pinInput.value = '';
      showDashboard();
    } catch (err) {
      showToast(err.message, 'error');
    }
  });

  // Logout Handler
  btnLogout.addEventListener('click', async () => {
    try {
      await fetch('/api/admin/logout', { method: 'POST' });
      showToast('Anda telah keluar.', 'info');
      showLogin();
    } catch (err) {
      console.error(err);
      showLogin();
    }
  });

  // Fetch Settings
  async function fetchSettings() {
    try {
      const res = await fetch('/api/admin/settings');
      if (res.ok) {
        const data = await res.json();
        currentSettings = data.settings || {};
        if (currentSettings.studio_name) {
          navStudioName.textContent = currentSettings.studio_name;
        }
      }
    } catch (err) {
      console.error(err);
    }
  }

  // Fetch Projects
  async function fetchProjects(isSilent = false) {
    try {
      const res = await fetch('/api/admin/projects');
      if (res.status === 401) {
        showLogin();
        return;
      }
      const data = await res.json();
      projects = data.projects || [];
      renderDashboard();
    } catch (err) {
      console.error(err);
      if (!isSilent) showToast('Gagal memuat daftar proyek.', 'error');
    }
  }

  // Render Dashboard
  function renderDashboard() {
    // 1. Calculate Stats
    const total = projects.length;
    const pending = projects.filter(p => p.status === 'PENDING_VERIFICATION').length;
    const approved = projects.filter(p => p.status === 'APPROVED').length;
    const unpaid = projects.filter(p => p.status === 'UNPAID' || p.status === 'REJECTED').length;

    statTotal.textContent = total;
    statPending.textContent = pending;
    statApproved.textContent = approved;
    statUnpaid.textContent = unpaid;

    if (pending > 0) {
      pendingAlertBadge.style.display = 'inline-flex';
      pendingAlertBadge.textContent = `${pending} Perlu Review`;
    } else {
      pendingAlertBadge.style.display = 'none';
    }

    // 2. Filter & Search
    let filtered = projects;

    if (currentFilter !== 'all') {
      filtered = filtered.filter(p => p.status === currentFilter);
    }

    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim();
      filtered = filtered.filter(p => 
        (p.client_name && p.client_name.toLowerCase().includes(q)) ||
        (p.project_title && p.project_title.toLowerCase().includes(q)) ||
        (p.client_phone && p.client_phone.includes(q))
      );
    }

    // 3. Render List
    projectsList.innerHTML = '';
    if (filtered.length === 0) {
      emptyState.style.display = 'block';
    } else {
      emptyState.style.display = 'none';
      filtered.forEach(p => {
        projectsList.appendChild(createProjectCard(p));
      });
    }
  }

  // Create Project Card Element
  function createProjectCard(p) {
    const card = document.createElement('div');
    card.className = 'project-card';

    const isFree = Boolean(p.is_free);

    // Status Pill
    let statusClass = 'status-unpaid';
    let statusLabel = 'Belum Bayar';
    if (isFree) {
      statusClass = 'status-approved';
      statusLabel = 'Akses Langsung';
    } else if (p.status === 'PENDING_VERIFICATION') {
      statusClass = 'status-pending';
      statusLabel = 'Menunggu Review';
    } else if (p.status === 'APPROVED') {
      statusClass = 'status-approved';
      statusLabel = 'Disetujui (Rilis)';
    } else if (p.status === 'REJECTED') {
      statusClass = 'status-rejected';
      statusLabel = 'Bukti Ditolak';
    }

    const clientUrl = `${window.location.origin}/p/${p.token}`;

    card.innerHTML = `
      <div class="project-header">
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="status-pill ${statusClass}">
              <span class="dot"></span>
              <span>${statusLabel}</span>
            </span>
            ${isFree ? `
              <span style="font-size: 0.72rem; font-weight: 700; color: #38bdf8; background: rgba(56,189,248,0.15); border: 1px solid rgba(56,189,248,0.3); padding: 1px 6px; border-radius: 999px;">
                ⚡ Akses Langsung
              </span>
            ` : ''}
            <span style="font-size: 0.8rem; color: var(--text-muted);">ID: #${p.id}</span>
          </div>
          <h3 style="font-size: 1.25rem; margin-top: 6px; color: #fff;">${escapeHtml(p.project_title)}</h3>
          <div style="color: var(--text-secondary); font-size: 0.88rem; margin-top: 2px;">
            Klien: <strong style="color: #f1f5f9;">${escapeHtml(p.client_name)}</strong>
            ${p.client_phone ? ` • <span style="color: #94a3b8;">${escapeHtml(p.client_phone)}</span>` : ''}
          </div>
        </div>

        <div style="text-align: right;">
          ${isFree ? `
            <div style="font-size: 1.2rem; font-weight: 800; color: #38bdf8; font-family: var(--font-heading);">
              Akses Langsung
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
              Tanpa Tagihan
            </div>
            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
              Dibuat: ${formatDate(p.created_at)}
            </div>
          ` : `
            ${(p.discount && p.discount > 0) ? `
              <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px; margin-bottom: 2px;">
                <span style="font-size: 0.82rem; text-decoration: line-through; color: var(--text-muted);">${formatIDR(p.amount)}</span>
                <span style="font-size: 0.72rem; font-weight: 700; color: #f87171; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); padding: 1px 6px; border-radius: 999px;">
                  -${formatIDR(p.discount)}
                </span>
              </div>
            ` : ''}
            <div style="font-size: 1.35rem; font-weight: 800; color: #34d399; font-family: var(--font-heading);">
              ${formatIDR(p.final_amount ?? (p.amount - (p.discount || 0)))}
            </div>
            ${p.discount_label ? `
              <div style="font-size: 0.75rem; color: #10b981; margin-top: 1px;">🎉 ${escapeHtml(p.discount_label)}</div>
            ` : ''}
            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
              Dibuat: ${formatDate(p.created_at)}
            </div>
          `}
        </div>
      </div>

      <div class="project-body">
        <div>
          <div style="display: flex; gap: 8px; align-items: center; font-size: 0.85rem; color: var(--text-secondary); background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.05); margin-bottom: 12px;">
            <span>📁 GDrive:</span>
            <a href="${escapeHtml(p.gdrive_url)}" target="_blank" style="color: var(--accent-primary); word-break: break-all; text-decoration: underline;" title="Cek Link Google Drive">
              ${escapeHtml(p.gdrive_url.length > 50 ? p.gdrive_url.substring(0, 50) + '...' : p.gdrive_url)}
            </a>
          </div>

          ${p.notes ? `
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">
              Catatan: <em>${escapeHtml(p.notes)}</em>
            </div>
          ` : ''}

          ${p.reject_reason ? `
            <div style="font-size: 0.85rem; color: #f87171; background: var(--danger-bg); padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--danger-border); margin-bottom: 8px;">
              Alasan Ditolak: "${escapeHtml(p.reject_reason)}"
            </div>
          ` : ''}
        </div>

        ${p.proof_image ? `
          <div style="text-align: center;">
            <img src="/uploads/${escapeHtml(p.proof_image)}" class="proof-thumbnail" alt="Bukti Transfer" title="Klik untuk memperbesar gambar">
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Bukti Transfer</div>
          </div>
        ` : `
          <div style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 10px 16px; border: 1px dashed var(--border-glass); border-radius: var(--radius-sm);">
            ${isFree ? '⚡ Akses Langsung (Tanpa Bukti)' : 'Belum ada bukti'}
          </div>
        `}
      </div>

      <!-- Action Buttons -->
      <div class="actions-row" style="padding-top: 14px; border-top: 1px solid var(--border-glass); flex-wrap: wrap; gap: 8px;">
        <!-- Copy Link -->
        <button type="button" class="btn btn-outline btn-copy-link" data-url="${clientUrl}" style="font-size: 0.85rem; padding: 7px 12px;">
          📋 Salin Link Klien
        </button>

        <!-- Share WhatsApp -->
        <button type="button" class="btn btn-outline btn-share-wa" data-phone="${p.client_phone || ''}" data-name="${escapeHtml(p.client_name)}" data-title="${escapeHtml(p.project_title)}" data-url="${clientUrl}" style="font-size: 0.85rem; padding: 7px 12px; color: #22c55e;">
          💬 Kirim WhatsApp
        </button>

        <!-- Edit Project & Link -->
        <button type="button" class="btn btn-outline btn-edit-project" style="font-size: 0.85rem; padding: 7px 12px; color: #f59e0b; border-color: rgba(245,158,11,0.4);" title="Ubah rincian data & link proyek">
          ✏️ Edit Link
        </button>

        ${p.status !== 'APPROVED' ? `
          <button type="button" class="btn btn-outline btn-make-free" data-id="${p.id}" style="font-size: 0.82rem; padding: 7px 11px; color: #38bdf8; border-color: rgba(56,189,248,0.4);" title="Bebaskan tagihan dan rilis link langsung">
            ⚡ Akses Langsung
          </button>
          <button type="button" class="btn btn-success btn-approve" data-id="${p.id}" style="font-size: 0.85rem; padding: 7px 14px; margin-left: auto;">
            ✅ Setujui & Rilis Link
          </button>
          <button type="button" class="btn btn-danger-outline btn-reject" data-id="${p.id}" style="font-size: 0.85rem; padding: 7px 14px;">
            ❌ Tolak
          </button>
        ` : `
          <span style="font-size: 0.85rem; color: #34d399; font-weight: 600; margin-left: auto; display: flex; align-items: center; gap: 4px;">
            <span>${isFree ? '⚡ File Siap Diakses' : '🔓 File Sudah Terbuka'}</span>
          </span>
          <button type="button" class="btn btn-outline btn-revert" data-id="${p.id}" style="font-size: 0.8rem; padding: 6px 10px; color: var(--text-muted);" title="Kunci kembali link jika terjadi kekeliruan">
            🔒 Kunci Kembali
          </button>
        `}

        <button type="button" class="btn btn-outline btn-delete" data-id="${p.id}" style="font-size: 0.85rem; padding: 7px 10px; color: #ef4444;" title="Hapus proyek">
          🗑️
        </button>
      </div>
    `;

    // Event Listeners for Card Buttons
    const btnCopy = card.querySelector('.btn-copy-link');
    btnCopy.addEventListener('click', () => {
      navigator.clipboard.writeText(clientUrl).then(() => {
        btnCopy.textContent = 'Tersalin! ✓';
        btnCopy.style.borderColor = 'var(--success)';
        showToast(`Link untuk "${p.client_name}" disalin ke clipboard!`, 'success');
        setTimeout(() => {
          btnCopy.textContent = '📋 Salin Link Klien';
          btnCopy.style.borderColor = '';
        }, 2000);
      });
    });

    const btnWa = card.querySelector('.btn-share-wa');
    btnWa.addEventListener('click', () => {
      let phone = p.client_phone ? p.client_phone.replace(/\D/g, '') : '';
      if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
      }
      const message = isFree
        ? `Halo Kak ${p.client_name},\n\nFile foto/video dokumentasi "${p.project_title}" telah selesai kami proses dan siap diakses. Silakan buka tautan resmi berikut untuk mengunduh file Google Drive Anda:\n\n${clientUrl}\n\nTerima kasih! 🙏`
        : `Halo Kak ${p.client_name},\n\nFile foto/video dokumentasi "${p.project_title}" telah selesai kami proses. Silakan buka tautan resmi berikut untuk rincian pembayaran dan membuka file Google Drive Anda:\n\n${clientUrl}\n\nTerima kasih! 🙏`;
      const waUrl = phone 
        ? `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
        : `https://wa.me/?text=${encodeURIComponent(message)}`;
      window.open(waUrl, '_blank');
    });

    // Edit Button
    const btnEdit = card.querySelector('.btn-edit-project');
    if (btnEdit) {
      btnEdit.addEventListener('click', () => {
        openEditModal(p);
      });
    }

    // Zoom Image click
    const thumb = card.querySelector('.proof-thumbnail');
    if (thumb) {
      thumb.addEventListener('click', () => {
        openZoomModal(p);
      });
    }

    // Approve Button
    const btnApprove = card.querySelector('.btn-approve');
    if (btnApprove) {
      btnApprove.addEventListener('click', () => {
        verifyProjectAction(p.id, 'APPROVED');
      });
    }

    // Make Free Button (Direct Access)
    const btnMakeFree = card.querySelector('.btn-make-free');
    if (btnMakeFree) {
      btnMakeFree.addEventListener('click', async () => {
        if (confirm(`Bebaskan tagihan untuk "${p.client_name}"? Link akan langsung terbuka tanpa meminta pembayaran, tanpa keterangan gratis, dan tanpa nominal Rp 0.`)) {
          try {
            const res = await fetch(`/api/admin/projects/${p.id}/make-free`, {
              method: 'POST'
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Gagal mengubah proyek menjadi akses langsung.');
            showToast('Link berhasil diubah menjadi Akses Langsung!', 'success');
            fetchProjects();
          } catch (err) {
            showToast(err.message, 'error');
          }
        }
      });
    }

    // Reject Button
    const btnReject = card.querySelector('.btn-reject');
    if (btnReject) {
      btnReject.addEventListener('click', () => {
        activeProjectId = p.id;
        rejectReasonInput.value = '';
        rejectModal.showModal();
      });
    }

    // Revert Lock Button
    const btnRevert = card.querySelector('.btn-revert');
    if (btnRevert) {
      btnRevert.addEventListener('click', () => {
        if (confirm(`Apakah Anda yakin ingin mengunci kembali link Google Drive untuk "${p.client_name}"?`)) {
          verifyProjectAction(p.id, 'PENDING_VERIFICATION');
        }
      });
    }

    // Delete Button
    const btnDelete = card.querySelector('.btn-delete');
    btnDelete.addEventListener('click', async () => {
      if (confirm(`Hapus proyek "${p.project_title}" (${p.client_name})? Data dan file bukti transfer akan dihapus permanen.`)) {
        try {
          const res = await fetch(`/api/admin/projects/${p.id}`, { method: 'DELETE' });
          if (res.ok) {
            showToast('Proyek berhasil dihapus.', 'info');
            fetchProjects();
          } else {
            showToast('Gagal menghapus proyek.', 'error');
          }
        } catch (e) {
          console.error(e);
        }
      }
    });

    return card;
  }

  // Open Zoom Proof Modal
  function openZoomModal(p) {
    activeProjectId = p.id;
    zoomModalImg.src = `/uploads/${p.proof_image}`;
    zoomProofFileName.textContent = `File: ${p.proof_original_name || p.proof_image} • Diupload: ${formatDate(p.proof_uploaded_at)}`;
    
    if (p.status === 'APPROVED') {
      btnZoomApprove.style.display = 'none';
      btnZoomReject.style.display = 'inline-flex';
    } else {
      btnZoomApprove.style.display = 'inline-flex';
      btnZoomReject.style.display = 'inline-flex';
    }

    zoomProofModal.showModal();
  }

  btnCloseZoomModal.addEventListener('click', () => zoomProofModal.close());
  btnZoomApprove.addEventListener('click', () => {
    if (activeProjectId) {
      verifyProjectAction(activeProjectId, 'APPROVED');
      zoomProofModal.close();
    }
  });
  btnZoomReject.addEventListener('click', () => {
    zoomProofModal.close();
    if (activeProjectId) {
      rejectReasonInput.value = '';
      rejectModal.showModal();
    }
  });

  // Reject Confirmation
  btnCloseRejectModal.addEventListener('click', () => rejectModal.close());
  btnCancelReject.addEventListener('click', () => rejectModal.close());
  btnConfirmReject.addEventListener('click', () => {
    if (!activeProjectId) return;
    const reason = rejectReasonInput.value.trim() || 'Bukti pembayaran belum sesuai.';
    verifyProjectAction(activeProjectId, 'REJECTED', reason);
    rejectModal.close();
  });

  // Verify Project API Call
  async function verifyProjectAction(id, status, rejectReason = null) {
    try {
      const res = await fetch(`/api/admin/projects/${id}/verify`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status, reject_reason: rejectReason })
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Gagal mengubah status verifikasi.');

      showToast(data.message, 'success');
      fetchProjects();
    } catch (err) {
      showToast(err.message, 'error');
    }
  }

  // Filter Tabs click
  filterTabs.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', () => {
      filterTabs.querySelectorAll('button').forEach(b => {
        b.className = 'btn btn-outline';
      });
      btn.className = 'btn btn-primary';
      currentFilter = btn.getAttribute('data-filter');
      renderDashboard();
    });
  });

  // Search Input
  searchInput.addEventListener('input', (e) => {
    searchQuery = e.target.value;
    renderDashboard();
  });

  // Create Project Modal Handlers
  function updateCreateCalcPreview() {
    const amt = parseInt(document.getElementById('amountInput').value, 10) || 0;
    const disc = parseInt(document.getElementById('discountInput').value, 10) || 0;
    const finalAmt = Math.max(0, amt - disc);
    const previewEl = document.getElementById('previewFinalAmount');
    if (previewEl) {
      previewEl.textContent = formatIDR(finalAmt);
    }
  }

  const amountInputEl = document.getElementById('amountInput');
  const discountInputEl = document.getElementById('discountInput');
  const isFreeInput = document.getElementById('isFreeInput');
  const pricingFieldsContainer = document.getElementById('pricingFieldsContainer');

  function updateIsFreeState() {
    if (!isFreeInput) return;
    if (isFreeInput.checked) {
      if (pricingFieldsContainer) pricingFieldsContainer.style.display = 'none';
      if (amountInputEl) {
        amountInputEl.removeAttribute('required');
        amountInputEl.value = '0';
      }
      if (discountInputEl) discountInputEl.value = '0';
    } else {
      if (pricingFieldsContainer) pricingFieldsContainer.style.display = 'block';
      if (amountInputEl) {
        amountInputEl.setAttribute('required', 'required');
        if (amountInputEl.value === '0') amountInputEl.value = '';
      }
    }
    updateCreateCalcPreview();
  }

  if (isFreeInput) {
    isFreeInput.addEventListener('change', updateIsFreeState);
  }

  if (amountInputEl) amountInputEl.addEventListener('input', updateCreateCalcPreview);
  if (discountInputEl) discountInputEl.addEventListener('input', updateCreateCalcPreview);

  btnOpenCreateModal.addEventListener('click', () => {
    createProjectForm.reset();
    if (isFreeInput) isFreeInput.checked = false;
    updateIsFreeState();
    document.getElementById('discountInput').value = '0';
    updateCreateCalcPreview();
    createProjectModal.showModal();
  });
  btnCloseCreateModal.addEventListener('click', () => createProjectModal.close());
  btnCancelCreate.addEventListener('click', () => createProjectModal.close());

  createProjectForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const is_free = isFreeInput && isFreeInput.checked ? 1 : 0;
    const client_name = document.getElementById('clientNameInput').value.trim();
    const client_phone = document.getElementById('clientPhoneInput').value.trim();
    const project_title = document.getElementById('projectTitleInput').value.trim();
    const amount = document.getElementById('amountInput').value.trim();
    const discount = document.getElementById('discountInput').value.trim();
    const discount_label = document.getElementById('discountLabelInput').value.trim();
    const gdrive_url = document.getElementById('gdriveUrlInput').value.trim();
    const notes = document.getElementById('notesInput').value.trim();

    try {
      const res = await fetch('/api/admin/projects', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          client_name,
          client_phone,
          project_title,
          amount: is_free ? 0 : amount,
          discount: is_free ? 0 : discount,
          discount_label: is_free ? '' : discount_label,
          gdrive_url,
          notes,
          is_free
        })
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Gagal membuat proyek.');

      showToast(is_free ? 'Link akses langsung berhasil dibuat!' : 'Proyek baru berhasil dibuat!', 'success');
      createProjectModal.close();
      fetchProjects();

      // Show instant prompt to copy new link
      const newUrl = `${window.location.origin}/p/${data.project.token}`;
      navigator.clipboard.writeText(newUrl);
      showToast(`Link klien otomatis disalin ke clipboard! (${newUrl})`, 'success');

    } catch (err) {
      showToast(err.message, 'error');
    }
  });

  // Edit Project Handlers
  function updateEditCalcPreview() {
    const amt = parseInt(editAmountInput.value, 10) || 0;
    const disc = parseInt(editDiscountInput.value, 10) || 0;
    const finalAmt = Math.max(0, amt - disc);
    if (editPreviewFinalAmount) {
      editPreviewFinalAmount.textContent = formatIDR(finalAmt);
    }
  }

  if (editAmountInput) editAmountInput.addEventListener('input', updateEditCalcPreview);
  if (editDiscountInput) editDiscountInput.addEventListener('input', updateEditCalcPreview);

  function updateEditIsFreeState() {
    if (!editIsFreeInput) return;
    if (editIsFreeInput.checked) {
      if (editPricingFieldsContainer) editPricingFieldsContainer.style.display = 'none';
      if (editAmountInput) {
        editAmountInput.removeAttribute('required');
      }
    } else {
      if (editPricingFieldsContainer) editPricingFieldsContainer.style.display = 'block';
      if (editAmountInput) {
        editAmountInput.setAttribute('required', 'required');
      }
    }
    updateEditCalcPreview();
  }

  if (editIsFreeInput) {
    editIsFreeInput.addEventListener('change', updateEditIsFreeState);
  }

  function openEditModal(p) {
    editProjectId.value = p.id;
    editClientNameInput.value = p.client_name || '';
    editClientPhoneInput.value = p.client_phone || '';
    editProjectTitleInput.value = p.project_title || '';
    editTokenInput.value = p.token || '';
    editIsFreeInput.checked = Boolean(p.is_free);
    editAmountInput.value = p.amount || 0;
    editDiscountInput.value = p.discount || 0;
    editDiscountLabelInput.value = p.discount_label || '';
    editStatusSelect.value = p.status || 'UNPAID';
    editGdriveUrlInput.value = p.gdrive_url || '';
    editNotesInput.value = p.notes || '';

    updateEditIsFreeState();
    editProjectModal.showModal();
  }

  if (btnCloseEditModal) btnCloseEditModal.addEventListener('click', () => editProjectModal.close());
  if (btnCancelEdit) btnCancelEdit.addEventListener('click', () => editProjectModal.close());

  if (editProjectForm) {
    editProjectForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = editProjectId.value;
      const is_free = editIsFreeInput && editIsFreeInput.checked ? 1 : 0;
      const client_name = editClientNameInput.value.trim();
      const client_phone = editClientPhoneInput.value.trim();
      const project_title = editProjectTitleInput.value.trim();
      const token = editTokenInput.value.trim();
      const amount = editAmountInput.value.trim();
      const discount = editDiscountInput.value.trim();
      const discount_label = editDiscountLabelInput.value.trim();
      const status = editStatusSelect.value;
      const gdrive_url = editGdriveUrlInput.value.trim();
      const notes = editNotesInput.value.trim();

      try {
        const res = await fetch(`/api/admin/projects/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            client_name,
            client_phone,
            project_title,
            token,
            amount: is_free ? 0 : amount,
            discount: is_free ? 0 : discount,
            discount_label: is_free ? '' : discount_label,
            status,
            gdrive_url,
            notes,
            is_free
          })
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Gagal menyimpan perubahan.');

        showToast('Perubahan data dan link proyek berhasil disimpan!', 'success');
        editProjectModal.close();
        fetchProjects();
      } catch (err) {
        showToast(err.message, 'error');
      }
    });
  }

  // Multiple Bank Accounts State & Functions
  let adminBankAccounts = [];
  const adminBankAccountsList = document.getElementById('adminBankAccountsList');
  const addBankForm = document.getElementById('addBankForm');
  const newBankName = document.getElementById('newBankName');
  const newAccountNumber = document.getElementById('newAccountNumber');
  const newAccountName = document.getElementById('newAccountName');

  async function fetchBankAccounts() {
    try {
      const res = await fetch('/api/admin/bank-accounts');
      if (res.ok) {
        const data = await res.json();
        adminBankAccounts = data.bank_accounts || [];
        renderAdminBankAccounts();
      }
    } catch (err) {
      console.error('Error fetching bank accounts:', err);
    }
  }

  function renderAdminBankAccounts() {
    if (!adminBankAccountsList) return;
    adminBankAccountsList.innerHTML = '';

    if (adminBankAccounts.length === 0) {
      adminBankAccountsList.innerHTML = '<div style="color: var(--text-muted); font-size: 0.85rem; padding: 8px;">Belum ada rekening aktif. Silakan tambahkan minimal 1 rekening.</div>';
      return;
    }

    adminBankAccounts.forEach(acc => {
      const item = document.createElement('div');
      item.style.cssText = 'display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.25); border: 1px solid var(--border-glass); border-radius: var(--radius-sm); padding: 10px 14px;';
      item.innerHTML = `
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 0.8rem; font-weight: 700; color: #fff; background: rgba(99,102,241,0.3); border: 1px solid rgba(99,102,241,0.5); padding: 2px 8px; border-radius: 4px;">
              ${escapeHtml(acc.bank_name)}
            </span>
            <span style="font-family: monospace; font-size: 1.05rem; font-weight: 700; color: #f8fafc;">
              ${escapeHtml(acc.account_number)}
            </span>
          </div>
          <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 2px;">
            a.n <strong style="color: #cbd5e1;">${escapeHtml(acc.account_name)}</strong>
          </div>
        </div>
        <div>
          <button type="button" class="btn btn-outline btn-del-acc" style="color: #ef4444; border-color: rgba(239,68,68,0.3); padding: 6px 10px; font-size: 0.8rem;" title="Hapus rekening ini">
            🗑️ Hapus
          </button>
        </div>
      `;

      const btnDel = item.querySelector('.btn-del-acc');
      btnDel.addEventListener('click', async () => {
        if (confirm(`Hapus rekening ${acc.bank_name} (${acc.account_number})?`)) {
          try {
            const res = await fetch(`/api/admin/bank-accounts/${acc.id}`, { method: 'DELETE' });
            if (res.ok) {
              showToast(`Rekening ${acc.bank_name} berhasil dihapus.`, 'info');
              fetchBankAccounts();
            } else {
              showToast('Gagal menghapus rekening.', 'error');
            }
          } catch (e) {
            console.error(e);
          }
        }
      });

      adminBankAccountsList.appendChild(item);
    });
  }

  // Add Bank Form Listener
  if (addBankForm) {
    addBankForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const bank_name = newBankName.value.trim();
      const account_number = newAccountNumber.value.trim();
      const account_name = newAccountName.value.trim();

      if (!bank_name || !account_number || !account_name) return;

      try {
        const res = await fetch('/api/admin/bank-accounts', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ bank_name, account_number, account_name })
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Gagal menambahkan rekening.');

        showToast(`Rekening ${bank_name} berhasil ditambahkan!`, 'success');
        addBankForm.reset();
        fetchBankAccounts();
      } catch (err) {
        showToast(err.message, 'error');
      }
    });
  }

  // Settings Modal Handlers
  btnOpenSettings.addEventListener('click', () => {
    document.getElementById('settingsStudioName').value = currentSettings.studio_name || '';
    document.getElementById('settingsBankInstructions').value = currentSettings.bank_instructions || '';
    document.getElementById('settingsNewPin').value = '';
    fetchBankAccounts();
    settingsModal.showModal();
  });

  btnCloseSettingsModal.addEventListener('click', () => settingsModal.close());
  btnCancelSettings.addEventListener('click', () => settingsModal.close());

  settingsForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
      studio_name: document.getElementById('settingsStudioName').value.trim(),
      bank_instructions: document.getElementById('settingsBankInstructions').value.trim()
    };

    const newPin = document.getElementById('settingsNewPin').value.trim();
    if (newPin) {
      payload.admin_pin = newPin;
    }

    try {
      const res = await fetch('/api/admin/settings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Gagal menyimpan pengaturan.');

      currentSettings = data.settings;
      navStudioName.textContent = currentSettings.studio_name;
      showToast('Pengaturan studio berhasil disimpan!', 'success');
      settingsModal.close();
    } catch (err) {
      showToast(err.message, 'error');
    }
  });

  // Utility to prevent HTML injection
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Close dialog modals on backdrop click
  [createProjectModal, editProjectModal, zoomProofModal, rejectModal, settingsModal].forEach(dialog => {
    if (!dialog) return;
    dialog.addEventListener('click', (e) => {
      const rect = dialog.getBoundingClientRect();
      if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
        dialog.close();
      }
    });
  });

  // Start Auth Check
  checkAuth();
});
