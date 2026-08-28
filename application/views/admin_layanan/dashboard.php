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
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Admin Layanan FIK',
        'user_display_sub'  => 'Unit Akademik & Kelulusan'
    ]); ?>

    <!-- Sub Navigation Page Title Bar -->
    <div class="glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg shadow-sm">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Portal Admin Layanan Akademik (LAA)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Verifikasi 4 berkas kelengkapan pendaftaran Tugas Akhir mahasiswa IFIK.</p>
                </div>
            </div>

            <!-- Profile Badge Right -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-bold text-slate-800 leading-tight">Admin Layanan LAA</span>
                    <span class="text-[10px] font-semibold text-slate-500">Layanan Akademik FIK</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-base shadow-xs">
                    <i class="fa-solid fa-user-check"></i>
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
            <!-- 1. Total Pengajuan -->
            <a href="<?= site_url('adminlayanan?status=all&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-brand-500/40 hover:shadow-2xl hover:shadow-brand-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Pengajuan</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['total']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Berkas Pendaftaran</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-md text-brand-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-folder-closed text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-brand-500 to-transparent rounded-full group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce"></div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 2. Menunggu Cek (Cyan) -->
            <a href="<?= site_url('adminlayanan?status=Pending&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Menunggu Cek</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['pending']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Wali Disetujui</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-hourglass-half text-lg"></i>
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

            <!-- 3. Disetujui (Emerald) -->
            <a href="<?= site_url('adminlayanan?status=Approved&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Disetujui LAA</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['approved']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">4 Berkas Valid</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-file-circle-check text-lg"></i>
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

            <!-- 4. Dikembalikan (Rose) -->
            <a href="<?= site_url('adminlayanan?status=Rejected&per_page=' . $per_page); ?>" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 block">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-rose-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-rose-500/40 hover:shadow-2xl hover:shadow-rose-500/10 p-5">
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-rose-600 transition-colors">Dikembalikan</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $stats['rejected']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu Revisi Mahasiswa</p>
                        </div>
                        <div class="relative shrink-0">
                            <div class="p-3.5 rounded-2xl border border-rose-200/80 bg-gradient-to-br from-rose-50 to-rose-100/70 shadow-md text-rose-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-arrow-rotate-left text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-rose-500 to-transparent rounded-full group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1">
                            <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce"></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Filter Tab Group for LAA -->
        <div class="card-custom p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0" id="filterTabsContainerLAA">
                    <button type="button" onclick="switchLAATab('all')" id="tabLAA_all" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_status === 'all' ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Semua Status (<?= $stats['total']; ?>)
                    </button>
                    <button type="button" onclick="switchLAATab('Pending')" id="tabLAA_Pending" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_status === 'Pending' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Menunggu Cek (<?= $stats['pending']; ?>)
                    </button>
                    <button type="button" onclick="switchLAATab('Approved')" id="tabLAA_Approved" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_status === 'Approved' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Disetujui (<?= $stats['approved']; ?>)
                    </button>
                    <button type="button" onclick="switchLAATab('Rejected')" id="tabLAA_Rejected" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_status === 'Rejected' ? 'bg-rose-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Dikembalikan (<?= $stats['rejected']; ?>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Unified Search Pill Bar with AUTOCOMPLETE & INSTANT SEARCH -->
        <div class="card-custom p-4 mb-6 relative">
            <form onsubmit="return false;" id="formSearchLAA" class="relative">
                <div class="unified-search-pill">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm ml-1"></i>
                    <input type="text" name="q" id="inputSearchLAA" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Ketik nama mahasiswa, NIM, atau judul berkas TA..." 
                           class="w-full bg-transparent px-3 text-xs text-slate-800 font-semibold focus:outline-none">
                    
                    <button type="button" id="btnClearSearchLAA" onclick="clearLAASearch()" class="<?= empty($search) ? 'hidden' : ''; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-2 cursor-pointer">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <!-- Autocomplete Dropdown -->
                <div id="autocompleteDropdownLAA" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsLAA" class="divide-y divide-slate-100 text-xs"></div>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card-custom overflow-hidden">
            <div class="overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3.5 px-3 text-center w-8">
                                <input type="checkbox" id="checkAllStudents" title="Pilih Semua Mahasiswa" class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500 cursor-pointer">
                            </th>
                            <th class="py-3.5 px-3">Mahasiswa</th>
                            <th class="py-3.5 px-3">Program Studi &amp; KK</th>
                            <th class="py-3.5 px-3">Judul Rencana TA</th>
                            <th class="py-3.5 px-3 text-center">Status 4 Berkas</th>
                            <th class="py-3.5 px-2 text-center">Dosen Wali</th>
                            <th class="py-3.5 px-2 text-center">Admin LAA</th>
                            <th class="py-3.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white" id="tableBody">
                        <?php if(empty($list_pengajuan)): ?>
                            <tr>
                                <td colspan="8" class="py-14 text-center text-slate-400">
                                    <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-3 block"></i>
                                    Tidak ada data pengajuan berkas ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($list_pengajuan as $row): ?>
                                <?php
                                    $is_wali_app = (($row['status_approval_wali'] ?? '') === 'Approved');
                                    $laa_status  = $row['status_approval_admin'] ?? 'Pending';
                                    
                                    $ksm_st = $row['status_ksm'] ?? 'Pending';
                                    $trs_st = $row['status_transkrip'] ?? 'Pending';
                                    $prn_st = $row['status_pernyataan'] ?? 'Pending';
                                    $lab_st = $row['status_bebas_lab'] ?? 'Pending';
                                    
                                    $full_name = trim(($row['nama_depan'] ?? '') . ' ' . ($row['nama_belakang'] ?? ''));
                                    if (empty($full_name)) $full_name = 'Mahasiswa ' . ($row['nim'] ?? '');
                                ?>
                                <tr class="hover:bg-orange-50/40 transition-colors">
                                    <!-- Checkbox Selection -->
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <input type="checkbox" name="batch_select[]" value="<?= $row['nim']; ?>" 
                                               data-name="<?= htmlspecialchars($full_name); ?>" 
                                               data-prereq="<?= $is_wali_app ? '1' : '0'; ?>"
                                               <?= !$is_wali_app ? 'disabled title="Belum disetujui Dosen Wali"' : ''; ?> 
                                               class="student-cb w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500 cursor-pointer">
                                    </td>
                                    
                                    <!-- Mahasiswa -->
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-orange-100 border border-orange-200 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                <?= strtoupper(substr($row['nama_depan'] ?? 'M', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($full_name); ?></div>
                                                <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                                                    <span><?= htmlspecialchars($row['nim'] ?? ''); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Prodi & KK -->
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <div class="font-bold text-slate-700 text-xs"><?= htmlspecialchars($row['prodi'] ?? 'DKV'); ?></div>
                                        <span class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.2 bg-orange-50 text-brand-700 rounded text-[9px] font-bold border border-orange-200">
                                            <i class="fa-solid fa-diagram-project text-[8px]"></i>
                                            <span><?= htmlspecialchars($row['kode_kk'] ?? 'KK-VCM'); ?></span>
                                        </span>
                                    </td>

                                    <!-- Judul TA -->
                                    <td class="py-3.5 px-3 max-w-[200px] lg:max-w-[240px]">
                                        <div class="font-semibold text-slate-800 line-clamp-2 text-xs leading-relaxed" title="<?= htmlspecialchars($row['judul_1'] ?? ''); ?>">
                                            <?= htmlspecialchars($row['judul_1'] ?? ''); ?>
                                        </div>
                                    </td>

                                    <!-- Status 4 Berkas -->
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs">
                                            <span class="<?= $ksm_st === 'Valid' ? 'text-emerald-600 font-extrabold' : ($ksm_st === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">KSM</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="<?= $trs_st === 'Valid' ? 'text-emerald-600 font-extrabold' : ($trs_st === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">TRS</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="<?= $prn_st === 'Valid' ? 'text-emerald-600 font-extrabold' : ($prn_st === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">SRT</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="<?= $lab_st === 'Valid' ? 'text-emerald-600 font-extrabold' : ($lab_st === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">LAB</span>
                                        </div>
                                    </td>

                                    <!-- Status Dosen Wali -->
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                        <?php if($is_wali_app): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa-solid fa-circle-check text-emerald-500"></i> Disetujui
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                <i class="fa-solid fa-hourglass text-slate-400"></i> Belum Wali
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status Admin LAA -->
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                        <?php if($laa_status === 'Approved'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa-solid fa-check-double text-emerald-600"></i> Approved
                                            </span>
                                        <?php elseif($laa_status === 'Rejected'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-arrow-rotate-left text-rose-600"></i> Dikembalikan
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="fa-solid fa-clock text-amber-600"></i> Menunggu Cek
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Aksi (3D Kinetic Button or Locked) -->
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <?php if($is_wali_app): ?>
                                            <a href="<?= site_url('adminlayanan/detail_berkas/' . $row['nim']); ?>" class="btn-3d-kinetic">
                                                <span class="bg"></span>
                                                <span class="wrap">
                                                    <span class="content">
                                                        <i class="fa-solid fa-magnifying-glass icon-action"></i>
                                                        <span class="char state-1"><span>P</span><span>e</span><span>r</span><span>i</span><span>k</span><span>s</span><span>a</span></span>
                                                    </span>
                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span title="Pengajuan belum disetujui oleh Dosen Wali" 
                                                  class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl text-[11px] font-semibold cursor-not-allowed opacity-75">
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
                    <select id="selectPerPageLAA" onchange="changeLAAPerPage(this.value)" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-600 shadow-xs cursor-pointer">
                        <option value="5" <?= $per_page == 5 ? 'selected' : ''; ?>>5 per halaman</option>
                        <option value="10" <?= $per_page == 10 ? 'selected' : ''; ?>>10 per halaman</option>
                        <option value="20" <?= $per_page == 20 ? 'selected' : ''; ?>>20 per halaman</option>
                    </select>
                    <span class="text-slate-300">|</span>
                    <span id="txtShowingCountLAA">
                        Menampilkan <strong><?= $total_rows > 0 ? (($page - 1) * $per_page + 1) : 0; ?></strong> - <strong><?= min($page * $per_page, $total_rows); ?></strong> dari <strong><?= $total_rows; ?></strong> mahasiswa
                    </span>
                </div>

                <div id="laaPaginationControls">
                    <?php if($total_pages > 1): ?>
                        <div class="flex items-center gap-1">
                            <?php if($page > 1): ?>
                                <button type="button" onclick="changeLAAPage(<?= $page - 1; ?>)" 
                                   class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer">
                                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Prev
                                </button>
                            <?php else: ?>
                                <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60">
                                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Prev
                                </span>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <button type="button" onclick="changeLAAPage(<?= $i; ?>)" 
                                   class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all cursor-pointer <?= $i == $page ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'; ?>">
                                    <?= $i; ?>
                                </button>
                            <?php endfor; ?>

                            <?php if($page < $total_pages): ?>
                                <button type="button" onclick="changeLAAPage(<?= $page + 1; ?>)" 
                                   class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer">
                                    Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </button>
                            <?php else: ?>
                                <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60">
                                    Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

    <!-- REAL-TIME AJAX JAVASCRIPT SYSTEM FOR ADMIN LAA -->
    <script>
        let currentLAAState = {
            status: '<?= $filter_status; ?>',
            search: '<?= addslashes($search ?? ""); ?>',
            perPage: <?= $per_page; ?>,
            page: <?= $page; ?>
        };
        let laaSearchTimer = null;

        function refreshLAATable(isSilent = false) {
            const url = `<?= site_url("adminlayanan/ajax_get_table"); ?>?status=${encodeURIComponent(currentLAAState.status)}&q=${encodeURIComponent(currentLAAState.search)}&per_page=${currentLAAState.perPage}&page=${currentLAAState.page}`;

            const tbody = document.getElementById('tableBody');
            if (!tbody) return;

            if (!isSilent) {
                tbody.style.opacity = '0.4';
            }

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    tbody.style.opacity = '1';

                    if (!res.success) return;

                    // 1. Update Stats Badges & Tab Buttons
                    if (res.stats) {
                        const tabAll = document.getElementById('tabLAA_all');
                        const tabPending = document.getElementById('tabLAA_Pending');
                        const tabApproved = document.getElementById('tabLAA_Approved');
                        const tabRejected = document.getElementById('tabLAA_Rejected');

                        if (tabAll) tabAll.textContent = `Semua Status (${res.stats.total || 0})`;
                        if (tabPending) tabPending.textContent = `Menunggu Cek (${res.stats.pending || 0})`;
                        if (tabApproved) tabApproved.textContent = `Disetujui (${res.stats.approved || 0})`;
                        if (tabRejected) tabRejected.textContent = `Dikembalikan (${res.stats.rejected || 0})`;
                    }

                    // 2. Render Rows
                    if (!res.list || res.list.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="py-14 text-center text-slate-400">
                                    <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-3 block"></i>
                                    Tidak ada data pengajuan berkas ditemukan.
                                </td>
                            </tr>
                        `;
                    } else {
                        let html = '';
                        res.list.forEach(row => {
                            let ksmClass = row.status_ksm === 'Valid' ? 'text-emerald-600 font-extrabold' : (row.status_ksm === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400');
                            let trsClass = row.status_transkrip === 'Valid' ? 'text-emerald-600 font-extrabold' : (row.status_transkrip === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400');
                            let prnClass = row.status_pernyataan === 'Valid' ? 'text-emerald-600 font-extrabold' : (row.status_pernyataan === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400');
                            let labClass = row.status_bebas_lab === 'Valid' ? 'text-emerald-600 font-extrabold' : (row.status_bebas_lab === 'Invalid' ? 'text-rose-600 font-extrabold' : 'text-slate-400');

                            let waliBadge = row.is_wali_app ? `
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Disetujui
                                </span>` : `
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    <i class="fa-solid fa-hourglass text-slate-400"></i> Belum Wali
                                </span>`;

                            let adminBadge = '';
                            if (row.status_approval_admin === 'Approved') {
                                adminBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check-double text-emerald-600"></i> Approved</span>`;
                            } else if (row.status_approval_admin === 'Rejected') {
                                adminBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-arrow-rotate-left text-rose-600"></i> Dikembalikan</span>`;
                            } else {
                                adminBadge = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-clock text-amber-600"></i> Menunggu Cek</span>`;
                            }

                            let actionBtn = row.is_wali_app ? `
                                <a href="${row.detail_url}" class="btn-3d-kinetic">
                                    <span class="bg"></span>
                                    <span class="wrap">
                                        <span class="content">
                                            <i class="fa-solid fa-magnifying-glass icon-action"></i>
                                            <span class="char state-1"><span>P</span><span>e</span><span>r</span><span>i</span><span>k</span><span>s</span><span>a</span></span>
                                        </span>
                                    </span>
                                </a>` : `
                                <span title="Pengajuan belum disetujui oleh Dosen Wali" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl text-[11px] font-semibold cursor-not-allowed opacity-75">
                                    <i class="fa-solid fa-lock text-slate-400"></i> Locked
                                </span>`;

                            html += `
                                <tr class="hover:bg-orange-50/40 transition-colors">
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <input type="checkbox" name="batch_select[]" value="${row.nim}" 
                                               data-name="${row.full_name}" 
                                               data-prereq="${row.is_wali_app ? '1' : '0'}"
                                               ${!row.is_wali_app ? 'disabled title="Belum disetujui Dosen Wali"' : ''} 
                                               class="student-cb w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500 cursor-pointer">
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-orange-100 border border-orange-200 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                ${row.first_char}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs">${row.full_name}</div>
                                                <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                                                    <span>${row.nim}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 whitespace-nowrap">
                                        <div class="font-bold text-slate-700 text-xs">${row.prodi}</div>
                                        <span class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.2 bg-orange-50 text-brand-700 rounded text-[9px] font-bold border border-orange-200">
                                            <i class="fa-solid fa-diagram-project text-[8px]"></i>
                                            <span>${row.kode_kk}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 max-w-[200px] lg:max-w-[240px]">
                                        <div class="font-semibold text-slate-800 line-clamp-2 text-xs leading-relaxed" title="${row.judul_1}">
                                            ${row.judul_1}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs">
                                            <span class="${ksmClass}">KSM</span><span class="text-slate-300">·</span>
                                            <span class="${trsClass}">TRS</span><span class="text-slate-300">·</span>
                                            <span class="${prnClass}">SRT</span><span class="text-slate-300">·</span>
                                            <span class="${labClass}">LAB</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">${waliBadge}</td>
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">${adminBadge}</td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">${actionBtn}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                        rebindStudentCheckboxes();
                    }

                    // 3. Update Pagination Text & Controls
                    currentLAAState.page = res.page;
                    currentLAAState.total_pages = res.total_pages;

                    const txtCount = document.getElementById('txtShowingCountLAA');
                    if (txtCount) {
                        const start = res.total_rows > 0 ? ((res.page - 1) * res.per_page + 1) : 0;
                        const end = Math.min(res.page * res.per_page, res.total_rows);
                        txtCount.innerHTML = `Menampilkan <strong>${start}</strong> - <strong>${end}</strong> dari <strong>${res.total_rows}</strong> mahasiswa`;
                    }

                    renderLAAPaginationControls(res.page, res.total_pages);
                })
                .catch(err => {
                    if (tbody) tbody.style.opacity = '1';
                    console.error('AJAX Error:', err);
                });
        }

        function renderLAAPaginationControls(page, totalPages) {
            const container = document.getElementById('laaPaginationControls');
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div class="flex items-center gap-1">';
            
            if (page > 1) {
                html += `<button type="button" onclick="changeLAAPage(${page - 1})" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer"><i class="fa-solid fa-chevron-left text-[10px]"></i> Prev</button>`;
            } else {
                html += `<span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60"><i class="fa-solid fa-chevron-left text-[10px]"></i> Prev</span>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                if (i === page) {
                    html += `<button type="button" onclick="changeLAAPage(${i})" class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all bg-brand-600 text-white shadow-md cursor-pointer">${i}</button>`;
                } else {
                    html += `<button type="button" onclick="changeLAAPage(${i})" class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 cursor-pointer">${i}</button>`;
                }
            }

            if (page < totalPages) {
                html += `<button type="button" onclick="changeLAAPage(${page + 1})" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer">Next <i class="fa-solid fa-chevron-right text-[10px]"></i></button>`;
            } else {
                html += `<span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60">Next <i class="fa-solid fa-chevron-right text-[10px]"></i></span>`;
            }

            html += '</div>';
            container.innerHTML = html;
        }

        function switchLAATab(status) {
            currentLAAState.status = status;
            currentLAAState.page = 1;

            const tabs = ['all', 'Pending', 'Approved', 'Rejected'];
            tabs.forEach(t => {
                const btn = document.getElementById('tabLAA_' + t);
                if (btn) {
                    if (t === status) {
                        btn.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer ' + 
                                       (t === 'Pending' ? 'bg-amber-600 text-white shadow-md' : (t === 'Approved' ? 'bg-emerald-600 text-white shadow-md' : (t === 'Rejected' ? 'bg-rose-600 text-white shadow-md' : 'bg-brand-600 text-white shadow-md')));
                    } else {
                        btn.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                    }
                }
            });

            refreshLAATable();
        }

        function changeLAAPerPage(perPageVal) {
            currentLAAState.perPage = parseInt(perPageVal) || 5;
            currentLAAState.page = 1;
            refreshLAATable();
        }

        function changeLAAPage(pageNum) {
            currentLAAState.page = parseInt(pageNum) || 1;
            refreshLAATable();
        }

        function clearLAASearch() {
            const input = document.getElementById('inputSearchLAA');
            const btnClear = document.getElementById('btnClearSearchLAA');
            if (input) input.value = '';
            if (btnClear) btnClear.classList.add('hidden');

            currentLAAState.search = '';
            currentLAAState.page = 1;
            refreshLAATable();
        }

        function rebindStudentCheckboxes() {
            const checkAll = document.getElementById('checkAllStudents');
            const studentCbs = document.querySelectorAll('.student-cb');

            if (checkAll) {
                checkAll.checked = false;
                checkAll.onchange = function() {
                    studentCbs.forEach(cb => {
                        if (!cb.disabled) cb.checked = this.checked;
                    });
                    updateBatchBar();
                };
            }

            studentCbs.forEach(cb => {
                cb.onchange = () => {
                    updateBatchBar();
                    if (checkAll) {
                        const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                        const checkedCount = enabledCbs.filter(c => c.checked).length;
                        checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
                    }
                };
            });

            updateBatchBar();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const inputSearch = document.getElementById('inputSearchLAA');
            const btnClear = document.getElementById('btnClearSearchLAA');

            if (inputSearch) {
                inputSearch.addEventListener('input', function() {
                    const q = this.value.trim();

                    if (btnClear) {
                        if (q.length > 0) btnClear.classList.remove('hidden');
                        else btnClear.classList.add('hidden');
                    }

                    clearTimeout(laaSearchTimer);
                    laaSearchTimer = setTimeout(() => {
                        currentLAAState.search = q;
                        currentLAAState.page = 1;
                        refreshLAATable();
                    }, 250);
                });
            }

            // Silent Auto-polling every 8 seconds for real-time live data
            setInterval(() => {
                refreshLAATable(true);
            }, 8000);
        });
    </script>

    <!-- Floating Batch Action Bar -->
    <div id="batchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="selectedCountBadge">0</span>
            <span class="text-xs font-bold tracking-tight">Mahasiswa Terpilih</span>
        </div>
        
        <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>

        <div class="flex items-center gap-2.5">
            <!-- Button 1: Popup Batch Review -->
            <button type="button" onclick="openBatchModal()" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-2 transition-all active:scale-95 cursor-pointer">
                <i class="fa-solid fa-layer-group text-sm"></i> 📂 Cek Dokumen Massal (Popup)
            </button>

            <!-- Button 2: Direct Batch Approve -->
            <button type="button" onclick="submitDirectBatchApprove()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                <i class="fa-solid fa-check-double"></i> Setujui Massal (Approve)
            </button>

            <!-- Button 3: Uncheck All -->
            <button type="button" onclick="unselectAllStudents()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                <i class="fa-solid fa-xmark"></i> Batal
            </button>
        </div>
    </div>

    <!-- Multi-Student Batch Review Modal Popup -->
    <div id="batchReviewModal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-3 sm:p-5 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-6xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header: Multi-Student Summary & Quick Nav Anchors -->
            <div class="p-4 px-6 bg-slate-900 text-white flex flex-col md:flex-row items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-extrabold text-base shadow-md">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            Verifikasi Massal Berkas Mahasiswa (Tampil Semua)
                            <span class="bg-orange-600 text-white px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold" id="modalStudentCounter">0 Mahasiswa</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Seluruh dokumen dari semua mahasiswa terpilih ditampilkan secara langsung dalam satu halaman scroll.</p>
                    </div>
                </div>

                <!-- Student Quick Jump Anchor Chips -->
                <div id="modalStudentTabs" class="flex items-center gap-2 overflow-x-auto max-w-xl py-1 px-2 bg-slate-800/80 rounded-2xl border border-slate-700">
                    <!-- Quick Jump Anchors injected dynamically via JS -->
                </div>

                <button type="button" onclick="closeBatchModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Content Body (Stacked View for All Selected Students) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-8 flex-1 bg-slate-100/80" id="batchModalBody">
                <div class="py-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500 mb-3 block"></i>
                    Memuat data dokumen seluruh mahasiswa terpilih...
                </div>
            </div>

            <!-- Modal Footer Actions Bar -->
            <div class="p-4 px-6 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
                <button type="button" onclick="markAllBatchStudentsValid()" class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-all cursor-pointer">
                    <i class="fa-solid fa-check-double"></i> Tandai Semua Mahasiswa Valid
                </button>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="button" onclick="closeBatchModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="submitFinalBatchVerifications()" class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-xs font-black shadow-lg shadow-orange-600/20 flex items-center gap-2 transition-all cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i> SIMPAN &amp; PROSES SEMUA VERIFIKASI MASSAL
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Document PDF Preview Modal -->
    <div id="pdfModal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-2xl max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200">
            <div class="p-3.5 px-5 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white flex items-center gap-2" id="pdfModalTitle">Pratinjau Dokumen PDF</h3>
                        <p class="text-[10px] text-slate-400" id="pdfModalSubtitle">Memuat tampilan dokumen...</p>
                    </div>
                </div>
                <button type="button" onclick="closePdfModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 relative overflow-hidden">
                <iframe id="pdfFrame" src="about:blank" class="w-full h-full border-none"></iframe>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Batch Submissions -->
    <form id="formBatchSubmit" method="POST" action="<?= site_url('adminlayanan/submit_verifikasi_batch'); ?>">
        <input type="hidden" name="action" id="batchFormAction" value="approve_all">
        <input type="hidden" name="verifications_json" id="batchFormVerificationsJson" value="">
        <div id="batchFormNimsContainer"></div>
    </form>

    <!-- Batch Selection & Popup Script -->
    <script>
        window.batchStudents = [];
        window.activeStudentIndex = 0;

        document.addEventListener('DOMContentLoaded', () => {
            const checkAll = document.getElementById('checkAllStudents');
            const studentCbs = document.querySelectorAll('.student-cb');

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    studentCbs.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = this.checked;
                        }
                    });
                    updateBatchBar();
                });
            }

            studentCbs.forEach(cb => {
                cb.addEventListener('change', () => {
                    updateBatchBar();
                    if (checkAll) {
                        const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                        const checkedCount = enabledCbs.filter(c => c.checked).length;
                        checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
                    }
                });
            });
        });

        function updateBatchBar() {
            const checkedCbs = document.querySelectorAll('.student-cb:checked');
            const batchBar = document.getElementById('batchActionBar');
            const countBadge = document.getElementById('selectedCountBadge');

            if (checkedCbs.length > 0) {
                countBadge.textContent = checkedCbs.length;
                batchBar.classList.remove('hidden');
                batchBar.classList.add('flex');
            } else {
                batchBar.classList.add('hidden');
                batchBar.classList.remove('flex');
            }
        }

        function unselectAllStudents() {
            document.querySelectorAll('.student-cb').forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('checkAllStudents');
            if (checkAll) checkAll.checked = false;
            updateBatchBar();
        }

        function submitDirectBatchApprove() {
            const checkedCbs = document.querySelectorAll('.student-cb:checked');
            if (checkedCbs.length === 0) return;

            if (!confirm(`Yakin ingin MENYETUJUI (Approve) ${checkedCbs.length} berkas pendaftaran mahasiswa sekaligus?`)) {
                return;
            }

            const form = document.getElementById('formBatchSubmit');
            document.getElementById('batchFormAction').value = 'approve_all';
            const container = document.getElementById('batchFormNimsContainer');
            container.innerHTML = '';

            checkedCbs.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'nims[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                unselectAllStudents();
                refreshLAATable();
                showLAAToast(res.message || 'Verifikasi berhasil disetujui!');
            })
            .catch(err => {
                console.error('Submit approve error:', err);
                form.submit();
            });
        }

        function openBatchModal() {
            const checkedCbs = document.querySelectorAll('.student-cb:checked');
            if (checkedCbs.length === 0) return;

            const selectedNims = Array.from(checkedCbs).map(cb => cb.value);

            const modal = document.getElementById('batchReviewModal');
            const modalBody = document.getElementById('batchModalBody');
            
            modalBody.innerHTML = `
                <div class="py-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500 mb-3 block"></i>
                    Memuat data <strong>${selectedNims.length} mahasiswa</strong> terpilih...
                </div>
            `;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const formData = new FormData();
            selectedNims.forEach(nim => formData.append('nims[]', nim));

            fetch('<?= site_url("adminlayanan/get_batch_details"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data.length > 0) {
                    window.batchStudents = res.data.map(st => {
                        const berkas_kurang = st.berkas_kurang || [];
                        const is_rejected = (st.status_approval_admin === 'Rejected');
                        
                        let valid_arr = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
                        if (berkas_kurang.length > 0) {
                            valid_arr = valid_arr.filter(k => !berkas_kurang.includes(k));
                        }

                        return {
                            ...st,
                            ver_action: is_rejected ? 'reject' : (st.status_approval_admin === 'Approved' ? 'approve' : 'pending'),
                            berkas_valid: valid_arr,
                            berkas_kurang: berkas_kurang,
                            catatan_admin: st.catatan_admin || ''
                        };
                    });

                    renderAllBatchStudentsContent();
                } else {
                    modalBody.innerHTML = `<div class="py-12 text-center text-rose-500 font-bold text-xs">${res.message || 'Gagal memuat data.'}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = `<div class="py-12 text-center text-rose-500 font-bold text-xs">Terjadi kesalahan koneksi server.</div>`;
            });
        }

        function closeBatchModal() {
            const modal = document.getElementById('batchReviewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function renderAllBatchStudentsContent() {
            const modalBody = document.getElementById('batchModalBody');
            const counterBadge = document.getElementById('modalStudentCounter');
            const navContainer = document.getElementById('modalStudentTabs');
            const students = window.batchStudents;

            if (!modalBody || !students) return;

            if (counterBadge) {
                counterBadge.textContent = `${students.length} Mahasiswa Terpilih`;
            }

            // Quick Jump Nav Chips
            if (navContainer) {
                let navHtml = '';
                students.forEach((st, idx) => {
                    navHtml += `
                        <a href="#student_block_${idx}" class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white transition-colors whitespace-nowrap flex items-center gap-1.5">
                            <span>${idx + 1}. ${st.nama.split(' ')[0]}</span>
                        </a>
                    `;
                });
                navContainer.innerHTML = navHtml;
            }

            const docNames = {
                'ksm': { 
                    title: '1. KSM (Kartu Studi Mahasiswa)', 
                    icon: 'fa-file-lines',
                    presets: ['Tanpa TTD Dosen Wali', 'Mata Kuliah TA Belum Ada', 'File Buram / Tidak Jelas']
                },
                'transkrip': { 
                    title: '2. Transkrip Nilai Akademik', 
                    icon: 'fa-file-invoice',
                    presets: ['Belum Update Semester Terbaru', 'SKS Kelulusan Kurang', 'Belum Tervalidasi Resmi']
                },
                'pernyataan': { 
                    title: '3. Surat Pernyataan Mahasiswa', 
                    icon: 'fa-file-signature',
                    presets: ['Tanpa Materai Rp 10.000', 'Belum Ditandatangani', 'Format Surat Salah']
                },
                'bebas_lab': { 
                    title: '4. Surat Bebas Lab & Perpustakaan', 
                    icon: 'fa-building-columns',
                    presets: ['Tanpa Stempel Resmi Lab', 'Pinjaman Alat Lab Belum Lunas', 'Buku Perpus Belum Kembali']
                }
            };

            let allHtml = '';

            students.forEach((st, stIdx) => {
                let cardsHtml = '';
                Object.keys(docNames).forEach(key => {
                    const info = docNames[key];
                    const fileObj = st.files[key] || {};
                    const isValid = st.berkas_valid.includes(key);
                    const isKurang = st.berkas_kurang.includes(key);

                    cardsHtml += `
                        <div class="bg-white rounded-2xl p-4 border ${isKurang ? 'border-rose-300 bg-rose-50/20' : (isValid ? 'border-emerald-200 bg-emerald-50/10' : 'border-slate-200')} shadow-xs flex flex-col justify-between space-y-3">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-orange-100 text-brand-600 font-bold text-xs flex items-center justify-center">
                                            <i class="fa-solid ${info.icon}"></i>
                                        </div>
                                        <span class="font-bold text-slate-800 text-xs">${info.title}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold ${isValid ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (isKurang ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-500')}">
                                        ${isValid ? 'Valid' : (isKurang ? 'Kurang/Revisi' : 'Belum Dicek')}
                                    </span>
                                </div>
                                <p class="text-[11px] font-mono text-slate-400 truncate mb-2" title="${fileObj.name}">${fileObj.name}</p>

                                <!-- Live Embedded PDF View Frame -->
                                <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100 mb-2">
                                    <div class="p-1.5 px-3 bg-slate-800 text-white flex items-center justify-between text-[10px]">
                                        <span class="font-mono text-slate-300 truncate max-w-[180px]"><i class="fa-solid fa-file-pdf text-rose-400 mr-1"></i> ${fileObj.name}</span>
                                        <button type="button" onclick="openPdfPreview('${fileObj.url}', '${info.title}')" class="text-orange-300 hover:text-white font-bold flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-expand text-[9px]"></i> Layar Penuh
                                        </button>
                                    </div>
                                    <iframe src="${fileObj.url}#view=FitH&zoom=100&toolbar=1" class="w-full h-[450px] border-none bg-slate-100" loading="lazy"></iframe>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <label class="inline-flex items-center gap-1.5 font-bold ${isValid ? 'text-emerald-700' : 'text-slate-600'} cursor-pointer">
                                        <input type="checkbox" onchange="toggleBatchStudentDocValid(${stIdx}, '${key}', this.checked)" ${isValid ? 'checked' : ''} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                        <span>Valid</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 font-bold ${isKurang ? 'text-rose-700' : 'text-slate-600'} cursor-pointer">
                                        <input type="checkbox" onchange="toggleBatchStudentDocKurang(${stIdx}, '${key}', this.checked)" ${isKurang ? 'checked' : ''} class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                        <span>Kurang / Revisi</span>
                                    </label>
                                </div>

                                <!-- Per-Document Revision Note Field (Tampil saat Kurang/Revisi dicentang) -->
                                <div id="doc_note_box_${stIdx}_${key}" class="${isKurang ? '' : 'hidden'} pt-2.5 border-t border-rose-200 space-y-1.5 transition-all">
                                    <label class="text-[10px] font-bold text-rose-700 uppercase flex items-center justify-between">
                                        <span><i class="fa-solid fa-pen-to-square text-[9px] mr-1"></i> Catatan Revisi khusus ${info.title.split('.')[1] || info.title}:</span>
                                    </label>
                                    <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                        ${(info.presets || []).map(ps => `
                                            <button type="button" onclick="setDocNoteInBatch(${stIdx}, '${key}', '${ps.replace(/'/g, "\\'")}')" 
                                                    class="px-2 py-0.5 rounded bg-rose-100/90 hover:bg-rose-200 text-rose-800 text-[10px] font-bold border border-rose-200 cursor-pointer transition-colors">
                                                + ${ps}
                                            </button>
                                        `).join('')}
                                    </div>
                                    <input type="text" 
                                           id="catatan_doc_${stIdx}_${key}"
                                           value="${(st.catatan_berkas && st.catatan_berkas[key]) ? st.catatan_berkas[key] : ''}"
                                           oninput="syncDocNoteToCatatanAdmin(${stIdx})"
                                           placeholder="Tuliskan catatan perbaikan spesifik berkas ini..." 
                                           class="w-full px-3 py-1.5 bg-white border border-rose-300 rounded-xl text-xs font-medium text-slate-800 placeholder-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 shadow-2xs">
                                </div>
                            </div>
                        </div>
                    `;
                });

                const isApproved = (st.ver_action === 'approve');
                const hasReject = (st.berkas_kurang && st.berkas_kurang.length > 0) || (st.ver_action === 'reject');

                allHtml += `
                    <!-- Student Block ${stIdx + 1} -->
                    <div id="student_block_${stIdx}" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-300 shadow-md space-y-5">
                        
                        <!-- Student Header Info -->
                        <div class="bg-slate-900 text-white rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white font-black text-sm flex items-center justify-center shadow-md shrink-0">
                                    ${stIdx + 1}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-sm font-black text-white">${st.nama}</h2>
                                        <span class="font-mono text-xs font-bold text-slate-300">(${st.nim})</span>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-0.5 font-medium">${st.prodi} · Kode KK: <span class="font-bold text-orange-400">${st.kode_kk}</span></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-xl text-xs font-extrabold ${isApproved ? 'bg-emerald-500 text-white' : (hasReject ? 'bg-rose-500 text-white' : 'bg-slate-700 text-slate-200')}">
                                    ${isApproved ? '🟢 Valid / Approved' : (hasReject ? '🔴 Ada Revisi' : '🟡 Pending Verifikasi')}
                                </span>
                            </div>
                        </div>

                        <!-- 4 Document Cards Grid for Student ${stIdx + 1} -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            ${cardsHtml}
                        </div>

                        <!-- Catatan Admin Box for Student ${stIdx + 1} -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <span class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-comment-dots text-orange-600"></i> Ringkasan Catatan Verifikasi Admin (Terkirim ke ${st.nama}):
                                </span>
                            </div>
                            <textarea id="catatan_admin_st_${stIdx}" oninput="updateBatchStudentCatatanByIndex(${stIdx}, this.value)" rows="2" 
                                      placeholder="Pesan catatan revisi gabungan akan terisi otomatis dari tiap berkas atau bisa diketik manual..." 
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:outline-none focus:border-brand-600 shadow-2xs">${st.catatan_admin || ''}</textarea>
                        </div>
                    </div>
                `;
            });

            modalBody.innerHTML = allHtml;
        }

        function setDocNoteInBatch(stIdx, docKey, noteText) {
            const input = document.getElementById(`catatan_doc_${stIdx}_${docKey}`);
            if (!input) return;
            if (input.value.trim().length > 0) {
                input.value = input.value.trim() + ' ' + noteText;
            } else {
                input.value = noteText;
            }
            syncDocNoteToCatatanAdmin(stIdx);
        }

        function syncDocNoteToCatatanAdmin(stIdx) {
            const st = window.batchStudents[stIdx];
            if (!st) return;

            if (!st.catatan_berkas) st.catatan_berkas = {};

            const keys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
            const docLabels = {
                'ksm': 'KSM',
                'transkrip': 'Transkrip Nilai Akademik',
                'pernyataan': 'Surat Pernyataan',
                'bebas_lab': 'Surat Bebas Lab'
            };

            let compiledNotes = [];
            keys.forEach(k => {
                const input = document.getElementById(`catatan_doc_${stIdx}_${k}`);
                const val = input ? input.value.trim() : '';
                st.catatan_berkas[k] = val;
                if (val && st.berkas_kurang.includes(k)) {
                    compiledNotes.push(`[${docLabels[k]}] - ${val}`);
                }
            });

            const mainTextarea = document.getElementById(`catatan_admin_st_${stIdx}`);
            if (mainTextarea) {
                mainTextarea.value = compiledNotes.join('\n');
                st.catatan_admin = mainTextarea.value;
            }
        }

        function toggleBatchStudentDocValid(stIdx, docKey, isChecked) {
            const st = window.batchStudents[stIdx];
            if (!st) return;

            if (isChecked) {
                if (!st.berkas_valid.includes(docKey)) st.berkas_valid.push(docKey);
                st.berkas_kurang = st.berkas_kurang.filter(k => k !== docKey);
            } else {
                st.berkas_valid = st.berkas_valid.filter(k => k !== docKey);
            }

            if (st.berkas_kurang.length === 0 && (!st.catatan_admin || !st.catatan_admin.trim())) {
                st.ver_action = 'approve';
            } else {
                st.ver_action = 'reject';
            }

            renderAllBatchStudentsContent();
        }

        function toggleBatchStudentDocKurang(stIdx, docKey, isChecked) {
            const st = window.batchStudents[stIdx];
            if (!st) return;

            if (isChecked) {
                if (!st.berkas_kurang.includes(docKey)) st.berkas_kurang.push(docKey);
                st.berkas_valid = st.berkas_valid.filter(k => k !== docKey);
                st.ver_action = 'reject';
            } else {
                st.berkas_kurang = st.berkas_kurang.filter(k => k !== docKey);
                if (st.berkas_kurang.length === 0 && (!st.catatan_admin || !st.catatan_admin.trim())) {
                    st.ver_action = 'approve';
                }
            }

            renderAllBatchStudentsContent();
        }

        function updateBatchStudentCatatanByIndex(stIdx, val) {
            const st = window.batchStudents[stIdx];
            if (!st) return;
            st.catatan_admin = val;
            if (val.trim().length > 0 || st.berkas_kurang.length > 0) {
                st.ver_action = 'reject';
            } else {
                st.ver_action = 'approve';
            }
        }

        function appendBatchPresetNoteForStudent(stIdx, text) {
            const textarea = document.getElementById(`catatan_admin_st_${stIdx}`);
            if (!textarea) return;
            if (textarea.value.trim().length > 0) {
                textarea.value = textarea.value.trim() + ' ' + text;
            } else {
                textarea.value = text;
            }
            updateBatchStudentCatatanByIndex(stIdx, textarea.value);
        }

        function markAllBatchStudentsValid() {
            if (!window.batchStudents) return;
            window.batchStudents.forEach(st => {
                st.ver_action = 'approve';
                st.berkas_valid = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
                st.berkas_kurang = [];
                st.catatan_admin = '';
            });
            renderAllBatchStudentsContent();
        }

        function openPdfPreview(url, title) {
            const modal = document.getElementById('pdfModal');
            const frame = document.getElementById('pdfFrame');
            document.getElementById('pdfModalTitle').textContent = title || 'Pratinjau Dokumen PDF';
            document.getElementById('pdfModalSubtitle').textContent = url;
            frame.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePdfModal() {
            const modal = document.getElementById('pdfModal');
            const frame = document.getElementById('pdfFrame');
            frame.src = 'about:blank';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modals when clicking dark backdrop outside modal box
        document.addEventListener('DOMContentLoaded', () => {
            const batchModal = document.getElementById('batchReviewModal');
            if (batchModal) {
                batchModal.addEventListener('click', (e) => {
                    if (e.target === batchModal) {
                        closeBatchModal();
                    }
                });
            }

            const pdfModal = document.getElementById('pdfModal');
            if (pdfModal) {
                pdfModal.addEventListener('click', (e) => {
                    if (e.target === pdfModal) {
                        closePdfModal();
                    }
                });
            }
        });

        function submitFinalBatchVerifications() {
            const students = window.batchStudents;
            if (!students || students.length === 0) return;

            if (!confirm(`Apakah Anda yakin ingin MENYIMPAN & MEMPROSES verifikasi massal untuk ${students.length} mahasiswa ini?`)) {
                return;
            }

            const verificationsPayload = students.map(st => ({
                nim: st.nim,
                action: st.ver_action,
                catatan_admin: st.catatan_admin,
                berkas_valid: st.berkas_valid,
                berkas_kurang: st.berkas_kurang
            }));

            const form = document.getElementById('formBatchSubmit');
            document.getElementById('batchFormAction').value = 'batch_update';
            document.getElementById('batchFormVerificationsJson').value = JSON.stringify(verificationsPayload);

            const container = document.getElementById('batchFormNimsContainer');
            container.innerHTML = '';
            students.forEach(st => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'nims[]';
                input.value = st.nim;
                container.appendChild(input);
            });

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                closeBatchModal();
                unselectAllStudents();
                refreshLAATable();
                showLAAToast(res.message || 'Verifikasi massal berhasil disimpan!');
            })
            .catch(err => {
                console.error('Submit batch error:', err);
                form.submit();
            });
        }

        function showLAAToast(msg) {
            let toast = document.getElementById('laaToastNotification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'laaToastNotification';
                toast.className = 'fixed top-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-emerald-500/50 transition-all duration-500 transform translate-y-0 opacity-100';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i> <span class="text-xs font-bold">${msg}</span>`;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 5000);
        }
    </script>

</body>
</html>
