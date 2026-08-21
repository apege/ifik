<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white relative">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d">
                        W
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block">Dosen Wali Akademik</span>
                    </div>
                </div>

                <!-- User Profile Pill (Kode, Nama, Kejuruan Dosen Wali) -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800 leading-tight"><?= $dosen_info['nama_dosen'] ?? 'Alif Dosen, S.T., M.T.'; ?></span>
                        <div class="flex items-center justify-end gap-2 text-[10px] font-semibold text-slate-500 mt-0.5">
                            <span class="px-2 py-0.5 bg-orange-100/90 text-orange-700 rounded-md border border-orange-200/80 font-bold"><?= $dosen_info['kode_dosen'] ?? 'DW-001'; ?></span>
                            <span>Prodi: <strong class="text-slate-700"><?= $dosen_info['kejuruan'] ?? 'Informatika / DKV'; ?></strong></span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-base box-3d shadow-xs">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container (Full Wide Layout) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-10 flex-grow">

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">OVERVIEW BIMBINGAN</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Wali</h1>
                <p class="text-slate-600 text-xs mt-1 font-normal">Kelola persetujuan pendaftaran Tugas Akhir mahasiswa bimbingan Anda secara praktis.</p>
            </div>
            <div class="px-4 py-2 bg-white/90 rounded-xl border border-orange-200 shadow-xs text-xs font-semibold text-slate-700 flex items-center gap-2 w-fit">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Approval System Active</span>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm box-3d">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Stat Summary Cards Grid (3D Claymorphic) -->
        <?php
            $totalMhs = !empty($list_mahasiswa) ? count($list_mahasiswa) : 0;
            $pendingCount = 0;
            $approvedCount = 0;
            $rejectedCount = 0;

            if(!empty($list_mahasiswa)) {
                foreach($list_mahasiswa as $row) {
                    $st = $row['status_approval_wali'] ?? 'Pending';
                    if($st === 'Approved') $approvedCount++;
                    else if($st === 'Rejected') $rejectedCount++;
                    else $pendingCount++;
                }
            }
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Bimbingan -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Mahasiswa</span>
                    <span class="text-2xl font-bold text-slate-900 tracking-tight block"><?= $totalMhs; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Bimbingan Akademik</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            <!-- Menunggu Approval -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block mb-1">Menunggu Approval</span>
                    <span class="text-2xl font-bold text-amber-600 tracking-tight block"><?= $pendingCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Perlu Ditolak / Disetujui</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>

            <!-- Disetujui (Approved) -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Disetujui</span>
                    <span class="text-2xl font-bold text-emerald-600 tracking-tight block"><?= $approvedCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Lanjut ke Admin</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>

            <!-- Ditolak / Revisi (Rejected) -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block mb-1">Perlu Revisi</span>
                    <span class="text-2xl font-bold text-rose-600 tracking-tight block"><?= $rejectedCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Telah Ditolak</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-500 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>

        <!-- Table Container Card (3D Warm) -->
        <div class="card-3d-warm card-no-hover rounded-2xl border border-orange-200/60 shadow-card-clean overflow-hidden">

            <!-- Table Header -->
            <div class="p-5 border-b border-orange-200/60 flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                        <i class="bi bi-people-fill text-orange-500 text-lg"></i> Daftar Mahasiswa Bimbingan
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Pilih mahasiswa untuk meninjau berkas dan melakukan persetujuan.</p>
                </div>
                <!-- Controls -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <div class="flex items-center gap-2 bg-white border border-orange-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-600">
                        <i class="bi bi-list-ul text-slate-400"></i>
                        <span>Tampilkan</span>
                        <select id="recordsPerPage" class="bg-transparent font-bold text-slate-800 outline-none cursor-pointer">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span>records / hal</span>
                    </div>
                    <button id="btnAddFilter" class="btn-3d-orange flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white">
                        <i class="bi bi-plus-lg"></i> Tambah Filter
                        <span id="filterCountBadge" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                    </button>
                    <button id="btnReset" class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-orange-300 bg-white text-xs font-semibold text-slate-600 hover:bg-orange-50 transition">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Filter Panel -->
            <div id="filterPanel" class="border-b border-orange-200/60 bg-orange-50/40 px-5 py-4">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                        <i class="bi bi-funnel-fill"></i> Dynamic Multi-Filter (Maksimal 4 Kriteria)
                    </span>
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold">
                        <span class="text-slate-500 mr-1">Pintas Status:</span>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-slate-800 text-white transition" data-status="all">Semua</button>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition" data-status="Pending">Pending</button>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition" data-status="Approved">Approved</button>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition" data-status="Rejected">Rejected</button>
                    </div>
                </div>
                <div id="filterRows" class="space-y-2"></div>
            </div>
            
            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-orange-100/40 text-slate-600 font-semibold uppercase tracking-wider border-b border-orange-200/60">
                            <th class="py-3.5 px-5 pl-6">NIM</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa</th>
                            <th class="py-3.5 px-5">Usulan Judul TA (Utama)</th>
                            <th class="py-3.5 px-5">Status Approval</th>
                            <th class="py-3.5 px-5">Tahap Saat Ini</th>
                            <th class="py-3.5 px-5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100/80 font-medium" id="tableBodyMhs">
                        <?php if(!empty($list_mahasiswa)): ?>
                            <?php foreach($list_mahasiswa as $mhs): ?>
                                <?php 
                                    $st = $mhs['status_approval_wali'] ?? 'Pending';
                                    $badgeStyle = ($st === 'Approved') ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : (($st === 'Rejected') ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-amber-100 text-amber-700 border-amber-300');
                                ?>
                                <tr class="hover:bg-orange-50/50 transition-all duration-150 mhs-row" data-status="<?= $st; ?>" data-nim="<?= strtolower($mhs['nim']); ?>" data-nama="<?= strtolower($mhs['nama_depan'] . ' ' . $mhs['nama_belakang']); ?>" data-judul="<?= strtolower($mhs['judul_1'] ?? ''); ?>" data-stage="<?= strtolower($mhs['current_stage'] ?? 'draft'); ?>">
                                    <td class="py-4 px-5 pl-6 font-bold text-slate-900 mhs-nim"><?= $mhs['nim']; ?></td>
                                    <td class="py-4 px-5 font-semibold text-slate-800 mhs-nama"><?= $mhs['nama_depan'] . ' ' . $mhs['nama_belakang']; ?></td>
                                    <td class="py-4 px-5 text-slate-600 max-w-xs truncate"><?= !empty($mhs['judul_1']) ? character_limiter($mhs['judul_1'], 45) : '<span class="text-slate-400 italic font-normal">Belum Mendaftar</span>'; ?></td>
                                    <td class="py-4 px-5">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full border shadow-xs inline-block <?= $badgeStyle; ?>"><?= $st; ?></span>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block"><?= $mhs['current_stage'] ?? 'Draft'; ?></span>
                                    </td>
                                    <td class="py-4 px-5 pr-6 text-right">
                                        <a href="<?= site_url('dosenwali/detail_mahasiswa/' . $mhs['nim']); ?>" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                            <i class="bi bi-search text-xs"></i> Detail & Approval
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Clean Empty State Row -->
                            <tr>
                                <td colspan="6" class="py-12 text-center bg-white">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-orange-100/80 text-orange-600 flex items-center justify-center text-2xl font-bold box-3d shadow-2xs">
                                            <i class="bi bi-inbox-fill"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <h4 class="text-sm font-bold text-slate-800">Belum Ada Mahasiswa Mengirim Pendaftaran TA</h4>
                                            <p class="text-xs text-slate-500 max-w-md mx-auto">Daftar ini akan otomatis terisi secara real-time begitu mahasiswa mengisi & mengirimkan Formulir Pendaftaran TA (6 Langkah).</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Footer: Info + Pagination -->
            <div class="px-5 py-3.5 border-t border-orange-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-orange-50/30">
                <span id="recordsInfo" class="text-[11px] text-slate-500 font-medium"></span>
                <div id="paginationContainer" class="flex items-center gap-1 text-xs font-semibold"></div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script>
    (() => {
        const allRows    = Array.from(document.querySelectorAll('.mhs-row'));
        const filterRows = document.getElementById('filterRows');
        const btnAdd     = document.getElementById('btnAddFilter');
        const btnReset   = document.getElementById('btnReset');
        const badge      = document.getElementById('filterCountBadge');
        const perPageSel = document.getElementById('recordsPerPage');
        const info       = document.getElementById('recordsInfo');
        const pagination = document.getElementById('paginationContainer');

        const COLUMNS = [
            { label: 'Semua Kolom (Pencarian Umum)', value: 'all' },
            { label: 'NIM',      value: 'nim'    },
            { label: 'Nama',     value: 'nama'   },
            { label: 'Judul TA', value: 'judul'  },
            { label: 'Status',   value: 'status' },
            { label: 'Tahap',    value: 'stage'  },
        ];

        let filters      = [];
        let pintasStatus = 'all';
        let currentPage  = 1;
        let perPage      = parseInt(perPageSel.value);

        function colSelect(selected = 'all') {
            return `<select class="filter-col border border-orange-200 rounded-lg px-2 py-1.5 text-xs font-medium bg-white outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition">${COLUMNS.map(c => `<option value="${c.value}" ${c.value === selected ? 'selected' : ''}>${c.label}</option>`).join('')}</select>`;
        }

        function addFilterRow(col = 'all', val = '') {
            if (filters.length >= 4) return;
            const idx = filters.length;
            filters.push({ col, val });
            const div = document.createElement('div');
            div.className = 'filter-row flex items-center gap-2';
            div.dataset.idx = idx;
            div.innerHTML = `
                <span class="text-[10px] font-bold text-slate-500 w-14 shrink-0">Filter #${idx + 1}:</span>
                ${colSelect(col)}
                <input type="text" placeholder="Ketik kata kunci pencarian..." value="${val}"
                    class="filter-val flex-1 border border-orange-200 rounded-lg px-3 py-1.5 text-xs font-medium bg-white outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition placeholder:text-slate-300">
                ${idx > 0 ? `<button class="btn-remove-filter w-7 h-7 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-200 transition flex items-center justify-center text-xs"><i class="bi bi-x-lg"></i></button>` : ''}`;
            filterRows.appendChild(div);
            div.querySelector('.filter-col').addEventListener('change', e => { filters[idx].col = e.target.value; applyAll(); });
            div.querySelector('.filter-val').addEventListener('input',  e => { filters[idx].val = e.target.value; currentPage = 1; applyAll(); });
            const rmBtn = div.querySelector('.btn-remove-filter');
            if (rmBtn) rmBtn.addEventListener('click', () => { filters.splice(idx, 1); rebuildFilterUI(); applyAll(); });
            updateBadge(); applyAll();
        }

        function rebuildFilterUI() {
            filterRows.innerHTML = '';
            const copy = [...filters]; filters = [];
            copy.forEach(f => addFilterRow(f.col, f.val));
        }

        function updateBadge() {
            badge.textContent = `${filters.length}/4`;
            btnAdd.style.opacity = filters.length >= 4 ? '0.5' : '1';
            btnAdd.style.pointerEvents = filters.length >= 4 ? 'none' : 'auto';
        }

        function rowMatches(row) {
            if (pintasStatus !== 'all' && row.dataset.status.toLowerCase() !== pintasStatus.toLowerCase()) return false;
            for (const f of filters) {
                const q = f.val.trim().toLowerCase();
                if (!q) continue;
                let h = '';
                if (f.col === 'all')    h = [row.dataset.nim, row.dataset.nama, row.dataset.judul, row.dataset.status, row.dataset.stage].join(' ');
                else h = row.dataset[f.col] || '';
                if (!h.toLowerCase().includes(q)) return false;
            }
            return true;
        }

        function applyAll() {
            const visible    = allRows.filter(rowMatches);
            const total      = visible.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * perPage;
            allRows.forEach(r => r.classList.add('hidden'));
            visible.slice(start, start + perPage).forEach(r => r.classList.remove('hidden'));
            const from = total === 0 ? 0 : start + 1;
            const to   = Math.min(start + perPage, total);
            info.textContent = `Menampilkan ${from}–${to} dari ${total} data`;
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            pagination.innerHTML = '';
            if (totalPages <= 1) return;
            const mk = (label, page, disabled, active) => {
                const btn = document.createElement('button');
                btn.innerHTML = label;
                btn.className = `px-3 py-1.5 rounded-lg border text-[11px] font-semibold transition ${ active ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : disabled ? 'bg-white text-slate-300 border-slate-200 cursor-not-allowed' : 'bg-white text-slate-600 border-slate-200 hover:bg-orange-50 hover:border-orange-300'}`;
                btn.disabled = disabled;
                if (!disabled && !active) btn.addEventListener('click', () => { currentPage = page; applyAll(); });
                return btn;
            };
            pagination.appendChild(mk('<i class="bi bi-chevron-left"></i>', currentPage - 1, currentPage === 1, false));
            for (let i = 1; i <= totalPages; i++) {
                if (totalPages > 7 && i > 2 && i < totalPages - 1 && Math.abs(i - currentPage) > 1) {
                    if (i === 3 || i === totalPages - 2) { const d = document.createElement('span'); d.textContent = '…'; d.className = 'px-1 text-slate-400'; pagination.appendChild(d); }
                    continue;
                }
                pagination.appendChild(mk(i, i, false, i === currentPage));
            }
            pagination.appendChild(mk('<i class="bi bi-chevron-right"></i>', currentPage + 1, currentPage === totalPages, false));
        }

        document.querySelectorAll('.btn-pintas').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.btn-pintas').forEach(b => { b.className = 'btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition text-[11px] font-semibold'; });
                btn.className = 'btn-pintas px-3 py-1 rounded-full bg-slate-800 text-white transition text-[11px] font-semibold';
                pintasStatus = btn.dataset.status; currentPage = 1; applyAll();
            });
        });

        btnAdd.addEventListener('click', () => addFilterRow());

        btnReset.addEventListener('click', () => {
            filters = []; pintasStatus = 'all'; currentPage = 1;
            filterRows.innerHTML = '';
            document.querySelectorAll('.btn-pintas').forEach((b, i) => {
                b.className = `btn-pintas px-3 py-1 rounded-full transition text-[11px] font-semibold ${i === 0 ? 'bg-slate-800 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-orange-50'}`;
            });
            perPageSel.value = '10'; perPage = 10;
            addFilterRow();
        });

        perPageSel.addEventListener('change', () => { perPage = parseInt(perPageSel.value); currentPage = 1; applyAll(); });

        addFilterRow();
    })();
    </script>
</body>
</html>
