/**
 * =========================================================
 * KOORDINATOR TA - DASHBOARD ENGINE
 * Complete Modular JavaScript Engine for:
 * 1. Multi-Search (+ 1/4 criteria rows, quick filters, custom dropdowns)
 * 2. Table Pagination & Rendering
 * 3. Table Multi-Select (Batch Checkbox Selection)
 * 4. Floating Action Bar (Slide up when items selected)
 * 5. Batch Approval Modal with Dosen Autocomplete (Pembimbing 1 & 2)
 * 6. AJAX Batch Approval Execution
 * =========================================================
 */

(function () {
    'use strict';

    const cfg = window.DASHBOARD_CONFIG || {
        list: [],
        dosenList: [],
        ajaxBatchUrl: '',
        detailUrlPrefix: ''
    };

    const state = {
        activeTab: 'pendaftaran',
        list: cfg.list || [],
        currentPage: 1,
        pageSize: 10,
        selectedStudents: new Map(), // nim -> { nim, name, judul, stage, status }

        // Preview 2 State
        p2List: cfg.listPreview2 || [],
        p2CurrentPage: 1,
        p2PageSize: 10,
        p2SearchQuery: '',
        p2StatusFilter: '',
        p2SelectedStudents: new Map(), // nim -> { nim, name, judul, pemb1, pemb2 }
        p2QuickBatchStudents: [], // array of { nim, name, judul, pemb1_nip, pemb2_nip, pemb1_name, pemb2_name, penguji_1, penguji_2, catatan_koor, isExpanded }
        p2SelectedDosen1: null,
        p2SelectedDosen2: null,
        p2TargetNim: null,

        // Sidang TA & Ruangan State
        sidangList: cfg.listSidang || [],
        sidangCurrentPage: 1,
        sidangPageSize: 10,
        sidangSearchQuery: '',
        sidangStatusFilter: 'all',
        sidangSelectedStudents: new Map(), // nim -> student object
        ruanganList: cfg.ruanganList || []
    };

    window.switchDashboardTab = function (tabName) {
        state.activeTab = tabName;

        const btnPendaftaran = document.getElementById('tabBtnPendaftaran');
        const btnPreview2 = document.getElementById('tabBtnPreview2');
        const btnSidang = document.getElementById('tabBtnSidang');

        const contentPendaftaran = document.getElementById('tabContentPendaftaran');
        const contentPreview2 = document.getElementById('tabContentPreview2');
        const contentSidang = document.getElementById('tabContentSidang');

        const p1Bar = document.getElementById('floatingBatchBar');
        const p2Bar = document.getElementById('floatingP2BatchBar');

        // Reset all buttons to inactive
        [
            { btn: btnPendaftaran, activeColor: 'text-orange-600' },
            { btn: btnPreview2, activeColor: 'text-indigo-600' },
            { btn: btnSidang, activeColor: 'text-amber-700' }
        ].forEach(item => {
            if (item.btn) {
                item.btn.classList.remove('bg-white', item.activeColor, 'shadow-sm', 'active');
                item.btn.classList.add('text-slate-600', 'hover:bg-white/50');
            }
        });

        // Hide all contents
        if (contentPendaftaran) contentPendaftaran.classList.add('hidden');
        if (contentPreview2) contentPreview2.classList.add('hidden');
        if (contentSidang) contentSidang.classList.add('hidden');
        if (p1Bar) { p1Bar.classList.add('hidden'); p1Bar.classList.remove('block'); }
        if (p2Bar) { p2Bar.classList.add('hidden'); p2Bar.classList.remove('block'); }

        if (tabName === 'pendaftaran') {
            if (btnPendaftaran) {
                btnPendaftaran.classList.add('bg-white', 'text-orange-600', 'shadow-sm', 'active');
                btnPendaftaran.classList.remove('text-slate-600', 'hover:bg-white/50');
            }
            if (contentPendaftaran) contentPendaftaran.classList.remove('hidden');
            updateFloatingBar();
            renderTable();
        } else if (tabName === 'preview2') {
            if (btnPreview2) {
                btnPreview2.classList.add('bg-white', 'text-indigo-600', 'shadow-sm', 'active');
                btnPreview2.classList.remove('text-slate-600', 'hover:bg-white/50');
            }
            if (contentPreview2) contentPreview2.classList.remove('hidden');
            updateP2FloatingBatchBar();
            renderP2Table();

            if (cfg.ajaxPreview2RealtimeUrl) {
                fetch(cfg.ajaxPreview2RealtimeUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.data) {
                            state.p2List = data.data;
                            if (data.stats) {
                                const t = document.getElementById('statP2Total');
                                const tr = document.getElementById('statP2Terjadwal');
                                const ps = document.getElementById('statP2Penguji');
                                const bl = document.getElementById('statP2Belum');
                                if (t) t.textContent = data.stats.total;
                                if (tr) {
                                    const pct = data.stats.total > 0 ? Math.round((data.stats.terjadwal / data.stats.total) * 100) : 0;
                                    tr.innerHTML = `${data.stats.terjadwal} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                }
                                if (ps) ps.textContent = data.stats.penguji_set;
                                if (bl) {
                                    const pct = data.stats.total > 0 ? Math.round((data.stats.belum_set / data.stats.total) * 100) : 0;
                                    bl.innerHTML = `${data.stats.belum_set} <span class="text-xs font-semibold text-amber-600 font-normal">(${pct}%)</span>`;
                                }
                            }
                            renderP2Table();
                        }
                    })
                    .catch(() => {});
            }
        } else if (tabName === 'sidang') {
            if (btnSidang) {
                btnSidang.classList.add('bg-white', 'text-amber-700', 'shadow-sm', 'active');
                btnSidang.classList.remove('text-slate-600', 'hover:bg-white/50');
            }
            if (contentSidang) contentSidang.classList.remove('hidden');
            populateRuanganDropdowns();
            renderSidangTable();

            if (cfg.ajaxSidangRealtimeUrl) {
                fetch(cfg.ajaxSidangRealtimeUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.data) {
                            state.sidangList = data.data;
                            if (data.ruangan) {
                                state.ruanganList = data.ruangan;
                                populateRuanganDropdowns();
                            }
                            if (data.stats) {
                                const stTot = document.getElementById('statSidangTotal');
                                const stTr = document.getElementById('statSidangTerjadwal');
                                const stBl = document.getElementById('statSidangBelumSet');
                                const stRq = document.getElementById('statSidangRuanganCount');
                                if (stTot) stTot.textContent = data.stats.total;
                                if (stTr) stTr.textContent = data.stats.terjadwal;
                                if (stBl) stBl.textContent = data.stats.belum_set;
                                if (stRq) stRq.textContent = data.stats.ruangan_cnt;
                            }
                            renderSidangTable();
                        }
                    })
                    .catch(() => {});
            }
        }
    };

    let extraRowCounter = 0;

    // Helper: Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Helper: Animate characters for 3D kinetic buttons
    function renderAnimatedChars(text) {
        return text.split('').map((c, i) => {
            const display = c === ' ' ? '&nbsp;' : c;
            return `<span data-label="${c}" style="--i: ${i + 1}">${display}</span>`;
        }).join('');
    }

    // =========================================================
    // 1. MULTI-SEARCH CRITERIA ENGINE (EXACT IMPORT AKUN STYLE)
    // =========================================================
    function showExtraCard() {
        const extraCard = document.getElementById('extraRowsCard');
        if (extraCard) extraCard.style.display = 'block';
    }

    function hideExtraCard() {
        const extraCard = document.getElementById('extraRowsCard');
        if (extraCard) extraCard.style.display = 'none';
    }

    function isExtraCardVisible() {
        const extraCard = document.getElementById('extraRowsCard');
        return extraCard && (extraCard.style.display === 'block' || window.getComputedStyle(extraCard).display !== 'none');
    }

    window.handleUnifiedMultiSearch = function () {
        state.currentPage = 1;
        renderTable();
    };

    function updateFilterBadge() {
        const totalRows = document.querySelectorAll('.extra-filter-row').length + 1;
        const badge = document.getElementById('filterCountBadge');
        if (badge) badge.innerText = `${totalRows}/4`;
    }

    window.toggleCustomDropdown = function (type, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const menu = document.getElementById('menu-filter-' + type);
        const arrow = document.getElementById('arrow-filter-' + type);
        const isHidden = menu ? menu.classList.contains('hidden') : true;

        closeAllCustomDropdowns();

        if (menu && isHidden) {
            menu.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');

            const parentRow = menu.closest('.extra-filter-row');
            if (parentRow) parentRow.classList.add('open-dropdown');

            const parentContainer = menu.closest('.custom-dropdown-container');
            if (parentContainer) parentContainer.classList.add('open');
        }
    };

    function closeAllCustomDropdowns() {
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
        document.querySelectorAll('.extra-filter-row').forEach(r => r.classList.remove('open-dropdown'));
        document.querySelectorAll('.custom-dropdown-container').forEach(c => c.classList.remove('open'));
        if (typeof closeRuanganDropdown === 'function') {
            closeRuanganDropdown('single');
            closeRuanganDropdown('batch');
        }
    }

    window.toggleOrAddFilterRow = function (e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        closeAllCustomDropdowns();

        const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;

        if (extraRowsCount > 0 && !isExtraCardVisible()) {
            showExtraCard();
            return;
        }

        if (extraRowsCount >= 3) {
            const extraCard = document.getElementById('extraRowsCard');
            if (extraCard && extraCard.style.display === 'block') {
                hideExtraCard();
            } else {
                showExtraCard();
            }
            return;
        }

        addAdditionalFilterRow(e);
    };

    function isTextCategory(cat) {
        return cat !== 'status' && cat !== 'tahap';
    }

    function getPlaceholderForCategory(cat) {
        if (cat === 'nama') return 'Ketik nama mahasiswa lalu tekan Enter atau klik Cari...';
        if (cat === 'nim') return 'Ketik NIM lalu tekan Enter atau klik Cari...';
        if (cat === 'judul') return 'Ketik topik/judul TA lalu tekan Enter atau klik Cari...';
        if (cat === 'konsentrasi') return 'Ketik bidang/peminatan lalu tekan Enter atau klik Cari...';
        return 'Ketik kata kunci lalu tekan Enter atau klik Cari...';
    }

    function addAdditionalFilterRow(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;
        if (extraRowsCount >= 3) {
            Swal.fire({
                icon: 'info',
                title: 'Maksimal 4 Filter',
                text: 'Maksimal 4 kriteria filter pencarian yang dapat aktif secara bersamaan.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        showExtraCard();

        extraRowCounter++;
        const rowId = extraRowCounter;

        const allCriteria = ['query', 'nama', 'nim', 'judul', 'konsentrasi', 'status', 'tahap'];
        const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'query';
        const usedCriteria = [mainCat];
        document.querySelectorAll('.extra-cat-select').forEach(el => usedCriteria.push(el.value));

        const defaultCrit = allCriteria.find(c => !usedCriteria.includes(c)) || 'status';

        let defaultLabel = '⚡ Status Approval';
        if (defaultCrit === 'nama') defaultLabel = '🏷️ Nama Mahasiswa';
        else if (defaultCrit === 'nim') defaultLabel = '🆔 NIM Mahasiswa';
        else if (defaultCrit === 'judul') defaultLabel = '📖 Judul Tugas Akhir';
        else if (defaultCrit === 'konsentrasi') defaultLabel = '🎯 Bidang / Peminatan';
        else if (defaultCrit === 'tahap') defaultLabel = '🔄 Tahap Saat Ini';
        else if (defaultCrit === 'query') defaultLabel = '🔍 Kata Kunci (Semua)';

        const container = document.getElementById('additionalFilterRowsContainer');
        const rowDiv = document.createElement('div');
        rowDiv.className = 'extra-filter-row';
        rowDiv.id = `extraRow_${rowId}`;

        rowDiv.innerHTML = `
            <div class="unified-search-pill">
                <!-- Extra Category Dropdown -->
                <div class="relative custom-dropdown-container">
                    <input type="hidden" id="extraCatSelect_${rowId}" class="extra-cat-select" value="${defaultCrit}">
                    <button type="button" onclick="toggleCustomDropdown('extra-cat-${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-brand-600 focus:outline-none">
                        <span id="label-filter-extra-cat-${rowId}" class="truncate max-w-[130px]">${defaultLabel}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-cat-${rowId}"></i>
                    </button>
                    <div id="menu-filter-extra-cat-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                        <div onclick="selectExtraCategory(${rowId}, 'query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'query' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>🔍 Kata Kunci (Semua)</span></div>
                        <div onclick="selectExtraCategory(${rowId}, 'nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nama' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>🏷️ Nama Mahasiswa</span></div>
                        <div onclick="selectExtraCategory(${rowId}, 'nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nim' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>🆔 NIM Mahasiswa</span></div>
                        <div onclick="selectExtraCategory(${rowId}, 'judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'judul' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>📖 Judul Tugas Akhir</span></div>
                        <div onclick="selectExtraCategory(${rowId}, 'konsentrasi', '🎯 Bidang / Peminatan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'konsentrasi' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>🎯 Bidang / Peminatan</span></div>
                        <div onclick="selectExtraCategory(${rowId}, 'status', '⚡ Status Approval', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'status' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>⚡ Status Approval</span></div>
                        <div onclick="selectExtraCategory(${rowId}, 'tahap', '🔄 Tahap Saat Ini', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'tahap' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}"><span>🔄 Tahap Saat Ini</span></div>
                    </div>
                </div>

                <div class="unified-divider"></div>

                <!-- Input Text Value Container -->
                <div id="extraValueContainer_${rowId}" class="${isTextCategory(defaultCrit) ? 'flex-1 flex items-center' : 'hidden'}">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                    <input type="text" id="extraInput_${rowId}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handleUnifiedMultiSearch(); }" placeholder="${getPlaceholderForCategory(defaultCrit)}" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                </div>

                <!-- Custom Dropdown Value Container -->
                <div id="extraCustomSelectWrap_${rowId}" class="${!isTextCategory(defaultCrit) ? 'flex-1 relative custom-dropdown-container' : 'hidden'}">
                    <input type="hidden" id="extraValueVal_${rowId}" class="extra-val-input" value="">
                    <button type="button" onclick="toggleCustomDropdown('extra-val-${rowId}', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                        <span id="label-filter-extra-val-${rowId}" class="flex items-center gap-1.5 truncate">Semua ${defaultCrit === 'status' ? 'Status' : 'Tahap'}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-val-${rowId}"></i>
                    </button>
                    <div id="menu-filter-extra-val-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                    </div>
                </div>
            </div>

            <!-- Remove Row Button -->
            <button type="button" onclick="removeExtraRow(${rowId})" class="btn-remove-row" title="Hapus Kriteria Ini">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        `;

        container.appendChild(rowDiv);
        if (!isTextCategory(defaultCrit)) {
            updateExtraValueOptions(rowId, defaultCrit);
        }
        updateFilterBadge();
    }

    window.removeExtraRow = function (rowId) {
        const row = document.getElementById(`extraRow_${rowId}`);
        if (row) {
            row.remove();
            updateFilterBadge();
            const count = document.querySelectorAll('.extra-filter-row').length;
            if (count === 0) hideExtraCard();
            handleUnifiedMultiSearch();
        }
    };

    window.selectMainCategory = function (cat, label, el) {
        document.getElementById('mainCategorySelect').value = cat;
        document.getElementById('label-filter-main-cat').innerText = label;

        const menu = document.getElementById('menu-filter-main-cat');
        if (menu) {
            menu.querySelectorAll('.dropdown-item').forEach(i => {
                i.classList.remove('bg-orange-50', 'text-brand-600');
                i.classList.add('text-slate-700');
            });
        }
        if (el) {
            el.classList.add('bg-orange-50', 'text-brand-600');
            el.classList.remove('text-slate-700');
        }

        const textWrap = document.getElementById('mainValueContainer');
        const selectWrap = document.getElementById('mainCustomSelectWrap');
        const inputEl = document.getElementById('mainSearchInput');

        if (isTextCategory(cat)) {
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
            selectWrap.classList.add('hidden');
            if (inputEl) inputEl.placeholder = getPlaceholderForCategory(cat);
        } else {
            textWrap.classList.add('hidden');
            textWrap.classList.remove('flex-1', 'flex');
            selectWrap.classList.remove('hidden');
            updateMainValueOptions(cat);
        }

        closeAllCustomDropdowns();
    };

    function updateMainValueOptions(cat) {
        const menu = document.getElementById('menu-filter-main-select');
        const label = document.getElementById('label-filter-main-select');
        const valInput = document.getElementById('mainCustomSelectVal');
        valInput.value = '';

        let html = '';
        if (cat === 'status') {
            label.innerText = 'Semua Status';
            html = `
                <div onclick="selectMainVal('', 'Semua Status', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectMainVal('siap', 'Siap Diproses', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Siap Diproses (Koordinator TA)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('antre_wali', 'Antre Dosen Wali', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Antre Dosen Wali</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('antre_admin', 'Antre Admin Layanan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> Antre Admin Layanan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('Approved', 'Disetujui', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Disetujui (Approved)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('Rejected', 'Perlu Revisi', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Perlu Revisi (Rejected)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        } else if (cat === 'tahap') {
            label.innerText = 'Semua Tahap';
            html = `
                <div onclick="selectMainVal('', 'Semua Tahap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Tahap</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectMainVal('Dosen Wali', 'Dosen Wali', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Dosen Wali</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('Admin Layanan', 'Admin Layanan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Admin Layanan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('Koordinator TA', 'Koordinator TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Koordinator TA</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainVal('Ketua KK', 'Ketua KK', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Ketua KK</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        }
        if (menu) menu.innerHTML = html;
    }

    window.selectMainVal = function (val, labelText, el) {
        document.getElementById('mainCustomSelectVal').value = val;
        document.getElementById('label-filter-main-select').innerText = labelText;
        closeAllCustomDropdowns();
    };

    window.selectExtraCategory = function (rowId, cat, label, el) {
        document.getElementById(`extraCatSelect_${rowId}`).value = cat;
        document.getElementById(`label-filter-extra-cat-${rowId}`).innerText = label;

        const textWrap = document.getElementById(`extraValueContainer_${rowId}`);
        const selectWrap = document.getElementById(`extraCustomSelectWrap_${rowId}`);
        const inputEl = document.getElementById(`extraInput_${rowId}`);

        if (isTextCategory(cat)) {
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
            selectWrap.classList.add('hidden');
            if (inputEl) inputEl.placeholder = getPlaceholderForCategory(cat);
        } else {
            textWrap.classList.add('hidden');
            textWrap.classList.remove('flex-1', 'flex');
            selectWrap.classList.remove('hidden');
            updateExtraValueOptions(rowId, cat);
        }

        closeAllCustomDropdowns();
    };

    function updateExtraValueOptions(rowId, cat) {
        const menu = document.getElementById(`menu-filter-extra-val-${rowId}`);
        const label = document.getElementById(`label-filter-extra-val-${rowId}`);
        const valInput = document.getElementById(`extraValueVal_${rowId}`);
        valInput.value = '';

        let html = '';
        if (cat === 'status') {
            label.innerText = 'Semua Status';
            html = `
                <div onclick="selectExtraVal(${rowId}, '', 'Semua Status', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'siap', 'Siap Diproses', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Siap Diproses (Koordinator TA)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'antre_wali', 'Antre Dosen Wali', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Antre Dosen Wali</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'antre_admin', 'Antre Admin Layanan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> Antre Admin Layanan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Approved', 'Disetujui', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Disetujui (Approved)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Rejected', 'Perlu Revisi', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Perlu Revisi (Rejected)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        } else if (cat === 'tahap') {
            label.innerText = 'Semua Tahap';
            html = `
                <div onclick="selectExtraVal(${rowId}, '', 'Semua Tahap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Tahap</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Dosen Wali', 'Dosen Wali', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Dosen Wali</span></div>
                <div onclick="selectExtraVal(${rowId}, 'Admin Layanan', 'Admin Layanan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Admin Layanan</span></div>
                <div onclick="selectExtraVal(${rowId}, 'Koordinator TA', 'Koordinator TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Koordinator TA</span></div>
                <div onclick="selectExtraVal(${rowId}, 'Ketua KK', 'Ketua KK', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Ketua KK</span></div>
            `;
        }
        if (menu) menu.innerHTML = html;
    }

    window.selectExtraVal = function (rowId, val, labelText, el) {
        document.getElementById(`extraValueVal_${rowId}`).value = val;
        document.getElementById(`label-filter-extra-val-${rowId}`).innerText = labelText;
        closeAllCustomDropdowns();
    };

    window.resetImportMultiSearch = function () {
        document.getElementById('mainCategorySelect').value = 'query';
        document.getElementById('label-filter-main-cat').innerText = 'Cari Kata Kunci';
        
        const mainInput = document.getElementById('mainSearchInput');
        if (mainInput) {
            mainInput.value = '';
            mainInput.placeholder = 'Cari Nama, NIM, Judul TA, Tahap...';
        }

        const textWrap = document.getElementById('mainValueContainer');
        const selectWrap = document.getElementById('mainCustomSelectWrap');
        if (textWrap) {
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
        }
        if (selectWrap) selectWrap.classList.add('hidden');

        document.getElementById('mainCustomSelectVal').value = '';

        const container = document.getElementById('additionalFilterRowsContainer');
        if (container) container.innerHTML = '';

        hideExtraCard();
        updateFilterBadge();

        state.currentPage = 1;
        renderTable();
    };

    function getActiveFilterCriteria() {
        const criteria = [];

        const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'query';
        let mainVal = '';
        if (isTextCategory(mainCat)) {
            mainVal = document.getElementById('mainSearchInput') ? document.getElementById('mainSearchInput').value.trim() : '';
        } else {
            mainVal = document.getElementById('mainCustomSelectVal') ? document.getElementById('mainCustomSelectVal').value : '';
        }
        if (mainVal) criteria.push({ type: mainCat, val: mainVal });

        document.querySelectorAll('.extra-filter-row').forEach(row => {
            const rowId = row.id.replace('extraRow_', '');
            const cat = document.getElementById('extraCatSelect_' + rowId) ? document.getElementById('extraCatSelect_' + rowId).value : 'query';
            let val = '';
            if (isTextCategory(cat)) {
                val = document.getElementById('extraInput_' + rowId) ? document.getElementById('extraInput_' + rowId).value.trim() : '';
            } else {
                val = document.getElementById('extraValueVal_' + rowId) ? document.getElementById('extraValueVal_' + rowId).value : '';
            }
            if (val) criteria.push({ type: cat, val: val });
        });

        return criteria;
    }

    function getFilteredMahasiswa() {
        const activeFilters = getActiveFilterCriteria();

        return state.list.filter(mhs => {
            const nim = (mhs.nim || '').toLowerCase();
            const nama = ((mhs.nama_depan || '') + ' ' + (mhs.nama_belakang || '')).toLowerCase();
            const judul = (mhs.judul_1 || '').toLowerCase();
            const status = (mhs.status_approval_koor || 'Pending').toLowerCase();
            const stage = (mhs.current_stage || 'Koordinator TA').toLowerCase();
            const prodi = (mhs.konsentrasi_dkv || 'Informatika').toLowerCase();

            for (let filter of activeFilters) {
                const valLower = filter.val.toLowerCase();
                if (filter.type === 'query') {
                    const match = nim.includes(valLower) || 
                                  nama.includes(valLower) || 
                                  judul.includes(valLower) || 
                                  status.includes(valLower) || 
                                  stage.includes(valLower) || 
                                  prodi.includes(valLower);
                    if (!match) return false;
                } else if (filter.type === 'nama') {
                    if (!nama.includes(valLower)) return false;
                } else if (filter.type === 'nim') {
                    if (!nim.includes(valLower)) return false;
                } else if (filter.type === 'judul') {
                    if (!judul.includes(valLower)) return false;
                } else if (filter.type === 'konsentrasi') {
                    if (!prodi.includes(valLower)) return false;
                } else if (filter.type === 'status') {
                    const stWali = (mhs.status_approval_wali || 'Pending').toLowerCase();
                    const stAdmin = (mhs.status_approval_admin || 'Pending').toLowerCase();
                    const stKoor = (mhs.status_approval_koor || 'Pending').toLowerCase();

                    if (valLower === 'siap') {
                        if (!(stKoor === 'pending' && stWali === 'approved' && stAdmin === 'approved')) return false;
                    } else if (valLower === 'antre_wali') {
                        if (stWali === 'approved') return false;
                    } else if (valLower === 'antre_admin') {
                        if (!(stWali === 'approved' && stAdmin !== 'approved')) return false;
                    } else if (valLower === 'approved') {
                        if (stKoor !== 'approved') return false;
                    } else if (valLower === 'rejected') {
                        if (stKoor !== 'rejected') return false;
                    } else if (valLower === 'pending') {
                        if (stKoor !== 'pending') return false;
                    } else if (valLower !== '') {
                        if (stKoor !== valLower) return false;
                    }
                } else if (filter.type === 'tahap') {
                    if (!stage.includes(valLower)) return false;
                }
            }
            return true;
        });
    }

    window.changePageSize = function (size) {
        state.pageSize = parseInt(size) || 10;
        state.currentPage = 1;
        renderTable();
    };

    window.goToPage = function (page) {
        state.currentPage = page;
        renderTable();
    };

    // =========================================================
    // 2. MULTI-SELECT CHECKBOXES & FLOATING BAR
    // =========================================================
    window.toggleSelectAll = function (selectAllInput) {
        const isChecked = selectAllInput.checked;
        const pageCheckboxes = document.querySelectorAll('.row-select-checkbox:not(:disabled)');

        pageCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            const nim = cb.value;
            const name = cb.getAttribute('data-name');
            const judul = cb.getAttribute('data-judul');
            const stage = cb.getAttribute('data-stage');
            const status = cb.getAttribute('data-status');

            if (isChecked) {
                state.selectedStudents.set(nim, { nim, name, judul, stage, status });
            } else {
                state.selectedStudents.delete(nim);
            }
        });

        updateFloatingBar();
    };

    window.toggleRowSelect = function (cb) {
        const nim = cb.value;
        const name = cb.getAttribute('data-name');
        const judul = cb.getAttribute('data-judul');
        const stage = cb.getAttribute('data-stage');
        const status = cb.getAttribute('data-status');

        if (cb.checked) {
            state.selectedStudents.set(nim, { nim, name, judul, stage, status });
        } else {
            state.selectedStudents.delete(nim);
        }

        const pageCheckboxes = document.querySelectorAll('.row-select-checkbox:not(:disabled)');
        const allChecked = pageCheckboxes.length > 0 && Array.from(pageCheckboxes).every(c => c.checked);
        const selectAllEl = document.getElementById('selectAllCheckbox');
        if (selectAllEl) selectAllEl.checked = allChecked;

        updateFloatingBar();
    };

    window.clearAllSelection = function () {
        state.selectedStudents.clear();
        document.querySelectorAll('.row-select-checkbox').forEach(cb => cb.checked = false);
        const selectAllEl = document.getElementById('selectAllCheckbox');
        if (selectAllEl) selectAllEl.checked = false;
        updateFloatingBar();
    };

    function updateFloatingBar() {
        const bar = document.getElementById('floatingBatchBar');
        const countBadge = document.getElementById('selectedCountBadge');
        const previewContainer = document.getElementById('selectedStudentsPreview');
        const count = state.selectedStudents.size;

        if (!bar) return;

        if (count > 0) {
            bar.classList.remove('hidden');
            bar.classList.add('block');
            if (countBadge) countBadge.innerText = count;

            if (previewContainer) {
                let chipsHtml = '';
                let idx = 0;
                state.selectedStudents.forEach(st => {
                    if (idx < 3) {
                        chipsHtml += `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/20 text-white rounded-lg text-xs font-semibold backdrop-blur-md">
                                <i class="fa-solid fa-user-graduate text-[10px]"></i> ${escapeHtml(st.name.split(' ')[0])} (${st.nim})
                            </span>
                        `;
                    }
                    idx++;
                });

                if (count > 3) {
                    chipsHtml += `
                        <span class="inline-flex items-center px-2 py-1 bg-white/30 text-white rounded-lg text-xs font-bold">
                            +${count - 3} lainnya
                        </span>
                    `;
                }
                previewContainer.innerHTML = chipsHtml;
            }
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('block');
        }
    }

    // =========================================================
    // 3. BATCH APPROVAL MODAL (PLOT DOSEN PER MAHASISWA)
    // =========================================================
    state.quickBatchStudents = [];

    function getDosenByNip(nip) {
        return (cfg.dosenList || []).find(d => String(d.nip) === String(nip));
    }

    window.openBatchModal = function (defaultStatus = 'Approved') {
        const modal = document.getElementById('batchApprovalModal');
        if (!modal) return;

        if (state.selectedStudents.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Mahasiswa Terpilih',
                text: 'Centang checkbox mahasiswa pada tabel terlebih dahulu.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        // Initialize state.quickBatchStudents
        state.quickBatchStudents = [];
        state.selectedStudents.forEach(st => {
            state.quickBatchStudents.push({
                nim: st.nim,
                name: st.name || ('Mahasiswa ' + st.nim),
                stage: st.stage || 'Koordinator TA',
                pembimbing_1: '',
                pembimbing_2: '',
                catatan_koor: '',
                isExpanded: true
            });
        });

        renderQuickBatchCards();

        const countBadge = document.getElementById('modalSelectedCountBadge');
        if (countBadge) countBadge.innerText = `${state.quickBatchStudents.length} Mahasiswa`;

        const btnSubmitText = document.getElementById('modalBtnSubmitText');
        if (btnSubmitText) btnSubmitText.innerText = `Simpan & Lanjutkan ke Ketua KK (${state.quickBatchStudents.length} Mahasiswa)`;

        document.body.classList.add('overflow-hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
    };

    window.closeBatchModal = function () {
        const modal = document.getElementById('batchApprovalModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        }
    };

    window.removeStudentFromBatch = function (nim) {
        state.selectedStudents.delete(nim);
        const cb = document.querySelector(`.row-select-checkbox[value="${nim}"]`);
        if (cb) cb.checked = false;

        state.quickBatchStudents = state.quickBatchStudents.filter(s => s.nim !== nim);

        if (state.quickBatchStudents.length === 0) {
            closeBatchModal();
        } else {
            renderQuickBatchCards();
            const countBadge = document.getElementById('modalSelectedCountBadge');
            if (countBadge) countBadge.innerText = `${state.quickBatchStudents.length} Mahasiswa`;
            const btnSubmitText = document.getElementById('modalBtnSubmitText');
            if (btnSubmitText) btnSubmitText.innerText = `Simpan & Lanjutkan ke Ketua KK (${state.quickBatchStudents.length} Mahasiswa)`;
        }
        updateFloatingBar();
    };

    function renderQuickBatchCards() {
        const listEl = document.getElementById('modalSelectedList');
        if (!listEl) return;

        let html = '';
        state.quickBatchStudents.forEach((st, idx) => {
            const p1 = getDosenByNip(st.pembimbing_1);
            const p2 = getDosenByNip(st.pembimbing_2);
            const isComplete = (st.pembimbing_1 && st.pembimbing_2);
            const isConflict = (st.pembimbing_1 && st.pembimbing_2 && st.pembimbing_1 === st.pembimbing_2);

            html += `
                <div id="quick_card_${idx}" class="bg-white rounded-2xl border ${isConflict ? 'border-rose-400 ring-2 ring-rose-200' : (isComplete ? 'border-emerald-300 ring-1 ring-emerald-100' : 'border-slate-200')} shadow-xs transition-all relative z-10">
                    <!-- Student Header / Summary Bar -->
                    <div class="p-3.5 px-4 bg-slate-50 hover:bg-slate-100/80 rounded-t-2xl flex items-center justify-between gap-3 transition cursor-pointer" onclick="toggleQuickStudentCard(${idx})">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl ${isComplete ? 'bg-emerald-600' : 'bg-orange-500'} text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                ${idx + 1}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">${escapeHtml(st.name)}</h5>
                                    <span class="text-[10px] font-mono font-bold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                        ${st.nim}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0" onclick="event.stopPropagation()">
                            ${isConflict ? `
                                <span class="px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-[10px] font-bold">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Dosen Sama
                                </span>
                            ` : (isComplete ? `
                                <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-300 text-emerald-700 rounded-lg text-[10px] font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span class="truncate max-w-[140px]">${escapeHtml((p1?.nama_dosen || 'P1').split(' ')[0])} &amp; ${escapeHtml((p2?.nama_dosen || 'P2').split(' ')[0])}</span>
                                </span>
                            ` : `
                                <span class="px-2.5 py-1 bg-amber-50 border border-amber-300 text-amber-700 rounded-lg text-[10px] font-bold">
                                    <i class="fa-solid fa-clock mr-1"></i> Belum Lengkap
                                </span>
                            `)}

                            <button type="button" onclick="removeStudentFromBatch('${st.nim}')" class="w-7 h-7 rounded-lg bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition border border-slate-200 cursor-pointer" title="Hapus mahasiswa ini dari pilihan">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>

                            <button type="button" onclick="toggleQuickStudentCard(${idx})" class="w-7 h-7 rounded-lg bg-white hover:bg-slate-200 text-slate-500 flex items-center justify-center transition border border-slate-200 cursor-pointer">
                                <i class="fa-solid ${st.isExpanded ? 'fa-chevron-up' : 'fa-chevron-down'} text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Student Body: Dosen Pembimbing 1 & 2 Combobox -->
                    <div id="quick_card_body_${idx}" class="${st.isExpanded ? '' : 'hidden'} p-4 bg-white border-t border-slate-100 rounded-b-2xl space-y-3.5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Slot 1 -->
                            <div class="relative z-30" id="q_wrapper_${idx}_1">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                    Pembimbing 1 (Utama) <span class="text-rose-500">*</span>
                                </label>

                                <!-- Chip -->
                                <div id="q_chip_${idx}_1" class="${p1 ? '' : 'hidden'} p-2.5 bg-orange-50 border border-orange-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</div>
                                        <span id="q_chip_name_${idx}_1" class="text-xs sm:text-sm font-bold text-orange-950 truncate">${p1 ? escapeHtml(p1.nama_dosen + ' (' + p1.nip + ')') : ''}</span>
                                    </div>
                                    <button type="button" onclick="changeQuickDosen(${idx}, 1)" class="text-xs text-orange-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="q_search_container_${idx}_1" class="${p1 ? 'hidden' : ''} relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="q_search_${idx}_1" onfocus="openQuickDosenDropdown(${idx}, 1)" onclick="openQuickDosenDropdown(${idx}, 1)" oninput="filterQuickDosen(${idx}, 1)" placeholder="Cari nama / NIP pembimbing 1..." class="w-full text-xs sm:text-sm font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" autocomplete="off">
                                        <button type="button" id="q_clear_${idx}_1" onclick="clearQuickDosen(${idx}, 1)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="q_dropdown_${idx}_1" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[150] max-h-56 overflow-y-auto custom-scrollbar p-1.5 divide-y divide-slate-100"></div>
                                </div>
                            </div>

                            <!-- Slot 2 -->
                            <div class="relative z-20" id="q_wrapper_${idx}_2">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                    Pembimbing 2 (Pendamping) <span class="text-rose-500">*</span>
                                </label>

                                <!-- Chip -->
                                <div id="q_chip_${idx}_2" class="${p2 ? '' : 'hidden'} p-2.5 bg-orange-50 border border-orange-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                                        <span id="q_chip_name_${idx}_2" class="text-xs sm:text-sm font-bold text-orange-950 truncate">${p2 ? escapeHtml(p2.nama_dosen + ' (' + p2.nip + ')') : ''}</span>
                                    </div>
                                    <button type="button" onclick="changeQuickDosen(${idx}, 2)" class="text-xs text-orange-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="q_search_container_${idx}_2" class="${p2 ? 'hidden' : ''} relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="q_search_${idx}_2" onfocus="openQuickDosenDropdown(${idx}, 2)" onclick="openQuickDosenDropdown(${idx}, 2)" oninput="filterQuickDosen(${idx}, 2)" placeholder="Cari nama / NIP pembimbing 2..." class="w-full text-xs sm:text-sm font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" autocomplete="off">
                                        <button type="button" id="q_clear_${idx}_2" onclick="clearQuickDosen(${idx}, 2)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="q_dropdown_${idx}_2" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[150] max-h-56 overflow-y-auto custom-scrollbar p-1.5 divide-y divide-slate-100"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <input type="text" 
                                   value="${escapeHtml(st.catatan_koor || '')}" 
                                   oninput="state.quickBatchStudents[${idx}].catatan_koor = this.value"
                                   placeholder="Catatan khusus untuk ${escapeHtml((st.name || '').split(' ')[0])} (opsional)..." 
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-2xs font-medium">
                        </div>
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
    }

    window.toggleQuickStudentCard = function (stIdx) {
        if (!state.quickBatchStudents[stIdx]) return;
        state.quickBatchStudents[stIdx].isExpanded = !state.quickBatchStudents[stIdx].isExpanded;
        renderQuickBatchCards();
    };

    window.openQuickDosenDropdown = function (stIdx, slotNum) {
        // Close all other q_dropdowns
        document.querySelectorAll('[id^="q_dropdown_"]').forEach(d => {
            if (d.id !== `q_dropdown_${stIdx}_${slotNum}`) d.classList.add('hidden');
        });

        // Boost z-index of current card and wrapper
        document.querySelectorAll('[id^="quick_card_"]').forEach(c => c.style.zIndex = '10');
        const currentCard = document.getElementById(`quick_card_${stIdx}`);
        if (currentCard) currentCard.style.zIndex = '90';

        const wrapper1 = document.getElementById(`q_wrapper_${stIdx}_1`);
        const wrapper2 = document.getElementById(`q_wrapper_${stIdx}_2`);
        if (wrapper1) wrapper1.style.zIndex = (slotNum === 1) ? '100' : '20';
        if (wrapper2) wrapper2.style.zIndex = (slotNum === 2) ? '100' : '20';

        const drop = document.getElementById(`q_dropdown_${stIdx}_${slotNum}`);
        if (!drop) return;
        renderQuickDosenDropdown(stIdx, slotNum, '');
        drop.classList.remove('hidden');
    };

    window.filterQuickDosen = function (stIdx, slotNum) {
        const input = document.getElementById(`q_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`q_clear_${stIdx}_${slotNum}`);
        const kw = input ? input.value : '';
        if (clearBtn) {
            if (kw) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }
        renderQuickDosenDropdown(stIdx, slotNum, kw);
    };

    window.clearQuickDosen = function (stIdx, slotNum) {
        const input = document.getElementById(`q_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`q_clear_${stIdx}_${slotNum}`);
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        renderQuickDosenDropdown(stIdx, slotNum, '');
    };

    function renderQuickDosenDropdown(stIdx, slotNum, kw) {
        const drop = document.getElementById(`q_dropdown_${stIdx}_${slotNum}`);
        if (!drop) return;

        const st = state.quickBatchStudents[stIdx];
        const otherNip = slotNum === 1 ? st?.pembimbing_2 : st?.pembimbing_1;

        const filtered = (cfg.dosenList || []).filter(d => {
            if (!kw) return true;
            const q = kw.toLowerCase();
            return (d.nama_dosen || '').toLowerCase().includes(q) || (d.nip || '').toLowerCase().includes(q);
        });

        if (filtered.length === 0) {
            drop.innerHTML = `<div class="p-2.5 text-center text-slate-400 text-xs">Tidak ada dosen ditemukan.</div>`;
            return;
        }

        drop.innerHTML = filtered.map(d => {
            const isSelectedHere = (slotNum === 1 ? st?.pembimbing_1 : st?.pembimbing_2) === d.nip;
            const isSelectedOther = (d.nip === otherNip);

            return `
                <div onclick="${isSelectedOther ? '' : `selectQuickDosen(${stIdx}, ${slotNum}, '${d.nip}')`}" 
                     class="p-2 rounded-xl transition flex items-center justify-between text-xs ${isSelectedOther ? 'opacity-40 cursor-not-allowed bg-slate-50' : 'hover:bg-orange-50 cursor-pointer'} ${isSelectedHere ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-800'}">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-5 h-5 rounded-md bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-[9px] shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="truncate">
                            <p class="font-bold truncate text-xs">${escapeHtml(d.nama_dosen)}</p>
                            <p class="text-[10px] text-slate-400 font-mono">NIP: ${escapeHtml(d.nip)}</p>
                        </div>
                    </div>
                    ${isSelectedOther ? '<span class="text-[9px] text-rose-500 font-bold ml-1.5 shrink-0">Dipilih</span>' : (isSelectedHere ? '<i class="fa-solid fa-check text-orange-600 text-xs shrink-0"></i>' : '')}
                </div>
            `;
        }).join('');
    }

    window.selectQuickDosen = function (stIdx, slotNum, nip) {
        if (!state.quickBatchStudents[stIdx]) return;

        if (slotNum === 1) {
            state.quickBatchStudents[stIdx].pembimbing_1 = nip;
        } else {
            state.quickBatchStudents[stIdx].pembimbing_2 = nip;
        }

        renderQuickBatchCards();
    };

    window.changeQuickDosen = function (stIdx, slotNum) {
        if (slotNum === 1) {
            state.quickBatchStudents[stIdx].pembimbing_1 = '';
        } else {
            state.quickBatchStudents[stIdx].pembimbing_2 = '';
        }
        renderQuickBatchCards();
        setTimeout(() => {
            openQuickDosenDropdown(stIdx, slotNum);
            const input = document.getElementById(`q_search_${stIdx}_${slotNum}`);
            if (input) input.focus();
        }, 50);
    };

    window.applyQuickFirstToAll = function () {
        if (!state.quickBatchStudents || state.quickBatchStudents.length === 0) return;
        const first = state.quickBatchStudents[0];
        if (!first.pembimbing_1 && !first.pembimbing_2) {
            Swal.fire({
                icon: 'warning',
                title: 'Pembimbing Belum Dipilih',
                text: `Silakan pilih Dosen Pembimbing pada Mahasiswa #1 (${first.name || 'urutan pertama'}) terlebih dahulu sebelum menyalin ke mahasiswa lainnya.`,
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        const hasP1 = Boolean(first.pembimbing_1);
        const hasP2 = Boolean(first.pembimbing_2);

        state.quickBatchStudents.forEach((st, idx) => {
            if (idx > 0) {
                if (hasP1) st.pembimbing_1 = first.pembimbing_1;
                if (hasP2) st.pembimbing_2 = first.pembimbing_2;
            }
        });

        renderQuickBatchCards();

        let toastMsg = `Pembimbing dari Mahasiswa #1 disalin ke semua ${state.quickBatchStudents.length} mahasiswa`;
        if (hasP1 && hasP2) {
            toastMsg = `Pembimbing 1 & 2 dari Mahasiswa #1 disalin ke semua ${state.quickBatchStudents.length} mahasiswa`;
        } else if (hasP1) {
            toastMsg = `Pembimbing 1 dari Mahasiswa #1 disalin ke semua ${state.quickBatchStudents.length} mahasiswa`;
        } else if (hasP2) {
            toastMsg = `Pembimbing 2 dari Mahasiswa #1 disalin ke semua ${state.quickBatchStudents.length} mahasiswa`;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: toastMsg,
            showConfirmButton: false,
            timer: 2500
        });
    };

    window.submitBatchApproval = function (e) {
        e.preventDefault();
        const students = state.quickBatchStudents;

        if (!students || students.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Mahasiswa',
                text: 'Silakan pilih setidaknya satu mahasiswa.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        // Validate each student has both Pembimbing 1 & 2, and distinct
        for (let i = 0; i < students.length; i++) {
            const st = students[i];
            if (!st.pembimbing_1 || !st.pembimbing_2) {
                st.isExpanded = true;
                renderQuickBatchCards();

                Swal.fire({
                    icon: 'warning',
                    title: `Pembimbing Belum Lengkap (${st.name})`,
                    text: `Mahasiswa #${i + 1} (${st.name}) wajib memiliki Dosen Pembimbing 1 dan Pembimbing 2!`,
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
            if (st.pembimbing_1 === st.pembimbing_2) {
                st.isExpanded = true;
                renderQuickBatchCards();

                Swal.fire({
                    icon: 'error',
                    title: `Dosen Pembimbing Sama (${st.name})`,
                    text: `Pembimbing 1 dan 2 pada mahasiswa #${i + 1} (${st.name}) tidak boleh dosen yang sama!`,
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
        }

        const globalCatatan = (document.getElementById('modalGlobalCatatanKoor')?.value || '').trim();

        Swal.fire({
            title: `Simpan & Setujui ${students.length} Mahasiswa?`,
            text: `Dosen pembimbing untuk ${students.length} mahasiswa akan disimpan dan pendaftaran dilanjutkan ke Ketua KK.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Simpan & Setujui Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Batch Approval...',
                    text: `Sedang menyimpan penetapan pembimbing ${students.length} mahasiswa...`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const nims = students.map(s => s.nim);
                const plottings = students.map(s => ({
                    nim: s.nim,
                    pembimbing_1: s.pembimbing_1,
                    pembimbing_2: s.pembimbing_2,
                    catatan_koor: s.catatan_koor || globalCatatan || ''
                }));

                const formData = new FormData();
                formData.append('nims', JSON.stringify(nims));
                formData.append('status', 'Approved');
                formData.append('plottings', JSON.stringify(plottings));
                formData.append('catatan_koor', globalCatatan);

                fetch(cfg.ajaxBatchUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Plotting Dosen Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#ea580c'
                        });

                        closeBatchModal();
                        clearAllSelection();

                        if (cfg.ajaxRealtimeUrl) {
                            fetch(cfg.ajaxRealtimeUrl)
                                .then(r => r.json())
                                .then(res => {
                                    if (res && res.data) {
                                        state.list = res.data;
                                        if (res.stats) {
                                            const totalEl = document.getElementById('statTotalCount');
                                            const pendEl = document.getElementById('statPendingCount');
                                            const appEl = document.getElementById('statApprovedCount');
                                            const rejEl = document.getElementById('statRejectedCount');

                                            if (totalEl) totalEl.textContent = res.stats.total;
                                            if (pendEl) {
                                                const pct = res.stats.total > 0 ? Math.round((res.stats.pending / res.stats.total) * 100) : 0;
                                                pendEl.innerHTML = `${res.stats.pending} <span class="text-xs font-semibold text-cyan-600 font-normal">(${pct}%)</span>`;
                                            }
                                            if (appEl) {
                                                const pct = res.stats.total > 0 ? Math.round((res.stats.approved / res.stats.total) * 100) : 0;
                                                appEl.innerHTML = `${res.stats.approved} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                            }
                                            if (rejEl) rejEl.textContent = res.stats.rejected;
                                        }
                                        renderTable();
                                    }
                                })
                                .catch(e => {
                                    console.debug('Realtime fetch after approval error:', e);
                                    renderTable();
                                });
                        } else {
                            renderTable();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memproses',
                            text: data.message || 'Terjadi kesalahan sistem.',
                            confirmButtonColor: '#ea580c'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Jaringan',
                        text: 'Gagal terhubung ke server.',
                        confirmButtonColor: '#ea580c'
                    });
                });
            }
        });
    };

    // =========================================================
    // 3B. MULTI-DETAIL BATCH REVIEW ENGINE (CEK DOKUMEN MASSAL)
    // =========================================================
    window.p1BatchStudents = [];

    window.openP1BatchReviewModal = function () {
        const nims = Array.from(state.selectedStudents.keys());
        if (nims.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Mahasiswa',
                text: 'Silakan pilih setidaknya satu mahasiswa melalui checklist tabel.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        const modal = document.getElementById('p1BatchReviewModal');
        const modalBody = document.getElementById('p1BatchModalBody');
        const counterBadge = document.getElementById('p1ModalStudentCounter');
        const navContainer = document.getElementById('p1ModalStudentTabs');

        if (modal) {
            document.body.classList.add('overflow-hidden');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.style.display = 'flex';
        }

        if (counterBadge) counterBadge.textContent = `${nims.length} Mahasiswa Terpilih`;
        if (navContainer) navContainer.innerHTML = '';
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="py-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500 mb-3 block"></i>
                    Memuat data dokumen &amp; profil ${nims.length} mahasiswa terpilih...
                </div>
            `;
        }

        const formData = new FormData();
        formData.append('nims', JSON.stringify(nims));

        fetch(cfg.ajaxBatchDetailsUrl, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status && Array.isArray(res.data)) {
                window.p1BatchStudents = res.data.map(st => {
                    return {
                        ...st,
                        pembimbing_1: st.pembimbing_1 || '',
                        pembimbing_2: st.pembimbing_2 || '',
                        catatan_koor: st.catatan_koor || ''
                    };
                });
                renderP1AllBatchStudentsContent();
            } else {
                if (modalBody) {
                    modalBody.innerHTML = `
                        <div class="p-8 text-center bg-rose-50 rounded-2xl border border-rose-200 text-rose-700 text-xs">
                            <i class="fa-solid fa-circle-exclamation text-2xl mb-2 block"></i>
                            Gagal memuat data dokumen mahasiswa: ${res.message || 'Terjadi kesalahan sistem.'}
                        </div>
                    `;
                }
            }
        })
        .catch(err => {
            console.error('Fetch batch details error:', err);
            if (modalBody) {
                modalBody.innerHTML = `
                    <div class="p-8 text-center bg-rose-50 rounded-2xl border border-rose-200 text-rose-700 text-xs">
                        <i class="fa-solid fa-triangle-exclamation text-2xl mb-2 block"></i>
                        Gagal terhubung ke server saat memuat dokumen mahasiswa.
                    </div>
                `;
            }
        });
    };

    window.closeP1BatchReviewModal = function () {
        const modal = document.getElementById('p1BatchReviewModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        }
    };

    function renderP1AllBatchStudentsContent() {
        const modalBody = document.getElementById('p1BatchModalBody');
        const counterBadge = document.getElementById('p1ModalStudentCounter');
        const navContainer = document.getElementById('p1ModalStudentTabs');
        const students = window.p1BatchStudents;

        if (!modalBody || !students) return;

        if (counterBadge) {
            counterBadge.textContent = `${students.length} Mahasiswa Terpilih`;
        }

        // Quick Jump Nav Chips
        if (navContainer) {
            let navHtml = '';
            students.forEach((st, idx) => {
                navHtml += `
                    <a href="#p1_student_block_${idx}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-orange-600 border border-slate-700/80 hover:border-orange-500 text-slate-200 hover:text-white transition-all shadow-xs whitespace-nowrap flex items-center gap-2 shrink-0 active:scale-95">
                        <span class="w-5 h-5 rounded-lg bg-orange-500 text-white flex items-center justify-center text-[10px] font-black shadow-xs">${idx + 1}</span>
                        <span>${escapeHtml((st.nama || '').split(' ')[0])}</span>
                    </a>
                `;
            });
            navContainer.innerHTML = navHtml;
            initP1TabsDragAndWheel();
        }

    window.scrollP1StudentTabs = function (direction) {
        const container = document.getElementById('p1ModalStudentTabs');
        if (!container) return;
        const scrollAmount = 260;
        if (direction === 'left') {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    };

    function initP1TabsDragAndWheel() {
        const container = document.getElementById('p1ModalStudentTabs');
        if (!container || container.dataset.initEvents) return;
        container.dataset.initEvents = 'true';

        // Mouse Wheel Horizontal Scroll
        container.addEventListener('wheel', (e) => {
            if (e.deltaY !== 0) {
                e.preventDefault();
                container.scrollLeft += e.deltaY * 0.8;
            }
        }, { passive: false });

        // Mouse Drag to Scroll
        let isDown = false;
        let startX;
        let scrollLeftPos;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            container.classList.add('cursor-grabbing');
            container.classList.remove('cursor-grab');
            startX = e.pageX - container.offsetLeft;
            scrollLeftPos = container.scrollLeft;
        });

        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('cursor-grabbing');
            container.classList.add('cursor-grab');
        });

        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('cursor-grabbing');
            container.classList.add('cursor-grab');
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 1.5;
            container.scrollLeft = scrollLeftPos - walk;
        });
    }

        let allHtml = '';

        students.forEach((st, stIdx) => {
            const p1Obj = getDosenByNip(st.pembimbing_1);
            const p2Obj = getDosenByNip(st.pembimbing_2);
            const isConflict = (st.pembimbing_1 && st.pembimbing_2 && st.pembimbing_1 === st.pembimbing_2);

            const docKeys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
            let docsHtml = '';

            docKeys.forEach(k => {
                const f = (st.files && st.files[k]) ? st.files[k] : { title: k.toUpperCase(), name: `${k}_${st.nim}.pdf`, url: '#', status: 'Valid' };
                docsHtml += `
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs flex flex-col justify-between space-y-2.5">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-slate-800 text-xs truncate" title="${escapeHtml(f.title)}">${escapeHtml(f.title)}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-check text-[9px] mr-0.5"></i> Valid
                                </span>
                            </div>
                            <p class="text-[10px] font-mono text-slate-400 truncate mb-2" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</p>

                            <!-- Live Embedded PDF View Frame -->
                            <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100 mb-1.5">
                                <div class="p-1.5 px-3 bg-slate-800 text-white flex items-center justify-between text-[10px]">
                                    <span class="font-mono text-slate-300 truncate max-w-[170px]"><i class="fa-solid fa-file-pdf text-rose-400 mr-1"></i> ${escapeHtml(f.name)}</span>
                                    <button type="button" onclick="openP1PdfModal('${f.url}', '${escapeHtml(f.title)} - ${escapeHtml(st.nama)}')" class="text-orange-300 hover:text-white font-bold flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-expand text-[9px]"></i> Layar Penuh
                                    </button>
                                </div>
                                <iframe src="${f.url}#view=FitH&zoom=100&toolbar=1" class="w-full h-[320px] border-none bg-slate-100" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                `;
            });

            allHtml += `
                <!-- Student Block ${stIdx + 1} -->
                <div id="p1_student_block_${stIdx}" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-300 shadow-md space-y-5 scroll-mt-6">
                    
                    <!-- 1. Header Mahasiswa Info Card -->
                    <div class="bg-slate-900 text-white rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-orange-500 text-white font-black text-base flex items-center justify-center shadow-md shrink-0">
                                ${stIdx + 1}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-extrabold text-white text-base tracking-tight">${escapeHtml(st.nama)}</h4>
                                    <span class="px-2.5 py-0.5 rounded-full bg-orange-600/60 border border-orange-400/40 text-[10px] font-mono font-bold text-orange-200">
                                        NIM: ${escapeHtml(st.nim)}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/30 border border-indigo-400/40 text-[10px] font-bold text-indigo-200">
                                        ${escapeHtml(st.prodi || 'Informatika')}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1 flex items-center gap-3 flex-wrap">
                                    <span><i class="fa-solid fa-user-tie text-orange-400 mr-1"></i> Wali: <strong>${escapeHtml(st.nama_dosen_wali || 'Dosen Wali')}</strong></span>
                                    <span><i class="fa-solid fa-envelope text-slate-500 mr-1"></i> ${escapeHtml(st.email || '-')}</span>
                                    <span><i class="fa-solid fa-phone text-slate-500 mr-1"></i> ${escapeHtml(st.no_hp || '-')}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-3 py-1.5 rounded-xl bg-emerald-950/80 border border-emerald-500/50 text-emerald-300 text-xs font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i> Lolos Wali &amp; LAA
                            </span>
                        </div>
                    </div>

                    <!-- 2. Usulan Judul TA (Utama & Alternatif) -->
                    <div class="p-4 bg-orange-50/40 border border-orange-200/80 rounded-2xl space-y-3">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-700 block mb-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-star text-amber-500"></i> Usulan Judul 1 (Utama):
                            </span>
                            <p class="text-xs font-bold text-slate-900 leading-relaxed bg-white p-3 rounded-xl border border-orange-200/90 shadow-2xs">
                                ${escapeHtml(st.judul_1 || '-')}
                            </p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            ${st.judul_2 ? `
                                <div class="bg-white/90 p-2.5 rounded-xl border border-slate-200">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase block mb-0.5">Judul Alternatif 2:</span>
                                    <p class="text-[11px] text-slate-700 font-medium">${escapeHtml(st.judul_2)}</p>
                                </div>
                            ` : ''}
                            ${st.judul_3 ? `
                                <div class="bg-white/90 p-2.5 rounded-xl border border-slate-200">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase block mb-0.5">Judul Alternatif 3:</span>
                                    <p class="text-[11px] text-slate-700 font-medium">${escapeHtml(st.judul_3)}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- 3. Empat Berkas Persyaratan PDF (2x2 Grid) -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-folder-open text-orange-600"></i> 4 Berkas Persyaratan TA (Telah Diverifikasi LAA):
                            </h5>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${docsHtml}
                        </div>
                    </div>

                    <!-- 4. Individual Plotting Dosen Pembimbing (Khusus Mahasiswa Ini) -->
                    <div class="p-4 sm:p-5 bg-gradient-to-br from-indigo-50/50 via-white to-purple-50/30 rounded-2xl border-2 border-indigo-200/90 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-black text-indigo-950 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-chalkboard-user text-indigo-600 text-sm"></i>
                                Plotting Dosen Pembimbing Mahasiswa #${stIdx + 1} (${escapeHtml((st.nama || '').split(' ')[0])})
                            </h5>
                            <span class="text-[10px] text-indigo-600 font-bold bg-indigo-100/70 px-2.5 py-0.5 rounded-full">
                                Disesuaikan per individu
                            </span>
                        </div>

                        ${isConflict ? `
                            <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                                <span class="font-bold">Peringatan: Dosen Pembimbing 1 dan 2 tidak boleh orang yang sama!</span>
                            </div>
                        ` : ''}

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Slot 1: Pembimbing 1 -->
                            <div class="relative z-30" id="p1_wrapper_${stIdx}_1">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                    Dosen Pembimbing 1 (Utama) <span class="text-rose-500">*</span>
                                </label>

                                <!-- Chip Preview -->
                                <div id="p1_chip_${stIdx}_1" class="${p1Obj ? '' : 'hidden'} p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</div>
                                        <span id="p1_chip_name_${stIdx}_1" class="text-xs sm:text-sm font-bold text-indigo-950 truncate">${p1Obj ? escapeHtml(p1Obj.nama_dosen + ' (' + p1Obj.nip + ')') : ''}</span>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); changeP1MultiDosen(${stIdx}, 1)" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input Container -->
                                <div id="p1_search_container_${stIdx}_1" class="${p1Obj ? 'hidden' : ''} relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="p1_search_${stIdx}_1" onfocus="openP1MultiDosenDropdown(${stIdx}, 1)" onclick="openP1MultiDosenDropdown(${stIdx}, 1)" oninput="filterP1MultiDosen(${stIdx}, 1)" placeholder="Cari nama / NIP pembimbing 1..." class="w-full text-xs sm:text-sm font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" autocomplete="off">
                                        <button type="button" id="p1_clear_${stIdx}_1" onclick="clearP1MultiDosen(${stIdx}, 1)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="p1_dropdown_${stIdx}_1" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[150] max-h-56 overflow-y-auto custom-scrollbar p-1.5 divide-y divide-slate-100"></div>
                                </div>
                            </div>

                            <!-- Slot 2: Pembimbing 2 -->
                            <div class="relative z-20" id="p1_wrapper_${stIdx}_2">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                    Dosen Pembimbing 2 (Pendamping) <span class="text-rose-500">*</span>
                                </label>

                                <!-- Chip Preview -->
                                <div id="p1_chip_${stIdx}_2" class="${p2Obj ? '' : 'hidden'} p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                                        <span id="p1_chip_name_${stIdx}_2" class="text-xs sm:text-sm font-bold text-indigo-950 truncate">${p2Obj ? escapeHtml(p2Obj.nama_dosen + ' (' + p2Obj.nip + ')') : ''}</span>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); changeP1MultiDosen(${stIdx}, 2)" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input Container -->
                                <div id="p1_search_container_${stIdx}_2" class="${p2Obj ? 'hidden' : ''} relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="p1_search_${stIdx}_2" onfocus="openP1MultiDosenDropdown(${stIdx}, 2)" onclick="openP1MultiDosenDropdown(${stIdx}, 2)" oninput="filterP1MultiDosen(${stIdx}, 2)" placeholder="Cari nama / NIP pembimbing 2..." class="w-full text-xs sm:text-sm font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" autocomplete="off">
                                        <button type="button" id="p1_clear_${stIdx}_2" onclick="clearP1MultiDosen(${stIdx}, 2)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="p1_dropdown_${stIdx}_2" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[150] max-h-56 overflow-y-auto custom-scrollbar p-1.5 divide-y divide-slate-100"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Catatan Koordinator TA (Opsional untuk mahasiswa ini):</label>
                            <input type="text" 
                                   id="catatan_koor_${stIdx}" 
                                   value="${escapeHtml(st.catatan_koor || '')}" 
                                   oninput="if(window.p1BatchStudents[${stIdx}]) window.p1BatchStudents[${stIdx}].catatan_koor = this.value"
                                   placeholder="Tuliskan catatan / arahan spesifik untuk mahasiswa ini..." 
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-2xs">
                        </div>
                    </div>

                </div>
            `;
        });

        modalBody.innerHTML = allHtml;
    }

    window.openP1MultiDosenDropdown = function (stIdx, slotNum) {
        // Close all other p1_dropdowns
        document.querySelectorAll('[id^="p1_dropdown_"]').forEach(d => {
            if (d.id !== `p1_dropdown_${stIdx}_${slotNum}`) d.classList.add('hidden');
        });

        const w1 = document.getElementById(`p1_wrapper_${stIdx}_1`);
        const w2 = document.getElementById(`p1_wrapper_${stIdx}_2`);
        if (w1) w1.style.zIndex = (slotNum === 1) ? '100' : '20';
        if (w2) w2.style.zIndex = (slotNum === 2) ? '100' : '20';

        const drop = document.getElementById(`p1_dropdown_${stIdx}_${slotNum}`);
        if (!drop) return;
        renderP1MultiDosenDropdown(stIdx, slotNum, '');
        drop.classList.remove('hidden');
    };

    window.filterP1MultiDosen = function (stIdx, slotNum) {
        const input = document.getElementById(`p1_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`p1_clear_${stIdx}_${slotNum}`);
        const kw = input ? input.value : '';
        if (clearBtn) {
            if (kw) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }
        renderP1MultiDosenDropdown(stIdx, slotNum, kw);
    };

    window.clearP1MultiDosen = function (stIdx, slotNum) {
        const input = document.getElementById(`p1_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`p1_clear_${stIdx}_${slotNum}`);
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        renderP1MultiDosenDropdown(stIdx, slotNum, '');
    };

    function renderP1MultiDosenDropdown(stIdx, slotNum, kw) {
        const drop = document.getElementById(`p1_dropdown_${stIdx}_${slotNum}`);
        if (!drop) return;

        const st = window.p1BatchStudents[stIdx];
        const otherNip = slotNum === 1 ? st?.pembimbing_2 : st?.pembimbing_1;

        const filtered = (cfg.dosenList || []).filter(d => {
            if (!kw) return true;
            const q = kw.toLowerCase();
            return (d.nama_dosen || '').toLowerCase().includes(q) || (d.nip || '').toLowerCase().includes(q);
        });

        if (filtered.length === 0) {
            drop.innerHTML = `<div class="p-3 text-center text-slate-400 text-xs">Tidak ada dosen ditemukan.</div>`;
            return;
        }

        drop.innerHTML = filtered.map(d => {
            const isSelectedHere = (slotNum === 1 ? st?.pembimbing_1 : st?.pembimbing_2) === d.nip;
            const isSelectedOther = (d.nip === otherNip);

            return `
                <div onclick="${isSelectedOther ? '' : `selectP1MultiDosen(${stIdx}, ${slotNum}, '${d.nip}')`}" 
                     class="p-2.5 rounded-xl transition flex items-center justify-between text-xs ${isSelectedOther ? 'opacity-40 cursor-not-allowed bg-slate-50' : 'hover:bg-indigo-50 cursor-pointer'} ${isSelectedHere ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-800'}">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="truncate">
                            <p class="font-bold truncate text-xs">${escapeHtml(d.nama_dosen)}</p>
                            <p class="text-[10px] text-slate-400 font-mono">NIP: ${escapeHtml(d.nip)}</p>
                        </div>
                    </div>
                    ${isSelectedOther ? '<span class="text-[10px] text-rose-500 font-bold ml-2 shrink-0">Sudah dipilih</span>' : (isSelectedHere ? '<i class="fa-solid fa-check text-indigo-600 text-xs shrink-0"></i>' : '')}
                </div>
            `;
        }).join('');
    }

    window.selectP1MultiDosen = function (stIdx, slotNum, nip) {
        if (!window.p1BatchStudents[stIdx]) return;

        if (slotNum === 1) {
            window.p1BatchStudents[stIdx].pembimbing_1 = nip;
        } else {
            window.p1BatchStudents[stIdx].pembimbing_2 = nip;
        }

        const dosen = getDosenByNip(nip);
        const chip = document.getElementById(`p1_chip_${stIdx}_${slotNum}`);
        const chipName = document.getElementById(`p1_chip_name_${stIdx}_${slotNum}`);
        const searchContainer = document.getElementById(`p1_search_container_${stIdx}_${slotNum}`);
        const dropdown = document.getElementById(`p1_dropdown_${stIdx}_${slotNum}`);

        if (chipName && dosen) chipName.innerText = `${dosen.nama_dosen} (${dosen.nip})`;
        if (searchContainer) searchContainer.classList.add('hidden');
        if (chip) chip.classList.remove('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    window.changeP1MultiDosen = function (stIdx, slotNum) {
        if (window.p1BatchStudents[stIdx]) {
            if (slotNum === 1) window.p1BatchStudents[stIdx].pembimbing_1 = '';
            else window.p1BatchStudents[stIdx].pembimbing_2 = '';
        }
        const chip = document.getElementById(`p1_chip_${stIdx}_${slotNum}`);
        const searchContainer = document.getElementById(`p1_search_container_${stIdx}_${slotNum}`);
        const searchInput = document.getElementById(`p1_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`p1_clear_${stIdx}_${slotNum}`);

        if (chip) chip.classList.add('hidden');
        if (searchContainer) searchContainer.classList.remove('hidden');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        openP1MultiDosenDropdown(stIdx, slotNum);
    };

    window.applyFirstStudentDosenToAll = function () {
        if (!window.p1BatchStudents || window.p1BatchStudents.length === 0) return;
        const first = window.p1BatchStudents[0];
        if (!first.pembimbing_1 && !first.pembimbing_2) {
            Swal.fire({
                icon: 'warning',
                title: 'Pembimbing Belum Dipilih',
                text: `Silakan pilih Dosen Pembimbing pada Mahasiswa #1 (${first.nama || 'urutan pertama'}) terlebih dahulu sebelum menyalin ke mahasiswa lainnya.`,
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        const hasP1 = Boolean(first.pembimbing_1);
        const hasP2 = Boolean(first.pembimbing_2);

        window.p1BatchStudents.forEach((st, idx) => {
            if (idx > 0) {
                if (hasP1) st.pembimbing_1 = first.pembimbing_1;
                if (hasP2) st.pembimbing_2 = first.pembimbing_2;
            }
        });

        renderP1AllBatchStudentsContent();

        let toastMsg = `Pembimbing dari Mahasiswa #1 disalin ke semua ${window.p1BatchStudents.length} mahasiswa`;
        if (hasP1 && hasP2) {
            toastMsg = `Pembimbing 1 & 2 dari Mahasiswa #1 disalin ke semua ${window.p1BatchStudents.length} mahasiswa`;
        } else if (hasP1) {
            toastMsg = `Pembimbing 1 dari Mahasiswa #1 disalin ke semua ${window.p1BatchStudents.length} mahasiswa`;
        } else if (hasP2) {
            toastMsg = `Pembimbing 2 dari Mahasiswa #1 disalin ke semua ${window.p1BatchStudents.length} mahasiswa`;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: toastMsg,
            showConfirmButton: false,
            timer: 2500
        });
    };

    window.submitP1MultiDetailPlottings = function () {
        const students = window.p1BatchStudents;
        if (!students || students.length === 0) return;

        // Validasi kelengkapan pembimbing untuk seluruh mahasiswa
        for (let i = 0; i < students.length; i++) {
            const st = students[i];
            if (!st.pembimbing_1 || !st.pembimbing_2) {
                const el = document.getElementById(`p1_student_block_${i}`);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });

                Swal.fire({
                    icon: 'warning',
                    title: `Pembimbing Belum Lengkap (${st.nama})`,
                    text: `Mahasiswa #${i + 1} (${st.nama}) wajib memiliki Dosen Pembimbing 1 dan Pembimbing 2!`,
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
            if (st.pembimbing_1 === st.pembimbing_2) {
                const el = document.getElementById(`p1_student_block_${i}`);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });

                Swal.fire({
                    icon: 'error',
                    title: `Dosen Pembimbing Sama (${st.nama})`,
                    text: `Pembimbing 1 dan 2 pada mahasiswa #${i + 1} (${st.nama}) tidak boleh dosen yang sama!`,
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
        }

        Swal.fire({
            title: `Simpan & Setujui ${students.length} Mahasiswa?`,
            text: `Dosen pembimbing untuk ${students.length} mahasiswa akan disimpan dan status pendaftaran akan dilanjutkan ke Ketua KK.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Simpan & Setujui',
            cancelButtonText: 'Batal'
        }).then(res => {
            if (res.isConfirmed) {
                const btn = document.getElementById('btnSubmitP1BatchReview');
                const btnText = document.getElementById('btnSubmitP1BatchReviewText');
                if (btn) btn.disabled = true;
                if (btnText) btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

                const nims = students.map(s => s.nim);
                const plottings = students.map(s => ({
                    nim: s.nim,
                    pembimbing_1: s.pembimbing_1,
                    pembimbing_2: s.pembimbing_2,
                    catatan_koor: s.catatan_koor || ''
                }));

                const formData = new FormData();
                formData.append('nims', JSON.stringify(nims));
                formData.append('status', 'Approved');
                formData.append('plottings', JSON.stringify(plottings));

                fetch(cfg.ajaxBatchUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status) {
                        closeP1BatchReviewModal();
                        clearAllSelection();

                        Swal.fire({
                            icon: 'success',
                            title: 'Penetapan Pembimbing Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#ea580c'
                        });

                        if (cfg.ajaxRealtimeUrl) {
                            fetch(cfg.ajaxRealtimeUrl)
                                .then(r => r.json())
                                .then(res => {
                                    if (res && res.data) {
                                        state.list = res.data;
                                        if (res.stats) {
                                            const totalEl = document.getElementById('statTotalCount');
                                            const pendEl = document.getElementById('statPendingCount');
                                            const appEl = document.getElementById('statApprovedCount');
                                            const rejEl = document.getElementById('statRejectedCount');
                                            if (totalEl) totalEl.textContent = res.stats.total;
                                            if (pendEl) {
                                                const pct = res.stats.total > 0 ? Math.round((res.stats.pending / res.stats.total) * 100) : 0;
                                                pendEl.innerHTML = `${res.stats.pending} <span class="text-xs font-semibold text-cyan-600 font-normal">(${pct}%)</span>`;
                                            }
                                            if (appEl) {
                                                const pct = res.stats.total > 0 ? Math.round((res.stats.approved / res.stats.total) * 100) : 0;
                                                appEl.innerHTML = `${res.stats.approved} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                            }
                                            if (rejEl) rejEl.textContent = res.stats.rejected;
                                        }
                                        renderTable();
                                    }
                                });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: data.message || 'Terjadi kesalahan sistem.',
                            confirmButtonColor: '#ea580c'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Jaringan',
                        text: 'Gagal terhubung ke server.',
                        confirmButtonColor: '#ea580c'
                    });
                })
                .finally(() => {
                    if (btn) btn.disabled = false;
                    if (btnText) btnText.innerHTML = 'SIMPAN SEMUA PEMBIMBING &amp; SETUJUI';
                });
            }
        });
    };

    window.openP1PdfModal = function (url, title) {
        const modal = document.getElementById('p1PdfModal');
        const frame = document.getElementById('p1PdfModalFrame');
        const titleEl = document.getElementById('p1PdfModalTitle');
        if (titleEl) titleEl.innerText = title || 'Pratinjau Dokumen PDF';
        if (frame) frame.src = url;
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    };

    window.closeP1PdfModal = function () {
        const modal = document.getElementById('p1PdfModal');
        const frame = document.getElementById('p1PdfModalFrame');
        if (frame) frame.src = 'about:blank';
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    // =========================================================
    // 4. TABLE RENDER & PAGINATION
    // =========================================================
    function renderTable() {
        const tbody = document.getElementById('tableBodyMhs');
        const filtered = getFilteredMahasiswa();

        const totalRecords = filtered.length;
        const totalPages = Math.ceil(totalRecords / state.pageSize) || 1;
        if (state.currentPage > totalPages) state.currentPage = totalPages;

        const startIdx = (state.currentPage - 1) * state.pageSize;
        const endIndex = Math.min(startIdx + state.pageSize, totalRecords);
        const pageData = filtered.slice(startIdx, endIndex);

        if (document.getElementById('pageStart')) document.getElementById('pageStart').textContent = totalRecords > 0 ? (startIdx + 1) : 0;
        if (document.getElementById('pageEnd')) document.getElementById('pageEnd').textContent = endIndex;
        if (document.getElementById('totalRecordsBottom')) document.getElementById('totalRecordsBottom').textContent = totalRecords;
        if (document.getElementById('toolbarTotalCount')) document.getElementById('toolbarTotalCount').textContent = totalRecords;

        if (pageData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                        <p class="font-medium text-xs">Tidak ada data pengajuan yang ditemukan.</p>
                    </td>
                </tr>
            `;
            renderPagination(totalPages);
            return;
        }

        let html = '';
        pageData.forEach((mhs, idx) => {
            const stKoor = mhs.status_approval_koor || 'Pending';
            const stWali = mhs.status_approval_wali || 'Pending';
            const stAdmin = mhs.status_approval_admin || 'Pending';
            const stage = mhs.current_stage || 'Koordinator TA';

            const fullName = `${mhs.nama_depan || ''} ${mhs.nama_belakang || ''}`.trim();
            const judul = mhs.judul_1 || 'Belum Mendaftar';

            const isWaliApproved = (stWali.toLowerCase() === 'approved');
            const isAdminApproved = (stAdmin.toLowerCase() === 'approved');
            const isKoorPending = (stKoor.toLowerCase() === 'pending');

            // Koordinator TA hanya bisa memilih/menyetujui mahasiswa yang:
            // 1. Status Koordinator masih Pending
            // 2. Dosen Wali sudah Approved
            // 3. Admin Layanan sudah Approved
            const isEligibleForKoor = isKoorPending && isWaliApproved && isAdminApproved;

            let disabledTitle = '';
            if (stKoor === 'Approved') {
                disabledTitle = `Tidak dapat dipilih: Sudah disetujui Koordinator TA (Tahap saat ini: ${stage})`;
            } else if (stKoor === 'Rejected') {
                disabledTitle = `Tidak dapat dipilih: Status pendaftaran Ditolak / Perlu Revisi`;
            } else if (!isWaliApproved) {
                disabledTitle = `Belum dapat diproses Koordinator: Menunggu persetujuan Dosen Wali`;
            } else if (!isAdminApproved) {
                disabledTitle = `Belum dapat diproses Koordinator: Menunggu verifikasi berkas oleh Admin Layanan (Tahap saat ini: ${stage})`;
            }

            const isSelected = isEligibleForKoor && state.selectedStudents.has(mhs.nim);

            // 1. Status Badge Koordinator
            let statusBadgeHtml = '';
            if (stKoor === 'Approved') {
                statusBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-[11px] rounded-full border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-2xs"><i class="fa-solid fa-circle-check text-xs"></i> Disetujui</span>`;
            } else if (stKoor === 'Rejected') {
                statusBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-[11px] rounded-full border border-rose-300 bg-rose-50 text-rose-700 shadow-2xs"><i class="fa-solid fa-circle-xmark text-xs"></i> Perlu Revisi</span>`;
            } else if (isEligibleForKoor) {
                statusBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-[11px] rounded-full border border-orange-400 bg-orange-100 text-orange-950 shadow-xs"><i class="fa-solid fa-bell text-xs text-orange-600"></i> Siap Diproses</span>`;
            } else if (!isWaliApproved) {
                statusBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-medium text-[11px] rounded-full border border-sky-200 bg-sky-50 text-sky-700"><i class="fa-solid fa-clock text-[10px]"></i> Antre Dosen Wali</span>`;
            } else if (!isAdminApproved) {
                statusBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-medium text-[11px] rounded-full border border-indigo-200 bg-indigo-50 text-indigo-700"><i class="fa-solid fa-clock text-[10px]"></i> Antre Admin</span>`;
            } else {
                statusBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-[11px] rounded-full border border-amber-300 bg-amber-50 text-amber-700">Pending</span>`;
            }

            // 2. Tahap Saat Ini Badge
            let stageBadgeHtml = '';
            if (stage === 'Dosen Wali' || !isWaliApproved) {
                stageBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-semibold text-[11px] rounded-full border border-sky-200 bg-sky-50 text-sky-700"><i class="fa-solid fa-user-tie text-[10px]"></i> Dosen Wali</span>`;
            } else if (stage === 'Admin Layanan' || (isWaliApproved && !isAdminApproved)) {
                stageBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-semibold text-[11px] rounded-full border border-indigo-200 bg-indigo-50 text-indigo-700"><i class="fa-solid fa-file-signature text-[10px]"></i> Admin Layanan</span>`;
            } else if (stage === 'Koordinator TA') {
                stageBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-bold text-[11px] rounded-full border border-orange-300 bg-orange-50 text-orange-800 shadow-2xs"><i class="fa-solid fa-graduation-cap text-[10px] text-orange-600"></i> Koordinator TA</span>`;
            } else if (stage === 'Ketua KK') {
                stageBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-semibold text-[11px] rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700"><i class="fa-solid fa-user-check text-[10px]"></i> Ketua KK</span>`;
            } else {
                stageBadgeHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 font-semibold text-[11px] rounded-full border border-slate-200 bg-slate-100 text-slate-700">${escapeHtml(stage)}</span>`;
            }

            // 3. Row styling with Left Border highlight
            let rowClass = 'hover:bg-slate-50/80';
            if (isSelected) {
                rowClass = 'bg-orange-100/60 border-l-4 border-l-orange-600';
            } else if (isEligibleForKoor) {
                rowClass = 'bg-orange-50/30 hover:bg-orange-50/70 border-l-4 border-l-orange-500';
            }

            html += `
                <tr class="table-row-animate ${rowClass} transition-colors" style="--row-index: ${idx};">
                    <td class="py-4 px-4 pl-6 text-center">
                        ${isEligibleForKoor ? `
                            <input type="checkbox" 
                                class="row-select-checkbox w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer" 
                                value="${mhs.nim}" 
                                data-name="${escapeHtml(fullName)}" 
                                data-judul="${escapeHtml(judul)}"
                                data-stage="${escapeHtml(stage)}"
                                data-status="${escapeHtml(stKoor)}"
                                ${isSelected ? 'checked' : ''}
                                onchange="toggleRowSelect(this)">
                        ` : `
                            <input type="checkbox" 
                                disabled 
                                class="w-4 h-4 rounded text-slate-300 border-slate-200 cursor-not-allowed bg-slate-100 opacity-40" 
                                title="${escapeHtml(disabledTitle)}">
                        `}
                    </td>
                    <td class="py-4 px-4 font-bold text-slate-900">${mhs.nim}</td>
                    <td class="py-4 px-4 font-semibold text-slate-800">${escapeHtml(fullName)}</td>
                    <td class="py-4 px-4 text-slate-600 max-w-xs truncate font-normal" title="${escapeHtml(judul)}">${escapeHtml(judul)}</td>
                    <td class="py-4 px-4 text-center">
                        ${statusBadgeHtml}
                    </td>
                    <td class="py-4 px-4 text-center">
                        ${stageBadgeHtml}
                    </td>
                    <td class="py-4 px-4 pr-6 text-right">
                        <a href="${cfg.detailUrlPrefix}${mhs.nim}" class="btn-3d-kinetic" title="Detail & Approval Mahasiswa">
                            <div class="bg"></div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 342 208" height="208" width="342" class="splash">
                                <path stroke-linecap="round" stroke-width="3" d="M54.1054 99.7837C54.1054 99.7837 40.0984 90.7874 26.6893 97.6362C13.2802 104.485 1.5 97.6362 1.5 97.6362" />
                                <path stroke-linecap="round" stroke-width="3" d="M285.273 99.7841C285.273 99.7841 299.28 90.7879 312.689 97.6367C326.098 104.486 340.105 95.4893 340.105 95.4893" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M281.133 64.9917C281.133 64.9917 287.96 49.8089 302.934 48.2295C317.908 46.6501 319.712 36.5272 319.712 36.5272" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M281.133 138.984C281.133 138.984 287.96 154.167 302.934 155.746C317.908 157.326 319.712 167.449 319.712 167.449" />
                                <path stroke-linecap="round" stroke-width="3" d="M230.578 57.4476C230.578 57.4476 225.785 41.5051 236.061 30.4998C246.337 19.4945 244.686 12.9998 244.686 12.9998" />
                                <path stroke-linecap="round" stroke-width="3" d="M230.578 150.528C230.578 150.528 225.785 166.471 236.061 177.476C246.337 188.481 244.686 194.976 244.686 194.976" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M170.392 57.0278C170.392 57.0278 173.89 42.1322 169.571 29.54C165.252 16.9478 168.751 2.05227 168.751 2.05227" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M170.392 150.948C170.392 150.948 173.89 165.844 169.571 178.436C165.252 191.028 168.751 205.924 168.751 205.924" />
                                <path stroke-linecap="round" stroke-width="3" d="M112.609 57.4476C112.609 57.4476 117.401 41.5051 107.125 30.4998C96.8492 19.4945 98.5 12.9998 98.5 12.9998" />
                                <path stroke-linecap="round" stroke-width="3" d="M112.609 150.528C112.609 150.528 117.401 166.471 107.125 177.476C96.8492 188.481 98.5 194.976 98.5 194.976" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M62.2941 64.9917C62.2941 64.9917 55.4671 49.8089 40.4932 48.2295C25.5194 46.6501 23.7159 36.5272 23.7159 36.5272" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M62.2941 145.984C62.2941 145.984 55.4671 161.167 40.4932 162.746C25.5194 164.326 23.7159 174.449 23.7159 174.449" />
                            </svg>
                            <div class="wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 221 42" height="42" width="221" class="path">
                                    <path stroke-linecap="round" stroke-width="3" d="M182.674 2H203C211.837 2 219 9.16344 219 18V24C219 32.8366 211.837 40 203 40H18C9.16345 40 2 32.8366 2 24V18C2 9.16344 9.16344 2 18 2H47.8855" />
                                </svg>
                                <div class="outline"></div>
                                <div class="content">
                                    <span class="char state-1">${renderAnimatedChars('Detail & Approval')}</span>
                                    <span class="char state-2">${renderAnimatedChars('Periksa Berkas')}</span>
                                    <i class="fa-solid fa-arrow-right icon-action"></i>
                                </div>
                            </div>
                        </a>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        const selectableCheckboxes = document.querySelectorAll('.row-select-checkbox:not(:disabled)');
        const selectAllEl = document.getElementById('selectAllCheckbox');
        if (selectAllEl) {
            if (selectableCheckboxes.length === 0) {
                selectAllEl.checked = false;
                selectAllEl.disabled = true;
                selectAllEl.title = "Tidak ada mahasiswa berstatus Pending di halaman ini";
            } else {
                selectAllEl.disabled = false;
                selectAllEl.title = "Pilih Semua Mahasiswa Pending di Halaman Ini";
                selectAllEl.checked = Array.from(selectableCheckboxes).every(c => c.checked);
            }
        }

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const navContainer = document.getElementById('paginationNav');
        if (!navContainer) return;
        navContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const btnFirst = document.createElement('button');
        btnFirst.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnFirst.innerHTML = '&laquo; Awal';
        btnFirst.disabled = (state.currentPage === 1);
        btnFirst.addEventListener('click', () => goToPage(1));
        navContainer.appendChild(btnFirst);

        const btnPrev = document.createElement('button');
        btnPrev.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnPrev.innerHTML = '&lsaquo; Prev';
        btnPrev.disabled = (state.currentPage === 1);
        btnPrev.addEventListener('click', () => goToPage(state.currentPage - 1));
        navContainer.appendChild(btnPrev);

        const maxVisibleButtons = 5;
        let startPage = Math.max(1, state.currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);
        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            const btnPage = document.createElement('button');
            const isActive = (p === state.currentPage);
            btnPage.className = `px-3 py-1 rounded-lg text-xs font-bold transition ${isActive ? 'bg-orange-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'}`;
            btnPage.textContent = p;
            btnPage.addEventListener('click', () => goToPage(p));
            navContainer.appendChild(btnPage);
        }

        const btnNext = document.createElement('button');
        btnNext.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnNext.innerHTML = 'Next &rsaquo;';
        btnNext.disabled = (state.currentPage === totalPages);
        btnNext.addEventListener('click', () => goToPage(state.currentPage + 1));
        navContainer.appendChild(btnNext);

        const btnLast = document.createElement('button');
        btnLast.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnLast.innerHTML = 'Akhir &raquo;';
        btnLast.disabled = (state.currentPage === totalPages);
        btnLast.addEventListener('click', () => goToPage(totalPages));
        navContainer.appendChild(btnLast);
    }

    // =========================================================
    // 5. TAHAP PREVIEW 2 - MULTI-CRITERIA SEARCH ENGINE
    // =========================================================
    let extraP2RowCounter = 0;

    function showExtraCardP2() {
        const extraCard = document.getElementById('extraRowsCardP2');
        if (extraCard) extraCard.style.display = 'block';
    }

    function hideExtraCardP2() {
        const extraCard = document.getElementById('extraRowsCardP2');
        if (extraCard) extraCard.style.display = 'none';
    }

    function isExtraCardP2Visible() {
        const extraCard = document.getElementById('extraRowsCardP2');
        return extraCard && (extraCard.style.display === 'block' || window.getComputedStyle(extraCard).display !== 'none');
    }

    window.handleUnifiedMultiSearchP2 = function () {
        state.p2CurrentPage = 1;
        renderP2Table();
    };

    function updateP2FilterBadge() {
        const totalRows = document.querySelectorAll('.extra-filter-row-p2').length + 1;
        const badge = document.getElementById('filterCountBadgeP2');
        if (badge) badge.innerText = `${totalRows}/4`;
    }

    window.toggleOrAddFilterRowP2 = function (e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        closeAllCustomDropdowns();

        const extraRowsCount = document.querySelectorAll('.extra-filter-row-p2').length;

        if (extraRowsCount > 0 && !isExtraCardP2Visible()) {
            showExtraCardP2();
            return;
        }

        if (extraRowsCount >= 3) {
            const extraCard = document.getElementById('extraRowsCardP2');
            if (extraCard && extraCard.style.display === 'block') {
                hideExtraCardP2();
            } else {
                showExtraCardP2();
            }
            return;
        }

        addAdditionalP2FilterRow(e);
    };

    function isTextP2Category(cat) {
        return cat !== 'status';
    }

    function getPlaceholderForP2Category(cat) {
        if (cat === 'nama') return 'Ketik nama mahasiswa lalu tekan Enter atau klik Cari...';
        if (cat === 'nim') return 'Ketik NIM lalu tekan Enter atau klik Cari...';
        if (cat === 'judul') return 'Ketik topik/judul TA lalu tekan Enter atau klik Cari...';
        if (cat === 'pembimbing') return 'Ketik nama pembimbing lalu tekan Enter atau klik Cari...';
        if (cat === 'penguji') return 'Ketik nama penguji lalu tekan Enter atau klik Cari...';
        if (cat === 'ruangan') return 'Ketik ruangan sidang lalu tekan Enter atau klik Cari...';
        return 'Ketik kata kunci lalu tekan Enter atau klik Cari...';
    }

    function addAdditionalP2FilterRow(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        const extraRowsCount = document.querySelectorAll('.extra-filter-row-p2').length;
        if (extraRowsCount >= 3) {
            Swal.fire({
                icon: 'info',
                title: 'Maksimal 4 Filter',
                text: 'Maksimal 4 kriteria filter pencarian yang dapat aktif secara bersamaan.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        showExtraCardP2();

        extraP2RowCounter++;
        const rowId = extraP2RowCounter;

        const allCriteria = ['query', 'nama', 'nim', 'judul', 'pembimbing', 'penguji', 'ruangan', 'status'];
        const mainCat = document.getElementById('p2MainCategorySelect') ? document.getElementById('p2MainCategorySelect').value : 'query';
        const usedCriteria = [mainCat];
        document.querySelectorAll('.extra-p2-cat-select').forEach(el => usedCriteria.push(el.value));

        const defaultCrit = allCriteria.find(c => !usedCriteria.includes(c)) || 'status';

        let defaultLabel = '⚡ Status Plotting';
        if (defaultCrit === 'nama') defaultLabel = '🏷️ Nama Mahasiswa';
        else if (defaultCrit === 'nim') defaultLabel = '🆔 NIM Mahasiswa';
        else if (defaultCrit === 'judul') defaultLabel = '📖 Judul Tugas Akhir';
        else if (defaultCrit === 'pembimbing') defaultLabel = '👔 Dosen Pembimbing';
        else if (defaultCrit === 'penguji') defaultLabel = '👨‍🏫 Dosen Penguji';
        else if (defaultCrit === 'ruangan') defaultLabel = '🏛️ Ruangan Sidang';
        else if (defaultCrit === 'query') defaultLabel = '🔍 Kata Kunci (Semua)';

        const container = document.getElementById('additionalFilterRowsContainerP2');
        const rowDiv = document.createElement('div');
        rowDiv.className = 'extra-filter-row extra-filter-row-p2';
        rowDiv.id = `extraP2Row_${rowId}`;

        rowDiv.innerHTML = `
            <div class="unified-search-pill">
                <!-- Extra Category Dropdown -->
                <div class="relative custom-dropdown-container">
                    <input type="hidden" id="extraP2CatSelect_${rowId}" class="extra-p2-cat-select" value="${defaultCrit}">
                    <button type="button" onclick="toggleCustomDropdown('extra-p2-cat-${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-indigo-600 focus:outline-none">
                        <span id="label-filter-extra-p2-cat-${rowId}" class="truncate max-w-[130px]">${defaultLabel}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-p2-cat-${rowId}"></i>
                    </button>
                    <div id="menu-filter-extra-p2-cat-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                        <div onclick="selectP2ExtraCategory(${rowId}, 'query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'query' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>🔍 Kata Kunci (Semua)</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nama' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>🏷️ Nama Mahasiswa</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nim' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>🆔 NIM Mahasiswa</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'judul' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>📖 Judul Tugas Akhir</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'pembimbing', '👔 Dosen Pembimbing', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'pembimbing' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>👔 Dosen Pembimbing</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'penguji', '👨‍🏫 Dosen Penguji', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'penguji' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>👨‍🏫 Dosen Penguji</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'ruangan', '🏛️ Ruangan Sidang', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'ruangan' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>🏛️ Ruangan Sidang</span></div>
                        <div onclick="selectP2ExtraCategory(${rowId}, 'status', '⚡ Status Plotting', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'status' ? 'active bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'}"><span>⚡ Status Plotting</span></div>
                    </div>
                </div>

                <div class="unified-divider"></div>

                <!-- Input Text Value Container -->
                <div id="extraP2ValueContainer_${rowId}" class="${isTextP2Category(defaultCrit) ? 'flex-1 flex items-center' : 'hidden'}">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                    <input type="text" id="extraP2Input_${rowId}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handleUnifiedMultiSearchP2(); }" placeholder="${getPlaceholderForP2Category(defaultCrit)}" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                </div>

                <!-- Custom Dropdown Value Container -->
                <div id="extraP2CustomSelectWrap_${rowId}" class="${!isTextP2Category(defaultCrit) ? 'flex-1 relative custom-dropdown-container' : 'hidden'}">
                    <input type="hidden" id="extraP2ValueVal_${rowId}" class="extra-p2-val-input" value="">
                    <button type="button" onclick="toggleCustomDropdown('extra-p2-val-${rowId}', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                        <span id="label-filter-extra-p2-val-${rowId}" class="flex items-center gap-1.5 truncate">Semua Status Plotting</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-p2-val-${rowId}"></i>
                    </button>
                    <div id="menu-filter-extra-p2-val-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                    </div>
                </div>
            </div>

            <!-- Remove Row Button -->
            <button type="button" onclick="removeP2ExtraRow(${rowId})" class="btn-remove-row" title="Hapus Kriteria Ini">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        `;

        container.appendChild(rowDiv);
        if (!isTextP2Category(defaultCrit)) {
            updateP2ExtraValueOptions(rowId, defaultCrit);
        }
        updateP2FilterBadge();
    }

    window.removeP2ExtraRow = function (rowId) {
        const row = document.getElementById(`extraP2Row_${rowId}`);
        if (row) {
            row.remove();
            updateP2FilterBadge();
            const count = document.querySelectorAll('.extra-filter-row-p2').length;
            if (count === 0) hideExtraCardP2();
            handleUnifiedMultiSearchP2();
        }
    };

    window.selectP2MainCategory = function (cat, label, el) {
        const catSelect = document.getElementById('p2MainCategorySelect');
        if (catSelect) catSelect.value = cat;
        const catLabel = document.getElementById('label-filter-p2-main-cat');
        if (catLabel) catLabel.innerText = label;

        const menu = document.getElementById('menu-filter-p2-main-cat');
        if (menu) {
            menu.querySelectorAll('.dropdown-item').forEach(i => {
                i.classList.remove('bg-indigo-50', 'text-indigo-600');
                i.classList.add('text-slate-700');
            });
        }
        if (el) {
            el.classList.add('bg-indigo-50', 'text-indigo-600');
            el.classList.remove('text-slate-700');
        }

        const textWrap = document.getElementById('p2MainValueContainer');
        const selectWrap = document.getElementById('p2MainCustomSelectWrap');
        const inputEl = document.getElementById('p2MainSearchInput');

        if (isTextP2Category(cat)) {
            if (textWrap) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.add('hidden');
            if (inputEl) inputEl.placeholder = getPlaceholderForP2Category(cat);
        } else {
            if (textWrap) {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.remove('hidden');
            updateP2MainValueOptions(cat);
        }

        closeAllCustomDropdowns();
    };

    function updateP2MainValueOptions(cat) {
        const menu = document.getElementById('menu-filter-p2-main-select');
        const label = document.getElementById('label-filter-p2-main-select');
        const valInput = document.getElementById('p2MainCustomSelectVal');
        if (valInput) valInput.value = '';

        let html = '';
        if (cat === 'status') {
            if (label) label.innerText = 'Semua Status Plotting';
            html = `
                <div onclick="selectP2MainVal('', 'Semua Status Plotting', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-indigo-50 text-indigo-600"><span>Semua Status Plotting</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectP2MainVal('Terjadwal', 'Terjadwal Lengkap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terjadwal Lengkap</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectP2MainVal('Penguji Ditetapkan', 'Penguji Ditetapkan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Penguji Ditetapkan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectP2MainVal('Belum Diplot', 'Belum Diplot', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Belum Diplot Penguji</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        }
        if (menu) menu.innerHTML = html;
    }

    window.selectP2MainVal = function (val, labelText, el) {
        const valInput = document.getElementById('p2MainCustomSelectVal');
        const label = document.getElementById('label-filter-p2-main-select');
        if (valInput) valInput.value = val;
        if (label) label.innerText = labelText;
        closeAllCustomDropdowns();
    };

    window.selectP2ExtraCategory = function (rowId, cat, label, el) {
        const catSelect = document.getElementById(`extraP2CatSelect_${rowId}`);
        if (catSelect) catSelect.value = cat;
        const catLabel = document.getElementById(`label-filter-extra-p2-cat-${rowId}`);
        if (catLabel) catLabel.innerText = label;

        const textWrap = document.getElementById(`extraP2ValueContainer_${rowId}`);
        const selectWrap = document.getElementById(`extraP2CustomSelectWrap_${rowId}`);
        const inputEl = document.getElementById(`extraP2Input_${rowId}`);

        if (isTextP2Category(cat)) {
            if (textWrap) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.add('hidden');
            if (inputEl) inputEl.placeholder = getPlaceholderForP2Category(cat);
        } else {
            if (textWrap) {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.remove('hidden');
            updateP2ExtraValueOptions(rowId, cat);
        }

        closeAllCustomDropdowns();
    };

    function updateP2ExtraValueOptions(rowId, cat) {
        const menu = document.getElementById(`menu-filter-extra-p2-val-${rowId}`);
        const label = document.getElementById(`label-filter-extra-p2-val-${rowId}`);
        const valInput = document.getElementById(`extraP2ValueVal_${rowId}`);
        if (valInput) valInput.value = '';

        let html = '';
        if (cat === 'status') {
            if (label) label.innerText = 'Semua Status Plotting';
            html = `
                <div onclick="selectP2ExtraVal(${rowId}, '', 'Semua Status Plotting', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-indigo-50 text-indigo-600"><span>Semua Status Plotting</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectP2ExtraVal(${rowId}, 'Terjadwal', 'Terjadwal Lengkap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terjadwal Lengkap</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectP2ExtraVal(${rowId}, 'Penguji Ditetapkan', 'Penguji Ditetapkan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Penguji Ditetapkan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectP2ExtraVal(${rowId}, 'Belum Diplot', 'Belum Diplot', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Belum Diplot Penguji</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        }
        if (menu) menu.innerHTML = html;
    }

    window.selectP2ExtraVal = function (rowId, val, labelText, el) {
        const valInput = document.getElementById(`extraP2ValueVal_${rowId}`);
        const label = document.getElementById(`label-filter-extra-p2-val-${rowId}`);
        if (valInput) valInput.value = val;
        if (label) label.innerText = labelText;
        closeAllCustomDropdowns();
    };

    window.resetP2MultiSearch = function () {
        const catSelect = document.getElementById('p2MainCategorySelect');
        if (catSelect) catSelect.value = 'query';
        const catLabel = document.getElementById('label-filter-p2-main-cat');
        if (catLabel) catLabel.innerText = 'Cari Kata Kunci';
        
        const mainInput = document.getElementById('p2MainSearchInput');
        if (mainInput) {
            mainInput.value = '';
            mainInput.placeholder = 'Cari Nama, NIM, Judul TA, Dosen Penguji, Ruangan...';
        }

        const textWrap = document.getElementById('p2MainValueContainer');
        const selectWrap = document.getElementById('p2MainCustomSelectWrap');
        if (textWrap) {
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
        }
        if (selectWrap) selectWrap.classList.add('hidden');

        const mainVal = document.getElementById('p2MainCustomSelectVal');
        if (mainVal) mainVal.value = '';

        const container = document.getElementById('additionalFilterRowsContainerP2');
        if (container) container.innerHTML = '';

        hideExtraCardP2();
        updateP2FilterBadge();
        closeAllCustomDropdowns();
        handleUnifiedMultiSearchP2();
    };

    function getActiveP2FilterCriteria() {
        const criteria = [];
        const mainCat = document.getElementById('p2MainCategorySelect') ? document.getElementById('p2MainCategorySelect').value : 'query';
        let mainVal = '';

        if (isTextP2Category(mainCat)) {
            mainVal = document.getElementById('p2MainSearchInput') ? document.getElementById('p2MainSearchInput').value.trim() : '';
        } else {
            mainVal = document.getElementById('p2MainCustomSelectVal') ? document.getElementById('p2MainCustomSelectVal').value : '';
        }
        if (mainVal) criteria.push({ type: mainCat, val: mainVal });

        document.querySelectorAll('.extra-filter-row-p2').forEach(row => {
            const rowId = row.id.replace('extraP2Row_', '');
            const cat = document.getElementById('extraP2CatSelect_' + rowId) ? document.getElementById('extraP2CatSelect_' + rowId).value : 'query';
            let val = '';
            if (isTextP2Category(cat)) {
                val = document.getElementById('extraP2Input_' + rowId) ? document.getElementById('extraP2Input_' + rowId).value.trim() : '';
            } else {
                val = document.getElementById('extraP2ValueVal_' + rowId) ? document.getElementById('extraP2ValueVal_' + rowId).value : '';
            }
            if (val) criteria.push({ type: cat, val: val });
        });

        return criteria;
    }

    function getFilteredP2Mahasiswa() {
        const activeFilters = getActiveP2FilterCriteria();

        return state.p2List.filter(mhs => {
            const nim = (mhs.nim || '').toLowerCase();
            const nama = `${mhs.nama_depan || ''} ${mhs.nama_belakang || ''}`.toLowerCase();
            const judul = (mhs.judul_1 || '').toLowerCase();
            const pemb1 = (mhs.nama_pembimbing_1 || mhs.pembimbing_1 || '').toLowerCase();
            const pemb2 = (mhs.nama_pembimbing_2 || mhs.pembimbing_2 || '').toLowerCase();
            const peng1 = (mhs.nama_penguji_1 || mhs.penguji_1 || '').toLowerCase();
            const peng2 = (mhs.nama_penguji_2 || mhs.penguji_2 || '').toLowerCase();
            const ruang = (mhs.ruangan_sidang || mhs.detail_nama_ruangan || '').toLowerCase();
            const status = (mhs.status_preview2 || 'Belum Diplot').toLowerCase();

            for (let filter of activeFilters) {
                const valLower = filter.val.toLowerCase();
                if (filter.type === 'query') {
                    const match = nim.includes(valLower) ||
                                  nama.includes(valLower) ||
                                  judul.includes(valLower) ||
                                  pemb1.includes(valLower) ||
                                  pemb2.includes(valLower) ||
                                  peng1.includes(valLower) ||
                                  peng2.includes(valLower) ||
                                  ruang.includes(valLower) ||
                                  status.includes(valLower);
                    if (!match) return false;
                } else if (filter.type === 'nama') {
                    if (!nama.includes(valLower)) return false;
                } else if (filter.type === 'nim') {
                    if (!nim.includes(valLower)) return false;
                } else if (filter.type === 'judul') {
                    if (!judul.includes(valLower)) return false;
                } else if (filter.type === 'pembimbing') {
                    if (!pemb1.includes(valLower) && !pemb2.includes(valLower)) return false;
                } else if (filter.type === 'penguji') {
                    if (!peng1.includes(valLower) && !peng2.includes(valLower)) return false;
                } else if (filter.type === 'ruangan') {
                    if (!ruang.includes(valLower)) return false;
                } else if (filter.type === 'status') {
                    if (valLower !== '' && status !== valLower) return false;
                }
            }

            return true;
        });
    }

    function renderP2Table() {
        const tbody = document.getElementById('tableBodyP2');
        if (!tbody) return;

        const filtered = getFilteredP2Mahasiswa();
        const totalRecords = filtered.length;
        const totalPages = Math.ceil(totalRecords / state.p2PageSize) || 1;

        if (state.p2CurrentPage > totalPages) state.p2CurrentPage = totalPages;

        const startIdx = (state.p2CurrentPage - 1) * state.p2PageSize;
        const endIdx = Math.min(startIdx + state.p2PageSize, totalRecords);
        const pageData = filtered.slice(startIdx, endIdx);

        const pageStartEl = document.getElementById('p2PageStart');
        const pageEndEl = document.getElementById('p2PageEnd');
        const totalBottomEl = document.getElementById('p2TotalRecords');
        const toolbarTotalEl = document.getElementById('p2ToolbarTotalCount');
        if (pageStartEl) pageStartEl.textContent = totalRecords > 0 ? startIdx + 1 : 0;
        if (pageEndEl) pageEndEl.textContent = endIdx;
        if (totalBottomEl) totalBottomEl.textContent = totalRecords;
        if (toolbarTotalEl) toolbarTotalEl.textContent = totalRecords;

        if (pageData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="py-12 px-4 text-center text-slate-400">
                        <i class="fa-solid fa-chalkboard-user text-3xl mb-2 text-slate-300 block"></i>
                        <p class="font-medium text-xs">Tidak ada data mahasiswa preview 2 yang ditemukan.</p>
                    </td>
                </tr>
            `;
            renderP2Pagination(totalPages);
            return;
        }

        let html = '';
        pageData.forEach((mhs, idx) => {
            const fullName = `${mhs.nama_depan || ''} ${mhs.nama_belakang || ''}`.trim();
            const judul = mhs.judul_1 || 'Belum Menentukan Judul';
            const pemb1 = mhs.nama_pembimbing_1 || mhs.pembimbing_1 || '-';
            const pemb2 = mhs.nama_pembimbing_2 || mhs.pembimbing_2 || '-';
            const peng1 = mhs.nama_penguji_1 || mhs.penguji_1 || '';
            const peng2 = mhs.nama_penguji_2 || mhs.penguji_2 || '';
            const statusP2 = mhs.status_preview2 || 'Belum Diplot';
            const isSelected = state.p2SelectedStudents.has(mhs.nim);

            let statusBadge = '';
            if (statusP2 === 'Terjadwal' || statusP2 === 'Penguji Ditetapkan') {
                statusBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold text-[10px] rounded-full border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-2xs whitespace-nowrap"><i class="fa-solid fa-user-check text-[10px]"></i> Penguji Siap</span>`;
            } else {
                statusBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold text-[10px] rounded-full border border-amber-300 bg-amber-50 text-amber-700 shadow-2xs whitespace-nowrap"><i class="fa-solid fa-clock text-[10px]"></i> Belum Diplot</span>`;
            }

            let pembimbingHtml = `
                <div class="space-y-0.5 text-slate-700 text-[11px] leading-tight">
                    <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-orange-100 text-orange-700 flex items-center justify-center text-[9px] font-bold shrink-0">1</span> <span class="truncate max-w-[125px]" title="${escapeHtml(pemb1)}">${escapeHtml(pemb1)}</span></div>
                    <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-orange-100 text-orange-700 flex items-center justify-center text-[9px] font-bold shrink-0">2</span> <span class="truncate max-w-[125px]" title="${escapeHtml(pemb2)}">${escapeHtml(pemb2)}</span></div>
                </div>
            `;

            let pengujiHtml = '';
            if (peng1 && peng2) {
                pengujiHtml = `
                    <div class="space-y-0.5 text-slate-800 text-[11px] leading-tight">
                        <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center text-[9px] font-bold shrink-0">1</span> <span class="truncate max-w-[125px] font-semibold" title="${escapeHtml(peng1)}">${escapeHtml(peng1)}</span></div>
                        <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center text-[9px] font-bold shrink-0">2</span> <span class="truncate max-w-[125px] font-semibold" title="${escapeHtml(peng2)}">${escapeHtml(peng2)}</span></div>
                    </div>
                `;
            } else {
                pengujiHtml = `<span class="text-slate-400 italic text-[11px]">Belum ditentukan</span>`;
            }

            let rowHighlight = isSelected ? 'bg-indigo-50/70 border-l-4 border-l-indigo-600' : 'hover:bg-slate-50/80';

            html += `
                <tr class="table-row-animate ${rowHighlight} transition-colors" style="--row-index: ${idx};">
                    <td class="w-8 py-3 px-3 text-center">
                        <input type="checkbox" 
                            class="row-select-p2 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer" 
                            value="${mhs.nim}" 
                            data-name="${escapeHtml(fullName)}" 
                            data-judul="${escapeHtml(judul)}"
                            data-pemb1="${escapeHtml(pemb1)}"
                            data-pemb2="${escapeHtml(pemb2)}"
                            data-peng1="${escapeHtml(peng1)}"
                            data-peng2="${escapeHtml(peng2)}"
                            data-status="${escapeHtml(statusP2)}"
                            ${isSelected ? 'checked' : ''}
                            onchange="toggleRowSelectP2(this)">
                    </td>
                    <td class="w-24 py-3 px-2 font-bold font-mono text-[11px] text-slate-900">${mhs.nim}</td>
                    <td class="w-36 py-3 px-2 font-semibold text-slate-800 text-xs">
                        <span class="truncate block max-w-[130px]" title="${escapeHtml(fullName)}">${escapeHtml(fullName)}</span>
                    </td>
                    <td class="py-3 px-2 text-slate-600 font-normal">
                        <p class="line-clamp-2 max-w-[200px] text-[11px] leading-snug" title="${escapeHtml(judul)}">${escapeHtml(judul)}</p>
                    </td>
                    <td class="w-36 py-3 px-2">${pembimbingHtml}</td>
                    <td class="w-36 py-3 px-2">${pengujiHtml}</td>
                    <td class="w-28 py-3 px-2 text-center">${statusBadge}</td>
                    <td class="w-32 py-3 px-3 pr-4 text-right">
                        ${(() => {
                            const isReady = (peng1 && peng2) || (statusP2 === 'Penguji Ditetapkan');
                            const btnColor = isReady ? 'btn-emerald' : 'btn-indigo';
                            const label1 = isReady ? 'Ubah Penguji' : 'Plot Penguji';
                            const label2 = isReady ? 'Edit Penguji' : 'Dosen Penguji';
                            const iconClass = isReady ? 'fa-pen-to-square' : 'fa-arrow-right';
                            const btnTitle = isReady ? 'Ubah Dosen Penguji' : 'Plot Dosen Penguji';

                            return `
                                <button type="button" onclick="openP2SingleModal('${mhs.nim}')" class="btn-3d-kinetic ${btnColor} btn-compact ml-auto cursor-pointer" title="${btnTitle}">
                                    <div class="bg"></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 342 208" height="208" width="342" class="splash">
                                        <path stroke-linecap="round" stroke-width="3" d="M54.1054 99.7837C54.1054 99.7837 40.0984 90.7874 26.6893 97.6362C13.2802 104.485 1.5 97.6362 1.5 97.6362" />
                                        <path stroke-linecap="round" stroke-width="3" d="M285.273 99.7841C285.273 99.7841 299.28 90.7879 312.689 97.6367C326.098 104.486 340.105 95.4893 340.105 95.4893" />
                                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M281.133 64.9917C281.133 64.9917 287.96 49.8089 302.934 48.2295C317.908 46.6501 319.712 36.5272 319.712 36.5272" />
                                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M281.133 138.984C281.133 138.984 287.96 154.167 302.934 155.746C317.908 157.326 319.712 167.449 319.712 167.449" />
                                        <path stroke-linecap="round" stroke-width="3" d="M230.578 57.4476C230.578 57.4476 225.785 41.5051 236.061 30.4998C246.337 19.4945 244.686 12.9998 244.686 12.9998" />
                                        <path stroke-linecap="round" stroke-width="3" d="M230.578 150.528C230.578 150.528 225.785 166.471 236.061 177.476C246.337 188.481 244.686 194.976 244.686 194.976" />
                                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M170.392 57.0278C170.392 57.0278 173.89 42.1322 169.571 29.54C165.252 16.9478 168.751 2.05227 168.751 2.05227" />
                                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M170.392 150.948C170.392 150.948 173.89 165.844 169.571 178.436C165.252 191.028 168.751 205.924 168.751 205.924" />
                                        <path stroke-linecap="round" stroke-width="3" d="M112.609 57.4476C112.609 57.4476 117.401 41.5051 107.125 30.4998C96.8492 19.4945 98.5 12.9998 98.5 12.9998" />
                                        <path stroke-linecap="round" stroke-width="3" d="M112.609 150.528C112.609 150.528 117.401 166.471 107.125 177.476C96.8492 188.481 98.5 194.976 98.5 194.976" />
                                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M62.2941 64.9917C62.2941 64.9917 55.4671 49.8089 40.4932 48.2295C25.5194 46.6501 23.7159 36.5272 23.7159 36.5272" />
                                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M62.2941 145.984C62.2941 145.984 55.4671 161.167 40.4932 162.746C25.5194 164.326 23.7159 174.449 23.7159 174.449" />
                                    </svg>
                                    <div class="wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 221 42" height="42" width="221" class="path">
                                            <path stroke-linecap="round" stroke-width="3" d="M182.674 2H203C211.837 2 219 9.16344 219 18V24C219 32.8366 211.837 40 203 40H18C9.16345 40 2 32.8366 2 24V18C2 9.16344 9.16344 2 18 2H47.8855" />
                                        </svg>
                                        <div class="outline"></div>
                                        <div class="content">
                                            <span class="char state-1">${renderAnimatedChars(label1)}</span>
                                            <span class="char state-2">${renderAnimatedChars(label2)}</span>
                                            <i class="fa-solid ${iconClass} icon-action"></i>
                                        </div>
                                    </div>
                                </button>
                            `;
                        })()}
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        const selectAllEl = document.getElementById('selectAllCheckboxP2');
        if (selectAllEl) {
            const rowCheckboxes = document.querySelectorAll('.row-select-p2');
            selectAllEl.checked = rowCheckboxes.length > 0 && Array.from(rowCheckboxes).every(c => c.checked);
        }

        renderP2Pagination(totalPages);
    }

    function renderP2Pagination(totalPages) {
        const navContainer = document.getElementById('p2PaginationNav');
        if (!navContainer) return;
        navContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const btnFirst = document.createElement('button');
        btnFirst.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.p2CurrentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnFirst.innerHTML = '&laquo; Awal';
        btnFirst.disabled = (state.p2CurrentPage === 1);
        btnFirst.addEventListener('click', () => goToP2Page(1));
        navContainer.appendChild(btnFirst);

        const btnPrev = document.createElement('button');
        btnPrev.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.p2CurrentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnPrev.innerHTML = '&lsaquo; Prev';
        btnPrev.disabled = (state.p2CurrentPage === 1);
        btnPrev.addEventListener('click', () => goToP2Page(state.p2CurrentPage - 1));
        navContainer.appendChild(btnPrev);

        const maxVisibleButtons = 5;
        let startPage = Math.max(1, state.p2CurrentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);
        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            const btnPage = document.createElement('button');
            const isActive = (p === state.p2CurrentPage);
            btnPage.className = `px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer ${isActive ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'}`;
            btnPage.textContent = p;
            btnPage.addEventListener('click', () => goToP2Page(p));
            navContainer.appendChild(btnPage);
        }

        const btnNext = document.createElement('button');
        btnNext.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.p2CurrentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnNext.innerHTML = 'Next &rsaquo;';
        btnNext.disabled = (state.p2CurrentPage === totalPages);
        btnNext.addEventListener('click', () => goToP2Page(state.p2CurrentPage + 1));
        navContainer.appendChild(btnNext);

        const btnLast = document.createElement('button');
        btnLast.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.p2CurrentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnLast.innerHTML = 'Akhir &raquo;';
        btnLast.disabled = (state.p2CurrentPage === totalPages);
        btnLast.addEventListener('click', () => goToP2Page(totalPages));
        navContainer.appendChild(btnLast);
    }

    window.goToP2Page = function (page) {
        state.p2CurrentPage = page;
        renderP2Table();
    };

    window.changeP2PageSize = function (size) {
        state.p2PageSize = parseInt(size) || 10;
        state.p2CurrentPage = 1;
        renderP2Table();
    };

    window.handleP2Search = function (val) {
        state.p2SearchQuery = val;
        state.p2CurrentPage = 1;
        renderP2Table();
    };

    window.handleP2StatusFilter = function (val) {
        state.p2StatusFilter = val;
        state.p2CurrentPage = 1;
        renderP2Table();
    };

    window.toggleSelectAllP2 = function (el) {
        const checkboxes = document.querySelectorAll('.row-select-p2');
        checkboxes.forEach(cb => {
            cb.checked = el.checked;
            const nim = cb.value;
            const row = cb.closest('tr');
            if (el.checked) {
                state.p2SelectedStudents.set(nim, {
                    nim: nim,
                    name: cb.dataset.name,
                    judul: cb.dataset.judul,
                    pemb1: cb.dataset.pemb1,
                    pemb2: cb.dataset.pemb2
                });
                if (row) {
                    row.classList.add('bg-indigo-50/70', 'border-l-4', 'border-l-indigo-600');
                    row.classList.remove('hover:bg-slate-50/80');
                }
            } else {
                state.p2SelectedStudents.delete(nim);
                if (row) {
                    row.classList.remove('bg-indigo-50/70', 'border-l-4', 'border-l-indigo-600');
                    row.classList.add('hover:bg-slate-50/80');
                }
            }
        });
        updateP2FloatingBatchBar();
    };

    window.toggleRowSelectP2 = function (el) {
        const nim = el.value;
        const row = el.closest('tr');
        if (el.checked) {
            state.p2SelectedStudents.set(nim, {
                nim: nim,
                name: el.dataset.name,
                judul: el.dataset.judul,
                pemb1: el.dataset.pemb1,
                pemb2: el.dataset.pemb2
            });
            if (row) {
                row.classList.add('bg-indigo-50/70', 'border-l-4', 'border-l-indigo-600');
                row.classList.remove('hover:bg-slate-50/80');
            }
        } else {
            state.p2SelectedStudents.delete(nim);
            if (row) {
                row.classList.remove('bg-indigo-50/70', 'border-l-4', 'border-l-indigo-600');
                row.classList.add('hover:bg-slate-50/80');
            }
        }

        const pageCheckboxes = document.querySelectorAll('.row-select-p2');
        const allChecked = pageCheckboxes.length > 0 && Array.from(pageCheckboxes).every(c => c.checked);
        const selectAllEl = document.getElementById('selectAllCheckboxP2');
        if (selectAllEl) selectAllEl.checked = allChecked;

        updateP2FloatingBatchBar();
    };

    function updateP2FloatingBatchBar() {
        const bar = document.getElementById('floatingP2BatchBar');
        const badge = document.getElementById('p2SelectedCountBadge');
        const previewWrap = document.getElementById('p2SelectedStudentsPreview');
        if (!bar || !badge) return;

        const count = state.p2SelectedStudents.size;
        if (count > 0) {
            badge.textContent = count;
            if (previewWrap) {
                let chipsHtml = '';
                let idx = 0;
                state.p2SelectedStudents.forEach(st => {
                    if (idx < 3) {
                        chipsHtml += `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/20 text-white rounded-lg text-xs font-semibold backdrop-blur-md">
                                <i class="fa-solid fa-user-graduate text-[10px]"></i> ${escapeHtml(st.name.split(' ')[0])} (${st.nim})
                            </span>
                        `;
                    }
                    idx++;
                });

                if (count > 3) {
                    chipsHtml += `
                        <span class="inline-flex items-center px-2 py-1 bg-white/30 text-white rounded-lg text-xs font-bold">
                            +${count - 3} lainnya
                        </span>
                    `;
                }
                previewWrap.innerHTML = chipsHtml;
            }
            bar.classList.remove('hidden');
            bar.classList.add('block');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('block');
        }
    }

    window.clearAllP2Selection = function () {
        state.p2SelectedStudents.clear();
        document.querySelectorAll('.row-select-p2').forEach(cb => {
            cb.checked = false;
            const row = cb.closest('tr');
            if (row) {
                row.classList.remove('bg-indigo-50/70', 'border-l-4', 'border-l-indigo-600');
                row.classList.add('hover:bg-slate-50/80');
            }
        });
        const selectAllEl = document.getElementById('selectAllCheckboxP2');
        if (selectAllEl) selectAllEl.checked = false;
        updateP2FloatingBatchBar();
    };

    window.removeStudentFromP2Batch = function (nim) {
        state.p2SelectedStudents.delete(nim);
        const cb = document.querySelector(`.row-select-p2[value="${nim}"]`);
        if (cb) {
            cb.checked = false;
            const row = cb.closest('tr');
            if (row) {
                row.classList.remove('bg-indigo-50/70', 'border-l-4', 'border-l-indigo-600');
                row.classList.add('hover:bg-slate-50/80');
            }
        }

        const selectAllEl = document.getElementById('selectAllCheckboxP2');
        if (selectAllEl) selectAllEl.checked = false;

        if (state.p2SelectedStudents.size === 0) {
            closeP2Modal();
        } else {
            openP2BatchModal();
        }
        updateP2FloatingBatchBar();
    };

    // =========================================================
    // 5. TAHAP 2: PLOTTING DOSEN PENGUJI PER MAHASISWA (SAMA DENGAN TA)
    // =========================================================

    window.openP2SingleModal = function (nim) {
        state.p2TargetNim = nim;
        const mhs = state.p2List.find(m => m.nim === nim);
        if (!mhs) return;

        const fullName = `${mhs.nama_depan || ''} ${mhs.nama_belakang || ''}`.trim() || mhs.nama_lengkap || ('Mahasiswa ' + nim);
        const pemb1 = mhs.pembimbing_1 || '';
        const pemb2 = mhs.pembimbing_2 || '';
        const pemb1Name = mhs.nama_pembimbing_1 || mhs.pembimbing_1 || '-';
        const pemb2Name = mhs.nama_pembimbing_2 || mhs.pembimbing_2 || '-';

        state.p2QuickBatchStudents = [{
            nim: mhs.nim,
            name: fullName,
            judul: mhs.judul_1 || '',
            pemb1_nip: pemb1,
            pemb2_nip: pemb2,
            pemb1_name: pemb1Name,
            pemb2_name: pemb2Name,
            penguji_1: mhs.penguji_1 || '',
            penguji_2: mhs.penguji_2 || '',
            catatan_koor: mhs.catatan_koor || '',
            isExpanded: true
        }];

        const modal = document.getElementById('modalPreview2Plotting');
        if (modal) modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        renderP2QuickBatchCards();
    };

    window.openP2BatchModal = function () {
        if (state.p2SelectedStudents.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Mahasiswa Dipilih',
                text: 'Silakan centang setidaknya satu mahasiswa dari tabel preview 2.',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        state.p2TargetNim = null;

        state.p2QuickBatchStudents = Array.from(state.p2SelectedStudents.values()).map(s => {
            const m = state.p2List.find(x => x.nim === s.nim) || {};
            const fullName = s.name || `${m.nama_depan || ''} ${m.nama_belakang || ''}`.trim() || ('Mahasiswa ' + s.nim);
            const pemb1 = m.pembimbing_1 || '';
            const pemb2 = m.pembimbing_2 || '';
            const pemb1Name = m.nama_pembimbing_1 || m.pembimbing_1 || '-';
            const pemb2Name = m.nama_pembimbing_2 || m.pembimbing_2 || '-';

            return {
                nim: s.nim,
                name: fullName,
                judul: s.judul || m.judul_1 || '',
                pemb1_nip: pemb1,
                pemb2_nip: pemb2,
                pemb1_name: pemb1Name,
                pemb2_name: pemb2Name,
                penguji_1: m.penguji_1 || '',
                penguji_2: m.penguji_2 || '',
                catatan_koor: m.catatan_koor || '',
                isExpanded: true
            };
        });

        const modal = document.getElementById('modalPreview2Plotting');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.style.display = 'flex';
        }
        document.body.classList.add('overflow-hidden');

        renderP2QuickBatchCards();
    };

    window.closeP2Modal = function () {
        const modal = document.getElementById('modalPreview2Plotting');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.display = 'none';
        }
        document.body.classList.remove('overflow-hidden');
    };

    function renderP2QuickBatchCards() {
        const listEl = document.getElementById('p2ModalSelectedList');
        const badge = document.getElementById('p2ModalSelectedCountBadge');
        if (badge) badge.textContent = `${state.p2QuickBatchStudents.length} Mahasiswa`;

        if (!listEl) return;

        if (state.p2QuickBatchStudents.length === 0) {
            closeP2Modal();
            return;
        }

        let html = '';
        state.p2QuickBatchStudents.forEach((st, idx) => {
            const u1 = getDosenByNip(st.penguji_1);
            const u2 = getDosenByNip(st.penguji_2);

            const isConflictPemb = (
                (st.penguji_1 && (st.penguji_1 === st.pemb1_nip || st.penguji_1 === st.pemb2_nip)) ||
                (st.penguji_2 && (st.penguji_2 === st.pemb1_nip || st.penguji_2 === st.pemb2_nip))
            );
            const isSameDosen = (st.penguji_1 && st.penguji_2 && st.penguji_1 === st.penguji_2);
            const isComplete = (st.penguji_1 && st.penguji_2 && !isSameDosen && !isConflictPemb);

            html += `
                <div id="p2_quick_card_${idx}" class="bg-white rounded-2xl border ${isConflictPemb || isSameDosen ? 'border-rose-400 ring-2 ring-rose-200' : (isComplete ? 'border-emerald-300 ring-1 ring-emerald-100' : 'border-slate-200')} shadow-xs transition-all relative z-10">
                    <!-- Student Header / Summary Bar -->
                    <div class="p-3.5 px-4 bg-slate-50 hover:bg-slate-100/80 rounded-t-2xl flex items-center justify-between gap-3 transition cursor-pointer" onclick="toggleP2QuickStudentCard(${idx})">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl ${isComplete ? 'bg-emerald-600' : 'bg-indigo-600'} text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                ${idx + 1}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">${escapeHtml(st.name)}</h5>
                                    <span class="text-[10px] font-mono font-bold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                        ${st.nim}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0" onclick="event.stopPropagation()">
                            ${isConflictPemb ? `
                                <span class="px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-[10px] font-bold">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Konflik Pembimbing
                                </span>
                            ` : (isSameDosen ? `
                                <span class="px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-[10px] font-bold">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Dosen Sama
                                </span>
                            ` : (isComplete ? `
                                <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-300 text-emerald-700 rounded-lg text-[10px] font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span class="truncate max-w-[140px]">${escapeHtml((u1?.nama_dosen || 'U1').split(' ')[0])} &amp; ${escapeHtml((u2?.nama_dosen || 'U2').split(' ')[0])}</span>
                                </span>
                            ` : `
                                <span class="px-2.5 py-1 bg-amber-50 border border-amber-300 text-amber-700 rounded-lg text-[10px] font-bold">
                                    <i class="fa-solid fa-clock mr-1"></i> Belum Lengkap
                                </span>
                            `))}

                            ${state.p2QuickBatchStudents.length > 1 ? `
                                <button type="button" onclick="removeStudentFromP2Batch('${st.nim}')" class="w-7 h-7 rounded-lg bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition border border-slate-200 cursor-pointer" title="Hapus mahasiswa ini dari pilihan">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            ` : ''}

                            <button type="button" onclick="toggleP2QuickStudentCard(${idx})" class="w-7 h-7 rounded-lg bg-white hover:bg-slate-200 text-slate-500 flex items-center justify-center transition border border-slate-200 cursor-pointer">
                                <i class="fa-solid ${st.isExpanded ? 'fa-chevron-up' : 'fa-chevron-down'} text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Student Body: Dosen Penguji 1 & 2 Combobox -->
                    <div id="p2_quick_card_body_${idx}" class="${st.isExpanded ? '' : 'hidden'} p-4 bg-white border-t border-slate-100 rounded-b-2xl space-y-3.5">
                        
                        <!-- Dosen Pembimbing Banner Info -->
                        <div class="p-2.5 bg-orange-50/70 border border-orange-200/80 rounded-xl text-[11px] flex flex-wrap items-center justify-between gap-2 text-slate-700">
                            <span class="font-bold text-orange-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-tie text-orange-600"></i> Dosen Pembimbing Mahasiswa:
                            </span>
                            <div class="flex items-center gap-3">
                                <span>Pemb. 1: <strong class="text-orange-900 font-semibold">${escapeHtml(st.pemb1_name || '-')}</strong></span>
                                <span>|</span>
                                <span>Pemb. 2: <strong class="text-orange-900 font-semibold">${escapeHtml(st.pemb2_name || '-')}</strong></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Slot 1: Penguji 1 -->
                            <div class="relative z-30" id="p2_q_wrapper_${idx}_1">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                    Penguji 1 (Utama) <span class="text-rose-500">*</span>
                                </label>

                                <!-- Chip -->
                                <div id="p2_q_chip_${idx}_1" class="${u1 ? '' : 'hidden'} p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</div>
                                        <span id="p2_q_chip_name_${idx}_1" class="text-xs sm:text-sm font-bold text-indigo-950 truncate">${u1 ? escapeHtml(u1.nama_dosen + ' (' + u1.nip + ')') : ''}</span>
                                    </div>
                                    <button type="button" onclick="changeP2QuickDosen(${idx}, 1)" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="p2_q_search_container_${idx}_1" class="${u1 ? 'hidden' : ''} relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="p2_q_search_${idx}_1" onfocus="openP2QuickDosenDropdown(${idx}, 1)" onclick="openP2QuickDosenDropdown(${idx}, 1)" oninput="filterP2QuickDosen(${idx}, 1)" placeholder="Cari nama / NIP penguji 1..." class="w-full text-xs sm:text-sm font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" autocomplete="off">
                                        <button type="button" id="p2_q_clear_${idx}_1" onclick="clearP2QuickDosen(${idx}, 1)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="p2_q_dropdown_${idx}_1" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[150] max-h-56 overflow-y-auto custom-scrollbar p-1.5 divide-y divide-slate-100"></div>
                                </div>
                            </div>

                            <!-- Slot 2: Penguji 2 -->
                            <div class="relative z-20" id="p2_q_wrapper_${idx}_2">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                    Penguji 2 (Pendamping) <span class="text-rose-500">*</span>
                                </label>

                                <!-- Chip -->
                                <div id="p2_q_chip_${idx}_2" class="${u2 ? '' : 'hidden'} p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                                        <span id="p2_q_chip_name_${idx}_2" class="text-xs sm:text-sm font-bold text-indigo-950 truncate">${u2 ? escapeHtml(u2.nama_dosen + ' (' + u2.nip + ')') : ''}</span>
                                    </div>
                                    <button type="button" onclick="changeP2QuickDosen(${idx}, 2)" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="p2_q_search_container_${idx}_2" class="${u2 ? 'hidden' : ''} relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="p2_q_search_${idx}_2" onfocus="openP2QuickDosenDropdown(${idx}, 2)" onclick="openP2QuickDosenDropdown(${idx}, 2)" oninput="filterP2QuickDosen(${idx}, 2)" placeholder="Cari nama / NIP penguji 2..." class="w-full text-xs sm:text-sm font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" autocomplete="off">
                                        <button type="button" id="p2_q_clear_${idx}_2" onclick="clearP2QuickDosen(${idx}, 2)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="p2_q_dropdown_${idx}_2" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[150] max-h-56 overflow-y-auto custom-scrollbar p-1.5 divide-y divide-slate-100"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <input type="text" 
                                   value="${escapeHtml(st.catatan_koor || '')}" 
                                   oninput="state.p2QuickBatchStudents[${idx}].catatan_koor = this.value"
                                   placeholder="Catatan khusus untuk ${escapeHtml((st.name || '').split(' ')[0])} (opsional)..." 
                                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-2xs font-medium">
                        </div>
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
    }

    window.toggleP2QuickStudentCard = function (stIdx) {
        if (!state.p2QuickBatchStudents[stIdx]) return;
        state.p2QuickBatchStudents[stIdx].isExpanded = !state.p2QuickBatchStudents[stIdx].isExpanded;
        renderP2QuickBatchCards();
    };

    window.openP2QuickDosenDropdown = function (stIdx, slotNum) {
        document.querySelectorAll('[id^="p2_q_dropdown_"]').forEach(d => {
            if (d.id !== `p2_q_dropdown_${stIdx}_${slotNum}`) d.classList.add('hidden');
        });

        document.querySelectorAll('[id^="p2_quick_card_"]').forEach(c => c.style.zIndex = '10');
        const currentCard = document.getElementById(`p2_quick_card_${stIdx}`);
        if (currentCard) currentCard.style.zIndex = '90';

        const wrapper1 = document.getElementById(`p2_q_wrapper_${stIdx}_1`);
        const wrapper2 = document.getElementById(`p2_q_wrapper_${stIdx}_2`);
        if (wrapper1) wrapper1.style.zIndex = (slotNum === 1) ? '100' : '20';
        if (wrapper2) wrapper2.style.zIndex = (slotNum === 2) ? '100' : '20';

        const drop = document.getElementById(`p2_q_dropdown_${stIdx}_${slotNum}`);
        if (!drop) return;
        renderP2QuickDosenDropdown(stIdx, slotNum, '');
        drop.classList.remove('hidden');
    };

    window.filterP2QuickDosen = function (stIdx, slotNum) {
        const input = document.getElementById(`p2_q_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`p2_q_clear_${stIdx}_${slotNum}`);
        const kw = input ? input.value : '';
        if (clearBtn) {
            if (kw) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }
        renderP2QuickDosenDropdown(stIdx, slotNum, kw);
    };

    window.clearP2QuickDosen = function (stIdx, slotNum) {
        const st = state.p2QuickBatchStudents[stIdx];
        if (!st) return;

        if (slotNum === 1) st.penguji_1 = '';
        else st.penguji_2 = '';

        const input = document.getElementById(`p2_q_search_${stIdx}_${slotNum}`);
        const clearBtn = document.getElementById(`p2_q_clear_${stIdx}_${slotNum}`);
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');

        renderP2QuickDosenDropdown(stIdx, slotNum, '');
        renderP2QuickBatchCards();
    };

    window.changeP2QuickDosen = function (stIdx, slotNum) {
        const st = state.p2QuickBatchStudents[stIdx];
        if (!st) return;

        if (slotNum === 1) st.penguji_1 = '';
        else st.penguji_2 = '';

        renderP2QuickBatchCards();

        setTimeout(() => {
            const input = document.getElementById(`p2_q_search_${stIdx}_${slotNum}`);
            if (input) {
                input.focus();
                openP2QuickDosenDropdown(stIdx, slotNum);
            }
        }, 50);
    };

    window.selectP2QuickDosen = function (stIdx, slotNum, nip, namaDosen) {
        const st = state.p2QuickBatchStudents[stIdx];
        if (!st) return;

        // Check if supervisor conflict
        if (nip === st.pemb1_nip || nip === st.pemb2_nip) {
            Swal.fire({
                icon: 'error',
                title: 'Konflik Dosen Pembimbing',
                text: `Dosen ${namaDosen} adalah Dosen Pembimbing mahasiswa ini. Dosen Pembimbing tidak boleh menjadi Penguji!`,
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        // Check if other slot has the same lecturer
        const otherSlotNip = (slotNum === 1) ? st.penguji_2 : st.penguji_1;
        if (otherSlotNip && otherSlotNip === nip) {
            Swal.fire({
                icon: 'warning',
                title: 'Dosen Sama',
                text: 'Dosen Penguji 1 dan Dosen Penguji 2 tidak boleh orang yang sama!',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        if (slotNum === 1) st.penguji_1 = nip;
        else st.penguji_2 = nip;

        renderP2QuickBatchCards();
    };

    function renderP2QuickDosenDropdown(stIdx, slotNum, query) {
        const drop = document.getElementById(`p2_q_dropdown_${stIdx}_${slotNum}`);
        if (!drop) return;

        const st = state.p2QuickBatchStudents[stIdx];
        const q = (query || '').toLowerCase().trim();
        const dosenList = cfg.dosenList || [];

        const filtered = dosenList.filter(d => {
            if (!q) return true;
            const nama = (d.nama_dosen || '').toLowerCase();
            const nip = (d.nip || '').toLowerCase();
            const keahlian = (d.keahlian || '').toLowerCase();
            return nama.includes(q) || nip.includes(q) || keahlian.includes(q);
        });

        if (filtered.length === 0) {
            drop.innerHTML = `<div class="p-3 text-center text-xs text-slate-400 italic">Dosen "${escapeHtml(query)}" tidak ditemukan.</div>`;
            return;
        }

        let html = '';
        filtered.forEach(d => {
            const isSupervisor = (st && (d.nip === st.pemb1_nip || d.nip === st.pemb2_nip));
            const isSelectedHere = (st && ((slotNum === 1 && st.penguji_1 === d.nip) || (slotNum === 2 && st.penguji_2 === d.nip)));
            const isOtherSlot = (st && ((slotNum === 1 && st.penguji_2 === d.nip) || (slotNum === 2 && st.penguji_1 === d.nip)));

            html += `
                <div onclick="${isSupervisor ? `Swal.fire({ icon: 'error', title: 'Konflik Dosen Pembimbing', text: 'Dosen ini adalah Dosen Pembimbing mahasiswa! Tidak boleh menjadi penguji.' })` : `selectP2QuickDosen(${stIdx}, ${slotNum}, '${d.nip}', '${escapeHtml(d.nama_dosen)}')`}" 
                     class="p-2.5 rounded-xl transition cursor-pointer flex items-center justify-between gap-2.5 ${isSupervisor ? 'opacity-40 bg-rose-50/50 cursor-not-allowed' : (isSelectedHere ? 'bg-indigo-50 border border-indigo-300' : (isOtherSlot ? 'bg-slate-50 opacity-60' : 'hover:bg-indigo-50/70'))}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg ${isSupervisor ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700'} font-bold text-xs flex items-center justify-center shrink-0">
                            ${(d.nama_dosen || 'D').charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">${escapeHtml(d.nama_dosen)}</p>
                            <p class="text-[10px] text-slate-400 font-mono">${escapeHtml(d.nip)} ${d.keahlian ? '• ' + escapeHtml(d.keahlian) : ''}</p>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        ${isSupervisor ? `
                            <span class="text-[9px] font-bold text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Pembimbing Mhs</span>
                        ` : (isSelectedHere ? `
                            <span class="text-xs font-bold text-indigo-600"><i class="fa-solid fa-check"></i> Terpilih</span>
                        ` : (isOtherSlot ? `
                            <span class="text-[9px] font-semibold text-slate-400">Dipilih di Slot ${slotNum === 1 ? '2' : '1'}</span>
                        ` : ''))}
                    </div>
                </div>
            `;
        });

        drop.innerHTML = html;
    }

    window.removeStudentFromP2Batch = function (nim) {
        state.p2QuickBatchStudents = state.p2QuickBatchStudents.filter(s => s.nim !== nim);
        state.p2SelectedStudents.delete(nim);
        renderP2QuickBatchCards();
        updateP2FloatingBatchBar();
        renderP2Table();
    };

    window.applyQuickFirstP2ToAll = function () {
        if (state.p2QuickBatchStudents.length === 0) return;
        const first = state.p2QuickBatchStudents[0];
        if (!first.penguji_1 || !first.penguji_2) {
            Swal.fire({
                icon: 'info',
                title: 'Lengkapi Mahasiswa #1 Terlebih Dahulu',
                text: 'Pilih Dosen Penguji 1 dan Penguji 2 pada Mahasiswa #1 sebelum menyalin ke semua mahasiswa lainnya.',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        let conflictCount = 0;
        let appliedCount = 0;

        state.p2QuickBatchStudents.forEach((st, idx) => {
            if (idx === 0) return;

            // Check if supervisor conflict for this student
            const hasConflict = (
                first.penguji_1 === st.pemb1_nip ||
                first.penguji_1 === st.pemb2_nip ||
                first.penguji_2 === st.pemb1_nip ||
                first.penguji_2 === st.pemb2_nip
            );

            if (hasConflict) {
                conflictCount++;
            } else {
                st.penguji_1 = first.penguji_1;
                st.penguji_2 = first.penguji_2;
                appliedCount++;
            }
        });

        renderP2QuickBatchCards();

        if (conflictCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Disalin dengan Peringatan',
                text: `Dosen penguji berhasil disalin ke ${appliedCount} mahasiswa. Namun ${conflictCount} mahasiswa dilewati karena bentrok dengan Dosen Pembimbing mereka!`,
                confirmButtonColor: '#4f46e5'
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disalin!',
                text: `Dosen Penguji dari Mahasiswa #1 berhasil disalin ke ${appliedCount} mahasiswa lainnya.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    };

    window.submitP2Plotting = function (e) {
        e.preventDefault();

        if (state.p2QuickBatchStudents.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Data Kosong', text: 'Tidak ada mahasiswa yang dipilih.' });
            return;
        }

        // Validation for each student
        for (let i = 0; i < state.p2QuickBatchStudents.length; i++) {
            const st = state.p2QuickBatchStudents[i];
            if (!st.penguji_1 || !st.penguji_2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dosen Penguji Belum Lengkap',
                    text: `Silakan lengkapi Dosen Penguji 1 & 2 untuk mahasiswa #${i + 1}: ${st.name} (${st.nim}).`,
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            if (st.penguji_1 === st.penguji_2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Dosen Penguji Sama',
                    text: `Dosen Penguji 1 & 2 tidak boleh sama untuk mahasiswa #${i + 1}: ${st.name} (${st.nim})!`,
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            if (st.penguji_1 === st.pemb1_nip || st.penguji_1 === st.pemb2_nip || st.penguji_2 === st.pemb1_nip || st.penguji_2 === st.pemb2_nip) {
                Swal.fire({
                    icon: 'error',
                    title: 'Konflik Dosen Pembimbing',
                    text: `Dosen Penguji pada mahasiswa #${i + 1}: ${st.name} (${st.nim}) tidak boleh bertindak sebagai Dosen Pembimbingnya!`,
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        }

        const btnSubmit = document.getElementById('modalP2BtnSubmit');
        const btnText = document.getElementById('modalP2BtnSubmitText');

        if (btnSubmit) btnSubmit.disabled = true;
        if (btnText) btnText.textContent = 'Menyimpan...';

        const nims = state.p2QuickBatchStudents.map(s => s.nim);
        const plottings = state.p2QuickBatchStudents.map(s => ({
            nim: s.nim,
            penguji_1: s.penguji_1,
            penguji_2: s.penguji_2,
            catatan_koor: s.catatan_koor || ''
        }));

        const globalCatatan = document.getElementById('p2ModalGlobalCatatanKoor') ? document.getElementById('p2ModalGlobalCatatanKoor').value : '';

        const formData = new FormData();
        formData.append('nims', JSON.stringify(nims));
        formData.append('plottings', JSON.stringify(plottings));
        formData.append('catatan_koor', globalCatatan);

        fetch(cfg.ajaxPreview2BatchUrl, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res && res.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Penetapan Dosen Penguji Preview 2 berhasil disimpan!',
                    confirmButtonColor: '#4f46e5'
                });
                closeP2Modal();

                if (cfg.ajaxPreview2RealtimeUrl) {
                    fetch(cfg.ajaxPreview2RealtimeUrl)
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.data) {
                                state.p2List = data.data;
                                if (data.stats) {
                                    const t = document.getElementById('statP2Total');
                                    const tr = document.getElementById('statP2Terjadwal');
                                    const ps = document.getElementById('statP2Penguji');
                                    const bl = document.getElementById('statP2Belum');
                                    if (t) t.textContent = data.stats.total;
                                    if (tr) {
                                        const pct = data.stats.total > 0 ? Math.round((data.stats.terjadwal / data.stats.total) * 100) : 0;
                                        tr.innerHTML = `${data.stats.terjadwal} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                    }
                                    if (ps) ps.textContent = data.stats.penguji_set;
                                    if (bl) {
                                        const pct = data.stats.total > 0 ? Math.round((data.stats.belum_set / data.stats.total) * 100) : 0;
                                        bl.innerHTML = `${data.stats.belum_set} <span class="text-xs font-semibold text-amber-600 font-normal">(${pct}%)</span>`;
                                    }
                                }
                                clearAllP2Selection();
                                renderP2Table();
                            }
                        });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: res.message || 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonColor: '#4f46e5'
                });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.', confirmButtonColor: '#4f46e5' });
        })
        .finally(() => {
            if (btnSubmit) btnSubmit.disabled = false;
            if (btnText) btnText.textContent = 'Simpan Penetapan Dosen Penguji';
        });
    };

    // =========================================================
    // RUANGAN AUTOCOMPLETE COMBOBOX (TAHAP PREVIEW 2)
    // =========================================================
    function getRuanganList() {
        return cfg.ruanganList || [];
    }

    window.openP2ModalRuanganList = function () {
        const input = document.getElementById('p2ModalSearchRuangan');
        filterP2ModalRuangan(input ? input.value : '');
        const drop = document.getElementById('p2ModalDropdownRuangan');
        if (drop) drop.classList.remove('hidden');
    };

    window.filterP2ModalRuangan = function (query) {
        const drop = document.getElementById('p2ModalDropdownRuangan');
        const clearBtn = document.getElementById('p2ModalClearRuangan');
        if (!drop) return;

        const q = (query || '').toLowerCase().trim();
        if (clearBtn) {
            if (q) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        const list = getRuanganList();
        const filtered = list.filter(r => {
            if (!q) return true;
            const nama = (r.nama_ruangan || '').toLowerCase();
            const kode = (r.kode_ruangan || '').toLowerCase();
            const lokasi = (r.lokasi || '').toLowerCase();
            return nama.includes(q) || kode.includes(q) || lokasi.includes(q);
        });

        if (filtered.length === 0) {
            drop.innerHTML = `
                <div class="p-4 text-center text-slate-400 text-xs font-medium">
                    <i class="fa-solid fa-door-closed text-slate-300 text-xl block mb-1"></i>
                    Ruangan "${escapeHtml(query)}" tidak ditemukan.
                </div>
            `;
        } else {
            drop.innerHTML = filtered.map(r => {
                const nama = r.nama_ruangan || '';
                const kode = r.kode_ruangan || '';
                const lokasi = r.lokasi || 'Gedung FIK';
                const kap = r.kapasitas || 30;

                return `
                    <div onclick="selectP2ModalRuangan('${escapeHtml(nama)}', '${escapeHtml(kode)}', '${escapeHtml(lokasi)}', ${kap})" class="p-2.5 hover:bg-indigo-50 rounded-xl cursor-pointer flex items-center justify-between text-xs transition duration-150">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">${escapeHtml(nama)} ${kode ? `<span class="font-normal text-slate-400 font-mono text-[11px]">(${escapeHtml(kode)})</span>` : ''}</p>
                                <p class="text-[10px] text-slate-500 truncate"><i class="fa-solid fa-location-dot text-slate-400"></i> ${escapeHtml(lokasi)}</p>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5">
                            <span class="text-[10px] bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 rounded-full font-semibold">
                                <i class="fa-solid fa-users text-[9px]"></i> Kap: ${kap}
                            </span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        drop.classList.remove('hidden');
    };

    window.clearP2ModalRuanganSearch = function () {
        const input = document.getElementById('p2ModalSearchRuangan');
        const clearBtn = document.getElementById('p2ModalClearRuangan');
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        filterP2ModalRuangan('');
    };

    window.selectP2ModalRuangan = function (nama, kode, lokasi, kap) {
        const hiddenInput = document.getElementById('p2ModalRuangan');
        const chipName = document.getElementById('p2ModalChipRuanganName');
        const chipDetails = document.getElementById('p2ModalChipRuanganDetails');
        const chip = document.getElementById('p2ModalChipRuangan');
        const searchContainer = document.getElementById('p2ModalSearchRuanganContainer');
        const dropdown = document.getElementById('p2ModalDropdownRuangan');

        if (hiddenInput) hiddenInput.value = nama;
        if (chipName) chipName.textContent = `${nama} ${kode ? `(${kode})` : ''}`;
        if (chipDetails) chipDetails.textContent = `${lokasi || 'Gedung FIK'} • Kapasitas: ${kap || 30} orang`;

        if (searchContainer) searchContainer.classList.add('hidden');
        if (chip) chip.classList.remove('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    window.changeP2ModalRuangan = function () {
        const chip = document.getElementById('p2ModalChipRuangan');
        const searchContainer = document.getElementById('p2ModalSearchRuanganContainer');
        const searchInput = document.getElementById('p2ModalSearchRuangan');
        const clearBtn = document.getElementById('p2ModalClearRuangan');

        if (chip) chip.classList.add('hidden');
        if (searchContainer) searchContainer.classList.remove('hidden');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        openP2ModalRuanganList();
    };

    window.clearP2ModalRuangan = function () {
        const hiddenInput = document.getElementById('p2ModalRuangan');
        const chip = document.getElementById('p2ModalChipRuangan');
        const searchContainer = document.getElementById('p2ModalSearchRuanganContainer');
        const searchInput = document.getElementById('p2ModalSearchRuangan');
        const dropdown = document.getElementById('p2ModalDropdownRuangan');
        const clearBtn = document.getElementById('p2ModalClearRuangan');

        if (hiddenInput) hiddenInput.value = '';
        if (searchInput) searchInput.value = '';
        if (clearBtn) clearBtn.classList.add('hidden');

        if (searchContainer) searchContainer.classList.remove('hidden');
        if (chip) chip.classList.add('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    // =========================================================
    // FLATPICKR DATEPICKER ENGINE (TAHAP PREVIEW 2)
    // =========================================================
    let p2DatePickerInstance = null;

    function initP2DatePicker() {
        const input = document.getElementById('p2ModalTanggal');
        if (!input || typeof flatpickr === 'undefined') return;

        p2DatePickerInstance = flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'l, j F Y',
            locale: (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
            minDate: 'today',
            disableMobile: true
        });
    }

    // =========================================================
    // INTERACTIVE RADIAL ANALOG TIMEPICKER ENGINE (PREVIEW 2)
    // =========================================================
    let p2TpTarget = 'mulai'; // 'mulai' or 'selesai'
    let p2TpSelectedHour = 8;
    let p2TpSelectedMinute = 0;
    let p2TpIsHourMode = true; // true = hour mode, false = minute mode
    let p2IsDragging = false;

    function handleP2ClockEvent(e) {
        if (e.type === 'mousemove' && !p2IsDragging) return;
        if (e.type === 'touchmove' && !p2IsDragging) return;

        if (e.cancelable) e.preventDefault();

        let clientX = e.clientX;
        let clientY = e.clientY;

        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        }

        const clockContainer = document.getElementById('p2TpClockContainer');
        if (!clockContainer) return;

        const rect = clockContainer.getBoundingClientRect();
        const cx = rect.left + (rect.width / 2);
        const cy = rect.top + (rect.height / 2);
        const x = clientX - cx;
        const y = clientY - cy;

        let angle = Math.atan2(y, x) * (180 / Math.PI) + 90;
        if (angle < 0) angle += 360;

        const distance = Math.sqrt(x * x + y * y);

        if (p2TpIsHourMode) {
            let hour = Math.round(angle / 30);
            if (hour === 0) hour = 12;
            if (hour === 12 && angle > 345) hour = 12;

            // Inner circle for 24h mode (13-24/00)
            if (distance < 76) {
                hour += 12;
                if (hour === 24) hour = 0;
            }

            if (p2TpSelectedHour !== hour) {
                p2TpSelectedHour = hour;
                updateP2ClockDisplay();
            }
        } else {
            let minute = Math.round(angle / 6);
            if (minute === 60) minute = 0;

            if (p2TpSelectedMinute !== minute) {
                p2TpSelectedMinute = minute;
                updateP2ClockDisplay();
            }
        }
    }

    function initP2ClockEvents() {
        const clockContainer = document.getElementById('p2TpClockContainer');
        if (!clockContainer || clockContainer.dataset.initEvents) return;
        clockContainer.dataset.initEvents = 'true';

        // Mouse events
        clockContainer.addEventListener('mousedown', function (e) {
            p2IsDragging = true;
            handleP2ClockEvent(e);
        });

        document.addEventListener('mousemove', handleP2ClockEvent);

        document.addEventListener('mouseup', function (e) {
            if (p2IsDragging && p2TpIsHourMode) {
                setTimeout(() => setP2ClockMode('minute'), 180);
            }
            p2IsDragging = false;
        });

        // Touch events
        clockContainer.addEventListener('touchstart', function (e) {
            p2IsDragging = true;
            handleP2ClockEvent(e);
        }, { passive: false });

        document.addEventListener('touchmove', handleP2ClockEvent, { passive: false });

        document.addEventListener('touchend', function (e) {
            if (p2IsDragging && p2TpIsHourMode) {
                setTimeout(() => setP2ClockMode('minute'), 180);
            }
            p2IsDragging = false;
        });
    }

    window.openP2InlineTimePicker = function (target) {
        p2TpTarget = target;
        const panel = document.getElementById('p2InlineClockPanel');
        const label = document.getElementById('p2TpTargetLabel');
        const jamMulaiVal = document.getElementById('p2ModalJamMulai')?.value || '';
        const jamSelesaiVal = document.getElementById('p2ModalJamSelesai')?.value || '';

        if (target === 'mulai') {
            if (label) label.innerHTML = `<i class="fa-solid fa-hourglass-start"></i> PENGATURAN JAM MULAI SIDANG`;
            if (jamMulaiVal && jamMulaiVal.includes(':')) {
                const parts = jamMulaiVal.split(':');
                p2TpSelectedHour = parseInt(parts[0]) || 8;
                p2TpSelectedMinute = parseInt(parts[1]) || 0;
            } else {
                p2TpSelectedHour = 8;
                p2TpSelectedMinute = 0;
            }
        } else {
            if (label) label.innerHTML = `<i class="fa-solid fa-hourglass-end"></i> PENGATURAN JAM SELESAI SIDANG`;
            if (jamSelesaiVal && jamSelesaiVal.includes(':')) {
                const parts = jamSelesaiVal.split(':');
                p2TpSelectedHour = parseInt(parts[0]) || 9;
                p2TpSelectedMinute = parseInt(parts[1]) || 0;
            } else if (jamMulaiVal && jamMulaiVal.includes(':')) {
                const parts = jamMulaiVal.split(':');
                p2TpSelectedHour = (parseInt(parts[0]) || 8) + 1;
                p2TpSelectedMinute = (parseInt(parts[1]) || 0);
                if (p2TpSelectedHour >= 24) p2TpSelectedHour = 0;
            } else {
                p2TpSelectedHour = 9;
                p2TpSelectedMinute = 0;
            }
        }

        if (panel) panel.classList.remove('hidden');
        initP2ClockEvents();
        initP2SlotDragEvents();
        setP2ClockMode('hour');
        updateP2ClockDisplay();
    };

    window.closeP2InlineTimePicker = function () {
        const panel = document.getElementById('p2InlineClockPanel');
        if (panel) panel.classList.add('hidden');
    };

    window.setP2ClockMode = function (mode) {
        p2TpIsHourMode = (mode === 'hour');
        const tabHour = document.getElementById('p2TpTabHour');
        const tabMinute = document.getElementById('p2TpTabMinute');
        const dispHour = document.getElementById('p2TpDisplayHour');
        const dispMin = document.getElementById('p2TpDisplayMinute');

        if (tabHour) {
            if (p2TpIsHourMode) {
                tabHour.className = 'flex-1 py-1 px-2 rounded-md bg-indigo-600 text-white cursor-pointer shadow-2xs transition';
            } else {
                tabHour.className = 'flex-1 py-1 px-2 rounded-md text-slate-600 hover:text-slate-900 cursor-pointer transition';
            }
        }
        if (tabMinute) {
            if (!p2TpIsHourMode) {
                tabMinute.className = 'flex-1 py-1 px-2 rounded-md bg-indigo-600 text-white cursor-pointer shadow-2xs transition';
            } else {
                tabMinute.className = 'flex-1 py-1 px-2 rounded-md text-slate-600 hover:text-slate-900 cursor-pointer transition';
            }
        }

        if (dispHour) dispHour.className = p2TpIsHourMode ? 'cursor-pointer text-indigo-600 font-black px-1 rounded-lg bg-indigo-50' : 'cursor-pointer hover:text-indigo-600 px-1 rounded-lg text-slate-800';
        if (dispMin) dispMin.className = !p2TpIsHourMode ? 'cursor-pointer text-indigo-600 font-black px-1 rounded-lg bg-indigo-50' : 'cursor-pointer hover:text-indigo-600 px-1 rounded-lg text-slate-400';

        renderP2Clock();
    };

    window.setP2QuickSlot = function (start, end) {
        const inputMulai = document.getElementById('p2ModalJamMulai');
        const inputSelesai = document.getElementById('p2ModalJamSelesai');
        if (inputMulai) inputMulai.value = start;
        if (inputSelesai) inputSelesai.value = end;
        closeP2InlineTimePicker();
    };

    function updateP2ClockDisplay() {
        const hh = String(p2TpSelectedHour).padStart(2, '0');
        const mm = String(p2TpSelectedMinute).padStart(2, '0');
        const dispHour = document.getElementById('p2TpDisplayHour');
        const dispMin = document.getElementById('p2TpDisplayMinute');
        if (dispHour) dispHour.innerText = hh;
        if (dispMin) dispMin.innerText = mm;
        renderP2Clock();
    }

    function drawP2ClockNumber(container, val, isInner) {
        const el = document.createElement('div');
        el.className = 'p2-tp-clock-number ' + (isInner ? 'inner' : '');
        el.innerText = isInner && val === 0 ? '00' : (val === 0 && !p2TpIsHourMode ? '00' : String(val).padStart(isInner ? 2 : 1, '0'));

        const radius = isInner ? 58 : 92;
        const angleBase = p2TpIsHourMode ? (val % 12) * 30 : val * 6;
        const rad = (angleBase - 90) * (Math.PI / 180);

        const x = 120 + radius * Math.cos(rad);
        const y = 120 + radius * Math.sin(rad);

        el.style.left = x + 'px';
        el.style.top = y + 'px';

        let isActive = false;
        if (p2TpIsHourMode && p2TpSelectedHour === val) isActive = true;
        if (!p2TpIsHourMode && p2TpSelectedMinute === val) isActive = true;

        if (isActive) {
            el.classList.add('active');
        }

        el.addEventListener('click', (e) => {
            e.stopPropagation();
            if (p2TpIsHourMode) {
                p2TpSelectedHour = val;
                updateP2ClockDisplay();
                setTimeout(() => setP2ClockMode('minute'), 220);
            } else {
                p2TpSelectedMinute = val;
                updateP2ClockDisplay();
            }
        });

        container.appendChild(el);
    }

    function renderP2Clock() {
        const container = document.getElementById('p2TpClockNumbers');
        const hand = document.getElementById('p2TpClockHand');
        if (!container || !hand) return;

        container.innerHTML = '';

        if (p2TpIsHourMode) {
            // Hours: Outer (1-12), Inner (13-24/00)
            for (let i = 1; i <= 12; i++) {
                drawP2ClockNumber(container, i, false);
                drawP2ClockNumber(container, i === 12 ? 0 : i + 12, true);
            }
            const isInner = (p2TpSelectedHour > 12 || p2TpSelectedHour === 0);
            const val = p2TpSelectedHour;
            const angle = (val % 12) * 30;
            hand.style.height = isInner ? '58px' : '92px';
            hand.style.transform = `translate(-50%, 0) rotate(${angle}deg)`;
        } else {
            // Minutes: 0, 5, 10, ..., 55
            for (let i = 0; i < 60; i += 5) {
                drawP2ClockNumber(container, i, false);
            }
            if (p2TpSelectedMinute % 5 !== 0) {
                drawP2ClockNumber(container, p2TpSelectedMinute, false);
            }
            const angle = p2TpSelectedMinute * 6;
            hand.style.height = '92px';
            hand.style.transform = `translate(-50%, 0) rotate(${angle}deg)`;
        }
    }

    window.applyP2InlineTimePicker = function () {
        const hh = String(p2TpSelectedHour).padStart(2, '0');
        const mm = String(p2TpSelectedMinute).padStart(2, '0');
        const timeStr = `${hh}:${mm}`;

        if (p2TpTarget === 'mulai') {
            const input = document.getElementById('p2ModalJamMulai');
            if (input) input.value = timeStr;
        } else {
            const input = document.getElementById('p2ModalJamSelesai');
            if (input) input.value = timeStr;
        }

        closeP2InlineTimePicker();
    };

    // =========================================================
    // MULTI-SLOT DRAG SELECT ENGINE (TAHAP PREVIEW 2)
    // =========================================================
    let p2SlotIsDragging = false;
    let p2SlotDragStartIdx = -1;
    let p2SlotCurrentEndIdx = -1;

    function getP2Slots() {
        return Array.from(document.querySelectorAll('#p2TimeSlots .p2-tp-slot'));
    }

    function resetP2SlotStyle(slot) {
        slot.className = 'p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none';
    }

    function previewP2SlotStyle(slot) {
        slot.className = 'p2-tp-slot p-2 border border-indigo-400 rounded-lg text-center font-bold text-indigo-700 bg-indigo-50/90 cursor-pointer transition shadow-sm select-none';
    }

    function selectedP2SlotStyle(slot) {
        slot.className = 'p2-tp-slot p-2 border border-indigo-600 rounded-lg text-center font-bold text-white bg-indigo-600 cursor-pointer transition shadow-md shadow-indigo-600/30 select-none';
    }

    function updateP2SlotHighlight(minIdx, maxIdx) {
        getP2Slots().forEach((slot, i) => {
            if (i >= minIdx && i <= maxIdx) {
                previewP2SlotStyle(slot);
            } else {
                resetP2SlotStyle(slot);
            }
        });
    }

    function finalizeP2SlotSelection(minIdx, maxIdx) {
        const slots = getP2Slots();
        slots.forEach((slot, i) => {
            if (i >= minIdx && i <= maxIdx) {
                selectedP2SlotStyle(slot);
            } else {
                resetP2SlotStyle(slot);
            }
        });

        if (slots[minIdx] && slots[maxIdx]) {
            const startVal = slots[minIdx].getAttribute('data-start');
            const endVal = slots[maxIdx].getAttribute('data-end');

            const inputMulai = document.getElementById('p2ModalJamMulai');
            const inputSelesai = document.getElementById('p2ModalJamSelesai');
            if (inputMulai) inputMulai.value = startVal;
            if (inputSelesai) inputSelesai.value = endVal;

            setTimeout(() => {
                closeP2InlineTimePicker();
                slots.forEach(resetP2SlotStyle);
            }, 300);
        }
    }

    function initP2SlotDragEvents() {
        const container = document.getElementById('p2TimeSlots');
        if (!container || container.dataset.initEvents) return;
        container.dataset.initEvents = 'true';

        container.addEventListener('mousedown', (e) => {
            const slot = e.target.closest('.p2-tp-slot');
            if (!slot) return;
            e.preventDefault();
            p2SlotIsDragging = true;
            const slots = getP2Slots();
            p2SlotDragStartIdx = slots.indexOf(slot);
            p2SlotCurrentEndIdx = p2SlotDragStartIdx;
            updateP2SlotHighlight(p2SlotDragStartIdx, p2SlotDragStartIdx);
        });

        document.addEventListener('mouseover', (e) => {
            if (!p2SlotIsDragging) return;
            const slot = e.target.closest('.p2-tp-slot');
            if (!slot) return;
            const slots = getP2Slots();
            const idx = slots.indexOf(slot);
            if (idx === -1) return;
            p2SlotCurrentEndIdx = idx;
            const minIdx = Math.min(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            const maxIdx = Math.max(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            updateP2SlotHighlight(minIdx, maxIdx);
        });

        document.addEventListener('mouseup', () => {
            if (!p2SlotIsDragging) return;
            p2SlotIsDragging = false;
            const minIdx = Math.min(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            const maxIdx = Math.max(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            finalizeP2SlotSelection(minIdx, maxIdx);
        });

        // Touch support for dragging across slots on mobile / touch displays
        container.addEventListener('touchstart', (e) => {
            const slot = e.target.closest('.p2-tp-slot');
            if (!slot) return;
            p2SlotIsDragging = true;
            const slots = getP2Slots();
            p2SlotDragStartIdx = slots.indexOf(slot);
            p2SlotCurrentEndIdx = p2SlotDragStartIdx;
            updateP2SlotHighlight(p2SlotDragStartIdx, p2SlotDragStartIdx);
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!p2SlotIsDragging) return;
            const touch = e.touches[0];
            const el = document.elementFromPoint(touch.clientX, touch.clientY);
            if (!el) return;
            const slot = el.closest('.p2-tp-slot');
            if (!slot) return;
            const slots = getP2Slots();
            const idx = slots.indexOf(slot);
            if (idx === -1) return;
            p2SlotCurrentEndIdx = idx;
            const minIdx = Math.min(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            const maxIdx = Math.max(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            updateP2SlotHighlight(minIdx, maxIdx);
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (!p2SlotIsDragging) return;
            p2SlotIsDragging = false;
            const minIdx = Math.min(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            const maxIdx = Math.max(p2SlotDragStartIdx, p2SlotCurrentEndIdx);
            finalizeP2SlotSelection(minIdx, maxIdx);
        });
    }

    // Render Dosen Penguji Dropdown with Pembimbing / Penguji conflict prevention
    function renderP2ModalDosenDropdown(slotNum, kw) {
        const dropdown = document.getElementById(`p2ModalDropdownList${slotNum}`);
        if (!dropdown) return;

        const otherSlotNum = slotNum === 1 ? 2 : 1;
        const currentSlotNip = (slotNum === 1 ? state.p2SelectedDosen1?.nip : state.p2SelectedDosen2?.nip) || '';
        const otherSlotNip = (slotNum === 1 ? state.p2SelectedDosen2?.nip : state.p2SelectedDosen1?.nip) || '';

        const q = (kw || '').toLowerCase().trim();

        // 1. Get Pembimbing conflict data for current target student(s)
        let pembimbingNips = new Set();
        let conflictMap = {}; // nip -> array of student names

        if (state.p2TargetNim) {
            const mhs = state.p2List.find(m => m.nim === state.p2TargetNim);
            if (mhs) {
                if (mhs.pembimbing_1) {
                    pembimbingNips.add(mhs.pembimbing_1);
                    conflictMap[mhs.pembimbing_1] = ['Pembimbing 1'];
                }
                if (mhs.pembimbing_2) {
                    pembimbingNips.add(mhs.pembimbing_2);
                    conflictMap[mhs.pembimbing_2] = ['Pembimbing 2'];
                }
            }
        } else {
            state.p2SelectedStudents.forEach(s => {
                const m = state.p2List.find(x => x.nim === s.nim);
                if (m) {
                    if (m.pembimbing_1) {
                        pembimbingNips.add(m.pembimbing_1);
                        if (!conflictMap[m.pembimbing_1]) conflictMap[m.pembimbing_1] = [];
                        conflictMap[m.pembimbing_1].push(s.name);
                    }
                    if (m.pembimbing_2) {
                        pembimbingNips.add(m.pembimbing_2);
                        if (!conflictMap[m.pembimbing_2]) conflictMap[m.pembimbing_2] = [];
                        conflictMap[m.pembimbing_2].push(s.name);
                    }
                }
            });
        }

        const filtered = (cfg.dosenList || []).filter(d => {
            if (!q) return true;
            return (d.nama_dosen || '').toLowerCase().includes(q) || (d.nip || '').toLowerCase().includes(q);
        });

        if (filtered.length === 0) {
            dropdown.innerHTML = `
                <div class="p-4 text-center text-slate-400 text-xs font-medium">
                    <i class="fa-solid fa-user-slash text-slate-300 text-xl block mb-1"></i>
                    Dosen "${escapeHtml(kw)}" tidak ditemukan.
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(d => {
            const isSelected = (currentSlotNip === d.nip);
            const isUsedByOther = (otherSlotNip && otherSlotNip === d.nip);
            const isPembimbing = pembimbingNips.has(d.nip);

            let itemClass = 'p-2.5 rounded-xl flex items-center justify-between text-xs transition duration-150 ';
            let clickHandler = '';
            let initials = (d.nama_dosen || 'D').split(' ').map(w => w[0]).filter(Boolean).slice(0, 2).join('').toUpperCase();

            if (isPembimbing) {
                itemClass += 'bg-rose-50/70 opacity-60 cursor-not-allowed border border-rose-100';
                const tagText = state.p2TargetNim ? (conflictMap[d.nip]?.[0] || 'Dosen Pembimbing') : `Pembimbing (${conflictMap[d.nip]?.length || 1} Mhs)`;
                clickHandler = `onclick="Swal.fire({icon: 'warning', title: 'Dosen Pembimbing Tidak Boleh Jadi Penguji', text: 'Dosen ini (${escapeHtml(d.nama_dosen)}) bertindak sebagai Dosen Pembimbing untuk mahasiswa terkait, sehingga secara akademik tidak dapat ditugaskan sebagai Dosen Penguji.', confirmButtonColor: '#4f46e5'})"`;

                html += `
                    <div class="${itemClass}" ${clickHandler} title="Dosen Pembimbing tidak boleh menjadi Dosen Penguji">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-[11px] shrink-0">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-700 truncate">${escapeHtml(d.nama_dosen)}</p>
                                <p class="text-[10px] text-slate-400 font-mono">NIP: ${d.nip}</p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <span class="text-[10px] bg-rose-100 text-rose-700 border border-rose-200 px-2 py-0.5 rounded-full font-bold flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> ${tagText}
                            </span>
                        </div>
                    </div>
                `;
            } else if (isUsedByOther) {
                itemClass += 'bg-slate-50 opacity-60 cursor-not-allowed';
                clickHandler = `onclick="Swal.fire({icon: 'info', title: 'Sudah Dipilih', text: 'Dosen ini sudah dipilih sebagai Dosen Penguji ${otherSlotNum}.', confirmButtonColor: '#4f46e5'})"`;

                html += `
                    <div class="${itemClass}" ${clickHandler}>
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[11px] shrink-0">
                                ${initials}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-700 truncate">${escapeHtml(d.nama_dosen)}</p>
                                <p class="text-[10px] text-slate-400 font-mono">NIP: ${d.nip}</p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded font-semibold">Dipilih sbg Penguji ${otherSlotNum}</span>
                        </div>
                    </div>
                `;
            } else {
                itemClass += isSelected 
                    ? 'bg-indigo-50 border border-indigo-200 text-indigo-950 font-bold' 
                    : 'hover:bg-indigo-50 text-slate-700 cursor-pointer';
                clickHandler = `onclick="selectP2ModalDosen(${slotNum}, '${d.nip}', '${escapeHtml(d.nama_dosen)}')"`;

                html += `
                    <div class="${itemClass}" ${clickHandler}>
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg ${isSelected ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'} flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                                ${initials}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">${escapeHtml(d.nama_dosen)}</p>
                                <p class="text-[10px] text-slate-400 font-mono">NIP: ${d.nip}</p>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            ${isSelected ? '<i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>' : '<i class="fa-solid fa-plus text-slate-300 hover:text-indigo-600 text-xs"></i>'}
                        </div>
                    </div>
                `;
            }
        });

        dropdown.innerHTML = html;
    }

    window.openP2ModalDosenList = function (slot) {
        const otherSlot = slot === 1 ? 2 : 1;
        const otherDropdown = document.getElementById(`p2ModalDropdownList${otherSlot}`);
        if (otherDropdown) otherDropdown.classList.add('hidden');

        const dropdown = document.getElementById(`p2ModalDropdownList${slot}`);
        const input = document.getElementById(`p2ModalSearchPenguji${slot}`);
        if (dropdown) {
            renderP2ModalDosenDropdown(slot, input ? input.value : '');
            dropdown.classList.remove('hidden');
        }
    };

    window.filterP2ModalDosen = function (slot, query) {
        const input = document.getElementById(`p2ModalSearchPenguji${slot}`);
        const clearBtn = document.getElementById(`p2ModalClear${slot}`);
        const kw = (typeof query === 'string' ? query : (input ? input.value : ''));

        if (clearBtn) {
            if (kw) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        renderP2ModalDosenDropdown(slot, kw);
    };

    window.clearP2ModalSearch = function (slot) {
        const input = document.getElementById(`p2ModalSearchPenguji${slot}`);
        const clearBtn = document.getElementById(`p2ModalClear${slot}`);
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        renderP2ModalDosenDropdown(slot, '');
    };

    window.changeP2ModalDosen = function (slot) {
        const searchContainer = document.getElementById(`p2ModalSearchContainer${slot}`);
        const searchInput = document.getElementById(`p2ModalSearchPenguji${slot}`);
        const chip = document.getElementById(`p2ModalChipPenguji${slot}`);
        const clearBtn = document.getElementById(`p2ModalClear${slot}`);

        if (chip) chip.classList.add('hidden');
        if (searchContainer) searchContainer.classList.remove('hidden');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        openP2ModalDosenList(slot);
    };

    window.selectP2ModalDosen = function (slot, nip, customNama) {
        const dosen = getDosenByNip(nip) || { nip: nip, nama_dosen: customNama || nip };
        const nama = dosen.nama_dosen || customNama || nip;

        // Check if supervisor conflict
        if (state.p2TargetNim) {
            const mhs = state.p2List.find(m => m.nim === state.p2TargetNim);
            if (mhs && (mhs.pembimbing_1 === nip || mhs.pembimbing_2 === nip)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dosen Pembimbing Dipilih',
                    text: `Dosen ${nama} adalah Dosen Pembimbing untuk mahasiswa ini. Dosen Pembimbing tidak boleh menjadi Dosen Penguji!`,
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        }

        if (slot === 1) state.p2SelectedDosen1 = { nip, nama };
        else state.p2SelectedDosen2 = { nip, nama };

        const hiddenInput = document.getElementById(`p2ModalInputPenguji${slot}`);
        const chipText = document.getElementById(`p2ModalChipPenguji${slot}Text`);
        const searchContainer = document.getElementById(`p2ModalSearchContainer${slot}`);
        const chip = document.getElementById(`p2ModalChipPenguji${slot}`);
        const dropdown = document.getElementById(`p2ModalDropdownList${slot}`);

        if (hiddenInput) hiddenInput.value = nip;
        if (chipText) chipText.textContent = `${nama} (${nip})`;

        if (searchContainer) searchContainer.classList.add('hidden');
        if (chip) chip.classList.remove('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    window.clearP2ModalDosen = function (slot) {
        if (slot === 1) state.p2SelectedDosen1 = null;
        else state.p2SelectedDosen2 = null;

        const hiddenInput = document.getElementById(`p2ModalInputPenguji${slot}`);
        const searchInput = document.getElementById(`p2ModalSearchPenguji${slot}`);
        const clearBtn = document.getElementById(`p2ModalClear${slot}`);
        const searchContainer = document.getElementById(`p2ModalSearchContainer${slot}`);
        const chip = document.getElementById(`p2ModalChipPenguji${slot}`);
        const dropdown = document.getElementById(`p2ModalDropdownList${slot}`);

        if (hiddenInput) hiddenInput.value = '';
        if (searchInput) searchInput.value = '';
        if (clearBtn) clearBtn.classList.add('hidden');

        if (searchContainer) searchContainer.classList.remove('hidden');
        if (chip) chip.classList.add('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    window.submitP2Plotting = function (e) {
        e.preventDefault();

        const p1 = document.getElementById('p2ModalInputPenguji1') ? document.getElementById('p2ModalInputPenguji1').value : '';
        const p2 = document.getElementById('p2ModalInputPenguji2') ? document.getElementById('p2ModalInputPenguji2').value : '';

        if (!p1 || !p2) {
            Swal.fire({
                icon: 'warning',
                title: 'Dosen Penguji Belum Lengkap',
                text: 'Dosen Penguji 1 dan Dosen Penguji 2 wajib dipilih!',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        if (p1 === p2) {
            Swal.fire({
                icon: 'error',
                title: 'Dosen Penguji Sama',
                text: 'Dosen Penguji 1 dan Dosen Penguji 2 tidak boleh orang yang sama!',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        // Supervisor conflict validation on submit
        if (state.p2TargetNim) {
            const mhs = state.p2List.find(m => m.nim === state.p2TargetNim);
            if (mhs) {
                if (p1 === mhs.pembimbing_1 || p1 === mhs.pembimbing_2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Konflik Dosen Pembimbing',
                        text: 'Dosen Penguji 1 tidak boleh sama dengan Dosen Pembimbing mahasiswa!',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }
                if (p2 === mhs.pembimbing_1 || p2 === mhs.pembimbing_2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Konflik Dosen Pembimbing',
                        text: 'Dosen Penguji 2 tidak boleh sama dengan Dosen Pembimbing mahasiswa!',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }
            }
        } else {
            // Batch mode check
            for (let s of state.p2SelectedStudents.values()) {
                const mhs = state.p2List.find(m => m.nim === s.nim);
                if (mhs) {
                    if (p1 === mhs.pembimbing_1 || p1 === mhs.pembimbing_2) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Konflik Dosen Pembimbing',
                            text: `Dosen Penguji 1 bertindak sebagai Dosen Pembimbing untuk mahasiswa ${s.name} (${s.nim}). Dosen Pembimbing tidak boleh menjadi Penguji!`,
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }
                    if (p2 === mhs.pembimbing_1 || p2 === mhs.pembimbing_2) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Konflik Dosen Pembimbing',
                            text: `Dosen Penguji 2 bertindak sebagai Dosen Pembimbing untuk mahasiswa ${s.name} (${s.nim}). Dosen Pembimbing tidak boleh menjadi Penguji!`,
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }
                }
            }
        }

        const isSingle = (state.p2TargetNim !== null);
        const url = isSingle ? cfg.ajaxPreview2UpdateUrl : cfg.ajaxPreview2BatchUrl;
        const btnSubmit = document.getElementById('modalP2BtnSubmit');
        const btnText = document.getElementById('modalP2BtnSubmitText');

        if (btnSubmit) btnSubmit.disabled = true;
        if (btnText) btnText.textContent = 'Menyimpan...';

        const formData = new FormData();
        if (isSingle) {
            formData.append('nim', state.p2TargetNim);
        } else {
            formData.append('nims', JSON.stringify(Array.from(state.p2SelectedStudents.keys())));
        }
        formData.append('penguji_1', p1);
        formData.append('penguji_2', p2);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res && res.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Penetapan Dosen Penguji berhasil disimpan!',
                    confirmButtonColor: '#4f46e5'
                });
                closeP2Modal();

                if (cfg.ajaxPreview2RealtimeUrl) {
                    fetch(cfg.ajaxPreview2RealtimeUrl)
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.data) {
                                state.p2List = data.data;
                                if (data.stats) {
                                    const t = document.getElementById('statP2Total');
                                    const tr = document.getElementById('statP2Terjadwal');
                                    const ps = document.getElementById('statP2Penguji');
                                    const bl = document.getElementById('statP2Belum');
                                    if (t) t.textContent = data.stats.total;
                                    if (tr) {
                                        const pct = data.stats.total > 0 ? Math.round((data.stats.terjadwal / data.stats.total) * 100) : 0;
                                        tr.innerHTML = `${data.stats.terjadwal} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                    }
                                    if (ps) ps.textContent = data.stats.penguji_set;
                                    if (bl) {
                                        const pct = data.stats.total > 0 ? Math.round((data.stats.belum_set / data.stats.total) * 100) : 0;
                                        bl.innerHTML = `${data.stats.belum_set} <span class="text-xs font-semibold text-amber-600 font-normal">(${pct}%)</span>`;
                                    }
                                }
                                clearAllP2Selection();
                                renderP2Table();
                            }
                        });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: res.message || 'Terjadi kesalahan saat menyimpan ke database.',
                    confirmButtonColor: '#4f46e5'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error Jaringan',
                text: 'Terjadi kegagalan komunikasi ke server.',
                confirmButtonColor: '#4f46e5'
            });
        })
        .finally(() => {
            if (btnSubmit) btnSubmit.disabled = false;
            if (btnText) btnText.textContent = 'Simpan Penetapan Penguji & Jadwal';
        });
    };

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#multiSearchWrapper') && !e.target.closest('#p2MultiSearchWrapper')) {
            closeAllCustomDropdowns();
        }
        const activeWrapper = e.target.closest('.modal-combobox-wrapper');
        const activeSlot = activeWrapper ? (activeWrapper.dataset.slot || activeWrapper.dataset.p2Slot) : null;

        [1, 2].forEach(s => {
            if (String(s) !== String(activeSlot)) {
                const drop = document.getElementById(`modalDropdownList${s}`);
                if (drop) drop.classList.add('hidden');
                const p2Drop = document.getElementById(`p2ModalDropdownList${s}`);
                if (p2Drop) p2Drop.classList.add('hidden');
            }
        });

        if (activeSlot !== 'ruangan') {
            const dropRuangan = document.getElementById('p2ModalDropdownRuangan');
            if (dropRuangan) dropRuangan.classList.add('hidden');
        }

        if (!e.target.closest('#singleRuanganCombobox')) {
            closeRuanganDropdown('single');
        }
        if (!e.target.closest('#batchRuanganCombobox')) {
            closeRuanganDropdown('batch');
        }

        if (!e.target.closest('[id^="q_wrapper_"]') && !e.target.closest('[id^="q_dropdown_"]')) {
            document.querySelectorAll('[id^="q_dropdown_"]').forEach(d => d.classList.add('hidden'));
            document.querySelectorAll('[id^="quick_card_"]').forEach(c => c.style.zIndex = '10');
        }

        if (!e.target.closest('[id^="p1_search_container_"]') && !e.target.closest('[id^="p1_dropdown_"]')) {
            document.querySelectorAll('[id^="p1_dropdown_"]').forEach(d => d.classList.add('hidden'));
        }
    });

    // =========================================================
    // 7. REALTIME BACKGROUND SYNC (AJAX POLLING)
    // =========================================================
    let realtimeTimer = null;
    let isSyncing = false;

    function startRealtimeSync() {
        if (!cfg.ajaxRealtimeUrl && !cfg.ajaxPreview2RealtimeUrl) return;

        realtimeTimer = setInterval(() => {
            const isModalOpen = (!document.getElementById('batchApprovalModal')?.classList.contains('hidden')) || 
                                (!document.getElementById('p1BatchReviewModal')?.classList.contains('hidden')) || 
                                (!document.getElementById('modalPreview2Plotting')?.classList.contains('hidden'));
            const isInputFocused = document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA');

            if (isModalOpen || isInputFocused || isSyncing) return;

            isSyncing = true;

            // Sync Pendaftaran TA
            if (cfg.ajaxRealtimeUrl && state.activeTab === 'pendaftaran') {
                fetch(cfg.ajaxRealtimeUrl)
                    .then(res => res.json())
                    .then(res => {
                        if (res && res.status && res.data) {
                            const oldJson = JSON.stringify(state.list);
                            const newJson = JSON.stringify(res.data);

                            if (oldJson !== newJson) {
                                state.list = res.data;

                                if (res.stats) {
                                    const totalEl = document.getElementById('statTotalCount');
                                    const pendEl = document.getElementById('statPendingCount');
                                    const appEl = document.getElementById('statApprovedCount');
                                    const rejEl = document.getElementById('statRejectedCount');

                                    if (totalEl) totalEl.textContent = res.stats.total;
                                    if (pendEl) {
                                        const pct = res.stats.total > 0 ? Math.round((res.stats.pending / res.stats.total) * 100) : 0;
                                        pendEl.innerHTML = `${res.stats.pending} <span class="text-xs font-semibold text-cyan-600 font-normal">(${pct}%)</span>`;
                                    }
                                    if (appEl) {
                                        const pct = res.stats.total > 0 ? Math.round((res.stats.approved / res.stats.total) * 100) : 0;
                                        appEl.innerHTML = `${res.stats.approved} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                    }
                                    if (rejEl) rejEl.textContent = res.stats.rejected;
                                }

                                renderTable();
                            }
                        }
                    })
                    .catch(err => console.debug('Realtime sync silent error:', err))
                    .finally(() => {
                        isSyncing = false;
                    });
            } else if (cfg.ajaxPreview2RealtimeUrl && state.activeTab === 'preview2') {
                fetch(cfg.ajaxPreview2RealtimeUrl)
                    .then(res => res.json())
                    .then(res => {
                        if (res && res.status && res.data) {
                            const oldJson = JSON.stringify(state.p2List);
                            const newJson = JSON.stringify(res.data);

                            if (oldJson !== newJson) {
                                state.p2List = res.data;

                                if (res.stats) {
                                    const t = document.getElementById('statP2Total');
                                    const tr = document.getElementById('statP2Terjadwal');
                                    const ps = document.getElementById('statP2Penguji');
                                    const bl = document.getElementById('statP2Belum');

                                    if (t) t.textContent = res.stats.total;
                                    if (tr) {
                                        const pct = res.stats.total > 0 ? Math.round((res.stats.terjadwal / res.stats.total) * 100) : 0;
                                        tr.innerHTML = `${res.stats.terjadwal} <span class="text-xs font-semibold text-emerald-600 font-normal">(${pct}%)</span>`;
                                    }
                                    if (ps) ps.textContent = res.stats.penguji_set;
                                    if (bl) {
                                        const pct = res.stats.total > 0 ? Math.round((res.stats.belum_set / res.stats.total) * 100) : 0;
                                        bl.innerHTML = `${res.stats.belum_set} <span class="text-xs font-semibold text-amber-600 font-normal">(${pct}%)</span>`;
                                    }
                                }

                                renderP2Table();
                            }
                        }
                    })
                    .catch(err => console.debug('Realtime P2 sync error:', err))
                    .finally(() => {
                        isSyncing = false;
                    });
            } else if (cfg.ajaxSidangRealtimeUrl && state.activeTab === 'sidang') {
                fetch(cfg.ajaxSidangRealtimeUrl)
                    .then(res => res.json())
                    .then(res => {
                        if (res && res.status && res.data) {
                            const oldJson = JSON.stringify(state.sidangList);
                            const newJson = JSON.stringify(res.data);

                            if (oldJson !== newJson) {
                                state.sidangList = res.data;
                                if (res.ruangan) {
                                    state.ruanganList = res.ruangan;
                                    populateRuanganDropdowns();
                                }

                                if (res.stats) {
                                    const stTot = document.getElementById('statSidangTotal');
                                    const stTr = document.getElementById('statSidangTerjadwal');
                                    const stBl = document.getElementById('statSidangBelumSet');
                                    const stRq = document.getElementById('statSidangRuanganCount');
                                    if (stTot) stTot.textContent = res.stats.total;
                                    if (stTr) stTr.textContent = res.stats.terjadwal;
                                    if (stBl) stBl.textContent = res.stats.belum_set;
                                    if (stRq) stRq.textContent = res.stats.ruangan_cnt;
                                }

                                renderSidangTable();
                            }
                        }
                    })
                    .catch(err => console.debug('Realtime Sidang sync error:', err))
                    .finally(() => {
                        isSyncing = false;
                    });
            } else {
                isSyncing = false;
            }
        }, 4000);
    }
    // =========================================================
    // 7. TAHAP 3: PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN
    // =========================================================

    // =========================================================
    // 7. TAHAP 3: PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN
    // =========================================================

    function isTextSidangCategory(cat) {
        return ['query', 'nama', 'nim', 'judul', 'pembimbing', 'penguji', 'ruangan'].includes(cat);
    }

    function getPlaceholderForSidangCategory(cat) {
        switch (cat) {
            case 'nama': return 'Ketik nama mahasiswa sidang...';
            case 'nim': return 'Ketik NIM mahasiswa sidang...';
            case 'judul': return 'Ketik kata kunci judul tugas akhir...';
            case 'pembimbing': return 'Ketik nama dosen pembimbing...';
            case 'penguji': return 'Ketik nama dosen penguji...';
            case 'ruangan': return 'Ketik nama atau kode ruangan sidang...';
            default: return 'Cari Nama, NIM, Judul TA, Pembimbing, Penguji, Ruangan...';
        }
    }

    window.selectSidangMainCategory = function (cat, label, el) {
        const catSelect = document.getElementById('sidangMainCategorySelect');
        if (catSelect) catSelect.value = cat;
        const catLabel = document.getElementById('label-filter-sidang-main-cat');
        if (catLabel) catLabel.innerText = label;

        const textWrap = document.getElementById('sidangMainValueContainer');
        const selectWrap = document.getElementById('sidangMainCustomSelectWrap');
        const inputEl = document.getElementById('sidangMainSearchInput');

        if (isTextSidangCategory(cat)) {
            if (textWrap) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.add('hidden');
            if (inputEl) {
                inputEl.placeholder = getPlaceholderForSidangCategory(cat);
                inputEl.focus();
            }
        } else {
            if (textWrap) {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.remove('hidden');
            updateSidangMainValueOptions(cat);
        }

        closeAllCustomDropdowns();
    };

    function updateSidangMainValueOptions(cat) {
        const menu = document.getElementById('menu-filter-sidang-main-select');
        const label = document.getElementById('label-filter-sidang-main-select');
        const valInput = document.getElementById('sidangMainCustomSelectVal');
        if (valInput) valInput.value = '';

        let html = '';
        if (cat === 'status') {
            if (label) label.innerText = 'Semua Status Sidang';
            html = `
                <div onclick="selectSidangMainVal('', 'Semua Status Sidang', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-amber-50 text-amber-600"><span>Semua Status Sidang</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectSidangMainVal('Terjadwal', 'Terjadwal', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terjadwal</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectSidangMainVal('Belum Dijadwalkan', 'Belum Dijadwalkan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Belum Dijadwalkan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        }
        if (menu) menu.innerHTML = html;
    }

    window.selectSidangMainVal = function (val, labelText, el) {
        const valInput = document.getElementById('sidangMainCustomSelectVal');
        const label = document.getElementById('label-filter-sidang-main-select');
        if (valInput) valInput.value = val;
        if (label) label.innerText = labelText;
        closeAllCustomDropdowns();
    };

    window.toggleOrAddFilterRowSidang = function (e) {
        if (e) e.stopPropagation();
        const card = document.getElementById('extraRowsCardSidang');
        if (!card) return;

        if (card.classList.contains('active')) {
            card.classList.remove('active');
        } else {
            const container = document.getElementById('additionalFilterRowsContainerSidang');
            if (container && container.children.length === 0) {
                addNewFilterRowSidang();
            }
            card.classList.add('active');
        }
    };

    function updateSidangFilterBadge() {
        const rows = document.querySelectorAll('.extra-filter-row-sidang');
        const total = rows.length + 1;
        const badge = document.getElementById('filterCountBadgeSidang');
        if (badge) badge.innerText = `${total}/4`;

        const addBtn = document.getElementById('standaloneAddBtnSidang');
        if (addBtn) {
            if (total >= 4) addBtn.classList.add('opacity-50', 'pointer-events-none');
            else addBtn.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    window.addNewFilterRowSidang = function () {
        const container = document.getElementById('additionalFilterRowsContainerSidang');
        if (!container) return;

        const currentRows = container.querySelectorAll('.extra-filter-row-sidang');
        if (currentRows.length >= 3) {
            Swal.fire({ icon: 'info', title: 'Batas Filter Tercapai', text: 'Maksimal 4 kriteria filter pencarian dapat aktif sekaligus.', timer: 2000, showConfirmButton: false });
            return;
        }

        const rowId = Date.now();
        const rowDiv = document.createElement('div');
        rowDiv.id = `extraSidangRow_${rowId}`;
        rowDiv.className = 'extra-filter-row extra-filter-row-sidang flex items-center gap-2 p-1.5 bg-slate-50 border border-slate-200 rounded-xl shadow-2xs text-xs animate-in fade-in duration-200';

        rowDiv.innerHTML = `
            <div class="relative custom-dropdown-container">
                <input type="hidden" id="extraSidangCatSelect_${rowId}" value="nama">
                <button type="button" onclick="toggleCustomDropdown('extra-sidang-cat-${rowId}', event)" class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-lg py-1 px-2 text-xs font-bold text-slate-800 hover:border-amber-400 focus:outline-none shadow-2xs">
                    <span id="label-filter-extra-sidang-cat-${rowId}" class="truncate max-w-[120px]">🏷️ Nama Mahasiswa</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-sidang-cat-${rowId}"></i>
                </button>
                <div id="menu-filter-extra-sidang-cat-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-1 w-48 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                    <div onclick="selectSidangExtraCategory(${rowId}, 'nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>🏷️ Nama Mahasiswa</span></div>
                    <div onclick="selectSidangExtraCategory(${rowId}, 'nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>🆔 NIM Mahasiswa</span></div>
                    <div onclick="selectSidangExtraCategory(${rowId}, 'judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>📖 Judul Tugas Akhir</span></div>
                    <div onclick="selectSidangExtraCategory(${rowId}, 'pembimbing', '👔 Dosen Pembimbing', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>👔 Dosen Pembimbing</span></div>
                    <div onclick="selectSidangExtraCategory(${rowId}, 'penguji', '👨‍🏫 Dosen Penguji', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>👨‍🏫 Dosen Penguji</span></div>
                    <div onclick="selectSidangExtraCategory(${rowId}, 'ruangan', '🏛️ Ruangan Sidang', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>🏛️ Ruangan Sidang</span></div>
                    <div onclick="selectSidangExtraCategory(${rowId}, 'status', '⚡ Status Sidang', this)" class="dropdown-item px-3 py-1.5 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>⚡ Status Sidang</span></div>
                </div>
            </div>

            <div id="extraSidangValueContainer_${rowId}" class="flex-1 flex items-center bg-white border border-slate-200 rounded-lg px-2 py-1 shadow-2xs">
                <input type="text" id="extraSidangInput_${rowId}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handleUnifiedMultiSearchSidang(); }" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." class="w-full text-xs bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
            </div>

            <div id="extraSidangCustomSelectWrap_${rowId}" class="hidden flex-1 relative custom-dropdown-container">
                <input type="hidden" id="extraSidangValueVal_${rowId}" value="">
                <button type="button" onclick="toggleCustomDropdown('extra-sidang-val-${rowId}', event)" class="w-full bg-white border border-slate-200 rounded-lg py-1 px-2 text-xs font-semibold text-slate-800 flex items-center justify-between shadow-2xs">
                    <span id="label-filter-extra-sidang-val-${rowId}" class="truncate">Semua Status</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-sidang-val-${rowId}"></i>
                </button>
                <div id="menu-filter-extra-sidang-val-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                </div>
            </div>

            <button type="button" onclick="removeFilterRowSidang(${rowId})" class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer shrink-0" title="Hapus kriteria ini">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        `;

        container.appendChild(rowDiv);
        updateSidangFilterBadge();
    };

    window.selectSidangExtraCategory = function (rowId, cat, label, el) {
        const catSelect = document.getElementById(`extraSidangCatSelect_${rowId}`);
        if (catSelect) catSelect.value = cat;
        const catLabel = document.getElementById(`label-filter-extra-sidang-cat-${rowId}`);
        if (catLabel) catLabel.innerText = label;

        const textWrap = document.getElementById(`extraSidangValueContainer_${rowId}`);
        const selectWrap = document.getElementById(`extraSidangCustomSelectWrap_${rowId}`);
        const inputEl = document.getElementById(`extraSidangInput_${rowId}`);

        if (isTextSidangCategory(cat)) {
            if (textWrap) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.add('hidden');
            if (inputEl) inputEl.placeholder = getPlaceholderForSidangCategory(cat);
        } else {
            if (textWrap) {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.remove('hidden');
            updateSidangExtraValueOptions(rowId, cat);
        }

        closeAllCustomDropdowns();
    };

    function updateSidangExtraValueOptions(rowId, cat) {
        const menu = document.getElementById(`menu-filter-extra-sidang-val-${rowId}`);
        const label = document.getElementById(`label-filter-extra-sidang-val-${rowId}`);
        const valInput = document.getElementById(`extraSidangValueVal_${rowId}`);
        if (valInput) valInput.value = '';

        let html = '';
        if (cat === 'status') {
            if (label) label.innerText = 'Semua Status Sidang';
            html = `
                <div onclick="selectSidangExtraVal(${rowId}, '', 'Semua Status Sidang', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-amber-50 text-amber-600"><span>Semua Status Sidang</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectSidangExtraVal(${rowId}, 'Terjadwal', 'Terjadwal', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terjadwal</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectSidangExtraVal(${rowId}, 'Belum Dijadwalkan', 'Belum Dijadwalkan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Belum Dijadwalkan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        }
        if (menu) menu.innerHTML = html;
    }

    window.selectSidangExtraVal = function (rowId, val, labelText, el) {
        const valInput = document.getElementById(`extraSidangValueVal_${rowId}`);
        const label = document.getElementById(`label-filter-extra-sidang-val-${rowId}`);
        if (valInput) valInput.value = val;
        if (label) label.innerText = labelText;
        closeAllCustomDropdowns();
    };

    window.removeFilterRowSidang = function (rowId) {
        const row = document.getElementById(`extraSidangRow_${rowId}`);
        if (row) {
            row.remove();
            updateSidangFilterBadge();
            handleUnifiedMultiSearchSidang();
        }
    };

    window.resetSidangMultiSearch = function () {
        const catSelect = document.getElementById('sidangMainCategorySelect');
        if (catSelect) catSelect.value = 'query';
        const catLabel = document.getElementById('label-filter-sidang-main-cat');
        if (catLabel) catLabel.innerText = 'Cari Kata Kunci';

        const mainInput = document.getElementById('sidangMainSearchInput');
        if (mainInput) {
            mainInput.value = '';
            mainInput.placeholder = 'Cari Nama, NIM, Judul TA, Pembimbing, Penguji, Ruangan...';
        }

        const textWrap = document.getElementById('sidangMainValueContainer');
        const selectWrap = document.getElementById('sidangMainCustomSelectWrap');
        if (textWrap) {
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
        }
        if (selectWrap) selectWrap.classList.add('hidden');

        const mainVal = document.getElementById('sidangMainCustomSelectVal');
        if (mainVal) mainVal.value = '';

        const container = document.getElementById('additionalFilterRowsContainerSidang');
        if (container) container.innerHTML = '';

        const card = document.getElementById('extraRowsCardSidang');
        if (card) card.classList.remove('active');

        updateSidangFilterBadge();
        closeAllCustomDropdowns();
        handleUnifiedMultiSearchSidang();
    };

    function getActiveSidangFilterCriteria() {
        const criteria = [];
        const mainCat = document.getElementById('sidangMainCategorySelect') ? document.getElementById('sidangMainCategorySelect').value : 'query';
        let mainVal = '';

        if (isTextSidangCategory(mainCat)) {
            mainVal = document.getElementById('sidangMainSearchInput') ? document.getElementById('sidangMainSearchInput').value.trim() : '';
        } else {
            mainVal = document.getElementById('sidangMainCustomSelectVal') ? document.getElementById('sidangMainCustomSelectVal').value : '';
        }
        if (mainVal) criteria.push({ type: mainCat, val: mainVal });

        document.querySelectorAll('.extra-filter-row-sidang').forEach(row => {
            const rowId = row.id.replace('extraSidangRow_', '');
            const cat = document.getElementById('extraSidangCatSelect_' + rowId) ? document.getElementById('extraSidangCatSelect_' + rowId).value : 'query';
            let val = '';
            if (isTextSidangCategory(cat)) {
                val = document.getElementById('extraSidangInput_' + rowId) ? document.getElementById('extraSidangInput_' + rowId).value.trim() : '';
            } else {
                val = document.getElementById('extraSidangValueVal_' + rowId) ? document.getElementById('extraSidangValueVal_' + rowId).value : '';
            }
            if (val) criteria.push({ type: cat, val: val });
        });

        return criteria;
    }

    function getFilteredSidangData() {
        let result = state.sidangList || [];
        const criteria = getActiveSidangFilterCriteria();

        if (criteria.length === 0) return result;

        return result.filter(item => {
            return criteria.every(crit => {
                const term = (crit.val || '').toLowerCase().trim();
                if (!term) return true;

                switch (crit.type) {
                    case 'query': {
                        const nim = (item.nim || '').toLowerCase();
                        const nama = (item.nama_lengkap || item.nama || '').toLowerCase();
                        const judul = (item.judul_1 || '').toLowerCase();
                        const ruang = (item.ruangan_sidang || item.detail_nama_ruangan || '').toLowerCase();
                        const pemb1 = (item.nama_pembimbing_1 || '').toLowerCase();
                        const pemb2 = (item.nama_pembimbing_2 || '').toLowerCase();
                        const peng1 = (item.nama_penguji_1 || '').toLowerCase();
                        const peng2 = (item.nama_penguji_2 || '').toLowerCase();
                        return nim.includes(term) || nama.includes(term) || judul.includes(term) || ruang.includes(term) || pemb1.includes(term) || pemb2.includes(term) || peng1.includes(term) || peng2.includes(term);
                    }
                    case 'nama':
                        return (item.nama_lengkap || item.nama || '').toLowerCase().includes(term);
                    case 'nim':
                        return (item.nim || '').toLowerCase().includes(term);
                    case 'judul':
                        return (item.judul_1 || '').toLowerCase().includes(term);
                    case 'pembimbing': {
                        const p1 = (item.nama_pembimbing_1 || item.pembimbing_1 || '').toLowerCase();
                        const p2 = (item.nama_pembimbing_2 || item.pembimbing_2 || '').toLowerCase();
                        return p1.includes(term) || p2.includes(term);
                    }
                    case 'penguji': {
                        const pg1 = (item.nama_penguji_1 || item.penguji_1 || '').toLowerCase();
                        const pg2 = (item.nama_penguji_2 || item.penguji_2 || '').toLowerCase();
                        return pg1.includes(term) || pg2.includes(term);
                    }
                    case 'ruangan':
                        return (item.ruangan_sidang || item.detail_nama_ruangan || '').toLowerCase().includes(term);
                    case 'status':
                        return (item.status_sidang || 'Belum Dijadwalkan') === crit.val;
                    default:
                        return true;
                }
            });
        });
    }

    window.handleUnifiedMultiSearchSidang = function () {
        state.sidangCurrentPage = 1;
        renderSidangTable();
    };

    window.changeSidangPageSize = function (size) {
        state.sidangPageSize = parseInt(size, 10) || 10;
        state.sidangCurrentPage = 1;
        renderSidangTable();
    };

    window.renderSidangTable = function () {
        const tbody = document.getElementById('tbodySidang');
        const selectAllCheckbox = document.getElementById('selectAllCheckboxSidang');
        const toolbarTotalEl = document.getElementById('sidangToolbarTotalCount');
        const pStart = document.getElementById('sidangPageStart');
        const pEnd = document.getElementById('sidangPageEnd');
        const pTot = document.getElementById('sidangTotalRecords');

        if (!tbody) return;

        const filtered = getFilteredSidangData();
        const totalItems = filtered.length;
        if (toolbarTotalEl) toolbarTotalEl.innerText = totalItems;

        const totalPages = Math.ceil(totalItems / state.sidangPageSize) || 1;

        if (state.sidangCurrentPage > totalPages) state.sidangCurrentPage = totalPages;
        if (state.sidangCurrentPage < 1) state.sidangCurrentPage = 1;

        const startIdx = (state.sidangCurrentPage - 1) * state.sidangPageSize;
        const pageItems = filtered.slice(startIdx, startIdx + state.sidangPageSize);

        if (totalItems === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300"></i>
                            <span class="text-xs font-semibold text-slate-500">Tidak ada data mahasiswa sidang yang sesuai filter</span>
                            <button type="button" onclick="resetSidangMultiSearch()" class="mt-2 text-xs font-bold text-amber-600 hover:underline">Reset Filter</button>
                        </div>
                    </td>
                </tr>
            `;
            if (pStart) pStart.innerText = '0';
            if (pEnd) pEnd.innerText = '0';
            if (pTot) pTot.innerText = '0';
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            renderSidangPagination(0);
            updateSidangSelectionUI();
            return;
        }

        let html = '';
        let allPageSelected = pageItems.length > 0;
        let anyPageSelected = false;

        pageItems.forEach((row, idx) => {
            const isChecked = state.sidangSelectedStudents.has(row.nim);
            if (isChecked) anyPageSelected = true;
            else allPageSelected = false;

            const isTerjadwal = (row.status_sidang === 'Terjadwal');
            const statusBadge = isTerjadwal
                ? '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold text-[10px] rounded-full border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-2xs whitespace-nowrap"><i class="fa-solid fa-circle-check text-[10px]"></i> Terjadwal</span>'
                : '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold text-[10px] rounded-full border border-rose-300 bg-rose-50 text-rose-700 shadow-2xs whitespace-nowrap"><i class="fa-solid fa-clock text-[10px]"></i> Belum Dijadwalkan</span>';

            const waktuDisplay = isTerjadwal && row.tgl_sidang
                ? `<div class="space-y-0.5 text-slate-800 text-[11px] leading-tight">
                    <div class="flex items-center gap-1 font-bold text-slate-900"><i class="fa-solid fa-calendar-day text-amber-500 text-[10px]"></i> <span>${escapeHtml(row.tgl_sidang)}</span></div>
                    <div class="flex items-center gap-1 text-[10px] text-slate-500 font-mono"><i class="fa-solid fa-clock text-slate-400 text-[9px]"></i> <span>${escapeHtml(row.jam_mulai_sidang ? row.jam_mulai_sidang.substring(0, 5) : '')} ${row.jam_selesai_sidang ? '- ' + escapeHtml(row.jam_selesai_sidang.substring(0, 5)) : 'WIB'}</span></div>
                   </div>`
                : '<span class="text-slate-400 italic text-[11px]">Belum diatur</span>';

            const roomText = row.detail_nama_ruangan || row.ruangan_sidang;
            const ruanganDisplay = isTerjadwal && roomText
                ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 shadow-2xs max-w-[120px] truncate" title="${escapeHtml(roomText)}">
                    <i class="fa-solid fa-door-open text-cyan-600 text-[10px]"></i> <span class="truncate">${escapeHtml(roomText)}</span>
                   </span>`
                : '<span class="text-slate-400 italic text-[11px]">-</span>';

            const p1Name = row.nama_pembimbing_1 || (row.pembimbing_1 ? 'NIP: ' + row.pembimbing_1 : '-');
            const p2Name = row.nama_pembimbing_2 || (row.pembimbing_2 ? 'NIP: ' + row.pembimbing_2 : '-');
            const pg1Name = row.nama_penguji_1 || (row.penguji_1 ? 'NIP: ' + row.penguji_1 : '-');
            const pg2Name = row.nama_penguji_2 || (row.penguji_2 ? 'NIP: ' + row.penguji_2 : '-');

            const pembimbingHtml = `
                <div class="space-y-0.5 text-slate-700 text-[11px] leading-tight">
                    <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-orange-100 text-orange-700 flex items-center justify-center text-[9px] font-bold shrink-0">1</span> <span class="truncate max-w-[125px]" title="${escapeHtml(p1Name)}">${escapeHtml(p1Name)}</span></div>
                    <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-orange-100 text-orange-700 flex items-center justify-center text-[9px] font-bold shrink-0">2</span> <span class="truncate max-w-[125px]" title="${escapeHtml(p2Name)}">${escapeHtml(p2Name)}</span></div>
                </div>
            `;

            let pengujiHtml = '';
            if (row.penguji_1 || row.nama_penguji_1 || row.penguji_2 || row.nama_penguji_2) {
                pengujiHtml = `
                    <div class="space-y-0.5 text-slate-800 text-[11px] leading-tight">
                        <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center text-[9px] font-bold shrink-0">1</span> <span class="truncate max-w-[125px] font-semibold" title="${escapeHtml(pg1Name)}">${escapeHtml(pg1Name)}</span></div>
                        <div class="flex items-center gap-1"><span class="w-3.5 h-3.5 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center text-[9px] font-bold shrink-0">2</span> <span class="truncate max-w-[125px] font-semibold" title="${escapeHtml(pg2Name)}">${escapeHtml(pg2Name)}</span></div>
                    </div>
                `;
            } else {
                pengujiHtml = `<span class="text-slate-400 italic text-[11px]">Belum ditentukan</span>`;
            }

            const rowHighlight = isChecked ? 'bg-amber-50/70 border-l-4 border-l-amber-600' : 'hover:bg-slate-50/80';
            const fullName = row.nama_lengkap || row.nama || '-';
            const judul = row.judul_1 || '-';

            const btnColor = isTerjadwal ? 'btn-emerald' : 'btn-amber';
            const label1 = isTerjadwal ? 'Ubah Jadwal' : 'Jadwalkan';
            const label2 = isTerjadwal ? 'Edit Jadwal' : 'Set Jadwal';
            const iconClass = isTerjadwal ? 'fa-pen-to-square' : 'fa-arrow-right';
            const btnTitle = isTerjadwal ? 'Ubah Jadwal & Ruangan Sidang' : 'Jadwalkan Sidang TA';

            html += `
                <tr class="table-row-animate ${rowHighlight} transition-colors" style="--row-index: ${idx};">
                    <td class="w-8 py-3 px-3 text-center">
                        <input type="checkbox" 
                            class="row-select-sidang w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300 cursor-pointer" 
                            value="${row.nim}" 
                            ${isChecked ? 'checked' : ''}
                            onchange="toggleRowSelectSidang(this)">
                    </td>
                    <td class="w-24 py-3 px-2 font-bold font-mono text-[11px] text-slate-900">${row.nim}</td>
                    <td class="w-36 py-3 px-2 font-semibold text-slate-800 text-xs">
                        <span class="truncate block max-w-[130px] cursor-pointer hover:text-amber-600 transition" onclick="openModalSingleSidang('${escapeHtml(row.nim)}')" title="${escapeHtml(fullName)}">${escapeHtml(fullName)}</span>
                    </td>
                    <td class="py-3 px-2 text-slate-600 font-normal">
                        <p class="line-clamp-2 max-w-[200px] text-[11px] leading-snug" title="${escapeHtml(judul)}">${escapeHtml(judul)}</p>
                    </td>
                    <td class="w-36 py-3 px-2">${pembimbingHtml}</td>
                    <td class="w-36 py-3 px-2">${pengujiHtml}</td>
                    <td class="w-32 py-3 px-2">${waktuDisplay}</td>
                    <td class="w-28 py-3 px-2">${ruanganDisplay}</td>
                    <td class="w-28 py-3 px-2 text-center">${statusBadge}</td>
                    <td class="w-32 py-3 px-3 pr-4 text-right">
                        <button type="button" onclick="openModalSingleSidang('${escapeHtml(row.nim)}')" class="btn-3d-kinetic ${btnColor} btn-compact ml-auto cursor-pointer" title="${btnTitle}">
                            <div class="bg"></div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 342 208" height="208" width="342" class="splash">
                                <path stroke-linecap="round" stroke-width="3" d="M54.1054 99.7837C54.1054 99.7837 40.0984 90.7874 26.6893 97.6362C13.2802 104.485 1.5 97.6362 1.5 97.6362" />
                                <path stroke-linecap="round" stroke-width="3" d="M285.273 99.7841C285.273 99.7841 299.28 90.7879 312.689 97.6367C326.098 104.486 340.105 95.4893 340.105 95.4893" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M281.133 64.9917C281.133 64.9917 287.96 49.8089 302.934 48.2295C317.908 46.6501 319.712 36.5272 319.712 36.5272" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M281.133 138.984C281.133 138.984 287.96 154.167 302.934 155.746C317.908 157.326 319.712 167.449 319.712 167.449" />
                                <path stroke-linecap="round" stroke-width="3" d="M230.578 57.4476C230.578 57.4476 225.785 41.5051 236.061 30.4998C246.337 19.4945 244.686 12.9998 244.686 12.9998" />
                                <path stroke-linecap="round" stroke-width="3" d="M230.578 150.528C230.578 150.528 225.785 166.471 236.061 177.476C246.337 188.481 244.686 194.976 244.686 194.976" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M170.392 57.0278C170.392 57.0278 173.89 42.1322 169.571 29.54C165.252 16.9478 168.751 2.05227 168.751 2.05227" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M170.392 150.948C170.392 150.948 173.89 165.844 169.571 178.436C165.252 191.028 168.751 205.924 168.751 205.924" />
                                <path stroke-linecap="round" stroke-width="3" d="M112.609 57.4476C112.609 57.4476 117.401 41.5051 107.125 30.4998C96.8492 19.4945 98.5 12.9998 98.5 12.9998" />
                                <path stroke-linecap="round" stroke-width="3" d="M112.609 150.528C112.609 150.528 117.401 166.471 107.125 177.476C96.8492 188.481 98.5 194.976 98.5 194.976" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M62.2941 64.9917C62.2941 64.9917 55.4671 49.8089 40.4932 48.2295C25.5194 46.6501 23.7159 36.5272 23.7159 36.5272" />
                                <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3" d="M62.2941 145.984C62.2941 145.984 55.4671 161.167 40.4932 162.746C25.5194 164.326 23.7159 174.449 23.7159 174.449" />
                            </svg>
                            <div class="wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 221 42" height="42" width="221" class="path">
                                    <path stroke-linecap="round" stroke-width="3" d="M182.674 2H203C211.837 2 219 9.16344 219 18V24C219 32.8366 211.837 40 203 40H18C9.16345 40 2 32.8366 2 24V18C2 9.16344 9.16344 2 18 2H47.8855" />
                                </svg>
                                <div class="outline"></div>
                                <div class="content">
                                    <span class="char state-1">${renderAnimatedChars(label1)}</span>
                                    <span class="char state-2">${renderAnimatedChars(label2)}</span>
                                    <i class="fa-solid ${iconClass} icon-action"></i>
                                </div>
                            </div>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allPageSelected && pageItems.length > 0;
            selectAllCheckbox.indeterminate = !allPageSelected && anyPageSelected;
        }

        const endIdx = Math.min(startIdx + state.sidangPageSize, totalItems);
        if (pStart) pStart.innerText = totalItems === 0 ? '0' : (startIdx + 1);
        if (pEnd) pEnd.innerText = endIdx;
        if (pTot) pTot.innerText = totalItems;

        renderSidangPagination(totalPages);
        updateSidangSelectionUI();
    };

    function renderSidangPagination(totalPages) {
        const navContainer = document.getElementById('sidangPaginationNav');
        if (!navContainer) return;
        navContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const btnFirst = document.createElement('button');
        btnFirst.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.sidangCurrentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnFirst.innerHTML = '&laquo; Awal';
        btnFirst.disabled = (state.sidangCurrentPage === 1);
        btnFirst.addEventListener('click', () => goToSidangPage(1));
        navContainer.appendChild(btnFirst);

        const btnPrev = document.createElement('button');
        btnPrev.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.sidangCurrentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnPrev.innerHTML = '&lsaquo; Prev';
        btnPrev.disabled = (state.sidangCurrentPage === 1);
        btnPrev.addEventListener('click', () => goToSidangPage(state.sidangCurrentPage - 1));
        navContainer.appendChild(btnPrev);

        const maxVisibleButtons = 5;
        let startPage = Math.max(1, state.sidangCurrentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);
        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            const btnPage = document.createElement('button');
            const isActive = (p === state.sidangCurrentPage);
            btnPage.className = `px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer ${isActive ? 'bg-amber-500 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'}`;
            btnPage.textContent = p;
            btnPage.addEventListener('click', () => goToSidangPage(p));
            navContainer.appendChild(btnPage);
        }

        const btnNext = document.createElement('button');
        btnNext.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.sidangCurrentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnNext.innerHTML = 'Next &rsaquo;';
        btnNext.disabled = (state.sidangCurrentPage === totalPages);
        btnNext.addEventListener('click', () => goToSidangPage(state.sidangCurrentPage + 1));
        navContainer.appendChild(btnNext);

        const btnLast = document.createElement('button');
        btnLast.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.sidangCurrentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 cursor-pointer'}`;
        btnLast.innerHTML = 'Akhir &raquo;';
        btnLast.disabled = (state.sidangCurrentPage === totalPages);
        btnLast.addEventListener('click', () => goToSidangPage(totalPages));
        navContainer.appendChild(btnLast);
    }

    window.goToSidangPage = function (page) {
        state.sidangCurrentPage = page;
        renderSidangTable();
    };

    window.toggleRowSelectSidang = function (checkbox) {
        const nim = checkbox.value;
        const student = (state.sidangList || []).find(s => s.nim == nim);
        if (!student) return;

        const row = checkbox.closest('tr');

        if (checkbox.checked) {
            state.sidangSelectedStudents.set(nim, student);
            if (row) {
                row.classList.add('bg-amber-50/70', 'border-l-4', 'border-l-amber-600');
                row.classList.remove('hover:bg-slate-50/80');
            }
        } else {
            state.sidangSelectedStudents.delete(nim);
            if (row) {
                row.classList.remove('bg-amber-50/70', 'border-l-4', 'border-l-amber-600');
                row.classList.add('hover:bg-slate-50/80');
            }
        }

        const pageCheckboxes = document.querySelectorAll('.row-select-sidang');
        const allChecked = pageCheckboxes.length > 0 && Array.from(pageCheckboxes).every(c => c.checked);
        const selectAllEl = document.getElementById('selectAllCheckboxSidang');
        if (selectAllEl) selectAllEl.checked = allChecked;

        updateSidangSelectionUI();
    };

    window.toggleSelectAllSidang = function (checkbox) {
        const isChecked = checkbox.checked;
        const pageCheckboxes = document.querySelectorAll('.row-select-sidang');

        pageCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            const nim = cb.value;
            const student = (state.sidangList || []).find(s => s.nim == nim);
            const row = cb.closest('tr');

            if (isChecked) {
                if (student) state.sidangSelectedStudents.set(nim, student);
                if (row) {
                    row.classList.add('bg-amber-50/70', 'border-l-4', 'border-l-amber-600');
                    row.classList.remove('hover:bg-slate-50/80');
                }
            } else {
                state.sidangSelectedStudents.delete(nim);
                if (row) {
                    row.classList.remove('bg-amber-50/70', 'border-l-4', 'border-l-amber-600');
                    row.classList.add('hover:bg-slate-50/80');
                }
            }
        });

        updateSidangSelectionUI();
    };

    window.clearAllSidangSelection = function () {
        state.sidangSelectedStudents.clear();
        document.querySelectorAll('.row-select-sidang').forEach(cb => {
            cb.checked = false;
            const row = cb.closest('tr');
            if (row) {
                row.classList.remove('bg-amber-50/70', 'border-l-4', 'border-l-amber-600');
                row.classList.add('hover:bg-slate-50/80');
            }
        });
        const selectAll = document.getElementById('selectAllCheckboxSidang');
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
        updateSidangSelectionUI();
    };

    window.updateSidangSelectionUI = function () {
        const count = state.sidangSelectedStudents.size;
        const floatingBar = document.getElementById('floatingSidangBatchBar');
        const floatingCount = document.getElementById('floatingSidangCount');
        const floatingBatchCountText = document.getElementById('floatingSidangBatchCountText');

        if (floatingBar) {
            if (count > 0) {
                floatingBar.classList.remove('hidden');
                floatingBar.classList.add('flex');
                if (floatingCount) floatingCount.innerText = count;
                if (floatingBatchCountText) floatingBatchCountText.innerText = count;
            } else {
                floatingBar.classList.add('hidden');
                floatingBar.classList.remove('flex');
            }
        }
    };

    // Modal Single Sidang
    // =========================================================
    // FLATPICKR DATEPICKER ENGINE (TAB PENJADWALAN SIDANG)
    // =========================================================
    let singleSidangDatePicker = null;
    let batchSidangDatePicker = null;

    function initSidangDatePickers() {
        const singleInput = document.getElementById('singleSidangTgl');
        if (singleInput && typeof flatpickr !== 'undefined') {
            if (singleInput._flatpickr) singleInput._flatpickr.destroy();
            singleSidangDatePicker = flatpickr(singleInput, {
                dateFormat: 'Y-m-d',
                minDate: 'today',
                disableMobile: true
            });
        }

        const batchInput = document.getElementById('batchSidangTgl');
        if (batchInput && typeof flatpickr !== 'undefined') {
            if (batchInput._flatpickr) batchInput._flatpickr.destroy();
            batchSidangDatePicker = flatpickr(batchInput, {
                dateFormat: 'Y-m-d',
                minDate: 'today',
                disableMobile: true
            });
        }
    }

    window.openModalSingleSidang = function (nim) {
        const student = (state.sidangList || []).find(s => s.nim == nim);
        if (!student) return;

        const modal = document.getElementById('modalSingleSidang');
        const nimInput = document.getElementById('singleSidangNim');
        const infoEl = document.getElementById('singleSidangStudentInfo');
        const tglInput = document.getElementById('singleSidangTgl');
        const jamMulaiInput = document.getElementById('singleSidangJamMulai');
        const jamSelesaiInput = document.getElementById('singleSidangJamSelesai');
        const ruanganHidden = document.getElementById('singleSidangRuangan');
        const ruanganInput = document.getElementById('singleSidangRuanganInput');

        if (nimInput) nimInput.value = student.nim;
        if (infoEl) infoEl.textContent = `${student.nama_lengkap} (${student.nim})`;
        
        if (singleSidangDatePicker) {
            if (student.tgl_sidang) {
                singleSidangDatePicker.setDate(student.tgl_sidang, true);
            } else {
                singleSidangDatePicker.clear();
            }
        } else if (tglInput) {
            tglInput.value = student.tgl_sidang || '';
        }

        if (jamMulaiInput) jamMulaiInput.value = student.jam_mulai_sidang ? student.jam_mulai_sidang.substring(0, 5) : '08:00';
        if (jamSelesaiInput) jamSelesaiInput.value = student.jam_selesai_sidang ? student.jam_selesai_sidang.substring(0, 5) : '10:00';

        if (student.ruangan_sidang) {
            const rObj = (state.ruanganList || []).find(r => r.kode_ruangan === student.ruangan_sidang || r.nama_ruangan === student.ruangan_sidang);
            const display = rObj ? `${rObj.kode_ruangan ? rObj.kode_ruangan + ' - ' : ''}${rObj.nama_ruangan}` : student.ruangan_sidang;
            if (ruanganInput) ruanganInput.value = display;
            if (ruanganHidden) ruanganHidden.value = student.ruangan_sidang;
        } else {
            if (ruanganInput) ruanganInput.value = '';
            if (ruanganHidden) ruanganHidden.value = '';
        }

        closeRuanganDropdown('single');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };

    window.closeModalSingleSidang = function () {
        const modal = document.getElementById('modalSingleSidang');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    window.submitSingleSidang = function (e) {
        e.preventDefault();

        const nim = document.getElementById('singleSidangNim').value;
        const tgl = document.getElementById('singleSidangTgl').value;
        const jamMulai = document.getElementById('singleSidangJamMulai').value;
        const jamSelesai = document.getElementById('singleSidangJamSelesai').value;
        const ruangan = document.getElementById('singleSidangRuangan').value;
        const btn = document.getElementById('btnSubmitSingleSidang');

        if (!nim || !tgl || !jamMulai || !ruangan) {
            Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Tanggal, jam mulai, dan ruangan sidang wajib diisi!' });
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menyimpan...';
        }

        const formData = new FormData();
        formData.append('nim', nim);
        formData.append('tgl_sidang', tgl);
        formData.append('jam_mulai_sidang', jamMulai);
        formData.append('jam_selesai_sidang', jamSelesai);
        formData.append('ruangan_sidang', ruangan);

        fetch(cfg.ajaxSidangUpdateUrl, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status) {
                // Update local state
                const student = (state.sidangList || []).find(s => s.nim == nim);
                if (student) {
                    student.tgl_sidang = tgl;
                    student.jam_mulai_sidang = jamMulai;
                    student.jam_selesai_sidang = jamSelesai;
                    student.ruangan_sidang = ruangan;
                    student.status_sidang = 'Terjadwal';

                    const rObj = (state.ruanganList || []).find(r => r.kode_ruangan === ruangan || r.nama_ruangan === ruangan);
                    if (rObj) student.detail_nama_ruangan = rObj.nama_ruangan;
                }

                closeModalSingleSidang();
                renderSidangTable();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Jadwal sidang berhasil ditetapkan!',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan saat menyimpan jadwal.' });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-save text-xs"></i> Simpan Jadwal Sidang';
            }
        });
    };

    // Modal Batch Sidang
    window.openModalBatchSidang = function () {
        if (state.sidangSelectedStudents.size === 0) {
            Swal.fire({ icon: 'info', title: 'Pilih Mahasiswa', text: 'Centang setidaknya satu mahasiswa untuk menjadwalkan sidang secara massal.' });
            return;
        }

        const modal = document.getElementById('modalBatchSidang');
        const listEl = document.getElementById('batchSidangSelectedList');
        const countBadge = document.getElementById('badgeBatchSidangCount');

        if (countBadge) countBadge.textContent = `${state.sidangSelectedStudents.size} Mahasiswa`;

        if (listEl) {
            let html = '';
            state.sidangSelectedStudents.forEach(item => {
                html += `
                    <div class="flex items-center justify-between px-3.5 py-2.5 bg-white rounded-xl border border-slate-200 shadow-2xs hover:border-amber-300 transition">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs shrink-0">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-slate-800 truncate">${escapeHtml(item.nama_lengkap)} <span class="text-slate-400 font-mono text-xs">(${escapeHtml(item.nim)})</span></span>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg shrink-0">${escapeHtml(item.prodi || 'Informatika')}</span>
                    </div>
                `;
            });
            listEl.innerHTML = html;
        }

        const ruanganHidden = document.getElementById('batchSidangRuangan');
        const ruanganInput = document.getElementById('batchSidangRuanganInput');
        const jamMulaiInput = document.getElementById('batchSidangJamMulai');
        const jamSelesaiInput = document.getElementById('batchSidangJamSelesai');

        if (ruanganHidden) ruanganHidden.value = '';
        if (ruanganInput) ruanganInput.value = '';
        if (jamMulaiInput) jamMulaiInput.value = '08:00';
        if (jamSelesaiInput) jamSelesaiInput.value = '10:00';

        closeRuanganDropdown('batch');

        if (batchSidangDatePicker) {
            batchSidangDatePicker.clear();
        } else {
            const batchTgl = document.getElementById('batchSidangTgl');
            if (batchTgl) batchTgl.value = '';
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };

    window.closeModalBatchSidang = function () {
        const modal = document.getElementById('modalBatchSidang');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    window.submitBatchSidang = function (e) {
        e.preventDefault();

        const nims = Array.from(state.sidangSelectedStudents.keys());
        const tgl = document.getElementById('batchSidangTgl').value;
        const jamMulai = document.getElementById('batchSidangJamMulai').value;
        const jamSelesai = document.getElementById('batchSidangJamSelesai').value;
        const ruangan = document.getElementById('batchSidangRuangan').value;
        const btn = document.getElementById('btnSubmitBatchSidang');

        if (nims.length === 0 || !tgl || !jamMulai || !ruangan) {
            Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Tanggal, jam mulai, dan ruangan sidang wajib diisi!' });
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Memproses...';
        }

        const formData = new FormData();
        formData.append('nims', JSON.stringify(nims));
        formData.append('tgl_sidang', tgl);
        formData.append('jam_mulai_sidang', jamMulai);
        formData.append('jam_selesai_sidang', jamSelesai);
        formData.append('ruangan_sidang', ruangan);

        fetch(cfg.ajaxSidangBatchUrl, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status) {
                // Update local state
                nims.forEach(nim => {
                    const student = (state.sidangList || []).find(s => s.nim == nim);
                    if (student) {
                        student.tgl_sidang = tgl;
                        student.jam_mulai_sidang = jamMulai;
                        student.jam_selesai_sidang = jamSelesai;
                        student.ruangan_sidang = ruangan;
                        student.status_sidang = 'Terjadwal';
                        const rObj = (state.ruanganList || []).find(r => r.kode_ruangan === ruangan || r.nama_ruangan === ruangan);
                        if (rObj) student.detail_nama_ruangan = rObj.nama_ruangan;
                    }
                });

                state.sidangSelectedStudents.clear();
                closeModalBatchSidang();
                renderSidangTable();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Jadwal sidang massal berhasil diterapkan!',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan saat memproses jadwal massal.' });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check-double text-xs"></i> Terapkan ke Semua Terpilih';
            }
        });
    };

    // Modal Kelola Ruangan Dinamis
    window.openModalKelolaRuangan = function () {
        const modal = document.getElementById('modalKelolaRuangan');
        renderRuanganListModal();

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };

    window.closeModalKelolaRuangan = function () {
        const modal = document.getElementById('modalKelolaRuangan');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    window.renderRuanganListModal = function () {
        const tbody = document.getElementById('tbodyRuanganList');
        const badgeTotal = document.getElementById('badgeTotalRuanganModal');
        const list = state.ruanganList || [];

        if (badgeTotal) badgeTotal.textContent = `${list.length} Ruangan`;

        if (!tbody) return;

        if (list.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-slate-400 italic">Belum ada ruangan yang ditambahkan.</td></tr>';
            return;
        }

        let html = '';
        list.forEach((item, idx) => {
            html += `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-2.5 px-3.5 font-bold font-mono text-slate-900">${escapeHtml(item.kode_ruangan || '-')}</td>
                    <td class="py-2.5 px-3.5 font-semibold text-slate-800">${escapeHtml(item.nama_ruangan || '-')}</td>
                    <td class="py-2.5 px-3.5 text-slate-500">${escapeHtml(item.lokasi || '-')}</td>
                    <td class="py-2.5 px-3.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ${escapeHtml(item.status || 'Tersedia')}
                        </span>
                    </td>
                    <td class="py-2.5 px-3.5 text-center">
                        <button type="button" onclick="hapusRuangan(${item.id}, '${escapeHtml(item.nama_ruangan)}')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition flex items-center justify-center mx-auto cursor-pointer" title="Hapus Ruangan">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    };

    // =========================================================
    // RUANGAN SIDANG AUTOCOMPLETE COMBOBOX (SEARCH + DYNAMIC ADD)
    // =========================================================
    window.openRuanganDropdown = function (modalType) {
        const dropdown = document.getElementById(`${modalType}RuanganDropdown`);
        const arrow = document.getElementById(`${modalType}RuanganArrow`);
        const input = document.getElementById(`${modalType}SidangRuanganInput`);
        const query = input ? input.value : '';

        filterRuanganDropdown(modalType, query);
        if (dropdown) dropdown.classList.remove('hidden');
        if (arrow) arrow.classList.add('rotate-180');
    };

    window.closeRuanganDropdown = function (modalType) {
        const dropdown = document.getElementById(`${modalType}RuanganDropdown`);
        const arrow = document.getElementById(`${modalType}RuanganArrow`);
        if (dropdown) dropdown.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    };

    window.toggleRuanganDropdown = function (modalType) {
        const dropdown = document.getElementById(`${modalType}RuanganDropdown`);
        if (dropdown && dropdown.classList.contains('hidden')) {
            openRuanganDropdown(modalType);
        } else {
            closeRuanganDropdown(modalType);
        }
    };

    window.filterRuanganDropdown = function (modalType, query) {
        const dropdown = document.getElementById(`${modalType}RuanganDropdown`);
        const hiddenInput = document.getElementById(`${modalType}SidangRuangan`);
        if (!dropdown) return;

        const list = state.ruanganList || [];
        const q = (query || '').toLowerCase().trim();

        let filtered = list.filter(r => {
            const codeMatch = (r.kode_ruangan || '').toLowerCase().includes(q);
            const nameMatch = (r.nama_ruangan || '').toLowerCase().includes(q);
            const locMatch = (r.lokasi || '').toLowerCase().includes(q);
            return codeMatch || nameMatch || locMatch;
        });

        let html = '';

        // If there is a custom query typed that doesn't match an exact room, show option to add / use custom
        const exactMatch = list.some(r => (r.nama_ruangan || '').toLowerCase() === q || (r.kode_ruangan || '').toLowerCase() === q);
        if (q && !exactMatch) {
            html += `
                <div onclick="selectRuangan('${modalType}', '${escapeHtml(query.trim())}', '${escapeHtml(query.trim())}')" 
                     class="p-2.5 bg-amber-50 hover:bg-amber-100/90 text-amber-900 font-bold cursor-pointer flex items-center justify-between transition border-b border-amber-200">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-amber-600"></i>
                        <span>Gunakan / Input Ruangan Baru: <strong>"${escapeHtml(query.trim())}"</strong></span>
                    </span>
                    <span class="text-[10px] bg-amber-200 text-amber-800 px-2 py-0.5 rounded-md font-semibold">Custom</span>
                </div>
            `;
        }

        if (filtered.length > 0) {
            filtered.forEach(r => {
                const code = r.kode_ruangan || '-';
                const name = r.nama_ruangan || '-';
                const loc = r.lokasi ? `(${r.lokasi})` : '';
                const display = `${code !== '-' ? code + ' - ' : ''}${name} ${loc}`;
                const val = r.kode_ruangan || r.nama_ruangan;

                html += `
                    <div onclick="selectRuangan('${modalType}', '${escapeHtml(val)}', '${escapeHtml(display)}')" 
                         class="p-2.5 hover:bg-slate-50 text-slate-800 font-medium cursor-pointer flex items-center justify-between transition">
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-cyan-50 text-cyan-800 border border-cyan-200 rounded text-[10.5px] font-bold font-mono">${escapeHtml(code)}</span>
                            <span class="font-semibold text-slate-900">${escapeHtml(name)}</span>
                            <span class="text-slate-400 text-[11px]">${escapeHtml(loc)}</span>
                        </div>
                        <i class="fa-solid fa-check text-xs text-amber-600 ${hiddenInput && hiddenInput.value === val ? '' : 'hidden'}"></i>
                    </div>
                `;
            });
        } else if (!q) {
            html += `<div class="p-3 text-center text-slate-400 text-xs">Belum ada data ruangan terdaftar.</div>`;
        }

        dropdown.innerHTML = html;
        if (hiddenInput) {
            hiddenInput.value = query.trim();
        }
    };

    window.selectRuangan = function (modalType, val, display) {
        const hiddenInput = document.getElementById(`${modalType}SidangRuangan`);
        const textInput = document.getElementById(`${modalType}SidangRuanganInput`);
        if (hiddenInput) hiddenInput.value = val;
        if (textInput) textInput.value = display || val;
        closeRuanganDropdown(modalType);
    };

    // =========================================================
    // EXACT RADIAL CLOCK & DRAG-SLOT ENGINE (TAB SIDANG)
    // =========================================================
    const sidangClockState = {
        single: { target: 'mulai', hour: 14, minute: 0, isHourMode: true, isDragging: false },
        batch: { target: 'mulai', hour: 14, minute: 0, isHourMode: true, isDragging: false }
    };

    window.openSidangInlinePicker = function (modalType, target) {
        const stateObj = sidangClockState[modalType];
        if (!stateObj) return;

        stateObj.target = target;
        const panel = document.getElementById(`${modalType}InlineClockPanel`);
        const label = document.getElementById(`${modalType}InlineTpLabel`);
        const inputMulai = document.getElementById(`${modalType}SidangJamMulai`);
        const inputSelesai = document.getElementById(`${modalType}SidangJamSelesai`);

        if (label) {
            label.textContent = (target === 'mulai') ? 'PILIH JAM MULAI' : 'PILIH JAM SELESAI';
        }

        const existingVal = (target === 'mulai') ? (inputMulai ? inputMulai.value : '') : (inputSelesai ? inputSelesai.value : '');
        if (existingVal && existingVal.includes(':')) {
            const parts = existingVal.split(':');
            stateObj.hour = parseInt(parts[0]) || 14;
            stateObj.minute = parseInt(parts[1]) || 0;
        } else {
            stateObj.hour = (target === 'mulai') ? 14 : 16;
            stateObj.minute = 0;
        }

        setSidangClockMode(modalType, 'hour');
        updateSidangClockDisplay(modalType);

        if (panel) {
            panel.style.display = 'block';
            setTimeout(() => {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 50);
        }

        initSidangClockEvents(modalType);
        initSidangSlotDragEvents(modalType);
    };

    window.closeSidangInlinePicker = function (modalType) {
        const panel = document.getElementById(`${modalType}InlineClockPanel`);
        if (panel) panel.style.display = 'none';
    };

    window.applySidangInlinePicker = function (modalType) {
        const stateObj = sidangClockState[modalType];
        if (!stateObj) return;

        const hh = stateObj.hour.toString().padStart(2, '0');
        const mm = stateObj.minute.toString().padStart(2, '0');
        const timeStr = `${hh}:${mm}`;

        const inputMulai = document.getElementById(`${modalType}SidangJamMulai`);
        const inputSelesai = document.getElementById(`${modalType}SidangJamSelesai`);

        if (stateObj.target === 'mulai') {
            if (inputMulai) inputMulai.value = timeStr;
        } else {
            if (inputSelesai) inputSelesai.value = timeStr;
        }

        closeSidangInlinePicker(modalType);
    };

    window.setSidangClockMode = function (modalType, mode) {
        const stateObj = sidangClockState[modalType];
        if (!stateObj) return;

        stateObj.isHourMode = (mode === 'hour');

        const tabHour = document.getElementById(`${modalType}TpTabHour`);
        const tabMinute = document.getElementById(`${modalType}TpTabMinute`);
        const dispHour = document.getElementById(`${modalType}TpDisplayHour`);
        const dispMin = document.getElementById(`${modalType}TpDisplayMinute`);

        if (tabHour) {
            if (stateObj.isHourMode) {
                tabHour.style.background = '#7c3aed';
                tabHour.style.color = '#ffffff';
                tabHour.style.boxShadow = '0 2px 6px rgba(124,58,237,0.3)';
            } else {
                tabHour.style.background = 'transparent';
                tabHour.style.color = '#64748b';
                tabHour.style.boxShadow = 'none';
            }
        }
        if (tabMinute) {
            if (!stateObj.isHourMode) {
                tabMinute.style.background = '#7c3aed';
                tabMinute.style.color = '#ffffff';
                tabMinute.style.boxShadow = '0 2px 6px rgba(124,58,237,0.3)';
            } else {
                tabMinute.style.background = 'transparent';
                tabMinute.style.color = '#64748b';
                tabMinute.style.boxShadow = 'none';
            }
        }

        if (dispHour) dispHour.style.color = stateObj.isHourMode ? '#1e293b' : '#94a3b8';
        if (dispMin) dispMin.style.color = !stateObj.isHourMode ? '#1e293b' : '#94a3b8';

        renderSidangClock(modalType);
    };

    function updateSidangClockDisplay(modalType) {
        const stateObj = sidangClockState[modalType];
        if (!stateObj) return;

        const hh = stateObj.hour.toString().padStart(2, '0');
        const mm = stateObj.minute.toString().padStart(2, '0');

        const dispHour = document.getElementById(`${modalType}TpDisplayHour`);
        const dispMin = document.getElementById(`${modalType}TpDisplayMinute`);

        if (dispHour) dispHour.textContent = hh;
        if (dispMin) dispMin.textContent = mm;

        renderSidangClock(modalType);
    }

    function drawSidangClockNumber(modalType, container, val, isInner) {
        const stateObj = sidangClockState[modalType];
        const el = document.createElement('div');
        el.className = 'tp-clock-number ' + (isInner ? 'inner' : '');
        el.innerText = val.toString().padStart(isInner ? 2 : 1, '0');

        const radius = isInner ? 60 : 95;
        const angleBase = stateObj.isHourMode ? (val % 12) * 30 : val * 6;
        const rad = (angleBase - 90) * (Math.PI / 180);

        const x = 120 + radius * Math.cos(rad);
        const y = 120 + radius * Math.sin(rad);

        el.style.position = 'absolute';
        el.style.left = x + 'px';
        el.style.top = y + 'px';
        el.style.transform = 'translate(-50%, -50%)';
        el.style.width = '28px';
        el.style.height = '28px';
        el.style.lineHeight = '28px';
        el.style.textAlign = 'center';
        el.style.fontSize = isInner ? '0.72rem' : '0.8rem';
        el.style.fontWeight = '600';
        el.style.color = isInner ? '#94a3b8' : '#334155';
        el.style.cursor = 'pointer';
        el.style.userSelect = 'none';

        let isActive = false;
        if (stateObj.isHourMode && stateObj.hour === val) isActive = true;
        if (!stateObj.isHourMode && stateObj.minute === val) isActive = true;

        if (isActive) {
            el.style.background = '#7c3aed';
            el.style.color = '#ffffff';
            el.style.borderRadius = '50%';
            el.style.fontWeight = '700';
            el.style.boxShadow = '0 2px 8px rgba(124, 58, 237, 0.4)';
            el.style.zIndex = '20';
        }

        el.onclick = function (e) {
            e.stopPropagation();
            if (stateObj.isHourMode) {
                stateObj.hour = val;
                updateSidangClockDisplay(modalType);
                setTimeout(() => setSidangClockMode(modalType, 'minute'), 180);
            } else {
                stateObj.minute = val;
                updateSidangClockDisplay(modalType);
            }
        };

        container.appendChild(el);
    }

    function renderSidangClock(modalType) {
        const stateObj = sidangClockState[modalType];
        if (!stateObj) return;

        const container = document.getElementById(`${modalType}TpClockNumbers`);
        const hand = document.getElementById(`${modalType}TpClockHand`);
        if (!container || !hand) return;

        container.innerHTML = '';

        const items = stateObj.isHourMode ? 12 : 60;
        const step = stateObj.isHourMode ? 1 : 5;

        for (let i = step; i <= items; i += step) {
            const val = (i === 60) ? 0 : i;
            if (stateObj.isHourMode) {
                drawSidangClockNumber(modalType, container, val, false);
                drawSidangClockNumber(modalType, container, val + 12 === 24 ? 0 : val + 12, true);
            } else {
                drawSidangClockNumber(modalType, container, val, false);
            }
        }

        if (!stateObj.isHourMode && stateObj.minute % 5 !== 0) {
            drawSidangClockNumber(modalType, container, stateObj.minute, false);
        }

        const val = stateObj.isHourMode ? stateObj.hour : stateObj.minute;
        const targetAngle = stateObj.isHourMode ? (val % 12) * 30 : val * 6;

        // Continuous shortest rotational path (eliminates 360-degree reverse spin on 11 <-> 12)
        if (typeof hand.currentAngle === 'undefined') {
            hand.currentAngle = targetAngle;
        } else {
            const diff = (targetAngle - (hand.currentAngle % 360) + 540) % 360 - 180;
            hand.currentAngle += diff;
        }

        const isInner = stateObj.isHourMode && (val === 0 || val > 12);
        const handHeight = isInner ? '60px' : '95px';

        hand.style.height = handHeight;
        hand.style.transition = stateObj.isDragging ? 'none' : 'transform 0.15s cubic-bezier(0.4, 0, 0.2, 1)';
        hand.style.transform = `translate(-50%, 0) rotate(${hand.currentAngle}deg)`;
    }

    function handleSidangClockEvent(modalType, e) {
        const stateObj = sidangClockState[modalType];
        if (!stateObj) return;

        if (e.type === 'mousemove' && !stateObj.isDragging) return;
        if (e.type === 'touchmove' && !stateObj.isDragging) return;

        if (e.cancelable) e.preventDefault();

        let clientX = e.clientX;
        let clientY = e.clientY;

        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        }

        const container = document.getElementById(`${modalType}TpClockContainer`);
        if (!container) return;

        const rect = container.getBoundingClientRect();
        const x = clientX - rect.left - 120;
        const y = clientY - rect.top - 120;

        let angle = Math.atan2(y, x) * (180 / Math.PI) + 90;
        if (angle < 0) angle += 360;

        const distance = Math.sqrt(x * x + y * y);

        if (stateObj.isHourMode) {
            let hour = Math.round(angle / 30);
            if (hour === 0) hour = 12;
            if (hour === 12 && angle > 345) hour = 12;

            if (distance < 75) {
                hour += 12;
                if (hour === 24) hour = 0;
            }

            if (stateObj.hour !== hour) {
                stateObj.hour = hour;
                updateSidangClockDisplay(modalType);
            }
        } else {
            let minute = Math.round(angle / 6);
            if (minute === 60) minute = 0;

            if (stateObj.minute !== minute) {
                stateObj.minute = minute;
                updateSidangClockDisplay(modalType);
            }
        }
    }

    function initSidangClockEvents(modalType) {
        const clockContainer = document.getElementById(`${modalType}TpClockContainer`);
        if (!clockContainer || clockContainer.dataset.clockInit) return;
        clockContainer.dataset.clockInit = 'true';

        const stateObj = sidangClockState[modalType];

        clockContainer.addEventListener('mousedown', function (e) {
            stateObj.isDragging = true;
            handleSidangClockEvent(modalType, e);
        });

        document.addEventListener('mousemove', function (e) {
            handleSidangClockEvent(modalType, e);
        });

        document.addEventListener('mouseup', function () {
            if (stateObj.isDragging && stateObj.isHourMode) {
                setTimeout(() => setSidangClockMode(modalType, 'minute'), 180);
            }
            stateObj.isDragging = false;
        });

        clockContainer.addEventListener('touchstart', function (e) {
            stateObj.isDragging = true;
            handleSidangClockEvent(modalType, e);
        }, { passive: false });

        document.addEventListener('touchmove', function (e) {
            handleSidangClockEvent(modalType, e);
        }, { passive: false });

        document.addEventListener('touchend', function () {
            if (stateObj.isDragging && stateObj.isHourMode) {
                setTimeout(() => setSidangClockMode(modalType, 'minute'), 180);
            }
            stateObj.isDragging = false;
        });
    }

    function initSidangSlotDragEvents(modalType) {
        const slotContainer = document.getElementById(`${modalType}TpTimeSlots`);
        if (!slotContainer || slotContainer.dataset.slotInit) return;
        slotContainer.dataset.slotInit = 'true';

        let isDraggingSlots = false;
        let dragStartIdx = -1;
        let currentEndIdx = -1;

        function getSlots() {
            return Array.from(slotContainer.querySelectorAll('.tp-slot'));
        }

        function resetSlotStyle(slot) {
            slot.style.background = '#fff';
            slot.style.borderColor = '#e2e8f0';
            slot.style.color = '#475569';
            slot.style.boxShadow = '0 1px 3px rgba(0,0,0,0.06)';
        }

        function previewSlotStyle(slot) {
            slot.style.background = '#ede9fe';
            slot.style.borderColor = '#7c3aed';
            slot.style.color = '#6d28d9';
            slot.style.boxShadow = '0 2px 6px rgba(124,58,237,0.2)';
        }

        function selectedSlotStyle(slot) {
            slot.style.background = '#7c3aed';
            slot.style.borderColor = '#7c3aed';
            slot.style.color = '#fff';
            slot.style.boxShadow = '0 3px 10px rgba(124,58,237,0.35)';
        }

        function updateHighlight(minIdx, maxIdx) {
            getSlots().forEach(function (slot, i) {
                if (i >= minIdx && i <= maxIdx) {
                    previewSlotStyle(slot);
                } else {
                    resetSlotStyle(slot);
                }
            });
        }

        function finalizeSelection(minIdx, maxIdx) {
            const slots = getSlots();
            slots.forEach(function (slot, i) {
                if (i >= minIdx && i <= maxIdx) {
                    selectedSlotStyle(slot);
                } else {
                    resetSlotStyle(slot);
                }
            });

            if (slots[minIdx] && slots[maxIdx]) {
                const jamMulai = slots[minIdx].getAttribute('data-start');
                const jamSelesai = slots[maxIdx].getAttribute('data-end');

                const elMulai = document.getElementById(`${modalType}SidangJamMulai`);
                const elSelesai = document.getElementById(`${modalType}SidangJamSelesai`);
                if (elMulai) elMulai.value = jamMulai;
                if (elSelesai) elSelesai.value = jamSelesai;

                setTimeout(function () {
                    closeSidangInlinePicker(modalType);
                    slots.forEach(resetSlotStyle);
                }, 400);
            }
        }

        slotContainer.addEventListener('mousedown', function (e) {
            const slot = e.target.closest('.tp-slot');
            if (!slot) return;
            e.preventDefault();
            isDraggingSlots = true;
            const slots = getSlots();
            dragStartIdx = slots.indexOf(slot);
            currentEndIdx = dragStartIdx;
            updateHighlight(dragStartIdx, dragStartIdx);
        });

        document.addEventListener('mouseover', function (e) {
            if (!isDraggingSlots) return;
            const slot = e.target.closest(`#${modalType}TpTimeSlots .tp-slot`);
            if (!slot) return;
            const slots = getSlots();
            const idx = slots.indexOf(slot);
            if (idx === -1) return;
            currentEndIdx = idx;
            const minIdx = Math.min(dragStartIdx, currentEndIdx);
            const maxIdx = Math.max(dragStartIdx, currentEndIdx);
            updateHighlight(minIdx, maxIdx);
        });

        document.addEventListener('mouseup', function () {
            if (!isDraggingSlots) return;
            isDraggingSlots = false;
            const minIdx = Math.min(dragStartIdx, currentEndIdx);
            const maxIdx = Math.max(dragStartIdx, currentEndIdx);
            finalizeSelection(minIdx, maxIdx);
        });

        slotContainer.addEventListener('touchstart', function (e) {
            const slot = e.target.closest('.tp-slot');
            if (!slot) return;
            isDraggingSlots = true;
            const slots = getSlots();
            dragStartIdx = slots.indexOf(slot);
            currentEndIdx = dragStartIdx;
            updateHighlight(dragStartIdx, dragStartIdx);
        }, { passive: true });

        document.addEventListener('touchmove', function (e) {
            if (!isDraggingSlots) return;
            const touch = e.touches[0];
            const el = document.elementFromPoint(touch.clientX, touch.clientY);
            if (!el) return;
            const slot = el.closest(`#${modalType}TpTimeSlots .tp-slot`);
            if (!slot) return;
            const slots = getSlots();
            const idx = slots.indexOf(slot);
            if (idx === -1) return;
            currentEndIdx = idx;
            const minIdx = Math.min(dragStartIdx, currentEndIdx);
            const maxIdx = Math.max(dragStartIdx, currentEndIdx);
            updateHighlight(minIdx, maxIdx);
        }, { passive: true });

        document.addEventListener('touchend', function () {
            if (!isDraggingSlots) return;
            isDraggingSlots = false;
            const minIdx = Math.min(dragStartIdx, currentEndIdx);
            const maxIdx = Math.max(dragStartIdx, currentEndIdx);
            finalizeSelection(minIdx, maxIdx);
        });
    }

    window.populateRuanganDropdowns = function () {
        filterRuanganDropdown('single', '');
        filterRuanganDropdown('batch', '');
    };

    window.submitTambahRuangan = function (e) {
        e.preventDefault();

        const kodeInput = document.getElementById('inputKodeRuangan');
        const namaInput = document.getElementById('inputNamaRuangan');
        const lokasiInput = document.getElementById('inputLokasiRuangan');
        const btn = document.getElementById('btnSubmitRuangan');

        const kode = kodeInput ? kodeInput.value.trim() : '';
        const nama = namaInput ? namaInput.value.trim() : '';
        const lokasi = lokasiInput ? lokasiInput.value.trim() : '';

        if (!kode || !nama) {
            Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Kode dan Nama Ruangan wajib diisi.' });
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menyimpan...';
        }

        const formData = new FormData();
        formData.append('kode_ruangan', kode);
        formData.append('nama_ruangan', nama);
        formData.append('lokasi', lokasi);

        fetch(cfg.ajaxTambahRuanganUrl, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status) {
                if (res.data) {
                    state.ruanganList.push(res.data);
                } else {
                    state.ruanganList.push({ id: Date.now(), kode_ruangan: kode, nama_ruangan: nama, lokasi: lokasi, status: 'Tersedia' });
                }

                if (kodeInput) kodeInput.value = '';
                if (namaInput) namaInput.value = '';
                if (lokasiInput) lokasiInput.value = '';

                renderRuanganListModal();
                populateRuanganDropdowns();

                const stRq = document.getElementById('statSidangRuanganCount');
                if (stRq) stRq.textContent = state.ruanganList.length;

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Ruangan baru berhasil ditambahkan!',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menambahkan ruangan.' });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-plus text-xs"></i> Simpan Ruangan';
            }
        });
    };

    window.hapusRuangan = function (id, nama) {
        Swal.fire({
            title: 'Hapus Ruangan?',
            html: `Apakah Anda yakin ingin menghapus ruangan <b>${escapeHtml(nama)}</b> dari sistem?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('id_ruangan', id);

            fetch(cfg.ajaxHapusRuanganUrl, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    state.ruanganList = state.ruanganList.filter(r => r.id != id);
                    renderRuanganListModal();
                    populateRuanganDropdowns();

                    const stRq = document.getElementById('statSidangRuanganCount');
                    if (stRq) stRq.textContent = state.ruanganList.length;

                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: res.message || 'Ruangan berhasil dihapus.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menghapus ruangan.' });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
            });
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        updateFilterBadge();
        renderTable();
        renderP2Table();
        populateRuanganDropdowns();
        renderSidangTable();
        initP2DatePicker();
        initP2ClockEvents();
        initP2SlotDragEvents();
        initSidangDatePickers();
        startRealtimeSync();
    });

})();
