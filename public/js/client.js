document.addEventListener('DOMContentLoaded', () => {
  // Extract token from URL: /p/:token
  const pathParts = window.location.pathname.split('/p/');
  const token = pathParts[1] ? pathParts[1].replace(/\/$/, '') : null;

  if (!token) {
    showError('Link Tidak Lengkap', 'Format URL tidak valid. Pastikan Anda membuka link lengkap yang diberikan admin.');
    return;
  }

  // State
  let currentProject = null;
  let cachedSettings = null;
  let cachedBankAccounts = [];
  let selectedFile = null;
  let currentObjectUrl = null;
  let isUploading = false;
  let pollInterval = null;
  let hasCelebrated = false;

  // DOM Elements
  const loadingState = document.getElementById('loadingState');
  const errorState = document.getElementById('errorState');
  const projectContent = document.getElementById('projectContent');

  const studioNameDisplay = document.getElementById('studioNameDisplay');
  const headerStatusBadge = document.getElementById('headerStatusBadge');
  const headerStatusText = document.getElementById('headerStatusText');

  const projectTitle = document.getElementById('projectTitle');
  const clientName = document.getElementById('clientName');
  const billingSection = document.getElementById('billingSection');
  const amountDisplay = document.getElementById('amountDisplay');
  const discountBox = document.getElementById('discountBox');
  const originalAmountDisplay = document.getElementById('originalAmountDisplay');
  const discountPill = document.getElementById('discountPill');
  const discountLabelText = document.getElementById('discountLabelText');
  const projectNotesBox = document.getElementById('projectNotesBox');
  const projectNotesText = document.getElementById('projectNotesText');

  const approvedSection = document.getElementById('approvedSection');
  const approvedHeroTitle = document.getElementById('approvedHeroTitle');
  const approvedHeroDesc = document.getElementById('approvedHeroDesc');
  const pendingSection = document.getElementById('pendingSection');
  const rejectedSection = document.getElementById('rejectedSection');
  const rejectedReasonText = document.getElementById('rejectedReasonText');
  const paymentFormSection = document.getElementById('paymentFormSection');

  const gdriveButton = document.getElementById('gdriveButton');
  const btnRefreshStatus = document.getElementById('btnRefreshStatus');

  const bankAccountsList = document.getElementById('bankAccountsList');
  const bankCountBadge = document.getElementById('bankCountBadge');
  const bankInstructionText = document.getElementById('bankInstructionText');

  const qrisContainer = document.getElementById('qrisContainer');
  const btnShowQris = document.getElementById('btnShowQris');
  const qrisModal = document.getElementById('qrisModal');
  const qrisModalImg = document.getElementById('qrisModalImg');
  const btnCloseQrisModal = document.getElementById('btnCloseQrisModal');

  const uploadForm = document.getElementById('uploadForm');
  const proofFileInput = document.getElementById('proofFileInput');
  const dropzoneBox = document.getElementById('dropzoneBox');
  const previewContainer = document.getElementById('previewContainer');
  const previewImg = document.getElementById('previewImg');
  const previewFileName = document.getElementById('previewFileName');
  const btnRemovePreview = document.getElementById('btnRemovePreview');
  const btnSubmitProof = document.getElementById('btnSubmitProof');
  const btnSubmitProofText = document.getElementById('btnSubmitProofText');

  // Progress Bar DOM Elements
  const uploadProgressBox = document.getElementById('uploadProgressBox');
  const uploadProgressBar = document.getElementById('uploadProgressBar');
  const uploadProgressPercent = document.getElementById('uploadProgressPercent');
  const uploadProgressBytes = document.getElementById('uploadProgressBytes');
  const uploadProgressStatusText = document.getElementById('uploadProgressStatusText');
  const uploadProgressSub = document.getElementById('uploadProgressSub');

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Format IDR Currency
  function formatIDR(number) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(number);
  }

  // Toast Notification
  function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
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

  function showError(title, desc) {
    loadingState.style.display = 'none';
    projectContent.style.display = 'none';
    errorState.style.display = 'block';
    document.getElementById('errorMessageTitle').textContent = title;
    document.getElementById('errorMessageDesc').textContent = desc;
  }

  // Fetch Project Data
  async function fetchProjectData(isPolling = false) {
    try {
      const res = await fetch(`/api/p/${token}`);
      if (!res.ok) {
        const data = await res.json().catch(() => ({}));
        showError('Proyek Tidak Ditemukan', data.error || 'Link ini tidak valid atau sudah kadaluarsa.');
        if (pollInterval) {
          clearInterval(pollInterval);
          pollInterval = null;
        }
        return;
      }

      const data = await res.json();
      currentProject = data.project;
      if (data.settings) cachedSettings = data.settings;
      if (data.bank_accounts) cachedBankAccounts = data.bank_accounts;

      renderProject(currentProject, cachedSettings, cachedBankAccounts, isPolling);
    } catch (err) {
      console.error('Fetch error:', err);
      if (!isPolling) {
        showError('Gagal Menghubungi Server', 'Koneksi internet bermasalah. Silakan periksa jaringan Anda dan coba lagi.');
      }
    }
  }

  // Render list of bank accounts (isolated to prevent polling flickering)
  function renderBankAccounts(bankAccounts = []) {
    if (!bankAccountsList) return;
    bankAccountsList.innerHTML = '';

    if (bankAccounts && bankAccounts.length > 0) {
      bankCountBadge.textContent = `${bankAccounts.length} Pilihan Rekening / E-Wallet`;
      bankAccounts.forEach(acc => {
        const row = document.createElement('div');
        row.className = 'bank-row';
        row.innerHTML = `
          <div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="font-size: 0.8rem; font-weight: 700; color: #ffffff; background: rgba(99,102,241,0.25); border: 1px solid rgba(99,102,241,0.4); padding: 2px 10px; border-radius: 4px;">
                ${escapeHtml(acc.bank_name)}
              </span>
            </div>
            <div class="account-number" style="margin-top: 6px; font-size: 1.25rem;">${escapeHtml(acc.account_number)}</div>
            <div style="font-size: 0.82rem; color: var(--text-secondary); margin-top: 2px;">
              a.n <strong style="color: #fff;">${escapeHtml(acc.account_name)}</strong>
            </div>
          </div>
          <div>
            <button type="button" class="btn btn-outline btn-copy-acc" style="padding: 8px 14px; font-size: 0.85rem;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
              </svg>
              <span>Salin</span>
            </button>
          </div>
        `;

        const btnCopy = row.querySelector('.btn-copy-acc');
        btnCopy.addEventListener('click', () => {
          navigator.clipboard.writeText(acc.account_number).then(() => {
            const label = btnCopy.querySelector('span');
            label.textContent = 'Tersalin! ✓';
            btnCopy.style.borderColor = 'var(--success)';
            showToast(`Nomor ${acc.bank_name} (${acc.account_number}) berhasil disalin!`, 'success');
            setTimeout(() => {
              label.textContent = 'Salin';
              btnCopy.style.borderColor = '';
            }, 2500);
          }).catch(() => {
            showToast('Gagal menyalin otomatis.', 'error');
          });
        });

        bankAccountsList.appendChild(row);
      });
    } else {
      bankAccountsList.innerHTML = '<div style="color: var(--text-muted); font-size: 0.85rem; padding: 12px; text-align: center;">Belum ada rekening yang ditambahkan oleh admin.</div>';
    }
  }

  // Render Project UI
  function renderProject(project, settings, bankAccounts = [], isPolling = false) {
    loadingState.style.display = 'none';
    errorState.style.display = 'none';
    projectContent.style.display = 'block';

    const effectiveSettings = settings || cachedSettings;
    const effectiveAccounts = (bankAccounts && bankAccounts.length > 0) ? bankAccounts : cachedBankAccounts;

    // Update Brand & Title
    if (effectiveSettings && effectiveSettings.studio_name) {
      studioNameDisplay.textContent = effectiveSettings.studio_name;
      document.title = `${project.project_title} - ${effectiveSettings.studio_name}`;
    }

    projectTitle.textContent = project.project_title;
    clientName.textContent = project.client_name;

    const isFree = Boolean(project.is_free);

    // Hide billing info completely if isFree
    if (billingSection) {
      billingSection.style.display = isFree ? 'none' : 'block';
    }

    const baseAmount = project.amount || 0;
    const discountAmount = project.discount || 0;
    const finalAmount = project.final_amount ?? Math.max(0, baseAmount - discountAmount);

    if (!isFree) {
      amountDisplay.textContent = formatIDR(finalAmount);

      if (discountAmount > 0) {
        discountBox.style.display = 'flex';
        originalAmountDisplay.textContent = formatIDR(baseAmount);
        const percent = baseAmount > 0 ? Math.round((discountAmount / baseAmount) * 100) : 0;
        discountPill.textContent = `- ${formatIDR(discountAmount)} (${percent}%)`;
        if (project.discount_label && project.discount_label.trim()) {
          discountLabelText.style.display = 'block';
          discountLabelText.textContent = `🎉 Promo: ${project.discount_label.trim()}`;
        } else {
          discountLabelText.style.display = 'none';
        }
      } else {
        discountBox.style.display = 'none';
      }
    }

    if (project.notes && project.notes.trim()) {
      projectNotesBox.style.display = 'block';
      projectNotesText.textContent = project.notes;
    } else {
      projectNotesBox.style.display = 'none';
    }

    // Bank Information
    if (effectiveSettings) {
      if (effectiveSettings.bank_instructions) {
        bankInstructionText.textContent = effectiveSettings.bank_instructions;
      }
      if (effectiveSettings.qris_url) {
        qrisContainer.style.display = 'block';
        qrisModalImg.src = effectiveSettings.qris_url;
      } else {
        qrisContainer.style.display = 'none';
      }
    }

    // Only render bank accounts on initial load or if empty, avoiding flicker during polling
    if (!isPolling || bankAccountsList.children.length === 0) {
      renderBankAccounts(effectiveAccounts);
    }

    // Status UI Switching
    headerStatusBadge.className = 'status-pill';

    if (project.status === 'APPROVED') {
      headerStatusBadge.classList.add('status-approved');
      headerStatusText.textContent = isFree ? 'Siap Diunduh' : 'Terverifikasi';

      if (approvedHeroTitle) {
        approvedHeroTitle.textContent = isFree ? 'File Dokumentasi Siap Diunduh' : 'Pembayaran Terverifikasi!';
      }
      if (approvedHeroDesc) {
        approvedHeroDesc.textContent = isFree
          ? 'Semua file foto & video Anda telah selesai diproses dan siap diunduh melalui tombol Google Drive di bawah ini.'
          : 'Terima kasih banyak atas kerjasamanya. Semua file foto & video Anda telah siap dan dapat diunduh sekarang.';
      }

      approvedSection.style.display = 'block';
      pendingSection.style.display = 'none';
      rejectedSection.style.display = 'none';
      paymentFormSection.style.display = 'none';

      // Set Google Drive Button URL
      if (project.gdrive_url) {
        gdriveButton.href = project.gdrive_url;
      }

      // Trigger Confetti celebration once
      if (!hasCelebrated && typeof confetti === 'function') {
        hasCelebrated = true;
        confetti({
          particleCount: 80,
          spread: 80,
          origin: { y: 0.6 }
        });
      }

      // Stop polling once approved
      if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
      }

    } else if (project.status === 'PENDING_VERIFICATION') {
      headerStatusBadge.classList.add('status-pending');
      headerStatusText.textContent = 'Menunggu Verifikasi';

      approvedSection.style.display = 'none';
      pendingSection.style.display = 'block';
      rejectedSection.style.display = 'none';
      paymentFormSection.style.display = 'none';

      // Start auto-poll if not running
      if (!pollInterval) {
        pollInterval = setInterval(() => {
          fetchProjectData(true);
        }, 5000);
      }

    } else if (project.status === 'REJECTED') {
      headerStatusBadge.classList.add('status-rejected');
      headerStatusText.textContent = 'Bukti Ditolak';

      approvedSection.style.display = 'none';
      pendingSection.style.display = 'none';
      rejectedSection.style.display = 'block';
      paymentFormSection.style.display = 'block';

      rejectedReasonText.textContent = project.reject_reason 
        ? `Alasan Penolakan: "${project.reject_reason}"` 
        : 'Bukti transfer tidak terbaca atau nominal tidak sesuai. Silakan upload bukti yang valid.';

      if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
      }

    } else {
      // UNPAID
      headerStatusBadge.classList.add('status-unpaid');
      headerStatusText.textContent = 'Belum Bayar';

      approvedSection.style.display = 'none';
      pendingSection.style.display = 'none';
      rejectedSection.style.display = 'none';
      paymentFormSection.style.display = 'block';

      if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
      }
    }
  }

  // QRIS Modal Triggers
  if (btnShowQris && qrisModal) {
    btnShowQris.addEventListener('click', () => qrisModal.showModal());
    btnCloseQrisModal.addEventListener('click', () => qrisModal.close());
    qrisModal.addEventListener('click', (e) => {
      const rect = qrisModal.getBoundingClientRect();
      if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
        qrisModal.close();
      }
    });
  }

  // Refresh status button in pending state
  if (btnRefreshStatus) {
    btnRefreshStatus.addEventListener('click', () => {
      btnRefreshStatus.textContent = 'Memeriksa...';
      fetchProjectData(true).finally(() => {
        setTimeout(() => {
          btnRefreshStatus.textContent = '🔄 Cek Sekarang';
        }, 800);
      });
    });
  }

  // Drag and Drop support
  dropzoneBox.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzoneBox.classList.add('dragover');
  });

  ['dragleave', 'dragend'].forEach(type => {
    dropzoneBox.addEventListener(type, () => dropzoneBox.classList.remove('dragover'));
  });

  dropzoneBox.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzoneBox.classList.remove('dragover');
    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileSelected(e.dataTransfer.files[0]);
    }
  });

  // Native input change event
  proofFileInput.addEventListener('change', (e) => {
    if (e.target.files && e.target.files.length > 0) {
      handleFileSelected(e.target.files[0]);
    }
  });

  // Handle file selected
  function handleFileSelected(file) {
    if (!file) return;

    // Validate size (< 25MB)
    if (file.size > 25 * 1024 * 1024) {
      showToast('Ukuran file terlalu besar! Maksimal 25MB.', 'error');
      proofFileInput.value = '';
      return;
    }

    selectedFile = file;
    previewFileName.textContent = `File dipilih: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;

    previewContainer.style.display = 'block';
    dropzoneBox.style.display = 'none';

    if (btnSubmitProofText) {
      btnSubmitProofText.textContent = '🚀 Kirim Bukti Pembayaran';
    }
    btnSubmitProof.classList.add('btn-success');
    btnSubmitProof.classList.remove('btn-primary');

    const isImg = file.type.startsWith('image/') || /\.(jpe?g|png|webp|heic|heif|gif|bmp)$/i.test(file.name);

    if (currentObjectUrl) {
      URL.revokeObjectURL(currentObjectUrl);
      currentObjectUrl = null;
    }

    if (isImg) {
      try {
        currentObjectUrl = URL.createObjectURL(file);
        previewImg.src = currentObjectUrl;
      } catch (err) {
        previewImg.src = 'https://placehold.co/300x200/1e293b/white?text=Foto+Terpilih';
      }
    } else {
      previewImg.src = 'https://placehold.co/300x200/1e293b/white?text=Dokumen+PDF';
    }
  }

  // Remove preview button
  btnRemovePreview.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    selectedFile = null;
    proofFileInput.value = '';
    if (currentObjectUrl) {
      URL.revokeObjectURL(currentObjectUrl);
      currentObjectUrl = null;
    }
    previewContainer.style.display = 'none';
    dropzoneBox.style.display = 'block';

    if (btnSubmitProofText) {
      btnSubmitProofText.textContent = '🚀 Kirim Bukti Pembayaran';
    }
    btnSubmitProof.classList.remove('btn-success');
    btnSubmitProof.classList.add('btn-primary');
  });

  // Dedicated Submit Payment Proof with Realtime Percentage & Progress Bar
  function submitPaymentProof() {
    if (isUploading) return;

    if (!selectedFile) {
      showToast('Silakan ketuk area "Buka Galeri / Kamera HP" untuk memilih foto bukti transfer.', 'error');
      proofFileInput.click();
      return;
    }

    isUploading = true;
    btnSubmitProof.disabled = true;
    btnSubmitProof.style.pointerEvents = 'none';
    btnSubmitProof.style.opacity = '0.7';

    // Activate Realtime Progress Bar
    uploadProgressBox.style.display = 'block';
    uploadProgressBar.style.width = '0%';
    uploadProgressPercent.textContent = '0%';
    const totalMb = (selectedFile.size / 1024 / 1024).toFixed(2);
    uploadProgressBytes.textContent = `0 MB / ${totalMb} MB (0%)`;
    uploadProgressStatusText.textContent = 'Mengunggah foto bukti transfer...';
    uploadProgressSub.textContent = 'Mohon jangan tutup halaman ini...';

    // Scroll to progress box smoothly
    uploadProgressBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    const formData = new FormData();
    formData.append('proof', selectedFile);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/api/p/${token}/upload`, true);

    // Track upload progress
    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        const percent = Math.min(100, Math.round((e.loaded / e.total) * 100));
        const loadedMb = (e.loaded / 1024 / 1024).toFixed(2);
        const totalMbCalc = (e.total / 1024 / 1024).toFixed(2);

        uploadProgressBar.style.width = `${percent}%`;
        uploadProgressPercent.textContent = `${percent}%`;
        uploadProgressBytes.textContent = `${loadedMb} MB dari ${totalMbCalc} MB (${percent}%)`;

        if (percent >= 100) {
          uploadProgressStatusText.textContent = 'Memverifikasi berkas di server... ⏳';
          uploadProgressSub.textContent = 'Hampir selesai, mohon tunggu sebentar...';
        }
      }
    });

    xhr.onload = function() {
      isUploading = false;
      btnSubmitProof.disabled = false;
      btnSubmitProof.style.pointerEvents = '';
      btnSubmitProof.style.opacity = '';

      let resData = null;
      try {
        resData = JSON.parse(xhr.responseText);
      } catch (err) {
        resData = {};
      }

      if (xhr.status >= 200 && xhr.status < 300 && resData.success) {
        uploadProgressBar.style.width = '100%';
        uploadProgressPercent.textContent = '100%';
        uploadProgressStatusText.textContent = 'Bukti Berhasil Diunggah! ✅';

        showToast('Bukti pembayaran berhasil dikirim! Menunggu verifikasi admin.', 'success');

        currentProject = resData.project;
        if (resData.settings) cachedSettings = resData.settings;
        if (resData.bank_accounts) cachedBankAccounts = resData.bank_accounts;

        setTimeout(() => {
          uploadProgressBox.style.display = 'none';
          renderProject(currentProject, cachedSettings, cachedBankAccounts, false);
        }, 800);
      } else {
        uploadProgressBox.style.display = 'none';
        const errMsg = resData.error || 'Gagal mengunggah bukti pembayaran. Silakan coba lagi.';
        showToast(errMsg, 'error');
      }
    };

    xhr.onerror = function() {
      isUploading = false;
      btnSubmitProof.disabled = false;
      btnSubmitProof.style.pointerEvents = '';
      btnSubmitProof.style.opacity = '';
      uploadProgressBox.style.display = 'none';
      showToast('Koneksi internet terputus saat mengunggah. Silakan periksa jaringan dan coba lagi.', 'error');
    };

    xhr.ontimeout = function() {
      isUploading = false;
      btnSubmitProof.disabled = false;
      btnSubmitProof.style.pointerEvents = '';
      btnSubmitProof.style.opacity = '';
      uploadProgressBox.style.display = 'none';
      showToast('Waktu unggah habis (timeout). Silakan coba lagi.', 'error');
    };

    xhr.timeout = 180000; // 3 minutes timeout for large mobile uploads
    xhr.send(formData);
  }

  // Bind click and submit
  btnSubmitProof.addEventListener('click', (e) => {
    e.preventDefault();
    submitPaymentProof();
  });

  uploadForm.addEventListener('submit', (e) => {
    e.preventDefault();
    submitPaymentProof();
  });

  // Initial Load
  fetchProjectData();
});
