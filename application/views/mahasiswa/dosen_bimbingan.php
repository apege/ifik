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
                                    <th class="py-4 px-4 font-bold">Mahasiswa & Judul TA</th>
                                    <th class="py-4 px-4">Berkas Terbaru</th>
                                    <th class="py-4 px-4 text-center">Waktu Upload</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                    <th class="py-4 px-4 pr-6 text-right">Aksi (Review)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="bimbinganTableBody">
                                <tr><td colspan="5" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memuat data...</td></tr>
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
                        <a href="#" id="modalFileLink" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold hover:bg-orange-200 transition"><i class="bi bi-file-earmark-pdf-fill"></i> Buka File Draft Terbaru</a>
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

        </div>
    </main>

    <script>
        let currentTahap = 'Preview 1';
        let bimbinganData = [];

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

        function fetchBimbinganData() {
            const tbody = document.getElementById('bimbinganTableBody');
            const posisi = <?= $posisi ?>;
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin text-xl"></i> Memuat data...</td></tr>';
            
            fetch(`<?= site_url('mahasiswa/ajax_get_dosen_bimbingan') ?>?posisi=${posisi}&tahap=${encodeURIComponent(currentTahap)}`)
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.text();
                })
                .then(text => {
                    let res;
                    try { res = JSON.parse(text); } catch(e) {
                        console.error('Server response (not JSON):', text);
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-rose-500 text-xs">Server mengembalikan response tidak valid. Cek Console (F12).</td></tr>`;
                        return;
                    }
                    if(res.status) {
                        bimbinganData = res.data;
                        renderTable();
                    } else {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-rose-500">${res.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-rose-500">Terjadi kesalahan koneksi: ${err.message}</td></tr>`;
                });
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
                count++;
                
                let previewHtml = `<span class="text-slate-400 italic text-xs">Belum ada berkas</span>`;
                let timeHtml = `-`;
                let statusBadge = `<span class="badge badge-secondary"><i class="bi bi-dash"></i> Kosong</span>`;
                let btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold cursor-not-allowed border border-slate-200">Belum ada file</button>`;
                
                if (mhs.latest_preview) {
                    const latest = mhs.latest_preview;
                    const fileUrl = `<?= base_url('uploads/preview_ta/') ?>${latest.file_draft}`;
                    previewHtml = `<a href="${fileUrl}" target="_blank" class="text-orange-600 hover:underline font-bold text-xs"><i class="bi bi-file-earmark-pdf-fill"></i> ${latest.file_draft}</a>`;
                    
                    const dt = new Date(latest.created_at);
                    timeHtml = `<div class="text-xs font-semibold text-slate-700">${dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}</div><div class="text-[10px] text-slate-500">${dt.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})} WIB</div>`;
                    
                    let st = latest.status_pembimbing;
                    if (st === 'Approved') statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Disetujui</span>`;
                    else if (st === 'Revision') statusBadge = `<span class="badge badge-danger"><i class="bi bi-x-circle-fill"></i> Revisi</span>`;
                    else statusBadge = `<span class="badge badge-warning"><i class="bi bi-clock-fill"></i> Pending</span>`;
                    
                    btnHtml = `<button onclick="openReviewModal(${index})" class="px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 rounded-lg text-xs font-bold transition shadow-2xs"><i class="bi bi-pencil-square"></i> Review</button>`;
                }
                
                html += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-900">${mhs.nama_mahasiswa}</div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">${mhs.nim}</div>
                            <div class="text-[11px] text-slate-600 mt-1 line-clamp-1 italic">${mhs.judul || '-'}</div>
                        </td>
                        <td class="py-4 px-4">${previewHtml}</td>
                        <td class="py-4 px-4 text-center">${timeHtml}</td>
                        <td class="py-4 px-4 text-center">${statusBadge}</td>
                        <td class="py-4 px-4 pr-6 text-right">${btnHtml}</td>
                    </tr>
                `;
            });
            
            if(count === 0) {
                html = `<tr><td colspan="5" class="text-center py-10 text-slate-500 font-medium">Tidak ada data mahasiswa ditemukan.</td></tr>`;
            }
            
            tbody.innerHTML = html;
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
            document.getElementById('modalCatatan').value = catatanDosen;
            
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
                        fetchBimbinganData();
                    }
                } catch(e) { console.error('SSE Error:', e); }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            startDosenSSE();
        });
    </script>
</body>
</html>
