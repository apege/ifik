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
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0" id="kkFilterContainer">
                    <button type="button" onclick="switchKkKK('all')" id="btnKK_all" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $selected_kk === 'all' ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Semua KK
                    </button>
                    <?php foreach($all_kk as $kk): ?>
                        <button type="button" onclick="switchKkKK('<?= $kk['id']; ?>')" id="btnKK_<?= $kk['id']; ?>" 
                           class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= (string)$selected_kk === (string)$kk['id'] ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                            <?= htmlspecialchars($kk['kode_kk']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5" id="statusFilterContainerKK">
                    <button type="button" onclick="switchStatusKK('all')" id="btnStatusKK_all" 
                       class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all cursor-pointer <?= $filter_status === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Semua Status
                    </button>
                    <button type="button" onclick="switchStatusKK('Pending')" id="btnStatusKK_Pending" 
                       class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all cursor-pointer <?= $filter_status === 'Pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Siap Review
                    </button>
                    <button type="button" onclick="switchStatusKK('Approved')" id="btnStatusKK_Approved" 
                       class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all cursor-pointer <?= $filter_status === 'Approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Disetujui
                    </button>
                </div>
            </div>
        </div>

        <!-- Unified Multi-Category Search Bar (+ 1/4 Standalone Add Button) -->
        <div class="card-custom p-4 mb-6 relative">
            <form action="<?= site_url('ketuakk'); ?>" method="GET" id="formSearchKK" class="relative search-pill-container">
                <input type="hidden" name="kk" value="<?= htmlspecialchars($selected_kk); ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                <input type="hidden" name="cat" id="mainCategorySelectKK" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                
                <div class="flex items-center gap-2.5">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1">
                        <!-- Main Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container shrink-0">
                            <button type="button" onclick="toggleKKCustomDropdown('main-cat-kk', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-brand-600 focus:outline-none">
                                <span id="label-filter-main-cat-kk" class="truncate max-w-[130px]">Cari Kata Kunci</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-cat-kk"></i>
                            </button>
                            <div id="menu-filter-main-cat-kk" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                <div onclick="selectKKMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectKKMainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectKKMainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectKKMainCategory('judul', '📖 Judul Usulan TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>📖 Judul Usulan TA</span></div>
                                <div onclick="selectKKMainCategory('kk', '🎯 Kelompok Keahlian', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🎯 Kelompok Keahlian</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Text Container -->
                        <div id="mainValueContainerKK" class="flex-1 flex items-center min-w-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchKK" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>" 
                                   placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." 
                                   class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                            
                            <button type="button" id="btnClearSearchKK" onclick="clearKKSearch()" class="<?= empty($search) ? 'hidden' : ''; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1 cursor-pointer shrink-0">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </div>

                        <!-- Tombol Cari -->
                        <button type="submit" id="btnSubmitSearchKK" class="px-3.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnKK" onclick="toggleKKMultiFilter(event)" class="btn-standalone-add shrink-0" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgeKK" class="badge-standalone-count">1/4</span>
                    </button>
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
                        <tbody id="tableBodyKK" class="divide-y divide-slate-100 bg-white">
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
                    <select id="selectPerPageKK" onchange="changeKKPerPage(this.value)" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-600 shadow-xs cursor-pointer">
                        <option value="5" <?= $per_page == 5 ? 'selected' : ''; ?>>5 per halaman</option>
                        <option value="10" <?= $per_page == 10 ? 'selected' : ''; ?>>10 per halaman</option>
                        <option value="20" <?= $per_page == 20 ? 'selected' : ''; ?>>20 per halaman</option>
                    </select>
                    <span class="text-slate-300">|</span>
                    <span id="txtShowingCountKK">
                        Menampilkan <strong><?= $total_rows > 0 ? (($page - 1) * $per_page + 1) : 0; ?></strong> - <strong><?= min($page * $per_page, $total_rows); ?></strong> dari <strong><?= $total_rows; ?></strong> mahasiswa
                    </span>
                </div>

                <div id="kkPaginationControls">
                    <?php if($total_pages > 1): ?>
                        <div class="flex items-center gap-1 flex-wrap">
                            <?php if($page > 1): ?>
                                <button type="button" onclick="changeKKPage(<?= $page - 1; ?>)" 
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
                                    <button type="button" onclick="changeKKPage(<?= $p; ?>)" 
                                       class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all cursor-pointer <?= $p == $page ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'; ?>">
                                        <?= $p; ?>
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if($page < $total_pages): ?>
                                <button type="button" onclick="changeKKPage(<?= $page + 1; ?>)" 
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

    <!-- REAL-TIME AJAX JAVASCRIPT SYSTEM FOR KETUA KK -->
    <script>
        let currentKKState = {
            kk: '<?= $selected_kk; ?>',
            status: '<?= $filter_status; ?>',
            search: '<?= addslashes($search ?? ''); ?>',
            cat: '<?= addslashes($cat ?? 'query'); ?>',
            page: 1,
            per_page: <?= $per_page; ?>
        };

        function toggleKKCustomDropdown(id, e) {
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

        function selectKKMainCategory(catKey, catLabel, el) {
            const hiddenCat = document.getElementById('mainCategorySelectKK');
            const labelEl = document.getElementById('label-filter-main-cat-kk');
            if (hiddenCat) hiddenCat.value = catKey;
            if (labelEl) labelEl.textContent = catLabel;

            document.querySelectorAll('#menu-filter-main-cat-kk .dropdown-item').forEach(i => {
                i.classList.remove('bg-orange-50', 'text-brand-600');
                i.classList.add('text-slate-700');
            });
            if (el) {
                el.classList.add('bg-orange-50', 'text-brand-600');
                el.classList.remove('text-slate-700');
            }

            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        }

        let kkSearchTimer = null;

        function refreshKKTable(isSilent = false) {
            const url = `<?= site_url("ketuakk/ajax_get_table"); ?>?kk=${encodeURIComponent(currentKKState.kk)}&status=${encodeURIComponent(currentKKState.status)}&q=${encodeURIComponent(currentKKState.search)}&per_page=${currentKKState.perPage}&page=${currentKKState.page}`;

            const tbody = document.getElementById('tableBodyKK');
            if (!tbody) return;

            if (!isSilent) {
                tbody.style.opacity = '0.4';
            }

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    tbody.style.opacity = '1';

                    if (!res.success) return;

                    // 1. Render Rows
                    if (!res.list || res.list.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="py-14 text-center text-slate-400">
                                    <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-3 block"></i>
                                    Tidak ada data mahasiswa ditemukan.
                                </td>
                            </tr>
                        `;
                    } else {
                        let html = '';
                        res.list.forEach(row => {
                            let isWaliApp = (row.status_approval_wali === 'Approved');
                            let isAdminApp = (row.status_approval_admin === 'Approved');
                            let isKoorApp = (row.status_approval_koor === 'Approved');
                            let isKkApp = (row.status_approval_kk === 'Approved');
                            let isKkRej = (row.status_approval_kk === 'Rejected');

                            let canBulkApprove = (row.is_ready_for_kk && !isKkApp);

                            let cbTd = canBulkApprove ? `
                                <input type="checkbox" name="nim_list[]" value="${row.nim}" class="kk-checkbox w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                            ` : `
                                <input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 text-slate-300 opacity-40 cursor-not-allowed">
                            `;

                            let prereqBadge = `
                                <div class="inline-flex items-center gap-1 bg-slate-50 px-3 py-1 rounded-xl border border-slate-200 text-[10px] font-mono shadow-xs">
                                    <span class="${isWaliApp ? 'text-emerald-600 font-extrabold' : 'text-slate-400'}">Wali</span>
                                    <span class="text-slate-300">›</span>
                                    <span class="${isAdminApp ? 'text-emerald-600 font-extrabold' : 'text-slate-400'}">LAA</span>
                                    <span class="text-slate-300">›</span>
                                    <span class="${isKoorApp ? 'text-emerald-600 font-extrabold' : 'text-slate-400'}">Koor</span>
                                </div>
                            `;

                            let statusBadge = '';
                            if (isKkApp) {
                                statusBadge = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-circle-check text-emerald-500"></i> Disetujui KK</span>`;
                            } else if (isKkRej) {
                                statusBadge = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-circle-xmark text-rose-500"></i> Ditolak KK</span>`;
                            } else {
                                statusBadge = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-clock text-amber-600"></i> Menunggu KK</span>`;
                            }

                            let unlockedBadge = isKkApp ? `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold border border-emerald-200">
                                    <i class="fa-solid fa-lock-open text-emerald-600"></i> Unlocked
                                </span>` : `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-semibold border border-slate-200">
                                    <i class="fa-solid fa-lock text-slate-400"></i> Terkunci
                                </span>`;

                            let actionBtn = row.is_ready_for_kk ? `
                                <a href="${row.detail_url}" class="btn-3d-kinetic">
                                    <span class="bg"></span>
                                    <span class="wrap">
                                        <span class="content">
                                            <i class="fa-solid fa-shield-halved icon-action"></i>
                                            <span class="char state-1"><span>R</span><span>e</span><span>v</span><span>i</span><span>e</span><span>w</span></span>
                                        </span>
                                    </span>
                                </a>` : `
                                <span title="Prasyarat Dosen Wali, Admin LAA, atau Koordinator TA belum disetujui" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl text-xs font-semibold cursor-not-allowed opacity-75">
                                    <i class="fa-solid fa-lock text-slate-400"></i> Locked
                                </span>`;

                            html += `
                                <tr class="hover:bg-orange-50/40 transition-colors">
                                    <td class="py-4 px-3 text-center w-10">${cbTd}</td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-orange-100 border border-orange-200 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                                ${row.first_char}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs">${row.full_name}</div>
                                                <div class="text-[11px] text-slate-400 font-mono">${row.nim}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 text-brand-700 rounded-lg text-[10px] font-bold border border-orange-200">
                                            <i class="fa-solid fa-shapes text-[9px]"></i>
                                            ${row.kode_kk}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 min-w-[240px] max-w-xs">
                                        <div class="font-semibold text-slate-800 line-clamp-2 text-xs leading-relaxed" title="${row.judul_1}">
                                            ${row.judul_1}
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">${prereqBadge}</td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">${statusBadge}</td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">${unlockedBadge}</td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">${actionBtn}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                        rebindKKCheckboxes();
                    }

                    // 2. Update Pagination Text & Controls
                    currentKKState.page = res.page;
                    currentKKState.total_pages = res.total_pages;

                    const txtCount = document.getElementById('txtShowingCountKK');
                    if (txtCount) {
                        const start = res.total_rows > 0 ? ((res.page - 1) * res.per_page + 1) : 0;
                        const end = Math.min(res.page * res.per_page, res.total_rows);
                        txtCount.innerHTML = `Menampilkan <strong>${start}</strong> - <strong>${end}</strong> dari <strong>${res.total_rows}</strong> mahasiswa`;
                    }

                    renderKKPaginationControls(res.page, res.total_pages);
                })
                .catch(err => {
                    if (tbody) tbody.style.opacity = '1';
                    console.error('AJAX Error:', err);
                });
        }

        function renderKKPaginationControls(page, totalPages) {
            const container = document.getElementById('kkPaginationControls');
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div class="flex items-center gap-1 flex-wrap">';
            
            if (page > 1) {
                html += `<button type="button" onclick="changeKKPage(${page - 1})" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer"><i class="fa-solid fa-chevron-left text-[10px]"></i> Prev</button>`;
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
                    html += `<button type="button" onclick="changeKKPage(${p})" class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all bg-brand-600 text-white shadow-md cursor-pointer">${p}</button>`;
                } else {
                    html += `<button type="button" onclick="changeKKPage(${p})" class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center transition-all bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 cursor-pointer">${p}</button>`;
                }
            });

            if (page < totalPages) {
                html += `<button type="button" onclick="changeKKPage(${page + 1})" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-orange-50 hover:text-brand-600 transition-all flex items-center gap-1 shadow-xs cursor-pointer">Next <i class="fa-solid fa-chevron-right text-[10px]"></i></button>`;
            } else {
                html += `<span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-400 cursor-not-allowed flex items-center gap-1 opacity-60">Next <i class="fa-solid fa-chevron-right text-[10px]"></i></span>`;
            }

            html += '</div>';
            container.innerHTML = html;
        }

        function switchKkKK(kkVal) {
            currentKKState.kk = kkVal;
            currentKKState.page = 1;

            const container = document.getElementById('kkFilterContainer');
            if (container) {
                const btns = container.querySelectorAll('button');
                btns.forEach(b => {
                    const id = b.id.replace('btnKK_', '');
                    if (id === String(kkVal)) {
                        b.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer bg-brand-600 text-white shadow-md';
                    } else {
                        b.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                    }
                });
            }

            refreshKKTable();
        }

        function switchStatusKK(statusVal) {
            currentKKState.status = statusVal;
            currentKKState.page = 1;

            const container = document.getElementById('statusFilterContainerKK');
            if (container) {
                const btns = container.querySelectorAll('button');
                btns.forEach(b => {
                    const id = b.id.replace('btnStatusKK_', '');
                    if (id === statusVal) {
                        b.className = 'px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all cursor-pointer ' + 
                                       (id === 'Pending' ? 'bg-amber-600 text-white shadow-md' : (id === 'Approved' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-white shadow-md'));
                    } else {
                        b.className = 'px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                    }
                });
            }

            refreshKKTable();
        }

        function changeKKPerPage(perPageVal) {
            currentKKState.perPage = parseInt(perPageVal) || 5;
            currentKKState.page = 1;
            refreshKKTable();
        }

        function changeKKPage(pageNum) {
            currentKKState.page = parseInt(pageNum) || 1;
            refreshKKTable();
        }

        function clearKKSearch() {
            const input = document.getElementById('inputSearchKK');
            const btnClear = document.getElementById('btnClearSearchKK');
            if (input) input.value = '';
            if (btnClear) btnClear.classList.add('hidden');

            currentKKState.search = '';
            currentKKState.page = 1;
            refreshKKTable();
        }

        function rebindKKCheckboxes() {
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
                selectAllKK.checked = false;
                selectAllKK.onchange = function() {
                    kkCheckboxes.forEach(cb => {
                        if (!cb.disabled) cb.checked = this.checked;
                    });
                    updateBulkToolbar();
                };
            }

            kkCheckboxes.forEach(cb => {
                cb.onchange = function() {
                    updateBulkToolbar();
                    if (!this.checked && selectAllKK) {
                        selectAllKK.checked = false;
                    }
                };
            });

            updateBulkToolbar();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputSearch = document.getElementById('inputSearchKK');
            const btnClear = document.getElementById('btnClearSearchKK');
            const formSearchKK = document.getElementById('formSearchKK');

            if (inputSearch) {
                inputSearch.addEventListener('input', function() {
                    const q = this.value.trim();
                    if (btnClear) {
                        if (q.length > 0) btnClear.classList.remove('hidden');
                        else btnClear.classList.add('hidden');
                    }
                });
            }

            if (formSearchKK) {
                formSearchKK.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const q = inputSearch ? inputSearch.value.trim() : '';
                    const catEl = document.getElementById('mainCategorySelectKK');
                    currentKKState.search = q;
                    currentKKState.cat = catEl ? catEl.value : 'query';
                    currentKKState.page = 1;
                    refreshKKTable();
                });
            }

            // 3. Form Bulk Approval Submit Handler via AJAX
            const formBulk = document.getElementById('formBulkApproval');
            if (formBulk) {
                formBulk.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const checkedCbs = document.querySelectorAll('.kk-checkbox:checked');
                    if (checkedCbs.length === 0) return;

                    const formData = new FormData(formBulk);
                    fetch(formBulk.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(res => {
                        const bulkToolbar = document.getElementById('bulkToolbar');
                        if (bulkToolbar) bulkToolbar.classList.add('hidden');
                        
                        const selectAllKK = document.getElementById('selectAllKK');
                        if (selectAllKK) selectAllKK.checked = false;

                        refreshKKTable();
                        showKKToast(res.message || 'Persetujuan massal Ketua KK berhasil!');
                    })
                    .catch(err => {
                        console.error('Bulk approval error:', err);
                        formBulk.submit();
                    });
                });
            }
        });

        function showKKToast(msg) {
            let toast = document.getElementById('kkToastNotification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'kkToastNotification';
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
