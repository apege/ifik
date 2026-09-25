<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Status Peserta TA — Admin LAA'; ?> - IFIK</title>

    <!-- Tailwind CSS CDN -->
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
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 3px 14px;
            height: 46px;
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1), 
                        box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), 
                        background-color 0.25s ease, 
                        transform 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider {
            width: 1px;
            height: 22px;
            background: #e2e8f0;
            margin: 0 8px;
        }
        .autocomplete-box {
            max-height: 320px;
            overflow-y: auto;
            border-radius: 18px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2), 0 8px 24px -4px rgba(234, 88, 12, 0.15);
        }
        .autocomplete-item-row {
            padding: 10px 16px;
            transition: all 0.18s ease;
            cursor: pointer;
        }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav {
            background-color: #fff7ed;
            color: #ea580c;
        }
        .autocomplete-item-row mark {
            background: #ffedd5;
            color: #ea580c;
            font-weight: 800;
            border-radius: 4px;
            padding: 0 3px;
        }

        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 16px;
            padding: 6px 14px;
            height: 46px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
        }
        .btn-standalone-add:hover {
            background: #ffedd5;
            border-color: #fdba74;
            transform: scale(1.02);
        }
        .badge-standalone-count {
            background: #ea580c;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 1.5px 8px;
            border-radius: 99px;
        }
        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 14px;
            color: #e11d48;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .btn-remove-row:hover {
            background: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
            transform: scale(1.05);
        }
        .extra-rows-card {
            display: none;
            position: relative;
            margin-top: 12px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .extra-rows-card.open {
            display: block !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Dedicated Admin Layanan (LAA) Curved Sidebar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>
    <?php $this->load->view('partials/app_navbar', [
        'user_role_id'      => $this->session->userdata('role_id') ?? 5,
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Unit Layanan FIK',
        'user_display_sub'  => 'Status Peserta Tugas Akhir'
    ]); ?>

    <!-- Main Content Container (Balanced padding on mobile & desktop) -->
    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Pendaftaran TA</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Status Peserta TA</span>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                            <i class="bi bi-bar-chart-steps text-lg sm:text-xl"></i>
                        </span>
                        <span>Status Peserta Tugas Akhir</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Monitoring komprehensif tahapan bimbingan dan status verifikasi berkas persyaratan TA mahasiswa.
                    </p>
                </div>

                <!-- Dual View Toggle Tabs -->
                <div class="flex items-center gap-1 sm:gap-1.5 p-1 sm:p-1.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-x-auto self-start lg:self-auto">
                    <a href="<?= site_url('adminlayanan/status_peserta_ta?tab=bimbingan' . ($search ? '&q='.urlencode($search) : '') . ($filter_stage !== 'all' ? '&stage='.$filter_stage : '') . ($cat ? '&cat='.$cat : '')); ?>"
                       class="px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap <?= $active_tab === 'bimbingan' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <span>Status Bimbingan</span>
                    </a>
                    <a href="<?= site_url('adminlayanan/status_peserta_ta?tab=file_ta' . ($search ? '&q='.urlencode($search) : '') . ($filter_stage !== 'all' ? '&stage='.$filter_stage : '') . ($cat ? '&cat='.$cat : '')); ?>"
                       class="px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap <?= $active_tab === 'file_ta' ? 'bg-orange-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                        <i class="bi bi-file-earmark-zip-fill"></i>
                        <span>Status File TA</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs sm:text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-check-circle-fill text-lg text-emerald-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-octagon-fill text-lg text-rose-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards: Preview 1, Preview 2, Preview 3, Tahap Sidang -->
        <?php
            $countPreview1 = 0;
            $countPreview2 = 0;
            $countPreview3 = 0;
            $countSidang   = 0;
            if (!empty($list_peserta)) {
                foreach ($list_peserta as $r) {
                    $stg = strtolower($r['tahapan_display'] ?? ($r['current_stage'] ?? ''));
                    if (strpos($stg, 'preview 3') !== false || strpos($stg, 'preview3') !== false || strpos($stg, 'pra-sidang') !== false) {
                        $countPreview3++;
                    } elseif (strpos($stg, 'preview 2') !== false || strpos($stg, 'preview2') !== false) {
                        $countPreview2++;
                    } elseif (strpos($stg, 'preview 1') !== false || strpos($stg, 'preview1') !== false) {
                        $countPreview1++;
                    } elseif (strpos($stg, 'sidang') !== false || strpos($stg, 'lulus') !== false || strpos($stg, 'selesai') !== false) {
                        $countSidang++;
                    } else {
                        $countPreview1++;
                    }
                }
            }
        ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">

            <!-- Card 1: Preview 1 -->
            <a href="<?= site_url('adminlayanan/status_peserta_ta?stage=preview1' . ($search ? '&q='.urlencode($search) : '') . ($cat ? '&cat='.$cat : '')); ?>" 
               class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 hover:border-orange-300 shadow-xs sm:shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-orange-500 uppercase tracking-wider block truncate">Preview 1</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block group-hover:text-orange-600 transition-colors"><?= $countPreview1; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Tahap Proposal TA</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2 group-hover:scale-110 transition-transform">
                    <i class="bi bi-journal-text"></i>
                </div>
            </a>

            <!-- Card 2: Preview 2 -->
            <a href="<?= site_url('adminlayanan/status_peserta_ta?stage=preview2' . ($search ? '&q='.urlencode($search) : '') . ($cat ? '&cat='.$cat : '')); ?>" 
               class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 hover:border-amber-300 shadow-xs sm:shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-amber-500 uppercase tracking-wider block truncate">Preview 2</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block group-hover:text-amber-600 transition-colors"><?= $countPreview2; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Tahap Progres 50%</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2 group-hover:scale-110 transition-transform">
                    <i class="bi bi-bar-chart-steps"></i>
                </div>
            </a>

            <!-- Card 3: Preview 3 -->
            <a href="<?= site_url('adminlayanan/status_peserta_ta?stage=preview3' . ($search ? '&q='.urlencode($search) : '') . ($cat ? '&cat='.$cat : '')); ?>" 
               class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 hover:border-indigo-300 shadow-xs sm:shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-indigo-500 uppercase tracking-wider block truncate">Preview 3</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block group-hover:text-indigo-600 transition-colors"><?= $countPreview3; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Tahap Pra-Sidang</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2 group-hover:scale-110 transition-transform">
                    <i class="bi bi-shield-check"></i>
                </div>
            </a>

            <!-- Card 4: Tahap Sidang -->
            <a href="<?= site_url('adminlayanan/status_peserta_ta?stage=sidang' . ($search ? '&q='.urlencode($search) : '') . ($cat ? '&cat='.$cat : '')); ?>" 
               class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 hover:border-emerald-300 shadow-xs sm:shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-500 uppercase tracking-wider block truncate">Tahap Sidang</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block group-hover:text-emerald-600 transition-colors"><?= $countSidang; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Siap / Daftar Sidang</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2 group-hover:scale-110 transition-transform">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </a>

        </div>

        <!-- Unified Multi-Search Bar (Up to 4 Categories, Manual Enter/Button Search, Autocomplete) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6 relative">
            <form action="<?= site_url('adminlayanan/status_peserta_ta'); ?>" method="GET" id="formSearchPeserta" onsubmit="executePesertaSearch(event)" class="relative">
                <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab); ?>">
                <input type="hidden" name="stage" value="<?= htmlspecialchars($filter_stage); ?>">
                <input type="hidden" name="cat" id="mainCategorySelectPeserta" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 sm:gap-3">
                    
                    <!-- Filter Stage Dropdown Select -->
                    <div class="flex items-center gap-2 shrink-0 bg-slate-50 px-3.5 py-2.5 rounded-2xl border border-slate-200">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tahapan:</span>
                        <select name="stage" onchange="this.form.submit()"
                                class="bg-transparent border-none text-xs font-bold text-slate-800 focus:outline-hidden cursor-pointer">
                            <option value="all" <?= $filter_stage === 'all' ? 'selected' : ''; ?>>Semua Tahapan</option>
                            <option value="preview1" <?= $filter_stage === 'preview1' ? 'selected' : ''; ?>>Preview 1</option>
                            <option value="preview2" <?= $filter_stage === 'preview2' ? 'selected' : ''; ?>>Preview 2</option>
                            <option value="preview3" <?= $filter_stage === 'preview3' ? 'selected' : ''; ?>>Preview 3</option>
                            <option value="sidang" <?= $filter_stage === 'sidang' ? 'selected' : ''; ?>>Tahap Sidang</option>
                        </select>
                    </div>

                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <!-- Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container shrink-0" id="dropdownCatWrapperPeserta">
                            <?php
                                $catLabels = [
                                    'query' => '🔍 Kata Kunci (Semua)',
                                    'nama'  => '🏷️ Nama Mahasiswa',
                                    'nim'   => '🆔 NIM Mahasiswa',
                                    'dosen' => '👨‍🏫 Dosen Wali',
                                    'prodi' => '🎯 Program Studi & KK'
                                ];
                                $curLabel = $catLabels[$cat ?? 'query'] ?? '🔍 Kata Kunci (Semua)';
                            ?>
                            <button type="button" onclick="togglePesertaCatDropdown(event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-hidden">
                                <span id="labelCatPeserta" class="truncate max-w-[120px] sm:max-w-[170px]"><?= $curLabel; ?></span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrowCatPeserta"></i>
                            </button>
                            <div id="menuCatPeserta" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                <div onclick="selectPesertaMainCategory('query', '🔍 Kata Kunci (Semua)')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? 'query') === 'query' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🔍 Kata Kunci (Semua)</span>
                                </div>
                                <div onclick="selectPesertaMainCategory('nama', '🏷️ Nama Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'nama' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🏷️ Nama Mahasiswa</span>
                                </div>
                                <div onclick="selectPesertaMainCategory('nim', '🆔 NIM Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'nim' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🆔 NIM Mahasiswa</span>
                                </div>
                                <div onclick="selectPesertaMainCategory('dosen', '👨‍🏫 Dosen Wali')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'dosen' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>👨‍🏫 Dosen Wali</span>
                                </div>
                                <div onclick="selectPesertaMainCategory('prodi', '🎯 Program Studi & KK')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'prodi' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🎯 Program Studi & KK</span>
                                </div>
                            </div>
                        </div>

                        <div class="unified-divider shrink-0"></div>

                        <!-- Search Text Input (Manual Enter / Click Cari, Autocomplete Triggered) -->
                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchPeserta" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Ketik kata kunci lalu tekan Enter..." 
                                   oninput="handlePesertaAutocomplete(this.value)"
                                   onkeydown="handlePesertaInputKey(event)"
                                   class="w-full text-xs font-semibold bg-transparent border-none focus:outline-hidden text-slate-800 placeholder:text-slate-400 placeholder:font-normal min-w-0">
                            
                            <button type="button" id="btnClearSearchPeserta" onclick="clearPesertaSearch()" class="<?= empty($search) ? 'opacity-0 scale-75 pointer-events-none' : 'opacity-100 scale-100'; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-1 cursor-pointer shrink-0 transition-all duration-200 transform" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark text-sm"></i>
                            </button>
                        </div>

                        <!-- Submit Cari Button -->
                        <button type="button" onclick="executePesertaSearch(event)" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer active:scale-95 shrink-0 ml-1">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnPeserta" onclick="togglePesertaMultiFilter(event)" class="btn-standalone-add shrink-0 justify-center w-full lg:w-auto" title="Tambah Kriteria Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgePeserta" class="badge-standalone-count">1/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown Box -->
                <div id="autocompletePeserta" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsPeserta" class="divide-y divide-slate-100 text-xs"></div>
                </div>

                <!-- Extra Filter Rows Container (Maks 4 total) -->
                <div id="extraRowsCardPeserta" class="extra-rows-card space-y-2.5">
                    <div id="additionalFilterRowsContainerPeserta" class="space-y-2.5">
                        <!-- Dynamic extra rows added here -->
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian peserta TA. Tekan Enter / Cari.</span>
                        <button type="button" onclick="resetPesertaMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors cursor-pointer self-start sm:self-auto">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Main Card Container: Desktop Table & Mobile Cards -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">

            <!-- TAB 1: STATUS BIMBINGAN TABLE -->
            <?php if ($active_tab === 'bimbingan'): ?>
                
                <!-- Desktop View (>= md) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="pesertaTable">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-5 text-center w-10">No</th>
                                <th class="py-3.5 px-5">Mahasiswa</th>
                                <th class="py-3.5 px-5">Prodi / Konsentrasi</th>
                                <th class="py-3.5 px-5">Dosen Wali</th>
                                <th class="py-3.5 px-5 text-center">Status Wali</th>
                                <th class="py-3.5 px-5 text-center">Tahapan</th>
                                <th class="py-3.5 px-5">Jalur TA</th>
                                <th class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm" id="pesertaTableBody">
                            <?php if (empty($list_peserta)): ?>
                                <tr id="emptyRow">
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 rounded-3xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                            <i class="bi bi-journal-x"></i>
                                        </div>
                                        <p class="font-bold text-slate-700">Belum Ada Data Peserta Bimbingan TA</p>
                                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                            Data mahasiswa yang mendaftar dan mengikuti tahapan bimbingan TA akan muncul di sini.
                                        </p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_peserta as $idx => $r): ?>
                                    <tr class="peserta-item peserta-row hover:bg-slate-50/70 transition-colors"
                                        data-nim="<?= strtolower($r['nim']); ?>"
                                        data-nama="<?= strtolower($r['nama_lengkap']); ?>"
                                        data-dosen="<?= strtolower($r['nama_dosen_wali'] ?? ''); ?>"
                                        data-prodi="<?= strtolower(($r['prodi'] ?? '') . ' ' . ($r['konsentrasi_dkv'] ?? '')); ?>">
                                        <!-- No -->
                                        <td class="py-4 px-5 text-center font-bold text-slate-400 text-xs row-num">
                                            <?= $idx + 1; ?>
                                        </td>

                                        <!-- Mahasiswa -->
                                        <td class="py-4 px-5">
                                            <span class="font-bold text-slate-800 block text-xs truncate max-w-[170px]" title="<?= htmlspecialchars($r['nama_lengkap']); ?>">
                                                <?= htmlspecialchars($r['nama_lengkap']); ?>
                                            </span>
                                            <span class="font-mono text-[11px] text-orange-600 font-bold">
                                                <?= htmlspecialchars($r['nim']); ?>
                                            </span>
                                        </td>

                                        <!-- Prodi & Konsentrasi -->
                                        <td class="py-4 px-5">
                                            <div class="text-xs font-bold text-slate-800 truncate max-w-[130px]">
                                                <?= htmlspecialchars($r['prodi'] ?? 'DKV'); ?>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-medium truncate max-w-[130px]">
                                                <?= htmlspecialchars($r['konsentrasi_dkv'] ?? 'ADVERTISING'); ?>
                                            </div>
                                        </td>

                                        <!-- Dosen Wali -->
                                        <td class="py-4 px-5">
                                            <span class="font-semibold text-slate-700 text-xs block truncate max-w-[150px]" title="<?= htmlspecialchars($r['nama_dosen_wali']); ?>">
                                                <?= htmlspecialchars($r['nama_dosen_wali']); ?>
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                <?= date('d M Y', strtotime($r['updated_at'] ?? 'now')); ?>
                                            </span>
                                        </td>

                                        <!-- Status Approval Wali -->
                                        <td class="py-4 px-5 text-center">
                                            <?php if (($r['status_approval_wali'] ?? '') === 'Approved'): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    <i class="bi bi-check-circle-fill text-emerald-600 text-[10px]"></i> Disetujui
                                                </span>
                                            <?php elseif (($r['status_approval_wali'] ?? '') === 'Rejected'): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                    <i class="bi bi-x-circle-fill text-rose-600 text-[10px]"></i> Ditolak
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    <i class="bi bi-hourglass-split text-amber-600 text-[10px]"></i> Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Tahapan -->
                                        <td class="py-4 px-5 text-center">
                                            <?php
                                            $td = $r['tahapan_display'] ?? 'Preview 1';
                                            $td_stg = strtolower($td);
                                            if (strpos($td_stg, 'preview 3') !== false || strpos($td_stg, 'preview3') !== false) {
                                                $badge_tahap = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                            } elseif (strpos($td_stg, 'preview 2') !== false || strpos($td_stg, 'preview2') !== false) {
                                                $badge_tahap = 'bg-amber-50 text-amber-700 border-amber-200';
                                            } elseif (strpos($td_stg, 'sidang') !== false) {
                                                $badge_tahap = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            } else {
                                                $badge_tahap = 'bg-orange-50 text-orange-700 border-orange-200';
                                            }
                                            ?>
                                            <span class="inline-block px-2.5 py-1 rounded-xl text-[11px] font-bold border <?= $badge_tahap; ?>">
                                                <?= htmlspecialchars($td); ?>
                                            </span>
                                        </td>

                                        <!-- Jalur TA -->
                                        <td class="py-4 px-5">
                                            <span class="text-xs font-semibold text-slate-700">
                                                <?= htmlspecialchars($r['nama_jalur'] ?? 'Pengkaryaan'); ?>
                                            </span>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-4 px-5 text-center">
                                            <?php if (strpos(strtolower($r['tahapan_display'] ?? ''), 'sidang') !== false): ?>
                                                <a href="<?= site_url('adminlayanan/kembalikan_ke_preview3/' . $r['nim']); ?>"
                                                   onclick="return confirm('Apakah Anda yakin ingin mengembalikan mahasiswa NIM <?= $r['nim']; ?> ke tahapan Preview 3?')"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 text-xs font-bold transition-all border border-amber-200 shadow-2xs"
                                                   title="Kembalikan ke Preview 3">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    <span>Reset Prev 3</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-400 font-semibold text-xs">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="noResultsDesktopPeserta" style="display: none;">
                                    <td colspan="8" class="py-10 text-center text-slate-400">
                                        <i class="bi bi-search text-2xl text-slate-300 block mb-2"></i>
                                        <p class="font-bold text-slate-600 text-xs">Tidak ada data peserta TA yang cocok dengan pencarian</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View (< md) -->
                <div class="block md:hidden p-3.5 space-y-3 bg-slate-50/40" id="pesertaMobileCardsContainer">
                    <?php if (empty($list_peserta)): ?>
                        <div class="py-10 text-center text-slate-400" id="emptyMobilePeserta">
                            <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-2.5 text-xl">
                                <i class="bi bi-journal-x"></i>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Belum Ada Data Peserta Bimbingan TA</p>
                            <p class="text-xs text-slate-400 mt-0.5 max-w-xs mx-auto">
                                Data mahasiswa yang mendaftar dan mengikuti tahapan bimbingan TA akan muncul di sini.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($list_peserta as $idx => $r): ?>
                            <?php
                            $td_m = $r['tahapan_display'] ?? 'Preview 1';
                            $td_stg_m = strtolower($td_m);
                            if (strpos($td_stg_m, 'preview 3') !== false || strpos($td_stg_m, 'preview3') !== false) {
                                $badge_tahap_m = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                            } elseif (strpos($td_stg_m, 'preview 2') !== false || strpos($td_stg_m, 'preview2') !== false) {
                                $badge_tahap_m = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif (strpos($td_stg_m, 'sidang') !== false) {
                                $badge_tahap_m = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } else {
                                $badge_tahap_m = 'bg-orange-50 text-orange-700 border-orange-200';
                            }
                            ?>
                            <div class="peserta-item peserta-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3 transition-all"
                                 data-nim="<?= strtolower($r['nim']); ?>"
                                 data-nama="<?= strtolower($r['nama_lengkap']); ?>"
                                 data-dosen="<?= strtolower($r['nama_dosen_wali'] ?? ''); ?>"
                                 data-prodi="<?= strtolower(($r['prodi'] ?? '') . ' ' . ($r['konsentrasi_dkv'] ?? '')); ?>">
                                
                                <!-- Header: No, NIM & Tahapan -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center row-num-mobile">
                                            <?= $idx + 1; ?>
                                        </span>
                                        <span class="font-mono text-xs text-orange-600 font-bold">
                                            <?= htmlspecialchars($r['nim']); ?>
                                        </span>
                                    </div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg text-[11px] font-bold border <?= $badge_tahap_m; ?>">
                                        <?= htmlspecialchars($td_m); ?>
                                    </span>
                                </div>

                                <!-- Nama & Prodi -->
                                <div class="flex items-start justify-between gap-2 pt-1 border-t border-slate-100">
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($r['nama_lengkap']); ?></h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($r['konsentrasi_dkv'] ?? 'ADVERTISING'); ?></p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 text-[10px] font-bold border border-orange-100 shrink-0">
                                        <?= htmlspecialchars($r['prodi'] ?? 'DKV'); ?>
                                    </span>
                                </div>

                                <!-- Dosen Wali & Approval Info Box -->
                                <div class="bg-slate-50/80 rounded-xl p-2.5 border border-slate-100 space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Dosen Wali:</span>
                                        <strong class="text-slate-700 font-semibold truncate max-w-[170px]"><?= htmlspecialchars($r['nama_dosen_wali'] ?: '-'); ?></strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Status Wali:</span>
                                        <?php if (($r['status_approval_wali'] ?? '') === 'Approved'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <i class="bi bi-check-circle-fill text-emerald-600 text-[9px]"></i> Disetujui
                                            </span>
                                        <?php elseif (($r['status_approval_wali'] ?? '') === 'Rejected'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                <i class="bi bi-x-circle-fill text-rose-600 text-[9px]"></i> Ditolak
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <i class="bi bi-hourglass-split text-amber-600 text-[9px]"></i> Pending
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center justify-between pt-0.5">
                                        <span class="text-slate-400 text-[11px]">Jalur TA:</span>
                                        <span class="text-slate-700 font-semibold text-[11px]"><?= htmlspecialchars($r['nama_jalur'] ?? 'Pengkaryaan'); ?></span>
                                    </div>
                                </div>

                                <!-- Action if Sidang stage -->
                                <?php if (strpos(strtolower($r['tahapan_display'] ?? ''), 'sidang') !== false): ?>
                                    <div class="pt-1 flex justify-end">
                                        <a href="<?= site_url('adminlayanan/kembalikan_ke_preview3/' . $r['nim']); ?>"
                                           onclick="return confirm('Apakah Anda yakin ingin mengembalikan mahasiswa NIM <?= $r['nim']; ?> ke tahapan Preview 3?')"
                                           class="w-full text-center inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 text-xs font-bold transition-all border border-amber-200 shadow-2xs">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                            <span>Kembalikan ke Preview 3</span>
                                        </a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>
                        <div id="noResultsMobilePeserta" style="display: none;" class="py-8 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 p-4">
                            <i class="bi bi-search text-xl text-slate-300 block mb-1.5"></i>
                            <p class="font-bold text-slate-600 text-xs">Tidak ada data peserta TA yang cocok dengan pencarian</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

            <!-- TAB 2: STATUS FILE TA TABLE -->
            <?php if ($active_tab === 'file_ta'): ?>
                
                <!-- Desktop View (>= md) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="pesertaTable">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-5 text-center w-10">No</th>
                                <th class="py-3.5 px-5">Mahasiswa</th>
                                <th class="py-3.5 px-5">Prodi</th>
                                <th class="py-3.5 px-5 text-center">Tahapan File</th>
                                <th class="py-3.5 px-5 text-center">KSM</th>
                                <th class="py-3.5 px-5 text-center">Transkrip</th>
                                <th class="py-3.5 px-5 text-center">Surat Pernyataan</th>
                                <th class="py-3.5 px-5 text-center">Bebas Lab</th>
                                <th class="py-3.5 px-5 text-center">Verifikasi LAA</th>
                                <th class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm" id="pesertaTableBody">
                            <?php if (empty($list_peserta)): ?>
                                <tr id="emptyRow">
                                    <td colspan="10" class="py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 rounded-3xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                            <i class="bi bi-folder-x"></i>
                                        </div>
                                        <p class="font-bold text-slate-700">Belum Ada Berkas File TA</p>
                                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                            Data kelengkapan berkas mahasiswa pendaftar TA akan muncul di sini.
                                        </p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_peserta as $idx => $r): ?>
                                    <tr class="peserta-item peserta-row hover:bg-slate-50/70 transition-colors"
                                        data-nim="<?= strtolower($r['nim']); ?>"
                                        data-nama="<?= strtolower($r['nama_lengkap']); ?>"
                                        data-dosen="<?= strtolower($r['nama_dosen_wali'] ?? ''); ?>"
                                        data-prodi="<?= strtolower(($r['prodi'] ?? '') . ' ' . ($r['konsentrasi_dkv'] ?? '')); ?>">
                                        <!-- No -->
                                        <td class="py-4 px-5 text-center font-bold text-slate-400 text-xs row-num">
                                            <?= $idx + 1; ?>
                                        </td>

                                        <!-- Mahasiswa -->
                                        <td class="py-4 px-5">
                                            <span class="font-bold text-slate-800 block text-xs truncate max-w-[170px]" title="<?= htmlspecialchars($r['nama_lengkap']); ?>">
                                                <?= htmlspecialchars($r['nama_lengkap']); ?>
                                            </span>
                                            <span class="font-mono text-[11px] text-orange-600 font-bold">
                                                <?= htmlspecialchars($r['nim']); ?>
                                            </span>
                                        </td>

                                        <!-- Prodi -->
                                        <td class="py-4 px-5">
                                            <span class="text-xs font-bold text-slate-800 block truncate max-w-[120px]">
                                                <?= htmlspecialchars($r['prodi'] ?? 'DKV'); ?>
                                            </span>
                                        </td>

                                        <!-- Tahapan File -->
                                        <td class="py-4 px-5 text-center whitespace-nowrap">
                                            <?php 
                                            $stgFile = $r['current_stage'] ?? 'Mahasiswa';
                                            $stgFileLower = strtolower($stgFile);
                                            if ($stgFileLower === 'mahasiswa') {
                                                $stgBadgeFile = 'bg-slate-100 text-slate-700 border-slate-200';
                                            } elseif (strpos($stgFileLower, 'wali') !== false) {
                                                $stgBadgeFile = 'bg-amber-100 text-amber-700 border-amber-200';
                                            } elseif (strpos($stgFileLower, 'admin') !== false || strpos($stgFileLower, 'laa') !== false) {
                                                $stgBadgeFile = 'bg-blue-100 text-blue-700 border-blue-200';
                                            } elseif (strpos($stgFileLower, 'koordinator') !== false || strpos($stgFileLower, 'koor') !== false) {
                                                $stgBadgeFile = 'bg-purple-100 text-purple-700 border-purple-200';
                                            } elseif (strpos($stgFileLower, 'approved') !== false || strpos($stgFileLower, 'selesai') !== false) {
                                                $stgBadgeFile = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                            } else {
                                                $stgBadgeFile = 'bg-slate-100 text-slate-700 border-slate-200';
                                            }
                                            ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border whitespace-nowrap <?= $stgBadgeFile ?>">
                                                <i class="bi bi-clock-history text-[9px]"></i> <?= htmlspecialchars($stgFile) ?>
                                            </span>
                                        </td>

                                        <!-- KSM -->
                                        <td class="py-4 px-5 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($r['status_ksm'] ?? '') === 'Valid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                                                <?= $r['status_ksm'] ?? 'Pending' ?>
                                            </span>
                                        </td>

                                        <!-- Transkrip -->
                                        <td class="py-4 px-5 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($r['status_transkrip'] ?? '') === 'Valid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                                                <?= $r['status_transkrip'] ?? 'Pending' ?>
                                            </span>
                                        </td>

                                        <!-- Pernyataan -->
                                        <td class="py-4 px-5 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($r['status_pernyataan'] ?? '') === 'Valid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                                                <?= $r['status_pernyataan'] ?? 'Pending' ?>
                                            </span>
                                        </td>

                                        <!-- Bebas Lab -->
                                        <td class="py-4 px-5 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($r['status_bebas_lab'] ?? '') === 'Valid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                                                <?= $r['status_bebas_lab'] ?? 'Pending' ?>
                                            </span>
                                        </td>

                                        <!-- Verifikasi LAA -->
                                        <td class="py-4 px-5 text-center">
                                            <?php if (($r['status_approval_admin'] ?? '') === 'Approved'): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
                                                    <i class="bi bi-check-circle-fill text-emerald-600 text-xs"></i> Complete
                                                </span>
                                            <?php elseif (($r['status_approval_admin'] ?? '') === 'Rejected'): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-50 text-rose-800 font-bold text-xs border border-rose-200">
                                                    <i class="bi bi-x-circle-fill text-rose-600 text-xs"></i> Revisi
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200">
                                                    <i class="bi bi-hourglass-split text-amber-600 text-xs"></i> Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-4 px-5 text-center">
                                            <a href="<?= site_url('adminlayanan/detail_berkas/' . $r['nim']); ?>"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-600 hover:text-white text-orange-700 text-xs font-bold transition-all shadow-2xs">
                                                <i class="bi bi-folder2-open text-xs"></i>
                                                <span>Detail Berkas</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="noResultsDesktopPeserta" style="display: none;">
                                    <td colspan="10" class="py-10 text-center text-slate-400">
                                        <i class="bi bi-search text-2xl text-slate-300 block mb-2"></i>
                                        <p class="font-bold text-slate-600 text-xs">Tidak ada data berkas yang cocok dengan pencarian</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View (< md) -->
                <div class="block md:hidden p-3.5 space-y-3 bg-slate-50/40" id="fileTaMobileCardsContainer">
                    <?php if (empty($list_peserta)): ?>
                        <div class="py-10 text-center text-slate-400" id="emptyMobileFileTa">
                            <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-2.5 text-xl">
                                <i class="bi bi-folder-x"></i>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Belum Ada Berkas File TA</p>
                            <p class="text-xs text-slate-400 mt-0.5 max-w-xs mx-auto">
                                Data kelengkapan berkas mahasiswa pendaftar TA akan muncul di sini.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($list_peserta as $idx => $r): ?>
                            <div class="peserta-item peserta-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3 transition-all"
                                 data-nim="<?= strtolower($r['nim']); ?>"
                                 data-nama="<?= strtolower($r['nama_lengkap']); ?>"
                                 data-dosen="<?= strtolower($r['nama_dosen_wali'] ?? ''); ?>"
                                 data-prodi="<?= strtolower(($r['prodi'] ?? '') . ' ' . ($r['konsentrasi_dkv'] ?? '')); ?>">
                                
                                <!-- Header: No, NIM & Verifikasi LAA -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center row-num-mobile">
                                            <?= $idx + 1; ?>
                                        </span>
                                        <span class="font-mono text-xs text-orange-600 font-bold">
                                            <?= htmlspecialchars($r['nim']); ?>
                                        </span>
                                    </div>
                                    <?php if (($r['status_approval_admin'] ?? '') === 'Approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                                            <i class="bi bi-check-circle-fill text-emerald-600 text-[10px]"></i> Complete
                                        </span>
                                    <?php elseif (($r['status_approval_admin'] ?? '') === 'Rejected'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 font-bold text-[11px] border border-rose-200">
                                            <i class="bi bi-x-circle-fill text-rose-600 text-[10px]"></i> Revisi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[11px] border border-amber-200">
                                            <i class="bi bi-hourglass-split text-amber-600 text-[10px]"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Nama & Action -->
                                <div class="flex items-start justify-between gap-2 pt-1 border-t border-slate-100">
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($r['nama_lengkap']); ?></h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($r['prodi'] ?? 'DKV'); ?></p>
                                    </div>
                                    <a href="<?= site_url('adminlayanan/detail_berkas/' . $r['nim']); ?>"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-xs shrink-0">
                                        <i class="bi bi-folder2-open text-xs"></i>
                                        <span>Detail Berkas</span>
                                    </a>
                                </div>

                                <!-- Grid Status Berkas -->
                                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50/80 border border-slate-100 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">KSM:</span>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold <?= ($r['status_ksm'] ?? '') === 'Valid' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' ?>">
                                            <?= $r['status_ksm'] ?? 'Pending' ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Transkrip:</span>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold <?= ($r['status_transkrip'] ?? '') === 'Valid' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' ?>">
                                            <?= $r['status_transkrip'] ?? 'Pending' ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Pernyataan:</span>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold <?= ($r['status_pernyataan'] ?? '') === 'Valid' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' ?>">
                                            <?= $r['status_pernyataan'] ?? 'Pending' ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Bebas Lab:</span>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold <?= ($r['status_bebas_lab'] ?? '') === 'Valid' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' ?>">
                                            <?= $r['status_bebas_lab'] ?? 'Pending' ?>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                        <div id="noResultsMobilePeserta" style="display: none;" class="py-8 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 p-4">
                            <i class="bi bi-search text-xl text-slate-300 block mb-1.5"></i>
                            <p class="font-bold text-slate-600 text-xs">Tidak ada data berkas yang cocok dengan pencarian</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>

    </main>

    <!-- Raw Data for Autocomplete -->
    <script>
        window.pesertaData = <?= json_encode(array_map(function($r) {
            return [
                'nim'   => $r['nim'],
                'nama'  => $r['nama_lengkap'],
                'dosen' => $r['nama_dosen_wali'] ?? '',
                'prodi' => ($r['prodi'] ?? '') . ' ' . ($r['konsentrasi_dkv'] ?? '')
            ];
        }, $list_peserta ?? [])); ?>;

        let extraRowCounterPeserta = 0;

        function updatePesertaFilterBadge() {
            const totalRows = document.querySelectorAll('.extra-filter-row-peserta').length + 1;
            const badge = document.getElementById('filterCountBadgePeserta');
            if (badge) badge.innerText = `${totalRows}/4`;
        }

        function togglePesertaCatDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('menuCatPeserta');
            const arrow = document.getElementById('arrowCatPeserta');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                arrow.style.transform = 'rotate(180deg)';
            } else {
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        function selectPesertaMainCategory(val, label) {
            document.getElementById('mainCategorySelectPeserta').value = val;
            document.getElementById('labelCatPeserta').textContent = label;
            document.getElementById('menuCatPeserta').classList.add('hidden');
            document.getElementById('arrowCatPeserta').style.transform = 'rotate(0deg)';
            document.getElementById('inputSearchPeserta').focus();
        }

        // Toggle or Add Extra Filter Row (Max 4 total)
        function togglePesertaMultiFilter(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const extraCard = document.getElementById('extraRowsCardPeserta');
            const currentRows = document.querySelectorAll('.extra-filter-row-peserta').length;

            if (currentRows >= 3) {
                if (extraCard) extraCard.classList.toggle('open');
                return;
            }

            addPesertaFilterRow(e);
        }

        function addPesertaFilterRow(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('additionalFilterRowsContainerPeserta');
            const extraCard = document.getElementById('extraRowsCardPeserta');
            const currentRows = document.querySelectorAll('.extra-filter-row-peserta').length;

            if (currentRows >= 3) return;

            extraRowCounterPeserta++;
            const rowId = 'extra-peserta-' + extraRowCounterPeserta;

            const catOptions = [
                { key: 'nama', label: '🏷️ Nama Mahasiswa', placeholder: 'Ketik nama mahasiswa lalu tekan Enter...' },
                { key: 'nim', label: '🆔 NIM Mahasiswa', placeholder: 'Ketik NIM mahasiswa...' },
                { key: 'dosen', label: '👨‍🏫 Dosen Wali', placeholder: 'Ketik nama dosen wali...' },
                { key: 'prodi', label: '🎯 Program Studi & KK', placeholder: 'Ketik prodi/konsentrasi...' }
            ];

            const selectedCat = catOptions[currentRows % catOptions.length];

            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row-peserta flex items-center gap-2 relative';
            rowDiv.id = rowId;

            rowDiv.innerHTML = `
                <div class="unified-search-pill flex-1 flex items-center justify-between gap-1">
                    <div class="relative custom-dropdown-container shrink-0">
                        <button type="button" onclick="toggleExtraDropdownPeserta('${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-hidden">
                            <span id="label-${rowId}" class="truncate max-w-[120px] sm:max-w-[130px]">${selectedCat.label}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-${rowId}"></i>
                        </button>
                        <div id="menu-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectExtraCatPeserta('${rowId}', 'nama', '🏷️ Nama Mahasiswa', 'Ketik nama mahasiswa...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🏷️ Nama Mahasiswa</span></div>
                            <div onclick="selectExtraCatPeserta('${rowId}', 'nim', '🆔 NIM Mahasiswa', 'Ketik NIM mahasiswa...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🆔 NIM Mahasiswa</span></div>
                            <div onclick="selectExtraCatPeserta('${rowId}', 'dosen', '👨‍🏫 Dosen Wali', 'Ketik nama dosen wali...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>👨‍🏫 Dosen Wali</span></div>
                            <div onclick="selectExtraCatPeserta('${rowId}', 'prodi', '🎯 Program Studi & KK', 'Ketik prodi/konsentrasi...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🎯 Program Studi & KK</span></div>
                        </div>
                    </div>
                    <div class="unified-divider"></div>
                    <div class="flex-1 flex items-center min-w-0 px-1">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                        <input type="text" data-cat="${selectedCat.key}" placeholder="${selectedCat.placeholder}" onkeydown="handlePesertaInputKey(event)" class="extra-row-input-peserta w-full text-xs font-semibold bg-transparent border-none focus:outline-hidden text-slate-800 placeholder:text-slate-400">
                    </div>
                </div>
                <button type="button" onclick="removePesertaFilterRow(this)" class="btn-remove-row shrink-0" title="Hapus Filter Ini">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            `;

            container.appendChild(rowDiv);
            if (extraCard) extraCard.classList.add('open');
            updatePesertaFilterBadge();

            const newInput = rowDiv.querySelector('input');
            if (newInput) newInput.focus();
        }

        function removePesertaFilterRow(btn) {
            const row = btn.closest('.extra-filter-row-peserta');
            if (row) row.remove();

            const extraCard = document.getElementById('extraRowsCardPeserta');
            const remainingRows = document.querySelectorAll('.extra-filter-row-peserta').length;
            if (remainingRows === 0 && extraCard) {
                extraCard.classList.remove('open');
            }

            updatePesertaFilterBadge();
            performPesertaTableFilter();
        }

        function toggleExtraDropdownPeserta(rowId, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menu-' + rowId);
            const arrow = document.getElementById('arrow-' + rowId);
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            document.querySelectorAll('.dropdown-arrow').forEach(a => {
                if (a !== arrow) a.style.transform = 'rotate(0deg)';
            });
            if (menu) {
                menu.classList.toggle('hidden');
                if (arrow) arrow.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        function selectExtraCatPeserta(rowId, key, label, placeholder, el) {
            const labelEl = document.getElementById('label-' + rowId);
            const inputEl = document.querySelector(`#${rowId} input.extra-row-input-peserta`);
            if (labelEl) labelEl.textContent = label;
            if (inputEl) {
                inputEl.setAttribute('data-cat', key);
                inputEl.placeholder = placeholder;
                inputEl.focus();
            }
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        function resetPesertaMultiSearch() {
            const container = document.getElementById('additionalFilterRowsContainerPeserta');
            const extraCard = document.getElementById('extraRowsCardPeserta');
            if (container) container.innerHTML = '';
            if (extraCard) extraCard.classList.remove('open');

            const mainInput = document.getElementById('inputSearchPeserta');
            if (mainInput) mainInput.value = '';

            const btnClear = document.getElementById('btnClearSearchPeserta');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }

            updatePesertaFilterBadge();
            performPesertaTableFilter();
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#dropdownCatWrapperPeserta')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            }
            const autoBox = document.getElementById('autocompletePeserta');
            if (autoBox && !e.target.closest('#inputSearchPeserta') && !e.target.closest('#autocompletePeserta')) {
                autoBox.classList.add('hidden');
            }
        });

        // Autocomplete Suggestion Logic
        function handlePesertaAutocomplete(val) {
            const q = val.trim().toLowerCase();
            const btnClear = document.getElementById('btnClearSearchPeserta');
            const autoBox = document.getElementById('autocompletePeserta');
            const autoResults = document.getElementById('autocompleteResultsPeserta');

            if (btnClear) {
                if (q.length > 0) {
                    btnClear.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                    btnClear.classList.add('opacity-100', 'scale-100');
                } else {
                    btnClear.classList.remove('opacity-100', 'scale-100');
                    btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                }
            }

            if (q.length < 1 || !window.pesertaData || window.pesertaData.length === 0) {
                if (autoBox) autoBox.classList.add('hidden');
                return;
            }

            const cat = document.getElementById('mainCategorySelectPeserta').value;
            const matches = window.pesertaData.filter(item => {
                if (cat === 'nim') return item.nim.toLowerCase().includes(q);
                if (cat === 'nama') return item.nama.toLowerCase().includes(q);
                if (cat === 'dosen') return item.dosen.toLowerCase().includes(q);
                if (cat === 'prodi') return item.prodi.toLowerCase().includes(q);
                return item.nim.toLowerCase().includes(q) || item.nama.toLowerCase().includes(q) || item.dosen.toLowerCase().includes(q) || item.prodi.toLowerCase().includes(q);
            }).slice(0, 6);

            if (matches.length === 0) {
                if (autoBox) autoBox.classList.add('hidden');
                return;
            }

            let html = '';
            matches.forEach(m => {
                const highlightedName = highlightKeyword(m.nama, q);
                const highlightedNim = highlightKeyword(m.nim, q);
                html += `
                    <div class="autocomplete-item-row flex items-center justify-between gap-3 p-3 hover:bg-orange-50 transition cursor-pointer" onclick="selectAutocompleteItemPeserta('${m.nama.replace(/'/g, "\\'")}')">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-xs">${highlightedName}</p>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">${highlightedNim} • Wali: ${m.dosen || '-'} • ${m.prodi}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-800 font-bold text-[10px] shrink-0">Pilih</span>
                    </div>
                `;
            });

            autoResults.innerHTML = html;
            autoBox.classList.remove('hidden');
        }

        function highlightKeyword(text, keyword) {
            if (!text) return '';
            const regex = new RegExp(`(${keyword})`, 'gi');
            return text.replace(regex, '<mark>$1</mark>');
        }

        function selectAutocompleteItemPeserta(val) {
            const input = document.getElementById('inputSearchPeserta');
            if (input) input.value = val;
            const autoBox = document.getElementById('autocompletePeserta');
            if (autoBox) autoBox.classList.add('hidden');
            performPesertaTableFilter();
        }

        // Keydown Handler (Manual Enter trigger)
        function handlePesertaInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const autoBox = document.getElementById('autocompletePeserta');
                if (autoBox) autoBox.classList.add('hidden');
                performPesertaTableFilter();
            }
        }

        function executePesertaSearch(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const autoBox = document.getElementById('autocompletePeserta');
            if (autoBox) autoBox.classList.add('hidden');
            performPesertaTableFilter();
        }

        // Multi-Criteria Combined Table & Mobile Card Filtering
        function performPesertaTableFilter() {
            const filters = [];

            // 1. Main filter
            const mainInput = document.getElementById('inputSearchPeserta');
            const mainQuery = mainInput ? mainInput.value.trim().toLowerCase() : '';
            const mainCat = document.getElementById('mainCategorySelectPeserta').value;
            if (mainQuery) {
                filters.push({ cat: mainCat, q: mainQuery });
            }

            // 2. Extra filters
            document.querySelectorAll('.extra-filter-row-peserta').forEach(row => {
                const inp = row.querySelector('.extra-row-input-peserta');
                if (inp && inp.value.trim()) {
                    filters.push({
                        cat: inp.getAttribute('data-cat') || 'nama',
                        q: inp.value.trim().toLowerCase()
                    });
                }
            });

            // Filter Desktop Rows
            const rows = document.querySelectorAll('.peserta-row');
            let visibleDesktop = 0;

            rows.forEach(row => {
                let isMatch = true;

                if (filters.length > 0) {
                    for (const f of filters) {
                        let fieldVal = '';
                        if (f.cat === 'nim') fieldVal = row.getAttribute('data-nim') || '';
                        else if (f.cat === 'nama') fieldVal = row.getAttribute('data-nama') || '';
                        else if (f.cat === 'dosen') fieldVal = row.getAttribute('data-dosen') || '';
                        else if (f.cat === 'prodi') fieldVal = row.getAttribute('data-prodi') || '';
                        else fieldVal = row.textContent.toLowerCase();

                        if (!fieldVal.includes(f.q)) {
                            isMatch = false;
                            break;
                        }
                    }
                }

                if (isMatch) {
                    row.style.display = '';
                    visibleDesktop++;
                    const numCell = row.querySelector('.row-num');
                    if (numCell) numCell.textContent = visibleDesktop;
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter Mobile Cards
            const cards = document.querySelectorAll('.peserta-card');
            let visibleMobile = 0;

            cards.forEach(card => {
                let isMatch = true;

                if (filters.length > 0) {
                    for (const f of filters) {
                        let fieldVal = '';
                        if (f.cat === 'nim') fieldVal = card.getAttribute('data-nim') || '';
                        else if (f.cat === 'nama') fieldVal = card.getAttribute('data-nama') || '';
                        else if (f.cat === 'dosen') fieldVal = card.getAttribute('data-dosen') || '';
                        else if (f.cat === 'prodi') fieldVal = card.getAttribute('data-prodi') || '';
                        else fieldVal = card.textContent.toLowerCase();

                        if (!fieldVal.includes(f.q)) {
                            isMatch = false;
                            break;
                        }
                    }
                }

                if (isMatch) {
                    card.style.display = '';
                    visibleMobile++;
                    const numCell = card.querySelector('.row-num-mobile');
                    if (numCell) numCell.textContent = visibleMobile;
                } else {
                    card.style.display = 'none';
                }
            });

            // Empty state toggles
            const noResDesktop = document.getElementById('noResultsDesktopPeserta');
            if (noResDesktop) {
                noResDesktop.style.display = (visibleDesktop === 0 && rows.length > 0) ? '' : 'none';
            }

            const noResMobile = document.getElementById('noResultsMobilePeserta');
            if (noResMobile) {
                noResMobile.style.display = (visibleMobile === 0 && cards.length > 0) ? '' : 'none';
            }
        }

        function clearPesertaSearch() {
            const input = document.getElementById('inputSearchPeserta');
            if (input) {
                input.value = '';
                input.focus();
            }
            const btnClear = document.getElementById('btnClearSearchPeserta');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }
            const autoBox = document.getElementById('autocompletePeserta');
            if (autoBox) autoBox.classList.add('hidden');
            performPesertaTableFilter();
        }
    </script>
</body>
</html>
