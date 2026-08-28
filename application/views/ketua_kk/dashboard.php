<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            900: '#7c2d12',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #fbf7f1;
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 88, 12, 0.15);
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05);
            border-radius: 1rem;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 2px 14px;
            height: 44px;
            transition: all 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }

        .autocomplete-box {
            max-height: 280px;
            overflow-y: auto;
        }

        /* 3D KINETIC INTERACTIVE BUTTON */
        .btn-3d-kinetic {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            text-decoration: none !important;
            user-select: none;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }
        .btn-3d-kinetic:hover {
            transform: translateY(-2px) scale(1.03);
        }
        .btn-3d-kinetic:active {
            transform: translateY(1px) scale(0.97);
        }
        .btn-3d-kinetic .bg {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: linear-gradient(180deg, #fb923c 0%, #ea580c 60%, #c2410c 100%);
            box-shadow: 
                0 10px 24px -4px rgba(234, 88, 12, 0.5),
                0 4px 10px -2px rgba(0, 0, 0, 0.2),
                inset 0 1.5px 2px rgba(255, 255, 255, 0.7);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-3d-kinetic:hover .bg {
            background: linear-gradient(180deg, #fdba74 0%, #f97316 55%, #ea580c 100%);
            box-shadow: 0 14px 30px -4px rgba(234, 88, 12, 0.65);
        }
        .btn-3d-kinetic .wrap {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 16px;
            min-width: 120px;
            height: 36px;
            border-radius: 9999px;
        }
        .btn-3d-kinetic .char {
            font-size: 11px;
            font-weight: 800;
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Ketua Kelompok Keahlian (KK)',
        'user_display_name' => 'Ketua KK Fakultas',
        'user_display_sub'  => 'Approval Bidang Keilmuan & Bimbingan'
    ]); ?>

    <!-- Sub Navigation Page Title Bar -->
    <div class="glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg shadow-sm">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Portal Ketua Kelompok Keahlian (KK)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Peninjauan bidang keilmuan usulan TA dan pembukaan modul bimbingan mahasiswa.</p>
                </div>
            </div>

            <!-- Profile Badge Right -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-bold text-slate-800 leading-tight">Ketua Kelompok Keahlian</span>
                    <span class="text-[10px] font-semibold text-slate-500">Evaluasi &amp; Approval KK</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-base shadow-xs">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <p class="font-bold"><?= $this->session->flashdata('success'); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold"><i class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-xs mb-6 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <p class="font-bold"><?= $this->session->flashdata('error'); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold"><i class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards Grid (Exact Design from Koor TA) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- 1. Total Pengajuan KK -->
            <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=all&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-brand-500/40 hover:shadow-2xl hover:shadow-brand-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Pengajuan KK</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['total']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Seluruh Mahasiswa KK</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-md text-brand-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-folder-tree text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-brand-500 to-transparent rounded-full group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 2. Siap Review KK (Cyan) -->
            <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=Pending&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Siap Review KK</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['ready']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Prasyarat Koor Disetujui</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-user-clock text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 3. Bimbingan Terbuka (Emerald) -->
            <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=Approved&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Bimbingan Terbuka</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['approved']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Resmi Unlocked</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-lock-open text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 4. Filter KK Aktif -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-purple-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-purple-500/40 hover:shadow-2xl hover:shadow-purple-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-purple-600 transition-colors">Kelompok Keahlian</p>
                            <h3 class="text-lg font-black text-purple-700 mt-1 tracking-tight truncate"><?= $selected_kk === 'all' ? 'Semua KK' : 'KK #' . $selected_kk; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Fokus Sub Bidang</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-purple-200/80 bg-gradient-to-br from-purple-50 to-purple-100/70 shadow-md text-purple-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-shapes text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tab Group for KK -->
        <div class="card-custom p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <a href="<?= site_url('ketuakk?kk=all&status=' . $filter_status . '&per_page=' . $per_page); ?>" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $selected_kk === 'all' ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Semua KK
                    </a>
                    <?php foreach($all_kk as $kk): ?>
                        <a href="<?= site_url('ketuakk?kk=' . $kk['id'] . '&status=' . $filter_status . '&per_page=' . $per_page); ?>" 
                           class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= (string)$selected_kk === (string)$kk['id'] ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                            <?= htmlspecialchars($kk['kode_kk']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5">
                    <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=all&per_page=' . $per_page); ?>" 
                       class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all <?= $filter_status === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Semua Status
                    </a>
                    <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=Pending&per_page=' . $per_page); ?>" 
                       class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all <?= $filter_status === 'Pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Siap Review
                    </a>
                    <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=Approved&per_page=' . $per_page); ?>" 
                       class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all <?= $filter_status === 'Approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Disetujui
                    </a>
                </div>
            </div>
        </div>

        <!-- Unified Search Pill Bar with AUTOCOMPLETE -->
        <div class="card-custom p-4 mb-6 relative">
            <form method="GET" action="<?= site_url('ketuakk'); ?>" id="formSearchKK" class="relative">
                <input type="hidden" name="kk" value="<?= htmlspecialchars($selected_kk); ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                <input type="hidden" name="per_page" value="<?= $per_page; ?>">

                <div class="unified-search-pill">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm ml-1"></i>
                    <input type="text" name="q" id="inputSearchKK" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Ketik nama mahasiswa, NIM, atau judul usulan TA..." 
                           class="w-full bg-transparent px-3 text-xs text-slate-800 font-semibold focus:outline-none">
                    
                    <?php if(!empty($search)): ?>
                        <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=' . $filter_status . '&per_page=' . $per_page); ?>" class="text-slate-400 hover:text-rose-600 text-xs font-bold px-2">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Autocomplete Dropdown -->
                <div id="autocompleteDropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResults" class="divide-y divide-slate-100 text-xs"></div>
                </div>
            </form>
        </div>

        <!-- Bulk Approval Form Wrapper -->
        <form id="formBulkApproval" method="POST" action="<?= site_url('ketuakk/submit_bulk_approval'); ?>">
            <!-- Table Card -->
            <div class="card-custom overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase tracking-wider text-[10px] font-bold">
                                <th class="py-4 px-3 text-center w-10">
                                    <input type="checkbox" id="selectAllKK" class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer" title="Pilih Semua Mahasiswa Siap Review">
                                </th>
                                <th class="py-4 px-5">Mahasiswa</th>
                                <th class="py-4 px-5">Kelompok Keahlian</th>
                                <th class="py-4 px-5">Judul Rencana TA</th>
                                <th class="py-4 px-5 text-center">Rantai Prasyarat</th>
                                <th class="py-4 px-5 text-center">Status Ketua KK</th>
                                <th class="py-4 px-5 text-center">Modul Bimbingan</th>
                                <th class="py-4 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if(empty($list_mahasiswa)): ?>
                                <tr>
                                    <td colspan="8" class="py-14 text-center text-slate-400">
                                        <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-3 block"></i>
                                        Tidak ada data mahasiswa ditemukan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($list_mahasiswa as $row): ?>
                                    <?php
                                        $is_wali_app  = ($row['status_approval_wali'] === 'Approved');
                                        $is_admin_app = ($row['status_approval_admin'] === 'Approved');
                                        $is_koor_app  = ($row['status_approval_koor'] === 'Approved');
                                        $is_kk_app    = ($row['status_approval_kk'] === 'Approved');
                                        $is_kk_rej    = ($row['status_approval_kk'] === 'Rejected');
                                        $is_unlocked  = ($row['is_bimbingan_unlocked'] == 1);
                                        $is_prereq_ok = ($is_wali_app && $is_admin_app && $is_koor_app);
                                        $can_bulk_approve = ($is_prereq_ok && !$is_kk_app);
                                    ?>
                                    <tr class="hover:bg-orange-50/40 transition-colors">
                                        <!-- Checkbox Selection -->
                                        <td class="py-4 px-3 text-center w-10">
                                            <?php if($can_bulk_approve): ?>
                                                <input type="checkbox" name="nim_list[]" value="<?= $row['nim']; ?>" class="kk-checkbox w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                                            <?php else: ?>
                                                <input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 text-slate-300 opacity-40 cursor-not-allowed">
                                            <?php endif; ?>
                                        </td>

                                        <!-- Mahasiswa -->
                                        <td class="py-4 px-5 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-orange-100 border border-orange-200 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                                    <?= strtoupper(substr($row['nama_depan'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($row['nama_depan'] . ' ' . $row['nama_belakang']); ?></div>
                                                    <div class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($row['nim']); ?></div>
                                                </div>
                                            </div>
                                        </td>

                                    <!-- KK -->
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 text-brand-700 rounded-lg text-[10px] font-bold border border-orange-200">
                                            <i class="fa-solid fa-shapes text-[9px]"></i>
                                            <?= htmlspecialchars($row['kode_kk'] ?? 'KK-VCM'); ?>
                                        </span>
                                    </td>

                                    <!-- Judul TA -->
                                    <td class="py-4 px-5 min-w-[240px] max-w-xs">
                                        <div class="font-semibold text-slate-800 line-clamp-2 text-xs leading-relaxed" title="<?= htmlspecialchars($row['judul_1']); ?>">
                                            <?= htmlspecialchars($row['judul_1']); ?>
                                        </div>
                                    </td>

                                    <!-- Rantai Prasyarat -->
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 bg-slate-50 px-3 py-1 rounded-xl border border-slate-200 text-[10px] font-mono shadow-xs">
                                            <span class="<?= $is_wali_app?'text-emerald-600 font-extrabold':'text-slate-400'; ?>">Wali</span>
                                            <span class="text-slate-300">›</span>
                                            <span class="<?= $is_admin_app?'text-emerald-600 font-extrabold':'text-slate-400'; ?>">LAA</span>
                                            <span class="text-slate-300">›</span>
                                            <span class="<?= $is_koor_app?'text-emerald-600 font-extrabold':'text-slate-400'; ?>">Koor</span>
                                        </div>
                                    </td>

                                    <!-- Status Ketua KK -->
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <?php if($is_kk_app): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa-solid fa-circle-check text-emerald-500"></i> Disetujui KK
                                            </span>
                                        <?php elseif($is_kk_rej): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-circle-xmark text-rose-500"></i> Ditolak KK
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="fa-solid fa-clock text-amber-600"></i> Menunggu KK
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Akses Bimbingan -->
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <?php if($is_unlocked): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold border border-emerald-200">
                                                <i class="fa-solid fa-lock-open text-emerald-600"></i> Unlocked
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-semibold border border-slate-200">
                                                <i class="fa-solid fa-lock text-slate-400"></i> Terkunci
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Aksi (3D Kinetic Button or Locked) -->
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <?php if($is_prereq_ok): ?>
                                            <a href="<?= site_url('ketuakk/detail/' . $row['nim']); ?>" class="btn-3d-kinetic">
                                                <span class="bg"></span>
                                                <span class="wrap">
                                                    <span class="content">
                                                        <i class="fa-solid fa-shield-halved icon-action"></i>
                                                        <span class="char state-1"><span>R</span><span>e</span><span>v</span><span>i</span><span>e</span><span>w</span></span>
                                                    </span>
                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span title="Prasyarat Dosen Wali, Admin LAA, atau Koordinator TA belum disetujui" 
                                                  class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl text-xs font-semibold cursor-not-allowed opacity-75">
                                                <i class="fa-solid fa-lock text-slate-400"></i> Locked
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION / PAGING FOOTER BAR -->
            <div class="px-5 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-3 text-slate-600">
                    <span class="font-medium text-slate-500">Tampilkan:</span>
                    <select id="selectPerPage" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-600 shadow-xs">
                        <option value="5" <?= $per_page == 5 ? 'selected' : ''; ?>>5 per halaman</option>
                        <option value="10" <?= $per_page == 10 ? 'selected' : ''; ?>>10 per halaman</option>
                        <option value="20" <?= $per_page == 20 ? 'selected' : ''; ?>>20 per halaman</option>
                    </select>
                    <span class="text-slate-300">|</span>
                    <span>
                        Menampilkan <strong><?= $total_rows > 0 ? (($page - 1) * $per_page + 1) : 0; ?></strong> - <strong><?= min($page * $per_page, $total_rows); ?></strong> dari <strong><?= $total_rows; ?></strong> mahasiswa
                    </span>
                </div>

                <?php if($total_pages > 1): ?>
                    <div class="flex items-center gap-1">
                        <?php if($page > 1): ?>
                            <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=' . $filter_status . '&q=' . urlencode($search) . '&per_page=' . $per_page . '&page=' . ($page - 1)); ?>" 
                               class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i> Prev
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i> Prev
                            </span>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=' . $filter_status . '&q=' . urlencode($search) . '&per_page=' . $per_page . '&page=' . $i); ?>" 
                               class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all <?= $i == $page ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'; ?>">
                                <?= $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if($page < $total_pages): ?>
                            <a href="<?= site_url('ketuakk?kk=' . $selected_kk . '&status=' . $filter_status . '&q=' . urlencode($search) . '&per_page=' . $per_page . '&page=' . ($page + 1)); ?>" 
                               class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs">
                                Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60">
                                Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Floating Sticky Bulk Action Bar -->
        <div id="bulkToolbar" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-slate-900 text-white px-6 py-3.5 rounded-2xl shadow-2xl flex items-center gap-4 border border-slate-700">
            <div class="flex items-center gap-2 text-xs font-bold text-orange-400">
                <i class="fa-solid fa-square-check text-base"></i>
                <span id="selectedCount">0</span> Mahasiswa Dipilih
            </div>

            <div class="h-5 w-px bg-slate-700"></div>

            <input type="text" name="catatan_kk_bulk" placeholder="Catatan rekomendasi massal (opsional)..." 
                   class="bg-slate-800 text-white text-xs px-3 py-1.5 rounded-xl border border-slate-700 focus:outline-none focus:border-brand-500 placeholder-slate-500 w-64">

            <button type="submit" onclick="return confirm('Yakin ingin menyetujui topik TA semua mahasiswa terpilih sekaligus & membuka akses modul bimbingan mereka?');" 
                    class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white rounded-xl text-xs font-extrabold shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-unlock-keyhole"></i> Setujui &amp; Unlock Terpilih
            </button>
        </div>
    </form>

    </main>

    <!-- AUTOCOMPLETE & LIVE SEARCH JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Limit Selector Event
            const selectPerPage = document.getElementById('selectPerPage');
            if (selectPerPage) {
                selectPerPage.addEventListener('change', function() {
                    const perPageVal = this.value;
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('per_page', perPageVal);
                    urlParams.set('page', '1');
                    window.location.search = urlParams.toString();
                });
            }

            // 2. BULK CHECKBOX SELECTION LISTENER
            const selectAllKK = document.getElementById('selectAllKK');
            const kkCheckboxes = document.querySelectorAll('.kk-checkbox');
            const bulkToolbar = document.getElementById('bulkToolbar');
            const selectedCount = document.getElementById('selectedCount');

            function updateBulkToolbar() {
                const checkedCount = document.querySelectorAll('.kk-checkbox:checked').length;
                if (selectedCount) selectedCount.innerText = checkedCount;
                if (bulkToolbar) {
                    if (checkedCount > 0) {
                        bulkToolbar.classList.remove('hidden');
                    } else {
                        bulkToolbar.classList.add('hidden');
                    }
                }
            }

            if (selectAllKK) {
                selectAllKK.addEventListener('change', function() {
                    kkCheckboxes.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = selectAllKK.checked;
                        }
                    });
                    updateBulkToolbar();
                });
            }

            kkCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateBulkToolbar();
                    if (!this.checked && selectAllKK) {
                        selectAllKK.checked = false;
                    }
                });
            });

            // 2. INSTANT LIVE AUTOMATIC SEARCH & AUTOCOMPLETE ACROSS ALL PAGES
            const inputSearch = document.getElementById('inputSearchKK');
            const dropdown = document.getElementById('autocompleteDropdown');
            const resultsBox = document.getElementById('autocompleteResults');
            const tableBody = document.querySelector('table tbody');
            let debounceTimer = null;

            if (inputSearch) {
                inputSearch.addEventListener('input', function() {
                    const q = this.value.trim();

                    if (tableBody) {
                        const rows = tableBody.querySelectorAll('tr');
                        const qLower = q.toLowerCase();
                        rows.forEach(row => {
                            const text = row.innerText.toLowerCase();
                            if (!qLower || text.includes(qLower)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }

                    clearTimeout(debounceTimer);

                    if (q.length < 2) {
                        if (dropdown) dropdown.classList.add('hidden');
                        if (q.length === 0) {
                            debounceTimer = setTimeout(() => {
                                const urlParams = new URLSearchParams(window.location.search);
                                if (urlParams.has('q')) {
                                    urlParams.delete('q');
                                    urlParams.set('page', '1');
                                    window.location.search = urlParams.toString();
                                }
                            }, 350);
                        }
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        const kkVal = '<?= htmlspecialchars($selected_kk); ?>';
                        fetch(`<?= site_url('ketuakk/autocomplete'); ?>?q=${encodeURIComponent(q)}&kk=${encodeURIComponent(kkVal)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.length > 0 && dropdown && resultsBox) {
                                    let html = '';
                                    data.forEach(item => {
                                        if (item.is_prereq_ok) {
                                            html += `
                                                <a href="<?= site_url('ketuakk/detail/'); ?>${item.nim}" class="p-3 hover:bg-orange-50 transition-colors flex items-center justify-between gap-3 block group">
                                                    <div>
                                                        <div class="font-bold text-slate-900 group-hover:text-brand-600 text-xs">${item.nama} <span class="text-slate-400 font-mono text-[11px]">(${item.nim})</span></div>
                                                        <div class="text-[11px] text-slate-500 line-clamp-1 italic">"${item.judul}"</div>
                                                    </div>
                                                    <span class="px-2.5 py-1 bg-orange-100 text-brand-700 text-[10px] font-bold rounded-lg shrink-0">Review &rarr;</span>
                                                </a>
                                            `;
                                        } else {
                                            html += `
                                                <div class="p-3 bg-slate-50 text-slate-400 flex items-center justify-between gap-3 opacity-75 cursor-not-allowed">
                                                    <div>
                                                        <div class="font-bold text-slate-700 text-xs">${item.nama} <span class="text-slate-400 font-mono text-[11px]">(${item.nim})</span></div>
                                                        <div class="text-[11px] text-slate-400 line-clamp-1 italic">"${item.judul}"</div>
                                                    </div>
                                                    <span class="px-2.5 py-1 bg-slate-200 text-slate-500 text-[10px] font-bold rounded-lg shrink-0 flex items-center gap-1"><i class="fa-solid fa-lock"></i> Locked</span>
                                                </div>
                                            `;
                                        }
                                    });
                                    resultsBox.innerHTML = html;
                                    dropdown.classList.remove('hidden');
                                } else if (dropdown && resultsBox) {
                                    resultsBox.innerHTML = `<div class="p-3.5 text-center text-slate-400 text-xs">Tidak ditemukan data cocok untuk "${q}"</div>`;
                                    dropdown.classList.remove('hidden');
                                }
                            })
                            .catch(err => {
                                console.error('Autocomplete error:', err);
                            });

                        const currentUrlParams = new URLSearchParams(window.location.search);
                        const currentQ = currentUrlParams.get('q') || '';
                        if (currentQ !== q) {
                            currentUrlParams.set('q', q);
                            currentUrlParams.set('page', '1');
                            window.location.search = currentUrlParams.toString();
                        }
                    }, 650);
                });

                document.addEventListener('click', function(e) {
                    if (dropdown && !inputSearch.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>

</body>
</html>
