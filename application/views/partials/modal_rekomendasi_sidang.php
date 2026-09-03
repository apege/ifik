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

        <!-- Cards Container (Sidang, Non-Sidang & Custom Main Categories) -->
        <div id="gridMainCategoryOptions" class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            <!-- Card 1: SIDANG -->
            <div onclick="openModalKonfirmasiSidang()" class="group relative bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 rounded-[2rem] p-8 text-center text-white cursor-pointer shadow-xl shadow-orange-500/20 hover:shadow-2xl hover:shadow-orange-500/35 hover:-translate-y-1.5 transition-all duration-300 border-2 border-white/20 flex flex-col items-center justify-between min-h-[300px]">
                <div class="w-full flex justify-end">
                    <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">Jalur Reguler</span>
                </div>
                
                <div class="my-4 transform group-hover:scale-110 transition duration-300">
                    <div class="w-28 h-28 mx-auto bg-white/10 rounded-3xl backdrop-blur-md flex items-center justify-center border border-white/25 shadow-inner">
                        <i class="bi bi-mortarboard-fill text-6xl text-amber-200 drop-shadow-md"></i>
                    </div>
                </div>

                <div class="space-y-1">
                    <h4 class="text-3xl font-black tracking-wider uppercase drop-shadow-sm">SIDANG</h4>
                    <p class="text-xs text-orange-100 font-medium">Lanjut ke Majelis Ujian Sidang Akhir TA</p>
                </div>
            </div>

            <!-- Card 2: NON SIDANG -->
            <div onclick="openModalPilihNonSidang()" class="group relative bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 rounded-[2rem] p-8 text-center text-white cursor-pointer shadow-xl shadow-orange-500/20 hover:shadow-2xl hover:shadow-orange-500/35 hover:-translate-y-1.5 transition-all duration-300 border-2 border-white/20 flex flex-col items-center justify-between min-h-[300px]">
                <div class="w-full flex justify-end">
                    <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">Jalur Ekuivalensi</span>
                </div>
                
                <div class="my-4 transform group-hover:scale-110 transition duration-300">
                    <div class="w-28 h-28 mx-auto bg-white/10 rounded-3xl backdrop-blur-md flex items-center justify-center border border-white/25 shadow-inner">
                        <i class="bi bi-award-fill text-6xl text-amber-200 drop-shadow-md"></i>
                    </div>
                </div>

                <div class="space-y-1">
                    <h4 class="text-3xl font-black tracking-wider uppercase drop-shadow-sm">NON SIDANG</h4>
                    <p class="text-xs text-orange-100 font-medium">Rekognisi Prestasi / HKI / Kebijakan / MBKM</p>
                </div>
            </div>
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
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pilih Jalur Tugas Akhir</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Pilih kategori ekuivalensi Non-Sidang yang direkomendasikan untuk mahasiswa.</p>
        </div>

        <!-- Dynamic Grid Container for Non-Sidang Options -->
        <div id="gridNonSidangOptions" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-2">
            <!-- Content will be injected via JS dynamically from database -->
            <div class="col-span-full text-center py-10 text-slate-400 font-medium">
                <i class="bi bi-arrow-repeat animate-spin text-2xl"></i> Memuat pilihan jalur non-sidang...
            </div>
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

function openRekomendasiModal(nim = '', previewId = '') {
    currentSelectedStudentNim = nim;
    currentSelectedPreviewId = previewId;
    
    document.getElementById('sidangModalNim').value = nim;
    document.getElementById('sidangModalPreviewId').value = previewId;
    
    document.getElementById('nonSidangNim').value = nim;
    document.getElementById('nonSidangPreviewId').value = previewId;

    document.getElementById('modalRekomendasiUtama').classList.remove('hidden');
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

function loadNonSidangOptions() {
    const container = document.getElementById('gridNonSidangOptions');
    container.innerHTML = '<div class="col-span-full text-center py-10 text-slate-400 font-medium"><i class="bi bi-arrow-repeat animate-spin text-2xl"></i> Memuat pilihan jalur...</div>';

    fetch('<?= site_url("mahasiswa/ajax_get_rekomen_options") ?>')
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                rekomenActiveOptions = res.data;
                renderNonSidangGrid(res.data);
            } else {
                container.innerHTML = '<div class="col-span-full text-center py-10 text-rose-500">Gagal memuat pilihan jalur.</div>';
            }
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<div class="col-span-full text-center py-10 text-rose-500">Terjadi kesalahan koneksi.</div>';
        });
}

function renderNonSidangGrid(options) {
    const container = document.getElementById('gridNonSidangOptions');
    if (!options || options.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-10 text-slate-400">Belum ada pilihan jalur non-sidang yang aktif.</div>';
        return;
    }

    let html = '';
    options.forEach(opt => {
        let iconClass = opt.icon_class || 'bi-award-fill';
        html += `
        <div onclick="selectNonSidangOption(${opt.id})" class="group bg-gradient-to-br from-orange-500 to-amber-500 rounded-[2rem] p-6 text-center text-white cursor-pointer shadow-lg shadow-orange-500/15 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-1 transition duration-200 flex flex-col items-center justify-between min-h-[220px]">
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

function selectNonSidangOption(optionId) {
    const opt = rekomenActiveOptions.find(o => o.id == optionId);
    if (!opt) return;

    closeModalRekomen('modalPilihNonSidang');
    document.getElementById('modalFormNonSidangTitle').textContent = `Upload persyaratan untuk Jalur ${opt.title}`;
    document.getElementById('nonSidangJalurId').value = opt.id;

    const fieldsContainer = document.getElementById('dynamicFormFieldsContainer');
    let fieldsHtml = '';

    if (opt.fields && opt.fields.length > 0) {
        opt.fields.forEach(f => {
            const reqStar = f.is_required ? '<span class="text-rose-600 font-bold">*</span>' : '';
            const helpText = f.help_text ? `<p class="text-xs text-rose-500 font-medium mb-1 tracking-wide">*) ${f.help_text}</p>` : '<p class="text-xs text-rose-500 font-medium mb-1">*) Format file diharuskan pdf/docx</p>';
            
            if (f.field_type === 'file') {
                fieldsHtml += `
                <div class="space-y-1.5">
                    ${helpText}
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">${f.field_label} ${reqStar}</label>
                    <input type="file" name="${f.field_key}" accept=".pdf,.docx,.doc" ${f.is_required ? 'required' : ''} class="w-full p-2.5 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-xs font-medium bg-slate-50/50">
                </div>
                `;
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
        <div class="space-y-1.5">
            <p class="text-xs text-rose-500 font-medium mb-1">*) Format file diharuskan pdf/docx</p>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Eviden <span class="text-rose-600">*</span></label>
            <input type="file" name="eviden" accept=".pdf,.docx,.doc" required class="w-full p-2.5 rounded-xl border border-slate-300 text-xs">
        </div>
        <div class="space-y-1.5">
            <p class="text-xs text-rose-500 font-medium mb-1">*) Format file diharuskan pdf/docx</p>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Persetujuan Pembimbing <span class="text-rose-600">*</span></label>
            <input type="file" name="persetujuan_pembimbing" accept=".pdf,.docx,.doc" required class="w-full p-2.5 rounded-xl border border-slate-300 text-xs">
        </div>
        <div class="space-y-1.5 pt-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggapan untuk rekomendasi non sidang jalur ${opt.title}</label>
            <textarea name="catatan_alasan" rows="4" placeholder="Masukan alasan direkomendasikan ke jalur TA" class="w-full p-3.5 rounded-xl border border-slate-300 text-xs font-medium"></textarea>
        </div>
        `;
    }

    fieldsContainer.innerHTML = fieldsHtml;
    document.getElementById('modalFormNonSidang').classList.remove('hidden');
}

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
                    setTimeout(() => window.location.reload(), 1000);
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
                    closeModalRekomen('modalFormNonSidang');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(err => { console.error(err); alert('Terjadi kesalahan koneksi saat mengunggah.'); })
            .finally(() => { btn.disabled = false; btn.innerHTML = 'Kirim Rekomendasi'; });
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
