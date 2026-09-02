<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Bimbingan Dosen — IFIK Portal'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        /* Unified Multi-Search Pill Component */
        .search-pill-container { position: relative; display: flex; align-items: center; gap: 8px; width: 100%; }
        .unified-search-pill {
            display: flex; align-items: center; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;
            padding: 2px 12px; flex: 1; height: 46px; transition: all 0.2s ease;
        }
        .unified-search-pill:focus-within {
            border-color: #ea580c !important; background: #ffffff !important; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }

        /* Rotating Border Table */
        @keyframes spinRotatingBorder { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .table-rotating-border-wrap {
            position: relative; border-radius: 16px; padding: 2px; overflow: hidden;
            box-shadow: 0 12px 36px -8px rgba(234, 88, 12, 0.15), 0 4px 16px rgba(71, 85, 105, 0.06); background: #ffffff;
        }
        .table-rotating-border-spin {
            position: absolute; inset: -350%; pointer-events: none; opacity: 0.95;
            background: conic-gradient(from 90deg at 50% 50%, #ea580c 0%, #f97316 12%, #ffffff 22%, #cbd5e1 35%, #475569 48%, #1e293b 58%, #ea580c 68%, #ffffff 80%, #94a3b8 90%, #ea580c 100%);
            animation: spinRotatingBorder 7s linear infinite;
        }
        .table-rotating-border-inner { position: relative; z-index: 10; width: 100%; background: #ffffff; border-radius: 14px; overflow: hidden; }
        .table-custom-rounded { border-collapse: separate !important; border-spacing: 0 !important; width: 100%; }
        .table-custom-rounded thead tr th:first-child { border-top-left-radius: 14px; }
        .table-custom-rounded thead tr th:last-child { border-top-right-radius: 14px; }
        
        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; gap: 0.375rem; border: 1px solid transparent; }
        .badge-success { background-color: #d1fae5; color: #065f46; border-color: #34d399; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border-color: #fbbf24; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; border-color: #fb7185; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }

        /* Hover Preview Panel */
        #hoverPreviewPanel {
            position: fixed;
            z-index: 999;
            width: 520px;
            max-width: 95vw;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 64px -12px rgba(234,88,12,0.18), 0 8px 24px rgba(71,85,105,0.10);
            border: 1.5px solid #fed7aa;
            overflow: hidden;
            display: none;
            pointer-events: auto;
            transition: opacity 0.18s, transform 0.18s;
        }
        #hoverPreviewPanel.visible { display: block; }
        #hoverPreviewPanel .panel-header {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: #fff;
            padding: 14px 18px;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #hoverPreviewPanel .pdf-frame-wrap {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        #hoverPreviewPanel .pdf-frame-wrap iframe {
            width: 100%; height: 100%; border: none;
        }
        #hoverPreviewPanel .pdf-no-file {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            flex-direction: column;
            gap: 8px;
        }
        #hoverPreviewPanel .panel-body { padding: 16px 18px 18px; }
        #hoverPreviewPanel .panel-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        /* P2 comment modal */
        #p2CommentModal {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        #p2CommentModal.open { display: flex; }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <?php $this->load->view('partials/mahasiswa_navbar'); ?>

    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Hero Card Dosen -->
        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-4 max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Dashboard Bimbingan Dosen
                    </h1>
                    <p class="text-sm sm:text-base text-orange-100/95 font-normal leading-relaxed">
                        Kelola dan evaluasi dokumen Tugas Akhir mahasiswa bimbingan Anda.
                    </p>
                </div>
                <div class="w-full xl:w-[400px] bg-black/25 backdrop-blur-xl rounded-3xl p-6 border border-white/20 shadow-2xl space-y-4 text-white">
                    <div class="flex gap-4">
                        <a href="<?= site_url('mahasiswa/bimbingan?posisi=1') ?>" class="flex-1 py-3 px-4 rounded-2xl font-bold text-center border <?= $posisi == 1 ? 'bg-orange-500 border-orange-400 text-white shadow-lg' : 'bg-white/10 border-white/20 hover:bg-white/20' ?> transition">
                            <i class="bi bi-person-fill mr-2"></i> Sebagai P1
                        </a>
                        <a href="<?= site_url('mahasiswa/bimbingan?posisi=2') ?>" class="flex-1 py-3 px-4 rounded-2xl font-bold text-center border <?= $posisi == 2 ? 'bg-orange-500 border-orange-400 text-white shadow-lg' : 'bg-white/10 border-white/20 hover:bg-white/20' ?> transition">
                            <i class="bi bi-person mr-2"></i> Sebagai P2
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-lg shadow-orange-500/5 space-y-7 border border-slate-200">
            <div class="flex flex-wrap items-center justify-between border-b border-orange-100 pb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900">
                        <i class="bi bi-people-fill text-orange-500 text-xl"></i> Daftar Mahasiswa Bimbingan (Pembimbing <?= $posisi ?>)
                    </h3>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex flex-wrap gap-4 border-b border-slate-200 pb-4">
                <button onclick="switchDosenTab('preview1')" id="dosenTab1" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-orange-100 text-orange-700 border border-orange-300">Preview 1</button>
                <button onclick="switchDosenTab('preview2')" id="dosenTab2" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 2</button>
                <button onclick="switchDosenTab('preview3')" id="dosenTab3" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">Preview 3</button>
            </div>

            <!-- Filter Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4 mb-2" id="filterCardsContainer">
                <div onclick="setDosenFilter('all')" id="fCard_all" class="p-3 rounded-xl border border-orange-300 bg-orange-50 cursor-pointer transition text-center shadow-xs">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Semua</div>
                    <div class="text-xl font-black text-slate-800" id="fCount_all">0</div>
                </div>
                <div onclick="setDosenFilter('approved')" id="fCard_approved" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Disetujui</div>
                    <div class="text-xl font-black text-emerald-700" id="fCount_approved">0</div>
                </div>
                <div onclick="setDosenFilter('pending')" id="fCard_pending" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Pending</div>
                    <div class="text-xl font-black text-amber-700" id="fCount_pending">0</div>
                </div>
                <div onclick="setDosenFilter('revision')" id="fCard_revision" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-rose-50 hover:border-rose-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-rose-600 font-bold uppercase tracking-wider">Revisi</div>
                    <div class="text-xl font-black text-rose-700" id="fCount_revision">0</div>
                </div>
                <div onclick="setDosenFilter('empty')" id="fCard_empty" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 cursor-pointer transition text-center">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Kosong</div>
                    <div class="text-xl font-black text-slate-700" id="fCount_empty">0</div>
                </div>
            </div>

            <!-- Unified Table Container -->
            <div id="dosenTableContainer" class="space-y-4">
                <!-- Search Pill -->
                <div class="relative search-pill-container" id="multiSearchWrapper">
                    <div class="unified-search-pill">
                        <div class="flex-1 flex items-center pl-2">
                            <i class="bi bi-search text-slate-400 text-sm mr-3"></i>
                            <input type="text" id="searchInput" oninput="fetchBimbinganData()" placeholder="Cari Nama, NIM, Judul TA..." class="w-full text-sm font-medium bg-transparent border-none focus:outline-none text-slate-800">
                        </div>
                    </div>
                </div>

                <!-- Table with Rotating Conic-Gradient Border -->
                <div class="table-rotating-border-wrap mt-4">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-4 text-center w-12">
                                        <input type="checkbox" id="checkAllDosenStudents" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                    </th>
                                    <th class="py-4 px-4 font-bold">Mahasiswa & Judul TA</th>
                                    <th class="py-4 px-4">Berkas Terbaru</th>
                                    <th class="py-4 px-4 text-center">Waktu Upload</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                    <th class="py-4 px-4 text-center">Komentar P2</th>
                                    <th class="py-4 px-4 pr-6 text-right">Aksi (Review)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="bimbinganTableBody">
                                <tr><td colspan="7" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Review -->
            <div id="reviewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeReviewModal()"></div>
                <div class="relative bg-white rounded-3xl p-6 sm:p-8 w-full max-w-2xl shadow-2xl transform transition-all">
                    <button onclick="closeReviewModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                    <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2 border-b pb-4"><i class="bi bi-pencil-square text-orange-500"></i> <span id="modalTahapTitle">Review Berkas</span></h3>
                    
                    <div class="mb-5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <p class="text-sm font-bold text-slate-800 mb-1" id="modalStudentName">Nama Mahasiswa (NIM)</p>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Judul: <span id="modalJudul" class="text-slate-700 font-bold normal-case"></span></p>
                        <p class="text-sm text-slate-600 italic mb-3 border-l-2 border-orange-300 pl-3">Catatan Mahasiswa: "<span id="modalStudentNotes"></span>"</p>
                        <button type="button" onclick="openPdfModal(document.getElementById('modalFileLink').href)" id="modalFileLink" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold hover:bg-orange-200 transition cursor-pointer"><i class="bi bi-file-earmark-pdf-fill"></i> Lihat File Preview (In-Page)</button>
                    </div>
                    
                    <form id="formReview" action="<?= site_url('mahasiswa/review_preview') ?>" method="POST" class="space-y-4">
                        <input type="hidden" name="id_preview" id="modalIdPreview" value="">
                        <input type="hidden" name="posisi" value="<?= $posisi ?>">
                        
                        <?php if($posisi == 1): ?>
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 mb-4 hidden" id="modalRiwayatContainer">
                            <p class="text-sm font-bold text-blue-900 mb-1"><i class="bi bi-clock-history"></i> Catatan Sebelumnya:</p>
                            <p class="text-xs text-blue-800 italic" id="modalCatatanSebelumnya"></p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Penilaian (P1)</label>
                            <select name="status_pembimbing" id="modalStatus" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-semibold">
                                <option value="Pending">Menunggu Review</option>
                                <option value="Approved">Disetujui (ACC)</option>
                                <option value="Revision">Perlu Revisi</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan / Feedback Anda</label>
                            <textarea name="catatan_pembimbing" id="modalCatatan" rows="4" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-medium" placeholder="Tuliskan feedback atau arahan revisi di sini..."></textarea>
                        </div>
                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" onclick="closeReviewModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold shadow-md transition transform hover:scale-105 active:scale-95">Simpan Review</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal PDF Preview -->
            <div id="pdfPreviewModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" onclick="closePdfModal()"></div>
                <div class="relative bg-white rounded-3xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
                    <div class="p-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                        <h3 class="text-sm font-bold flex items-center gap-2"><i class="bi bi-file-earmark-pdf-fill text-orange-500"></i> Preview Dokumen</h3>
                        <button onclick="closePdfModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition cursor-pointer"><i class="bi bi-x-lg text-sm"></i></button>
                    </div>
                    <div class="flex-1 bg-slate-100 p-2 relative">
                        <div id="pdfLoadingIndicator" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 z-0">
                            <i class="bi bi-arrow-repeat animate-spin text-3xl mb-2"></i>
                            <span class="text-sm font-semibold text-slate-600">Memuat Dokumen...</span>
                        </div>
                        <iframe id="pdfIframe" src="" class="w-full h-full rounded-xl border border-slate-300 relative z-10 bg-white" frameborder="0"></iframe>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        let currentTahap = 'Preview 1';
        let bimbinganData = [];
        let currentDosenFilter = 'all';

        // Initialize TinyMCE for single review modal
        function initModalTinyMCE() {
            tinymce.init({
                selector: '#modalCatatan',
                menubar: false,
                statusbar: false,
                plugins: 'lists link',
                toolbar: 'bold italic underline | bullist numlist | link',
                height: 200,
                skin: 'oxide',
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchBimbinganData();
        });

        function switchDosenTab(tab) {
            let btnClassInactive = 'px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200'.split(' ');
            let btnClassActive = 'px-5 py-2.5 rounded-xl font-bold text-sm bg-orange-100 text-orange-700 border border-orange-300'.split(' ');

            ['dosenTab1', 'dosenTab2', 'dosenTab3'].forEach(id => {
                let el = document.getElementById(id);
                el.classList.remove(...btnClassActive);
                el.classList.add(...btnClassInactive);
            });

            if(tab === 'preview1') {
                currentTahap = 'Preview 1';
                document.getElementById('dosenTab1').classList.remove(...btnClassInactive);
                document.getElementById('dosenTab1').classList.add(...btnClassActive);
            } else if(tab === 'preview2') {
                currentTahap = 'Preview 2';
                document.getElementById('dosenTab2').classList.remove(...btnClassInactive);
                document.getElementById('dosenTab2').classList.add(...btnClassActive);
            } else if(tab === 'preview3') {
                currentTahap = 'Preview 3';
                document.getElementById('dosenTab3').classList.remove(...btnClassInactive);
                document.getElementById('dosenTab3').classList.add(...btnClassActive);
            }
            
            fetchBimbinganData();
        }

        function fetchBimbinganData(silent = false) {
            const tbody = document.getElementById('bimbinganTableBody');
            const posisi = <?= $posisi ?>;
            if(!silent) tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin text-xl"></i> Memuat data...</td></tr>';
            
            fetch(`<?= site_url('mahasiswa/ajax_get_dosen_bimbingan') ?>?posisi=${posisi}&tahap=${encodeURIComponent(currentTahap)}`)
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.text();
                })
                .then(text => {
                    let res;
                    try { res = JSON.parse(text); } catch(e) {
                        console.error('Server response (not JSON):', text);
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-rose-500 text-xs">Server mengembalikan response tidak valid. Cek Console (F12).</td></tr>`;
                        return;
                    }
                    if(res.status) {
                        bimbinganData = res.data;
                        // Sort by newest upload (created_at) descending
                        bimbinganData.sort((a, b) => {
                            let dateA = a.latest_preview ? new Date(a.latest_preview.created_at).getTime() : 0;
                            let dateB = b.latest_preview ? new Date(b.latest_preview.created_at).getTime() : 0;
                            return dateB - dateA;
                        });
                        updateFilterCounts();
                        renderTable();
                    } else {
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-rose-500">${res.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-rose-500">Terjadi kesalahan koneksi: ${err.message}</td></tr>`;
                });
        }

        function setDosenFilter(filter) {
            currentDosenFilter = filter;
            ['all', 'approved', 'pending', 'revision', 'empty'].forEach(f => {
                let card = document.getElementById('fCard_' + f);
                card.classList.remove('border-orange-300', 'bg-orange-50', 'shadow-xs');
                if (f === filter) {
                    card.classList.add('border-orange-300', 'bg-orange-50', 'shadow-xs');
                } else {
                    card.classList.add('border-slate-200', 'bg-slate-50');
                }
            });
            renderTable();
        }

        function updateFilterCounts() {
            let counts = { all: 0, approved: 0, pending: 0, revision: 0, empty: 0 };
            bimbinganData.forEach(mhs => {
                counts.all++;
                if (!mhs.latest_preview) {
                    counts.empty++;
                } else {
                    let st = mhs.latest_preview.status_pembimbing;
                    if (st === 'Approved') counts.approved++;
                    else if (st === 'Revision') counts.revision++;
                    else counts.pending++;
                }
            });
            for (const [key, val] of Object.entries(counts)) {
                document.getElementById('fCount_' + key).textContent = val;
            }
        }

        function renderTable() {
            const tbody = document.getElementById('bimbinganTableBody');
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            
            let html = '';
            let count = 0;
            
            bimbinganData.forEach((mhs, index) => {
                const match = mhs.nim.toLowerCase().includes(keyword) || 
                              mhs.nama_mahasiswa.toLowerCase().includes(keyword) ||
                              (mhs.judul && mhs.judul.toLowerCase().includes(keyword));
                              
                if(!match) return;

                // Apply current filter
                if (currentDosenFilter !== 'all') {
                    if (currentDosenFilter === 'empty' && mhs.latest_preview) return;
                    if (currentDosenFilter !== 'empty' && !mhs.latest_preview) return;
                    if (currentDosenFilter !== 'empty' && mhs.latest_preview) {
                        let st = mhs.latest_preview.status_pembimbing;
                        if (currentDosenFilter === 'approved' && st !== 'Approved') return;
                        if (currentDosenFilter === 'revision' && st !== 'Revision') return;
                        if (currentDosenFilter === 'pending' && (st === 'Approved' || st === 'Revision')) return;
                    }
                }

                count++;
                
                let previewHtml = `<span class="text-slate-400 italic text-xs">Belum ada berkas</span>`;
                let timeHtml = `-`;
                let statusBadge = `<span class="badge badge-secondary"><i class="bi bi-dash"></i> Kosong</span>`;
                let btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold cursor-not-allowed border border-slate-200">Belum ada file</button>`;
                
                if (mhs.latest_preview) {
                    const latest = mhs.latest_preview;
                    const fileUrl = `<?= base_url('uploads/preview_ta/') ?>${latest.file_draft}`;
                    previewHtml = `<button onclick="openPdfModal('${fileUrl}')" class="text-orange-600 hover:underline font-bold text-xs cursor-pointer text-left"><i class="bi bi-file-earmark-pdf-fill"></i> ${latest.file_draft}</button>`;
                    
                    const dt = new Date(latest.created_at);
                    timeHtml = `<div class="text-xs font-semibold text-slate-700">${dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}</div><div class="text-[10px] text-slate-500">${dt.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})} WIB</div>`;
                    
                    let st = latest.status_pembimbing;
                    if (st === 'Approved') statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Disetujui</span>`;
                    else if (st === 'Revision') statusBadge = `<span class="badge badge-danger"><i class="bi bi-x-circle-fill"></i> Revisi</span>`;
                    else statusBadge = `<span class="badge badge-warning"><i class="bi bi-clock-fill"></i> Pending</span>`;
                    
                    btnHtml = `<button onclick="openReviewModal(${index})" class="px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded-lg text-xs font-bold transition shadow-2xs"><i class="bi bi-pencil-square"></i> Review</button>`;
                }
                
                let checkboxHtml = '';
                if (mhs.latest_preview && mhs.latest_preview.status_pembimbing !== 'Approved') {
                    checkboxHtml = `<input type="checkbox" value="${mhs.latest_preview.id}" data-name="${mhs.nama_mahasiswa}" data-file="${mhs.latest_preview.file_draft}" data-id="${mhs.latest_preview.id}" data-status="${mhs.latest_preview.status_pembimbing || 'Pending'}" data-catatan="${encodeURIComponent(mhs.latest_preview.catatan_pembimbing || '')}" data-catatan2="${encodeURIComponent(mhs.latest_preview.catatan_pembimbing_2 || '')}" class="dosen-student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">`;
                } else if (mhs.latest_preview && mhs.latest_preview.status_pembimbing === 'Approved') {
                    checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-50" title="Sudah Disetujui">`;
                } else {
                    checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-50" title="Belum ada berkas">`;
                }

                // Komentar P2 button
                let p2Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_pembimbing_2 || '') : '';
                let hasP2Comment = p2Comment.trim().length > 0;
                let encodedP2 = hasP2Comment ? encodeURIComponent(p2Comment) : '';
                let p2BtnHtml = hasP2Comment
                    ? `<button onclick="showP2Comment(decodeURIComponent('${encodedP2}'), '${mhs.nama_mahasiswa}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition cursor-pointer"><i class="bi bi-chat-quote-fill"></i> Lihat</button>`
                    : `<span class="text-slate-400 text-xs italic">-</span>`;

                // Hover data
                const fileUrlEncoded = mhs.latest_preview ? encodeURIComponent(`<?= base_url('uploads/preview_ta/') ?>${mhs.latest_preview.file_draft}`) : '';
                const nameEncoded = encodeURIComponent(mhs.nama_mahasiswa);
                const idPreview = mhs.latest_preview ? mhs.latest_preview.id : '';
                const statusVal = mhs.latest_preview ? (mhs.latest_preview.status_pembimbing || 'Pending') : 'Pending';

                html += `
                    <tr class="hover:bg-slate-50 transition-colors" data-index="${index}">
                        <td class="py-4 px-4 text-center">${checkboxHtml}</td>
                        <td class="py-4 px-4">
                            <div class="relative inline-block group">
                                <span
                                    class="font-bold text-slate-900 cursor-pointer hover:text-orange-600 transition-colors underline decoration-dotted decoration-orange-300 underline-offset-2"
                                    onmouseenter="showHoverPanel(event, ${index})"
                                    onmouseleave="scheduleHidePanel()"
                                >${mhs.nama_mahasiswa}</span>
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">${mhs.nim}</div>
                            <div class="text-[11px] text-slate-600 mt-1 line-clamp-1 italic">${mhs.judul || '-'}</div>
                        </td>
                        <td class="py-4 px-4">${previewHtml}</td>
                        <td class="py-4 px-4 text-center">${timeHtml}</td>
                        <td class="py-4 px-4 text-center">${statusBadge}</td>
                        <td class="py-4 px-4 text-center">${p2BtnHtml}</td>
                        <td class="py-4 px-4 pr-6 text-right">${btnHtml}</td>
                    </tr>
                `;
            });
            
            if(count === 0) {
                html = `<tr><td colspan="7" class="text-center py-10 text-slate-500 font-medium">Tidak ada data mahasiswa ditemukan.</td></tr>`;
            }
            
            tbody.innerHTML = html;
            rebindDosenCheckboxes();
        }

        function openReviewModal(index) {
            const mhs = bimbinganData[index];
            const latest = mhs.latest_preview;
            if(!latest) return;
            
            document.getElementById('modalTahapTitle').textContent = `Review Berkas ${currentTahap}`;
            document.getElementById('modalStudentName').textContent = `${mhs.nama_mahasiswa} (${mhs.nim})`;
            document.getElementById('modalJudul').textContent = mhs.judul || '-';
            document.getElementById('modalStudentNotes').textContent = latest.catatan_mahasiswa || '-';
            
            document.getElementById('modalFileLink').href = `<?= base_url('uploads/preview_ta/') ?>${latest.file_draft}`;
            document.getElementById('modalIdPreview').value = latest.id;
            
            // Populate form values
            if (document.getElementById('modalStatus')) {
                document.getElementById('modalStatus').value = latest.status_pembimbing || 'Pending';
            }
            
            let catatanDosen = <?= $posisi ?> === 1 ? (latest.catatan_pembimbing || '') : (latest.catatan_pembimbing_2 || '');
            
            // Re-init TinyMCE if already exists, else setContent
            if (tinymce.get('modalCatatan')) {
                tinymce.get('modalCatatan').setContent(catatanDosen);
            } else {
                document.getElementById('modalCatatan').value = catatanDosen;
                initModalTinyMCE();
            }
            
            if (<?= $posisi ?> === 1 && document.getElementById('modalRiwayatContainer')) {
                if (latest.catatan_pembimbing) {
                    document.getElementById('modalRiwayatContainer').classList.remove('hidden');
                    document.getElementById('modalCatatanSebelumnya').textContent = latest.catatan_pembimbing;
                } else {
                    document.getElementById('modalRiwayatContainer').classList.add('hidden');
                }
            }
            
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[9999] p-4 rounded-xl text-white font-bold shadow-lg transition-opacity ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;
            toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} mr-2"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(()=>toast.remove(), 300); }, 3000);
        }

        const formReview = document.getElementById('formReview');
        if (formReview) {
            formReview.addEventListener('submit', function(e) {
                e.preventDefault();
                tinymce.triggerSave();
                
                const btn = this.querySelector('button[type="submit"]');
                const originalBtnText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...';
                
                const formData = new FormData(this);
                
                fetch(this.action + '_ajax', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        showToast(data.message, 'success');
                        closeReviewModal();
                        // Dosen list will automatically update via SSE
                    } else {
                        showToast(data.message || 'Gagal menyimpan', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Kesalahan koneksi', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                });
            });
        }

        let dosenEventSource = null;
        function startDosenSSE() {
            if (dosenEventSource) dosenEventSource.close();
            const posisi = <?= $posisi ?>;
            dosenEventSource = new EventSource(`<?= site_url('mahasiswa/sse_dosen_bimbingan') ?>?posisi=${posisi}`);
            
            dosenEventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if(data && data.length !== undefined) {
                        // SSE sends data only when there is a change.
                        // We use the existing fetchBimbinganData() to refresh the view to ensure 
                        // format compatibility with the current rendering logic.
                        fetchBimbinganData(true);
                    }
                } catch(e) { console.error('SSE Error:', e); }
            };
        }

        function rebindDosenCheckboxes() {
            const checkAll = document.getElementById('checkAllDosenStudents');
            const studentCbs = document.querySelectorAll('.dosen-student-cb');

            if (checkAll) {
                checkAll.checked = false;
                checkAll.onchange = function() {
                    studentCbs.forEach(cb => {
                        if (!cb.disabled) cb.checked = this.checked;
                    });
                    updateDosenBatchBar();
                };
            }

            studentCbs.forEach(cb => {
                cb.onchange = () => {
                    updateDosenBatchBar();
                    if (checkAll) {
                        const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                        const checkedCount = enabledCbs.filter(c => c.checked).length;
                        checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
                    }
                };
            });

            updateDosenBatchBar();
        }

        function updateDosenBatchBar() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            const batchBar = document.getElementById('dosenBatchActionBar');
            const countBadge = document.getElementById('dosenSelectedCountBadge');

            if (checkedCbs.length > 0) {
                countBadge.textContent = checkedCbs.length;
                batchBar.classList.remove('hidden');
                batchBar.classList.add('flex');
            } else {
                batchBar.classList.add('hidden');
                batchBar.classList.remove('flex');
            }
        }

        function unselectAllDosenStudents() {
            document.querySelectorAll('.dosen-student-cb').forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('checkAllDosenStudents');
            if (checkAll) checkAll.checked = false;
            updateDosenBatchBar();
        }

        function openDosenBatchModal() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            if (checkedCbs.length === 0) return;

            const posisi = <?= $posisi ?>;
            let html = '';

            checkedCbs.forEach((cb, i) => {
                const name = cb.getAttribute('data-name');
                const file = cb.getAttribute('data-file');
                const idPreview = cb.getAttribute('data-id');
                const statusCurrent = cb.getAttribute('data-status') || 'Pending';
                const catatanCurrent = decodeURIComponent(cb.getAttribute('data-catatan') || '');
                const catatan2Current = decodeURIComponent(cb.getAttribute('data-catatan2') || '');
                const fileUrl = `<?= base_url('uploads/preview_ta/') ?>${file}`;

                const statusOptions = posisi === 1 ? `
                    <div class="mb-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Penilaian (P1)</label>
                        <select id="batchStatus_${i}" class="w-full p-2.5 rounded-xl border border-slate-200 focus:ring-orange-500 focus:border-orange-500 text-xs font-semibold bg-white">
                            <option value="Pending" ${statusCurrent === 'Pending' ? 'selected' : ''}>Menunggu Review</option>
                            <option value="Approved" ${statusCurrent === 'Approved' ? 'selected' : ''}>Disetujui (ACC)</option>
                            <option value="Revision" ${statusCurrent === 'Revision' ? 'selected' : ''}>Perlu Revisi</option>
                        </select>
                    </div>
                ` : '';

                const catatanLabel = posisi === 1 ? 'Komentar / Feedback' : 'Komentar untuk P1';
                const catatanVal = posisi === 1 ? catatanCurrent : catatan2Current;
                const btnColor = posisi === 1
                    ? 'bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600'
                    : 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700';

                html += `
                    <div class="mb-6 rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-white">
                        <!-- Card Header -->
                        <div class="flex items-center gap-3 px-4 py-3 bg-slate-900 text-white">
                            <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shrink-0">${i+1}</span>
                            <div class="min-w-0">
                                <div class="font-bold text-sm truncate">${name}</div>
                                <div class="text-[10px] text-slate-400 font-mono truncate"><i class="bi bi-file-earmark-pdf-fill text-orange-400 mr-1"></i>${file}</div>
                            </div>
                        </div>

                        <!-- PDF Preview Inline -->
                        <div class="relative bg-slate-100" style="height:220px;">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-xs z-0" id="batchPdfLoader_${i}">
                                <i class="bi bi-arrow-repeat animate-spin text-2xl mb-1"></i> Memuat dokumen...
                            </div>
                            <iframe
                                src="${fileUrl}"
                                class="w-full h-full border-0 relative z-10"
                                onload="document.getElementById('batchPdfLoader_${i}').style.display='none'"
                            ></iframe>
                        </div>

                        <!-- Form Area -->
                        <div class="p-4 space-y-2 border-t border-slate-100">
                            ${statusOptions}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">${catatanLabel}</label>
                                <textarea id="batchCatatan_${i}" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-medium resize-none focus:outline-none focus:ring-2 focus:ring-orange-400/25 focus:border-orange-400" placeholder="Tuliskan komentar...">${catatanVal}</textarea>
                            </div>
                            <button
                                onclick="submitBatchItemReview('${idPreview}', ${posisi}, ${i})"
                                class="w-full py-2 px-4 rounded-xl ${btnColor} text-white font-bold text-xs shadow transition cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <i class="bi bi-send-fill"></i> Simpan Review
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('dosenBatchModalBody').innerHTML = html;
            document.getElementById('dosenBatchReviewModal').classList.remove('hidden');
        }

        function submitBatchItemReview(idPreview, posisi, idx) {
            const catatan = document.getElementById('batchCatatan_' + idx)?.value || '';
            const statusEl = document.getElementById('batchStatus_' + idx);
            const status = statusEl ? statusEl.value : null;

            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', posisi);
            fd.append('catatan_pembimbing', catatan);
            if (status) fd.append('status_pembimbing', status);

            const btn = document.querySelector(`button[onclick="submitBatchItemReview('${idPreview}', ${posisi}, ${idx})"]`);
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...'; }

            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    showToast(data.message, 'success');
                    if (btn) {
                        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';
                        btn.classList.remove('from-orange-500','to-amber-500','from-indigo-500','to-purple-600','hover:from-orange-600','hover:to-amber-600','hover:from-indigo-600','hover:to-purple-700');
                        btn.classList.add('bg-emerald-500');
                    }
                    fetchBimbinganData(true);
                } else {
                    showToast(data.message || 'Gagal menyimpan', 'error');
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan Review'; }
                }
            })
            .catch(() => {
                showToast('Kesalahan koneksi', 'error');
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan Review'; }
            });
        }

        function closeDosenBatchModal() {
            document.getElementById('dosenBatchReviewModal').classList.add('hidden');
        }

        function openPdfModal(url) {
            const iframe = document.getElementById('pdfIframe');
            iframe.src = '';
            document.getElementById('pdfLoadingIndicator').style.display = 'flex';
            iframe.onload = function() {
                document.getElementById('pdfLoadingIndicator').style.display = 'none';
            };
            iframe.src = url;
            document.getElementById('pdfPreviewModal').classList.remove('hidden');
        }

        function closePdfModal() {
            document.getElementById('pdfIframe').src = '';
            document.getElementById('pdfPreviewModal').classList.add('hidden');
        }

        function submitDosenBatchApprove() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            if (checkedCbs.length === 0) return;

            Swal.fire({
                title: 'Konfirmasi Approve Massal',
                text: `Apakah anda yakin ingin approve masal ${checkedCbs.length} mahasiswa terpilih?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const ids = Array.from(checkedCbs).map(cb => cb.value);
                    const formData = new FormData();
                    ids.forEach(id => formData.append('ids[]', id));
                    formData.append('posisi', <?= $posisi ?>);

                    fetch('<?= site_url("mahasiswa/review_preview_batch_ajax") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status) {
                            Swal.fire('Berhasil!', data.message, 'success');
                            unselectAllDosenStudents();
                            fetchBimbinganData(true);
                        } else {
                            Swal.fire('Gagal!', data.message || 'Gagal approve massal', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error!', 'Kesalahan koneksi saat approve massal', 'error');
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            startDosenSSE();
        });

        /* ===== HOVER PREVIEW PANEL ===== */
        let hoverHideTimer = null;
        let hoverShowTimer = null;
        const panel = document.getElementById('hoverPreviewPanel');

        function showHoverPanel(event, index) {
            clearTimeout(hoverHideTimer);
            clearTimeout(hoverShowTimer);

            hoverShowTimer = setTimeout(() => {
                const mhs = bimbinganData[index];
                if (!mhs) return;

                const posisi = <?= $posisi ?>;
                const latest = mhs.latest_preview;

                // Header
                document.getElementById('hoverPanelName').textContent = mhs.nama_mahasiswa + ' (' + mhs.nim + ')';

                // PDF Preview
                const pdfWrap = document.getElementById('hoverPdfWrap');
                if (latest && latest.file_draft) {
                    const fileUrl = `<?= base_url('uploads/preview_ta/') ?>${latest.file_draft}`;
                    pdfWrap.innerHTML = `<iframe src="${fileUrl}" class="w-full h-full" frameborder="0"></iframe>`;
                } else {
                    pdfWrap.innerHTML = `<div class="pdf-no-file"><i class="bi bi-file-earmark-x text-3xl"></i><span>Belum ada berkas diunggah</span></div>`;
                }

                // Comment field & action
                const formWrap = document.getElementById('hoverFormWrap');
                if (!latest) {
                    formWrap.innerHTML = `<p class="text-xs text-slate-500 italic">Tidak ada berkas untuk dikomentari.</p>`;
                } else if (posisi === 1) {
                    // P1: TinyMCE comment + Approve/Revisi
                    formWrap.innerHTML = `
                        <div class="panel-label">Status Penilaian (P1)</div>
                        <select id="hoverStatusSelect" class="w-full mb-3 p-2.5 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-semibold">
                            <option value="Pending" ${(latest.status_pembimbing||'Pending')==='Pending'?'selected':''}>Menunggu Review</option>
                            <option value="Approved" ${latest.status_pembimbing==='Approved'?'selected':''}>Disetujui (ACC)</option>
                            <option value="Revision" ${latest.status_pembimbing==='Revision'?'selected':''}>Perlu Revisi</option>
                        </select>
                        <div class="panel-label">Komentar / Feedback</div>
                        <textarea id="hoverCatatanTA" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-sm font-medium resize-none focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-500" placeholder="Tuliskan feedback...">${latest.catatan_pembimbing || ''}</textarea>
                        <button onclick="submitHoverReview(${latest.id}, 1)" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs shadow-md transition cursor-pointer"><i class="bi bi-send-fill mr-1"></i> Simpan Review</button>
                    `;
                } else {
                    // P2: comment only
                    formWrap.innerHTML = `
                        <div class="panel-label">Komentar untuk P1</div>
                        <textarea id="hoverCatatanTA" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-sm font-medium resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-500" placeholder="Tuliskan komentar untuk Pembimbing 1...">${latest.catatan_pembimbing_2 || ''}</textarea>
                        <button onclick="submitHoverReview(${latest.id}, 2)" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold text-xs shadow-md transition cursor-pointer"><i class="bi bi-send-fill mr-1"></i> Simpan Komentar</button>
                    `;
                }

                // Position panel
                const rect = event.target.getBoundingClientRect();
                let left = rect.left + window.scrollX;
                let top = rect.bottom + window.scrollY + 8;

                // Ensure not off-screen right
                const panelW = 520;
                if (left + panelW > window.innerWidth - 16) {
                    left = window.innerWidth - panelW - 16;
                }
                if (left < 8) left = 8;

                // Ensure not off-screen bottom
                const panelEstH = 480;
                if (top + panelEstH > window.innerHeight + window.scrollY - 16) {
                    top = rect.top + window.scrollY - panelEstH - 8;
                }

                panel.style.left = left + 'px';
                panel.style.top = top + 'px';
                panel.classList.add('visible');
            }, 350);
        }

        function scheduleHidePanel() {
            clearTimeout(hoverShowTimer);
            hoverHideTimer = setTimeout(() => {
                panel.classList.remove('visible');
            }, 300);
        }

        panel.addEventListener('mouseenter', () => clearTimeout(hoverHideTimer));
        panel.addEventListener('mouseleave', scheduleHidePanel);

        function submitHoverReview(idPreview, posisi) {
            const catatan = document.getElementById('hoverCatatanTA')?.value || '';
            const status = posisi === 1 ? (document.getElementById('hoverStatusSelect')?.value || 'Pending') : null;

            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', posisi);
            fd.append('catatan_pembimbing', catatan);
            if (status) fd.append('status_pembimbing', status);

            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    showToast(data.message, 'success');
                    panel.classList.remove('visible');
                    fetchBimbinganData(true);
                } else {
                    showToast(data.message || 'Gagal menyimpan', 'error');
                }
            })
            .catch(() => showToast('Kesalahan koneksi', 'error'));
        }

        /* ===== P2 COMMENT MODAL ===== */
        function showP2Comment(comment, name) {
            document.getElementById('p2ModalName').textContent = name;
            document.getElementById('p2ModalContent').innerHTML = comment || '<em class="text-slate-400">Tidak ada komentar.</em>';
            document.getElementById('p2CommentModal').classList.add('open');
        }

        function closeP2Comment() {
            document.getElementById('p2CommentModal').classList.remove('open');
        }
    </script>

    <!-- Hover Preview Panel -->
    <div id="hoverPreviewPanel">
        <div class="panel-header">
            <i class="bi bi-person-circle"></i>
            <span id="hoverPanelName">Nama Mahasiswa</span>
        </div>
        <div class="pdf-frame-wrap" id="hoverPdfWrap">
            <div class="pdf-no-file"><i class="bi bi-file-earmark-x text-3xl"></i><span>Belum ada berkas</span></div>
        </div>
        <div class="panel-body">
            <div id="hoverFormWrap">
                <!-- Injected by JS -->
            </div>
        </div>
    </div>

    <!-- P2 Comment Modal -->
    <div id="p2CommentModal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeP2Comment()"></div>
        <div class="relative bg-white rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl">
            <button onclick="closeP2Comment()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-2xl leading-none">&times;</button>
            <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                <i class="bi bi-chat-quote-fill text-indigo-500"></i> Komentar Pembimbing 2
            </h3>
            <p class="text-xs text-slate-500 font-semibold mb-4" id="p2ModalName">Nama Mahasiswa</p>
            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl text-sm text-slate-800 font-medium leading-relaxed" id="p2ModalContent">
                <!-- Comment content -->
            </div>
        </div>
    </div>

    <!-- Floating Batch Action Bar -->
    <div id="dosenBatchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="dosenSelectedCountBadge">0</span>
            <span class="text-xs font-bold tracking-tight">Mahasiswa Terpilih</span>
        </div>
        
        <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>

        <div class="flex items-center gap-2.5">
            <!-- Button 1: Popup Batch Review -->
            <button type="button" onclick="openDosenBatchModal()" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-2 transition-all active:scale-95 cursor-pointer">
                <i class="bi bi-files"></i> Preview Massal
            </button>

            <!-- Button 2: Direct Batch Approve -->
            <button type="button" onclick="submitDosenBatchApprove()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                <i class="bi bi-check2-all"></i> Approve Massal
            </button>

            <!-- Button 3: Uncheck All -->
            <button type="button" onclick="unselectAllDosenStudents()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                <i class="bi bi-x-lg"></i> Batal
            </button>
        </div>
    </div>

    <!-- Multi-Student Batch Review Modal Popup -->
    <div id="dosenBatchReviewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeDosenBatchModal()"></div>
        <div class="relative bg-white rounded-3xl w-full max-w-6xl flex flex-col overflow-hidden shadow-2xl" style="max-height:90vh">
            <div class="p-4 px-6 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <h3 class="text-sm font-extrabold flex items-center gap-2">
                    <i class="bi bi-files text-orange-500"></i> Review Preview Massal
                    <span class="text-[10px] font-normal text-slate-400 ml-1">— Setiap kartu berisi preview langsung, komentar, dan tombol simpan</span>
                </h3>
                <button type="button" onclick="closeDosenBatchModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
            
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="dosenBatchModalBody">
                <!-- Data cards injected via JS -->
            </div>
            
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500 font-medium">Simpan setiap kartu secara individual, atau gunakan Approve Massal dari toolbar bawah.</span>
                <button type="button" onclick="closeDosenBatchModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</body>
</html>
