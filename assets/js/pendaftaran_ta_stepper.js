/**
 * JavaScript logic for 3-step Pendaftaran Tugas Akhir (Mahasiswa Module)
 * Handles Stepper Navigation, Validation, Dynamic Step Counter, Interactive PDF Upload Card UI, and High-Visibility In-Page Web Toast Alerts
 */

document.addEventListener('DOMContentLoaded', function () {
    const totalSteps = 3;
    const userNim = window.CURRENT_USER_NIM ? window.CURRENT_USER_NIM.trim() : 'guest';
    const STEP_KEY = 'ifik_ta_active_step_' + userNim;

    let currentStep = 1;

    // Direct navigation support from URL or localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const urlStep = parseInt(urlParams.get('step'));
    const savedStep = parseInt(localStorage.getItem(STEP_KEY));

    if (urlStep && urlStep >= 1 && urlStep <= totalSteps) {
        currentStep = urlStep;
    } else if (savedStep && savedStep >= 1 && savedStep <= totalSteps) {
        currentStep = savedStep;
    }

    const btnNext = document.getElementById('btnNext');
    const btnPrev = document.getElementById('btnPrev');
    const btnSubmit = document.getElementById('btnSubmit');
    const stepCounterText = document.getElementById('stepCounterText');
    const toastAlert = document.getElementById('inPageToastAlert');
    const toastMessage = document.getElementById('toastAlertMessage');
    const btnCloseToast = document.getElementById('btnCloseToast');
    const stepperProgressLine = document.getElementById('stepperProgressLine');

    let toastTimeout;

    // Show High-Visibility In-Page Toast Notification
    function showInPageAlert(message, type = 'warning') {
        if (!toastAlert || !toastMessage) return;

        toastMessage.textContent = message;

        if (type === 'warning' || type === 'error') {
            toastAlert.className = 'fixed top-28 right-6 z-[9999] max-w-lg bg-rose-600 text-white p-4 rounded-2xl shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-0 opacity-100 ring-4 ring-rose-300/50';
        } else {
            toastAlert.className = 'fixed top-28 right-6 z-[9999] max-w-lg bg-emerald-600 text-white p-4 rounded-2xl shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-0 opacity-100 ring-4 ring-emerald-300/50';
        }

        toastAlert.classList.remove('hidden', 'translate-y-[-20px]', 'opacity-0');

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            hideInPageAlert();
        }, 5000);
    }

    function hideInPageAlert() {
        if (!toastAlert) return;
        toastAlert.classList.add('opacity-0', 'translate-y-[-20px]');
        setTimeout(() => {
            toastAlert.classList.add('hidden');
        }, 300);
    }

    if (btnCloseToast) {
        btnCloseToast.addEventListener('click', hideInPageAlert);
    }

    // Populate Summary on Step 3
    function populateSummary() {
        const jenisTA = document.getElementById('inputJenisTA')?.value.trim() || '-';
        const judul1 = document.getElementById('inputJudul1')?.value.trim() || '-';
        const judulEn = document.getElementById('inputJudulEn')?.value.trim() || '-';
        const konsentrasi = document.querySelector('input[name="konsentrasi_dkv"]')?.value.trim() || 'Desain Komunikasi Visual';

        const sumJenis = document.getElementById('summaryJenisTA');
        const sumJudul1 = document.getElementById('summaryJudul1');
        const sumJudulEn = document.getElementById('summaryJudulEn');
        const sumKons = document.getElementById('summaryKonsentrasi');

        if (sumJenis) sumJenis.textContent = jenisTA || '-';
        if (sumJudul1) sumJudul1.textContent = judul1 || '-';
        if (sumJudulEn) sumJudulEn.textContent = judulEn || '-';
        if (sumKons) sumKons.textContent = konsentrasi || 'Desain Komunikasi Visual';

        // Populate Document Status List
        const sumDocList = document.getElementById('summaryDocList');
        if (sumDocList) {
            sumDocList.innerHTML = '';
            const docCards = document.querySelectorAll('.doc-requirement-card');

            docCards.forEach(card => {
                const titleEl = card.querySelector('h4');
                const rawTitle = titleEl ? titleEl.textContent.trim() : 'Dokumen';
                const cleanTitle = rawTitle.replace(/^\d+\.\s*/, '').replace(/\s*(Wajib|Opsional)\s*$/gi, '').trim();

                const fileInput = card.querySelector('.input-doc-file');
                const oldInput = card.querySelector('.input-doc-old');
                const hasFile = (fileInput && fileInput.files && fileInput.files.length > 0) || (oldInput && oldInput.value.trim() !== '');

                const docItem = document.createElement('div');
                docItem.className = `p-2.5 rounded-xl border flex items-center justify-between text-xs font-semibold ${hasFile ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-rose-50/80 border-rose-200 text-rose-900'}`;
                docItem.innerHTML = `
                    <span class="truncate pr-2">${cleanTitle}</span>
                    <span class="text-[10px] uppercase font-bold shrink-0 ${hasFile ? 'text-emerald-700 bg-emerald-100 border border-emerald-300' : 'text-rose-700 bg-rose-100 border border-rose-300'} px-2 py-0.5 rounded-full">
                        ${hasFile ? '<i class="bi bi-check-lg mr-1"></i> Terunggah' : '<i class="bi bi-x-lg mr-1"></i> Belum ada'}
                    </span>
                `;
                sumDocList.appendChild(docItem);
            });
        }
    }

    // Update UI step state
    function updateStepUI() {
        if (currentStep === 3) {
            populateSummary();
        }
        if (stepCounterText) {
            stepCounterText.textContent = `LANGKAH ${currentStep} / ${totalSteps}`;
        }

        if (stepperProgressLine) {
            const pct = ((currentStep - 1) / (totalSteps - 1)) * 100;
            stepperProgressLine.style.width = pct + '%';
        }

        // Toggle Step Views & Stepper Header UI
        for (let i = 1; i <= totalSteps; i++) {
            const stepContent = document.getElementById(`step-content-${i}`);
            const stepItem = document.getElementById(`step-item-${i}`);

            if (stepContent) {
                if (i === currentStep) {
                    stepContent.classList.remove('hidden');
                    stepContent.style.display = 'block';
                } else {
                    stepContent.classList.add('hidden');
                    stepContent.style.display = 'none';
                }
            }

            if (stepItem) {
                const counter = stepItem.querySelector('.step-counter');
                const title = stepItem.querySelector('.step-title');

                stepItem.style.cursor = (i <= currentStep) ? 'pointer' : 'default';
                stepItem.onclick = () => {
                    if (i <= currentStep) {
                        currentStep = i;
                        updateStepUI();
                    }
                };

                if (i === currentStep) {
                    stepItem.classList.add('active');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold flex items-center justify-center text-sm box-3d ring-4 ring-orange-200/80 transition-all duration-300 z-10';
                    }
                    if (title) {
                        title.className = 'step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300';
                    }
                } else if (i < currentStep) {
                }
            }
        }

        // Stepper progress line width
        const progressLine = document.getElementById('stepperProgressLine');
        if (progressLine) {
            const percent = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressLine.style.width = percent + '%';
        }

        // Check actual submission/file completion status of each step:
        function checkStepCompletionStatus(stepIndex) {
            if (stepIndex === 1) {
                const inputJenis = document.getElementById('inputJenisTA');
                const inputJ1 = document.querySelector('input[name="judul_1"]');
                const inputJEn = document.querySelector('input[name="judul_en"]');
                return (inputJenis && inputJenis.value.trim() !== '') &&
                       (inputJ1 && inputJ1.value.trim() !== '') &&
                       (inputJEn && inputJEn.value.trim() !== '');
            } else if (stepIndex === 2) {
                const reqCards = document.querySelectorAll('.doc-requirement-card[data-required="1"]');
                if (reqCards.length === 0) return true;
                let allUploaded = true;
                reqCards.forEach(card => {
                    const fileInput = card.querySelector('.input-doc-file');
                    const oldInput = card.querySelector('.input-doc-old');
                    const hasFile = (fileInput && fileInput.files && fileInput.files.length > 0) || (oldInput && oldInput.value.trim() !== '');
                    if (!hasFile) allUploaded = false;
                });
                return allUploaded;
            } else if (stepIndex === 3) {
                const checkSubmit = document.getElementById('checkKonfirmasiSubmit');
                return checkSubmit ? checkSubmit.checked : false;
            }
            return false;
        }

        // Update Right Sidebar (Progres Pendaftaran)
        let filledCount = 0;
        for (let i = 1; i <= totalSteps; i++) {
            const sideItem = document.getElementById(`side-step-${i}`);
            if (!sideItem) continue;

            const counter = sideItem.querySelector('.side-step-counter');
            const title = sideItem.querySelector('.side-step-title');
            const badge = sideItem.querySelector('.side-step-badge');

            const isFilled = checkStepCompletionStatus(i);
            if (isFilled) filledCount++;

            // Sidebar item clickable to navigate back to previous steps
            sideItem.style.cursor = (i <= currentStep) ? 'pointer' : 'default';
            sideItem.onclick = () => {
                if (i < currentStep) {
                    currentStep = i;
                    updateStepUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            };

            if (i === currentStep) {
                sideItem.classList.remove('opacity-50');
                sideItem.classList.add('opacity-100');
                if (counter) {
                    if (isFilled) {
                        counter.className = 'side-step-counter w-7 h-7 rounded-full bg-emerald-500 text-white font-bold text-xs flex items-center justify-center shadow-xs shrink-0';
                        counter.innerHTML = '<i class="bi bi-check-lg text-xs"></i>';
                    } else {
                        counter.className = 'side-step-counter w-7 h-7 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold text-xs flex items-center justify-center box-3d shrink-0';
                        counter.textContent = i;
                    }
                }
                if (title) title.className = 'side-step-title text-xs font-bold text-slate-900';
                if (badge) {
                    badge.textContent = isFilled ? 'Terisi' : 'Aktif';
                    badge.className = isFilled 
                        ? 'side-step-badge text-[10px] font-bold text-emerald-700 bg-emerald-100/90 px-2.5 py-0.5 rounded-full border border-emerald-200 block'
                        : 'side-step-badge text-[10px] font-bold text-orange-700 bg-orange-100/90 px-2.5 py-0.5 rounded-full border border-orange-200 block';
                }
            } else if (isFilled) {
                sideItem.classList.remove('opacity-50');
                sideItem.classList.add('opacity-100');
                if (counter) {
                    counter.className = 'side-step-counter w-7 h-7 rounded-full bg-emerald-500 text-white font-bold text-xs flex items-center justify-center shadow-xs shrink-0';
                    counter.innerHTML = '<i class="bi bi-check-lg text-xs"></i>';
                }
                if (title) title.className = 'side-step-title text-xs font-semibold text-slate-800';
                if (badge) {
                    badge.textContent = 'Terisi';
                    badge.className = 'side-step-badge text-[10px] font-bold text-emerald-700 bg-emerald-100/90 px-2.5 py-0.5 rounded-full border border-emerald-200 block';
                }
            } else {
                sideItem.classList.add('opacity-50');
                sideItem.classList.remove('opacity-100');
                if (counter) {
                    counter.className = 'side-step-counter w-7 h-7 rounded-full bg-slate-100 text-slate-400 font-semibold text-xs border border-slate-300 flex items-center justify-center shrink-0';
                    counter.textContent = i;
                }
                if (title) title.className = 'side-step-title text-xs font-medium text-slate-400';
                if (badge) {
                    badge.classList.add('hidden');
                }
            }
        }

        // Calculate and update Kelengkapan Percentage Bar based on REAL file uploads
        const completenessPercent = Math.round((filledCount / totalSteps) * 100);
        const percentText = document.getElementById('sidebarCompletenessPercent');
        const percentBar = document.getElementById('sidebarCompletenessBar');

        if (percentText) percentText.textContent = `${completenessPercent}%`;
        if (percentBar) percentBar.style.width = `${completenessPercent}%`;

        // Control Buttons
        if (btnPrev) {
            if (currentStep === 1) {
                btnPrev.innerHTML = '<i class="bi bi-arrow-left text-base"></i> <span>Kembali ke Dashboard</span>';
            } else {
                btnPrev.innerHTML = '<i class="bi bi-arrow-left text-base font-bold"></i> <span>Kembali ke Langkah Sebelumnya</span>';
            }
            btnPrev.classList.remove('hidden');
        }

        if (btnNext && btnSubmit) {
            if (currentStep === totalSteps) {
                btnNext.classList.add('hidden');
                btnSubmit.classList.remove('hidden');
            } else {
                btnNext.classList.remove('hidden');
                btnSubmit.classList.add('hidden');
            }
        }

        try {
            localStorage.setItem(STEP_KEY, currentStep);
        } catch (e) {}
    }

    // Validate current step inputs
    function validateStep(step) {
        const currentContainer = document.getElementById(`step-content-${step}`);
        if (!currentContainer) return true;

        // If form is locked in view-only mode, allow smooth step navigation
        if (currentContainer.closest('fieldset[disabled]')) {
            return true;
        }

        let isValid = true;
        let errorMessage = '';
        const stepAlert = currentContainer.querySelector('.step-inline-alert');
        const stepAlertText = currentContainer.querySelector('.step-inline-alert-text');

        if (step === 1) {
            const inputJenis = document.getElementById('inputJenisTA');
            const inputJ1 = document.querySelector('input[name="judul_1"]');
            const inputJEn = document.querySelector('input[name="judul_en"]');

            if (!inputJenis || !inputJenis.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap pilih Jenis Tugas Akhir pada Langkah 1 terlebih dahulu!';
                const trigger = currentContainer.querySelector('.dropdown-trigger');
                if (trigger) trigger.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
            } else if (!inputJ1 || !inputJ1.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap isi Judul Usulan 1 (Utama) pada Langkah 1!';
                if (inputJ1) inputJ1.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
            } else if (!inputJEn || !inputJEn.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap isi Judul dalam Bahasa Inggris pada Langkah 1 (atau klik Translate Otomatis)!';
                if (inputJEn) inputJEn.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
            } else {
                const trigger = currentContainer.querySelector('.dropdown-trigger');
                if (trigger) trigger.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (inputJ1) inputJ1.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (inputJEn) inputJEn.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
            }
        } else if (step === 2) {
            const reqCards = currentContainer.querySelectorAll('.doc-requirement-card[data-required="1"]');
            let missingDoc = null;
            reqCards.forEach(card => {
                const fileInput = card.querySelector('.input-doc-file');
                const oldInput = card.querySelector('.input-doc-old');
                const hasFile = (fileInput && fileInput.files && fileInput.files.length > 0) || (oldInput && oldInput.value.trim() !== '');
                if (!hasFile && !missingDoc) {
                    const titleEl = card.querySelector('h4');
                    missingDoc = titleEl ? titleEl.textContent.trim().replace(/^\d+\.\s*/, '').replace(/\s*(Wajib|Opsional)\s*$/gi, '') : 'Dokumen';
                    card.querySelector('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
                } else if (hasFile) {
                    card.querySelector('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
                }
            });
            if (missingDoc) {
                isValid = false;
                errorMessage = `⚠️ Mohon unggah berkas wajib "${missingDoc}" pada Langkah 2 sebelum melanjutkan!`;
            }
        } else if (step === 3) {
            const checkSubmit = document.getElementById('checkKonfirmasiSubmit');
            if (checkSubmit && !checkSubmit.checked) {
                isValid = false;
                errorMessage = '⚠️ Harap centang pernyataan konfirmasi sebelum mengirimkan pendaftaran!';
            }
        }

        if (!isValid) {
            // 1. Show High-Visibility Toast Banner (below header)
            showInPageAlert(errorMessage, 'error');

            // 2. Show In-Line Step Warning Box
            if (stepAlert && stepAlertText) {
                stepAlertText.textContent = errorMessage;
                stepAlert.classList.remove('hidden');
            }
        } else {
            if (stepAlert) {
                stepAlert.classList.add('hidden');
            }
        }

        return isValid;
    }

    // Tombol Lanjut & Kembali
    if (btnNext) {
        btnNext.addEventListener('click', function () {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                updateStepUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                const targetUrl = btnPrev.getAttribute('data-dashboard-url') || (window.location.origin + '/ifik/mahasiswa');
                window.location.href = targetUrl;
            }
        });
    }

    // Custom 3D Glass Dropdown Interaction Handler
    const dropdowns = document.querySelectorAll('.custom-dropdown');
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const menu = dropdown.querySelector('.dropdown-menu');
        const hiddenInput = dropdown.querySelector('input[type="hidden"]');
        const triggerLabel = dropdown.querySelector('.trigger-label');
        const chevronIcon = dropdown.querySelector('.chevron-icon');
        const options = dropdown.querySelectorAll('.dropdown-option');

        if (!trigger || !menu) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdowns.forEach(d => {
                if (d !== dropdown) {
                    d.querySelector('.dropdown-menu')?.classList.add('hidden');
                    d.querySelector('.chevron-icon')?.classList.remove('rotate-180');
                }
            });

            menu.classList.toggle('hidden');
            if (chevronIcon) chevronIcon.classList.toggle('rotate-180');
        });

        options.forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                const labelText = opt.querySelector('span')?.textContent || val;

                if (hiddenInput) {
                    hiddenInput.value = val;
                    trigger.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    saveDraft();
                }

                if (triggerLabel) {
                    triggerLabel.textContent = labelText;
                    triggerLabel.className = 'trigger-label text-slate-900 font-semibold';
                }

                options.forEach(o => {
                    o.classList.remove('bg-orange-100/80', 'text-orange-700', 'font-bold');
                    const check = o.querySelector('.check-icon');
                    if (check) check.classList.add('hidden');
                });

                opt.classList.add('bg-orange-100/80', 'text-orange-700', 'font-bold');
                const check = opt.querySelector('.check-icon');
                if (check) check.classList.remove('hidden');

                menu.classList.add('hidden');
                if (chevronIcon) chevronIcon.classList.remove('rotate-180');

                // Step 1 Jenis TA Preview Badge update
                if (hiddenInput && hiddenInput.id === 'inputJenisTA') {
                    const previewJenisTA = document.getElementById('previewJenisTA');
                    const previewTextJenisTA = document.getElementById('previewTextJenisTA');
                    if (previewJenisTA && previewTextJenisTA) {
                        if (val) {
                            previewTextJenisTA.textContent = labelText;
                            previewJenisTA.classList.remove('hidden');
                        } else {
                            previewJenisTA.classList.add('hidden');
                        }
                    }
                }
            });
        });

        // Prefill initialization for dropdowns
        if (hiddenInput && hiddenInput.value.trim() !== '') {
            const currentVal = hiddenInput.value.trim();
            options.forEach(opt => {
                if (opt.getAttribute('data-value') === currentVal) {
                    opt.click();
                }
            });
        }
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.chevron-icon').forEach(i => i.classList.remove('rotate-180'));
    });

    // Interactive Drag and Drop PDF setup with Replace & Reset features
    const dropZones = document.querySelectorAll('.drop-zone');
    dropZones.forEach(zone => {
        const fileInput = zone.querySelector('input[type="file"]');
        const promptContainer = zone.querySelector('.drop-zone-prompt');
        const selectedContainer = zone.querySelector('.drop-zone-selected');
        const fileNameEl = zone.querySelector('.file-name');
        const fileSizeEl = zone.querySelector('.file-size');
        const btnChange = zone.querySelector('.btn-change-file');
        const btnReset = zone.querySelector('.btn-reset-file');
        const stepContainer = zone.closest('.step-content');
        const stepAlert = stepContainer ? stepContainer.querySelector('.step-inline-alert') : null;

        if (!fileInput) return;

        const oldFileInput = document.querySelector(`input[type="hidden"][name="${fileInput.name}_old"]`);

        function renderFileCard(file, isSaved = false) {
            if (!file) return;

            if (fileNameEl) fileNameEl.textContent = file.name;
            if (fileSizeEl) fileSizeEl.textContent = isSaved ? 'Berkas Tersimpan (Siap Diperbarui Jika Perlu)' : `${(file.size / 1024 / 1024).toFixed(2)} MB • PDF Terverifikasi`;

            if (promptContainer) {
                promptContainer.classList.add('hidden');
                promptContainer.style.display = 'none';
            }
            if (selectedContainer) {
                selectedContainer.classList.remove('hidden');
                selectedContainer.style.display = 'flex';
            }

            if (stepAlert) {
                stepAlert.classList.add('hidden');
            }

            zone.classList.remove('border-rose-500', 'bg-rose-50');
            zone.classList.add('border-emerald-400', 'bg-emerald-50/20');
            updateStepUI();
        }

        // Initialize prefilled old file card on load
        if (oldFileInput && oldFileInput.value.trim() !== '') {
            renderFileCard({ name: oldFileInput.value.split('/').pop(), size: 0 }, true);
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                zone.classList.add('border-orange-500', 'bg-orange-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                zone.classList.remove('border-orange-500', 'bg-orange-50');
            }, false);
        });

        // Drop Event
        zone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                if (files[0].type === 'application/pdf' || files[0].name.toLowerCase().endsWith('.pdf')) {
                    fileInput.files = files;
                    renderFileCard(files[0]);
                } else {
                    showInPageAlert('⚠️ Hanya berkas berformat .PDF yang diperbolehkan!', 'error');
                }
            }
        });

        // Click Zone (unless clicking buttons)
        zone.addEventListener('click', (e) => {
            if (e.target.closest('.btn-change-file') || e.target.closest('.btn-reset-file')) return;
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });

        // Input Change Event
        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files[0]) {
                if (!fileInput.files[0].name.toLowerCase().endsWith('.pdf')) {
                    fileInput.value = '';
                    showInPageAlert('⚠️ Berkas harus berformat .PDF!', 'error');
                    return;
                }
                renderFileCard(fileInput.files[0]);
            }
        });

        // Ganti File Button Event
        if (btnChange) {
            btnChange.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });
        }

        // Hapus / Reset File Button Event
        if (btnReset) {
            btnReset.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.value = ''; // Clear file input
                if (oldFileInput) oldFileInput.value = ''; // Clear old file reference too
                if (promptContainer) {
                    promptContainer.classList.remove('hidden');
                    promptContainer.style.display = 'block';
                }
                if (selectedContainer) {
                    selectedContainer.classList.add('hidden');
                    selectedContainer.style.display = 'none';
                }
                zone.classList.remove('border-emerald-400', 'bg-emerald-50/20');
                updateStepUI();
            });
        }
    });

    // --- DRAFT FORM PERSISTENCE ---
    function saveDraft() {
        try {
            const draft = {
                jenis_ta: document.getElementById('inputJenisTA')?.value || '',
                judul_1: document.getElementById('inputJudul1')?.value || '',
                judul_2: document.getElementById('inputJudul2')?.value || '',
                judul_3: document.getElementById('inputJudul3')?.value || '',
                judul_en: document.getElementById('inputJudulEn')?.value || ''
            };
            localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
        } catch (e) {}
    }

    function loadDraft() {
        try {
            const draftStr = localStorage.getItem(DRAFT_KEY);
            if (!draftStr) return;
            const draft = JSON.parse(draftStr);

            if (draft.jenis_ta) {
                const inputJenis = document.getElementById('inputJenisTA');
                if (inputJenis && !inputJenis.value) {
                    inputJenis.value = draft.jenis_ta;
                    const opt = document.querySelector(`.dropdown-option[data-value="${draft.jenis_ta}"]`);
                    if (opt) opt.click();
                }
            }
            if (draft.judul_1) {
                const el = document.getElementById('inputJudul1');
                if (el && !el.value) el.value = draft.judul_1;
            }
            if (draft.judul_2) {
                const el = document.getElementById('inputJudul2');
                if (el && !el.value) {
                    el.value = draft.judul_2;
                    const c2 = document.getElementById('containerJudul2');
                    if (c2) c2.classList.remove('hidden');
                }
            }
            if (draft.judul_3) {
                const el = document.getElementById('inputJudul3');
                if (el && !el.value) {
                    el.value = draft.judul_3;
                    const c3 = document.getElementById('containerJudul3');
                    if (c3) c3.classList.remove('hidden');
                }
            }
            if (draft.judul_en) {
                const el = document.getElementById('inputJudulEn');
                if (el && !el.value) el.value = draft.judul_en;
            }
        } catch (e) {}
    }

    // Form submission validation, double-submit protection & progress bar
    const form = document.querySelector('form');
    let isSubmitting = false;

    if (form) {
        form.addEventListener('input', saveDraft);

        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            for (let step = 1; step <= totalSteps; step++) {
                if (!validateStep(step)) {
                    e.preventDefault();
                    currentStep = step;
                    updateStepUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return false;
                }
            }

            // Flag to prevent multiple submit clicks
            isSubmitting = true;

            // Immediately disable buttons and show loading status on submit button
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
                btnSubmit.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Mengirimkan...</span>';
            }
            if (btnPrev) {
                btnPrev.disabled = true;
                btnPrev.classList.add('opacity-50', 'pointer-events-none');
            }

            // Show Progress Modal Overlay & Inline Progress
            const progressModal = document.getElementById('submitProgressModal');
            const progressBar = document.getElementById('submitProgressBar');
            const progressPercent = document.getElementById('submitProgressPercent');
            const progressStatusText = document.getElementById('submitProgressStatusText');

            const inlineProgress = document.getElementById('inlineSubmitProgress');
            const inlineBar = document.getElementById('inlineProgressBar');
            const inlinePercent = document.getElementById('inlineProgressPercent');
            const inlineStatusText = document.getElementById('inlineProgressStatusText');

            if (progressModal) {
                progressModal.classList.remove('hidden');
                progressModal.classList.add('flex');
            }
            if (inlineProgress) {
                inlineProgress.classList.remove('hidden');
            }

            let progress = 5;
            const updateProgressUI = (val, text) => {
                if (progressBar) progressBar.style.width = val + '%';
                if (progressPercent) progressPercent.textContent = val + '%';
                if (progressStatusText && text) {
                    progressStatusText.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-xs"></i> ${text}`;
                }

                if (inlineBar) inlineBar.style.width = val + '%';
                if (inlinePercent) inlinePercent.textContent = val + '%';
                if (inlineStatusText && text) {
                    inlineStatusText.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-xs"></i> ${text}`;
                }
            };

            updateProgressUI(progress, 'Memeriksa kelengkapan data...');

            const progressInterval = setInterval(() => {
                if (progress < 85) {
                    progress += Math.floor(Math.random() * 10) + 6;
                    if (progress > 85) progress = 85;
                } else if (progress < 98) {
                    progress += 1;
                }

                let statusMsg = 'Mengunggah Berkas TA...';
                if (progress < 30) {
                    statusMsg = 'Menyiapkan dokumen PDF...';
                } else if (progress < 70) {
                    statusMsg = 'Mengunggah berkas ke server...';
                } else {
                    statusMsg = 'Memproses finalisasi pendaftaran...';
                }

                updateProgressUI(progress, statusMsg);
            }, 250);

            // Clear saved draft & step upon valid submit
            try {
                localStorage.removeItem(STEP_KEY);
                localStorage.removeItem(DRAFT_KEY);
            } catch (err) {}
        });
    }

    // --- FITUR AUTO-TRANSLATE JUDUL ID -> EN ---
    const btnAutoTranslate = document.getElementById('btnAutoTranslate');
    const inputJudul1 = document.getElementById('inputJudul1');
    const inputJudulEn = document.getElementById('inputJudulEn');
    const translateSpinner = document.getElementById('translateSpinner');
    const btnAutoTranslateText = document.getElementById('btnAutoTranslateText');

    if (btnAutoTranslate && inputJudul1 && inputJudulEn) {
        btnAutoTranslate.addEventListener('click', async function () {
            const judulIndo = inputJudul1.value.trim();

            if (!judulIndo) {
                showInPageAlert('⚠️ Masukkan Judul Usulan 1 (Utama) terlebih dahulu sebelum klik Translate!', 'warning');
                inputJudul1.focus();
                inputJudul1.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                return;
            } else {
                inputJudul1.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
            }

            // Tampilkan status loading
            btnAutoTranslate.disabled = true;
            btnAutoTranslate.classList.add('opacity-75', 'cursor-not-allowed');
            if (btnAutoTranslateText) btnAutoTranslateText.textContent = 'Menerjemahkan...';
            if (translateSpinner) translateSpinner.classList.remove('hidden');

            try {
                let translatedText = '';

                // 1. Coba panggil Endpoint Backend Mahasiswa/translate_judul
                try {
                    const postUrl = window.location.href.split('?')[0].replace(/\/pendaftaran_ta.*$/, '/translate_judul');
                    const formData = new FormData();
                    formData.append('text', judulIndo);

                    const response = await fetch(postUrl, {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        const resJson = await response.json();
                        if (resJson.status === 'success' && resJson.translated) {
                            translatedText = resJson.translated;
                        }
                    }
                } catch (e) {
                    console.warn('Backend translate failed, trying client APIs:', e);
                }

                // 2. Client-side Fallback: Google Translate API
                if (!translatedText) {
                    try {
                        const gUrl = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=id&tl=en&dt=t&q=${encodeURIComponent(judulIndo)}`;
                        const gRes = await fetch(gUrl);
                        if (gRes.ok) {
                            const gJson = await gRes.json();
                            if (gJson && gJson[0]) {
                                translatedText = gJson[0].map(s => s[0]).join('').trim();
                            }
                        }
                    } catch (e) {
                        console.warn('Google client API error:', e);
                    }
                }

                // 3. Client-side Fallback: MyMemory API
                if (!translatedText) {
                    try {
                        const mUrl = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(judulIndo)}&langpair=id|en`;
                        const mRes = await fetch(mUrl);
                        if (mRes.ok) {
                            const mJson = await mRes.json();
                            if (mJson && mJson.responseData && mJson.responseData.translatedText) {
                                translatedText = mJson.responseData.translatedText.trim();
                            }
                        }
                    } catch (e) {
                        console.warn('MyMemory API error:', e);
                    }
                }

                if (translatedText) {
                    inputJudulEn.value = translatedText;
                    inputJudulEn.classList.add('bg-emerald-50', 'border-emerald-400');
                    inputJudulEn.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    saveDraft();
                    setTimeout(() => {
                        inputJudulEn.classList.remove('bg-emerald-50', 'border-emerald-400');
                    }, 2000);
                    showInPageAlert('✅ Berhasil menerjemahkan judul ke Bahasa Inggris!', 'success');
                } else {
                    showInPageAlert('⚠️ Gagal menerjemahkan otomatis. Silakan ketik judul Bahasa Inggris secara manual.', 'warning');
                }

            } catch (err) {
                console.error('Translation error:', err);
                showInPageAlert('⚠️ Terjadi kendala saat menerjemahkan. Silakan ketik secara manual.', 'warning');
            } finally {
                btnAutoTranslate.disabled = false;
                btnAutoTranslate.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnAutoTranslateText) btnAutoTranslateText.textContent = 'Translate Otomatis';
                if (translateSpinner) translateSpinner.classList.add('hidden');
            }
        });
    }

    // --- FITUR TAMBAH / HAPUS JUDUL ALTERNATIF DINAMIS ---
    const btnAddJudulAlt = document.getElementById('btnAddJudulAlt');
    const containerJudul2 = document.getElementById('containerJudul2');
    const containerJudul3 = document.getElementById('containerJudul3');
    const inputJudul2 = document.getElementById('inputJudul2');
    const inputJudul3 = document.getElementById('inputJudul3');

    function updateAddButtonState() {
        if (!btnAddJudulAlt) return;
        const is2Visible = containerJudul2 && !containerJudul2.classList.contains('hidden');
        const is3Visible = containerJudul3 && !containerJudul3.classList.contains('hidden');

        if (is2Visible && is3Visible) {
            btnAddJudulAlt.classList.add('hidden');
        } else {
            btnAddJudulAlt.classList.remove('hidden');
        }
    }

    if (btnAddJudulAlt) {
        btnAddJudulAlt.addEventListener('click', function () {
            const is2Visible = containerJudul2 && !containerJudul2.classList.contains('hidden');
            const is3Visible = containerJudul3 && !containerJudul3.classList.contains('hidden');

            if (!is2Visible) {
                containerJudul2.classList.remove('hidden');
                if (inputJudul2) inputJudul2.focus();
            } else if (!is3Visible) {
                containerJudul3.classList.remove('hidden');
                if (inputJudul3) inputJudul3.focus();
            }
            updateAddButtonState();
        });
    }

    document.querySelectorAll('.btn-remove-alt').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            if (target === '2' && containerJudul2 && inputJudul2) {
                containerJudul2.classList.add('hidden');
                inputJudul2.value = '';
                saveDraft();
            } else if (target === '3' && containerJudul3 && inputJudul3) {
                containerJudul3.classList.add('hidden');
                inputJudul3.value = '';
                saveDraft();
            }
            updateAddButtonState();
        });
    });

    // Initialize UI and load draft on load
    loadDraft();
    updateStepUI();
});
