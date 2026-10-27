<?php
/**
 * Direktori Dosen Pembimbing, Kuota, & Beban Bimbingan Aktif - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Lihat Pembimbing — Admin LAA'; ?> - IFIK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 900: '#7c2d12'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .unified-search-pill {
            display: flex; align-items: center;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0; border-radius: 16px;
            padding: 3px 14px; height: 46px;
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important; background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider { width: 1px; height: 22px; background: #e2e8f0; margin: 0 8px; }
        .autocomplete-box {
            max-height: 320px; overflow-y: auto; border-radius: 18px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2), 0 8px 24px -4px rgba(234, 88, 12, 0.15);
        }
        .autocomplete-item-row { padding: 10px 16px; transition: all 0.18s ease; cursor: pointer; }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav { background-color: #fff7ed; color: #ea580c; }
        .autocomplete-item-row mark { background: #fed7aa; color: #c2410c; font-weight: 800; border-radius: 4px; padding: 0 3px; }

        .btn-standalone-add {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 16px;
            padding: 6px 14px; height: 46px; font-size: 0.8rem; font-weight: 700; color: #ea580c;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); white-space: nowrap;
        }
        .btn-standalone-add:hover { background: #ffedd5; border-color: #fdba74; transform: scale(1.02); }
        .badge-standalone-count {
            background: #ea580c; color: #ffffff; font-size: 0.72rem; font-weight: 800; padding: 1.5px 8px; border-radius: 99px;
        }
        .btn-remove-row {
            display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px;
            background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 14px; color: #e11d48;
            cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;
        }
        .btn-remove-row:hover { background: #ffe4e6; border-color: #fda4af; color: #be123c; transform: scale(1.05); }
        .extra-rows-card {
            display: none; position: relative; margin-top: 12px; background: #fafafa;
            border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 14px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .extra-rows-card.open { display: block !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Sidebar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Lihat Pembimbing</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                            <i class="bi bi-people text-lg sm:text-xl"></i>
                        </span>
                        <span>Rekapitulasi Dosen Pembimbing TA</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Daftar lengkap alokasi Dosen Pembimbing 1 dan Dosen Pembimbing 2 seluruh mahasiswa Tugas Akhir.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <!-- Export to Excel Button -->
                    <a href="<?= site_url('adminlayanan/export_pembimbing'); ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition flex items-center gap-2">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i> Export ke Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Unified Multi-Search Bar (4 Filters + Autocomplete) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6 relative">
            <form action="<?= site_url('adminlayanan/lihat_pembimbing'); ?>" method="GET" id="formSearchPembimbing" onsubmit="executePembimbingSearch(event)" class="relative">
                <input type="hidden" name="cat" id="mainCategorySelectPembimbing" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                <input type="hidden" name="prodi" value="<?= htmlspecialchars($filter_prodi ?? 'all'); ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status ?? 'all'); ?>">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <div class="relative custom-dropdown-container shrink-0" id="dropdownCatWrapperPembimbing">
                            <?php
                                $catLabels = [
                                    'query'   => '🔍 Kata Kunci (Semua)',
                                    'nama'    => '🏷️ Nama Mahasiswa',
                                    'nim'     => '🆔 NIM Mahasiswa',
                                    'prodi'   => '🎯 Program Studi & KK',
                                    'dosen'   => '👨‍🏫 Dosen Pembimbing',
                                    'tahapan' => '📌 Tahapan / Status'
                                ];
                                $curLabel = $catLabels[$cat ?? 'query'] ?? '🔍 Kata Kunci (Semua)';
                            ?>
                            <button type="button" onclick="togglePembimbingCatDropdown(event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                                <span id="labelCatPembimbing" class="truncate max-w-[120px] sm:max-w-[170px]"><?= $curLabel; ?></span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrowCatPembimbing"></i>
                            </button>
                            <div id="menuCatPembimbing" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                <div onclick="selectPembimbingMainCategory('query', '🔍 Kata Kunci (Semua)')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? 'query') === 'query' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectPembimbingMainCategory('nama', '🏷️ Nama Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'nama' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectPembimbingMainCategory('nim', '🆔 NIM Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'nim' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectPembimbingMainCategory('prodi', '🎯 Program Studi & KK')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'prodi' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🎯 Program Studi & KK</span></div>
                                <div onclick="selectPembimbingMainCategory('dosen', '👨‍🏫 Dosen Pembimbing')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'dosen' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>👨‍🏫 Dosen Pembimbing</span></div>
                                <div onclick="selectPembimbingMainCategory('tahapan', '📌 Tahapan / Status')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'tahapan' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>📌 Tahapan / Status</span></div>
                            </div>
                        </div>

                        <div class="unified-divider shrink-0"></div>

                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchPembimbing" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Ketik kata kunci lalu tekan Enter..." 
                                   oninput="handlePembimbingAutocomplete(this.value)"
                                   onkeydown="handlePembimbingInputKey(event)"
                                   class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
                            
                            <button type="button" id="btnClearSearchPembimbing" onclick="clearPembimbingSearch()" class="<?= empty($search) ? 'opacity-0 scale-75 pointer-events-none' : 'opacity-100 scale-100'; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-1 cursor-pointer shrink-0 transition-all transform" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark text-sm"></i>
                            </button>
                        </div>

                        <button type="button" onclick="executePembimbingSearch(event)" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer active:scale-95 shrink-0 ml-1">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>

                    <button type="button" id="standaloneAddBtnPembimbing" onclick="togglePembimbingMultiFilter(event)" class="btn-standalone-add shrink-0 justify-center w-full sm:w-auto" title="Tambah Kriteria Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgePembimbing" class="badge-standalone-count">1/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown Box -->
                <div id="autocompletePembimbing" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsPembimbing" class="divide-y divide-slate-100 text-xs"></div>
                </div>

                <!-- Extra Filter Rows Container -->
                <div id="extraRowsCardPembimbing" class="extra-rows-card space-y-2.5">
                    <div id="additionalFilterRowsContainerPembimbing" class="space-y-2.5"></div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian. Tekan Enter / Cari.</span>
                        <button type="button" onclick="resetPembimbingMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold cursor-pointer self-start sm:self-auto">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table View: Rekap Pembimbing -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-person-lines-fill text-orange-600"></i> Data Alokasi Pembimbing
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">(<?= count($list ?? []); ?> Mahasiswa)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-2 text-center w-10">No</th>
                            <th class="py-3 px-3">Nama Mahasiswa</th>
                            <th class="py-3 px-2.5">Prodi</th>
                            <th class="py-3 px-2.5">Konsentrasi</th>
                            <th class="py-3 px-2.5">Dosen Wali</th>
                            <th class="py-3 px-2.5">Pembimbing 1</th>
                            <th class="py-3 px-2.5">Pembimbing 2</th>
                            <th class="py-3 px-2 text-center whitespace-nowrap">Tgl Approve</th>
                            <th class="py-3 px-2.5 text-center whitespace-nowrap">Tahapan</th>
                            <th class="py-3 px-2.5 text-center whitespace-nowrap">Jenis TA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium">
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-slate-700">Tidak Ada Data Pembimbing</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $r): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-2 text-center font-bold text-slate-400"><?= $r['no']; ?></td>
                                    <td class="py-3.5 px-3 font-bold text-slate-800">
                                        <div><?= htmlspecialchars($r['nama']); ?></div>
                                        <div class="font-mono text-orange-600 text-[10.5px]"><?= htmlspecialchars($r['nim']); ?></div>
                                    </td>
                                    <td class="py-3.5 px-2.5 text-slate-600 text-[11.5px]"><?= htmlspecialchars($r['prodi']); ?></td>
                                    <td class="py-3.5 px-2.5 text-slate-500 text-[11.5px]"><?= htmlspecialchars($r['konsentrasi']); ?></td>
                                    <td class="py-3.5 px-2.5 text-slate-600 text-[11.5px]"><?= htmlspecialchars($r['dosen_wali']); ?></td>
                                    <td class="py-3.5 px-2.5 font-semibold text-slate-800 text-[11.5px]"><?= htmlspecialchars($r['pembimbing_1']); ?></td>
                                    <td class="py-3.5 px-2.5 font-semibold text-slate-800 text-[11.5px]"><?= htmlspecialchars($r['pembimbing_2']); ?></td>
                                    <td class="py-3.5 px-2 text-center text-slate-500 font-mono text-[10.5px] whitespace-nowrap"><?= htmlspecialchars($r['tanggal_approve']); ?></td>
                                    <td class="py-3.5 px-2.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap">
                                            <?= htmlspecialchars($r['tahapan']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-2.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 whitespace-nowrap">
                                            <?= htmlspecialchars($r['jenis_ta']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <script>
        window.pembimbingData = <?= json_encode($list ?? []); ?>;

        const extraCategoriesPembimbing = [
            { key: 'nama',    label: '🏷️ Nama Mahasiswa', placeholder: 'Ketik nama mahasiswa...' },
            { key: 'nim',     label: '🆔 NIM Mahasiswa',  placeholder: 'Ketik NIM mahasiswa...' },
            { key: 'prodi',   label: '🎯 Program Studi & KK', placeholder: 'Ketik Prodi atau KK...' },
            { key: 'dosen',   label: '👨‍🏫 Dosen Pembimbing', placeholder: 'Ketik nama dosen pembimbing...' },
            { key: 'tahapan', label: '📌 Tahapan / Status', placeholder: 'Ketik tahapan / status...' }
        ];

        function togglePembimbingCatDropdown(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menuCatPembimbing');
            const arrow = document.getElementById('arrowCatPembimbing');
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

        function selectPembimbingMainCategory(key, label) {
            document.getElementById('mainCategorySelectPembimbing').value = key;
            document.getElementById('labelCatPembimbing').textContent = label;
            document.querySelectorAll('#menuCatPembimbing .dropdown-item').forEach(el => {
                el.classList.remove('bg-orange-50', 'text-orange-700', 'font-bold');
                el.classList.add('text-slate-700');
            });
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('bg-orange-50', 'text-orange-700', 'font-bold');
                event.currentTarget.classList.remove('text-slate-700');
            }
            document.getElementById('menuCatPembimbing').classList.add('hidden');
            const arrow = document.getElementById('arrowCatPembimbing');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
            document.getElementById('inputSearchPembimbing').focus();
        }

        function togglePembimbingMultiFilter(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('additionalFilterRowsContainerPembimbing');
            const extraCard = document.getElementById('extraRowsCardPembimbing');
            const currentRows = container.querySelectorAll('.extra-filter-row-pembimbing').length;

            if (currentRows >= 3) {
                alert('Maksimal 4 kriteria filter pencarian.');
                return;
            }

            extraCard.classList.add('open');
            const rowIdx = currentRows + 1;
            const defCat = extraCategoriesPembimbing[rowIdx % extraCategoriesPembimbing.length];
            const rowId = 'filterRowPembimbing_' + Date.now();

            const rowHtml = `
                <div class="extra-filter-row-pembimbing flex items-center gap-2" id="${rowId}">
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <div class="relative custom-dropdown-container shrink-0">
                            <button type="button" onclick="toggleExtraDropdownPembimbing('${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                                <span id="label-${rowId}" class="truncate max-w-[120px] sm:max-w-[170px]">${defCat.label}</span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrow-${rowId}"></i>
                            </button>
                            <div id="menu-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                ${extraCategoriesPembimbing.map(c => `
                                    <div onclick="selectExtraCatPembimbing('${rowId}', '${c.key}', '${c.label}', '${c.placeholder}', this)" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold ${c.key === defCat.key ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'}">
                                        <span>${c.label}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div class="unified-divider shrink-0"></div>
                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" data-cat="${defCat.key}" placeholder="${defCat.placeholder}"
                                   onkeydown="handlePembimbingInputKey(event)"
                                   class="extra-row-input-pembimbing w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
                        </div>
                    </div>
                    <button type="button" onclick="removePembimbingFilterRow(this)" class="btn-remove-row" title="Hapus filter ini">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', rowHtml);
            updatePembimbingFilterBadge();
            const newInp = document.querySelector(`#${rowId} input.extra-row-input-pembimbing`);
            if (newInp) newInp.focus();
        }

        function updatePembimbingFilterBadge() {
            const count = 1 + document.querySelectorAll('.extra-filter-row-pembimbing').length;
            const badge = document.getElementById('filterCountBadgePembimbing');
            if (badge) badge.textContent = `${count}/4`;
        }

        function removePembimbingFilterRow(btn) {
            const row = btn.closest('.extra-filter-row-pembimbing');
            if (row) row.remove();
            const extraCard = document.getElementById('extraRowsCardPembimbing');
            const remainingRows = document.querySelectorAll('.extra-filter-row-pembimbing').length;
            if (remainingRows === 0 && extraCard) {
                extraCard.classList.remove('open');
            }
            updatePembimbingFilterBadge();
        }

        function toggleExtraDropdownPembimbing(rowId, e) {
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

        function selectExtraCatPembimbing(rowId, key, label, placeholder, el) {
            const labelEl = document.getElementById('label-' + rowId);
            const inputEl = document.querySelector(`#${rowId} input.extra-row-input-pembimbing`);
            if (labelEl) labelEl.textContent = label;
            if (inputEl) {
                inputEl.setAttribute('data-cat', key);
                inputEl.placeholder = placeholder;
                inputEl.focus();
            }
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        function resetPembimbingMultiSearch() {
            const container = document.getElementById('additionalFilterRowsContainerPembimbing');
            const extraCard = document.getElementById('extraRowsCardPembimbing');
            if (container) container.innerHTML = '';
            if (extraCard) extraCard.classList.remove('open');

            const mainInput = document.getElementById('inputSearchPembimbing');
            if (mainInput) mainInput.value = '';

            const btnClear = document.getElementById('btnClearSearchPembimbing');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }

            updatePembimbingFilterBadge();
        }

        function clearPembimbingSearch() {
            document.getElementById('inputSearchPembimbing').value = '';
            document.getElementById('autocompletePembimbing').classList.add('hidden');
            const btnClear = document.getElementById('btnClearSearchPembimbing');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }
        }

        function executePembimbingSearch(e) {
            if (e && e.preventDefault) e.preventDefault();
            const form = document.getElementById('formSearchPembimbing');
            let q = document.getElementById('inputSearchPembimbing').value.trim();
            let cat = document.getElementById('mainCategorySelectPembimbing').value;

            if (!q) {
                document.querySelectorAll('.extra-row-input-pembimbing').forEach(inp => {
                    if (!q && inp.value.trim()) {
                        q = inp.value.trim();
                        cat = inp.getAttribute('data-cat') || cat;
                    }
                });
            }

            if (q) {
                document.getElementById('inputSearchPembimbing').value = q;
                document.getElementById('mainCategorySelectPembimbing').value = cat;
            }
            form.submit();
        }

        function handlePembimbingInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                executePembimbingSearch(e);
            }
            if (e.key === 'Escape') {
                document.getElementById('autocompletePembimbing').classList.add('hidden');
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            }
        }

        // Autocomplete Suggestion Logic
        function handlePembimbingAutocomplete(val) {
            const q = val.trim().toLowerCase();
            const btnClear = document.getElementById('btnClearSearchPembimbing');
            const autoBox = document.getElementById('autocompletePembimbing');
            const autoResults = document.getElementById('autocompleteResultsPembimbing');

            if (btnClear) {
                if (q.length > 0) {
                    btnClear.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                    btnClear.classList.add('opacity-100', 'scale-100');
                } else {
                    btnClear.classList.remove('opacity-100', 'scale-100');
                    btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                }
            }

            if (q.length < 1 || !window.pembimbingData || window.pembimbingData.length === 0) {
                if (autoBox) autoBox.classList.add('hidden');
                return;
            }

            const cat = document.getElementById('mainCategorySelectPembimbing').value;
            const matches = window.pembimbingData.filter(item => {
                if (cat === 'nim')   return (item.nim || '').toLowerCase().includes(q);
                if (cat === 'nama')  return (item.nama || '').toLowerCase().includes(q);
                if (cat === 'prodi') return (item.prodi || '').toLowerCase().includes(q);
                if (cat === 'dosen') return (item.pembimbing_1 || '').toLowerCase().includes(q) || (item.pembimbing_2 || '').toLowerCase().includes(q);
                return (item.nim || '').toLowerCase().includes(q) || (item.nama || '').toLowerCase().includes(q) || (item.pembimbing_1 || '').toLowerCase().includes(q);
            }).slice(0, 8);

            if (matches.length === 0) {
                autoBox.classList.add('hidden');
                return;
            }

            const highlight = (str) => {
                if (!str) return '';
                return str.replace(new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'), '<mark>$1</mark>');
            };

            autoResults.innerHTML = matches.map(item => `
                <div class="autocomplete-item-row flex items-center justify-between" onclick="selectPembimbingAutocomplete('${item.nama}')">
                    <div class="min-w-0 pr-2">
                        <span class="font-bold text-slate-800 block truncate">${highlight(item.nama)}</span>
                        <span class="text-[11px] text-slate-400 block truncate">${highlight(item.prodi)} · Pembimbing: ${highlight(item.pembimbing_1 || '-')}</span>
                    </div>
                    <span class="font-mono text-orange-600 font-bold text-xs shrink-0">${highlight(item.nim)}</span>
                </div>
            `).join('');

            autoBox.classList.remove('hidden');
        }

        function selectPembimbingAutocomplete(keyword) {
            document.getElementById('inputSearchPembimbing').value = keyword;
            document.getElementById('autocompletePembimbing').classList.add('hidden');
            document.getElementById('formSearchPembimbing').submit();
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#dropdownCatWrapperPembimbing')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            }
            const autoBox = document.getElementById('autocompletePembimbing');
            if (autoBox && !e.target.closest('#inputSearchPembimbing') && !e.target.closest('#autocompletePembimbing')) {
                autoBox.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
