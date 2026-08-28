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
        p2SelectedDosen1: null,
        p2SelectedDosen2: null,
        p2TargetNim: null
    };

    window.switchDashboardTab = function (tabName) {
        state.activeTab = tabName;

        const btnPendaftaran = document.getElementById('tabBtnPendaftaran');
        const btnPreview2 = document.getElementById('tabBtnPreview2');
        const contentPendaftaran = document.getElementById('tabContentPendaftaran');
        const contentPreview2 = document.getElementById('tabContentPreview2');

        if (!btnPendaftaran || !btnPreview2 || !contentPendaftaran || !contentPreview2) return;

        if (tabName === 'pendaftaran') {
            btnPendaftaran.classList.add('bg-white', 'text-orange-600', 'shadow-sm', 'active');
            btnPendaftaran.classList.remove('text-slate-600', 'hover:bg-white/50');

            btnPreview2.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm', 'active');
            btnPreview2.classList.add('text-slate-600', 'hover:bg-white/50');

            contentPendaftaran.classList.remove('hidden');
            contentPreview2.classList.add('hidden');

            const p2Bar = document.getElementById('floatingP2BatchBar');
            if (p2Bar) p2Bar.classList.remove('show');
            updateFloatingBar();
            renderTable();
        } else {
            btnPreview2.classList.add('bg-white', 'text-indigo-600', 'shadow-sm', 'active');
            btnPreview2.classList.remove('text-slate-600', 'hover:bg-white/50');

            btnPendaftaran.classList.remove('bg-white', 'text-orange-600', 'shadow-sm', 'active');
            btnPendaftaran.classList.add('text-slate-600', 'hover:bg-white/50');

            contentPendaftaran.classList.add('hidden');
            contentPreview2.classList.remove('hidden');

            const p1Bar = document.getElementById('floatingBatchBar');
            if (p1Bar) p1Bar.classList.remove('show');
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
        if (cat === 'nama') return 'Cari nama mahasiswa (misal: Budi)...';
        if (cat === 'nim') return 'Cari NIM (misal: 1301210045)...';
        if (cat === 'judul') return 'Cari topik atau judul TA...';
        if (cat === 'konsentrasi') return 'Cari bidang/konsentrasi (misal: AI, Cyber)...';
        return 'Cari Nama, NIM, Judul TA, Tahap...';
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
                    <input type="text" id="extraInput_${rowId}" oninput="handleUnifiedMultiSearch()" placeholder="${getPlaceholderForCategory(defaultCrit)}" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
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
        handleUnifiedMultiSearch();
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
        handleUnifiedMultiSearch();
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
        handleUnifiedMultiSearch();
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
        handleUnifiedMultiSearch();
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
            bar.classList.add('show');
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
            bar.classList.remove('show');
        }
    }

    // =========================================================
    // 3. BATCH APPROVAL MODAL & DOSEN AUTOCOMPLETE
    // =========================================================
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

        const listEl = document.getElementById('modalSelectedList');
        if (listEl) {
            let html = '';
            state.selectedStudents.forEach(st => {
                html += `
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-xs shrink-0">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">${escapeHtml(st.name)}</p>
                                <p class="text-[10px] text-slate-500 font-mono">NIM: ${st.nim} &bull; ${escapeHtml(st.stage)}</p>
                            </div>
                        </div>
                        <button type="button" onclick="removeStudentFromBatch('${st.nim}')" class="text-slate-400 hover:text-rose-600 p-1 transition" title="Hapus dari daftar pilihan">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                `;
            });
            listEl.innerHTML = html;
        }

        const countBadge = document.getElementById('modalSelectedCountBadge');
        if (countBadge) countBadge.innerText = `${state.selectedStudents.size} Mahasiswa`;

        setBatchDecisionStatus(defaultStatus);
        resetComboboxSlot(1);
        resetComboboxSlot(2);

        document.body.classList.add('overflow-hidden');
        modal.classList.remove('hidden');
    };

    window.closeBatchModal = function () {
        const modal = document.getElementById('batchApprovalModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    };

    window.removeStudentFromBatch = function (nim) {
        state.selectedStudents.delete(nim);
        const cb = document.querySelector(`.row-select-checkbox[value="${nim}"]`);
        if (cb) cb.checked = false;

        if (state.selectedStudents.size === 0) {
            closeBatchModal();
        } else {
            window.openBatchModal();
        }
        updateFloatingBar();
    };

    window.setBatchDecisionStatus = function (status) {
        const hiddenStatus = document.getElementById('batchStatusInput');
        if (hiddenStatus) hiddenStatus.value = status;

        const optApprove = document.getElementById('modalOptApprove');
        const optReject = document.getElementById('modalOptReject');
        const secDosen = document.getElementById('modalSectionDosen');
        const btnSubmit = document.getElementById('modalBtnSubmit');
        const btnSubmitText = document.getElementById('modalBtnSubmitText');

        if (status === 'Approved') {
            if (optApprove) optApprove.className = 'flex items-center gap-2.5 p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50 text-emerald-950 font-bold text-xs cursor-pointer shadow-xs';
            if (optReject) optReject.className = 'flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-medium text-xs cursor-pointer hover:bg-slate-50 shadow-xs';
            if (secDosen) secDosen.classList.remove('hidden');
            if (btnSubmit) btnSubmit.className = 'px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-2';
            if (btnSubmitText) btnSubmitText.innerText = `Setujui & Plot Dosen (${state.selectedStudents.size} Mahasiswa)`;
        } else {
            if (optApprove) optApprove.className = 'flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-medium text-xs cursor-pointer hover:bg-slate-50 shadow-xs';
            if (optReject) optReject.className = 'flex items-center gap-2.5 p-3 rounded-xl border-2 border-rose-500 bg-rose-50 text-rose-950 font-bold text-xs cursor-pointer shadow-xs';
            if (secDosen) secDosen.classList.add('hidden');
            if (btnSubmit) btnSubmit.className = 'px-6 py-2.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold text-xs rounded-xl shadow-md shadow-rose-600/20 transition cursor-pointer flex items-center gap-2';
            if (btnSubmitText) btnSubmitText.innerText = `Tolak / Minta Revisi (${state.selectedStudents.size} Mahasiswa)`;
        }
    };

    function getDosenByNip(nip) {
        return (cfg.dosenList || []).find(d => String(d.nip) === String(nip));
    }

    function renderModalDosenDropdown(slotNum, searchKeyword) {
        const dropdown = document.getElementById(`modalDropdownList${slotNum}`);
        if (!dropdown) return;

        const currentNipInput = document.getElementById(`modalInputPembimbing${slotNum}`);
        const otherSlotNum = slotNum === 1 ? 2 : 1;
        const otherNipInput = document.getElementById(`modalInputPembimbing${otherSlotNum}`);

        const currentNip = currentNipInput ? currentNipInput.value : '';
        const otherNip = otherNipInput ? otherNipInput.value : '';

        const kw = (searchKeyword || '').toLowerCase().trim();

        const filtered = (cfg.dosenList || []).filter(d => {
            const name = (d.nama_dosen || '').toLowerCase();
            const nip = String(d.nip || '');
            const prodi = (d.prodi || '').toLowerCase();
            return name.includes(kw) || nip.includes(kw) || prodi.includes(kw);
        });

        if (filtered.length === 0) {
            dropdown.innerHTML = `
                <div class="p-4 text-center text-xs text-slate-400 font-medium">
                    <i class="fa-solid fa-user-slash text-base mb-1 block opacity-60"></i>
                    Tidak ada dosen yang cocok dengan "<strong>${escapeHtml(searchKeyword)}</strong>"
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(d => {
            const isSelected = String(d.nip) === String(currentNip);
            const isUsedByOther = String(d.nip) === String(otherNip);

            const parts = (d.nama_dosen || '').split(' ');
            const initials = parts.slice(0, 2).map(p => p[0] || '').join('').toUpperCase() || 'DS';

            let itemClass = 'p-3 flex items-center justify-between gap-3 cursor-pointer transition hover:bg-orange-50/80 rounded-xl';
            if (isSelected) {
                itemClass += ' bg-orange-50 text-orange-950 font-bold';
            }
            if (isUsedByOther) {
                itemClass = 'p-3 flex items-center justify-between gap-3 opacity-40 cursor-not-allowed bg-slate-50 rounded-xl';
            }

            const clickHandler = isUsedByOther ? '' : `onclick="selectModalDosen(${slotNum}, '${d.nip}')"`;

            html += `
                <div class="${itemClass}" ${clickHandler}>
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg ${isSelected ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-700'} flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                            ${initials}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">${escapeHtml(d.nama_dosen)}</p>
                            <p class="text-[10px] text-slate-400 font-mono">NIP: ${d.nip}</p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        ${isSelected ? '<i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>' : ''}
                        ${isUsedByOther ? `<span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded font-semibold">Dipilih sbg Pemb. ${otherSlotNum}</span>` : ''}
                    </div>
                </div>
            `;
        });

        dropdown.innerHTML = html;
    }

    window.openModalDosenDropdown = function (slotNum) {
        const otherSlot = slotNum === 1 ? 2 : 1;
        const otherDropdown = document.getElementById(`modalDropdownList${otherSlot}`);
        if (otherDropdown) otherDropdown.classList.add('hidden');

        const dropdown = document.getElementById(`modalDropdownList${slotNum}`);
        const input = document.getElementById(`modalSearchP${slotNum}`);
        if (dropdown) {
            renderModalDosenDropdown(slotNum, input ? input.value : '');
            dropdown.classList.remove('hidden');
        }
    };

    window.filterModalDosen = function (slotNum) {
        const input = document.getElementById(`modalSearchP${slotNum}`);
        const clearBtn = document.getElementById(`modalClearP${slotNum}`);
        const kw = input ? input.value : '';

        if (clearBtn) {
            if (kw) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        renderModalDosenDropdown(slotNum, kw);
    };

    window.clearModalSearch = function (slotNum) {
        const input = document.getElementById(`modalSearchP${slotNum}`);
        const clearBtn = document.getElementById(`modalClearP${slotNum}`);
        if (input) {
            input.value = '';
            input.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        renderModalDosenDropdown(slotNum, '');
    };

    window.selectModalDosen = function (slotNum, nip) {
        const hiddenInput = document.getElementById(`modalInputPembimbing${slotNum}`);
        const searchContainer = document.getElementById(`modalSearchContainer${slotNum}`);
        const chip = document.getElementById(`modalChipP${slotNum}`);
        const chipName = document.getElementById(`modalChipNameP${slotNum}`);
        const dropdown = document.getElementById(`modalDropdownList${slotNum}`);

        const dosen = getDosenByNip(nip);
        if (!dosen) return;

        if (hiddenInput) hiddenInput.value = nip;
        if (chipName) chipName.innerText = `${dosen.nama_dosen} (${dosen.nip})`;

        if (searchContainer) searchContainer.classList.add('hidden');
        if (chip) chip.classList.remove('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    };

    window.changeModalDosen = function (slotNum) {
        const searchContainer = document.getElementById(`modalSearchContainer${slotNum}`);
        const searchInput = document.getElementById(`modalSearchP${slotNum}`);
        const chip = document.getElementById(`modalChipP${slotNum}`);
        const clearBtn = document.getElementById(`modalClearP${slotNum}`);

        if (chip) chip.classList.add('hidden');
        if (searchContainer) searchContainer.classList.remove('hidden');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        openModalDosenDropdown(slotNum);
    };

    function resetComboboxSlot(slotNum) {
        const hiddenInput = document.getElementById(`modalInputPembimbing${slotNum}`);
        const searchContainer = document.getElementById(`modalSearchContainer${slotNum}`);
        const searchInput = document.getElementById(`modalSearchP${slotNum}`);
        const chip = document.getElementById(`modalChipP${slotNum}`);
        const dropdown = document.getElementById(`modalDropdownList${slotNum}`);
        const clearBtn = document.getElementById(`modalClearP${slotNum}`);

        if (hiddenInput) hiddenInput.value = '';
        if (searchInput) searchInput.value = '';
        if (clearBtn) clearBtn.classList.add('hidden');
        if (chip) chip.classList.add('hidden');
        if (searchContainer) searchContainer.classList.remove('hidden');
        if (dropdown) dropdown.classList.add('hidden');
    }

    window.submitBatchApproval = function (e) {
        e.preventDefault();

        const form = document.getElementById('formBatchApproval');
        if (!form) return;

        const formData = new FormData(form);
        const status = formData.get('status') || 'Approved';
        const p1 = formData.get('pembimbing_1');
        const p2 = formData.get('pembimbing_2');
        const catatan = (formData.get('catatan_koor') || '').trim();

        const nims = Array.from(state.selectedStudents.keys());

        if (nims.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Mahasiswa',
                text: 'Silakan pilih setidaknya satu mahasiswa.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        if (status === 'Approved') {
            if (!p1 || !p2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dosen Pembimbing Belum Lengkap',
                    text: 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih untuk approval.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
            if (p1 === p2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Dosen Pembimbing Sama',
                    text: 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 tidak boleh orang yang sama!',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
        }

        if (status === 'Rejected' && !catatan) {
            Swal.fire({
                icon: 'warning',
                title: 'Catatan Penolakan Wajib',
                text: 'Silakan isi alasan penolakan pada kolom catatan.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        const isApprove = (status === 'Approved');
        Swal.fire({
            title: isApprove ? `Setujui ${nims.length} Mahasiswa?` : `Tolak ${nims.length} Mahasiswa?`,
            text: isApprove 
                ? `Pendaftaran ${nims.length} mahasiswa akan disetujui dan diberikan Dosen Pembimbing yang sama.`
                : `${nims.length} mahasiswa akan menerima status penolakan beserta catatan revisi.`,
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#059669' : '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: isApprove ? 'Ya, Setujui Semua' : 'Ya, Tolak Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Batch Approval...',
                    text: `Sedang memproses ${nims.length} data mahasiswa...`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                formData.append('nims', JSON.stringify(nims));

                fetch(cfg.ajaxBatchUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Batch Approval Berhasil!',
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
        if (cat === 'nama') return 'Cari nama mahasiswa (misal: Budi)...';
        if (cat === 'nim') return 'Cari NIM (misal: 1301210045)...';
        if (cat === 'judul') return 'Cari topik atau judul TA...';
        if (cat === 'pembimbing') return 'Cari nama/NIP dosen pembimbing...';
        if (cat === 'penguji') return 'Cari nama/NIP dosen penguji...';
        if (cat === 'ruangan') return 'Cari ruangan sidang (misal: Aula, Lab)...';
        return 'Cari Nama, NIM, Judul TA, Dosen Penguji, Ruangan...';
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
                    <input type="text" id="extraP2Input_${rowId}" oninput="handleUnifiedMultiSearchP2()" placeholder="${getPlaceholderForP2Category(defaultCrit)}" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
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
        handleUnifiedMultiSearchP2();
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
        handleUnifiedMultiSearchP2();
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
        handleUnifiedMultiSearchP2();
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
        handleUnifiedMultiSearchP2();
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
                    <td colspan="9" class="py-12 px-4 text-center text-slate-400">
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
            if (statusP2 === 'Terjadwal') {
                statusBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold text-[10px] rounded-full border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-2xs whitespace-nowrap"><i class="fa-solid fa-calendar-check text-[10px]"></i> Terjadwal</span>`;
            } else if (statusP2 === 'Penguji Ditetapkan') {
                statusBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 font-bold text-[10px] rounded-full border border-sky-300 bg-sky-50 text-sky-700 shadow-2xs whitespace-nowrap"><i class="fa-solid fa-user-check text-[10px]"></i> Penguji Siap</span>`;
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

            let jadwalHtml = '';
            if (mhs.tgl_sidang && mhs.ruangan_sidang) {
                jadwalHtml = `
                    <div class="space-y-0.5 text-center text-[10px] leading-tight">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-bold block truncate max-w-[110px] mx-auto"><i class="fa-regular fa-calendar text-[9px]"></i> ${escapeHtml(mhs.tgl_sidang)}</span>
                        <span class="text-slate-500 block">${escapeHtml(mhs.jam_mulai_sidang || '08:00')} - ${escapeHtml(mhs.jam_selesai_sidang || '10:00')}</span>
                        <span class="font-semibold text-indigo-600 truncate block max-w-[110px] mx-auto" title="${escapeHtml(mhs.ruangan_sidang)}"><i class="fa-solid fa-door-open text-[8px]"></i> ${escapeHtml(mhs.ruangan_sidang)}</span>
                    </div>
                `;
            } else {
                jadwalHtml = `<span class="text-slate-400 italic text-[11px] text-center block">-</span>`;
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
                    <td class="w-28 py-3 px-2 text-center">${jadwalHtml}</td>
                    <td class="w-24 py-3 px-2 text-center">${statusBadge}</td>
                    <td class="w-32 py-3 px-3 pr-4 text-right">
                        <button type="button" onclick="openP2SingleModal('${mhs.nim}')" class="btn-3d-kinetic btn-indigo btn-compact ml-auto cursor-pointer" title="Plot Dosen Penguji & Jadwal Sidang">
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
                                    <span class="char state-1">${renderAnimatedChars('Plot Penguji')}</span>
                                    <span class="char state-2">${renderAnimatedChars('Set Jadwal')}</span>
                                    <i class="fa-solid fa-arrow-right icon-action"></i>
                                </div>
                            </div>
                        </button>
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
            bar.classList.add('show');
        } else {
            bar.classList.remove('show');
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

    // Modal Handlers
    window.openP2SingleModal = function (nim) {
        state.p2TargetNim = nim;
        const mhs = state.p2List.find(m => m.nim === nim);
        if (!mhs) return;

        const listContainer = document.getElementById('p2ModalSelectedList');
        const badge = document.getElementById('p2ModalSelectedCountBadge');
        if (badge) badge.textContent = '1 Mahasiswa';

        const fullName = `${mhs.nama_depan || ''} ${mhs.nama_belakang || ''}`.trim();
        const pemb1Name = mhs.nama_pembimbing_1 || mhs.pembimbing_1 || '-';
        const pemb2Name = mhs.nama_pembimbing_2 || mhs.pembimbing_2 || '-';

        if (listContainer) {
            listContainer.innerHTML = `
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[11px] shrink-0">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800">${escapeHtml(fullName)} <span class="font-normal text-slate-500 font-mono">(${mhs.nim})</span></p>
                                <p class="text-[10px] text-slate-500 truncate max-w-sm">${escapeHtml(mhs.judul_1 || '')}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200 shrink-0">${escapeHtml(mhs.status_preview2 || 'Belum Diplot')}</span>
                    </div>

                    <!-- Dosen Pembimbing Banner -->
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200/80 text-[11px]">
                        <span class="font-bold text-slate-500 text-[10px] uppercase tracking-wider flex items-center gap-1">
                            <i class="fa-solid fa-user-tie text-orange-600"></i> Pembimbing:
                        </span>
                        <span class="px-2 py-0.5 rounded-lg bg-orange-50 border border-orange-200 text-orange-800 font-semibold">
                            <strong class="font-bold">1:</strong> ${escapeHtml(pemb1Name)}
                        </span>
                        <span class="px-2 py-0.5 rounded-lg bg-orange-50 border border-orange-200 text-orange-800 font-semibold">
                            <strong class="font-bold">2:</strong> ${escapeHtml(pemb2Name)}
                        </span>
                    </div>
                </div>
            `;
        }

        if (mhs.penguji_1) {
            selectP2ModalDosen(1, mhs.penguji_1, mhs.nama_penguji_1 || mhs.penguji_1);
        } else {
            clearP2ModalDosen(1);
        }

        if (mhs.penguji_2) {
            selectP2ModalDosen(2, mhs.penguji_2, mhs.nama_penguji_2 || mhs.penguji_2);
        } else {
            clearP2ModalDosen(2);
        }

        if (mhs.ruangan_sidang) {
            const list = getRuanganList();
            const r = list.find(x => (x.nama_ruangan || '').toLowerCase() === (mhs.ruangan_sidang || '').toLowerCase());
            if (r) {
                selectP2ModalRuangan(r.nama_ruangan, r.kode_ruangan, r.lokasi, r.kapasitas);
            } else {
                selectP2ModalRuangan(mhs.ruangan_sidang, '', '', 30);
            }
        } else {
            clearP2ModalRuangan();
        }

        if (mhs.tgl_sidang) {
            if (p2DatePickerInstance) {
                p2DatePickerInstance.setDate(mhs.tgl_sidang, true);
            } else {
                const tglEl = document.getElementById('p2ModalTanggal');
                if (tglEl) tglEl.value = mhs.tgl_sidang;
            }
        } else {
            if (p2DatePickerInstance) p2DatePickerInstance.clear();
            else {
                const tglEl = document.getElementById('p2ModalTanggal');
                if (tglEl) tglEl.value = '';
            }
        }

        const jamMulaiEl = document.getElementById('p2ModalJamMulai');
        const jamSelesaiEl = document.getElementById('p2ModalJamSelesai');
        if (jamMulaiEl) jamMulaiEl.value = mhs.jam_mulai_sidang || '';
        if (jamSelesaiEl) jamSelesaiEl.value = mhs.jam_selesai_sidang || '';
        closeP2InlineTimePicker();

        const modal = document.getElementById('modalPreview2Plotting');
        if (modal) modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
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
        const listContainer = document.getElementById('p2ModalSelectedList');
        const badge = document.getElementById('p2ModalSelectedCountBadge');
        if (badge) badge.textContent = `${state.p2SelectedStudents.size} Mahasiswa`;

        if (listContainer) {
            listContainer.innerHTML = Array.from(state.p2SelectedStudents.values()).map(s => {
                const m = state.p2List.find(x => x.nim === s.nim);
                const p1 = m ? (m.nama_pembimbing_1 || m.pembimbing_1 || '-') : '-';
                const p2 = m ? (m.nama_pembimbing_2 || m.pembimbing_2 || '-') : '-';

                return `
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[11px] shrink-0">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800">${escapeHtml(s.name)} <span class="font-normal text-slate-500 font-mono">(${s.nim})</span></p>
                                    <p class="text-[10px] text-slate-500 truncate max-w-sm">${escapeHtml(s.judul)}</p>
                                </div>
                            </div>
                            <button type="button" onclick="removeStudentFromP2Batch('${s.nim}')" class="text-slate-400 hover:text-rose-600 p-1 transition cursor-pointer shrink-0" title="Hapus dari daftar pilihan">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                        <div class="text-[10px] text-slate-500 flex items-center gap-2 pt-1 border-t border-slate-100">
                            <span>Pemb. 1: <strong class="text-orange-700 font-semibold">${escapeHtml(p1)}</strong></span>
                            <span>|</span>
                            <span>Pemb. 2: <strong class="text-orange-700 font-semibold">${escapeHtml(p2)}</strong></span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        clearP2ModalDosen(1);
        clearP2ModalDosen(2);

        clearP2ModalRuangan();
        if (p2DatePickerInstance) p2DatePickerInstance.clear();
        const tglEl = document.getElementById('p2ModalTanggal');
        const jamMulaiEl = document.getElementById('p2ModalJamMulai');
        const jamSelesaiEl = document.getElementById('p2ModalJamSelesai');
        if (tglEl) tglEl.value = '';
        if (jamMulaiEl) jamMulaiEl.value = '';
        if (jamSelesaiEl) jamSelesaiEl.value = '';
        closeP2InlineTimePicker();

        const modal = document.getElementById('modalPreview2Plotting');
        if (modal) modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    window.closeP2Modal = function () {
        const modal = document.getElementById('modalPreview2Plotting');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        closeP2InlineTimePicker();
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

        const p1 = document.getElementById('p2ModalInputPenguji1').value;
        const p2 = document.getElementById('p2ModalInputPenguji2').value;
        const tgl = document.getElementById('p2ModalTanggal').value;
        const jamMulai = document.getElementById('p2ModalJamMulai').value;
        const jamSelesai = document.getElementById('p2ModalJamSelesai').value;
        const ruangan = document.getElementById('p2ModalRuangan').value;

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

        if (jamMulai && jamSelesai && jamMulai >= jamSelesai) {
            Swal.fire({
                icon: 'warning',
                title: 'Rentang Waktu Tidak Valid',
                text: 'Jam Selesai Sidang harus lebih besar dari Jam Mulai Sidang!',
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
        formData.append('tgl_sidang', tgl);
        formData.append('jam_mulai_sidang', jamMulai);
        formData.append('jam_selesai_sidang', jamSelesai);
        formData.append('ruangan_sidang', ruangan);

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
                    text: res.message || 'Penetapan Dosen Penguji & Jadwal Sidang berhasil disimpan!',
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
            } else {
                isSyncing = false;
            }
        }, 4000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateFilterBadge();
        renderTable();
        renderP2Table();
        initP2DatePicker();
        initP2ClockEvents();
        initP2SlotDragEvents();
        startRealtimeSync();
    });

})();
