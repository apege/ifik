/**
 * =========================================================
 * KOORDINATOR TA - DETAIL MAHASISWA & DOCK ENGINE
 * Modular JavaScript Engine for:
 * 1. macOS Vertical Floating Dock & Tab Switcher
 * 2. Search Autocomplete Combobox for Lecturers (Pembimbing 1 & 2)
 * 3. Approval Decision Toggle & AJAX Submission
 * 4. Realtime Live Background Auto-Sync
 * =========================================================
 */

(function () {
    'use strict';

    // Global Config check
    const config = window.KOOR_CONFIG || {
        nim: '',
        ajaxApprovalUrl: '',
        ajaxRealtimeUrl: '',
        stWali: 'Pending',
        stAdmin: 'Pending',
        stKoor: 'Pending',
        stKk: 'Pending',
        activeStageNum: 1,
        tahapTerakhir: 'Dosen Wali',
        isWaliApproved: false,
        isLAAApproved: false,
        dosenList: [],
        initialP1: '',
        initialP2: ''
    };

    // Helper: Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // =========================================================
    // 1. DOCK & TAB NAVIGATION MODULE
    // =========================================================
    window.switchDockTab = function (tabIndex) {
        // Update Dock Buttons
        document.querySelectorAll('.dock-item').forEach((btn, idx) => {
            if (idx === tabIndex) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Update Tab Panels
        document.querySelectorAll('.tab-panel').forEach((panel, idx) => {
            if (idx === tabIndex) {
                panel.classList.add('active');
            } else {
                panel.classList.remove('active');
            }
        });
    };

    function initDockPhysics() {
        const dock = document.querySelector('.vertical-floating-dock');
        const items = document.querySelectorAll('.dock-item');
        if (!dock || !items.length) return;

        const INFLUENCE_DIST = 80; // px
        const MAX_SCALE = 1.28; // 1.28x clean scale

        let targetPos = Infinity;
        let isHovered = false;

        dock.addEventListener('mousemove', (e) => {
            isHovered = true;
            const isVertical = window.innerWidth >= 768;
            targetPos = isVertical ? e.clientY : e.clientX;
            applyMagnification(isVertical);
        });

        dock.addEventListener('mouseleave', () => {
            isHovered = false;
            targetPos = Infinity;
            resetMagnification();
        });

        function applyMagnification(isVertical) {
            if (!isHovered) return;

            items.forEach(item => {
                const rect = item.getBoundingClientRect();
                const itemCenter = isVertical 
                    ? (rect.top + rect.height / 2) 
                    : (rect.left + rect.width / 2);
                const distance = Math.abs(targetPos - itemCenter);

                if (distance < INFLUENCE_DIST) {
                    const factor = Math.cos((distance / INFLUENCE_DIST) * (Math.PI / 2));
                    const scale = 1 + (MAX_SCALE - 1) * Math.pow(factor, 1.25);
                    item.style.transform = `scale(${scale.toFixed(3)})`;
                    item.style.zIndex = '20';
                } else {
                    item.style.transform = 'scale(1)';
                    item.style.zIndex = '1';
                }
            });
        }

        function resetMagnification() {
            items.forEach(item => {
                item.style.transform = 'scale(1)';
                item.style.zIndex = '1';
            });
        }
    }

    // =========================================================
    // 2. DOSEN AUTOCOMPLETE COMBOBOX MODULE
    // =========================================================
    function getDosenByNip(nip) {
        return (config.dosenList || []).find(d => String(d.nip) === String(nip));
    }

    function renderDosenDropdown(slotNum, searchKeyword) {
        const dropdown = document.getElementById(`dropdownList${slotNum}`);
        if (!dropdown) return;

        const currentNipInput = document.getElementById(`inputPembimbing${slotNum}`);
        const otherSlotNum = slotNum === 1 ? 2 : 1;
        const otherNipInput = document.getElementById(`inputPembimbing${otherSlotNum}`);

        const currentNip = currentNipInput ? currentNipInput.value : '';
        const otherNip = otherNipInput ? otherNipInput.value : '';

        const kw = (searchKeyword || '').toLowerCase().trim();
        const filtered = (config.dosenList || []).filter(d => {
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

            // Initials
            const parts = (d.nama_dosen || '').split(' ');
            const initials = parts.slice(0, 2).map(p => p[0] || '').join('').toUpperCase() || 'DS';

            let itemClass = 'p-3 flex items-center justify-between gap-3 cursor-pointer transition hover:bg-orange-50/80';
            if (isSelected) {
                itemClass += ' bg-orange-50/90 text-orange-950 font-bold';
            }
            if (isUsedByOther) {
                itemClass = 'p-3 flex items-center justify-between gap-3 opacity-40 cursor-not-allowed bg-slate-50';
            }

            const clickHandler = isUsedByOther ? '' : `onclick="selectDosen(${slotNum}, '${d.nip}')"`;

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

    window.openDosenDropdown = function (slotNum) {
        const dropdown = document.getElementById(`dropdownList${slotNum}`);
        const input = document.getElementById(`searchP${slotNum}`);
        const otherSlot = slotNum === 1 ? 2 : 1;

        // Close other dropdown
        const otherDropdown = document.getElementById(`dropdownList${otherSlot}`);
        if (otherDropdown) otherDropdown.classList.add('hidden');

        if (dropdown) {
            renderDosenDropdown(slotNum, input ? input.value : '');
            dropdown.classList.remove('hidden');
        }
    };

    window.filterDosen = function (slotNum) {
        const input = document.getElementById(`searchP${slotNum}`);
        const clearBtn = document.getElementById(`clearP${slotNum}`);
        const kw = input ? input.value : '';

        if (clearBtn) {
            if (kw) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        renderDosenDropdown(slotNum, kw);
    };

    window.selectDosen = function (slotNum, nip) {
        const hiddenInput = document.getElementById(`inputPembimbing${slotNum}`);
        const searchContainer = document.getElementById(`searchContainer${slotNum}`);
        const chip = document.getElementById(`chipP${slotNum}`);
        const chipName = document.getElementById(`chipNameP${slotNum}`);
        const chipNip = document.getElementById(`chipNipP${slotNum}`);
        const badge = document.getElementById(`badgeP${slotNum}`);
        const dropdown = document.getElementById(`dropdownList${slotNum}`);

        const dosen = getDosenByNip(nip);
        if (!dosen) return;

        if (hiddenInput) hiddenInput.value = nip;
        if (chipName) chipName.innerText = dosen.nama_dosen;
        if (chipNip) chipNip.innerText = `NIP: ${dosen.nip}`;

        if (searchContainer) searchContainer.classList.add('hidden');
        if (chip) chip.classList.remove('hidden');
        if (badge) badge.classList.remove('hidden');
        if (dropdown) dropdown.classList.add('hidden');

        validatePembimbingDistinct();
    };

    window.changeDosen = function (slotNum) {
        const searchContainer = document.getElementById(`searchContainer${slotNum}`);
        const searchInput = document.getElementById(`searchP${slotNum}`);
        const chip = document.getElementById(`chipP${slotNum}`);
        const badge = document.getElementById(`badgeP${slotNum}`);

        if (chip) chip.classList.add('hidden');
        if (badge) badge.classList.add('hidden');
        if (searchContainer) searchContainer.classList.remove('hidden');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        window.openDosenDropdown(slotNum);
    };

    window.clearDosenSelection = function (slotNum) {
        const hiddenInput = document.getElementById(`inputPembimbing${slotNum}`);
        const searchInput = document.getElementById(`searchP${slotNum}`);
        const clearBtn = document.getElementById(`clearP${slotNum}`);
        const badge = document.getElementById(`badgeP${slotNum}`);

        if (hiddenInput) hiddenInput.value = '';
        if (badge) badge.classList.add('hidden');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        renderDosenDropdown(slotNum, '');
        validatePembimbingDistinct();
    };

    function validatePembimbingDistinct() {
        const p1Input = document.getElementById('inputPembimbing1');
        const p2Input = document.getElementById('inputPembimbing2');
        const warning = document.getElementById('pembimbingConflictWarning');

        const p1 = p1Input ? p1Input.value : '';
        const p2 = p2Input ? p2Input.value : '';

        if (p1 && p2 && String(p1) === String(p2)) {
            if (warning) warning.classList.remove('hidden');
            return false;
        } else {
            if (warning) warning.classList.add('hidden');
            return true;
        }
    }

    // Close combobox when clicking outside
    document.addEventListener('click', (e) => {
        const wrap1 = document.getElementById('comboboxWrapper1');
        const wrap2 = document.getElementById('comboboxWrapper2');
        const drop1 = document.getElementById('dropdownList1');
        const drop2 = document.getElementById('dropdownList2');

        if (wrap1 && !wrap1.contains(e.target) && drop1) {
            drop1.classList.add('hidden');
        }
        if (wrap2 && !wrap2.contains(e.target) && drop2) {
            drop2.classList.add('hidden');
        }
    });

    // =========================================================
    // 3. APPROVAL DECISION & AJAX SUBMISSION MODULE
    // =========================================================
    window.onStatusDecisionChange = function (status) {
        const secPem = document.getElementById('sectionPembimbing');
        const optApprove = document.getElementById('labelOptApprove');
        const optReject = document.getElementById('labelOptReject');
        const indicatorApprove = document.getElementById('indicatorApprove');
        const indicatorReject = document.getElementById('indicatorReject');
        const catatanLabel = document.getElementById('catatanLabel');
        const catatanTextarea = document.getElementById('catatanKoor');
        const btnSubmit = document.getElementById('btnSubmitApproval');
        const btnSubmitText = document.getElementById('btnSubmitText');

        if (status === 'Approved') {
            if (secPem) secPem.classList.remove('hidden');
            if (optApprove) optApprove.className = 'relative flex items-center justify-between p-4 sm:p-5 rounded-2xl cursor-pointer transition-all duration-200 border-2 border-emerald-500 bg-gradient-to-br from-emerald-50/90 to-teal-50/40 ring-4 ring-emerald-500/10 shadow-lg shadow-emerald-500/10';
            if (optReject) optReject.className = 'relative flex items-center justify-between p-4 sm:p-5 rounded-2xl cursor-pointer transition-all duration-200 border border-slate-200 bg-white hover:bg-slate-50 hover:-translate-y-0.5 shadow-xs';
            
            if (indicatorApprove) indicatorApprove.innerHTML = `<div class="w-6 h-6 rounded-full bg-emerald-600 text-white shadow-sm flex items-center justify-center text-xs transition"><i class="fa-solid fa-check"></i></div>`;
            if (indicatorReject) indicatorReject.innerHTML = `<div class="w-6 h-6 rounded-full border-2 border-slate-300 bg-white text-transparent flex items-center justify-center text-xs transition"><i class="fa-solid fa-xmark"></i></div>`;
            
            if (catatanLabel) catatanLabel.innerHTML = 'Catatan Koordinator TA:';
            if (catatanTextarea) catatanTextarea.placeholder = 'Masukkan catatan usulan judul atau arahan untuk mahasiswa/pembimbing...';

            if (btnSubmit) btnSubmit.className = 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold px-6 py-3 rounded-xl text-xs shadow-md shadow-emerald-600/20 inline-flex items-center gap-2 transition-all hover:-translate-y-0.5 active:translate-y-0 cursor-pointer';
            if (btnSubmitText) btnSubmitText.innerText = 'Simpan Keputusan Approve & Dosen Pembimbing';
        } else {
            if (secPem) secPem.classList.add('hidden');
            if (optApprove) optApprove.className = 'relative flex items-center justify-between p-4 sm:p-5 rounded-2xl cursor-pointer transition-all duration-200 border border-slate-200 bg-white hover:bg-slate-50 hover:-translate-y-0.5 shadow-xs';
            if (optReject) optReject.className = 'relative flex items-center justify-between p-4 sm:p-5 rounded-2xl cursor-pointer transition-all duration-200 border-2 border-rose-500 bg-gradient-to-br from-rose-50/90 to-pink-50/40 ring-4 ring-rose-500/10 shadow-lg shadow-rose-500/10';
            
            if (indicatorApprove) indicatorApprove.innerHTML = `<div class="w-6 h-6 rounded-full border-2 border-slate-300 bg-white text-transparent flex items-center justify-center text-xs transition"><i class="fa-solid fa-check"></i></div>`;
            if (indicatorReject) indicatorReject.innerHTML = `<div class="w-6 h-6 rounded-full bg-rose-600 text-white shadow-sm flex items-center justify-center text-xs transition"><i class="fa-solid fa-xmark"></i></div>`;
            
            if (catatanLabel) catatanLabel.innerHTML = 'Catatan Koordinator TA <span class="text-rose-500 font-bold">* (Wajib diisi jika Reject)</span>:';
            if (catatanTextarea) catatanTextarea.placeholder = 'Masukkan alasan penolakan atau revisi yang harus diperbaiki mahasiswa...';

            if (btnSubmit) btnSubmit.className = 'bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold px-6 py-3 rounded-xl text-xs shadow-md shadow-rose-600/20 inline-flex items-center gap-2 transition-all hover:-translate-y-0.5 active:translate-y-0 cursor-pointer';
            if (btnSubmitText) btnSubmitText.innerText = 'Simpan Keputusan Reject (Minta Revisi)';
        }
    };

    window.handleAjaxApproval = function (e) {
        e.preventDefault();

        const form = document.getElementById('formApprovalKoor');
        if (!form) return;

        const formData = new FormData(form);
        const status = formData.get('status');
        const catatan = (formData.get('catatan_koor') || '').trim();
        const p1 = formData.get('pembimbing_1');
        const p2 = formData.get('pembimbing_2');

        // 0. Validasi Dosen Wali
        if (status === 'Approved' && !config.isWaliApproved) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Disetujui Dosen Wali',
                text: 'Mahasiswa ini masih berada di tahap 1 (Dosen Wali). Persetujuan Koordinator TA belum dapat diproses.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        // 1. Validasi LAA
        if (status === 'Approved' && !config.isLAAApproved) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Disetujui Admin LAA',
                text: 'Pendaftaran ini belum disetujui oleh Admin Layanan Akademik (LAA). Mahasiswa harus lulus verifikasi LAA terlebih dahulu.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        // 2. Validasi Pembimbing saat Approve
        if (status === 'Approved') {
            if (!p1 || !p2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dosen Pembimbing Belum Lengkap',
                    text: 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih sebelum menyetujui pendaftaran TA.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            if (p1 === p2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Dosen Pembimbing Duplikat',
                    text: 'Dosen Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama!',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
        }

        // 3. Validasi Catatan saat Reject
        if (status === 'Rejected' && !catatan) {
            const warning = document.getElementById('catatanWarning');
            if (warning) warning.classList.remove('hidden');
            const catatanEl = document.getElementById('catatanKoor');
            if (catatanEl) catatanEl.focus();
            Swal.fire({
                icon: 'warning',
                title: 'Catatan Penolakan Wajib',
                text: 'Silakan isi alasan penolakan atau instruksi revisi pada kolom catatan Koordinator TA.',
                confirmButtonColor: '#ea580c'
            });
            return;
        }

        // 4. Konfirmasi Eksekusi
        const isApprove = (status === 'Approved');
        Swal.fire({
            title: isApprove ? 'Konfirmasi Approval' : 'Konfirmasi Penolakan',
            text: isApprove 
                ? 'Apakah Anda yakin ingin MENYETUJUI usulan judul ini dan menetapkan Dosen Pembimbing?'
                : 'Apakah Anda yakin ingin MENOLAK pendaftaran ini dan mengirim catatan revisi?',
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#059669' : '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: isApprove ? 'Ya, Setujui Sekarang' : 'Ya, Tolak Pendaftaran',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menyimpan keputusan approval ke database...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(config.ajaxApprovalUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Disimpan!',
                            text: data.message,
                            confirmButtonColor: '#ea580c'
                        });

                        // Instantly update live state without page reload
                        config.stKoor = selectedDecision;
                        if (config.ajaxRealtimeUrl) {
                            fetch(config.ajaxRealtimeUrl)
                                .then(res => res.json())
                                .then(latest => {
                                    if (latest && latest.status) {
                                        config.stWali = latest.status_approval_wali;
                                        config.stAdmin = latest.status_approval_admin;
                                        config.stKoor = latest.status_approval_koor;
                                        config.stKk = latest.status_approval_kk;
                                        config.activeStageNum = latest.activeStageNum;
                                        config.tahapTerakhir = latest.tahapTerakhir;
                                        config.isWaliApproved = (latest.status_approval_wali === 'Approved');
                                        config.isLAAApproved = (latest.status_approval_admin === 'Approved');
                                        config.isKoorApproved = (latest.status_approval_koor === 'Approved');

                                        renderStagesGrid(latest);
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
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Jaringan',
                        text: 'Gagal terhubung ke server. Silakan coba lagi.',
                        confirmButtonColor: '#ea580c'
                    });
                });
            }
        });
    };

    // =========================================================
    // 4. REALTIME LIVE AUTO-SYNC MODULE (NO REFRESH)
    // =========================================================
    function renderStagesGrid(state) {
        const grid = document.getElementById('statusGridStages');
        if (!grid) return;

        const stWali = state.status_approval_wali || 'Pending';
        const stAdmin = state.status_approval_admin || 'Pending';
        const stKoor = state.status_approval_koor || 'Pending';
        const stKk = state.status_approval_kk || 'Pending';
        const activeStageNum = state.activeStageNum || 1;

        const isCurrent1 = (activeStageNum === 1);
        const isCurrent2 = (activeStageNum === 2);
        const isCurrent3 = (activeStageNum === 3);
        const isCurrent4 = (activeStageNum === 4);

        const getBg = (st, isCur) => {
            if (st === 'Approved') return 'bg-emerald-50 border-emerald-200 text-emerald-800';
            if (st === 'Rejected') return 'bg-rose-50 border-rose-200 text-rose-800';
            if (isCur) return 'bg-amber-50 border-amber-300 text-amber-800 border-2 ring-2 ring-orange-500/20';
            return 'bg-slate-50 border-slate-200 text-slate-600';
        };

        const getStatusIcon = (st, isCur) => {
            if (st === 'Approved') return '<i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Disetujui';
            if (st === 'Rejected') return '<i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i> Ditolak';
            if (isCur) return '<i class="fa-solid fa-clock text-amber-600 text-xs"></i> Pending';
            return '<i class="fa-solid fa-clock text-slate-400 text-xs"></i> Menunggu';
        };

        const catWali = (state.catatan_wali || '').trim();
        const catAdmin = (state.catatan_admin || '').trim();
        const catKoor = (state.catatan_koor || '').trim();

        grid.innerHTML = `
            <!-- Stage 1: Dosen Wali -->
            <div class="p-3.5 rounded-xl border ${getBg(stWali, isCurrent1)} shadow-xs relative transition-all duration-300">
                ${isCurrent1 ? '<span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>' : ''}
                <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">1. Dosen Wali</span>
                <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                    ${getStatusIcon(stWali, isCurrent1)}
                </div>
                ${catWali ? `<p class="text-[10px] opacity-80 mt-1 italic line-clamp-2" title="${escapeHtml(catWali)}">"${escapeHtml(catWali)}"</p>` : ''}
            </div>

            <!-- Stage 2: Admin Layanan -->
            <div class="p-3.5 rounded-xl border ${getBg(stAdmin, isCurrent2)} shadow-xs relative transition-all duration-300">
                ${isCurrent2 ? '<span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>' : ''}
                <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">2. Admin Layanan</span>
                <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                    ${getStatusIcon(stAdmin, isCurrent2)}
                </div>
                ${catAdmin ? `<p class="text-[10px] opacity-80 mt-1 italic line-clamp-2" title="${escapeHtml(catAdmin)}">"${escapeHtml(catAdmin)}"</p>` : ''}
            </div>

            <!-- Stage 3: Koordinator TA -->
            <div class="p-3.5 rounded-xl border ${getBg(stKoor, isCurrent3)} shadow-xs relative transition-all duration-300">
                ${isCurrent3 ? '<span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>' : ''}
                <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">3. Koordinator TA</span>
                <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                    ${getStatusIcon(stKoor, isCurrent3)}
                </div>
                ${catKoor ? `<p class="text-[10px] opacity-80 mt-1 italic line-clamp-2" title="${escapeHtml(catKoor)}">"${escapeHtml(catKoor)}"</p>` : ''}
            </div>

            <!-- Stage 4: Ketua KK -->
            <div class="p-3.5 rounded-xl border ${getBg(stKk, isCurrent4)} shadow-xs relative transition-all duration-300">
                ${isCurrent4 ? '<span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>' : ''}
                <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">4. Ketua KK</span>
                <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                    ${getStatusIcon(stKk, isCurrent4)}
                </div>
            </div>
        `;

        // Update Tahap Terakhir Badge
        const badge = document.getElementById('tahapTerakhirBadge');
        if (badge) {
            badge.innerText = state.tahapTerakhir || 'Koordinator TA';
        }

        // Update Top Warning Banner
        const topWarn = document.getElementById('topWarningContainer');
        if (topWarn) {
            if (!config.isWaliApproved) {
                topWarn.innerHTML = `
                    <div class="bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-2xl shadow-xs flex items-start gap-3.5 mb-6">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm mt-0.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wide">Menunggu Review Dosen Wali</h4>
                            <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                                Mahasiswa ini masih berada di tahap peninjauan <strong>1. Dosen Wali</strong>. Berkas belum diverifikasi Admin Layanan maupun Koordinator TA.
                            </p>
                        </div>
                    </div>
                `;
            } else if (!config.isLAAApproved) {
                topWarn.innerHTML = `
                    <div class="bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-2xl shadow-xs flex items-start gap-3.5 mb-6">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm mt-0.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wide">Menunggu Approval Admin Layanan (LAA)</h4>
                            <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                                Mahasiswa ini telah disetujui Dosen Wali namun <strong>belum diverifikasi berkas & SKS-nya oleh Admin Layanan (LAA)</strong>. Penetapan Dosen Pembimbing dan persetujuan Koordinator TA baru dapat diproses setelah status Admin LAA <strong>Approved</strong>.
                            </p>
                        </div>
                    </div>
                `;
            } else {
                topWarn.innerHTML = '';
            }
        }
    }

    function checkRealtimeStatus() {
        if (!config.nim || !config.ajaxRealtimeUrl) return;

        fetch(config.ajaxRealtimeUrl)
            .then(res => res.json())
            .then(data => {
                if (!data || !data.status) return;

                const hasChanged = (
                    data.status_approval_wali !== config.stWali ||
                    data.status_approval_admin !== config.stAdmin ||
                    data.status_approval_koor !== config.stKoor ||
                    data.status_approval_kk !== config.stKk ||
                    data.activeStageNum !== config.activeStageNum
                );

                if (hasChanged) {
                    config.stWali = data.status_approval_wali;
                    config.stAdmin = data.status_approval_admin;
                    config.stKoor = data.status_approval_koor;
                    config.stKk = data.status_approval_kk;
                    config.activeStageNum = data.activeStageNum;
                    config.tahapTerakhir = data.tahapTerakhir;
                    config.isWaliApproved = (data.status_approval_wali === 'Approved');
                    config.isLAAApproved = (data.status_approval_admin === 'Approved');

                    renderStagesGrid(data);

                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4500,
                            timerProgressBar: true,
                            background: '#1e293b',
                            color: '#ffffff'
                        });
                        Toast.fire({
                            icon: 'info',
                            title: `⚡ Tahap Berubah: ${data.tahapTerakhir}`
                        });
                    }
                }
            })
            .catch(() => {
                // silent fallback
            });
    }

    // =========================================================
    // 5. PDF PREVIEW POPUP MODAL
    // =========================================================
    window.openPdfPreviewModal = function (url, title) {
        const modal = document.getElementById('pdfPreviewModal');
        const frame = document.getElementById('pdfModalFrame');
        const titleEl = document.getElementById('pdfModalTitle');
        const subEl = document.getElementById('pdfModalSubtitle');

        if (frame) frame.src = url;
        if (titleEl) titleEl.innerText = title || 'Pratinjau Dokumen PDF';
        if (subEl) subEl.innerText = url.split('/').pop() || 'Dokumen Persyaratan';

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };

    window.closePdfPreviewModal = function () {
        const modal = document.getElementById('pdfPreviewModal');
        const frame = document.getElementById('pdfModalFrame');
        if (frame) frame.src = 'about:blank';
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePdfPreviewModal();
        }
    });

    // =========================================================
    // INITIALIZATION ON DOM READY
    // =========================================================
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Initialize Dock Magnification
        initDockPhysics();

        // 2. Hydrate Combobox default selections
        if (config.initialP1) {
            window.selectDosen(1, config.initialP1);
        }
        if (config.initialP2) {
            window.selectDosen(2, config.initialP2);
        }

        // 3. Start background realtime heartbeat sync
        if (config.ajaxRealtimeUrl) {
            setInterval(checkRealtimeStatus, 3500);
        }
    });

})();
