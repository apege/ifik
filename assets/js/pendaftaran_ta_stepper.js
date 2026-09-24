/**
 * JavaScript logic for 3-step Pendaftaran Tugas Akhir (Mahasiswa Module)
 * Handles Stepper Navigation, Validation, Dynamic Step Counter, Interactive PDF Upload Card UI, and High-Visibility In-Page Web Toast Alerts
 */

document.addEventListener('DOMContentLoaded', function () {
    const totalSteps = 3;
    const userNim = window.CURRENT_USER_NIM ? window.CURRENT_USER_NIM.trim() : 'guest';
    const STEP_KEY = 'ifik_ta_active_step_' + userNim;
    const DRAFT_KEY = 'ifik_ta_draft_' + userNim;

    let currentStep = 1;

    // Direct navigation support from URL, localStorage, or Database (Server Step)
    const urlParams = new URLSearchParams(window.location.search);
    const urlStep = parseInt(urlParams.get('step'));
    const savedStep = parseInt(localStorage.getItem(STEP_KEY));
    const serverStep = parseInt(window.SERVER_DRAFT_STEP);

    if (urlStep && urlStep >= 1 && urlStep <= totalSteps) {
        currentStep = urlStep;
    } else if (serverStep && serverStep >= 1 && serverStep <= totalSteps) {
        currentStep = serverStep;
        if (savedStep && savedStep > currentStep && savedStep <= totalSteps) {
            currentStep = savedStep;
        }
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

    // Check actual submission/file completion status of each step (available across whole scope)
    function checkStepCompletionStatus(stepIndex) {
        if (stepIndex === 1) {
            const inputJenis = document.getElementById('inputJenisTA');
            const inputJ1 = document.getElementById('inputJudul1') || document.querySelector('input[name="judul_1"]');
            const inputJEn = document.getElementById('inputJudulEn') || document.querySelector('input[name="judul_en"]');
            return !!(inputJenis && inputJenis.value.trim() !== '' &&
                      inputJ1 && inputJ1.value.trim() !== '' &&
                      inputJEn && inputJEn.value.trim() !== '');
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

                // Cursor pointer if clickable
                const canClick = (i <= currentStep) || (i === currentStep + 1 && checkStepCompletionStatus(currentStep));
                stepItem.style.cursor = canClick ? 'pointer' : 'default';

                stepItem.onclick = () => {
                    if (i === currentStep) return;
                    if (i < currentStep) {
                        currentStep = i;
                        saveDraft(true);
                        updateStepUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else if (i > currentStep) {
                        if (validateStep(currentStep)) {
                            currentStep = i;
                            saveDraft(true);
                            updateStepUI();
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    }
                };

                if (i === currentStep) {
                    stepItem.classList.add('active');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold flex items-center justify-center text-sm box-3d ring-4 ring-orange-200/80 transition-all duration-300 z-10';
                        counter.innerHTML = `${i}`;
                    }
                    if (title) {
                        title.className = 'step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300';
                    }
                } else if (i < currentStep) {
                    stepItem.classList.remove('active');
                    if (counter) {
                        counter.className = 'step-counter w-10 h-10 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-xs shadow-xs transition-all duration-300 z-10';
                        counter.innerHTML = '<i class="bi bi-check-lg text-sm font-bold"></i>';
                    }
                    if (title) {
                        title.className = 'step-title font-semibold text-xs sm:text-sm text-slate-700 mt-2 text-center transition-all duration-300';
                    }
                } else {
                    stepItem.classList.remove('active');
                    if (counter) {
                        counter.className = 'step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10';
                        counter.innerHTML = `${i}`;
                    }
                    if (title) {
                        title.className = 'step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300';
                    }
                }
            }
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

            // Sidebar item clickable to navigate back or forward if valid
            const canSideClick = (i <= currentStep) || (i === currentStep + 1 && checkStepCompletionStatus(currentStep));
            sideItem.style.cursor = canSideClick ? 'pointer' : 'default';
            sideItem.onclick = () => {
                if (i === currentStep) return;
                if (i < currentStep) {
                    currentStep = i;
                    saveDraft(true);
                    updateStepUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else if (i > currentStep) {
                    if (validateStep(currentStep)) {
                        currentStep = i;
                        saveDraft(true);
                        updateStepUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
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
                if (trigger) {
                    trigger.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else if (!inputJ1 || !inputJ1.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap isi Judul Usulan 1 (Utama) pada Langkah 1!';
                if (inputJ1) {
                    inputJ1.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    inputJ1.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    inputJ1.focus();
                }
            } else if (!inputJEn || !inputJEn.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap isi Judul dalam Bahasa Inggris pada Langkah 1 (atau klik Translate Otomatis)!';
                if (inputJEn) {
                    inputJEn.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                    inputJEn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    inputJEn.focus();
                }
            } else {
                const trigger = currentContainer.querySelector('.dropdown-trigger');
                if (trigger) trigger.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (inputJ1) inputJ1.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (inputJEn) inputJEn.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
            }
        } else if (step === 2) {
            const reqCards = currentContainer.querySelectorAll('.doc-requirement-card[data-required="1"]');
            let missingDoc = null;
            let firstMissingCard = null;
            reqCards.forEach(card => {
                const fileInput = card.querySelector('.input-doc-file');
                const oldInput = card.querySelector('.input-doc-old');
                const hasFile = (fileInput && fileInput.files && fileInput.files.length > 0) || (oldInput && oldInput.value.trim() !== '');
                if (!hasFile && !missingDoc) {
                    const titleEl = card.querySelector('h4');
                    missingDoc = titleEl ? titleEl.textContent.trim().replace(/^\d+\.\s*/, '').replace(/\s*(Wajib|Opsional)\s*$/gi, '') : 'Dokumen';
                    card.querySelector('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
                    firstMissingCard = card;
                } else if (hasFile) {
                    card.querySelector('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
                }
            });
            if (missingDoc) {
                isValid = false;
                errorMessage = `⚠️ Mohon unggah berkas wajib "${missingDoc}" pada Langkah 2 sebelum melanjutkan!`;
                if (firstMissingCard) {
                    firstMissingCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        } else if (step === 3) {
            const checkSubmit = document.getElementById('checkKonfirmasiSubmit');
            if (checkSubmit && !checkSubmit.checked) {
                isValid = false;
                errorMessage = '⚠️ Harap centang pernyataan konfirmasi sebelum mengirimkan pendaftaran!';
                checkSubmit.scrollIntoView({ behavior: 'smooth', block: 'center' });
                checkSubmit.focus();
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
        btnNext.addEventListener('click', function (e) {
            e.preventDefault();
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    saveDraft(true);
                    updateStepUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function (e) {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                saveDraft(true);
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

        // Prefill initialization for dropdowns (without firing artificial saveDraft on load)
        if (hiddenInput && hiddenInput.value.trim() !== '') {
            const currentVal = hiddenInput.value.trim();
            options.forEach(opt => {
                if (opt.getAttribute('data-value') === currentVal) {
                    const labelText = opt.querySelector('span')?.textContent || currentVal;
                    if (triggerLabel) {
                        triggerLabel.textContent = labelText;
                        triggerLabel.className = 'trigger-label text-slate-900 font-semibold';
                    }
                    opt.classList.add('bg-orange-100/80', 'text-orange-700', 'font-bold');
                    const check = opt.querySelector('.check-icon');
                    if (check) check.classList.remove('hidden');

                    if (hiddenInput.id === 'inputJenisTA') {
                        const previewJenisTA = document.getElementById('previewJenisTA');
                        const previewTextJenisTA = document.getElementById('previewTextJenisTA');
                        if (previewJenisTA && previewTextJenisTA) {
                            previewTextJenisTA.textContent = labelText;
                            previewJenisTA.classList.remove('hidden');
                        }
                    }
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

        function renderFileCard(file, isSaved = false, shouldUpdateUI = true) {
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
            if (shouldUpdateUI) {
                updateStepUI();
            }
        }

        // Initialize prefilled old file card on load (without premature step UI update)
        if (oldFileInput && oldFileInput.value.trim() !== '') {
            renderFileCard({ name: oldFileInput.value.split('/').pop(), size: 0 }, true, false);
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
                    uploadDocFile(fileInput, files[0], zone);
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
                uploadDocFile(fileInput, fileInput.files[0], zone);
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
                const fieldNameToDelete = fileInput.name;
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
                saveDraft(true);
                deleteDocFile(fieldNameToDelete, zone);
            });
        }
    });

    // Background Delete berkas PDF dari database server
    function deleteDocFile(fieldName, zone) {
        if (!fieldName || !window.DELETE_FILE_AJAX_URL) return;

        setDbStatus('saving', 'Menghapus berkas dari database...');

        const fd = new FormData();
        fd.append('nim', userNim);
        fd.append('field_name', fieldName);

        fetch(window.DELETE_FILE_AJAX_URL, {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                setDbStatus('saved', 'Berkas berhasil dihapus');
                showInPageAlert('🗑️ Berkas berhasil dihapus dari database!', 'warning');
            } else {
                setDbStatus('error', 'Gagal menghapus berkas');
            }
        })
        .catch(err => {
            console.error('File delete error:', err);
            setDbStatus('error', 'Gagal tersambung ke server');
        });
    }

    // Background Auto-Upload berkas PDF langsung ke database server
    function uploadDocFile(fileInput, file, zone) {
        if (!file || !window.UPLOAD_AJAX_URL) return;

        const oldFileInput = document.querySelector(`input[type="hidden"][name="${fileInput.name}_old"]`);
        const fileSizeEl = zone.querySelector('.file-size');

        if (fileSizeEl) {
            fileSizeEl.innerHTML = '<span class="text-orange-600 font-semibold flex items-center gap-1.5"><i class="bi bi-arrow-repeat animate-spin text-orange-500"></i> Mengunggah berkas ke database...</span>';
        }
        setDbStatus('saving', 'Mengunggah berkas ke database...');

        const fd = new FormData();
        fd.append('nim', userNim);
        fd.append('field_name', fileInput.name);
        fd.append(fileInput.name, file);

        fetch(window.UPLOAD_AJAX_URL, {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (oldFileInput) {
                    oldFileInput.value = data.file_name;
                }
                if (fileSizeEl) {
                    fileSizeEl.innerHTML = `<span class="text-emerald-600 font-semibold flex items-center gap-1.5"><i class="bi bi-check-circle-fill text-emerald-500"></i> ${data.file_size || ''} • Tersimpan di database</span>`;
                }
                setDbStatus('saved', 'Berkas berhasil disimpan di database');
                showInPageAlert('✅ Berkas PDF berhasil diunggah & tersimpan aman di database!', 'success');
                updateStepUI();
                saveDraft(true, true, false);
            } else {
                if (fileSizeEl) {
                    fileSizeEl.innerHTML = `<span class="text-rose-600 font-semibold flex items-center gap-1.5"><i class="bi bi-exclamation-triangle-fill text-rose-500"></i> Gagal: ${data.message || 'Error'}</span>`;
                }
                setDbStatus('error', 'Gagal menyimpan berkas');
                showInPageAlert(`⚠️ ${data.message || 'Gagal mengunggah berkas'}`, 'error');
            }
        })
        .catch(err => {
            console.error('File auto-upload error:', err);
            if (fileSizeEl) {
                fileSizeEl.innerHTML = '<span class="text-amber-600 font-semibold">Tersimpan sementara (akan diunggah saat submit)</span>';
            }
            setDbStatus('saved', 'Draft formulir aktif');
        });
    }

    // Indikator Status Simpan Database di Header Formulir
    function setDbStatus(state, message) {
        const pill = document.getElementById('dbSaveStatus');
        const icon = document.getElementById('dbSaveStatusIcon');
        const text = document.getElementById('dbSaveStatusText');
        if (!pill || !text) return;

        if (state === 'saving') {
            pill.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/90 shadow-2xs transition-all duration-300';
            if (icon) icon.className = 'bi bi-arrow-repeat animate-spin text-amber-500 text-sm';
            text.textContent = message || 'Menyimpan ke database...';
        } else if (state === 'saved') {
            pill.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs transition-all duration-300';
            if (icon) icon.className = 'bi bi-cloud-check-fill text-emerald-500 text-sm';
            text.textContent = message || 'Draft tersimpan di database';
        } else if (state === 'error') {
            pill.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/90 shadow-2xs transition-all duration-300';
            if (icon) icon.className = 'bi bi-exclamation-triangle-fill text-rose-500 text-sm';
            text.textContent = message || 'Gagal menyimpan ke database';
        }
    }

    // --- DRAFT FORM PERSISTENCE (LOCAL STORAGE + DATABASE AUTO-SAVE & MANUAL SAVE) ---
    let draftDebounceTimer = null;

    function saveDraft(syncToServer = true, forceImmediate = false, notifyUser = false) {
        try {
            const inputJenis = document.getElementById('inputJenisTA');
            const inputJ1 = document.getElementById('inputJudul1');
            const inputJ2 = document.getElementById('inputJudul2');
            const inputJ3 = document.getElementById('inputJudul3');
            const inputJEn = document.getElementById('inputJudulEn');

            // Kumpulkan semua nama berkas PDF yang ada di form
            const filesData = {};
            document.querySelectorAll('.input-doc-old').forEach(input => {
                if (input.name && input.value && input.value.trim() !== '') {
                    filesData[input.name] = input.value.trim();
                }
            });

            const draft = {
                jenis_ta: inputJenis ? inputJenis.value : '',
                judul_1: inputJ1 ? inputJ1.value : '',
                judul_2: inputJ2 ? inputJ2.value : '',
                judul_3: inputJ3 ? inputJ3.value : '',
                judul_en: inputJEn ? inputJEn.value : '',
                draft_step: currentStep,
                files: filesData
            };

            // 1. Simpan langsung ke memori lokal browser (localStorage)
            localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));

            // 2. Simpan ke Database MySQL via AJAX
            if (syncToServer && window.SAVE_DRAFT_AJAX_URL) {
                const executeAjaxSave = () => {
                    setDbStatus('saving', 'Menyimpan perubahan ke database...');

                    const fd = new FormData();
                    fd.append('nim', userNim);
                    fd.append('jenis_ta', draft.jenis_ta);
                    fd.append('judul_1', draft.judul_1);
                    fd.append('judul_2', draft.judul_2);
                    fd.append('judul_3', draft.judul_3);
                    fd.append('judul_en', draft.judul_en);
                    fd.append('draft_step', currentStep);

                    // Sertakan seluruh referensi file berkas
                    for (const [fKey, fVal] of Object.entries(filesData)) {
                        fd.append(fKey, fVal);
                        fd.append(fKey.replace('_old', ''), fVal);
                    }

                    fetch(window.SAVE_DRAFT_AJAX_URL, {
                        method: 'POST',
                        body: fd
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP status ' + res.status);
                        return res.text();
                    })
                    .then(text => {
                        let resData = { success: true };
                        try {
                            resData = JSON.parse(text);
                        } catch(e) {
                            console.warn('Draft save text response:', text);
                        }
                        return resData;
                    })
                    .then(resData => {
                        setDbStatus('saved', 'Draft tersimpan di database');
                        if (notifyUser) {
                            showInPageAlert('✅ Draft formulir dan seluruh berkas berhasil disimpan di database server!', 'success');
                        }
                    })
                    .catch(err => {
                        console.warn('Save draft warning:', err);
                        setDbStatus('error', 'Gagal tersambung ke database');
                        if (notifyUser) {
                            showInPageAlert('⚠️ Gagal menghubungi server saat menyimpan draft.', 'error');
                        }
                    });
                };

                clearTimeout(draftDebounceTimer);
                if (forceImmediate) {
                    executeAjaxSave();
                } else {
                    setDbStatus('saving', 'Menyimpan perubahan ke database...');
                    draftDebounceTimer = setTimeout(executeAjaxSave, 400);
                }
            }
        } catch (e) {
            console.warn('saveDraft error:', e);
        }
    }

    function loadDraft() {
        try {
            const inputJenis = document.getElementById('inputJenisTA');
            const inputJ1 = document.getElementById('inputJudul1');
            const inputJ2 = document.getElementById('inputJudul2');
            const inputJ3 = document.getElementById('inputJudul3');
            const inputJEn = document.getElementById('inputJudulEn');

            // 1. Sinkronkan UI dropdown & badge jika nilai sudah terisi dari Database PHP
            function syncJenisUI(val) {
                if (!val) return;
                const opt = document.querySelector(`.dropdown-option[data-value="${val}"]`);
                const labelText = opt ? (opt.querySelector('span')?.textContent || val) : val;
                const triggerLabel = document.querySelector('#dropdownJenisTA .trigger-label');
                if (triggerLabel) {
                    triggerLabel.textContent = labelText;
                    triggerLabel.className = 'trigger-label text-slate-900 font-semibold';
                }
                const previewJenisTA = document.getElementById('previewJenisTA');
                const previewTextJenisTA = document.getElementById('previewTextJenisTA');
                if (previewJenisTA && previewTextJenisTA) {
                    previewTextJenisTA.textContent = labelText;
                    previewJenisTA.classList.remove('hidden');
                }
                // Update dropdown options styling
                document.querySelectorAll('#dropdownJenisTA .dropdown-option').forEach(o => {
                    if (o.getAttribute('data-value') === val) {
                        o.classList.add('bg-orange-100/80', 'text-orange-700', 'font-bold');
                        o.querySelector('.check-icon')?.classList.remove('hidden');
                    } else {
                        o.classList.remove('bg-orange-100/80', 'text-orange-700', 'font-bold');
                        o.querySelector('.check-icon')?.classList.add('hidden');
                    }
                });
            }

            if (inputJenis && inputJenis.value) {
                syncJenisUI(inputJenis.value);
            }
            if (inputJ2 && inputJ2.value) {
                const c2 = document.getElementById('containerJudul2');
                if (c2) c2.classList.remove('hidden');
            }
            if (inputJ3 && inputJ3.value) {
                const c3 = document.getElementById('containerJudul3');
                if (c3) c3.classList.remove('hidden');
            }

            // 2. Baca dari localStorage jika ada isian yang belum tersimpan di DB
            const draftStr = localStorage.getItem(DRAFT_KEY);
            if (!draftStr) return;
            const draft = JSON.parse(draftStr);

            if (draft.jenis_ta && inputJenis && !inputJenis.value) {
                inputJenis.value = draft.jenis_ta;
                syncJenisUI(draft.jenis_ta);
            }
            if (draft.judul_1 && inputJ1 && !inputJ1.value) {
                inputJ1.value = draft.judul_1;
            }
            if (draft.judul_2 && inputJ2 && !inputJ2.value) {
                inputJ2.value = draft.judul_2;
                const c2 = document.getElementById('containerJudul2');
                if (c2) c2.classList.remove('hidden');
            }
            if (draft.judul_3 && inputJ3 && !inputJ3.value) {
                inputJ3.value = draft.judul_3;
                const c3 = document.getElementById('containerJudul3');
                if (c3) c3.classList.remove('hidden');
            }
            if (draft.judul_en && inputJEn && !inputJEn.value) {
                inputJEn.value = draft.judul_en;
            }

            // Pulihkan berkas dari localStorage jika di HTML belum ada
            if (draft.files && typeof draft.files === 'object') {
                for (const [fName, fVal] of Object.entries(draft.files)) {
                    const oldInp = document.querySelector(`input[type="hidden"][name="${fName}"]`);
                    if (oldInp && !oldInp.value && fVal) {
                        oldInp.value = fVal;
                        const card = oldInp.closest('.doc-requirement-card');
                        const z = card ? card.querySelector('.drop-zone') : null;
                        if (z) {
                            const nameEl = z.querySelector('.file-name');
                            const pContainer = z.querySelector('.drop-zone-prompt');
                            const sContainer = z.querySelector('.drop-zone-selected');
                            if (nameEl) nameEl.textContent = fVal.split('/').pop();
                            if (pContainer) { pContainer.classList.add('hidden'); pContainer.style.display = 'none'; }
                            if (sContainer) { sContainer.classList.remove('hidden'); sContainer.style.display = 'flex'; }
                            z.classList.remove('border-rose-500', 'bg-rose-50');
                            z.classList.add('border-emerald-400', 'bg-emerald-50/20');
                        }
                    }
                }
            }
        } catch (e) {
            console.warn('loadDraft error:', e);
        }
    }

    // Form submission validation, double-submit protection & progress bar
    const form = document.querySelector('form');
    let isSubmitting = false;

    if (form) {
        form.addEventListener('input', saveDraft);

        // Cegah submit otomatis saat menekan Enter di input teks jika belum di Langkah terakhir
        form.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                if (currentStep < totalSteps) {
                    if (btnNext) btnNext.click();
                }
            }
        });

        form.addEventListener('submit', function (e) {
            if (currentStep < totalSteps) {
                e.preventDefault();
                if (btnNext) btnNext.click();
                return false;
            }

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

            // Safety timeout to unlock button if server takes too long or fails
            setTimeout(() => {
                if (isSubmitting) {
                    const btnCloseModal = document.getElementById('btnCloseSubmitModal');
                    if (btnCloseModal) btnCloseModal.classList.remove('hidden');
                }
            }, 12000);

            // Clear saved draft & step upon valid submit
            try {
                localStorage.removeItem(STEP_KEY);
                localStorage.removeItem(DRAFT_KEY);
            } catch (err) {}
        });

        // Reset submit lock if user navigates back from browser history / bfcache
        window.addEventListener('pageshow', function (e) {
            isSubmitting = false;
            const progressModal = document.getElementById('submitProgressModal');
            if (progressModal) {
                progressModal.classList.add('hidden');
                progressModal.classList.remove('flex');
            }
            const inlineProgress = document.getElementById('inlineSubmitProgress');
            if (inlineProgress) {
                inlineProgress.classList.add('hidden');
            }
            if (btnSubmit && !btnSubmit.hasAttribute('disabled-permanently')) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
                btnSubmit.innerHTML = '<i class="bi bi-send-fill text-sm"></i> Kirim Pendaftaran';
            }
            if (btnPrev) {
                btnPrev.disabled = false;
                btnPrev.classList.remove('opacity-50', 'pointer-events-none');
            }
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

                // 1. Coba langsung dari Browser (Google Translate Client API - Paling Cepat ~300ms)
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
                    console.warn('Google client API error, trying backend:', e);
                }

                // 2. Fallback: Endpoint Backend Mahasiswa/translate_judul
                if (!translatedText) {
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
                        console.warn('Backend translate failed, trying MyMemory:', e);
                    }
                }

                // 3. Fallback: MyMemory API
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

    // Auto-advance ke step berikutnya hanya jika tidak ada parameter ?step= di URL dan tidak ada step tersimpan di localStorage
    if (!urlStep && !savedStep) {
        const hasStep1Data = checkStepCompletionStatus(1);
        if (hasStep1Data && currentStep < 2) {
            currentStep = 2;
        }
        if (serverStep === 3 && checkStepCompletionStatus(2)) {
            currentStep = 3;
        }
    }

    updateStepUI();
});
