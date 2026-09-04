<!-- ========================================================= -->
<!-- MODAL SERI REKOMENDASI SIDANG / NON SIDANG (DINAMIS & PDF/DOCX) -->
<!-- ========================================================= -->

<!-- 1. POP-UP MODAL UTAMA: REKOMENDASI SIDANG / NON SIDANG (FOTO 1) -->
<div id="modalRekomendasiUtama" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md transition-opacity">
    <div class="relative bg-white rounded-[2.5rem] p-7 sm:p-10 w-full max-w-3xl shadow-2xl border border-slate-100 transform transition-all animate-in fade-in zoom-in-95 duration-200">
        <button onclick="closeModalRekomen('modalRekomendasiUtama')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 font-bold text-xl flex items-center justify-center transition leading-none">&times;</button>
        
        <!-- Header Modal -->
        <div class="mb-8 text-left border-b border-slate-100 pb-5">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Rekomendasi Sidang / Non Sidang</h3>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Pilih jenis rekomendasi kelulusan Tugas Akhir untuk mahasiswa bimbingan Anda.</p>
        </div>

        <!-- Cards Container (Horizontal Slider / Carousel Kiri/Kanan) -->
        <div class="relative group">
            <button onclick="scrollMainSlider('left')" type="button" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white shadow-xl border border-slate-200 text-slate-700 hover:text-orange-600 flex items-center justify-center transition cursor-pointer hover:scale-110 active:scale-95">
                <i class="bi bi-chevron-left text-lg font-bold"></i>
            </button>

            <div id="gridMainCategoryOptions" class="flex overflow-x-auto snap-x snap-mandatory space-x-6 p-3 pb-6 scrollbar-none scroll-smooth">
                <!-- Cards injected dynamically via JS -->
            </div>

            <button onclick="scrollMainSlider('right')" type="button" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white shadow-xl border border-slate-200 text-slate-700 hover:text-orange-600 flex items-center justify-center transition cursor-pointer hover:scale-110 active:scale-95">
                <i class="bi bi-chevron-right text-lg font-bold"></i>
            </button>
        </div>


        <!-- Manage Options Link for Admins / Lecturers -->
        <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span class="font-medium"><i class="bi bi-gear-fill text-orange-500 mr-1"></i> Sistem Rekomendasi Dinamis</span>
            <a href="<?= site_url('adminlayanan/pengaturan_jalur') ?>" target="_blank" class="text-orange-600 hover:text-orange-700 font-bold hover:underline">
                <i class="bi bi-sliders mr-1"></i> Kelola Jalur &amp; Form Persyaratan (Admin Panel)
            </a>
        </div>
    </div>
</div>



<!-- 2. POP-UP MODAL KONFIRMASI SIDANG (FOTO 2) -->
<div id="modalKonfirmasiSidang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
    <div class="relative bg-white rounded-3xl p-7 sm:p-8 w-full max-w-lg shadow-2xl border border-slate-100 transform transition-all">
        <button onclick="closeModalRekomen('modalKonfirmasiSidang')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
        
        <h3 class="text-xl font-bold text-slate-900 mb-6 border-b border-slate-100 pb-3">Lanjut ke tahapan pendaftaran sidang</h3>

        <div class="space-y-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="font-bold text-rose-600 text-sm">1.</span>
                <p class="text-sm font-semibold text-rose-600">Eviden untuk non sidang</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="font-bold text-rose-600 text-sm">2.</span>
                <p class="text-sm font-semibold text-rose-600">Persetujuan Pembimbing</p>
            </div>
            
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-slate-700 text-xs leading-relaxed mt-4">
                <span class="font-bold text-amber-800">nb :</span> Mohon dosen pembimbing memastikan peserta sidang lulus eprt atau surat pemakluman dan TAK
            </div>
        </div>

        <form id="formSubmitSidang" action="<?= site_url('mahasiswa/submit_rekomendasi_sidang') ?>" method="POST" class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <input type="hidden" name="nim" id="sidangModalNim" value="">
            <input type="hidden" name="id_preview" id="sidangModalPreviewId" value="">
            
            <button type="button" onclick="closeModalRekomen('modalKonfirmasiSidang')" class="px-6 py-2.5 bg-slate-500 hover:bg-slate-600 text-white font-bold text-sm rounded-xl transition">Batal</button>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm rounded-xl shadow-md shadow-orange-500/20 transition">Ya, Lanjutkan</button>
        </form>
    </div>
</div>


<!-- 3. POP-UP MODAL PILIH JALUR NON SIDANG (FOTO 4 & 5) -->
<div id="modalPilihNonSidang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md">
    <div class="relative bg-white rounded-[2.5rem] p-7 sm:p-9 w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 transform transition-all">
        <button onclick="closeModalRekomen('modalPilihNonSidang')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-800 hover:text-white font-bold text-xl flex items-center justify-center transition leading-none">&times;</button>
        
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h3 id="modalPilihNonSidangHeaderTitle" class="text-2xl font-extrabold text-slate-900 tracking-tight">Pilih Jalur Tugas Akhir</h3>
            <p id="modalPilihNonSidangHeaderSub" class="text-xs text-slate-500 font-medium mt-1">Pilih kategori ekuivalensi yang direkomendasikan untuk mahasiswa.</p>
        </div>


        <!-- Dynamic Horizontal Slider Container for Non-Sidang Options -->
        <div class="relative group">
            <button onclick="scrollNonSidangSlider('left')" type="button" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white shadow-xl border border-slate-200 text-slate-700 hover:text-orange-600 flex items-center justify-center transition cursor-pointer hover:scale-110 active:scale-95">
                <i class="bi bi-chevron-left text-base font-bold"></i>
            </button>

            <div id="gridNonSidangOptions" class="flex overflow-x-auto snap-x snap-mandatory space-x-5 p-3 pb-6 scrollbar-none scroll-smooth">
                <!-- Content injected dynamically via JS -->
                <div class="w-full text-center py-10 text-slate-400 font-medium">
                    <i class="bi bi-arrow-repeat animate-spin text-2xl"></i> Memuat pilihan jalur non-sidang...
                </div>
            </div>

            <button onclick="scrollNonSidangSlider('right')" type="button" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white shadow-xl border border-slate-200 text-slate-700 hover:text-orange-600 flex items-center justify-center transition cursor-pointer hover:scale-110 active:scale-95">
                <i class="bi bi-chevron-right text-base font-bold"></i>
            </button>
        </div>
    </div>
</div>


<!-- 4. POP-UP MODAL FORM UPLOAD PERSYARATAN NON SIDANG (FOTO 7) -->
<div id="modalFormNonSidang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md">
    <div class="relative bg-white rounded-[2rem] p-7 sm:p-9 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 transform transition-all">
        <button onclick="closeModalRekomen('modalFormNonSidang')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-800 hover:text-white font-bold text-xl flex items-center justify-center transition leading-none">&times;</button>
        
        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 mb-6 border-b border-slate-100 pb-4" id="modalFormNonSidangTitle">Upload persyaratan untuk Jalur ...</h3>

        <form id="formNonSidangSubmit" action="<?= site_url('mahasiswa/submit_rekomendasi_nonsidang') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="nim" id="nonSidangNim" value="">
            <input type="hidden" name="id_preview" id="nonSidangPreviewId" value="">
            <input type="hidden" name="jalur_id" id="nonSidangJalurId" value="">

            <!-- Dynamic Form Fields Container -->
            <div id="dynamicFormFieldsContainer" class="space-y-6">
                <!-- Injected dynamically via JS based on selected non-sidang option -->
            </div>

            <!-- Upload Progress Bar Container -->
            <div id="uploadProgressBarContainer" class="hidden space-y-2 py-3">
                <div class="flex items-center justify-between text-xs font-extrabold">
                    <span id="uploadProgressText" class="text-slate-700 flex items-center gap-1.5"><i class="bi bi-cloud-arrow-up-fill text-orange-500 animate-bounce"></i> Mengunggah berkas...</span>
                    <span id="uploadProgressPercent" class="text-orange-600 font-mono text-sm font-black">0%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden p-0.5 border border-slate-200 shadow-inner">
                    <div id="uploadProgressBarFill" class="bg-gradient-to-r from-orange-500 via-amber-500 to-emerald-500 h-full rounded-full transition-all duration-200 shadow-sm" style="width: 0%"></div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModalRekomen('modalFormNonSidang')" class="px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-sm rounded-xl transition">Batal</button>
                <button type="submit" class="px-7 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/20 transition transform hover:-translate-y-0.5">Kirim Rekomendasi</button>
            </div>
        </form>
    </div>
</div>



<!-- 5. POP-UP MODAL KELOLA JALUR & FORM PERSYARATAN (CRUD DINAMIS ADMIN/DOSEN) -->
<div id="modalManageCrud" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md">
    <div class="relative bg-white rounded-[2rem] p-7 sm:p-9 w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 transform transition-all">
        <button onclick="closeModalRekomen('modalManageCrud')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-800 hover:text-white font-bold text-xl flex items-center justify-center transition leading-none">&times;</button>
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-4 gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Kelola Jalur Sidang/Non-Sidang &amp; Form Requirements (Dinamis)</h3>
                <p class="text-xs text-slate-500">Tambah, edit, atau hapus pilihan jalur serta persyaratannya secara dinamis.</p>
            </div>
            <button onclick="openFormAddJalur()" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-sm transition shrink-0">
                <i class="bi bi-plus-lg mr-1"></i> Tambah Jalur Baru
            </button>
        </div>

        <div id="crudJalurList" class="space-y-6">
            <!-- Rendered dynamically -->
        </div>
    </div>
</div>


<!-- JAVASCRIPT LOGIC FOR REKOMENDASI MODALS -->
<script>
let rekomenActiveOptions = [];
let currentSelectedStudentNim = '';
let currentSelectedPreviewId = '';

let cachedMainOptions = null;
let cachedNonSidangOptions = null;

function prefetchRekomenOptions(callback = null) {
    fetch('<?= site_url("mahasiswa/ajax_get_rekomen_options") ?>?t=' + new Date().getTime())
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                cachedMainOptions = res.main_options || [];
                cachedNonSidangOptions = res.data || [];
                rekomenActiveOptions = res.data || [];
                renderMainCategoryGrid(cachedMainOptions);
                if (typeof callback === 'function') callback(res);
            }
        })
        .catch(err => console.error(err));
}

document.addEventListener('DOMContentLoaded', () => {
    prefetchRekomenOptions();
});

function openRekomendasiModal(nim = '', previewId = '') {
    currentSelectedStudentNim = nim;
    currentSelectedPreviewId = previewId;
    
    document.getElementById('sidangModalNim').value = nim;
    document.getElementById('sidangModalPreviewId').value = previewId;
    
    document.getElementById('nonSidangNim').value = nim;
    document.getElementById('nonSidangPreviewId').value = previewId;

    document.getElementById('modalRekomendasiUtama').classList.remove('hidden');
    prefetchRekomenOptions();
}

function closeModalRekomen(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openModalKonfirmasiSidang() {
    closeModalRekomen('modalRekomendasiUtama');
    document.getElementById('modalKonfirmasiSidang').classList.remove('hidden');
}

function openModalPilihNonSidang() {
    closeModalRekomen('modalRekomendasiUtama');
    document.getElementById('modalPilihNonSidang').classList.remove('hidden');
    loadNonSidangOptions();
}

function loadMainCategoryOptions() {
    if (cachedMainOptions && cachedMainOptions.length > 0) {
        renderMainCategoryGrid(cachedMainOptions);
    }
    prefetchRekomenOptions();
}


function scrollMainSlider(direction) {
    const el = document.getElementById('gridMainCategoryOptions');
    if (el) el.scrollBy({ left: direction === 'left' ? -320 : 320, behavior: 'smooth' });
}

function scrollNonSidangSlider(direction) {
    const el = document.getElementById('gridNonSidangOptions');
    if (el) el.scrollBy({ left: direction === 'left' ? -280 : 280, behavior: 'smooth' });
}

function renderMainCategoryGrid(options) {
    const container = document.getElementById('gridMainCategoryOptions');
    if (!container || !options || options.length === 0) return;

    let html = '';
    options.forEach(opt => {
        const titleUpper = (opt.title || '').toUpperCase();
        const isSidang = opt.code === 'main_sidang' || opt.code === 'sidang_reguler' || titleUpper === 'SIDANG';
        
        let onClickAction = '';
        if (isSidang) {
            onClickAction = 'openModalKonfirmasiSidang()';
        } else {
            let catKey = opt.category;
            if (catKey === 'main') {
                if (opt.code && opt.code.startsWith('main_')) {
                    catKey = opt.code.replace('main_', '');
                } else {
                    catKey = (opt.title || '').toLowerCase().trim().replace(/[^a-z0-9]/g, '_');
                }
            }
            onClickAction = `openModalCategoryFilter('${catKey}', '${opt.title.replace(/'/g, "\\'")}')`;
        }


        let badgeText = opt.category === 'main' ? (isSidang ? 'Jalur Reguler' : 'Jalur Ekuivalensi') : opt.category.toUpperCase().replace('_', ' ');
        let iconClass = opt.icon_class || (isSidang ? 'bi-mortarboard-fill' : 'bi-award-fill');

        html += `
        <div onclick="${onClickAction}" class="group relative bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 rounded-[2rem] p-7 text-center text-white cursor-pointer shadow-xl shadow-orange-500/20 hover:shadow-2xl hover:shadow-orange-500/35 hover:-translate-y-1.5 transition-all duration-300 border-2 border-white/20 flex flex-col items-center justify-between min-h-[290px] min-w-[280px] sm:min-w-[310px] max-w-[320px] shrink-0 snap-center">
            <div class="w-full flex justify-end">
                <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">${badgeText}</span>
            </div>
            
            <div class="my-3 transform group-hover:scale-110 transition duration-300">
                <div class="w-24 h-24 mx-auto bg-white/10 rounded-3xl backdrop-blur-md flex items-center justify-center border border-white/25 shadow-inner">
                    <i class="bi ${iconClass} text-5xl text-amber-200 drop-shadow-md"></i>
                </div>
            </div>

            <div class="space-y-1">
                <h4 class="text-2xl sm:text-3xl font-black tracking-wider uppercase drop-shadow-sm">${opt.title}</h4>
                <p class="text-xs text-orange-100 font-medium line-clamp-2">${opt.description || 'Pilihan jalur kelulusan Tugas Akhir'}</p>
            </div>
        </div>
        `;
    });
    container.innerHTML = html;
}

function openModalCategoryFilter(catKey, catTitle = '') {
    closeModalRekomen('modalRekomendasiUtama');

    const headerTitle = document.getElementById('modalPilihNonSidangHeaderTitle');
    const headerSub = document.getElementById('modalPilihNonSidangHeaderSub');
    if (headerTitle) {
        headerTitle.textContent = catTitle ? `Pilih Jalur ${catTitle}` : 'Pilih Jalur Tugas Akhir';
    }
    if (headerSub) {
        headerSub.textContent = catTitle ? `Pilih opsi ekuivalensi / jalur ${catTitle} yang direkomendasikan untuk mahasiswa.` : 'Pilih kategori ekuivalensi yang direkomendasikan untuk mahasiswa.';
    }

    document.getElementById('modalPilihNonSidang').classList.remove('hidden');
    loadNonSidangOptions(catKey);
}


function loadNonSidangOptions(filterCategory = null) {
    if (cachedNonSidangOptions && cachedNonSidangOptions.length > 0) {
        let options = cachedNonSidangOptions;
        if (filterCategory) {
            options = options.filter(o => o.category === filterCategory);
        }

        renderNonSidangGrid(options);
        return;
    }


    const container = document.getElementById('gridNonSidangOptions');
    container.innerHTML = '<div class="w-full text-center py-10 text-slate-400 font-medium"><i class="bi bi-arrow-repeat animate-spin text-2xl"></i> Memuat pilihan jalur...</div>';
    prefetchRekomenOptions();
}


function renderNonSidangGrid(options) {
    const container = document.getElementById('gridNonSidangOptions');
    if (!options || options.length === 0) {
        container.innerHTML = '<div class="w-full text-center py-10 text-slate-400">Belum ada pilihan jalur non-sidang yang aktif.</div>';
        return;
    }

    let html = '';
    options.forEach(opt => {
        let iconClass = opt.icon_class || 'bi-award-fill';
        html += `
        <div onclick="selectNonSidangOption(${opt.id})" class="group bg-gradient-to-br from-orange-500 to-amber-500 rounded-[2rem] p-6 text-center text-white cursor-pointer shadow-lg shadow-orange-500/15 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-1 transition duration-200 flex flex-col items-center justify-between min-h-[220px] min-w-[240px] sm:min-w-[270px] max-w-[280px] shrink-0 snap-center">
            <div class="w-full flex justify-end">
                <i class="bi bi-arrow-right-circle-fill text-white/40 group-hover:text-white text-xl transition"></i>
            </div>
            
            <div class="w-20 h-20 mx-auto bg-white/10 rounded-2xl backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-inner group-hover:scale-110 transition duration-300">
                <i class="bi ${iconClass} text-4xl text-amber-200"></i>
            </div>
            
            <div class="mt-4">
                <h4 class="font-extrabold text-lg text-white leading-tight">${opt.title}</h4>
                <p class="text-[11px] text-orange-100 font-medium mt-1 line-clamp-1">${opt.description || 'Pilihan jalur tugas akhir'}</p>
            </div>
        </div>
        `;
    });
    container.innerHTML = html;
}


function createDropzoneHtml(fieldKey, fieldLabel, isRequired, allowedExt = 'pdf,docx,doc') {
    const reqStar = isRequired ? '<span class="text-rose-600 font-bold">*</span>' : '';
    const extUpper = (allowedExt || 'pdf,docx,doc').toUpperCase().replace(/,/g, ', ');
    
    return `
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">${fieldLabel} ${reqStar}</label>
        
        <div class="dropzone-container relative border-2 border-dashed border-slate-300 hover:border-orange-500 rounded-2xl p-5 bg-slate-50/70 hover:bg-orange-50/40 text-center transition-all duration-200 cursor-pointer group">
            <input type="file" name="${fieldKey}" accept=".${allowedExt.replace(/,/g, ',.')}" ${isRequired ? 'required' : ''} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20 file-dropzone-input" onchange="handleFileDropzoneChange(this)">
            
            <div class="dropzone-default flex flex-col items-center justify-center space-y-2 pointer-events-none">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 group-hover:bg-orange-500 group-hover:text-white text-orange-600 flex items-center justify-center text-2xl transition duration-200 shadow-inner">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
                <div>
                    <p class="text-xs font-extrabold text-slate-800"><span class="text-orange-600 underline">Klik untuk upload</span> atau geser file (drag & drop) ke sini</p>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Format file: <span class="font-mono text-slate-600 uppercase font-bold">${extUpper}</span></p>
                </div>
            </div>

            <div class="dropzone-preview hidden flex items-center justify-between p-3.5 bg-white rounded-xl border border-emerald-300 shadow-sm pointer-events-none">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div class="text-left overflow-hidden">
                        <p class="dropzone-file-name text-xs font-bold text-slate-900 truncate">file_name.pdf</p>
                        <p class="dropzone-file-size text-[10px] text-slate-500 font-semibold">1.2 MB</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase rounded-full">Siap Diunggah</span>
            </div>
        </div>
    </div>
    `;
}

function handleFileDropzoneChange(input) {
    const container = input.closest('.dropzone-container');
    if (!container) return;

    const defaultState = container.querySelector('.dropzone-default');
    const previewState = container.querySelector('.dropzone-preview');
    const fileNameEl = container.querySelector('.dropzone-file-name');
    const fileSizeEl = container.querySelector('.dropzone-file-size');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        
        if (fileNameEl) fileNameEl.textContent = file.name;
        if (fileSizeEl) fileSizeEl.textContent = `${fileSizeMB} MB`;

        if (defaultState) defaultState.classList.add('hidden');
        if (previewState) previewState.classList.remove('hidden');
        container.classList.add('border-emerald-400', 'bg-emerald-50/30');
        container.classList.remove('border-slate-300', 'hover:border-orange-500');
    } else {
        if (defaultState) defaultState.classList.remove('hidden');
        if (previewState) previewState.classList.add('hidden');
        container.classList.remove('border-emerald-400', 'bg-emerald-50/30');
        container.classList.add('border-slate-300', 'hover:border-orange-500');
    }
}

function selectNonSidangOption(optionId) {
    const opt = rekomenActiveOptions.find(o => o.id == optionId);
    if (!opt) return;

    closeModalRekomen('modalPilihNonSidang');
    document.getElementById('modalFormNonSidangTitle').textContent = `Upload Persyaratan Rekomendasi ${opt.title}`;
    document.getElementById('nonSidangJalurId').value = opt.id;

    const fieldsContainer = document.getElementById('dynamicFormFieldsContainer');
    let fieldsHtml = '';

    if (opt.fields && opt.fields.length > 0) {
        opt.fields.forEach(f => {
            const reqStar = f.is_required ? '<span class="text-rose-600 font-bold">*</span>' : '';
            
            if (f.field_type === 'file') {
                fieldsHtml += createDropzoneHtml(f.field_key, f.field_label, f.is_required, f.allowed_ext);
            } else if (f.field_type === 'textarea') {
                fieldsHtml += `
                <div class="space-y-1.5 pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">${f.field_label} ${reqStar}</label>
                    <textarea name="${f.field_key}" rows="4" placeholder="${f.help_text || 'Masukan alasan direkomendasikan ke jalur TA'}" ${f.is_required ? 'required' : ''} class="w-full p-3.5 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-medium text-slate-800"></textarea>
                </div>
                `;
            } else {
                fieldsHtml += `
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">${f.field_label} ${reqStar}</label>
                    <input type="text" name="${f.field_key}" placeholder="${f.help_text || ''}" ${f.is_required ? 'required' : ''} class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-medium text-slate-800">
                </div>
                `;
            }
        });
    } else {
        fieldsHtml = `
        ${createDropzoneHtml('eviden', 'Eviden Berkas', true, 'pdf,docx,doc')}
        ${createDropzoneHtml('persetujuan_pembimbing', 'Persetujuan Pembimbing', true, 'pdf,docx,doc')}
        <div class="space-y-1.5 pt-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan / Tanggapan Rekomendasi Jalur ${opt.title}</label>
            <textarea name="catatan_alasan" rows="4" placeholder="Masukan alasan direkomendasikan ke jalur TA" class="w-full p-3.5 rounded-xl border border-slate-300 text-xs font-medium"></textarea>
        </div>
        `;
    }

    fieldsContainer.innerHTML = fieldsHtml;

    // Reset Progress Bar
    const progressContainer = document.getElementById('uploadProgressBarContainer');
    if (progressContainer) progressContainer.classList.add('hidden');

    document.getElementById('modalFormNonSidang').classList.remove('hidden');
}

// Drag & Drop Feedback Listeners
document.addEventListener('dragover', (e) => {
    const dropzone = e.target.closest('.dropzone-container');
    if (dropzone) {
        e.preventDefault();
        dropzone.classList.add('border-orange-500', 'bg-orange-50/70');
    }
});

document.addEventListener('dragleave', (e) => {
    const dropzone = e.target.closest('.dropzone-container');
    if (dropzone) {
        dropzone.classList.remove('border-orange-500', 'bg-orange-50/70');
    }
});

// Handle Form Submissions via AJAX
document.addEventListener('DOMContentLoaded', () => {
    const formSidang = document.getElementById('formSubmitSidang');
    if (formSidang) {
        formSidang.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Memproses...';

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async res => {
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch(err) {
                    console.error('Server HTML Response:', text);
                    alert('Terjadi kesalahan server. Cek console (F12).');
                    return;
                }
                if (data.status === 'success') {
                    if (typeof showToast === 'function') showToast(data.message, 'success');
                    else alert(data.message);
                    closeModalRekomen('modalKonfirmasiSidang');
                    if (typeof fetchBimbinganData === 'function') {
                        fetchBimbinganData(true);
                    } else {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(err => { console.error(err); alert('Terjadi kesalahan koneksi.'); })
            .finally(() => { btn.disabled = false; btn.innerHTML = 'Ya, Lanjutkan'; });
        });
    }

    const formNonSidang = document.getElementById('formNonSidangSubmit');
    if (formNonSidang) {
        formNonSidang.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Mengirim...';

            const progressContainer = document.getElementById('uploadProgressBarContainer');
            const progressFill = document.getElementById('uploadProgressBarFill');
            const progressPercent = document.getElementById('uploadProgressPercent');
            const progressText = document.getElementById('uploadProgressText');

            if (progressContainer) progressContainer.classList.remove('hidden');
            if (progressFill) progressFill.style.width = '0%';
            if (progressPercent) progressPercent.textContent = '0%';

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', this.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    if (progressFill) progressFill.style.width = percent + '%';
                    if (progressPercent) progressPercent.textContent = percent + '%';
                    
                    if (progressText) {
                        if (percent < 100) {
                            const loadedKB = (e.loaded / 1024).toFixed(0);
                            const totalKB = (e.total / 1024).toFixed(0);
                            progressText.innerHTML = `<i class="bi bi-cloud-arrow-up-fill text-orange-500 animate-bounce"></i> Mengunggah berkas (${loadedKB} KB / ${totalKB} KB)...`;
                        } else {
                            progressText.innerHTML = `<i class="bi bi-check-circle-fill text-emerald-500"></i> Memproses berkas & simpan rekomendasi...`;
                        }
                    }
                }
            });

            xhr.onload = function() {
                btn.disabled = false;
                btn.innerHTML = 'Kirim Rekomendasi';

                let data;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch(err) {
                    console.error('Server Response:', xhr.responseText);
                    alert('Terjadi kesalahan server. Cek console (F12).');
                    return;
                }

                if (xhr.status === 200 && data.status === 'success') {
                    if (typeof showToast === 'function') showToast(data.message, 'success');
                    else alert(data.message);
                    closeModalRekomen('modalFormNonSidang');
                    if (progressContainer) progressContainer.classList.add('hidden');
                    if (typeof fetchBimbinganData === 'function') {
                        fetchBimbinganData(true);
                    } else {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            };

            xhr.onerror = function() {
                btn.disabled = false;
                btn.innerHTML = 'Kirim Rekomendasi';
                alert('Terjadi kesalahan koneksi saat mengunggah.');
            };

            xhr.send(formData);
        });
    }

});


// Admin/Lecturer Dynamic CRUD Management Logic
function openModalManageCrud() {
    closeModalRekomen('modalRekomendasiUtama');
    document.getElementById('modalManageCrud').classList.remove('hidden');
    loadCrudJalurList();
}

function loadCrudJalurList() {
    const container = document.getElementById('crudJalurList');
    container.innerHTML = '<div class="text-center py-8 text-slate-400 font-medium"><i class="bi bi-arrow-repeat animate-spin"></i> Memuat data kelola jalur...</div>';

    fetch('<?= site_url("mahasiswa/ajax_get_all_rekomen_crud") ?>')
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                renderCrudJalurList(res.data);
            } else {
                container.innerHTML = '<div class="text-center py-8 text-rose-500">Gagal memuat data.</div>';
            }
        })
        .catch(err => { console.error(err); container.innerHTML = '<div class="text-center py-8 text-rose-500">Kesalahan koneksi.</div>'; });
}

function renderCrudJalurList(options) {
    const container = document.getElementById('crudJalurList');
    let html = '';

    options.forEach(opt => {
        let fieldsHtml = '';
        if (opt.fields && opt.fields.length > 0) {
            opt.fields.forEach(f => {
                fieldsHtml += `
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <div>
                        <span class="font-bold text-slate-800">${f.field_label}</span>
                        <span class="text-[10px] text-slate-500 bg-slate-200 px-2 py-0.5 rounded-md ml-2 font-mono">${f.field_type}</span>
                        <span class="text-[10px] text-rose-600 ml-2 font-semibold">(${f.allowed_ext || 'pdf,docx'})</span>
                    </div>
                    <button onclick="deleteFieldCrud(${f.id})" class="text-rose-600 hover:text-rose-800 text-xs font-bold">&times; Hapus</button>
                </div>
                `;
            });
        } else {
            fieldsHtml = '<p class="text-xs text-slate-400 italic">Belum ada field khusus.</p>';
        }

        html += `
        <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xl">
                        <i class="bi ${opt.icon_class}"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">${opt.title}</h4>
                        <p class="text-xs text-slate-500 font-medium">${opt.description || '-'}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="addFieldCrud(${opt.id})" class="px-3 py-1.5 bg-emerald-100 text-emerald-800 hover:bg-emerald-200 text-xs font-bold rounded-lg border border-emerald-300 transition">+ Tambah Field</button>
                    <button onclick="deleteOptionCrud(${opt.id})" class="px-3 py-1.5 bg-rose-100 text-rose-800 hover:bg-rose-200 text-xs font-bold rounded-lg border border-rose-300 transition">Hapus Jalur</button>
                </div>
            </div>
            
            <div class="space-y-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Form Persyaratan (${opt.fields ? opt.fields.length : 0}):</span>
                ${fieldsHtml}
            </div>
        </div>
        `;
    });

    container.innerHTML = html;
}

function openFormAddJalur() {
    const title = prompt("Masukkan Nama Jalur Non-Sidang Baru:");
    if (!title) return;
    const desc = prompt("Masukkan Deskripsi Singkat Jalur:");
    
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', desc || '');
    formData.append('category', 'non_sidang');

    fetch('<?= site_url("mahasiswa/ajax_save_rekomen_option") ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        loadCrudJalurList();
    });
}

function addFieldCrud(jalurId) {
    const label = prompt("Masukkan Label Field Persyaratan (Contoh: Surat Izin Industri):");
    if (!label) return;
    const type = prompt("Tipe Field (file / text / textarea):", "file");

    const formData = new FormData();
    formData.append('jalur_id', jalurId);
    formData.append('field_label', label);
    formData.append('field_type', type || 'file');
    formData.append('allowed_ext', 'pdf,docx,doc');
    formData.append('is_required', 1);

    fetch('<?= site_url("mahasiswa/ajax_save_rekomen_field") ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        loadCrudJalurList();
    });
}

function deleteOptionCrud(id) {
    if (!confirm("Yakin ingin menghapus jalur ini beserta semua persyaratannya?")) return;
    const formData = new FormData();
    formData.append('id', id);

    fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_option") ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        loadCrudJalurList();
    });
}

function deleteFieldCrud(id) {
    if (!confirm("Hapus field ini?")) return;
    const formData = new FormData();
    formData.append('id', id);

    fetch('<?= site_url("mahasiswa/ajax_delete_rekomen_field") ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        loadCrudJalurList();
    });
}
</script>
