/**
 * IK Labs Portal — Radial Orbital Onboarding JavaScript
 * 4-Step Breakdown across the Orbital Nodes:
 * Node 1: Ganti Password
 * Node 2: Identitas Mahasiswa (NIM, Nama Depan, Nama Belakang)
 * Node 3: Kelahiran & Domisili (Tempat Lahir, Tanggal Lahir, Alamat)
 * Node 4: Akademik & Dosen Wali (Konsentrasi/Jurusan, Dosen Wali)
 */

document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const stage = document.getElementById('orbitalStage');
  const autoRotateBtn = document.getElementById('toggleAutoRotate');

  if (!stage) return;

  // State Variables
  let rotationAngle = 0;
  let autoRotate = false; // Start paused on active node 1
  let activeNodeId = 1;   // Default Node 1 (Ganti Password) active on load
  let rotationInterval = null;
  const radius = 220;

  // Read dynamic DB payload if provided by backend
  const initData = window.ONBOARDING_INITIAL_DATA || {};
  const isDosen = !!initData.is_dosen;

  // Master Daftar Dosen Wali Akademik (Dynamic from DB with fallback)
  let daftarDosen = [
    { nip: '1985010101', nama: 'Dr. Ir. Ahmad Yani, M.T.', bidang: 'Informatika' },
    { nip: '1988020502', nama: 'Prof. Siti Aminah, Ph.D.', bidang: 'Informatika' },
    { nip: '1990031203', nama: 'Hendra Kusuma, S.T., M.T.', bidang: 'Informatika' },
    { nip: '1994110804', nama: 'Rian Pratama, S.Kom., M.T.', bidang: 'Informatika' },
    { nip: '1987041505', nama: 'Dr. Budi Santoso, M.Sc.', bidang: 'Rekayasa Perangkat Lunak' },
    { nip: '1991063006', nama: 'Fajar Nugraha, S.T., M.Kom.', bidang: 'Rekayasa Perangkat Lunak' },
    { nip: '1995051907', nama: 'Dimas Aditya, S.Kom., M.Cs.', bidang: 'Rekayasa Perangkat Lunak' },
    { nip: '1992072008', nama: 'Dra. Nurul Hidayah, M.Ds.', bidang: 'Desain Komunikasi Visual' },
    { nip: '1986031409', nama: 'Dr. Raden Mas Bagus, M.Sn.', bidang: 'Desain Komunikasi Visual' },
    { nip: '1993092810', nama: 'Annisa Larasati, S.Ds., M.Ds.', bidang: 'Desain Komunikasi Visual' },
    { nip: '1990120511', nama: 'Yusuf Maulana, S.Sn., M.A.', bidang: 'Desain Komunikasi Visual' },
    { nip: '1989092312', nama: 'Ratna Dewi, S.Sn., M.Ds.', bidang: 'Desain Produk' },
    { nip: '1991021713', 'nama': 'Bambang Triyono, S.Ds., M.T.', bidang: 'Desain Produk' },
    { nip: '1994071114', 'nama': 'Eka Wahyuni, S.Ds., M.Sc.', bidang: 'Desain Produk' },
    { nip: '1993081415', 'nama': 'Maya Anggraini, S.Ds., M.A.', bidang: 'Desain Interior' },
    { nip: '1987112016', 'nama': 'Ir. Gunawan Wibisono, M.Ars.', bidang: 'Desain Interior' },
    { nip: '1992050917', 'nama': 'Citra Maharani, S.Ars., M.Ds.', bidang: 'Desain Interior' },
    { nip: '1988100418', 'nama': 'Dra. Endang Lestari, M.Sn.', bidang: 'Kriya Tekstil & Fashion' },
    { nip: '1993042219', 'nama': 'Rizky Fitriani, S.Ds., M.Ds.', bidang: 'Kriya Tekstil & Fashion' },
    { nip: '1996011520', 'nama': 'Taufik Hidayat, S.Sn., M.A.', bidang: 'Kriya Tekstil & Fashion' }
  ];

  if (Array.isArray(initData.dosen_list) && initData.dosen_list.length > 0) {
    daftarDosen = initData.dosen_list.map(d => ({
      nip: d.nip,
      nama: d.nama,
      bidang: d.jurusan || 'Fakultas Industri Kreatif'
    }));
  }

  // Timeline / Onboarding Nodes Data (4-Step Breakdown)
  const nodesData = [
    {
      id: 1,
      title: "1. Ganti Password",
      date: "Langkah 1 dari 4",
      content: "Langkah 1: Ganti password token sementara dengan password baru Anda.",
      category: "Keamanan",
      status: "in-progress",
      energy: 100,
      relatedIds: [2],
      iconSvg: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>`
    },
    {
      id: 2,
      title: isDosen ? "2. Identitas Dosen" : "2. Identitas Mahasiswa",
      date: "Langkah 2 dari 4",
      content: isDosen 
        ? "Langkah 2: Verifikasi NIP / NIDN serta Nama Depan dan Belakang."
        : "Langkah 2: Verifikasi NIM serta Nama Depan dan Belakang.",
      category: "Identitas",
      status: "pending",
      energy: 75,
      relatedIds: [1, 3],
      iconSvg: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`
    },
    {
      id: 3,
      title: "3. Kelahiran & Domisili",
      date: "Langkah 3 dari 4",
      content: "Langkah 3: Lengkapi tempat lahir, tanggal lahir, dan alamat domisili.",
      category: "Domisili",
      status: "pending",
      energy: 50,
      relatedIds: [2, 4],
      iconSvg: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`
    },
    {
      id: 4,
      title: isDosen ? "4. Program Studi" : "4. Akademik & Dosen",
      date: "Langkah 4 dari 4",
      content: isDosen
        ? "Langkah 4: Pilih Program Studi / Homebase pengajaran Anda."
        : "Langkah 4: Pilih konsentrasi peminatan dan dosen wali akademik pembimbing.",
      category: "Akademik",
      status: "pending",
      energy: 25,
      relatedIds: [3],
      iconSvg: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7"/></svg>`
    }
  ];

  // Map of persistent DOM node elements
  const nodeElementsMap = {};

  // Helper to filter lecturers matching a selected jurusan
  function getDosenByJurusan(jurusanName) {
    if (!jurusanName) return daftarDosen;
    const target = jurusanName.trim().toLowerCase();
    const matches = daftarDosen.filter(d => {
      const b = (d.bidang || '').trim().toLowerCase();
      return b === target || b.includes(target) || target.includes(b);
    });
    return matches.length > 0 ? matches : daftarDosen;
  }

  const initialJurusan = (initData.konsentrasi_list && initData.konsentrasi_list[0]) || 'Desain Komunikasi Visual';
  const initialJurusanDosen = getDosenByJurusan(initialJurusan);

  // Global collected form state across nodes (Pre-filled from logged-in account)
  const formDataState = {
    password_baru: '',
    konfirmasi_password: '',
    nim: initData.nim || (isDosen ? '19850101' : '130210091'),
    nama_depan: initData.nama_depan || (isDosen ? 'Dr. Ahmad' : 'Indah'),
    nama_belakang: initData.nama_belakang || (isDosen ? 'Yani' : 'Permatasari'),
    tempat_lahir: '',
    tanggal_lahir: '',
    alamat: '',
    konsentrasi: initialJurusan,
    dosen_wali: isDosen ? null : (initialJurusanDosen[0] ? initialJurusanDosen[0].nip : '1985010101')
  };

  // State Persistence across Page Refresh
  const STORAGE_KEY = 'onboarding_draft_' + (initData.nim || 'session');

  function saveDraftState() {
    try {
      if (typeof window === 'undefined' || !window.sessionStorage) return;
      const payload = {
        formDataState,
        activeNodeId,
        completedSteps: nodesData.filter(n => n.status === 'completed').map(n => n.id)
      };
      window.sessionStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    } catch (e) {
      console.warn('Draft save error:', e);
    }
  }

  function restoreDraftState() {
    try {
      if (typeof window === 'undefined' || !window.sessionStorage) return 1;
      const raw = window.sessionStorage.getItem(STORAGE_KEY);
      if (!raw) return 1;
      const saved = JSON.parse(raw);
      if (saved && saved.formDataState) {
        Object.assign(formDataState, saved.formDataState);
      }

      // Validate each step's data before marking it complete.
      // A step is only truly complete if its required data is present.
      function isStepDataValid(stepId) {
        switch (stepId) {
          case 1:
            // Step 1 is valid if the password was saved and restored from sessionStorage
            return !!(formDataState.password_baru && formDataState.password_baru.length >= 6);
          case 2:
            // Step 2: must have at least a first name
            return !!(formDataState.nama_depan && formDataState.nama_depan.trim().length > 0);
          case 3:
            // Step 3: must have an address or birthplace
            return !!(formDataState.alamat && formDataState.alamat.trim().length > 0) ||
                   !!(formDataState.tempat_lahir && formDataState.tempat_lahir.trim().length > 0);
          case 4:
            // Step 4: always allow (last step)
            return true;
          default:
            return false;
        }
      }

      // Only restore completed status for steps whose data is actually valid.
      // Track the furthest valid step to determine where to navigate.
      let highestValidCompletedStep = 0;
      if (Array.isArray(saved.completedSteps)) {
        saved.completedSteps.forEach(stepId => {
          if (isStepDataValid(stepId)) {
            const n = nodesData.find(x => x.id === stepId);
            if (n) {
              n.status = 'completed';
              updateBadge(n.id, 'COMPLETE', 'badge-complete');
              if (stepId > highestValidCompletedStep) {
                highestValidCompletedStep = stepId;
              }
            }
          }
        });
      }

      // Determine which step to navigate to:
      // Only go to saved activeNodeId if step 1 is genuinely completed.
      // Otherwise always start at step 1.
      if (!isStepDataValid(1)) {
        // Clear stale saved state so it doesn't persist wrongly.
        clearDraftState();
        return 1;
      }

      // Cap the active step to one beyond the highest valid completed step.
      const restoredStep = saved.activeNodeId || 1;
      const maxAllowedStep = highestValidCompletedStep + 1;
      return Math.min(restoredStep, maxAllowedStep);
    } catch (e) {
      console.warn('Draft restore error:', e);
      return 1;
    }
  }

  function clearDraftState() {
    try {
      if (typeof window !== 'undefined' && window.sessionStorage) {
        window.sessionStorage.removeItem(STORAGE_KEY);
      }
    } catch (e) {}
  }





  // =========================================================================
  // 1. POSITION MATH CALCULATIONS
  // =========================================================================
  function calculateNodePosition(index, total, currentRotation) {
    const angle = ((index / total) * 360 + currentRotation) % 360;
    const radian = (angle * Math.PI) / 180;

    const x = radius * Math.cos(radian);
    const y = radius * Math.sin(radian);

    const zIndex = Math.round(100 + 50 * Math.cos(radian));
    const opacity = Math.max(0.45, Math.min(1, 0.45 + 0.55 * ((1 + Math.sin(radian)) / 2)));

    return { x, y, angle, zIndex, opacity };
  }

  function getRelatedItems(nodeId) {
    const found = nodesData.find(n => n.id === nodeId);
    return found ? found.relatedIds : [];
  }

  // =========================================================================
  // 2. INITIALIZE PERSISTENT DOM NODES
  // =========================================================================
  function initOrbitalNodes() {
    stage.querySelectorAll('.orbital-node').forEach(el => el.remove());

    nodesData.forEach((node, index) => {
      const nodeDiv = document.createElement('div');
      nodeDiv.className = 'orbital-node';
      nodeDiv.setAttribute('data-id', node.id);

      // Halo
      const halo = document.createElement('div');
      halo.className = 'node-halo';
      halo.style.background = `radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%)`;
      const haloSize = node.energy * 0.5 + 40;
      halo.style.width = `${haloSize}px`;
      halo.style.height = `${haloSize}px`;
      halo.style.left = `-${(haloSize - 44) / 2}px`;
      halo.style.top = `-${(haloSize - 44) / 2}px`;
      nodeDiv.appendChild(halo);

      // Circle icon with step number badge
      const circle = document.createElement('div');
      circle.className = 'node-circle';
      circle.innerHTML = `${node.iconSvg}<span class="node-number-badge">${node.id}</span>`;
      nodeDiv.appendChild(circle);

      // Label text
      const label = document.createElement('div');
      label.className = 'node-label';
      label.textContent = node.title;
      nodeDiv.appendChild(label);

      // Popup Card
      const card = createCardElement(node);
      nodeDiv.appendChild(card);

      // Event listener for node click
      nodeDiv.addEventListener('click', (e) => {
        e.stopPropagation();
        selectNode(node.id);
      });

      stage.appendChild(nodeDiv);
      nodeElementsMap[node.id] = nodeDiv;
    });

    centerViewOnNode(activeNodeId);
    updateNodeStates();
  }

  // =========================================================================
  // 3. CREATE POPUP CARD CONTENT PER STEP
  // =========================================================================
  function createCardElement(node) {
    const card = document.createElement('div');
    card.className = 'orbital-card';
    card.style.display = node.id === activeNodeId ? 'block' : 'none';

    // Top connector pointer
    const pointer = document.createElement('div');
    pointer.className = 'card-top-pointer';
    card.appendChild(pointer);

    // Status badge class
    let badgeClass = 'badge-pending';
    let statusLabel = 'PENDING';
    if (node.status === 'completed') {
      badgeClass = 'badge-complete';
      statusLabel = 'COMPLETE';
    } else if (node.status === 'in-progress') {
      badgeClass = 'badge-progress';
      statusLabel = 'IN PROGRESS';
    }

    let formHtml = '';

    // ─────────────────────────────────────────────
    // NODE 1: GANTI PASSWORD
    // ─────────────────────────────────────────────
    if (node.id === 1) {
      formHtml = `
        <div class="orbital-form-pane">
          <div class="orb-input-group">
            <label class="orb-label">Password Baru <span class="text-red-400">*</span></label>
            <div class="orb-input-wrap">
              <input type="password" id="orb_password_baru" class="orb-input" placeholder="Min. 6 karakter" value="${formDataState.password_baru || ''}" autocomplete="new-password" oncopy="return false;" onpaste="return false;" oncut="return false;" ondrop="return false;" required>
              <button type="button" id="orbEyeNew" class="orb-eye-toggle" title="Lihat password">
                <svg class="eye-open-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg class="eye-slash-icon hidden" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>
          </div>

          <!-- Neon Password Strength Bar -->
          <div class="orb-strength-container" id="orbStrengthContainer">
            <div class="orb-strength-track">
              <div class="orb-strength-bar" id="orbStrengthBar"></div>
            </div>
            <div class="orb-strength-meta">
              <span class="orb-strength-label">Kekuatan Password</span>
              <span class="orb-strength-status" id="orbStrengthStatus">-</span>
            </div>
          </div>

          <div class="orb-input-group mt-1">
            <label class="orb-label">Konfirmasi Password Baru <span class="text-red-400">*</span></label>
            <div class="orb-input-wrap">
              <input type="password" id="orb_konfirmasi_password" class="orb-input" placeholder="Ulangi password baru" value="${formDataState.konfirmasi_password || ''}" autocomplete="new-password" oncopy="return false;" onpaste="return false;" oncut="return false;" ondrop="return false;" required>
              <button type="button" id="orbEyeConfirm" class="orb-eye-toggle" title="Lihat password">
                <svg class="eye-open-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg class="eye-slash-icon hidden" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>
          </div>

          <p id="orbPassError" class="text-xs text-red-400 hidden"></p>

          <div class="orb-btn-group">
            <button type="button" id="btnNextStep1" class="orb-btn-action">
              Simpan & Lanjut ke Langkah 2
            </button>
          </div>
        </div>
      `;
    }
 
    // ─────────────────────────────────────────────
    // NODE 2: IDENTITAS (NIM / NIP TERKUNCI & NAMA PRE-FILLED)
    // ─────────────────────────────────────────────
    else if (node.id === 2) {
      const idLabel = isDosen ? 'NIP / NIDN Dosen' : 'NIM Mahasiswa';
      formHtml = `
        <div class="orbital-form-pane">
          <div class="orb-input-group">
            <label class="orb-label">
              <span>${idLabel}</span>
              <span class="orb-badge-locked">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Terkunci
              </span>
            </label>
            <div class="orb-input-wrap">
              <input type="text" id="orb_nim" class="orb-input orb-input-readonly" value="${formDataState.nim}" readonly title="${idLabel} telah terdaftar dari akun Anda">
              <svg class="orb-lock-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
          </div>

          <div class="orb-input-group">
            <label class="orb-label">
              <span>Nama Depan *</span>
              <span class="orb-badge-warn" id="orbBadgeDepan">Tanpa Simbol</span>
            </label>
            <input type="text" id="orb_nama_depan" class="orb-input no-icon" placeholder="Nama depan" value="${formDataState.nama_depan}" required>
          </div>

          <div class="orb-input-group">
            <label class="orb-label">
              <span>Nama Belakang *</span>
              <span class="orb-badge-warn" id="orbBadgeBelakang">Tanpa Simbol</span>
            </label>
            <input type="text" id="orb_nama_belakang" class="orb-input no-icon" placeholder="Nama belakang" value="${formDataState.nama_belakang}" required>
          </div>

          <p id="orbStep2Error" class="text-xs text-red-400 hidden"></p>

          <div class="orb-btn-group">
            <button type="button" id="btnBackStep2" class="orb-btn-secondary">Kembali</button>
            <button type="button" id="btnNextStep2" class="orb-btn-action">
              Lanjut ke Langkah 3
            </button>
          </div>
        </div>
      `;
    }

    // ─────────────────────────────────────────────
    // NODE 3: KELAHIRAN & DOMISILI
    // ─────────────────────────────────────────────
    else if (node.id === 3) {
      formHtml = `
        <div class="orbital-form-pane">
          <!-- Tempat & Tanggal Lahir -->
          <div class="grid grid-cols-2 gap-2">
            <div class="orb-input-group">
              <label class="orb-label">Tempat Lahir *</label>
              <div class="orb-autocomplete-wrapper">
                <div class="orb-autocomplete-box">
                  <input type="text" id="orb_tempat_lahir" class="orb-input no-icon text-xs" placeholder="Kota lahir" value="${formDataState.tempat_lahir}" autocomplete="off" required>
                  <div class="orb-autocomplete-dropdown" id="tempatLahirDropdownList"></div>
                </div>
              </div>
            </div>

            <div class="orb-input-group">
              <label class="orb-label">Tanggal Lahir *</label>
              <input type="date" id="orb_tanggal_lahir" class="orb-input no-icon text-xs" value="${formDataState.tanggal_lahir}" required>
            </div>
          </div>

          <!-- Alamat Domisili Header & Mode Tabs -->
          <div class="mt-1">
            <label class="orb-label flex items-center justify-between">
              <span>Alamat Domisili *</span>
              <span class="text-[10px] text-orange-600 font-mono" id="geoStatusLabel">Autocomplete & Pin Map</span>
            </label>

            <!-- Mode Tabs -->
            <div class="orb-segmented-tabs">
              <button type="button" id="tabModeManual" class="orb-tab-btn active">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Ketik Wilayah</span>
              </button>
              <button type="button" id="tabModeMap" class="orb-tab-btn">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pin di Peta</span>
              </button>
            </div>

            <!-- PANE 1: Ketik Wilayah -->
            <div id="paneManualWilayah" class="space-y-1.5">
              <div class="grid grid-cols-2 gap-1.5">
                <!-- 1. Provinsi Autocomplete -->
                <div class="orb-autocomplete-wrapper">
                  <div class="orb-autocomplete-box">
                    <input type="text" id="orb_provinsi" class="orb-input no-icon text-xs" placeholder="Provinsi..." autocomplete="off">
                    <div class="orb-autocomplete-dropdown" id="provinsiDropdownList"></div>
                  </div>
                </div>

                <!-- 2. Kota/Kabupaten Autocomplete -->
                <div class="orb-autocomplete-wrapper">
                  <div class="orb-autocomplete-box">
                    <input type="text" id="orb_kota" class="orb-input no-icon text-xs" placeholder="Kota / Kab..." autocomplete="off">
                    <div class="orb-autocomplete-dropdown" id="kotaDropdownList"></div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-1.5">
                <!-- 3. Kecamatan Autocomplete -->
                <div class="orb-autocomplete-wrapper">
                  <div class="orb-autocomplete-box">
                    <input type="text" id="orb_kecamatan" class="orb-input no-icon text-xs" placeholder="Kecamatan..." autocomplete="off">
                    <div class="orb-autocomplete-dropdown" id="kecamatanDropdownList"></div>
                  </div>
                </div>

                <!-- 4. Kelurahan / Desa Autocomplete -->
                <div class="orb-autocomplete-wrapper">
                  <div class="orb-autocomplete-box">
                    <input type="text" id="orb_kelurahan" class="orb-input no-icon text-xs" placeholder="Kelurahan / Desa..." autocomplete="off">
                    <div class="orb-autocomplete-dropdown" id="kelurahanDropdownList"></div>
                  </div>
                </div>
              </div>

              <!-- 5. Nama Jalan (Isi Sendiri) -->
              <input type="text" id="orb_jalan" class="orb-input no-icon text-xs" placeholder="Nama Jalan / Alamat Rumah">
            </div>

            <!-- PANE 2: Pin di Peta (Leaflet OpenStreetMap + GPS) -->
            <div id="paneMapWilayah" class="hidden space-y-1.5">
              <div class="orb-map-wrap">
                <div id="domisiliMap"></div>
                <button type="button" id="btnGpsCurrentLocation" class="orb-geo-btn" title="Gunakan Lokasi GPS Saat Ini">
                  <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                  <span>Lokasi Saya</span>
                </button>
              </div>
              <div class="orb-location-badge" id="mapAddressBadge">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                <span id="mapAddressText" class="truncate text-[11px]">Geser pin pada peta untuk memilih lokasi</span>
              </div>
            </div>

            <!-- Hidden input synced for Alamat Domisili -->
            <input type="hidden" id="orb_alamat" value="${formDataState.alamat || ''}">
          </div>

          <p id="orbStep3Error" class="text-xs text-red-400 hidden"></p>

          <div class="orb-btn-group">
            <button type="button" id="btnBackStep3" class="orb-btn-secondary">Kembali</button>
            <button type="button" id="btnNextStep3" class="orb-btn-action">
              Lanjut ke Langkah 4
            </button>
          </div>
        </div>
      `;
    }
    // ─────────────────────────────────────────────
    // NODE 4: AKADEMIK & DOSEN WALI (ROLE CONDITIONAL)
    // ─────────────────────────────────────────────
    else if (node.id === 4) {
      const curJurusan = formDataState.konsentrasi || 'Desain Komunikasi Visual';
      const availableDosen = getDosenByJurusan(curJurusan);
      const selectedDosen = availableDosen.find(d => d.nip === formDataState.dosen_wali) || availableDosen[0] || daftarDosen[0];
      const initialDosenName = selectedDosen ? selectedDosen.nama : '';
      const konsentrasiLabel = isDosen ? 'Program Studi / Homebase *' : 'Konsentrasi / Program Studi *';

      // Build Dosen Wali Autocomplete block ONLY for Mahasiswa
      const dosenWaliBlock = isDosen ? '' : `
        <!-- Search Autocomplete: Dosen Wali -->
        <div class="orb-autocomplete-wrapper mt-1">
          <label class="orb-label">
            <span>Dosen Wali Akademik *</span>
            <span class="text-[10px] text-slate-500 font-mono" id="dosenCountBadge">Dosen ${curJurusan} (${availableDosen.length})</span>
          </label>
          <div class="orb-autocomplete-box" id="dosenAutocompleteBox">
            <div class="orb-input-wrap">
              <svg class="orb-input-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input type="text" id="orb_dosen_search" class="orb-input" placeholder="Cari nama atau NIP dosen..." value="${initialDosenName}" autocomplete="off">
              <button type="button" id="orbClearDosen" class="orb-clear-btn" title="Hapus / Reset">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            
            <!-- Floating Autocomplete Search Results Dropdown -->
            <div class="orb-autocomplete-dropdown" id="dosenDropdownList"></div>
          </div>
        </div>
      `;

      formHtml = `
        <div class="orbital-form-pane">
          
          <!-- Custom Select: Konsentrasi / Homebase -->
          <div class="orb-custom-select-wrapper">
            <label class="orb-label">${konsentrasiLabel}</label>
            <div class="orb-custom-select" id="select_konsentrasi">
              <button type="button" class="orb-select-trigger">
                <span class="orb-select-value">${formDataState.konsentrasi}</span>
                <svg class="orb-select-chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div class="orb-select-dropdown">
                <div class="orb-select-option ${formDataState.konsentrasi === 'Desain Komunikasi Visual' ? 'selected' : ''}" data-val="Desain Komunikasi Visual">
                  <span>Desain Komunikasi Visual</span>
                  <svg class="orb-opt-check" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="orb-select-option ${formDataState.konsentrasi === 'Informatika' ? 'selected' : ''}" data-val="Informatika">
                  <span>Informatika</span>
                  <svg class="orb-opt-check" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="orb-select-option ${formDataState.konsentrasi === 'Rekayasa Perangkat Lunak' ? 'selected' : ''}" data-val="Rekayasa Perangkat Lunak">
                  <span>Rekayasa Perangkat Lunak</span>
                  <svg class="orb-opt-check" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="orb-select-option ${formDataState.konsentrasi === 'Desain Produk' ? 'selected' : ''}" data-val="Desain Produk">
                  <span>Desain Produk</span>
                  <svg class="orb-opt-check" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="orb-select-option ${formDataState.konsentrasi === 'Desain Interior' ? 'selected' : ''}" data-val="Desain Interior">
                  <span>Desain Interior</span>
                  <svg class="orb-opt-check" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="orb-select-option ${formDataState.konsentrasi === 'Kriya Tekstil & Fashion' ? 'selected' : ''}" data-val="Kriya Tekstil & Fashion">
                  <span>Kriya Tekstil & Fashion</span>
                  <svg class="orb-opt-check" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
              </div>
            </div>
          </div>

          ${dosenWaliBlock}

          <p id="orbStep4Error" class="text-xs text-red-400 hidden mt-1"></p>

          <div class="orb-btn-group">
            <button type="button" id="btnBackStep4" class="orb-btn-secondary">Kembali</button>
            <button type="button" id="btnFinalSubmit" class="orb-btn-action">
              Simpan & Masuk Dashboard
            </button>
          </div>
        </div>
      `;
    }


    card.innerHTML = `
      <div class="card-header-flex">
        <span class="card-badge ${badgeClass}" id="badgeStatus_${node.id}">${statusLabel}</span>
        <span class="card-date">${node.date}</span>
      </div>
      <h3 class="card-title">${node.title}</h3>
      <p class="card-content-desc">${node.content}</p>

      ${formHtml}
    `;

    card.addEventListener('click', (e) => e.stopPropagation());
    bindCardEvents(card, node);

    return card;
  }

  // =========================================================================
  // 4. BIND CARD EVENTS
  // =========================================================================
  function bindCardEvents(card, node) {
    // ──────── STEP 1 ACTIONS ────────
    if (node.id === 1) {
      const passNew = card.querySelector('#orb_password_baru');
      const passConfirm = card.querySelector('#orb_konfirmasi_password');
      const eyeNew = card.querySelector('#orbEyeNew');
      const eyeConfirm = card.querySelector('#orbEyeConfirm');
      const nextBtn = card.querySelector('#btnNextStep1');
      const passErr = card.querySelector('#orbPassError');
      const strengthBar = card.querySelector('#orbStrengthBar');
      const strengthStatus = card.querySelector('#orbStrengthStatus');

      // Helper function to toggle password visibility and swap icons
      function setupEyeToggle(btn, input) {
        if (!btn || !input) return;
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isPass = input.type === 'password';
          input.type = isPass ? 'text' : 'password';

          const iconOpen = btn.querySelector('.eye-open-icon');
          const iconSlash = btn.querySelector('.eye-slash-icon');

          if (isPass) {
            // Password is now visible -> show slashed eye icon
            iconOpen?.classList.add('hidden');
            iconSlash?.classList.remove('hidden');
            btn.classList.add('active');
            btn.title = 'Sembunyikan password';
          } else {
            // Password is now hidden -> show normal open eye icon
            iconOpen?.classList.remove('hidden');
            iconSlash?.classList.add('hidden');
            btn.classList.remove('active');
            btn.title = 'Lihat password';
          }
        });
      }

      setupEyeToggle(eyeNew, passNew);
      setupEyeToggle(eyeConfirm, passConfirm);

      // Restore saved password draft if present
      if (passNew && formDataState.password_baru) {
        passNew.value = formDataState.password_baru;
        setTimeout(() => passNew.dispatchEvent(new Event('input')), 60);
      }
      if (passConfirm && formDataState.konfirmasi_password) {
        passConfirm.value = formDataState.konfirmasi_password;
      }

      // Strict Security: Block Copy, Paste, Cut & Drag-Drop on Password Fields (Silent)
      function disableCopyPaste(input) {
        if (!input) return;
        ['paste', 'copy', 'cut', 'drop'].forEach(evt => {
          input.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
          });
        });
      }

      disableCopyPaste(passNew);
      disableCopyPaste(passConfirm);

      // Live Neon Password Strength Evaluator & Real-time State Sync
      passNew?.addEventListener('input', () => {
        const val = passNew.value;
        formDataState.password_baru = val;
        saveDraftState();

        if (!val || val.length === 0) {
          strengthBar.style.width = '0%';
          strengthBar.className = 'orb-strength-bar';
          strengthStatus.textContent = '-';
          strengthStatus.className = 'orb-strength-status';
          return;
        }

        const hasLower = /[a-z]/.test(val);
        const hasUpper = /[A-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSymbol = /[^a-zA-Z0-9]/.test(val);
        const hasMinLen = val.length >= 8;

        // Calculate count of security requirements met
        const requirementsMet = [hasLower, hasUpper, hasNumber, hasSymbol].filter(Boolean).length;

        if (val.length < 6 || requirementsMet <= 1) {
          // Tier 1: Lemah -> 33%
          strengthBar.style.width = '33%';
          strengthBar.className = 'orb-strength-bar strength-weak';
          strengthStatus.textContent = 'Lemah';
          strengthStatus.className = 'orb-strength-status text-weak';
        } else if (!hasMinLen || requirementsMet < 4) {
          // Tier 2: Sedang -> 66% (Panjang < 8 atau belum lengkap kombinasi huruf besar/kecil/angka/simbol)
          strengthBar.style.width = '66%';
          strengthBar.className = 'orb-strength-bar strength-medium';
          strengthStatus.textContent = 'Sedang';
          strengthStatus.className = 'orb-strength-status text-medium';
        } else {
          // Tier 3: Sangat Kuat -> 100% FULL (Wajib: Huruf Besar + Huruf Kecil + Angka + Simbol + Min 8 Karakter)
          strengthBar.style.width = '100%';
          strengthBar.className = 'orb-strength-bar strength-strong';
          strengthStatus.textContent = 'Sangat Kuat';
          strengthStatus.className = 'orb-strength-status text-strong';
        }
      });

      passConfirm?.addEventListener('input', () => {
        formDataState.konfirmasi_password = passConfirm.value;
        saveDraftState();
      });




      nextBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!passNew.value || passNew.value.length < 6) {
          passErr.textContent = 'Password baru minimal 6 karakter!';
          passErr.classList.remove('hidden');
          return;
        }
        if (passNew.value !== passConfirm.value) {
          passErr.textContent = 'Konfirmasi password tidak cocok!';
          passErr.classList.remove('hidden');
          return;
        }
        passErr.classList.add('hidden');
        formDataState.password_baru = passNew.value;
        formDataState.konfirmasi_password = passConfirm.value;

        // Mark node 1 completed, activate node 2
        node.status = 'completed';
        updateBadge(node.id, 'COMPLETE', 'badge-complete');
        saveDraftState();

        nodesData[1].status = 'in-progress';
        nodesData[1].energy = 90;
        selectNode(2);
      });
    }

    // ──────── STEP 2 ACTIONS (IDENTITAS) ────────
    if (node.id === 2) {
      const nimInput = card.querySelector('#orb_nim');
      const namaDepanInput = card.querySelector('#orb_nama_depan');
      const namaBelakangInput = card.querySelector('#orb_nama_belakang');
      const badgeDepan = card.querySelector('#orbBadgeDepan');
      const badgeBelakang = card.querySelector('#orbBadgeBelakang');
      const nextBtn = card.querySelector('#btnNextStep2');
      const errElem = card.querySelector('#orbStep2Error');

      function setupStrictNameInput(input, badge) {
        if (!input) return;
        input.addEventListener('input', () => {
          const raw = input.value;
          // Filter out symbols and numbers: allow ONLY letters and spaces
          const clean = raw.replace(/[^a-zA-Z\s]/g, '');
          if (raw !== clean) {
            input.value = clean;
            if (badge) {
              badge.classList.add('show');
              setTimeout(() => badge.classList.remove('show'), 2500);
            }
          }
        });
      }

      setupStrictNameInput(namaDepanInput, badgeDepan);
      setupStrictNameInput(namaBelakangInput, badgeBelakang);

      // Back button to Step 1
      card.querySelector('#btnBackStep2')?.addEventListener('click', (e) => {
        e.stopPropagation();
        selectNode(1);
      });

      nextBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!nimInput.value.trim() || !namaDepanInput.value.trim() || !namaBelakangInput.value.trim()) {
          errElem.textContent = 'Mohon lengkapi NIM, Nama Depan, dan Nama Belakang tanpa simbol!';
          errElem.classList.remove('hidden');
          return;
        }
        errElem.classList.add('hidden');

        formDataState.nim = nimInput.value;
        formDataState.nama_depan = namaDepanInput.value;
        formDataState.nama_belakang = namaBelakangInput.value;

        // Mark node 2 completed, activate node 3
        node.status = 'completed';
        updateBadge(node.id, 'COMPLETE', 'badge-complete');
        saveDraftState();

        nodesData[2].status = 'in-progress';
        nodesData[2].energy = 75;
        selectNode(3);
      });
    }

    // ──────── STEP 3 ACTIONS (KELAHIRAN & DOMISILI AUTOCOMPLETE & MAP PIN) ────────
    if (node.id === 3) {
      const tempatInput = card.querySelector('#orb_tempat_lahir');
      const tempatDropdown = card.querySelector('#tempatLahirDropdownList');
      const tglInput = card.querySelector('#orb_tanggal_lahir');
      const alamatInput = card.querySelector('#orb_alamat');
      const nextBtn = card.querySelector('#btnNextStep3');
      const errElem = card.querySelector('#orbStep3Error');

      const tabManual = card.querySelector('#tabModeManual');
      const tabMap = card.querySelector('#tabModeMap');
      const paneManual = card.querySelector('#paneManualWilayah');
      const paneMap = card.querySelector('#paneMapWilayah');
      const btnGps = card.querySelector('#btnGpsCurrentLocation');
      const mapBadgeText = card.querySelector('#mapAddressText');

      const provInput = card.querySelector('#orb_provinsi');
      const provDropdown = card.querySelector('#provinsiDropdownList');
      const kotaInput = card.querySelector('#orb_kota');
      const kotaDropdown = card.querySelector('#kotaDropdownList');
      const kecInput = card.querySelector('#orb_kecamatan');
      const kecDropdown = card.querySelector('#kecamatanDropdownList');
      const kelInput = card.querySelector('#orb_kelurahan');
      const kelDropdown = card.querySelector('#kelurahanDropdownList');
      const jalanInput = card.querySelector('#orb_jalan');

      // State tracking official wilayah.id codes (Parent-Child Hierarchy)
      let selectedProvCode = null;
      let selectedRegencyCode = null;
      let selectedDistrictCode = null;

      // Memory cache keyed by parent code
      const cacheWilayah = {
        provinces: null,
        regencies: {},  // provCode -> [{ code, name }]
        districts: {},  // regencyCode -> [{ code, name }]
        villages: {}    // districtCode -> [{ code, name }]
      };

      // 1. Fetch Provinces from Wilayah.id (via PHP proxy)
      async function fetchProvinces() {
        if (cacheWilayah.provinces) return cacheWilayah.provinces;
        try {
          const res = await fetch('/onboarding/wilayah_proxy?endpoint=provinces');
          const json = await res.json();
          if (json && Array.isArray(json.data) && json.data.length > 0) {
            cacheWilayah.provinces = json.data.map(p => ({ code: p.code, name: p.name }));
            return cacheWilayah.provinces;
          }
        } catch (e) {
          console.warn('Fetch provinces error:', e);
        }
        return [];
      }

      // 2. Fetch Regencies strictly by province_code
      async function fetchRegencies(provCode) {
        if (!provCode) return [];
        if (cacheWilayah.regencies[provCode]) return cacheWilayah.regencies[provCode];
        try {
          const res = await fetch(`/onboarding/wilayah_proxy?endpoint=regencies&code=${encodeURIComponent(provCode)}`);
          const json = await res.json();
          if (json && Array.isArray(json.data) && json.data.length > 0) {
            cacheWilayah.regencies[provCode] = json.data.map(r => ({ code: r.code, name: r.name }));
            return cacheWilayah.regencies[provCode];
          }
        } catch (e) {
          console.warn(`Fetch regencies (${provCode}) error:`, e);
        }
        return [];
      }

      // 3. Fetch Districts strictly by regency_code
      async function fetchDistricts(regencyCode) {
        if (!regencyCode) return [];
        if (cacheWilayah.districts[regencyCode]) return cacheWilayah.districts[regencyCode];
        try {
          const res = await fetch(`/onboarding/wilayah_proxy?endpoint=districts&code=${encodeURIComponent(regencyCode)}`);
          const json = await res.json();
          if (json && Array.isArray(json.data) && json.data.length > 0) {
            cacheWilayah.districts[regencyCode] = json.data.map(d => ({ code: d.code, name: d.name }));
            return cacheWilayah.districts[regencyCode];
          }
        } catch (e) {
          console.warn(`Fetch districts (${regencyCode}) error:`, e);
        }
        return [];
      }

      // 4. Fetch Villages strictly by district_code
      async function fetchVillages(districtCode) {
        if (!districtCode) return [];
        if (cacheWilayah.villages[districtCode]) return cacheWilayah.villages[districtCode];
        try {
          const res = await fetch(`/onboarding/wilayah_proxy?endpoint=villages&code=${encodeURIComponent(districtCode)}`);
          const json = await res.json();
          if (json && Array.isArray(json.data) && json.data.length > 0) {
            cacheWilayah.villages[districtCode] = json.data.map(v => ({ code: v.code, name: v.name }));
            return cacheWilayah.villages[districtCode];
          }
        } catch (e) {
          console.warn(`Fetch villages (${districtCode}) error:`, e);
        }
        return [];
      }

      // Generic Strict Cascading Autocomplete
      function setupCascadingAutocomplete({
        inputElem,
        dropdownElem,
        getParentCode,
        parentWarning,
        fetchItemsFn,
        onSelect,
        onClear
      }) {
        if (!inputElem || !dropdownElem) return;

        async function renderList(query = '') {
          // Verify parent requirement
          if (getParentCode) {
            const pCode = getParentCode();
            if (!pCode) {
              dropdownElem.innerHTML = `
                <div class="p-2.5 text-xs text-amber-600 font-medium text-center flex items-center justify-center gap-1.5">
                  <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                  <span>${parentWarning}</span>
                </div>
              `;
              dropdownElem.style.display = 'block';
              return;
            }
          }

          const currentParentCode = getParentCode ? getParentCode() : null;
          dropdownElem.innerHTML = `
            <div class="p-2 text-xs text-slate-400 text-center font-medium">Memuat data wilayah...</div>
          `;
          dropdownElem.style.display = 'block';

          const items = await fetchItemsFn(currentParentCode);
          const q = (query || '').trim().toLowerCase();
          const filtered = q ? items.filter(it => it.name.toLowerCase().includes(q)) : items;

          if (!filtered || filtered.length === 0) {
            dropdownElem.innerHTML = `
              <div class="p-2 text-xs text-slate-400 text-center">Tidak ditemukan hasil "${query}"</div>
            `;
            return;
          }

          dropdownElem.innerHTML = filtered.slice(0, 30).map(item => `
            <div class="orb-auto-item" data-code="${item.code}" data-name="${item.name}">
              <span class="font-medium text-xs text-slate-800">${item.name}</span>
            </div>
          `).join('');

          dropdownElem.querySelectorAll('.orb-auto-item').forEach(el => {
            el.addEventListener('click', (e) => {
              e.stopPropagation();
              const code = el.getAttribute('data-code');
              const name = el.getAttribute('data-name');
              inputElem.value = name;
              dropdownElem.style.display = 'none';
              if (onSelect) onSelect({ code, name });
            });
          });
        }

        inputElem.addEventListener('focus', () => renderList(inputElem.value));
        inputElem.addEventListener('input', () => {
          if (!inputElem.value.trim() && onClear) {
            onClear();
          }
          renderList(inputElem.value);
        });

        document.addEventListener('click', (e) => {
          if (e && e.target && !inputElem.contains(e.target) && !dropdownElem.contains(e.target)) {
            dropdownElem.style.display = 'none';
          }
        });
      }

      // Update Combined Address String
      function updateAddressFromParts() {
        const parts = [];
        if (jalanInput?.value.trim()) parts.push(jalanInput.value.trim());
        if (kelInput?.value.trim()) {
          const val = kelInput.value.trim();
          parts.push(val.toLowerCase().startsWith('kel') || val.toLowerCase().startsWith('desa') ? val : `Kel. ${val}`);
        }
        if (kecInput?.value.trim()) {
          const val = kecInput.value.trim();
          parts.push(val.toLowerCase().startsWith('kec') ? val : `Kec. ${val}`);
        }
        if (kotaInput?.value.trim()) parts.push(kotaInput.value.trim());
        if (provInput?.value.trim()) parts.push(provInput.value.trim());

        if (parts.length > 0) {
          alamatInput.value = parts.join(', ');
        }
      }

      // 1. TEMPAT LAHIR (All Major Indonesian Cities)
      const daftarKotaUmum = [
        "Kota Bandung", "Kota Jakarta Selatan", "Kota Jakarta Pusat", "Kota Jakarta Barat", "Kota Jakarta Timur", "Kota Jakarta Utara",
        "Kota Surabaya", "Kota Medan", "Kota Semarang", "Kota Makassar", "Kota Palembang", "Kota Tangerang", "Kota Tangerang Selatan",
        "Kota Bekasi", "Kota Depok", "Kota Bogor", "Kota Cimahi", "Kota Yogyakarta", "Kota Surakarta", "Kota Malang", "Kota Denpasar",
        "Kota Padang", "Kota Pekanbaru", "Kota Bandar Lampung", "Kota Samarinda", "Kota Banjarmasin", "Kota Balikpapan", "Kota Pontianak",
        "Kota Manado", "Kota Mataram", "Kota Kupang", "Kota Ambon", "Kota Jayapura", "Kota Cirebon", "Kota Tasikmalaya", "Kota Sukabumi"
      ];
      setupCascadingAutocomplete({
        inputElem: tempatInput,
        dropdownElem: tempatDropdown,
        fetchItemsFn: async () => daftarKotaUmum.map((name, idx) => ({ code: String(idx), name })),
        onSelect: ({ name }) => {
          formDataState.tempat_lahir = name;
        }
      });

      // 2. TINGKAT 1: PROVINSI (GET provinces.json)
      setupCascadingAutocomplete({
        inputElem: provInput,
        dropdownElem: provDropdown,
        fetchItemsFn: async () => await fetchProvinces(),
        onSelect: ({ code, name }) => {
          selectedProvCode = code;
          // Cascade reset: kosongkan semua child di bawahnya
          selectedRegencyCode = null;
          selectedDistrictCode = null;
          if (kotaInput) kotaInput.value = '';
          if (kecInput) kecInput.value = '';
          if (kelInput) kelInput.value = '';
          updateAddressFromParts();
          if (kotaInput) kotaInput.focus();
        },
        onClear: () => {
          selectedProvCode = null;
          selectedRegencyCode = null;
          selectedDistrictCode = null;
          if (kotaInput) kotaInput.value = '';
          if (kecInput) kecInput.value = '';
          if (kelInput) kelInput.value = '';
          updateAddressFromParts();
        }
      });

      // 3. TINGKAT 2: KABUPATEN / KOTA (GET regencies/{province_code}.json)
      setupCascadingAutocomplete({
        inputElem: kotaInput,
        dropdownElem: kotaDropdown,
        getParentCode: () => selectedProvCode,
        parentWarning: 'Pilih Provinsi terlebih dahulu',
        fetchItemsFn: async (provCode) => await fetchRegencies(provCode),
        onSelect: ({ code, name }) => {
          selectedRegencyCode = code;
          // Cascade reset: kosongkan child kecamatan & kelurahan
          selectedDistrictCode = null;
          if (kecInput) kecInput.value = '';
          if (kelInput) kelInput.value = '';
          updateAddressFromParts();
          if (kecInput) kecInput.focus();
        },
        onClear: () => {
          selectedRegencyCode = null;
          selectedDistrictCode = null;
          if (kecInput) kecInput.value = '';
          if (kelInput) kelInput.value = '';
          updateAddressFromParts();
        }
      });

      // 4. TINGKAT 3: KECAMATAN (GET districts/{regency_code}.json)
      setupCascadingAutocomplete({
        inputElem: kecInput,
        dropdownElem: kecDropdown,
        getParentCode: () => selectedRegencyCode,
        parentWarning: 'Pilih Kota / Kabupaten terlebih dahulu',
        fetchItemsFn: async (regencyCode) => await fetchDistricts(regencyCode),
        onSelect: ({ code, name }) => {
          selectedDistrictCode = code;
          // Cascade reset: kosongkan child kelurahan
          if (kelInput) kelInput.value = '';
          updateAddressFromParts();
          if (kelInput) kelInput.focus();
        },
        onClear: () => {
          selectedDistrictCode = null;
          if (kelInput) kelInput.value = '';
          updateAddressFromParts();
        }
      });

      // 5. TINGKAT 4: KELURAHAN / DESA (GET villages/{district_code}.json)
      setupCascadingAutocomplete({
        inputElem: kelInput,
        dropdownElem: kelDropdown,
        getParentCode: () => selectedDistrictCode,
        parentWarning: 'Pilih Kecamatan terlebih dahulu',
        fetchItemsFn: async (districtCode) => await fetchVillages(districtCode),
        onSelect: ({ code, name }) => {
          updateAddressFromParts();
          if (jalanInput) jalanInput.focus();
        },
        onClear: () => {
          updateAddressFromParts();
        }
      });

      jalanInput?.addEventListener('input', updateAddressFromParts);
      kelInput?.addEventListener('input', updateAddressFromParts);
      kecInput?.addEventListener('input', updateAddressFromParts);
      kotaInput?.addEventListener('input', updateAddressFromParts);
      provInput?.addEventListener('input', updateAddressFromParts);

      // Tab Switching (Ketik Manual vs Pin di Peta)
      tabManual?.addEventListener('click', () => {
        tabManual.classList.add('active');
        tabMap.classList.remove('active');
        paneManual.classList.remove('hidden');
        paneMap.classList.add('hidden');
      });

      let leafletMap = null;
      let mapMarker = null;

      tabMap?.addEventListener('click', () => {
        tabMap.classList.add('active');
        tabManual.classList.remove('active');
        paneMap.classList.remove('hidden');
        paneManual.classList.add('hidden');

        initMapIfNeeded();
      });

      // Reverse Geocoding via Nominatim OpenStreetMap
      async function reverseGeocode(lat, lng) {
        if (mapBadgeText) mapBadgeText.textContent = 'Mencari alamat lokasi...';
        try {
          const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&zoom=18`, {
            headers: { 'Accept-Language': 'id', 'User-Agent': 'ifik-app/1.0' }
          });
          const data = await res.json();
          if (data && data.display_name) {
            const addr = data.address || {};

            // --- Correct Nominatim field mapping for Indonesia ---
            // Provinsi  → addr.state
            // Kota/Kab  → addr.city || addr.town || addr.municipality
            // Kecamatan → addr.district  (OSM uses "district" for kecamatan in ID)
            // Kelurahan → addr.suburb    (OSM uses "suburb" for kelurahan in ID)
            // Jalan     → road + neighbourhood (gang/komplek usually in neighbourhood)

            const prov      = addr.state || '';
            const kota      = addr.city || addr.town || addr.municipality || '';
            const kecamatan = addr.district || addr.city_district || '';
            const kelurahan = addr.suburb || addr.village || addr.quarter || '';

            // Build street: combine road + neighbourhood if both exist and different
            const detailRoad = addr.road || addr.pedestrian || addr.path || '';
            const gangName = addr.neighbourhood || '';
            const roadParts = [];
            if (detailRoad) roadParts.push(detailRoad);
            // Add neighbourhood only if it's different from road (avoid duplication)
            if (gangName && gangName !== detailRoad) roadParts.push(gangName);
            const jalan = roadParts.join(', ');

            // Fill Provinsi, Kota, Kecamatan, Kelurahan from map pin
            if (prov && provInput) provInput.value = prov;
            if (kota && kotaInput) kotaInput.value = kota;
            if (kecamatan && kecInput) kecInput.value = kecamatan;
            if (kelurahan && kelInput) kelInput.value = kelurahan;

            // Jalan: use what OSM gives, but placeholder hints user to complete manually.
            // OSM data for small gang/alley in Indonesia is often incomplete —
            // user should manually type parent road if it's missing.
            if (jalanInput) {
              jalanInput.value = jalan;
              jalanInput.placeholder = jalan
                ? 'Lengkapi jika perlu (mis: Jl. Curug Candung, Gang Mekar Surya 2)'
                : 'Ketik nama jalan lengkap (mis: Jl. Curug Candung, Gang Mekar Surya 2)';
              // Focus jalan field so user can easily edit
              setTimeout(() => jalanInput.focus(), 300);
            }

            // Reset cascading code state so fields show correctly
            selectedProvCode = null;
            selectedRegencyCode = null;
            selectedDistrictCode = null;

            updateAddressFromParts();

            const shortAddr = [jalan || '(Ketik nama jalan)', kelurahan, kecamatan, kota].filter(Boolean).join(', ');
            if (mapBadgeText) mapBadgeText.textContent = shortAddr || data.display_name.split(',').slice(0, 3).join(',');
          }
        } catch (err) {
          console.warn('Reverse geocode error:', err);
          if (mapBadgeText) mapBadgeText.textContent = `Lat: ${lat.toFixed(5)}, Lon: ${lng.toFixed(5)}`;
        }
      }

      function initMapIfNeeded() {
        if (typeof L === 'undefined') return;
        const mapEl = card.querySelector('#domisiliMap');
        if (!mapEl) return;

        setTimeout(() => {
          if (!leafletMap) {
            // Default center: Telkom University, Bandung
            const initialLat = -6.9740;
            const initialLng = 107.6303;

            leafletMap = L.map(mapEl, { zoomControl: false }).setView([initialLat, initialLng], 15);
            L.control.zoom({ position: 'topright' }).addTo(leafletMap);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              maxZoom: 19,
              attribution: '© OpenStreetMap'
            }).addTo(leafletMap);

            // Custom Orange Pin Marker
            const orangeIcon = L.divIcon({
              className: 'custom-map-pin',
              html: `
                <div style="
                  width: 24px;
                  height: 24px;
                  background: #ea580c;
                  border: 3px solid #ffffff;
                  border-radius: 50%;
                  box-shadow: 0 0 14px rgba(234, 88, 12, 0.7);
                  animation: pulse 1.5s infinite;
                "></div>
              `,
              iconSize: [24, 24],
              iconAnchor: [12, 12]
            });

            mapMarker = L.marker([initialLat, initialLng], {
              draggable: true,
              icon: orangeIcon
            }).addTo(leafletMap);

            mapMarker.on('dragend', () => {
              const pos = mapMarker.getLatLng();
              reverseGeocode(pos.lat, pos.lng);
            });

            leafletMap.on('click', (e) => {
              mapMarker.setLatLng(e.latlng);
              reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
          }

          leafletMap.invalidateSize();
        }, 150);
      }

      // GPS Current Location Button
      btnGps?.addEventListener('click', () => {
        if (!navigator.geolocation) {
          alert('Geolocation tidak didukung pada browser Anda.');
          return;
        }
        if (mapBadgeText) mapBadgeText.textContent = 'Mendeteksi koordinat GPS...';
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            const { latitude, longitude } = pos.coords;
            if (leafletMap && mapMarker) {
              leafletMap.setView([latitude, longitude], 16);
              mapMarker.setLatLng([latitude, longitude]);
            }
            reverseGeocode(latitude, longitude);
          },
          (err) => {
            console.error('GPS error:', err);
            if (mapBadgeText) mapBadgeText.textContent = 'Gagal mengakses GPS. Silakan klik pada peta secara manual.';
          },
          { enableHighAccuracy: true, timeout: 10000 }
        );
      });

      // Back button to Step 2
      card.querySelector('#btnBackStep3')?.addEventListener('click', (e) => {
        e.stopPropagation();
        selectNode(2);
      });

      nextBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!tempatInput.value.trim() || !tglInput.value || !alamatInput.value.trim()) {
          errElem.textContent = 'Mohon lengkapi Tempat Lahir, Tanggal Lahir, dan Alamat Domisili!';
          errElem.classList.remove('hidden');
          return;
        }
        errElem.classList.add('hidden');

        formDataState.tempat_lahir = tempatInput.value.trim();
        formDataState.tanggal_lahir = tglInput.value;
        formDataState.alamat = alamatInput.value.trim();

        // Mark node 3 completed, activate node 4
        node.status = 'completed';
        updateBadge(node.id, 'COMPLETE', 'badge-complete');
        saveDraftState();

        nodesData[3].status = 'in-progress';
        nodesData[3].energy = 90;
        selectNode(4);
      });
    }

    // ──────── STEP 4 ACTIONS (CUSTOM SELECT DROPDOWNS & FINISH) ────────
    if (node.id === 4) {
      const selectKonsentrasi = card.querySelector('#select_konsentrasi');
      const finishBtn = card.querySelector('#btnFinalSubmit');

      // Back button to Step 3
      card.querySelector('#btnBackStep4')?.addEventListener('click', (e) => {
        e.stopPropagation();
        selectNode(3);
      });

      function setupCustomSelect(selectContainer, stateKey, onSelectCallback) {
        if (!selectContainer || typeof selectContainer.querySelector !== 'function') return;
        const trigger = selectContainer.querySelector('.orb-select-trigger');
        const valueDisplay = selectContainer.querySelector('.orb-select-value');
        const options = selectContainer.querySelectorAll('.orb-select-option');

        // Toggle open/close dropdown
        trigger?.addEventListener('click', (e) => {
          e.stopPropagation();
          // Close any other open dropdowns inside card first
          card.querySelectorAll('.orb-custom-select').forEach(other => {
            if (other !== selectContainer) other.classList.remove('is-open');
          });
          selectContainer.classList.toggle('is-open');
        });

        // Click option
        options.forEach(opt => {
          opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = opt.getAttribute('data-val');
            const label = opt.getAttribute('data-label') || opt.querySelector('span')?.textContent;

            formDataState[stateKey] = val;
            if (valueDisplay) valueDisplay.textContent = label;

            options.forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');

            selectContainer.classList.remove('is-open');
            if (onSelectCallback) onSelectCallback(val, label);
          });
        });
      }

      setupCustomSelect(selectKonsentrasi, 'konsentrasi', (newJurusan) => {
        if (isDosen) return;
        const filteredDosen = getDosenByJurusan(newJurusan);
        const badge = card.querySelector('#dosenCountBadge');
        if (badge) badge.textContent = `Dosen ${newJurusan} (${filteredDosen.length})`;

        // Auto select first lecturer of newly picked department
        if (filteredDosen.length > 0) {
          formDataState.dosen_wali = filteredDosen[0].nip;
          if (searchInput) searchInput.value = filteredDosen[0].nama;
          if (clearBtn) clearBtn.style.display = 'flex';
        } else {
          formDataState.dosen_wali = '';
          if (searchInput) searchInput.value = '';
          if (clearBtn) clearBtn.style.display = 'none';
        }

        // Re-render autocomplete dropdown
        renderDosenAutocomplete('');
      });

      // ─── SEARCH AUTOCOMPLETE: DOSEN WALI (FILTERED BY JURUSAN) ───
      const searchInput = card.querySelector('#orb_dosen_search');
      const clearBtn = card.querySelector('#orbClearDosen');
      const dropdownList = card.querySelector('#dosenDropdownList');
      const step4Err = card.querySelector('#orbStep4Error');

      function renderDosenAutocomplete(query = '') {
        if (!dropdownList) return;
        const q = query.trim().toLowerCase();
        const curJurusan = formDataState.konsentrasi || '';
        const pool = getDosenByJurusan(curJurusan);

        const filtered = pool.filter(d => 
          d.nama.toLowerCase().includes(q) || 
          d.nip.includes(q)
        );

        if (filtered.length === 0) {
          dropdownList.innerHTML = `
            <div class="orb-auto-empty">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>Dosen ${curJurusan} tidak ditemukan</span>
            </div>
          `;
          return;
        }

        dropdownList.innerHTML = filtered.map(d => {
          const isSel = formDataState.dosen_wali === d.nip;
          return `
            <div class="orb-auto-item ${isSel ? 'selected' : ''}" data-nip="${d.nip}" data-nama="${d.nama}">
              <div class="orb-auto-info">
                <span class="orb-auto-name">${d.nama}</span>
                <span class="orb-auto-sub">NIP: ${d.nip} &bull; ${d.bidang}</span>
              </div>
              <svg class="orb-opt-check ${isSel ? 'active' : ''}" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
          `;
        }).join('');

        // Bind clicks on each result item
        dropdownList.querySelectorAll('.orb-auto-item').forEach(item => {
          item.addEventListener('click', (e) => {
            e.stopPropagation();
            const nip = item.getAttribute('data-nip');
            const nama = item.getAttribute('data-nama');

            formDataState.dosen_wali = nip;
            if (searchInput) searchInput.value = nama;
            if (clearBtn) clearBtn.style.display = 'flex';
            if (step4Err) step4Err.classList.add('hidden');

            dropdownList.style.display = 'none';
          });
        });
      }

      // Input Focus & Typing
      searchInput?.addEventListener('focus', (e) => {
        e.stopPropagation();
        renderDosenAutocomplete(searchInput.value);
        dropdownList.style.display = 'block';
      });

      searchInput?.addEventListener('input', (e) => {
        e.stopPropagation();
        formDataState.dosen_wali = ''; // Reset until selected
        if (clearBtn) clearBtn.style.display = searchInput.value ? 'flex' : 'none';
        renderDosenAutocomplete(searchInput.value);
        dropdownList.style.display = 'block';
      });

      // Clear button
      clearBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        searchInput.value = '';
        formDataState.dosen_wali = '';
        clearBtn.style.display = 'none';
        searchInput.focus();
        renderDosenAutocomplete('');
        dropdownList.style.display = 'block';
      });

      // Close dropdowns when clicking outside
      card.addEventListener('click', () => {
        card.querySelectorAll('.orb-custom-select').forEach(s => s.classList.remove('is-open'));
        if (dropdownList) dropdownList.style.display = 'none';
      });

      finishBtn?.addEventListener('click', async (e) => {
        e.stopPropagation();
        
        // 1. Check Step 1 (Password)
        if (!formDataState.password_baru || formDataState.password_baru.length < 6) {
          showStepWarning('Password baru wajib diisi minimal 6 karakter pada Langkah 1!');
          selectNode(1);
          return;
        }
        if (formDataState.password_baru !== formDataState.konfirmasi_password) {
          showStepWarning('Konfirmasi password tidak cocok pada Langkah 1!');
          selectNode(1);
          return;
        }

        // 2. Check Step 2 (Nama)
        if (!formDataState.nama_depan || formDataState.nama_depan.trim().length === 0) {
          showStepWarning('Nama depan wajib diisi pada Langkah 2!');
          selectNode(2);
          return;
        }

        // 3. Check Step 3 (Kelahiran & Domisili)
        if (!formDataState.tempat_lahir || !formDataState.tanggal_lahir || !formDataState.alamat) {
          showStepWarning('Data kelahiran dan alamat domisili wajib dilengkapi pada Langkah 3!');
          selectNode(3);
          return;
        }

        // 4. Check Step 4 (Akademik / Dosen Wali)
        if (!isDosen && !formDataState.dosen_wali) {
          if (step4Err) {
            step4Err.textContent = 'Mohon cari dan pilih Dosen Wali Akademik dari daftar!';
            step4Err.classList.remove('hidden');
          }
          searchInput?.focus();
          return;
        }
        if (step4Err) step4Err.classList.add('hidden');

        // Show loading state on button
        const originalBtnHtml = finishBtn.innerHTML;
        finishBtn.disabled = true;
        finishBtn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Menyimpan ke Database...
        `;

        const saveUrl = initData.save_url || `${window.location.origin}/onboarding/process_biodata`;
        const postData = new FormData();
        postData.append('is_ajax', '1');
        postData.append('password_baru', formDataState.password_baru);
        postData.append('konfirmasi_password', formDataState.konfirmasi_password);
        postData.append('nim', formDataState.nim || '');
        postData.append('nama_depan', formDataState.nama_depan);
        postData.append('nama_belakang', formDataState.nama_belakang || '');
        postData.append('tempat_lahir', formDataState.tempat_lahir);
        postData.append('tanggal_lahir', formDataState.tanggal_lahir);
        postData.append('alamat', formDataState.alamat);
        postData.append('konsentrasi', formDataState.konsentrasi || 'Informatika');
        if (!isDosen && formDataState.dosen_wali) {
          postData.append('dosen_wali', formDataState.dosen_wali);
        }

        try {
          const response = await fetch(saveUrl, {
            method: 'POST',
            body: postData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const result = await response.json();

          if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Gagal menyimpan data ke database.');
          }

          node.status = 'completed';
          updateBadge(node.id, 'COMPLETE', 'badge-complete');

          const roleTitle = isDosen ? 'Dosen' : 'Mahasiswa';
          const targetUrl = result.redirect || initData.dashboard_url || `${window.location.origin}/dashboard`;

          const dosenObj = daftarDosen.find(d => d.nip === formDataState.dosen_wali);
          const summaryDetails = {
            'Akun': `${formDataState.nama_depan} ${formDataState.nama_belakang}`,
            [isDosen ? 'NIP / NIDN' : 'NIM']: formDataState.nim,
            'Program Studi': formDataState.konsentrasi
          };

          if (!isDosen && dosenObj) {
            summaryDetails['Dosen Wali'] = dosenObj.nama;
          }

          clearDraftState();

          showSuccessModal({
            title: 'Aktivasi Akun Berhasil!',
            subtitle: `Seluruh data profil dan keamanan password ${roleTitle} telah berhasil disimpan ke database.`,
            details: summaryDetails,
            targetUrl: targetUrl
          });

        } catch (err) {
          console.error('Save error:', err);
          if (step4Err) {
            step4Err.textContent = err.message || 'Terjadi kesalahan saat menyimpan ke database.';
            step4Err.classList.remove('hidden');
          }
          finishBtn.disabled = false;
          finishBtn.innerHTML = originalBtnHtml;
        }
      });
    }

  }

  // =========================================================================
  // 4.1 CUSTOM COSMIC SUCCESS MODAL
  // =========================================================================
  function showSuccessModal({ title, subtitle, details, targetUrl }) {
    document.getElementById('orbSuccessModal')?.remove();

    const modalBackdrop = document.createElement('div');
    modalBackdrop.id = 'orbSuccessModal';
    modalBackdrop.className = 'orb-modal-backdrop';

    const detailsHtml = Object.entries(details).map(([label, val]) => `
      <div class="orb-modal-summary-row">
        <span class="orb-modal-summary-label">${label}</span>
        <span class="orb-modal-summary-val">${val}</span>
      </div>
    `).join('');

    modalBackdrop.innerHTML = `
      <div class="orb-modal-card">
        <div class="orb-modal-icon-ring">
          <svg width="34" height="34" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <h2 class="orb-modal-title">${title}</h2>
        <p class="orb-modal-desc">${subtitle}</p>

        <div class="orb-modal-summary">
          ${detailsHtml}
        </div>

        <div class="flex items-center justify-center gap-2 mt-4 text-xs font-semibold text-slate-500">
          <svg class="animate-spin w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Mengalihkan Anda ke Dashboard...</span>
        </div>

        <div class="orb-modal-progress mt-3">
          <div class="orb-modal-progress-bar" id="orbModalProgressBar"></div>
        </div>
      </div>
    `;

    document.body.appendChild(modalBackdrop);

    // Trigger entrance animation
    requestAnimationFrame(() => {
      modalBackdrop.classList.add('is-active');
      const progressBar = modalBackdrop.querySelector('#orbModalProgressBar');
      if (progressBar) {
        setTimeout(() => progressBar.style.width = '100%', 50);
      }
    });

    let redirected = false;
    const goToTarget = () => {
      if (redirected) return;
      redirected = true;
      window.location.href = targetUrl;
    };

    // Auto redirect after 2.4 seconds
    setTimeout(goToTarget, 2400);
  }

  function updateBadge(nodeId, text, className) {
    const badge = document.querySelector(`#badgeStatus_${nodeId}`);
    if (badge) {
      badge.className = `card-badge ${className}`;
      badge.textContent = text;
    }
  }

  // =========================================================================
  // 5. UPDATE NODE STATES & POSITIONS
  // =========================================================================
  function updateNodeStates() {
    const total = nodesData.length;
    const relatedList = activeNodeId ? getRelatedItems(activeNodeId) : [];

    nodesData.forEach((node, index) => {
      const pos = calculateNodePosition(index, total, rotationAngle);
      const nodeDiv = nodeElementsMap[node.id];
      if (!nodeDiv) return;

      const isActive = node.id === activeNodeId;
      const isRelated = relatedList.includes(node.id);

      nodeDiv.style.transform = `translate(${pos.x}px, ${pos.y}px)`;
      nodeDiv.style.zIndex = isActive ? 200 : pos.zIndex;
      nodeDiv.style.opacity = isActive ? 1 : pos.opacity;

      nodeDiv.classList.toggle('active', isActive);
      nodeDiv.classList.toggle('related', isRelated);

      // Dynamically switch icon to checkmark if completed
      const circle = nodeDiv.querySelector('.node-circle');
      if (circle) {
        if (node.status === 'completed') {
          circle.innerHTML = `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
          circle.classList.add('is-completed');
        } else {
          circle.innerHTML = node.iconSvg;
          circle.classList.remove('is-completed');
        }
      }

      const card = nodeDiv.querySelector('.orbital-card');
      if (card) {
        card.style.display = isActive ? 'block' : 'none';
      }

    });
  }

  function updateStepperNav(activeId) {
    const stepperNav = document.getElementById('stepperNav');
    if (!stepperNav) return;

    stepperNav.querySelectorAll('.step-nav-item').forEach(item => {
      const step = parseInt(item.getAttribute('data-step'), 10);
      const isCurrent = step === activeId;
      const node = nodesData.find(n => n.id === step);
      const isComplete = node && node.status === 'completed';

      item.classList.toggle('active', isCurrent);
      item.classList.toggle('completed', isComplete && !isCurrent);

      const numElem = item.querySelector('.step-num');
      if (numElem) {
        if (isComplete && !isCurrent) {
          numElem.innerHTML = '✓';
        } else {
          numElem.textContent = step;
        }
      }
    });
  }

  let warningToastTimer = null;
  function showStepWarning(msg) {
    let toast = document.getElementById('orbToastWarning');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'orbToastWarning';
      toast.className = 'orb-toast-warning';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <span>${msg}</span>
    `;
    toast.classList.add('show');
    if (warningToastTimer) clearTimeout(warningToastTimer);
    warningToastTimer = setTimeout(() => {
      toast.classList.remove('show');
    }, 3200);
  }

  function canNavigateToStep(targetId) {
    if (targetId <= 1) return { allowed: true };

    // Check Step 1 (Password)
    if (!formDataState.password_baru || formDataState.password_baru.length < 6) {
      return { allowed: false, step: 1, msg: 'Selesaikan ganti password (Langkah 1) terlebih dahulu!' };
    }
    if (formDataState.password_baru !== formDataState.konfirmasi_password) {
      return { allowed: false, step: 1, msg: 'Konfirmasi password pada Langkah 1 belum cocok!' };
    }

    if (targetId <= 2) return { allowed: true };

    // Check Step 2 (Identitas)
    if (!formDataState.nama_depan || formDataState.nama_depan.trim().length === 0) {
      return { allowed: false, step: 2, msg: 'Lengkapi data Identitas (Langkah 2) terlebih dahulu!' };
    }

    if (targetId <= 3) return { allowed: true };

    // Check Step 3 (Kelahiran & Domisili)
    if (!formDataState.tempat_lahir || !formDataState.tanggal_lahir || !formDataState.alamat) {
      return { allowed: false, step: 3, msg: 'Lengkapi data Kelahiran & Domisili (Langkah 3) terlebih dahulu!' };
    }

    return { allowed: true };
  }

  function centerViewOnNode(nodeId) {
    if (!nodeId) return;
    const index = nodesData.findIndex(n => n.id === nodeId);
    if (index === -1) return;
    const total = nodesData.length;
    const targetAngle = (index / total) * 360;
    rotationAngle = (270 - targetAngle + 360) % 360;
  }

  function selectNode(id) {
    if (!id) id = 1;

    // Strict navigation guard against jumping ahead without filling prior steps
    if (id > activeNodeId) {
      const navCheck = canNavigateToStep(id);
      if (!navCheck.allowed) {
        showStepWarning(navCheck.msg);
        id = navCheck.step;
      }
    }

    activeNodeId = id;
    autoRotate = false;
    centerViewOnNode(id);
    updateNodeStates();
    updateStepperNav(id);

    // Sync input values from formDataState when switching nodes
    if (id === 1 && typeof stage?.querySelector === 'function') {
      const pass1 = stage.querySelector('#orb_password_baru');
      const pass2 = stage.querySelector('#orb_konfirmasi_password');
      if (pass1 && formDataState.password_baru && pass1.value !== formDataState.password_baru) {
        pass1.value = formDataState.password_baru;
        if (typeof pass1.dispatchEvent === 'function') {
          try { pass1.dispatchEvent(new Event('input')); } catch (e) {}
        }
      }
      if (pass2 && formDataState.konfirmasi_password && pass2.value !== formDataState.konfirmasi_password) {
        pass2.value = formDataState.konfirmasi_password;
      }
    }

    saveDraftState();
  }

  function toggleNode(id) {
    selectNode(id);
  }

  // Stepper Nav bar click delegation
  document.getElementById('stepperNav')?.addEventListener('click', (e) => {
    const btn = e.target.closest('.step-nav-item');
    if (btn) {
      const step = parseInt(btn.getAttribute('data-step'), 10);
      if (step) selectNode(step);
    }
  });

  // Global helper for step selection
  window.onboardingSelectStep = selectNode;

  // Restore draft state BEFORE initializing DOM nodes so initial values are populated
  const initialStep = restoreDraftState();
  initOrbitalNodes();
  selectNode(initialStep);
});
