/**
 * JavaScript logic for 6-step Pendaftaran Tugas Akhir (Mahasiswa Module)
 * Handles Stepper Navigation, Validation, Dynamic Step Counter, Interactive PDF Upload Card UI, and High-Visibility In-Page Web Toast Alerts
 */

document.addEventListener('DOMContentLoaded', function () {
    let currentStep = 1;
    const totalSteps = 6;

    // Direct navigation support from Dashboard redirect (e.g. ?step=3)
    const urlParams = new URLSearchParams(window.location.search);
    const initialStep = parseInt(urlParams.get('step'));
    if (initialStep && initialStep >= 1 && initialStep <= totalSteps) {
        currentStep = initialStep;
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

    // Update UI step state
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

                if (i === currentStep) {
                    stepItem.classList.add('active');
                    stepItem.classList.remove('completed');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-sm shadow-md ring-8 ring-orange-100 transition-all duration-300 z-10';
                        counter.innerHTML = i;
                    }
                    if (title) {
                        title.className = 'step-title font-bold text-xs text-orange-500 mt-2.5 text-center transition-all duration-300';
                    }
                } else if (i < currentStep) {
                    stepItem.classList.add('completed');
                    stepItem.classList.remove('active');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition-all duration-300 z-10';
                        counter.innerHTML = '<i class="bi bi-check-lg text-base"></i>';
                    }
                    if (title) {
                        title.className = 'step-title font-medium text-xs text-slate-500 mt-2.5 text-center transition-all duration-300';
                    }
                } else {
                    stepItem.classList.remove('active', 'completed');
                    if (counter) {
                        counter.className = 'step-counter w-11 h-11 rounded-full bg-slate-100 text-slate-400 font-semibold flex items-center justify-center text-sm transition-all duration-300 z-10';
                        counter.innerHTML = i;
                    }
                    if (title) {
                        title.className = 'step-title font-medium text-xs text-slate-400 mt-2.5 text-center transition-all duration-300';
                    }
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
                return inputJenis && inputJenis.value.trim() !== '';
            } else if (stepIndex === 2) {
                const inputJ1 = document.querySelector('input[name="judul_1"]');
                const inputJEn = document.querySelector('input[name="judul_en"]');
                return inputJ1 && inputJ1.value.trim() !== '' && inputJEn && inputJEn.value.trim() !== '';
            } else if (stepIndex === 3) {
                const inputKsm = document.querySelector('input[name="file_ksm"]');
                return inputKsm && inputKsm.files && inputKsm.files.length > 0;
            } else if (stepIndex === 4) {
                const inputTranskrip = document.querySelector('input[name="file_transkrip"]');
                return inputTranskrip && inputTranskrip.files && inputTranskrip.files.length > 0;
            } else if (stepIndex === 5) {
                const inputPernyataan = document.querySelector('input[name="file_pernyataan"]');
                return inputPernyataan && inputPernyataan.files && inputPernyataan.files.length > 0;
            } else if (stepIndex === 6) {
                const inputBebasLab = document.querySelector('input[name="file_bebas_lab"]');
                return inputBebasLab && inputBebasLab.files && inputBebasLab.files.length > 0;
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

        const isEditing = !!(document.querySelector('input[name="file_ksm_old"]')?.value || document.querySelector('input[name="judul_1"]')?.value);

        if (btnNext && btnSubmit) {
            if (currentStep === totalSteps) {
                btnNext.classList.add('hidden');
                btnSubmit.classList.remove('hidden');
            } else if (isEditing) {
                // When editing/revising, show BOTH "Lanjutkan" and "Kirim Pendaftaran" buttons
                btnNext.classList.remove('hidden');
                btnSubmit.classList.remove('hidden');
            } else {
                btnNext.classList.remove('hidden');
                btnSubmit.classList.add('hidden');
            }
        }
    }

    // Validate current step inputs
    function validateStep(step) {
        const currentContainer = document.getElementById(`step-content-${step}`);
        if (!currentContainer) return true;

        let isValid = true;
        let errorMessage = '';
        const stepAlert = currentContainer.querySelector('.step-inline-alert');
        const stepAlertText = currentContainer.querySelector('.step-inline-alert-text');
        const requiredInputs = currentContainer.querySelectorAll('[required]');

        requiredInputs.forEach(input => {
            if (input.type === 'file') {
                const oldFileInput = document.querySelector(`input[type="hidden"][name="${input.name}_old"]`);
                const hasOldFile = oldFileInput && oldFileInput.value.trim() !== '';

                if ((!input.files || input.files.length === 0) && !hasOldFile) {
                    isValid = false;
                    errorMessage = `⚠️ Mohon unggah berkas PDF pada Langkah ${step} sebelum melanjutkan!`;
                    input.closest('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
                } else if (input.files && input.files.length > 0) {
                    const fileName = input.files[0].name;
                    if (!fileName.toLowerCase().endsWith('.pdf')) {
                        isValid = false;
                        errorMessage = `⚠️ Berkas "${fileName}" harus berformat .PDF!`;
                        input.closest('.drop-zone')?.classList.add('border-rose-500', 'bg-rose-50');
                    } else {
                        input.closest('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
                    }
                } else {
                    input.closest('.drop-zone')?.classList.remove('border-rose-500', 'bg-rose-50');
                }
            } else {
                if (!input.value.trim()) {
                    isValid = false;
                    errorMessage = `⚠️ Harap lengkapi semua kolom yang wajib diisi (*) pada Langkah ${step}!`;
                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                } else {
                    input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                }
            }
        });

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
            });
        }
    });

    // Form submission validation
    const form = document.querySelector('form');
    if (form) {
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
            } else if (target === '3' && containerJudul3 && inputJudul3) {
                containerJudul3.classList.add('hidden');
                inputJudul3.value = '';
            }
            updateAddButtonState();
        });
    });

    // Initialize UI on load
    updateStepUI();
});
    // Initialize UI on load
    updateStepUI();
});
