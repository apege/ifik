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

        @keyframes popInCard {
            0% { opacity: 0; transform: scale(0.9) translateY(24px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes fadeInSlideRight {
            0% { opacity: 0; transform: scale(0.95) translateX(60px); }
            100% { opacity: 1; transform: scale(1) translateX(0); }
        }
        @keyframes fadeInDownSmooth {
            0% { opacity: 0; transform: translateY(-16px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-pop-in { animation: popInCard 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-preview-in { animation: fadeInSlideRight 1.1s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-bar-in { animation: fadeInDownSmooth 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .student-card-item { will-change: transform; transition: box-shadow 0.4s ease, border-color 0.4s ease; }
        .preview-card-item { will-change: transform, width, max-width, opacity; }
        #wrapperPreviewBerkas { transition: all 1.1s cubic-bezier(0.2, 0.9, 0.2, 1); }
        #wrapperDaftarMhs::-webkit-scrollbar { width: 5px; }
        #wrapperDaftarMhs::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 9999px; }
        #wrapperDaftarMhs::-webkit-scrollbar-track { background: transparent; }
        .btn-3d-orange {
            background: linear-gradient(180deg, #fb923c 0%, #ea580c 60%, #c2410c 100%);
            box-shadow: 0 4px 12px -2px rgba(234, 88, 12, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.5);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-3d-orange:hover {
            background: linear-gradient(180deg, #fdba74 0%, #f97316 55%, #ea580c 100%);
            box-shadow: 0 6px 18px -2px rgba(234, 88, 12, 0.55);
            transform: translateY(-1px);
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
                <a href="<?= site_url('adminlayanan/pengaturan_jalur'); ?>" class="btn-3d-kinetic inline-flex items-center gap-2">
                    <span class="bg"></span>
                    <span class="wrap">
                        <i class="bi bi-sliders text-white text-xs mr-1.5"></i>
                        <span class="char">Pengaturan Jalur Sidang/Non-Sidang</span>
                    </span>
                </a>
                <a href="<?= site_url('adminlayanan/pengaturan_berkas'); ?>" class="btn-3d-kinetic inline-flex items-center gap-2">
                    <span class="bg"></span>
                    <span class="wrap">
                        <i class="bi bi-gear-fill text-white text-xs mr-1.5"></i>
                        <span class="char">Pengaturan Berkas TA</span>
                    </span>
                </a>

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

        <!-- Unified Multi-Search Bar (Exact Koordinator TA & Import Akun Style) -->
        <div class="card-custom p-4 mb-6 relative">
            <form action="<?= site_url('adminlayanan'); ?>" method="GET" id="formSearchLAA" class="relative search-pill-container" id="multiSearchWrapper">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                <input type="hidden" name="cat" id="mainCategorySelectLAA" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                
                <div class="flex items-center gap-2.5">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1">
                        <!-- Main Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container shrink-0">
                            <button type="button" onclick="toggleLAACustomDropdown('main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-brand-600 focus:outline-none">
                                <span id="label-filter-main-cat" class="truncate max-w-[130px]">Cari Kata Kunci</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-cat"></i>
                            </button>
                            <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                <div onclick="selectLAAMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectLAAMainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectLAAMainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectLAAMainCategory('judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>📖 Judul Tugas Akhir</span></div>
                                <div onclick="selectLAAMainCategory('prodi', '🎯 Program Studi & KK', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🎯 Program Studi & KK</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Text Container -->
                        <div id="mainValueContainer" class="flex-1 flex items-center min-w-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchLAA" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>" 
                                   placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." 
                                   class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                            
                            <button type="button" id="btnClearSearchLAA" onclick="clearLAASearch()" class="<?= empty($search) ? 'hidden' : ''; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1 cursor-pointer shrink-0">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </div>

                        <!-- Tombol Cari -->
                        <button type="submit" id="btnSubmitSearchLAA" class="px-3.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtn" onclick="toggleLAAMultiFilter(event)" class="btn-standalone-add shrink-0" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
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
                            <th class="py-3.5 px-3 text-center">Status Berkas Persyaratan</th>
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

                                    <!-- Status Berkas Persyaratan Dinamis dengan Ringkasan Angka & Indikator Warna -->
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">
                                        <?php 
                                            $b_summary = $berkas_summaries[$row['nim']] ?? null;
                                            if (!$b_summary) {
                                                $b_summary = $this->AdminLayanan_model->get_student_berkas_summary($row['nim'], $syarat_berkas, $row);
                                            }
                                            $v_cnt = $b_summary['valid_count'];
                                            $i_cnt = $b_summary['invalid_count'];
                                            $p_cnt = $b_summary['pending_count'];
                                            $b_items = $b_summary['items'];
                                            $detail_link = site_url('adminlayanan/detail_berkas/' . $row['nim']);
                                        ?>
                                        <div class="flex flex-col items-center gap-1.5">
                                            <!-- Ringkasan Status Warna + Angka -->
                                            <div class="flex items-center gap-1 flex-wrap justify-center">
                                                <?php if($v_cnt > 0): ?>
                                                    <button type="button" onclick="openQuickDocReview('<?= $row['nim']; ?>')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="<?= $v_cnt; ?> Berkas Disetujui/Valid — Klik untuk Lihat Multi Card">
                                                        <i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i>
                                                        <span><?= $v_cnt; ?> Valid</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($i_cnt > 0): ?>
                                                    <button type="button" onclick="openQuickDocReview('<?= $row['nim']; ?>')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="<?= $i_cnt; ?> Berkas Ditolak/Direvisi — Klik untuk Lihat Multi Card">
                                                        <i class="fa-solid fa-circle-xmark text-rose-500 text-[9px]"></i>
                                                        <span><?= $i_cnt; ?> Direvisi</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($p_cnt > 0): ?>
                                                    <button type="button" onclick="openQuickDocReview('<?= $row['nim']; ?>')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="<?= $p_cnt; ?> Berkas Menunggu Verifikasi — Klik untuk Lihat Multi Card">
                                                        <i class="fa-solid fa-clock text-amber-500 text-[9px]"></i>
                                                        <span><?= $p_cnt; ?> Menunggu</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($v_cnt === 0 && $i_cnt === 0 && $p_cnt === 0): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                        Belum ada berkas
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Status Berkas Persyaratan Dinamis per Item (Klik untuk Pop-up Multi-Card / Preview PDF) -->
                                            <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs flex-wrap justify-center max-w-[240px]">
                                                <?php foreach ($b_items as $s_idx => $item): ?>
                                                    <?php 
                                                        $st = $item['status'];
                                                        $color = ($st === 'Valid' || $st === 'Approved') ? 'text-emerald-600 font-extrabold hover:underline hover:scale-125 transition-transform' : (($st === 'Invalid' || $st === 'Rejected') ? 'text-rose-600 font-extrabold hover:underline hover:scale-125 transition-transform' : 'text-slate-400 hover:underline hover:scale-125 transition-transform');
                                                        $item_url = htmlspecialchars($item['file_url'] ?? '');
                                                        $item_nama = htmlspecialchars($item['nama'] ?? '');
                                                        $item_full_name = htmlspecialchars(trim(($row['nama_depan'] ?? '') . ' ' . ($row['nama_belakang'] ?? '')));
                                                    ?>
                                                    <?php if($s_idx > 0): ?><span class="text-slate-300">·</span><?php endif; ?>
                                                    <button type="button" onclick="openQuickDocReview('<?= $row['nim']; ?>', '<?= $item['kode']; ?>')" class="<?= $color; ?> inline-block px-0.5 cursor-pointer border-0 bg-transparent p-0" title="Klik untuk Pratinjau berkas <?= $item_nama; ?> (<?= $st; ?>)"><?= htmlspecialchars($item['short']); ?></button>
                                                <?php endforeach; ?>
                                            </div>
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
                                            <a href="<?= site_url('adminlayanan/detail_berkas/' . $row['nim']); ?>" class="btn-3d-kinetic" title="Buka Halaman Detail Berkas Lengkap">
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
                        <div class="flex items-center gap-1 flex-wrap">
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

                            <?php 
                            $pages = array();
                            if ($total_pages <= 7) {
                                for ($i = 1; $i <= $total_pages; $i++) $pages[] = $i;
                            } else {
                                $pages[] = 1;
                                if ($page > 3) $pages[] = '...';
                                $start = max(2, $page - 1);
                                $end = min($total_pages - 1, $page + 1);
                                for ($i = $start; $i <= $end; $i++) {
                                    if (!in_array($i, $pages)) $pages[] = $i;
                                }
                                if ($page < $total_pages - 2) $pages[] = '...';
                                if (!in_array($total_pages, $pages)) $pages[] = $total_pages;
                            }
                            foreach($pages as $p): 
                            ?>
                                <?php if($p === '...'): ?>
                                    <span class="px-2 text-slate-400 font-bold text-xs select-none">...</span>
                                <?php else: ?>
                                    <button type="button" onclick="changeLAAPage(<?= $p; ?>)" 
                                       class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all cursor-pointer <?= $p == $page ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'; ?>">
                                        <?= $p; ?>
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>

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
        window.selectedLAANims = window.selectedLAANims || new Set();

        function refreshLAATable(isSilent = false) {
            const url = `<?= site_url("adminlayanan/ajax_get_table"); ?>?status=${encodeURIComponent(currentLAAState.status)}&cat=${encodeURIComponent(currentLAAState.cat || 'query')}&q=${encodeURIComponent(currentLAAState.search)}&per_page=${currentLAAState.perPage}&page=${currentLAAState.page}`;

            if (window.history && window.history.replaceState) {
                const searchParams = new URLSearchParams(window.location.search);
                searchParams.set('status', currentLAAState.status);
                searchParams.set('cat', currentLAAState.cat || 'query');
                searchParams.set('q', currentLAAState.search);
                searchParams.set('per_page', currentLAAState.perPage);
                searchParams.set('page', currentLAAState.page);
                const newUrl = window.location.pathname + '?' + searchParams.toString();
                window.history.replaceState(null, '', newUrl);
            }

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
                        res.list.forEach((row, idx) => {
                            if (row && row.nim) {
                                if (!window.mhsDataMap) window.mhsDataMap = {};
                                window.mhsDataMap[String(row.nim).trim()] = row;
                            }
                            let bSummary = row.berkas_summary;
                            let summaryHTML = '';
                            let isWaliApp = row.is_wali_app;
                            let detailUrl = row.detail_url;

                            if (bSummary) {
                                let vCnt = bSummary.valid_count || 0;
                                let iCnt = bSummary.invalid_count || 0;
                                let pCnt = bSummary.pending_count || 0;
                                let items = bSummary.items || [];

                                 let badgesHTML = '';
                                if (vCnt > 0) {
                                    badgesHTML += `<button type="button" onclick="openQuickDocReview('${row.nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${vCnt} Berkas Disetujui/Valid — Klik untuk Lihat Multi Card"><i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i> <span>${vCnt} Valid</span></button>`;
                                }
                                if (iCnt > 0) {
                                    badgesHTML += `<button type="button" onclick="openQuickDocReview('${row.nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${iCnt} Berkas Ditolak/Direvisi — Klik untuk Lihat Multi Card"><i class="fa-solid fa-circle-xmark text-rose-500 text-[9px]"></i> <span>${iCnt} Direvisi</span></button>`;
                                }
                                if (pCnt > 0) {
                                    badgesHTML += `<button type="button" onclick="openQuickDocReview('${row.nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${pCnt} Berkas Menunggu Verifikasi — Klik untuk Lihat Multi Card"><i class="fa-solid fa-clock text-amber-500 text-[9px]"></i> <span>${pCnt} Menunggu</span></button>`;
                                }
                                if (vCnt === 0 && iCnt === 0 && pCnt === 0) {
                                    badgesHTML = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">Belum ada berkas</span>`;
                                }

                                let itemsHTML = '';
                                items.forEach((item, idx) => {
                                    let color = (item.status === 'Valid' || item.status === 'Approved') ? 'text-emerald-600 font-extrabold hover:underline hover:scale-125 transition-transform' : ((item.status === 'Invalid' || item.status === 'Rejected') ? 'text-rose-600 font-extrabold hover:underline hover:scale-125 transition-transform' : 'text-slate-400 hover:underline hover:scale-125 transition-transform');

                                    if (idx > 0) itemsHTML += `<span class="text-slate-300">·</span>`;
                                    itemsHTML += `<button type="button" onclick="openQuickDocReview('${row.nim}', '${item.kode}')" class="${color} inline-block px-0.5 cursor-pointer border-0 bg-transparent p-0" title="Klik untuk Lihat &amp; Pratinjau berkas ${item.nama} (${item.status})">${item.short}</button>`;
                                });

                                let itemsWrapper = `<div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs flex-wrap justify-center max-w-[240px]">${itemsHTML}</div>`;

                                summaryHTML = `
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="flex items-center gap-1 flex-wrap justify-center">
                                            ${badgesHTML}
                                        </div>
                                        ${itemsWrapper}
                                    </div>
                                `;
                            } else {
                                summaryHTML = `
                                    <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs">
                                        <span class="text-slate-400">Pending</span>
                                    </div>
                                `;
                            }

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
                                <a href="${row.detail_url}" class="btn-3d-kinetic" title="Buka Halaman Detail Berkas Lengkap">
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
                                        ${summaryHTML}
                                    </td>
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">${waliBadge}</td>
                                    <td class="py-3.5 px-2 text-center whitespace-nowrap">${adminBadge}</td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">${actionBtn}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                        rebindStudentCheckboxes();
                        if (window.activeLihatBerkasNims && window.activeLihatBerkasNims.length > 0) {
                            refreshLihatBerkasView();
                        }
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
        window.refreshLAATable = refreshLAATable;

        function renderLAAPaginationControls(page, totalPages) {
            const container = document.getElementById('laaPaginationControls');
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div class="flex items-center gap-1 flex-wrap">';
            
            if (page > 1) {
                html += `<button type="button" onclick="changeLAAPage(${page - 1})" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer"><i class="fa-solid fa-chevron-left text-[10px]"></i> Prev</button>`;
            } else {
                html += `<span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60"><i class="fa-solid fa-chevron-left text-[10px]"></i> Prev</span>`;
            }

            const pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                if (page > 3) pages.push('...');
                
                const start = Math.max(2, page - 1);
                const end = Math.min(totalPages - 1, page + 1);
                for (let i = start; i <= end; i++) {
                    if (!pages.includes(i)) pages.push(i);
                }
                
                if (page < totalPages - 2) pages.push('...');
                if (!pages.includes(totalPages)) pages.push(totalPages);
            }

            pages.forEach(p => {
                if (p === '...') {
                    html += `<span class="px-2 text-slate-400 font-bold text-xs select-none">...</span>`;
                } else if (p === page) {
                    html += `<button type="button" onclick="changeLAAPage(${p})" class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all bg-brand-600 text-white shadow-md cursor-pointer">${p}</button>`;
                } else {
                    html += `<button type="button" onclick="changeLAAPage(${p})" class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 cursor-pointer">${p}</button>`;
                }
            });

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

            // Re-apply checked state from selectedLAANims
            studentCbs.forEach(cb => {
                if (window.selectedLAANims.has(cb.value)) {
                    cb.checked = true;
                }
                cb.onchange = function() {
                    if (this.checked) {
                        window.selectedLAANims.add(this.value);
                    } else {
                        window.selectedLAANims.delete(this.value);
                    }
                    updateBatchBar();
                    if (checkAll) {
                        const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                        const checkedCount = enabledCbs.filter(c => c.checked).length;
                        checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
                    }
                };
            });

            if (checkAll) {
                const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                const checkedCount = enabledCbs.filter(c => c.checked).length;
                checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);

                checkAll.onchange = function() {
                    enabledCbs.forEach(cb => {
                        cb.checked = this.checked;
                        if (this.checked) {
                            window.selectedLAANims.add(cb.value);
                        } else {
                            window.selectedLAANims.delete(cb.value);
                        }
                    });
                    updateBatchBar();
                };
            }

            updateBatchBar();
        }

        function toggleLAACustomDropdown(id, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menu-filter-' + id);
            const arrow = document.getElementById('arrow-filter-' + id);
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));

            if (isHidden) {
                menu.classList.remove('hidden');
                if (arrow) arrow.classList.add('rotate-180');
            } else {
                if (arrow) arrow.classList.remove('rotate-180');
            }
        }

        function selectLAAMainCategory(catKey, catLabel, el) {
            const hiddenCat = document.getElementById('mainCategorySelectLAA');
            const labelEl = document.getElementById('label-filter-main-cat');
            if (hiddenCat) hiddenCat.value = catKey;
            if (labelEl) labelEl.textContent = catLabel;

            document.querySelectorAll('#menu-filter-main-cat .dropdown-item').forEach(i => {
                i.classList.remove('bg-orange-50', 'text-orange-600');
                i.classList.add('text-slate-700');
            });
            if (el) {
                el.classList.add('bg-orange-50', 'text-orange-600');
                el.classList.remove('text-slate-700');
            }

            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        });

        document.addEventListener('DOMContentLoaded', () => {
            const inputSearch = document.getElementById('inputSearchLAA');
            const btnClear = document.getElementById('btnClearSearchLAA');
            const formSearch = document.getElementById('formSearchLAA');

            if (inputSearch) {
                inputSearch.addEventListener('input', function() {
                    const q = this.value.trim();
                    if (btnClear) {
                        if (q.length > 0) btnClear.classList.remove('hidden');
                        else btnClear.classList.add('hidden');
                    }
                });
            }

            if (formSearch) {
                formSearch.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const q = inputSearch ? inputSearch.value.trim() : '';
                    const catEl = document.getElementById('mainCategorySelectLAA');
                    currentLAAState.search = q;
                    currentLAAState.cat = catEl ? catEl.value : 'query';
                    currentLAAState.page = 1;
                    refreshLAATable();
                });
            }
        });
    </script>

    <!-- Floating Batch Action Bar -->
    <div id="batchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300">
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
    <div id="batchReviewModal" class="fixed inset-0 z-[10000] bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-3 sm:p-5 overflow-y-auto">
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
    <div id="pdfModal" class="fixed inset-0 z-[10001] bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
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
            rebindStudentCheckboxes();
        });

        function updateBatchBar() {
            const checkedCbs = document.querySelectorAll('.student-cb:checked');
            if (window.selectedLAANims) {
                checkedCbs.forEach(cb => window.selectedLAANims.add(cb.value));
            }
            const count = (window.selectedLAANims && window.selectedLAANims.size > 0) ? window.selectedLAANims.size : checkedCbs.length;
            const batchBar = document.getElementById('batchActionBar');
            const countBadge = document.getElementById('selectedCountBadge');

            if (batchBar && countBadge) {
                countBadge.textContent = count;
                if (count > 0) {
                    batchBar.classList.remove('hidden');
                    batchBar.style.display = 'flex';
                } else {
                    batchBar.classList.add('hidden');
                    batchBar.style.display = 'none';
                }
            }
        }

        function unselectAllStudents() {
            if (window.selectedLAANims) window.selectedLAANims.clear();
            document.querySelectorAll('.student-cb').forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('checkAllStudents');
            if (checkAll) checkAll.checked = false;
            updateBatchBar();
        }

        function submitDirectBatchApprove() {
            const selectedNims = Array.from(window.selectedLAANims || []);
            if (selectedNims.length === 0) return;

            if (!confirm(`Yakin ingin MENYETUJUI (Approve) ${selectedNims.length} berkas pendaftaran mahasiswa sekaligus?`)) {
                return;
            }

            const form = document.getElementById('formBatchSubmit');
            document.getElementById('batchFormAction').value = 'approve_all';
            const container = document.getElementById('batchFormNimsContainer');
            container.innerHTML = '';

            selectedNims.forEach(nim => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'nims[]';
                input.value = nim;
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
            const selectedNims = Array.from(window.selectedLAANims || []);
            if (selectedNims.length === 0) return;

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
                        
                        let valid_arr = [];
                        let invalid_arr = berkas_kurang.slice();
                        if (st.files) {
                            Object.keys(st.files).forEach(k => {
                                const fSt = st.files[k].status;
                                if (fSt === 'Valid' || fSt === 'Approved') {
                                    if (!valid_arr.includes(k)) valid_arr.push(k);
                                } else if (fSt === 'Invalid' || fSt === 'Rejected') {
                                    if (!invalid_arr.includes(k)) invalid_arr.push(k);
                                }
                            });
                        }

                        return {
                            ...st,
                            ver_action: is_rejected ? 'reject' : (st.status_approval_admin === 'Approved' ? 'approve' : 'pending'),
                            berkas_valid: valid_arr,
                            berkas_kurang: invalid_arr,
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

            let allHtml = '';

            students.forEach((st, stIdx) => {
                let cardsHtml = '';
                const fileKeys = Object.keys(st.files || {});

                fileKeys.forEach((key, docIdx) => {
                    const fileObj = st.files[key] || {};
                    const isValid = st.berkas_valid.includes(key);
                    const isKurang = st.berkas_kurang.includes(key);
                    const docTitle = fileObj.title || `${docIdx + 1}. Berkas ${key.toUpperCase()}`;
                    const defaultPresets = ['Dokumen Belum Lengkap / Sesuai', 'File Buram / Tidak Jelas', 'Perlu Diperbarui'];

                    cardsHtml += `
                        <div class="bg-white rounded-2xl p-4 border ${isKurang ? 'border-rose-300 bg-rose-50/20' : (isValid ? 'border-emerald-200 bg-emerald-50/10' : 'border-slate-200')} shadow-xs flex flex-col justify-between space-y-3">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-orange-100 text-brand-600 font-bold text-xs flex items-center justify-center">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <span class="font-bold text-slate-800 text-xs">${docTitle}</span>
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
                                        <button type="button" onclick="openPdfPreview('${fileObj.url}', '${docTitle.replace(/'/g, "\\'")}')" class="text-orange-300 hover:text-white font-bold flex items-center gap-1 cursor-pointer">
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

                                <!-- Per-Document Revision Note Field -->
                                <div id="doc_note_box_${stIdx}_${key}" class="${isKurang ? '' : 'hidden'} pt-2.5 border-t border-rose-200 space-y-1.5 transition-all">
                                    <label class="text-[10px] font-bold text-rose-700 uppercase flex items-center justify-between">
                                        <span><i class="fa-solid fa-pen-to-square text-[9px] mr-1"></i> Catatan Revisi khusus ${docTitle}:</span>
                                    </label>
                                    <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                        ${defaultPresets.map(ps => `
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
        window.showLAAToast = showLAAToast;

        // Pop-up Modal PDF Viewer Preview & Quick Verification Handler
        let currentPreviewData = null;

        function openBerkasPreviewModal(nim, studentName, itemKode, itemNama, itemFileUrl, itemStatus, detailUrl) {
            currentPreviewData = { nim, studentName, itemKode, itemNama, itemFileUrl, itemStatus, detailUrl };

            const modal = document.getElementById('modalBerkasPreview');
            const titleEl = document.getElementById('previewModalTitle');
            const studentEl = document.getElementById('previewModalStudent');
            const iframeEl = document.getElementById('previewModalIframe');
            const btnOpenTab = document.getElementById('previewModalBtnTab');
            const btnDetail = document.getElementById('previewModalBtnDetail');
            const quickRejectBox = document.getElementById('quickRejectBox');

            if (!modal) return;

            if (quickRejectBox) quickRejectBox.classList.add('hidden');
            const inputCatatan = document.getElementById('inputQuickCatatan');
            if (inputCatatan) inputCatatan.value = '';

            titleEl.textContent = itemNama || 'Preview Dokumen';
            studentEl.textContent = `${studentName} (${nim})`;
            iframeEl.src = itemFileUrl;
            btnOpenTab.href = itemFileUrl;
            btnDetail.href = `${detailUrl}#doc_card_${itemKode}`;

            updateModalStatusBadge(itemStatus);

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function updateModalStatusBadge(st) {
            const statusBadgeEl = document.getElementById('previewModalStatus');
            if (!statusBadgeEl) return;

            let statusHTML = '';
            if (st === 'Valid' || st === 'Approved') {
                statusHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-circle-check text-emerald-500"></i> Valid</span>`;
            } else if (st === 'Invalid' || st === 'Rejected') {
                statusHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-circle-xmark text-rose-500"></i> Direvisi</span>`;
            } else {
                statusHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-clock text-amber-500"></i> Menunggu Verifikasi</span>`;
            }
            statusBadgeEl.innerHTML = statusHTML;
        }

        function toggleQuickRejectBox() {
            const box = document.getElementById('quickRejectBox');
            if (box) {
                box.classList.toggle('hidden');
                if (!box.classList.contains('hidden')) {
                    const input = document.getElementById('inputQuickCatatan');
                    if (input) input.focus();
                }
            }
        }

        function setQuickCatatan(text) {
            const input = document.getElementById('inputQuickCatatan');
            if (input) {
                input.value = text;
                input.focus();
            }
        }

        function submitQuickSingleVerify(status) {
            if (!currentPreviewData) return;

            const catatan = (document.getElementById('inputQuickCatatan')?.value || '').trim();
            if (status === 'Invalid' && !catatan) {
                alert('Harap masukkan atau pilih alasan/catatan revisi terlebih dahulu!');
                return;
            }

            const btnValid = document.getElementById('btnQuickValid');
            const btnReject = document.getElementById('btnSubmitQuickReject');
            const btnPending = document.getElementById('btnQuickPending');
            if (btnValid) btnValid.disabled = true;
            if (btnReject) btnReject.disabled = true;
            if (btnPending) btnPending.disabled = true;

            const formData = new FormData();
            formData.append('nim', currentPreviewData.nim);
            formData.append('kode_berkas', currentPreviewData.itemKode);
            formData.append('status', status);
            formData.append('catatan', catatan);

            fetch('<?= site_url("adminlayanan/ajax_update_single_berkas"); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async response => {
                const text = await response.text();
                let json;
                try {
                    json = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    if (text.includes('"success":true')) {
                        const matchStatus = text.match(/"status":"([^"]+)"/);
                        json = { success: true, message: 'Status berkas berhasil diperbarui!', status: matchStatus ? matchStatus[1] : status };
                    } else {
                        throw new Error('Gagal memproses respon server.');
                    }
                }
                return json;
            })
            .then(res => {
                if (btnValid) btnValid.disabled = false;
                if (btnReject) btnReject.disabled = false;
                if (btnPending) btnPending.disabled = false;

                if (res.success) {
                    currentPreviewData.itemStatus = res.status;
                    updateModalStatusBadge(res.status);

                    const box = document.getElementById('quickRejectBox');
                    if (box) box.classList.add('hidden');

                    if (typeof window.refreshLAATable === 'function') {
                        window.refreshLAATable();
                    } else if (typeof refreshLAATable === 'function') {
                        refreshLAATable();
                    }

                    if (typeof window.showLAAToast === 'function') {
                        window.showLAAToast(res.message || 'Status berkas berhasil diperbarui!');
                    } else if (typeof showLAAToast === 'function') {
                        showLAAToast(res.message || 'Status berkas berhasil diperbarui!');
                    }
                } else {
                    alert(res.message || 'Gagal memperbarui status berkas.');
                }
            })
            .catch(err => {
                if (btnValid) btnValid.disabled = false;
                if (btnReject) btnReject.disabled = false;
                if (btnPending) btnPending.disabled = false;
                console.error('Single verify error:', err);
                alert(err.message || 'Terjadi kesalahan jaringan.');
            });
        }

        function closeBerkasPreviewModal() {
            const modal = document.getElementById('modalBerkasPreview');
            const iframeEl = document.getElementById('previewModalIframe');
            if (modal) {
                modal.classList.add('hidden');
                if (iframeEl) iframeEl.src = 'about:blank';
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (window.activePreviews && window.activePreviews.length > 0) {
                    closeSinglePreview(window.activePreviews.length - 1);
                    return;
                }
                const lihatContainer = document.getElementById('lihatBerkasContainer');
                if (lihatContainer && lihatContainer.style.display !== 'none' && !lihatContainer.classList.contains('hidden')) {
                    closeLihatBerkasPanel();
                    return;
                }
                closeBerkasPreviewModal();
            }
        });

        // ==========================================
        // FLOATING NON-BLOCKING: LIHAT & PREVIEW BERKAS MULTI-CARD (ADMIN LAA)
        // ==========================================
        window.mhsDataMap = <?= json_encode(array_column($list_pengajuan ?: array(), null, 'nim')); ?>;
        window.SYARAT_BERKAS = <?= json_encode(!empty($syarat_berkas) ? $syarat_berkas : []); ?>;
        window.activeLihatBerkasNims = [];
        window.activePreviews = [];

        // ==========================================
        // STANDALONE ADMIN LAA DOCUMENT REVIEW MODAL
        // ==========================================
        window.currentLAANim = null;
        window.currentLAADocKey = null;
        window.currentLAADecision = 'Pending';
        window.laaStudentData = {};

        function openLAADocReview(nim, docKey) {
            if (!nim) return;
            nim = String(nim).trim();
            if (!docKey) docKey = 'ksm';

            window.currentLAANim = nim;

            // Fetch detail student data via AJAX endpoint
            fetch('<?= site_url("adminlayanan/get_batch_details"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ 'nims[]': nim })
            })
            .then(res => res.json())
            .then(res => {
                const list = res.data || res;
                if (list && list.length > 0) {
                    window.laaStudentData[nim] = list[0];
                    showLAADocModal(nim, docKey);
                } else {
                    alert('Data detail mahasiswa tidak ditemukan.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat detail berkas mahasiswa.');
            });
        }

        function showLAADocModal(nim, docKey) {
            const mhs = window.laaStudentData[nim];
            if (!mhs) return;

            const modal = document.getElementById('laaDocReviewModal');
            if (!modal) return;

            // Set student header info
            const fullName = (mhs.nama || (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : 'Mahasiswa')).trim();
            document.getElementById('laaDocStudentName').textContent = fullName;
            document.getElementById('laaDocStudentNim').textContent = mhs.nim;
            document.getElementById('laaDocJudulTa').textContent = mhs.judul || mhs.judul_1 || 'Judul Rencana Tugas Akhir';

            // Render Tabs
            const tabsContainer = document.getElementById('laaDocTabsContainer');
            if (tabsContainer && mhs.files) {
                let tabsHtml = '';
                Object.keys(mhs.files).forEach(k => {
                    const f = mhs.files[k];
                    const isActive = (k === docKey);
                    const st = f.status || 'Pending';
                    
                    let badgeClass = 'bg-slate-700 text-slate-300';
                    if (st === 'Valid' || st === 'Approved') badgeClass = 'bg-emerald-500 text-white';
                    else if (st === 'Invalid' || st === 'Rejected') badgeClass = 'bg-rose-500 text-white';

                    tabsHtml += `
                        <button type="button" onclick="switchLAADocTab('${k}')" 
                                class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition flex items-center gap-2 cursor-pointer whitespace-nowrap ${isActive ? 'bg-orange-600 text-white shadow-md ring-2 ring-orange-400' : 'bg-slate-900 text-slate-300 hover:bg-slate-700 hover:text-white'}">
                            <span>${f.title}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono uppercase ${badgeClass}">${st}</span>
                        </button>
                    `;
                });
                tabsContainer.innerHTML = tabsHtml;
            }

            switchLAADocTab(docKey);

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            document.body.style.overflow = '';
        }

        function switchLAADocTab(docKey) {
            const nim = window.currentLAANim;
            const mhs = window.laaStudentData[nim];
            if (!mhs || !mhs.files || !mhs.files[docKey]) return;

            window.currentLAADocKey = docKey;
            const f = mhs.files[docKey];

            // Update active tab buttons UI
            const tabsContainer = document.getElementById('laaDocTabsContainer');
            if (tabsContainer) {
                tabsContainer.querySelectorAll('button').forEach(btn => {
                    const isThis = btn.getAttribute('onclick').includes(`'${docKey}'`);
                    btn.className = `px-3.5 py-1.5 rounded-xl font-bold text-xs transition flex items-center gap-2 cursor-pointer whitespace-nowrap ${isThis ? 'bg-orange-600 text-white shadow-md ring-2 ring-orange-400' : 'bg-slate-900 text-slate-300 hover:bg-slate-700 hover:text-white'}`;
                });
            }

            // Set PDF viewer
            document.getElementById('laaDocFileTitle').textContent = f.title + ' (' + f.name + ')';
            const pdfUrl = f.url || '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
            document.getElementById('laaDocIframe').src = pdfUrl + '#toolbar=0&navpanes=0';
            document.getElementById('laaDocOpenNewTab').href = pdfUrl;

            // Set form item title
            document.getElementById('laaDocFormItemTitle').textContent = f.title;

            // Set decision & notes
            const currentStatus = f.status || 'Pending';
            setLAADecision(currentStatus);

            const inputNote = document.getElementById('inputLAANote');
            if (inputNote) inputNote.value = mhs.catatan_admin || '';
        }

        function setLAADecision(status) {
            const normStatus = (status === 'Valid' || status === 'Approved') ? 'Valid' : ((status === 'Invalid' || status === 'Rejected') ? 'Invalid' : 'Pending');
            window.currentLAADecision = normStatus;

            const btnValid = document.getElementById('btnLAADec_Valid');
            const btnInvalid = document.getElementById('btnLAADec_Invalid');
            const indicator = document.getElementById('laaSaveIndicator');

            if (normStatus === 'Valid') {
                if (btnValid) btnValid.className = 'px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-400';
                if (btnInvalid) btnInvalid.className = 'px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-rose-50 text-slate-700 border-slate-200';
                if (indicator) indicator.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Status: Valid (Setuju)';
            } else if (normStatus === 'Invalid') {
                if (btnValid) btnValid.className = 'px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-emerald-50 text-slate-700 border-slate-200';
                if (btnInvalid) btnInvalid.className = 'px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-rose-600 text-white border-rose-600 shadow-md ring-2 ring-rose-400';
                if (indicator) indicator.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-500 mr-1"></i>Status: Minta Revisi';
            } else {
                if (btnValid) btnValid.className = 'px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-emerald-50 text-slate-700 border-slate-200';
                if (btnInvalid) btnInvalid.className = 'px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-rose-50 text-slate-700 border-slate-200';
                if (indicator) indicator.innerHTML = '<i class="fa-solid fa-clock text-amber-500 mr-1"></i>Status: Belum Dicek';
            }
        }

        function appendLAANote(text) {
            const input = document.getElementById('inputLAANote');
            if (!input) return;
            if (input.value.trim().length > 0) {
                if (!input.value.includes(text)) {
                    input.value += ', ' + text;
                }
            } else {
                input.value = text;
            }
            input.focus();
        }

        function toggleLAAScrollIframe() {
            const iframe = document.getElementById('laaDocIframe');
            const btn = document.getElementById('btnLAAScroll');
            const icon = document.getElementById('iconLAAScroll');
            if (!iframe) return;

            const isLocked = iframe.classList.contains('pointer-events-none');
            if (isLocked) {
                iframe.classList.remove('pointer-events-none');
                if (btn) btn.className = 'px-2.5 py-1 rounded-lg bg-orange-100 border border-orange-300 text-orange-800 text-[11px] font-bold cursor-pointer flex items-center gap-1 shadow-2xs';
                if (icon) icon.className = 'fa-solid fa-hand text-[10px] text-orange-600';
                showLAADocToast('Mode scroll PDF aktif! Kursor dapat berinteraksi di dalam berkas.', 'success');
            } else {
                iframe.classList.add('pointer-events-none');
                if (btn) btn.className = 'px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 text-[11px] font-bold hover:bg-slate-50 transition cursor-pointer flex items-center gap-1';
                if (icon) icon.className = 'fa-solid fa-hand text-[10px]';
                showLAADocToast('Kursor PDF terkunci.', 'info');
            }
        }

        function saveLAAVerification() {
            const nim = window.currentLAANim;
            const kode_berkas = window.currentLAADocKey;
            const status = window.currentLAADecision;
            const inputNote = document.getElementById('inputLAANote');
            const catatan = inputNote ? inputNote.value.trim() : '';

            if (!nim || !kode_berkas) return;
            if (status === 'Pending') {
                alert('Silakan pilih status verifikasi (Valid atau Revisi) terlebih dahulu!');
                return;
            }
            if (status === 'Invalid' && !catatan) {
                alert('Harap masukkan alasan/catatan revisi untuk mahasiswa!');
                if (inputNote) inputNote.focus();
                return;
            }

            const btnSave = document.getElementById('btnSaveLAADoc');
            const origHtml = btnSave ? btnSave.innerHTML : '';
            if (btnSave) {
                btnSave.disabled = true;
                btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
            }

            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('kode_berkas', kode_berkas);
            formData.append('status', status);
            formData.append('catatan', catatan);

            fetch('<?= site_url("adminlayanan/ajax_update_single_berkas"); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = origHtml;
                }

                if (res.success) {
                    if (window.laaStudentData[nim] && window.laaStudentData[nim].files && window.laaStudentData[nim].files[kode_berkas]) {
                        window.laaStudentData[nim].files[kode_berkas].status = res.status || status;
                    }
                    
                    // Refresh tab UI
                    showLAADocModal(nim, kode_berkas);
                    showLAADocToast('Status berkas berhasil disimpan!', 'success');

                    // Refresh table row if refreshLAATable exists
                    if (typeof refreshLAATable === 'function') {
                        refreshLAATable();
                    }
                } else {
                    alert(res.message || 'Gagal menyimpan verifikasi berkas.');
                }
            })
            .catch(err => {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = origHtml;
                }
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            });
        }

        function closeLAADocModal() {
            const modal = document.getElementById('laaDocReviewModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            }
            document.body.style.overflow = '';
        }

        let laaToastTimer = null;
        function showLAADocToast(msg, type = 'success') {
            const toast = document.getElementById('laaDocToast');
            const msgEl = document.getElementById('laaDocToastMsg');
            const iconEl = document.getElementById('laaDocToastIcon');
            if (!toast || !msgEl) return;

            msgEl.textContent = msg;
            if (iconEl) {
                if (type === 'success') iconEl.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>';
                else iconEl.innerHTML = '<i class="fa-solid fa-circle-info text-amber-400 text-sm"></i>';
            }

            toast.classList.remove('-translate-y-16', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');

            if (laaToastTimer) clearTimeout(laaToastTimer);
            laaToastTimer = setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('-translate-y-16', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        function updateMhsDataDocStatus(nim, kode_berkas, status) {
            if (!nim || !kode_berkas) return;
            const nimStr = String(nim).trim();
            const nimNum = Number(nimStr);
            const keyStr = String(kode_berkas).trim();

            const targets = [
                window.mhsDataMap ? window.mhsDataMap[nimStr] : null,
                window.mhsDataMap ? window.mhsDataMap[nimNum] : null,
                window.laaStudentData ? window.laaStudentData[nimStr] : null,
                window.laaStudentData ? window.laaStudentData[nimNum] : null,
            ];

            targets.forEach(mhs => {
                if (!mhs) return;

                if (!mhs.files) mhs.files = {};
                if (!mhs.files[keyStr]) mhs.files[keyStr] = {};
                mhs.files[keyStr].status = status;

                if (mhs.berkas_summary && Array.isArray(mhs.berkas_summary.items)) {
                    const item = mhs.berkas_summary.items.find(i => String(i.kode).trim() === keyStr);
                    if (item) {
                        item.status = status;
                    }
                }

                mhs[`status_file_${keyStr}`] = status;
                mhs[`status_${keyStr}`] = status;
            });
        }

        function quickVerifyFloatingDoc(nim, kode_berkas, status, customCatatan = '') {
            if (!nim || !kode_berkas) return;
            let catatan = customCatatan || '';

            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('kode_berkas', kode_berkas);
            formData.append('status', status);
            formData.append('catatan', catatan);

            fetch('<?= site_url("adminlayanan/ajax_update_single_berkas"); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const normStatus = res.status || status;
                    
                    updateMhsDataDocStatus(nim, kode_berkas, normStatus);

                    // Tutup / hilangkan tab preview berkas yang baru saja di-aksi
                    const pIdx = (window.activePreviews || []).findIndex(p => String(p.nim).trim() === String(nim).trim() && String(p.docKey).trim() === String(kode_berkas).trim());
                    if (pIdx > -1) {
                        window.activePreviews.splice(pIdx, 1);
                    }

                    if (typeof showLAAToast === 'function') {
                        const msg = (normStatus === 'Valid') ? 'Dokumen berhasil disetujui (Valid)! Tab pratinjau ditutup.' : 'Catatan revisi berhasil dikirim! Tab pratinjau ditutup.';
                        showLAAToast(msg, normStatus === 'Valid');
                    }

                    // Close the floating preview card if present in activePreviews
                    if (window.activePreviews && window.activePreviews.length > 0) {
                        const pNim = String(nim).trim();
                        const pDocKey = String(kode_berkas).trim();
                        const existingIdx = window.activePreviews.findIndex(p => String(p.nim).trim() === pNim && String(p.docKey).trim() === pDocKey);
                        if (existingIdx > -1) {
                            closeSinglePreview(existingIdx);
                        } else {
                            refreshLihatBerkasView();
                        }
                    } else {
                        refreshLihatBerkasView();
                    }

                    if (typeof refreshLAATable === 'function') {
                        refreshLAATable();
                    }
                } else {
                    alert(res.message || 'Gagal memperbarui status berkas.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function toggleFloatingRevisiBox(nim, docKey) {
            const box = document.getElementById(`floatingRevisiBox_${nim}_${docKey}`);
            if (!box) return;
            const isHidden = box.classList.contains('hidden');
            if (isHidden) {
                box.classList.remove('hidden');
                const input = document.getElementById(`inputFloatingRevisiNote_${nim}_${docKey}`);
                if (input) input.focus();
            } else {
                box.classList.add('hidden');
            }
        }

        function appendFloatingRevisiChip(nim, docKey, text) {
            const input = document.getElementById(`inputFloatingRevisiNote_${nim}_${docKey}`);
            if (!input) return;
            if (input.value.trim().length > 0) {
                if (!input.value.includes(text)) {
                    input.value += ', ' + text;
                }
            } else {
                input.value = text;
            }
            input.focus();
        }

        function submitFloatingRevisi(nim, docKey) {
            const input = document.getElementById(`inputFloatingRevisiNote_${nim}_${docKey}`);
            const catatan = input ? input.value.trim() : '';

            if (!catatan) {
                alert('Harap masukkan alasan / catatan revisi untuk mahasiswa!');
                if (input) input.focus();
                return;
            }

            quickVerifyFloatingDoc(nim, docKey, 'Invalid', catatan);
        }

        window.quickVerifyFloatingDoc = quickVerifyFloatingDoc;
        window.toggleFloatingRevisiBox = toggleFloatingRevisiBox;
        window.appendFloatingRevisiChip = appendFloatingRevisiChip;
        window.submitFloatingRevisi = submitFloatingRevisi;

        // Global functions mapping
        window.openLAADocReview = openLAADocReview;
        window.openQuickDocReview = openStudentBerkasPreview;
        window.openStudentBerkasPreview = openStudentBerkasPreview;
        window.closeLAADocModal = closeLAADocModal;
        window.switchLAADocTab = switchLAADocTab;
        window.setLAADecision = setLAADecision;
        window.appendLAANote = appendLAANote;
        window.toggleLAAScrollIframe = toggleLAAScrollIframe;
        window.saveLAAVerification = saveLAAVerification;

        function showLAAToast(msg, isSuccess = true) {
            const toast = document.getElementById('dwToast');
            const toastMsg = document.getElementById('dwToastMsg');
            const toastIcon = document.getElementById('dwToastIcon');
            if (!toast || !toastMsg) return;

            toastMsg.textContent = msg;
            if (toastIcon) {
                toastIcon.className = isSuccess 
                    ? 'w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold'
                    : 'w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-bold';
            }

            toast.classList.remove('translate-y-[-150%]', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-[-150%]', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }
        window.showLAAToast = showLAAToast;

        function resolveDocPdfUrl(filename, existingUrl) {
            const fallbackPdf = '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
            if (existingUrl && (existingUrl.startsWith('http://') || existingUrl.startsWith('https://'))) {
                return existingUrl;
            }
            if (!filename) return fallbackPdf;
            filename = String(filename).trim();
            if (filename.startsWith('http://') || filename.startsWith('https://')) return filename;
            if (filename.startsWith('uploads/')) return '<?= base_url(); ?>' + filename;
            return '<?= base_url("uploads/persyaratan_ta/"); ?>' + filename;
        }

        function refreshLihatBerkasView() {
            const oldPos = {};
            document.querySelectorAll('.student-card-item').forEach(el => {
                const r = el.getBoundingClientRect();
                if (r.width > 0 && r.height > 0) {
                    oldPos[el.id] = { left: r.left, top: r.top };
                }
            });

            updateLihatBerkasLayout();
            renderAllLihatBerkasCards();
            renderAllPreviewCards();
            updateTableButtonHighlights();

            document.querySelectorAll('.student-card-item').forEach(el => {
                const old = oldPos[el.id];
                if (old) {
                    const cur = el.getBoundingClientRect();
                    const dx = old.left - cur.left;
                    const dy = old.top - cur.top;

                    if (Math.abs(dx) > 2 || Math.abs(dy) > 2) {
                        el.animate([
                            { transform: `translate3d(${dx}px, ${dy}px, 0)`, zIndex: 30, boxShadow: '0 20px 40px -10px rgba(0, 0, 0, 0.28)' },
                            { transform: 'translate3d(0, 0, 0)', zIndex: 30, boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)' }
                        ], {
                            duration: 650,
                            easing: 'cubic-bezier(0.25, 1, 0.4, 1)'
                        });
                    }
                }
            });
        }

        function getMhsFullName(mhs, nim) {
            if (!mhs) return 'Mahasiswa ' + (nim || '');
            if (mhs.full_name) return mhs.full_name.trim();
            if (mhs.nama_depan) return ((mhs.nama_depan || '') + ' ' + (mhs.nama_belakang || '')).trim();
            if (mhs.nama) return mhs.nama.trim();
            return 'Mahasiswa ' + (nim || '');
        }

        function getMhsDocInfo(mhs, docKey, nim) {
            const fallbackPdf = '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
            if (!mhs) {
                const fallbackName = `${docKey}_${nim}.pdf`;
                return { filename: fallbackName, url: fallbackPdf, status: 'Pending' };
            }

            let filename = '';
            let url = '';
            let status = null;

            if (mhs.files && mhs.files[docKey]) {
                filename = mhs.files[docKey].name || '';
                url = mhs.files[docKey].url || '';
                if (mhs.files[docKey].status) {
                    status = mhs.files[docKey].status;
                }
            }

            if (!status && mhs.berkas_summary && mhs.berkas_summary.items) {
                const item = mhs.berkas_summary.items.find(i => String(i.kode).trim() === String(docKey).trim());
                if (item) {
                    if (!filename) filename = item.file_name || '';
                    if (!url) url = item.file_url || '';
                    if (item.status) status = item.status;
                }
            }

            if (!filename) {
                filename = mhs[`file_${docKey}`] || mhs[`file_name_${docKey}`] || '';
            }
            if (!status) {
                status = mhs[`status_file_${docKey}`] || mhs[`status_${docKey}`] || 'Pending';
            }

            if (status === 'Approved') status = 'Valid';
            if (status === 'Rejected') status = 'Invalid';

            if (!filename) filename = `${docKey}_${nim}.pdf`;
            if (!url) url = resolveDocPdfUrl(filename, url);

            return { filename, url, status: status || 'Pending' };
        }

        function toggleLihatBerkasPanel(nim) {
            if (!nim) return;
            nim = String(nim).trim();

            const activeNims = (window.activeLihatBerkasNims || []).map(n => String(n).trim());
            const idx = activeNims.indexOf(nim);
            if (idx > -1) {
                removeStudentFromLihatBerkas(nim);
                return;
            }

            window.activeLihatBerkasNims.push(nim);

            if (!window.mhsDataMap || (!window.mhsDataMap[nim] && !window.mhsDataMap[Number(nim)])) {
                fetch('<?= site_url("adminlayanan/get_batch_details"); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({ 'nims[]': nim })
                })
                .then(res => res.json())
                .then(res => {
                    const data = res.data || res;
                    if (data && data.length > 0) {
                        if (!window.mhsDataMap) window.mhsDataMap = {};
                        window.mhsDataMap[nim] = data[0];
                        showLihatBerkasContainer();
                        refreshLihatBerkasView();
                    } else {
                        alert('Data mahasiswa tidak ditemukan.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal memuat berkas mahasiswa.');
                });
                return;
            }

            showLihatBerkasContainer();
            refreshLihatBerkasView();
        }

        function showLihatBerkasContainer() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) {
                container.style.display = 'flex';
                container.classList.remove('hidden');
                container.classList.add('flex');
            }
        }

        function updateLihatBerkasLayout() {
            const container = document.getElementById('lihatBerkasContainer');
            const wrapper = document.getElementById('wrapperDaftarMhs');
            if (!container || !wrapper) return;

            container.classList.remove('justify-center', 'justify-start');
            container.classList.add('justify-end');

            const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;
            if (isPreviewActive) {
                wrapper.className = 'flex flex-col gap-3 max-h-[92vh] overflow-y-auto pr-1.5 shrink-0 w-[280px] sm:w-[320px]';
            } else {
                wrapper.className = 'flex flex-row items-center gap-3 max-h-[92vh] overflow-x-auto p-1 shrink-0';
            }
        }

        function renderAllLihatBerkasCards() {
            const wrapper = document.getElementById('wrapperDaftarMhs');
            if (!wrapper) return;

            if (!window.activeLihatBerkasNims || window.activeLihatBerkasNims.length === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const totalActive = window.activeLihatBerkasNims.length;
            const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;
            const cardWidthClass = isPreviewActive ? 'w-full' : (totalActive > 1 ? 'w-[350px] sm:w-[370px]' : 'w-[390px] sm:w-[410px]');

            const docList = (window.SYARAT_BERKAS && window.SYARAT_BERKAS.length > 0)
                ? window.SYARAT_BERKAS.map((sb, i) => {
                    const k = sb.kode_berkas;
                    const iconMap = {
                        ksm: 'fa-solid fa-file-lines',
                        transkrip: 'fa-solid fa-file-spreadsheet',
                        pernyataan: 'fa-solid fa-file-contract',
                        bebas_lab: 'fa-solid fa-building-circle-check'
                    };
                    return {
                        key: k,
                        title: `${i + 1}. ${sb.nama_berkas}`,
                        icon: iconMap[k] || 'fa-solid fa-file-pdf'
                    };
                })
                : [
                    { key: 'ksm', title: '1. KSM', icon: 'fa-solid fa-file-lines' },
                    { key: 'transkrip', title: '2. Transkrip Nilai', icon: 'fa-solid fa-file-spreadsheet' },
                    { key: 'pernyataan', title: '3. Surat Pernyataan', icon: 'fa-solid fa-file-contract' },
                    { key: 'bebas_lab', title: '4. Bebas Lab & Perpus', icon: 'fa-solid fa-building-circle-check' }
                ];

            let headerSummaryBar = '';
            if (totalActive > 1 && isPreviewActive) {
                headerSummaryBar = `
                    <div class="bg-slate-900 text-white px-3.5 py-2 rounded-2xl flex items-center justify-between shadow-lg border border-slate-800 shrink-0 pointer-events-auto w-full animate-bar-in">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-orange-400 text-xs"></i>
                            <span class="text-xs font-bold">${totalActive} Mahasiswa Terpilih</span>
                        </div>
                        <button type="button" onclick="closeLihatBerkasPanel()" class="text-[11px] text-slate-300 hover:text-rose-400 font-bold transition cursor-pointer">
                            Tutup Semua
                        </button>
                    </div>
                `;
            }

            const cardsHtml = window.activeLihatBerkasNims.map((nim, index) => {
                const nimStr = String(nim).trim();
                const mhs = window.mhsDataMap ? (window.mhsDataMap[nimStr] || window.mhsDataMap[Number(nimStr)] || {}) : {};
                const fullName = getMhsFullName(mhs, nimStr);
                const cardNum = index + 1;

                const itemsHtml = docList.map(doc => {
                    const docInfo = getMhsDocInfo(mhs, doc.key, nimStr);
                    const rawFilename = docInfo.filename;
                    const pdfUrl = docInfo.url;
                    const status = docInfo.status;
                    const isCurrentlyPreviewed = window.activePreviews && window.activePreviews.some(p => String(p.nim).trim() === nimStr && String(p.docKey).trim() === String(doc.key).trim());

                    let statusBadge = '';
                    if (status === 'Valid' || status === 'Approved') {
                        statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Valid</span>';
                    } else if (status === 'Invalid' || status === 'Rejected') {
                        statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Revisi</span>';
                    } else {
                        statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Pending</span>';
                    }

                    const activeCardBorder = isCurrentlyPreviewed 
                        ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' 
                        : 'border-slate-200 bg-white hover:border-slate-300';
                    
                    const previewBtnStyle = isCurrentlyPreviewed 
                        ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' 
                        : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';

                    return `
                        <div class="p-2 px-2.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex items-center justify-between gap-2 ${activeCardBorder}">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="${doc.icon} text-orange-500 text-xs shrink-0"></i>
                                    <span class="font-bold text-slate-800 text-[11px] truncate">${doc.title}</span>
                                    ${statusBadge}
                                </div>
                                <p class="text-[10px] font-mono text-slate-400 truncate mt-0.5" title="${rawFilename}">
                                    <i class="fa-solid fa-file-pdf text-rose-500 mr-1 text-[9px]"></i>${rawFilename}
                                </p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" 
                                        onclick="previewBerkasItem('${nimStr}', '${doc.key}')" 
                                        class="w-7 h-7 rounded-lg border text-xs font-bold transition flex items-center justify-center cursor-pointer active:scale-95 ${previewBtnStyle}" 
                                        title="${isCurrentlyPreviewed ? 'Tutup Pratinjau Ini' : 'Pratinjau Berkas'}">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                                <a href="${pdfUrl}" 
                                   download="${rawFilename}" 
                                   target="_blank" 
                                   class="w-7 h-7 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition flex items-center justify-center cursor-pointer shadow-2xs active:scale-95" 
                                   title="Unduh Berkas">
                                    <i class="fa-solid fa-download text-xs"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                    <div id="cardMhs_${nimStr}" class="student-card-item pointer-events-auto ${cardWidthClass} bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0">
                        <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                                    ${cardNum}
                                </div>
                                <div class="min-w-0 flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[150px] sm:max-w-[180px]">${fullName}</h4>
                                    <span class="px-1.5 py-0.2 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${nimStr}</span>
                                </div>
                            </div>
                            <button type="button" onclick="removeStudentFromLihatBerkas('${nimStr}')" class="w-6 h-6 rounded-md bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-[11px] font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="p-2.5 space-y-1.5 bg-slate-50/50 overflow-y-auto">
                            ${itemsHtml}
                        </div>
                    </div>
                `;
            }).join('');

            wrapper.innerHTML = headerSummaryBar + cardsHtml;
        }

        function previewBerkasItem(nim, docKey) {
            if (!nim || !docKey) return;
            nim = String(nim).trim();
            docKey = String(docKey).trim();

            const mhs = window.mhsDataMap ? (window.mhsDataMap[nim] || window.mhsDataMap[Number(nim)]) : null;
            if (!mhs) return;

            if (!window.activePreviews) window.activePreviews = [];

            const existingIdx = window.activePreviews.findIndex(p => String(p.nim).trim() === nim && String(p.docKey).trim() === docKey);
            if (existingIdx > -1) {
                closeSinglePreview(existingIdx);
                return;
            }

            if (window.activePreviews.length >= 5) {
                window.activePreviews.shift();
            }
            window.activePreviews.push({ nim, docKey });

            refreshLihatBerkasView();

            setTimeout(() => {
                const previewWrapper = document.getElementById('wrapperPreviewBerkas');
                if (previewWrapper) {
                    previewWrapper.scrollLeft = previewWrapper.scrollWidth;
                }
            }, 100);
        }

        function renderAllPreviewCards() {
            const wrapper = document.getElementById('wrapperPreviewBerkas');
            if (!wrapper) return;

            if (!window.activePreviews || window.activePreviews.length === 0) {
                wrapper.innerHTML = '';
                wrapper.classList.add('hidden');
                return;
            }

            wrapper.classList.remove('hidden');
            wrapper.className = 'flex items-center gap-3 shrink-0 max-w-[65vw] sm:max-w-[70vw] overflow-x-auto p-1.5 scroll-smooth';

            const totalPreviews = window.activePreviews.length;
            const panelWidthClass = totalPreviews >= 3 ? 'w-[340px] sm:w-[370px] lg:w-[400px] shrink-0' : (totalPreviews > 1 ? 'w-[390px] sm:w-[430px] shrink-0' : 'w-[460px] sm:w-[500px] shrink-0');

            const docList = (window.SYARAT_BERKAS && window.SYARAT_BERKAS.length > 0)
                ? window.SYARAT_BERKAS.map((sb, i) => ({
                    key: sb.kode_berkas,
                    title: `${i + 1}. ${sb.nama_berkas}`
                }))
                : [
                    { key: 'ksm', title: '1. KSM' },
                    { key: 'transkrip', title: '2. Transkrip Nilai' },
                    { key: 'pernyataan', title: '3. Surat Pernyataan' },
                    { key: 'bebas_lab', title: '4. Bebas Lab & Perpus' }
                ];

            const activeCardIds = window.activePreviews.map(p => `previewCard_${p.nim}_${p.docKey}`);
            Array.from(wrapper.children).forEach(child => {
                if (!activeCardIds.includes(child.id)) {
                    child.remove();
                }
            });

            window.activePreviews.forEach((p, index) => {
                const pNim = String(p.nim).trim();
                const pDocKey = String(p.docKey).trim();
                const cardId = `previewCard_${pNim}_${pDocKey}`;
                let cardEl = document.getElementById(cardId);
                const slotNum = index + 1;

                if (cardEl) {
                    cardEl.className = `preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 transition-all duration-300 ${panelWidthClass}`;
                    const badgeEl = cardEl.querySelector('.preview-slot-badge');
                    if (badgeEl) {
                        badgeEl.innerHTML = totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>';
                    }
                    const closeBtn = cardEl.querySelector('.preview-close-btn');
                    if (closeBtn) {
                        closeBtn.setAttribute('onclick', `closeSinglePreview(${index})`);
                    }
                } else {
                    const mhs = window.mhsDataMap ? (window.mhsDataMap[pNim] || window.mhsDataMap[Number(pNim)] || {}) : {};
                    const doc = docList.find(d => String(d.key).trim() === pDocKey) || { title: pDocKey };
                    const docInfo = getMhsDocInfo(mhs, pDocKey, pNim);
                    const rawFilename = docInfo.filename;
                    const pdfUrl = docInfo.url;
                    const fullName = getMhsFullName(mhs, pNim);

                    const div = document.createElement('div');
                    div.id = cardId;
                    div.className = `preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 transition-all duration-300 ${panelWidthClass}`;
                    div.innerHTML = `
                        <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="preview-slot-badge w-7 h-7 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                    ${totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>'}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[190px] sm:max-w-[240px]">${doc.title}</h4>
                                    <p class="text-[10px] text-slate-300 font-medium truncate">${fullName} · <span class="font-mono text-slate-400">${rawFilename}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="togglePreviewIframeInteraction('${pNim}', '${pDocKey}')" id="btnPreviewInteract_${pNim}_${pDocKey}" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Kursor Terkunci (Normal). Klik jika ingin mengaktifkan scroll di dalam berkas">
                                    <i class="fa-solid fa-arrow-pointer text-[10px]" id="iconPreviewInteract_${pNim}_${pDocKey}"></i>
                                </button>
                                <a href="${pdfUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka Layar Penuh di Tab Baru">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                                <button type="button" onclick="closeSinglePreview(${index})" class="preview-close-btn w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer ml-0.5" title="Tutup Pratinjau Ini">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <div class="h-[410px] sm:h-[470px] bg-slate-200 relative border-b border-slate-200 overflow-hidden cursor-default select-none">
                            <iframe id="iframePreviewBerkas_${pNim}_${pDocKey}" src="${pdfUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 pointer-events-none" title="Pratinjau Dokumen PDF"></iframe>
                        </div>

                        <div class="p-2.5 px-3 bg-slate-50 border-t border-slate-200 flex flex-col gap-2 shrink-0">
                            <div class="flex items-center justify-between text-xs gap-2">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick="quickVerifyFloatingDoc('${pNim}', '${pDocKey}', 'Valid')" 
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 cursor-pointer"
                                            title="Setujui dokumen ini (Valid)">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Setuju</span>
                                    </button>
                                    <button type="button" 
                                            onclick="toggleFloatingRevisiBox('${pNim}', '${pDocKey}')" 
                                            class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 cursor-pointer"
                                            title="Minta revisi dokumen ini">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        <span>Revisi</span>
                                    </button>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="${pdfUrl}" download="${rawFilename}" target="_blank" class="w-7 h-7 rounded-lg bg-white hover:bg-slate-200 text-slate-600 border border-slate-200 font-bold transition flex items-center justify-center cursor-pointer shadow-2xs" title="Unduh File">
                                        <i class="fa-solid fa-download text-[10px]"></i>
                                    </a>
                                    <button type="button" onclick="closeSinglePreview(${index})" class="w-7 h-7 rounded-lg bg-white hover:bg-rose-50 text-slate-500 hover:text-rose-600 border border-slate-200 font-bold transition flex items-center justify-center cursor-pointer shadow-2xs" title="Tutup Pratinjau Ini">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Inline Revision Notes Form (Expandable) -->
                            <div id="floatingRevisiBox_${pNim}_${pDocKey}" class="hidden pt-2 border-t border-slate-200/80 flex flex-col gap-2 animate-fade-in">
                                <div class="flex items-center justify-between text-[10px] font-bold text-slate-600">
                                    <span class="text-rose-600 flex items-center gap-1"><i class="fa-solid fa-comment-dots"></i> Catatan Revisi:</span>
                                    <div class="flex gap-1 flex-wrap">
                                        <button type="button" onclick="appendFloatingRevisiChip('${pNim}', '${pDocKey}', 'File buram / kurang jelas')" class="px-1.5 py-0.5 bg-slate-200 hover:bg-rose-100 hover:text-rose-800 rounded text-[9px] transition cursor-pointer">+ Buram</button>
                                        <button type="button" onclick="appendFloatingRevisiChip('${pNim}', '${pDocKey}', 'Tanpa TTD / stempel')" class="px-1.5 py-0.5 bg-slate-200 hover:bg-rose-100 hover:text-rose-800 rounded text-[9px] transition cursor-pointer">+ TTD/Stempel</button>
                                        <button type="button" onclick="appendFloatingRevisiChip('${pNim}', '${pDocKey}', 'Format tidak sesuai')" class="px-1.5 py-0.5 bg-slate-200 hover:bg-rose-100 hover:text-rose-800 rounded text-[9px] transition cursor-pointer">+ Format Salah</button>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" id="inputFloatingRevisiNote_${pNim}_${pDocKey}" placeholder="Tuliskan catatan revisi di sini..." class="flex-grow text-[11px] px-2.5 py-1.5 rounded-lg border border-rose-300 focus:ring-1 focus:ring-rose-500 focus:outline-none bg-rose-50/40">
                                    <button type="button" onclick="submitFloatingRevisi('${pNim}', '${pDocKey}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] rounded-lg shadow-2xs transition active:scale-95 whitespace-nowrap cursor-pointer flex items-center gap-1">
                                        <i class="fa-solid fa-paper-plane text-[9px]"></i> Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    wrapper.appendChild(div);
                    div.animate([
                        { opacity: 0, transform: 'translateX(35px) scale(0.97)' },
                        { opacity: 1, transform: 'translateX(0) scale(1)' }
                    ], {
                        duration: 500,
                        easing: 'cubic-bezier(0.25, 1, 0.4, 1)'
                    });
                }
            });
        }

        function togglePreviewIframeInteraction(nim, docKey) {
            const iframe = document.getElementById(`iframePreviewBerkas_${nim}_${docKey}`);
            const btn = document.getElementById(`btnPreviewInteract_${nim}_${docKey}`);
            const icon = document.getElementById(`iconPreviewInteract_${nim}_${docKey}`);
            if (!iframe) return;

            const isLocked = iframe.classList.contains('pointer-events-none');
            if (isLocked) {
                iframe.classList.remove('pointer-events-none');
                if (btn) {
                    btn.className = 'w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center text-xs transition cursor-pointer shadow-2xs';
                    btn.title = 'Mode Scroll Aktif. Klik untuk kunci kursor kembali.';
                }
                if (icon) icon.className = 'fa-solid fa-hand text-xs';
            } else {
                iframe.classList.add('pointer-events-none');
                if (btn) {
                    btn.className = 'w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer';
                    btn.title = 'Kursor Normal (Terkunci). Klik jika ingin mengaktifkan scroll di dalam berkas.';
                }
                if (icon) icon.className = 'fa-solid fa-arrow-pointer text-xs';
            }
        }

        function closeSinglePreview(index) {
            if (index < 0 || index >= window.activePreviews.length) return;
            window.activePreviews.splice(index, 1);
            refreshLihatBerkasView();
        }

        function closeAllPreviews() {
            window.activePreviews = [];
            refreshLihatBerkasView();
        }

        function removeStudentFromLihatBerkas(nim) {
            const nimStr = String(nim).trim();
            window.activeLihatBerkasNims = (window.activeLihatBerkasNims || []).filter(n => String(n).trim() !== nimStr);
            window.activePreviews = (window.activePreviews || []).filter(p => String(p.nim).trim() !== nimStr);

            if (window.activeLihatBerkasNims.length === 0) {
                closeLihatBerkasPanel();
            } else {
                refreshLihatBerkasView();
            }
            updateTableButtonHighlights();
        }

        function closeLihatBerkasPanel() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) {
                container.style.display = 'none';
                container.classList.add('hidden');
                container.classList.remove('flex');
            }
            window.activePreviews = [];
            window.activeLihatBerkasNims = [];

            const wrapper = document.getElementById('wrapperDaftarMhs');
            if (wrapper) wrapper.innerHTML = '';

            const previewWrapper = document.getElementById('wrapperPreviewBerkas');
            if (previewWrapper) {
                previewWrapper.innerHTML = '';
                previewWrapper.classList.add('hidden');
            }

            updateTableButtonHighlights();
        }

        function updateTableButtonHighlights() {
            const activeNims = (window.activeLihatBerkasNims || []).map(n => String(n).trim());
            document.querySelectorAll('[id^="btn_lihat_berkas_"]').forEach(btn => {
                const nim = String(btn.id.replace('btn_lihat_berkas_', '')).trim();
                const isActive = activeNims.includes(nim);
                if (isActive) {
                    btn.className = 'btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer ring-2 ring-orange-400 scale-105 shadow-md';
                    btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i> Melihat';
                } else {
                    btn.className = 'btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-2xs';
                    btn.innerHTML = '<i class="fa-solid fa-folder-open text-xs"></i> Lihat Berkas';
                }
            });
        }

        async function openStudentBerkasPreview(nim, docKey) {
            if (!nim) return;
            nim = String(nim).trim();

            if (!window.mhsDataMap) window.mhsDataMap = {};
            if (!window.activeLihatBerkasNims) window.activeLihatBerkasNims = [];
            if (!window.activePreviews) window.activePreviews = [];

            if (!window.mhsDataMap[nim]) {
                try {
                    const res = await fetch('<?= site_url("adminlayanan/get_batch_details"); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                        body: new URLSearchParams({ 'nims[]': nim })
                    }).then(r => r.json());

                    const data = res.data || res;
                    if (data && data.length > 0) {
                        window.mhsDataMap[nim] = data[0];
                    } else {
                        alert('Data mahasiswa tidak ditemukan.');
                        return;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Gagal memuat berkas mahasiswa.');
                    return;
                }
            }

            const activeNims = window.activeLihatBerkasNims.map(n => String(n).trim());
            if (!activeNims.includes(nim)) {
                window.activeLihatBerkasNims.push(nim);
            }

            showLihatBerkasContainer();
            refreshLihatBerkasView();

            if (docKey) {
                docKey = String(docKey).trim();
                const isPreviewed = window.activePreviews.some(p => String(p.nim).trim() === nim && String(p.docKey).trim() === docKey);
                if (!isPreviewed) {
                    previewBerkasItem(nim, docKey);
                }
            }
        }

        window.openStudentBerkasPreview = openStudentBerkasPreview;
        window.toggleLihatBerkasPanel = toggleLihatBerkasPanel;
        window.renderAllLihatBerkasCards = renderAllLihatBerkasCards;
        window.previewBerkasItem = previewBerkasItem;
        window.renderAllPreviewCards = renderAllPreviewCards;
        window.togglePreviewIframeInteraction = togglePreviewIframeInteraction;
        window.closeSinglePreview = closeSinglePreview;
        window.closeAllPreviews = closeAllPreviews;
        window.removeStudentFromLihatBerkas = removeStudentFromLihatBerkas;
        window.closeLihatBerkasPanel = closeLihatBerkasPanel;
        window.updateTableButtonHighlights = updateTableButtonHighlights;
    </script>

    <!-- Dedicated Admin LAA Document Review Side Drawer (Non-blocking Right Side Panel) -->
    <div id="laaDocReviewModal" style="display: none;" class="fixed inset-0 z-[65] pointer-events-none hidden flex justify-end p-2 sm:p-4 overflow-hidden">
        <div class="bg-white rounded-3xl w-full max-w-2xl lg:max-w-3xl xl:max-w-4xl h-full max-h-[96vh] flex flex-col overflow-hidden shadow-2xl border border-slate-300 pointer-events-auto transition-all duration-300 relative my-auto border-l-4 border-l-orange-500">
            
            <!-- In-Modal Aligned Notification Toast -->
            <div id="laaDocToast" class="absolute top-4 left-1/2 -translate-x-1/2 z-[70] transform transition-all duration-300 -translate-y-16 opacity-0 pointer-events-none">
                <div class="bg-slate-900/95 text-white px-4 py-2 rounded-2xl shadow-2xl flex items-center gap-2.5 border border-slate-700 backdrop-blur-md">
                    <div class="w-5 h-5 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" id="laaDocToastIcon">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                    </div>
                    <span class="text-xs font-bold whitespace-nowrap" id="laaDocToastMsg">Pemberitahuan</span>
                </div>
            </div>

            <!-- Modal Header -->
            <div class="p-4 px-6 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-4 shrink-0">
                <!-- Student Info & Title -->
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-base shrink-0 shadow-2xs">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-bold text-white tracking-tight" id="laaDocStudentName">Nama Mahasiswa</h3>
                            <span class="px-2 py-0.5 rounded-md bg-white/10 text-orange-300 font-mono text-[11px] font-bold" id="laaDocStudentNim">NIM</span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate mt-0.5" id="laaDocJudulTa">Judul Tugas Akhir</p>
                    </div>
                </div>

                <!-- Close Modal Button -->
                <button type="button" onclick="closeLAADocModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Document Tabs Selector -->
            <div class="px-6 py-2.5 bg-slate-800 border-b border-slate-700 flex items-center gap-2 overflow-x-auto" id="laaDocTabsContainer">
                <!-- Dynamic Tabs Rendered Here -->
            </div>

            <!-- Main Body: PDF Viewer (Left) & Verification Form (Right) -->
            <div class="flex-1 flex flex-col md:flex-row overflow-hidden bg-slate-50">
                <!-- PDF Viewer Area -->
                <div class="flex-1 flex flex-col p-4 border-b md:border-b-0 md:border-r border-slate-200 overflow-hidden">
                    <div class="flex items-center justify-between gap-2 mb-3 shrink-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <i class="fa-solid fa-file-pdf text-rose-500 text-sm"></i>
                            <span class="text-xs font-bold text-slate-800 truncate" id="laaDocFileTitle">Dokumen PDF</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" id="btnLAAScroll" onclick="toggleLAAScrollIframe()" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 text-[11px] font-bold hover:bg-slate-50 transition cursor-pointer flex items-center gap-1">
                                <i id="iconLAAScroll" class="fa-solid fa-hand text-[10px]"></i> Scroll PDF
                            </button>
                            <a id="laaDocOpenNewTab" href="#" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 text-[11px] font-bold hover:bg-slate-50 transition cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Tab Baru
                            </a>
                        </div>
                    </div>
                    <div class="flex-1 rounded-2xl border border-slate-300 bg-white overflow-hidden shadow-inner relative min-h-[380px]">
                        <iframe id="laaDocIframe" src="about:blank" class="w-full h-full border-0 pointer-events-none"></iframe>
                    </div>
                </div>

                <!-- Decision & Feedback Form Area -->
                <div class="w-full md:w-80 lg:w-96 p-5 flex flex-col justify-between bg-white shrink-0 overflow-y-auto">
                    <div class="space-y-4">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Verifikasi Dokumen</span>
                            <h4 class="text-sm font-bold text-slate-800" id="laaDocFormItemTitle">Judul Dokumen</h4>
                        </div>

                        <!-- Status Choice Buttons -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Keputusan Verifikasi:</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" id="btnLAADec_Valid" onclick="setLAADecision('Valid')" class="px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-emerald-50 text-slate-700 border-slate-200">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Valid (Setuju)
                                </button>
                                <button type="button" id="btnLAADec_Invalid" onclick="setLAADecision('Invalid')" class="px-3 py-2.5 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-rose-50 text-slate-700 border-slate-200">
                                    <i class="fa-solid fa-circle-xmark text-rose-600"></i> Minta Revisi
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1" id="laaSaveIndicator">
                                <i class="fa-solid fa-clock text-amber-500 mr-1"></i>Status: Belum Dicek
                            </p>
                        </div>

                        <!-- Preset Catatan Chips -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Catatan Cepat (Presisi):</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="appendLAANote('File buram / tidak terbaca')" class="px-2.5 py-1 bg-slate-100 hover:bg-orange-50 hover:text-orange-700 text-slate-600 rounded-lg text-[11px] font-medium border border-slate-200 transition-colors cursor-pointer">+ File Buram</button>
                                <button type="button" onclick="appendLAANote('Tanpa tanda tangan / stempel')" class="px-2.5 py-1 bg-slate-100 hover:bg-orange-50 hover:text-orange-700 text-slate-600 rounded-lg text-[11px] font-medium border border-slate-200 transition-colors cursor-pointer">+ Tanpa TTD/Stempel</button>
                                <button type="button" onclick="appendLAANote('Format / template tidak sesuai')" class="px-2.5 py-1 bg-slate-100 hover:bg-orange-50 hover:text-orange-700 text-slate-600 rounded-lg text-[11px] font-medium border border-slate-200 transition-colors cursor-pointer">+ Format Salah</button>
                                <button type="button" onclick="appendLAANote('Masa berlaku berkas kadaluwarsa')" class="px-2.5 py-1 bg-slate-100 hover:bg-orange-50 hover:text-orange-700 text-slate-600 rounded-lg text-[11px] font-medium border border-slate-200 transition-colors cursor-pointer">+ Kadaluwarsa</button>
                            </div>
                        </div>

                        <!-- Catatan Textarea -->
                        <div class="space-y-1.5">
                            <label for="inputLAANote" class="text-xs font-bold text-slate-700">Catatan untuk Mahasiswa:</label>
                            <textarea id="inputLAANote" rows="3" placeholder="Tuliskan catatan revisi atau kelengkapan berkas di sini..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none resize-none bg-slate-50/50"></textarea>
                        </div>
                    </div>

                    <!-- Save & Close Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2 mt-4">
                        <button type="button" onclick="closeLAADocModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" id="btnSaveLAADoc" onclick="saveLAAVerification()" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Non-Blocking Container: Lihat & Pratinjau Berkas (Multiple Floating Tabs - Right Side Docked) -->
    <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-50 flex items-center justify-end p-3 sm:p-5 gap-4 sm:gap-5 overflow-x-auto" style="display: none;">
        
        <!-- Wrapper Kartu Mahasiswa (Kanan-Kiri saat tanpa preview, Atas-Bawah di Kiri Ujung saat preview aktif) -->
        <div id="wrapperDaftarMhs" class="flex flex-row items-center gap-4 shrink-0 max-h-[92vh] overflow-y-auto">
            <!-- Kartu mahasiswa dirender dinamis di sini -->
        </div>

        <!-- Wrapper Pratinjau Dokumen Berkas (Dapat Menampilkan Hingga 2 Panel Pratinjau Berdampingan) -->
        <div id="wrapperPreviewBerkas" class="flex items-center gap-4 shrink-0 hidden">
            <!-- 1 atau 2 Panel Pratinjau dirender dinamis di sini -->
        </div>

    </div>

    <!-- Toast Notification -->
    <div id="dwToast" class="fixed top-6 right-6 z-50 transform transition-all duration-300 translate-y-[-150%] opacity-0 pointer-events-none">
        <div class="bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700">
            <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold" id="dwToastIcon">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="text-xs font-bold" id="dwToastMsg">Pemberitahuan</span>
        </div>
    </div>

</body>
</html>
