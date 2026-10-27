<?php
/**
 * Monitoring Mahasiswa Lulus Sidang & Pengumpulan Berkas Revisi - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mahasiswa Lulus Sidang — Admin LAA'; ?> - IFIK</title>

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
            border-color: #059669 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.14), 0 10px 25px -5px rgba(16, 185, 129, 0.12) !important;
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
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2), 0 8px 24px -4px rgba(16, 185, 129, 0.15);
        }
        .autocomplete-item-row {
            padding: 10px 16px;
            transition: all 0.18s ease;
            cursor: pointer;
        }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav {
            background-color: #ecfdf5;
            color: #059669;
        }
        .autocomplete-item-row mark {
            background: #d1fae5;
            color: #047857;
            font-weight: 800;
            border-radius: 4px;
            padding: 0 3px;
        }

        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            border: 1.5px solid #a7f3d0;
            border-radius: 16px;
            padding: 6px 14px;
            height: 46px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #059669;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.06);
        }
        .btn-standalone-add:hover {
            background: #d1fae5;
            border-color: #6ee7b7;
            transform: scale(1.02);
        }
        .badge-standalone-count {
            background: #059669;
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

    <!-- Main Content Container (Balanced padding on mobile & desktop) -->
    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Pendaftaran TA</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-emerald-600 font-bold">Sudah Lulus Sidang</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 shrink-0">
                            <i class="bi bi-mortarboard-fill text-lg sm:text-xl"></i>
                        </span>
                        <span>Mahasiswa Sudah Lulus Sidang</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Daftar rekapitulasi mahasiswa yang telah sukses menyelesaikan dan dinyatakan lulus Sidang Akhir Tugas Akhir.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-emerald-50 text-emerald-800 text-[11px] sm:text-xs font-extrabold tracking-wider uppercase border border-emerald-200 shadow-xs flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-emerald-600"></i> Total: <?= count($list ?? []); ?> Mahasiswa
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <?php
            $totalLulus = count($list ?? []);
            $dkvCount = 0;
            $diCount = 0;
            $dpCount = 0;
            if (!empty($list)) {
                foreach ($list as $r) {
                    $p = strtolower($r['prodi'] ?? '');
                    if (strpos($p, 'komunikasi') !== false || strpos($p, 'dkv') !== false) $dkvCount++;
                    elseif (strpos($p, 'interior') !== false || strpos($p, 'di') !== false) $diCount++;
                    elseif (strpos($p, 'produk') !== false) $dpCount++;
                    else $dkvCount++;
                }
            }
        ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">

            <!-- Total Lulus -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block truncate">Total Kelulusan</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block"><?= $totalLulus; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Mahasiswa Lulus</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>

            <!-- Prodi DKV -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-orange-500 uppercase tracking-wider block truncate">Desain Komunikasi</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block"><?= $dkvCount; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">S1 DKV</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-palette-fill"></i>
                </div>
            </div>

            <!-- Prodi Desain Interior -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-blue-500 uppercase tracking-wider block truncate">Desain Interior</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 sm:mt-1 block"><?= $diCount; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">S1 Desain Interior</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-house-door-fill"></i>
                </div>
            </div>

            <!-- Periode Status -->
            <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-purple-500 uppercase tracking-wider block truncate">Status Sidang</span>
                    <span class="text-sm sm:text-lg font-extrabold text-purple-700 mt-0.5 sm:mt-1 block truncate">Yudisium Ready</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate block">Data Terverifikasi</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg sm:text-xl shrink-0 ml-2">
                    <i class="bi bi-award-fill"></i>
                </div>
            </div>

        </div>

        <!-- Unified Multi-Search Bar (Up to 4 Categories, Manual Enter/Button Search, Autocomplete) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6 relative">
            <form action="<?= site_url('adminlayanan/lulus_sidang'); ?>" method="GET" id="formSearchLulus" onsubmit="executeLulusSearch(event)" class="relative">
                <input type="hidden" name="cat" id="mainCategorySelectLulus" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <!-- Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container shrink-0" id="dropdownCatWrapper">
                            <?php
                                $catLabels = [
                                    'query' => '🔍 Kata Kunci (Semua)',
                                    'nama'  => '🏷️ Nama Mahasiswa',
                                    'nim'   => '🆔 NIM Mahasiswa',
                                    'judul' => '📖 Judul Tugas Akhir',
                                    'prodi' => '🎯 Program Studi & KK'
                                ];
                                $curLabel = $catLabels[$cat ?? 'query'] ?? '🔍 Kata Kunci (Semua)';
                            ?>
                            <button type="button" onclick="toggleLulusCatDropdown(event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-emerald-600 focus:outline-hidden">
                                <span id="labelCatLulus" class="truncate max-w-[120px] sm:max-w-[170px]"><?= $curLabel; ?></span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrowCatLulus"></i>
                            </button>
                            <div id="menuCatLulus" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                <div onclick="selectLulusMainCategory('query', '🔍 Kata Kunci (Semua)')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? 'query') === 'query' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🔍 Kata Kunci (Semua)</span>
                                </div>
                                <div onclick="selectLulusMainCategory('nama', '🏷️ Nama Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'nama' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🏷️ Nama Mahasiswa</span>
                                </div>
                                <div onclick="selectLulusMainCategory('nim', '🆔 NIM Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'nim' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🆔 NIM Mahasiswa</span>
                                </div>
                                <div onclick="selectLulusMainCategory('judul', '📖 Judul Tugas Akhir')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'judul' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>📖 Judul Tugas Akhir</span>
                                </div>
                                <div onclick="selectLulusMainCategory('prodi', '🎯 Program Studi & KK')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer flex items-center justify-between font-semibold <?= ($cat ?? '') === 'prodi' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>">
                                    <span>🎯 Program Studi & KK</span>
                                </div>
                            </div>
                        </div>

                        <div class="unified-divider shrink-0"></div>

                        <!-- Search Text Input (Manual Enter / Click Cari, Autocomplete Triggered) -->
                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchLulus" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Ketik kata kunci lalu tekan Enter..." 
                                   oninput="handleLulusAutocomplete(this.value)"
                                   onkeydown="handleLulusInputKey(event)"
                                   class="w-full text-xs font-semibold bg-transparent border-none focus:outline-hidden text-slate-800 placeholder:text-slate-400 placeholder:font-normal min-w-0">
                            
                            <button type="button" id="btnClearSearchLulus" onclick="clearLulusSearch()" class="<?= empty($search) ? 'opacity-0 scale-75 pointer-events-none' : 'opacity-100 scale-100'; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-1 cursor-pointer shrink-0 transition-all duration-200 transform" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark text-sm"></i>
                            </button>
                        </div>

                        <!-- Submit Cari Button -->
                        <button type="button" onclick="executeLulusSearch(event)" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer active:scale-95 shrink-0 ml-1">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnLulus" onclick="toggleLulusMultiFilter(event)" class="btn-standalone-add shrink-0 justify-center w-full sm:w-auto" title="Tambah Kriteria Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgeLulus" class="badge-standalone-count">1/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown Box -->
                <div id="autocompleteLulus" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsLulus" class="divide-y divide-slate-100 text-xs"></div>
                </div>

                <!-- Extra Filter Rows Container (Maks 4 total) -->
                <div id="extraRowsCardLulus" class="extra-rows-card space-y-2.5">
                    <div id="additionalFilterRowsContainerLulus" class="space-y-2.5">
                        <!-- Dynamic extra rows added here -->
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian mahasiswa. Tekan Enter / Cari.</span>
                        <button type="button" onclick="resetLulusMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors cursor-pointer self-start sm:self-auto">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Main Card Container: Desktop Table & Mobile Cards -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">

            <!-- Table Header Toolbar -->
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-journal-text text-emerald-600"></i> Rekapitulasi Mahasiswa Lulus
                    </h3>
                    <span class="text-xs text-slate-400 font-medium" id="tableCountBadge">(<?= $totalLulus; ?> data)</span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 1. DESKTOP / TABLET TABLE VIEW (>= md)    -->
            <!-- ========================================== -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse" id="lulusSidangTable">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6 text-center w-12">No</th>
                            <th class="py-3.5 px-6">NIM & Mahasiswa</th>
                            <th class="py-3.5 px-6">Program Studi</th>
                            <th class="py-3.5 px-6">Judul Tugas Akhir</th>
                            <th class="py-3.5 px-6 text-center">Status Kelulusan</th>
                            <th class="py-3.5 px-6 text-center">Tanggal Lulus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm" id="lulusTableBody">
                        <?php if (empty($list)): ?>
                            <tr id="emptyRow">
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                        <i class="bi bi-mortarboard"></i>
                                    </div>
                                    <p class="font-bold text-slate-700">Belum Ada Mahasiswa Lulus Sidang</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Data mahasiswa yang telah menyelesaikan sidang dan dinyatakan lulus akan tercatat di sini.
                                    </p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $idx => $row): ?>
                                <tr class="lulus-item lulus-row hover:bg-slate-50/70 transition-colors"
                                    data-nim="<?= strtolower($row['nim']); ?>"
                                    data-nama="<?= strtolower($row['nama_lengkap']); ?>"
                                    data-judul="<?= strtolower($row['judul_1'] ?? ''); ?>"
                                    data-prodi="<?= strtolower(($row['prodi'] ?? '') . ' ' . ($row['konsentrasi_dkv'] ?? '')); ?>">
                                    <!-- No -->
                                    <td class="py-4 px-6 text-center font-bold text-slate-400 text-xs row-num">
                                        <?= $idx + 1; ?>
                                    </td>

                                    <!-- NIM & Mahasiswa -->
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-slate-800 block text-xs">
                                            <?= htmlspecialchars($row['nama_lengkap']); ?>
                                        </span>
                                        <span class="font-mono text-[11px] text-orange-600 font-bold">
                                            <?= htmlspecialchars($row['nim']); ?>
                                        </span>
                                    </td>

                                    <!-- Prodi & Konsentrasi -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                            <span class="text-xs font-bold text-slate-800"><?= htmlspecialchars($row['prodi'] ?? 'Desain Komunikasi Visual'); ?></span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium pl-3.5 mt-0.5">
                                            <?= htmlspecialchars($row['konsentrasi_dkv'] ?? 'ADVERTISING'); ?>
                                        </div>
                                    </td>

                                    <!-- Judul TA -->
                                    <td class="py-4 px-6 max-w-md">
                                        <p class="font-semibold text-slate-700 text-xs leading-relaxed line-clamp-2" title="<?= htmlspecialchars($row['judul_1'] ?? ''); ?>">
                                            <?= htmlspecialchars($row['judul_1'] ?? 'Judul Tugas Akhir'); ?>
                                        </p>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill text-emerald-600 text-xs"></i>
                                            <span>Lulus Sidang TA</span>
                                        </span>
                                    </td>

                                    <!-- Tanggal Lulus -->
                                    <td class="py-4 px-6 text-center">
                                        <span class="font-mono text-xs font-semibold text-slate-600">
                                            <?= date('d M Y', strtotime($row['updated_at'] ?? 'now')); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="noResultsDesktopLulus" style="display: none;">
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    <i class="bi bi-search text-2xl text-slate-300 block mb-2"></i>
                                    <p class="font-bold text-slate-600 text-xs">Tidak ada data mahasiswa lulus yang cocok dengan pencarian</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ========================================== -->
            <!-- 2. MOBILE CARD VIEW (< md)                 -->
            <!-- ========================================== -->
            <div class="block md:hidden p-3.5 space-y-3 bg-slate-50/40" id="lulusMobileCardsContainer">
                <?php if (empty($list)): ?>
                    <div class="py-10 text-center text-slate-400" id="emptyMobileLulus">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-400 flex items-center justify-center mx-auto mb-2.5 text-xl">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Belum Ada Mahasiswa Lulus Sidang</p>
                        <p class="text-xs text-slate-400 mt-0.5 max-w-xs mx-auto">
                            Data mahasiswa yang telah menyelesaikan sidang dan dinyatakan lulus akan tercatat di sini.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($list as $idx => $row): ?>
                        <div class="lulus-item lulus-card bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3 transition-all"
                             data-nim="<?= strtolower($row['nim']); ?>"
                             data-nama="<?= strtolower($row['nama_lengkap']); ?>"
                             data-judul="<?= strtolower($row['judul_1'] ?? ''); ?>"
                             data-prodi="<?= strtolower(($row['prodi'] ?? '') . ' ' . ($row['konsentrasi_dkv'] ?? '')); ?>">
                            
                            <!-- Header: No, NIM & Tanggal Lulus -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center row-num-mobile">
                                        <?= $idx + 1; ?>
                                    </span>
                                    <span class="font-mono text-xs text-orange-600 font-bold">
                                        <?= htmlspecialchars($row['nim']); ?>
                                    </span>
                                </div>
                                <span class="font-mono text-[10px] text-slate-400 font-medium flex items-center gap-1">
                                    <i class="bi bi-calendar-check text-[10px] text-emerald-600"></i>
                                    <?= date('d M Y', strtotime($row['updated_at'] ?? 'now')); ?>
                                </span>
                            </div>

                            <!-- Nama & Prodi -->
                            <div class="flex items-start justify-between gap-2 pt-1 border-t border-slate-100">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($row['nama_lengkap']); ?></h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($row['konsentrasi_dkv'] ?? 'ADVERTISING'); ?></p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <?= htmlspecialchars($row['prodi'] ?? 'DKV'); ?>
                                </span>
                            </div>

                            <!-- Judul Tugas Akhir -->
                            <div class="bg-slate-50/80 rounded-xl p-2.5 border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Judul Tugas Akhir</span>
                                <p class="text-xs font-semibold text-slate-700 leading-relaxed line-clamp-2" title="<?= htmlspecialchars($row['judul_1'] ?? ''); ?>">
                                    <?= htmlspecialchars($row['judul_1'] ?? 'Judul Tugas Akhir'); ?>
                                </p>
                            </div>

                            <!-- Bottom: Status Badge -->
                            <div class="flex items-center justify-between gap-2 pt-1">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill text-emerald-600 text-xs"></i>
                                    <span>Lulus Sidang TA</span>
                                </span>
                                <span class="text-[11px] font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-100">
                                    Yudisium Ready
                                </span>
                            </div>

                        </div>
                    <?php endforeach; ?>
                    <div id="noResultsMobileLulus" style="display: none;" class="py-8 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 p-4">
                        <i class="bi bi-search text-xl text-slate-300 block mb-1.5"></i>
                        <p class="font-bold text-slate-600 text-xs">Tidak ada data mahasiswa lulus yang cocok dengan pencarian</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <!-- Raw Data for Autocomplete -->
    <script>
        window.lulusData = <?= json_encode(array_map(function($r) {
            return [
                'nim'   => $r['nim'],
                'nama'  => $r['nama_lengkap'],
                'judul' => $r['judul_1'] ?? '',
                'prodi' => ($r['prodi'] ?? '') . ' ' . ($r['konsentrasi_dkv'] ?? '')
            ];
        }, $list ?? [])); ?>;

        let extraRowCounterLulus = 0;

        function updateLulusFilterBadge() {
            const totalRows = document.querySelectorAll('.extra-filter-row-lulus').length + 1;
            const badge = document.getElementById('filterCountBadgeLulus');
            if (badge) badge.innerText = `${totalRows}/4`;
        }

        function toggleLulusCatDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('menuCatLulus');
            const arrow = document.getElementById('arrowCatLulus');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                arrow.style.transform = 'rotate(180deg)';
            } else {
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        function selectLulusMainCategory(val, label) {
            document.getElementById('mainCategorySelectLulus').value = val;
            document.getElementById('labelCatLulus').textContent = label;
            document.getElementById('menuCatLulus').classList.add('hidden');
            document.getElementById('arrowCatLulus').style.transform = 'rotate(0deg)';
            document.getElementById('inputSearchLulus').focus();
        }

        // Toggle or Add Extra Filter Row (Max 4 total)
        function toggleLulusMultiFilter(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const extraCard = document.getElementById('extraRowsCardLulus');
            const currentRows = document.querySelectorAll('.extra-filter-row-lulus').length;

            if (currentRows >= 3) {
                if (extraCard) extraCard.classList.toggle('open');
                return;
            }

            addLulusFilterRow(e);
        }

        function addLulusFilterRow(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('additionalFilterRowsContainerLulus');
            const extraCard = document.getElementById('extraRowsCardLulus');
            const currentRows = document.querySelectorAll('.extra-filter-row-lulus').length;

            if (currentRows >= 3) return;

            extraRowCounterLulus++;
            const rowId = 'extra-lulus-' + extraRowCounterLulus;

            const catOptions = [
                { key: 'nama', label: '🏷️ Nama Mahasiswa', placeholder: 'Ketik nama mahasiswa lalu tekan Enter...' },
                { key: 'nim', label: '🆔 NIM Mahasiswa', placeholder: 'Ketik NIM mahasiswa...' },
                { key: 'judul', label: '📖 Judul Tugas Akhir', placeholder: 'Ketik judul/topik TA...' },
                { key: 'prodi', label: '🎯 Program Studi & KK', placeholder: 'Ketik prodi/konsentrasi...' }
            ];

            const selectedCat = catOptions[currentRows % catOptions.length];

            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row-lulus flex items-center gap-2 relative';
            rowDiv.id = rowId;

            rowDiv.innerHTML = `
                <div class="unified-search-pill flex-1 flex items-center justify-between gap-1">
                    <div class="relative custom-dropdown-container shrink-0">
                        <button type="button" onclick="toggleExtraDropdown('${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-emerald-600 focus:outline-hidden">
                            <span id="label-${rowId}" class="truncate max-w-[120px] sm:max-w-[130px]">${selectedCat.label}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-${rowId}"></i>
                        </button>
                        <div id="menu-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectExtraCat('${rowId}', 'nama', '🏷️ Nama Mahasiswa', 'Ketik nama mahasiswa...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700"><span>🏷️ Nama Mahasiswa</span></div>
                            <div onclick="selectExtraCat('${rowId}', 'nim', '🆔 NIM Mahasiswa', 'Ketik NIM mahasiswa...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700"><span>🆔 NIM Mahasiswa</span></div>
                            <div onclick="selectExtraCat('${rowId}', 'judul', '📖 Judul Tugas Akhir', 'Ketik judul/topik TA...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700"><span>📖 Judul Tugas Akhir</span></div>
                            <div onclick="selectExtraCat('${rowId}', 'prodi', '🎯 Program Studi & KK', 'Ketik prodi/konsentrasi...', this)" class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700"><span>🎯 Program Studi & KK</span></div>
                        </div>
                    </div>
                    <div class="unified-divider"></div>
                    <div class="flex-1 flex items-center min-w-0 px-1">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                        <input type="text" data-cat="${selectedCat.key}" placeholder="${selectedCat.placeholder}" onkeydown="handleLulusInputKey(event)" class="extra-row-input-lulus w-full text-xs font-semibold bg-transparent border-none focus:outline-hidden text-slate-800 placeholder:text-slate-400">
                    </div>
                </div>
                <button type="button" onclick="removeLulusFilterRow(this)" class="btn-remove-row shrink-0" title="Hapus Filter Ini">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            `;

            container.appendChild(rowDiv);
            if (extraCard) extraCard.classList.add('open');
            updateLulusFilterBadge();

            const newInput = rowDiv.querySelector('input');
            if (newInput) newInput.focus();
        }

        function removeLulusFilterRow(btn) {
            const row = btn.closest('.extra-filter-row-lulus');
            if (row) row.remove();

            const extraCard = document.getElementById('extraRowsCardLulus');
            const remainingRows = document.querySelectorAll('.extra-filter-row-lulus').length;
            if (remainingRows === 0 && extraCard) {
                extraCard.classList.remove('open');
            }

            updateLulusFilterBadge();
            performLulusTableFilter();
        }

        function toggleExtraDropdown(rowId, e) {
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

        function selectExtraCat(rowId, key, label, placeholder, el) {
            const labelEl = document.getElementById('label-' + rowId);
            const inputEl = document.querySelector(`#${rowId} input.extra-row-input-lulus`);
            if (labelEl) labelEl.textContent = label;
            if (inputEl) {
                inputEl.setAttribute('data-cat', key);
                inputEl.placeholder = placeholder;
                inputEl.focus();
            }
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        function resetLulusMultiSearch() {
            const container = document.getElementById('additionalFilterRowsContainerLulus');
            const extraCard = document.getElementById('extraRowsCardLulus');
            if (container) container.innerHTML = '';
            if (extraCard) extraCard.classList.remove('open');

            const mainInput = document.getElementById('inputSearchLulus');
            if (mainInput) mainInput.value = '';

            const btnClear = document.getElementById('btnClearSearchLulus');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }

            updateLulusFilterBadge();
            performLulusTableFilter();
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#dropdownCatWrapper')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            }
            const autoBox = document.getElementById('autocompleteLulus');
            if (autoBox && !e.target.closest('#inputSearchLulus') && !e.target.closest('#autocompleteLulus')) {
                autoBox.classList.add('hidden');
            }
        });

        // Autocomplete Suggestion Logic
        function handleLulusAutocomplete(val) {
            const q = val.trim().toLowerCase();
            const btnClear = document.getElementById('btnClearSearchLulus');
            const autoBox = document.getElementById('autocompleteLulus');
            const autoResults = document.getElementById('autocompleteResultsLulus');

            if (btnClear) {
                if (q.length > 0) {
                    btnClear.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                    btnClear.classList.add('opacity-100', 'scale-100');
                } else {
                    btnClear.classList.remove('opacity-100', 'scale-100');
                    btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                }
            }

            if (q.length < 1 || !window.lulusData || window.lulusData.length === 0) {
                if (autoBox) autoBox.classList.add('hidden');
                return;
            }

            const cat = document.getElementById('mainCategorySelectLulus').value;
            const matches = window.lulusData.filter(item => {
                if (cat === 'nim') return item.nim.toLowerCase().includes(q);
                if (cat === 'nama') return item.nama.toLowerCase().includes(q);
                if (cat === 'judul') return item.judul.toLowerCase().includes(q);
                if (cat === 'prodi') return item.prodi.toLowerCase().includes(q);
                return item.nim.toLowerCase().includes(q) || item.nama.toLowerCase().includes(q) || item.judul.toLowerCase().includes(q) || item.prodi.toLowerCase().includes(q);
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
                    <div class="autocomplete-item-row flex items-center justify-between gap-3 p-3 hover:bg-emerald-50 transition cursor-pointer" onclick="selectAutocompleteItem('${m.nama.replace(/'/g, "\\'")}')">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-xs">${highlightedName}</p>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">${highlightedNim} • ${m.prodi}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px] shrink-0">Pilih</span>
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

        function selectAutocompleteItem(val) {
            const input = document.getElementById('inputSearchLulus');
            if (input) input.value = val;
            const autoBox = document.getElementById('autocompleteLulus');
            if (autoBox) autoBox.classList.add('hidden');
            performLulusTableFilter();
        }

        // Keydown Handler (Manual Enter trigger)
        function handleLulusInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const autoBox = document.getElementById('autocompleteLulus');
                if (autoBox) autoBox.classList.add('hidden');
                performLulusTableFilter();
            }
        }

        function executeLulusSearch(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const autoBox = document.getElementById('autocompleteLulus');
            if (autoBox) autoBox.classList.add('hidden');
            performLulusTableFilter();
        }

        // Multi-Criteria Combined Table & Mobile Card Filtering
        function performLulusTableFilter() {
            const filters = [];

            // 1. Main filter
            const mainInput = document.getElementById('inputSearchLulus');
            const mainQuery = mainInput ? mainInput.value.trim().toLowerCase() : '';
            const mainCat = document.getElementById('mainCategorySelectLulus').value;
            if (mainQuery) {
                filters.push({ cat: mainCat, q: mainQuery });
            }

            // 2. Extra filters
            document.querySelectorAll('.extra-filter-row-lulus').forEach(row => {
                const inp = row.querySelector('.extra-row-input-lulus');
                if (inp && inp.value.trim()) {
                    filters.push({
                        cat: inp.getAttribute('data-cat') || 'nama',
                        q: inp.value.trim().toLowerCase()
                    });
                }
            });

            // Filter Desktop Rows
            const rows = document.querySelectorAll('.lulus-row');
            let visibleDesktop = 0;

            rows.forEach(row => {
                let isMatch = true;

                if (filters.length > 0) {
                    for (const f of filters) {
                        let fieldVal = '';
                        if (f.cat === 'nim') fieldVal = row.getAttribute('data-nim') || '';
                        else if (f.cat === 'nama') fieldVal = row.getAttribute('data-nama') || '';
                        else if (f.cat === 'judul') fieldVal = row.getAttribute('data-judul') || '';
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
            const cards = document.querySelectorAll('.lulus-card');
            let visibleMobile = 0;

            cards.forEach(card => {
                let isMatch = true;

                if (filters.length > 0) {
                    for (const f of filters) {
                        let fieldVal = '';
                        if (f.cat === 'nim') fieldVal = card.getAttribute('data-nim') || '';
                        else if (f.cat === 'nama') fieldVal = card.getAttribute('data-nama') || '';
                        else if (f.cat === 'judul') fieldVal = card.getAttribute('data-judul') || '';
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
            const noResDesktop = document.getElementById('noResultsDesktopLulus');
            if (noResDesktop) {
                noResDesktop.style.display = (visibleDesktop === 0 && rows.length > 0) ? '' : 'none';
            }

            const noResMobile = document.getElementById('noResultsMobileLulus');
            if (noResMobile) {
                noResMobile.style.display = (visibleMobile === 0 && cards.length > 0) ? '' : 'none';
            }

            const tableBadge = document.getElementById('tableCountBadge');
            if (tableBadge) tableBadge.textContent = `(${visibleDesktop} data)`;
        }

        function clearLulusSearch() {
            const input = document.getElementById('inputSearchLulus');
            if (input) {
                input.value = '';
                input.focus();
            }
            const btnClear = document.getElementById('btnClearSearchLulus');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }
            const autoBox = document.getElementById('autocompleteLulus');
            if (autoBox) autoBox.classList.add('hidden');
            performLulusTableFilter();
        }
    </script>
</body>
</html>
