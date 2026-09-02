/**
 * JavaScript logic for 6-step Pendaftaran Tugas Akhir (Mahasiswa Module)
 * Handles Stepper Navigation, Validation, Dynamic Step Counter, Interactive PDF Upload Card UI, and High-Visibility In-Page Web Toast Alerts
 */

document.addEventListener('DOMContentLoaded', function () {
    const totalSteps = 6;
    const userNim = window.CURRENT_USER_NIM ? window.CURRENT_USER_NIM.trim() : 'guest';
    const STEP_KEY = 'ifik_ta_active_step_' + userNim;
    const DRAFT_KEY = 'ifik_ta_form_draft_' + userNim;

    // Clean legacy un-scoped draft keys from browser
    try {
        localStorage.removeItem('ifik_ta_active_step');
        localStorage.removeItem('ifik_ta_form_draft');
    } catch(e) {}

    let currentStep = 1;
    let isRestoringDraft = false;

    // Direct navigation support from Dashboard redirect (e.g. ?step=3), localStorage / sessionStorage, or server DB draft step
    const urlParams = new URLSearchParams(window.location.search);
    const urlStep = parseInt(urlParams.get('step'));
    const savedStep = parseInt(localStorage.getItem(STEP_KEY) || sessionStorage.getItem(STEP_KEY));
    const serverStep = parseInt(window.SERVER_DRAFT_STEP);

    if (urlStep && urlStep >= 1 && urlStep <= totalSteps) {
        currentStep = urlStep;
    } else if (savedStep && savedStep >= 1 && savedStep <= totalSteps) {
        currentStep = savedStep;
    } else if (serverStep && serverStep >= 1 && serverStep <= totalSteps) {
        currentStep = serverStep;
    }

    const btnNext = document.getElementById('btnNext');
    const btnPrev = document.getElementById('btnPrev');
    const btnSubmit = document.getElementById('btnSubmit');
    const stepCounterText = document.getElementById('stepCounterText');
    const toastAlert = document.getElementById('inPageToastAlert');
    const toastMessage = document.getElementById('toastAlertMessage');
    const btnCloseToast = document.getElementById('btnCloseToast');

    let toastTimeout;

    // Show High-Visibility In-Page Toast Notification (Below sticky header)
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
            toastAlert.classList.add('translate-y-[-20px]', 'opacity-0');
            setTimeout(() => {
                toastAlert.classList.add('hidden');
            }, 300);
        }, 5000);
    }

    if (btnCloseToast) {
        btnCloseToast.addEventListener('click', () => {
            toastAlert.classList.add('translate-y-[-20px]', 'opacity-0');
            setTimeout(() => {
                toastAlert.classList.add('hidden');
            }, 300);
        });
    }

    // Update Stepper UI and Steps
    function updateStepUI() {
        if (stepCounterText) {
            stepCounterText.textContent = `LANGKAH ${currentStep} / ${totalSteps}`;
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

                // Step Item Clickable to navigate to visited steps
                stepItem.style.cursor = (i <= currentStep) ? 'pointer' : 'default';
                stepItem.onclick = () => {
                    if (i < currentStep) {
                        saveDraft();
                        currentStep = i;
                        updateStepUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                };

                if (i === currentStep) {
                    stepItem.classList.add('active');
                    stepItem.classList.remove('completed');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold flex items-center justify-center text-sm box-3d ring-4 ring-orange-200/80 transition-all duration-300 z-10';
                        counter.innerHTML = i;
                    }
                    if (title) {
                        title.className = 'step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300 px-1';
                    }
                } else if (i < currentStep) {
                    stepItem.classList.add('completed');
                    stepItem.classList.remove('active');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm border border-slate-300 transition-all duration-300 z-10';
                        counter.innerHTML = '<i class="bi bi-check-lg text-base"></i>';
                    }
                    if (title) {
                        title.className = 'step-title font-medium text-xs sm:text-sm text-slate-600 mt-2 text-center transition-all duration-300 px-1';
                    }
                } else {
                    stepItem.classList.remove('active', 'completed');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-sm transition-all duration-300 z-10';
                        counter.innerHTML = i;
                    }
                    if (title) {
                        title.className = 'step-title font-medium text-xs sm:text-sm text-slate-400 mt-2 text-center transition-all duration-300 px-1';
                    }
                }
            }
        }

        const progressLine = document.getElementById('stepperProgressLine');
        if (progressLine) {
            const percent = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressLine.style.width = percent + '%';
        }

        // Check actual submission/file completion status of each step:
        function checkStepCompletionStatus(stepIndex) {
            if (stepIndex === 1) {
                const inputJenis = document.getElementById('inputJenisTA');
                return inputJenis && inputJenis.value.trim() !== '';
            } else if (stepIndex === 2) {
                const inputJ1 = document.querySelector('input[name="judul_1"]');
                const inputJEn = document.querySelector('input[name="judul_en"]');
                return inputJ1 && inputJ1.value.trim() !== '' && inputJEn && inputJEn.value.trim() !== '';
            } else if (stepIndex === 3) {
                const inputKsm = document.querySelector('input[name="file_ksm"]');
                const oldKsm = document.querySelector('input[name="file_ksm_old"]');
                return (inputKsm && inputKsm.files && inputKsm.files.length > 0) || (oldKsm && oldKsm.value.trim() !== '');
            } else if (stepIndex === 4) {
                const inputTranskrip = document.querySelector('input[name="file_transkrip"]');
                const oldTranskrip = document.querySelector('input[name="file_transkrip_old"]');
                return (inputTranskrip && inputTranskrip.files && inputTranskrip.files.length > 0) || (oldTranskrip && oldTranskrip.value.trim() !== '');
            } else if (stepIndex === 5) {
                const inputPernyataan = document.querySelector('input[name="file_pernyataan"]');
                const oldPernyataan = document.querySelector('input[name="file_pernyataan_old"]');
                return (inputPernyataan && inputPernyataan.files && inputPernyataan.files.length > 0) || (oldPernyataan && oldPernyataan.value.trim() !== '');
            } else if (stepIndex === 6) {
                const inputBebasLab = document.querySelector('input[name="file_bebas_lab"]');
                const oldBebasLab = document.querySelector('input[name="file_bebas_lab_old"]');
                return (inputBebasLab && inputBebasLab.files && inputBebasLab.files.length > 0) || (oldBebasLab && oldBebasLab.value.trim() !== '');
            }
            return false;
        }

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
            if (!inputJenis || !inputJenis.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap pilih Jenis Tugas Akhir pada Langkah 1 terlebih dahulu!';
                const trigger = currentContainer.querySelector('.dropdown-trigger');
                if (trigger) trigger.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
            } else {
                const trigger = currentContainer.querySelector('.dropdown-trigger');
                if (trigger) trigger.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
            }
        } else if (step === 2) {
            const inputJ1 = document.querySelector('input[name="judul_1"]');
            const inputJEn = document.querySelector('input[name="judul_en"]');
            if (!inputJ1 || !inputJ1.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap isi Judul Usulan 1 (Utama) pada Langkah 2!';
                if (inputJ1) inputJ1.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
            } else if (!inputJEn || !inputJEn.value.trim()) {
                isValid = false;
                errorMessage = '⚠️ Harap isi Judul dalam Bahasa Inggris pada Langkah 2 (atau klik Translate Otomatis)!';
                if (inputJEn) inputJEn.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
            } else {
                if (inputJ1) inputJ1.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (inputJEn) inputJEn.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
            }
        } else if (step === 3) {
            const inputKsm = document.querySelector('input[name="file_ksm"]');
            const oldKsm = document.querySelector('input[name="file_ksm_old"]');
            const hasKsm = (inputKsm && inputKsm.files && inputKsm.files.length > 0) || (oldKsm && oldKsm.value.trim() !== '');
            if (!hasKsm) {
                isValid = false;
                errorMessage = '⚠️ Harap unggah berkas KSM PDF pada Langkah 3 sebelum melanjutkan!';
                inputKsm?.closest('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
            } else {
                inputKsm?.closest('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
            }
        } else if (step === 4) {
            const inputTranskrip = document.querySelector('input[name="file_transkrip"]');
            const oldTranskrip = document.querySelector('input[name="file_transkrip_old"]');
            const hasTranskrip = (inputTranskrip && inputTranskrip.files && inputTranskrip.files.length > 0) || (oldTranskrip && oldTranskrip.value.trim() !== '');
            if (!hasTranskrip) {
                isValid = false;
                errorMessage = '⚠️ Harap unggah berkas Transkrip Nilai PDF pada Langkah 4 sebelum melanjutkan!';
                inputTranskrip?.closest('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
            } else {
                inputTranskrip?.closest('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
            }
        } else if (step === 5) {
            const inputPernyataan = document.querySelector('input[name="file_pernyataan"]');
            const oldPernyataan = document.querySelector('input[name="file_pernyataan_old"]');
            const hasPernyataan = (inputPernyataan && inputPernyataan.files && inputPernyataan.files.length > 0) || (oldPernyataan && oldPernyataan.value.trim() !== '');
            if (!hasPernyataan) {
                isValid = false;
                errorMessage = '⚠️ Harap unggah berkas Surat Pernyataan PDF pada Langkah 5 sebelum melanjutkan!';
                inputPernyataan?.closest('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
            } else {
                inputPernyataan?.closest('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
            }
        } else if (step === 6) {
            const inputBebasLab = document.querySelector('input[name="file_bebas_lab"]');
            const oldBebasLab = document.querySelector('input[name="file_bebas_lab_old"]');
            const hasBebasLab = (inputBebasLab && inputBebasLab.files && inputBebasLab.files.length > 0) || (oldBebasLab && oldBebasLab.value.trim() !== '');
            if (!hasBebasLab) {
                isValid = false;
                errorMessage = '⚠️ Harap unggah berkas Surat Bebas Lab PDF pada Langkah 6 sebelum mengirim pendaftaran!';
                inputBebasLab?.closest('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
            } else {
                inputBebasLab?.closest('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
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
                    saveDraft();
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
                saveDraft();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                const dashboardLink = document.querySelector('a[href*="/mahasiswa"]')?.href;
                window.location.href = dashboardLink || '/mahasiswa';
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
            if (fileSizeEl) {
                fileSizeEl.innerHTML = isSaved 
                    ? '<span class="text-emerald-700 font-semibold"><i class="bi bi-cloud-check-fill"></i> Berkas Tersimpan di Server</span>' 
                    : `${(file.size / 1024 / 1024).toFixed(2)} MB • PDF Terverifikasi`;
            }

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

        // Background Auto-Upload function
        async function uploadFileViaAjax(fieldName, file) {
            if (!window.UPLOAD_AJAX_URL) return;
            const fd = new FormData();
            fd.append('field_name', fieldName);
            fd.append(fieldName, file);

            if (fileSizeEl) {
                fileSizeEl.innerHTML = '<span class="text-orange-600 font-semibold animate-pulse"><i class="bi bi-arrow-repeat animate-spin"></i> Mengunggah ke server...</span>';
            }

            try {
                const res = await fetch(window.UPLOAD_AJAX_URL, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                if (json && json.success) {
                    if (oldFileInput) oldFileInput.value = json.file_name;
                    if (fileSizeEl) {
                        fileSizeEl.innerHTML = `<span class="text-emerald-700 font-semibold"><i class="bi bi-cloud-check-fill"></i> ${json.file_size} • Tersimpan di Server</span>`;
                    }
                    showInPageAlert(`✅ Berkas ${file.name} berhasil tersimpan di server!`, 'success');
                    saveDraft();
                    updateStepUI();
                } else {
                    showInPageAlert(json.message || 'Gagal menyimpan berkas ke server.', 'error');
                }
            } catch (err) {
                console.error('Auto-upload error:', err);
            }
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
                    uploadFileViaAjax(fileInput.name, files[0]);
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
                uploadFileViaAjax(fileInput.name, fileInput.files[0]);
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
                saveDraft();
                updateStepUI();
            });
        }
    });

    // --- DRAFT FORM PERSISTENCE WITH SERVER-SYNC ---
    let serverSaveTimeout = null;

    function setAutoSaveIndicator(status) {
        const indicator = document.getElementById('autoSaveIndicator');
        if (!indicator) return;

        if (status === 'saving') {
            indicator.className = 'text-[11px] font-bold text-amber-700 bg-amber-100/90 border border-amber-300 px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-2xs transition-all';
            indicator.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-amber-600"></i><span id="autoSaveText">Menyimpan ke Server...</span>';
        } else if (status === 'saved') {
            indicator.className = 'text-[11px] font-bold text-emerald-700 bg-emerald-100/90 border border-emerald-300 px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-2xs transition-all';
            indicator.innerHTML = '<i class="bi bi-cloud-check-fill text-emerald-600"></i><span id="autoSaveText">Tersimpan di Server</span>';
        }
    }

    function saveDraftToServer(draftData) {
        if (!window.SAVE_DRAFT_AJAX_URL) return;
        setAutoSaveIndicator('saving');

        const formData = new FormData();
        formData.append('draft_step', draftData.draft_step || currentStep);
        if (draftData.jenis_ta) formData.append('jenis_ta', draftData.jenis_ta);
        if (draftData.judul_1 !== undefined) formData.append('judul_1', draftData.judul_1);
        if (draftData.judul_2 !== undefined) formData.append('judul_2', draftData.judul_2);
        if (draftData.judul_3 !== undefined) formData.append('judul_3', draftData.judul_3);
        if (draftData.judul_en !== undefined) formData.append('judul_en', draftData.judul_en);

        fetch(window.SAVE_DRAFT_AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                setAutoSaveIndicator('saved');
            }
        })
        .catch(err => {
            console.warn('Auto-save to server warning:', err);
            setAutoSaveIndicator('saved');
        });
    }

    function saveDraft() {
        if (isRestoringDraft) return;
        try {
            const jenisVal = document.getElementById('inputJenisTA')?.value || '';
            const j1Val    = document.getElementById('inputJudul1')?.value || '';
            const j2Val    = document.getElementById('inputJudul2')?.value || '';
            const j3Val    = document.getElementById('inputJudul3')?.value || '';
            const jEnVal   = document.getElementById('inputJudulEn')?.value || '';
            const filesObj = {
                file_ksm: document.getElementById('file_ksm_old')?.value || '',
                file_transkrip: document.getElementById('file_transkrip_old')?.value || '',
                file_pernyataan: document.getElementById('file_pernyataan_old')?.value || '',
                file_bebas_lab: document.getElementById('file_bebas_lab_old')?.value || ''
            };

            // Guard against wiping existing draft on initial empty DOM renders
            if (!jenisVal && !j1Val && !j2Val && !j3Val && !jEnVal && !filesObj.file_ksm && !filesObj.file_transkrip && !filesObj.file_pernyataan && !filesObj.file_bebas_lab) {
                const existing = localStorage.getItem(DRAFT_KEY);
                if (existing) return; // Keep existing draft intact
            }

            const draft = {
                draft_step: currentStep,
                jenis_ta: jenisVal,
                judul_1: j1Val,
                judul_2: j2Val,
                judul_3: j3Val,
                judul_en: jEnVal,
                files: filesObj
            };
            const payload = JSON.stringify(draft);
            localStorage.setItem(DRAFT_KEY, payload);
            sessionStorage.setItem(DRAFT_KEY, payload);
            localStorage.setItem(STEP_KEY, currentStep);
            sessionStorage.setItem(STEP_KEY, currentStep);

            // Debounce background server sync (400ms)
            clearTimeout(serverSaveTimeout);
            serverSaveTimeout = setTimeout(() => {
                saveDraftToServer(draft);
            }, 400);
        } catch (e) {}
    }

    function loadDraft() {
        try {
            const draftStr = localStorage.getItem(DRAFT_KEY) || sessionStorage.getItem(DRAFT_KEY);
            if (!draftStr) return;
            const draft = JSON.parse(draftStr);

            isRestoringDraft = true;

            // 1. Jenis TA (update input + dropdown UI directly without triggering click events)
            if (draft.jenis_ta) {
                const inputJenis = document.getElementById('inputJenisTA');
                if (inputJenis) {
                    inputJenis.value = draft.jenis_ta;
                    const opt = document.querySelector(`.dropdown-option[data-value="${draft.jenis_ta}"]`);
                    if (opt) {
                        const triggerLabel = opt.closest('.custom-dropdown')?.querySelector('.trigger-label');
                        const labelText = opt.querySelector('span')?.textContent || draft.jenis_ta;
                        if (triggerLabel) {
                            triggerLabel.textContent = labelText;
                            triggerLabel.className = 'trigger-label text-slate-900 font-semibold';
                        }
                        const allOpts = opt.closest('.dropdown-menu')?.querySelectorAll('.dropdown-option');
                        if (allOpts) {
                            allOpts.forEach(o => {
                                o.classList.remove('bg-orange-100/80', 'text-orange-700', 'font-bold');
                                const check = o.querySelector('.check-icon');
                                if (check) check.classList.add('hidden');
                            });
                        }
                        opt.classList.add('bg-orange-100/80', 'text-orange-700', 'font-bold');
                        const check = opt.querySelector('.check-icon');
                        if (check) check.classList.remove('hidden');

                        const previewJenisTA = document.getElementById('previewJenisTA');
                        const previewTextJenisTA = document.getElementById('previewTextJenisTA');
                        if (previewJenisTA && previewTextJenisTA) {
                            previewTextJenisTA.textContent = labelText;
                            previewJenisTA.classList.remove('hidden');
                        }
                    }
                }
            }

            // 2. Judul Utama 1
            if (draft.judul_1) {
                const el = document.getElementById('inputJudul1');
                if (el) el.value = draft.judul_1;
            }

            // 3. Judul Alternatif 2
            if (draft.judul_2) {
                const el = document.getElementById('inputJudul2');
                if (el) {
                    el.value = draft.judul_2;
                    const c2 = document.getElementById('containerJudul2');
                    if (c2) c2.classList.remove('hidden');
                }
            }

            // 4. Judul Alternatif 3
            if (draft.judul_3) {
                const el = document.getElementById('inputJudul3');
                if (el) {
                    el.value = draft.judul_3;
                    const c3 = document.getElementById('containerJudul3');
                    if (c3) c3.classList.remove('hidden');
                }
            }

            // 5. Judul Translation EN
            if (draft.judul_en) {
                const el = document.getElementById('inputJudulEn');
                if (el) el.value = draft.judul_en;
            }

            // 6. Restore File Cards for Steps 3 - 6
            const fileFields = ['file_ksm', 'file_transkrip', 'file_pernyataan', 'file_bebas_lab'];
            fileFields.forEach(f => {
                const oldInput = document.getElementById(f + '_old');
                const savedFileName = (oldInput && oldInput.value.trim()) ? oldInput.value.trim() : (draft.files ? draft.files[f] : '');
                if (savedFileName) {
                    if (oldInput) oldInput.value = savedFileName;
                    const zone = document.querySelector(`input[name="${f}"]`)?.closest('.drop-zone');
                    if (zone) {
                        const fileNameEl = zone.querySelector('.file-name');
                        const fileSizeEl = zone.querySelector('.file-size');
                        const promptContainer = zone.querySelector('.drop-zone-prompt');
                        const selectedContainer = zone.querySelector('.drop-zone-selected');
                        const stepContainer = zone.closest('.step-content');
                        const stepAlert = stepContainer ? stepContainer.querySelector('.step-inline-alert') : null;

                        if (fileNameEl) fileNameEl.textContent = savedFileName.split('/').pop();
                        if (fileSizeEl) fileSizeEl.innerHTML = '<span class="text-emerald-700 font-semibold"><i class="bi bi-cloud-check-fill"></i> Berkas Tersimpan di Server</span>';
                        if (promptContainer) {
                            promptContainer.classList.add('hidden');
                            promptContainer.style.display = 'none';
                        }
                        if (selectedContainer) {
                            selectedContainer.classList.remove('hidden');
                            selectedContainer.style.display = 'flex';
                        }
                        if (stepAlert) stepAlert.classList.add('hidden');
                        zone.classList.remove('border-rose-500', 'bg-rose-50');
                        zone.classList.add('border-emerald-400', 'bg-emerald-50/20');
                    }
                }
            });

            isRestoringDraft = false;
        } catch (e) {
            isRestoringDraft = false;
        }
    }

    // Form submission validation & draft listening
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('input', saveDraft);
        form.addEventListener('change', saveDraft);

        form.addEventListener('submit', function (e) {
            for (let step = 1; step <= totalSteps; step++) {
                if (!validateStep(step)) {
                    e.preventDefault();
                    currentStep = step;
                    updateStepUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return false;
                }
            }
            // Clear saved draft & step ONLY upon valid and successful submit
            try {
                localStorage.removeItem(STEP_KEY);
                localStorage.removeItem(DRAFT_KEY);
                sessionStorage.removeItem(STEP_KEY);
                sessionStorage.removeItem(DRAFT_KEY);
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

    // Attach real-time input listeners for auto-saving title fields
    ['inputJudul1', 'inputJudul2', 'inputJudul3', 'inputJudulEn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', () => {
                saveDraft();
            });
            el.addEventListener('change', () => {
                saveDraft();
            });
        }
    });

    // Initialize UI and load draft on load
    loadDraft();
    updateStepUI();
});
