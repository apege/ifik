<?php
/**
 * Pembuatan & Cetak Berita Acara Pelaksanaan Sidang (BAP) - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BAP Sidang — Admin LAA'; ?> - IFIK</title>

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

    <main id="mainBapContainer" class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto transition-all duration-300">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">BAP Sidang</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-rose-600 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0">
                            <i class="bi bi-file-earmark-text-fill text-lg sm:text-xl"></i>
                        </span>
                        <span>Pengajuan BAP Sidang Tugas Akhir</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Daftar dokumen BAP IGrACIAS dan Berita Acara Resmi Fakultas Industri Kreatif beserta nilai evaluasi sidang.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="px-3.5 py-2 rounded-xl bg-orange-50 text-orange-800 text-xs font-extrabold uppercase tracking-wider border border-orange-200 shadow-xs flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-orange-600"></i> Total: <?= count($list ?? []); ?> BAP
                    </span>
                </div>
            </div>
        </div>

        <!-- Unified Multi-Search Bar (4 Filters + Autocomplete) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6 relative">
            <form action="<?= site_url('adminlayanan/bap_sidang'); ?>" method="GET" id="formSearchBap" onsubmit="executeBapSearch(event)" class="relative">
                <input type="hidden" name="cat" id="mainCategorySelectBap" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status ?? 'all'); ?>">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <div class="relative custom-dropdown-container shrink-0" id="dropdownCatWrapperBap">
                            <?php
                                $catLabels = [
                                    'query'   => '🔍 Kata Kunci (Semua)',
                                    'nama'    => '🏷️ Peserta Sidang',
                                    'nim'     => '🆔 NIM Mahasiswa',
                                    'prodi'   => '🎯 Prodi / Peminatan',
                                    'dosen'   => '👨‍🏫 Pembimbing / Penguji',
                                    'status'  => '📑 Status Dokumen'
                                ];
                                $curLabel = $catLabels[$cat ?? 'query'] ?? '🔍 Kata Kunci (Semua)';
                            ?>
                            <button type="button" onclick="toggleBapCatDropdown(event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                                <span id="labelCatBap" class="truncate max-w-[120px] sm:max-w-[170px]">${curLabel}</span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrowCatBap"></i>
                            </button>
                            <div id="menuCatBap" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                <div onclick="selectBapMainCategory('query', '🔍 Kata Kunci (Semua)')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? 'query') === 'query' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectBapMainCategory('nama', '🏷️ Peserta Sidang')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'nama' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🏷️ Peserta Sidang</span></div>
                                <div onclick="selectBapMainCategory('nim', '🆔 NIM Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'nim' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectBapMainCategory('prodi', '🎯 Prodi / Peminatan')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'prodi' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🎯 Prodi / Peminatan</span></div>
                                <div onclick="selectBapMainCategory('dosen', '👨‍🏫 Pembimbing / Penguji')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'dosen' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>👨‍🏫 Pembimbing / Penguji</span></div>
                                <div onclick="selectBapMainCategory('status', '📑 Status Dokumen')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'status' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>📑 Status Dokumen</span></div>
                            </div>
                        </div>

                        <div class="unified-divider shrink-0"></div>

                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchBap" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Ketik kata kunci lalu tekan Enter..." 
                                   oninput="handleBapAutocomplete(this.value)"
                                   onkeydown="handleBapInputKey(event)"
                                   class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
                            
                            <button type="button" id="btnClearSearchBap" onclick="clearBapSearch()" class="<?= empty($search) ? 'opacity-0 scale-75 pointer-events-none' : 'opacity-100 scale-100'; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-1 cursor-pointer shrink-0 transition-all transform" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark text-sm"></i>
                            </button>
                        </div>

                        <button type="button" onclick="executeBapSearch(event)" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer active:scale-95 shrink-0 ml-1">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>

                    <button type="button" id="standaloneAddBtnBap" onclick="toggleBapMultiFilter(event)" class="btn-standalone-add shrink-0 justify-center w-full sm:w-auto" title="Tambah Kriteria Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgeBap" class="badge-standalone-count">1/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown Box -->
                <div id="autocompleteBap" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsBap" class="divide-y divide-slate-100 text-xs"></div>
                </div>

                <!-- Extra Filter Rows Container -->
                <div id="extraRowsCardBap" class="extra-rows-card space-y-2.5">
                    <div id="additionalFilterRowsContainerBap" class="space-y-2.5"></div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian. Tekan Enter / Cari.</span>
                        <button type="button" onclick="resetBapMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold cursor-pointer self-start sm:self-auto">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table View: BAP Sidang (Matching Photo 3 & 4) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-file-earmark-check text-rose-600"></i> Daftar Berita Acara Sidang (BAP)
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">(<?= count($list ?? []); ?> data)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Peserta Sidang</th>
                            <th class="py-3.5 px-4">Prodi / Peminatan</th>
                            <th class="py-3.5 px-5">Dosen Pembimbing</th>
                            <th class="py-3.5 px-5">Dosen Penguji</th>
                            <th class="py-3.5 px-4">Dokumen</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-slate-700">Belum Ada Pengajuan BAP Sidang</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $r): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    
                                    <!-- Peserta Sidang -->
                                    <td class="py-4 px-5">
                                        <div class="font-mono text-orange-600 text-[11px] font-bold"><?= htmlspecialchars($r['nim']); ?></div>
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($r['nama']); ?></div>
                                    </td>

                                    <!-- Prodi / Peminatan -->
                                    <td class="py-4 px-4 text-slate-600">
                                        <div><?= htmlspecialchars($r['prodi']); ?> /</div>
                                        <div class="text-[11px] text-slate-400"><?= htmlspecialchars($r['konsentrasi']); ?></div>
                                    </td>

                                    <!-- Dosen Pembimbing -->
                                    <td class="py-4 px-5 space-y-1">
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Pembimbing 1 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['pembimbing_1']); ?></span>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Pembimbing 2 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['pembimbing_2']); ?></span>
                                        </div>
                                    </td>

                                    <!-- Dosen Penguji -->
                                    <td class="py-4 px-5 space-y-1">
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Penguji 1 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['penguji_1']); ?></span>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Penguji 2 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['penguji_2']); ?></span>
                                        </div>
                                    </td>

                                    <!-- Dokumen BAP -->
                                    <td class="py-4 px-4 font-mono text-[11px] space-y-1">
                                        <div class="flex items-center gap-1.5 text-rose-600 font-bold">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <span class="truncate max-w-[140px]"><?= htmlspecialchars($r['dokumen_bap']); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-blue-600 font-bold">
                                            <i class="bi bi-file-earmark-check"></i>
                                            <span class="truncate max-w-[140px]">BAP_Fakultas_<?= htmlspecialchars($r['nim']); ?>.pdf</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-4 text-center space-y-1">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 block">
                                            <?= htmlspecialchars($r['status_igracias']); ?>
                                        </span>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 block">
                                            <?= htmlspecialchars($r['status_fakultas']); ?>
                                        </span>
                                    </td>

                                    <!-- Action: 2 Distinct Buttons (Matching Photo 3 & 4) -->
                                    <td class="py-4 px-4 text-center space-y-1.5">
                                        <!-- Tombol Dokumen 1: BAP IGrACIAS (Foto 4) -->
                                        <button type="button" 
                                                id="btn_bap_igracias_<?= $r['nim']; ?>"
                                                onclick="toggleBapPopup('<?= $r['nim']; ?>', '<?= htmlspecialchars($r['nama'], ENT_QUOTES); ?>', 'igracias')"
                                                class="btn-bap-action w-full px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1 cursor-pointer">
                                            <i class="bi bi-file-earmark-text-fill"></i> <span>BAP (IGRACIAS)</span>
                                        </button>

                                        <!-- Tombol Dokumen 2: BAP FAKULTAS (Foto 5 & 6) -->
                                        <button type="button" 
                                                id="btn_bap_fakultas_<?= $r['nim']; ?>"
                                                onclick="toggleBapPopup('<?= $r['nim']; ?>', '<?= htmlspecialchars($r['nama'], ENT_QUOTES); ?>', 'fakultas')"
                                                class="btn-bap-action w-full px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1 cursor-pointer">
                                            <i class="bi bi-award-fill"></i> <span>BAP FAKULTAS</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <!-- Floating Non-Blocking Container: Preview Dokumen BAP (Multiple Floating Windows - Left Docked Vertical Stack) -->
    <div id="floatingBapContainer" class="fixed bottom-0 left-16 sm:left-20 top-16 pointer-events-none z-[100000] p-3 sm:p-5 flex flex-col items-start justify-start gap-4 overflow-y-auto max-h-[calc(100vh-4rem)] scroll-smooth" style="display: none; max-width: 65vw;">
        <!-- Dynamic Floating Windows will be rendered here -->
    </div>

    <script>
        window.bapData = <?= json_encode($list ?? []); ?>;

        const extraCategoriesBap = [
            { key: 'nama',   label: '🏷️ Peserta Sidang', placeholder: 'Ketik nama peserta...' },
            { key: 'nim',    label: '🆔 NIM Mahasiswa',  placeholder: 'Ketik NIM mahasiswa...' },
            { key: 'prodi',  label: '🎯 Prodi / Peminatan', placeholder: 'Ketik Prodi atau peminatan...' },
            { key: 'dosen',  label: '👨‍🏫 Pembimbing / Penguji', placeholder: 'Ketik nama pembimbing / penguji...' },
            { key: 'status', label: '📑 Status Dokumen', placeholder: 'Ketik status BAP...' }
        ];

        function toggleBapCatDropdown(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menuCatBap');
            const arrow = document.getElementById('arrowCatBap');
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

        function selectBapMainCategory(key, label) {
            document.getElementById('mainCategorySelectBap').value = key;
            document.getElementById('labelCatBap').textContent = label;
            document.querySelectorAll('#menuCatBap .dropdown-item').forEach(el => {
                el.classList.remove('bg-orange-50', 'text-orange-700', 'font-bold');
                el.classList.add('text-slate-700');
            });
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('bg-orange-50', 'text-orange-700', 'font-bold');
                event.currentTarget.classList.remove('text-slate-700');
            }
            document.getElementById('menuCatBap').classList.add('hidden');
            const arrow = document.getElementById('arrowCatBap');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
            document.getElementById('inputSearchBap').focus();
        }

        function toggleBapMultiFilter(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('additionalFilterRowsContainerBap');
            const extraCard = document.getElementById('extraRowsCardBap');
            const currentRows = container.querySelectorAll('.extra-filter-row-bap').length;

            if (currentRows >= 3) {
                alert('Maksimal 4 kriteria filter pencarian.');
                return;
            }

            extraCard.classList.add('open');
            const rowIdx = currentRows + 1;
            const defCat = extraCategoriesBap[rowIdx % extraCategoriesBap.length];
            const rowId = 'filterRowBap_' + Date.now();

            const rowHtml = `
                <div class="extra-filter-row-bap flex items-center gap-2" id="${rowId}">
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <div class="relative custom-dropdown-container shrink-0">
                            <button type="button" onclick="toggleExtraDropdownBap('${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                                <span id="label-${rowId}" class="truncate max-w-[120px] sm:max-w-[170px]">${defCat.label}</span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrow-${rowId}"></i>
                            </button>
                            <div id="menu-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                ${extraCategoriesBap.map(c => `
                                    <div onclick="selectExtraCatBap('${rowId}', '${c.key}', '${c.label}', '${c.placeholder}', this)" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold ${c.key === defCat.key ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'}">
                                        <span>${c.label}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div class="unified-divider shrink-0"></div>
                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" data-cat="${defCat.key}" placeholder="${defCat.placeholder}"
                                   onkeydown="handleBapInputKey(event)"
                                   class="extra-row-input-bap w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
                        </div>
                    </div>
                    <button type="button" onclick="removeBapFilterRow(this)" class="btn-remove-row" title="Hapus filter ini">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', rowHtml);
            updateBapFilterBadge();
            const newInp = document.querySelector(`#${rowId} input.extra-row-input-bap`);
            if (newInp) newInp.focus();
        }

        function updateBapFilterBadge() {
            const count = 1 + document.querySelectorAll('.extra-filter-row-bap').length;
            const badge = document.getElementById('filterCountBadgeBap');
            if (badge) badge.textContent = `${count}/4`;
        }

        function removeBapFilterRow(btn) {
            const row = btn.closest('.extra-filter-row-bap');
            if (row) row.remove();
            const extraCard = document.getElementById('extraRowsCardBap');
            const remainingRows = document.querySelectorAll('.extra-filter-row-bap').length;
            if (remainingRows === 0 && extraCard) {
                extraCard.classList.remove('open');
            }
            updateBapFilterBadge();
        }

        function toggleExtraDropdownBap(rowId, e) {
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

        function selectExtraCatBap(rowId, key, label, placeholder, el) {
            const labelEl = document.getElementById('label-' + rowId);
            const inputEl = document.querySelector(`#${rowId} input.extra-row-input-bap`);
            if (labelEl) labelEl.textContent = label;
            if (inputEl) {
                inputEl.setAttribute('data-cat', key);
                inputEl.placeholder = placeholder;
                inputEl.focus();
            }
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        function resetBapMultiSearch() {
            const container = document.getElementById('additionalFilterRowsContainerBap');
            const extraCard = document.getElementById('extraRowsCardBap');
            if (container) container.innerHTML = '';
            if (extraCard) extraCard.classList.remove('open');

            const mainInput = document.getElementById('inputSearchBap');
            if (mainInput) mainInput.value = '';

            const btnClear = document.getElementById('btnClearSearchBap');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }

            updateBapFilterBadge();
        }

        function clearBapSearch() {
            document.getElementById('inputSearchBap').value = '';
            document.getElementById('autocompleteBap').classList.add('hidden');
            const btnClear = document.getElementById('btnClearSearchBap');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }
        }

        function executeBapSearch(e) {
            if (e && e.preventDefault) e.preventDefault();
            const form = document.getElementById('formSearchBap');
            let q = document.getElementById('inputSearchBap').value.trim();
            let cat = document.getElementById('mainCategorySelectBap').value;

            if (!q) {
                document.querySelectorAll('.extra-row-input-bap').forEach(inp => {
                    if (!q && inp.value.trim()) {
                        q = inp.value.trim();
                        cat = inp.getAttribute('data-cat') || cat;
                    }
                });
            }

            if (q) {
                document.getElementById('inputSearchBap').value = q;
                document.getElementById('mainCategorySelectBap').value = cat;
            }
            form.submit();
        }

        function handleBapInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeBapSearch(e);
            }
            if (e.key === 'Escape') {
                document.getElementById('autocompleteBap').classList.add('hidden');
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            }
        }

        // Autocomplete Suggestion Logic
        function handleBapAutocomplete(val) {
            const q = val.trim().toLowerCase();
            const btnClear = document.getElementById('btnClearSearchBap');
            const autoBox = document.getElementById('autocompleteBap');
            const autoResults = document.getElementById('autocompleteResultsBap');

            if (btnClear) {
                if (q.length > 0) {
                    btnClear.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                    btnClear.classList.add('opacity-100', 'scale-100');
                } else {
                    btnClear.classList.remove('opacity-100', 'scale-100');
                    btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                }
            }

            if (q.length < 1 || !window.bapData || window.bapData.length === 0) {
                if (autoBox) autoBox.classList.add('hidden');
                return;
            }

            const cat = document.getElementById('mainCategorySelectBap').value;
            const matches = window.bapData.filter(item => {
                if (cat === 'nim')    return (item.nim || '').toLowerCase().includes(q);
                if (cat === 'nama')   return (item.nama || '').toLowerCase().includes(q);
                if (cat === 'prodi')  return (item.prodi || '').toLowerCase().includes(q) || (item.peminatan || '').toLowerCase().includes(q);
                if (cat === 'dosen')  return (item.pembimbing_1 || '').toLowerCase().includes(q) || (item.penguji_1 || '').toLowerCase().includes(q);
                if (cat === 'status') return (item.status_bap || '').toLowerCase().includes(q);
                return (item.nim || '').toLowerCase().includes(q) || (item.nama || '').toLowerCase().includes(q) || (item.prodi || '').toLowerCase().includes(q);
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
                <div class="autocomplete-item-row flex items-center justify-between" onclick="selectBapAutocomplete('${item.nama}')">
                    <div class="min-w-0 pr-2">
                        <span class="font-bold text-slate-800 block truncate">${highlight(item.nama)}</span>
                        <span class="text-[11px] text-slate-400 block truncate">${highlight(item.prodi || '')} · Penguji 1: ${highlight(item.penguji_1 || '-')}</span>
                    </div>
                    <span class="font-mono text-orange-600 font-bold text-xs shrink-0">${highlight(item.nim)}</span>
                </div>
            `).join('');

            autoBox.classList.remove('hidden');
        }

        function selectBapAutocomplete(keyword) {
            document.getElementById('inputSearchBap').value = keyword;
            document.getElementById('autocompleteBap').classList.add('hidden');
            document.getElementById('formSearchBap').submit();
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#dropdownCatWrapperBap')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            }
            const autoBox = document.getElementById('autocompleteBap');
            if (autoBox && !e.target.closest('#inputSearchBap') && !e.target.closest('#autocompleteBap')) {
                autoBox.classList.add('hidden');
            }
        });

        // =====================================================================
        // MULTIPLE FLOATING PREVIEW BAP SYSTEM (TRUE CONCURRENT MULTI-WINDOW)
        // =====================================================================
        window.activeBapPopups = []; // [ { id, nim, nama, type, isMinimized, customLeft, customTop } ]

        function updateBapLayoutSplitView() {
            const mainContainer = document.getElementById('mainBapContainer');
            if (!mainContainer) return;
            mainContainer.style.maxWidth = '';
            mainContainer.style.marginLeft = '';
            mainContainer.style.marginRight = '';
        }

        window.addEventListener('resize', updateBapLayoutSplitView);

        function toggleBapPopup(nim, nama, type) {
            const popupId = 'bap_' + type + '_' + nim;
            const existingIdx = window.activeBapPopups.findIndex(p => p.id === popupId);

            if (existingIdx > -1) {
                // If already open, toggle close
                closeBapPopup(popupId);
            } else {
                // Open a NEW separate floating card side-by-side!
                window.activeBapPopups.push({
                    id: popupId,
                    nim: nim,
                    nama: nama,
                    type: type,
                    isMinimized: false,
                    customLeft: null,
                    customTop: null
                });
                renderBapFloatingPopups();
                highlightBapCard(popupId);
                updateBapTableButtonHighlights();
                updateBapLayoutSplitView();
            }
        }

        function openBapIgracias(nim, nama) {
            toggleBapPopup(nim, nama, 'igracias');
        }

        function openBapFakultas(nim, nama) {
            toggleBapPopup(nim, nama, 'fakultas');
        }

        function switchBapType(id, newType) {
            const p = window.activeBapPopups.find(item => item.id === id);
            if (p) {
                p.type = newType;
                p.id = 'bap_' + newType + '_' + p.nim;
                renderBapFloatingPopups();
                highlightBapCard(p.id);
                updateBapTableButtonHighlights();
                updateBapLayoutSplitView();
            }
        }

        function toggleMinimizeBap(id) {
            const popup = window.activeBapPopups.find(p => p.id === id);
            if (popup) {
                popup.isMinimized = !popup.isMinimized;
                renderBapFloatingPopups();
                updateBapLayoutSplitView();
            }
        }

        function dockBapCard(id, position) {
            const popup = window.activeBapPopups.find(p => p.id === id);
            const card = document.getElementById('bapCard_' + id);
            if (!popup || !card) return;

            card.style.position = 'fixed';
            card.style.bottom = '16px';
            card.style.top = 'auto';

            if (position === 'left') {
                card.style.left = '20px';
                card.style.right = 'auto';
                popup.customLeft = 20;
                popup.customTop = null;
            } else {
                card.style.right = '20px';
                card.style.left = 'auto';
                popup.customLeft = null;
                popup.customTop = null;
            }
        }

        function closeBapPopup(id) {
            window.activeBapPopups = window.activeBapPopups.filter(p => p.id !== id);
            renderBapFloatingPopups();
            updateBapTableButtonHighlights();
            updateBapLayoutSplitView();
        }

        function closeAllBapPopups() {
            window.activeBapPopups = [];
            renderBapFloatingPopups();
            updateBapTableButtonHighlights();
            updateBapLayoutSplitView();
        }

        function highlightBapCard(id) {
            setTimeout(() => {
                const el = document.getElementById('bapCard_' + id);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', inline: 'end', block: 'nearest' });
                    el.classList.add('ring-4', 'ring-orange-400', 'scale-[1.01]');
                    setTimeout(() => el.classList.remove('ring-4', 'ring-orange-400', 'scale-[1.01]'), 800);
                }
            }, 50);
        }

        function updateBapTableButtonHighlights() {
            document.querySelectorAll('.btn-bap-action').forEach(btn => {
                const isIgraciasBtn = btn.id.startsWith('btn_bap_igracias_');
                const nim = btn.id.replace('btn_bap_igracias_', '').replace('btn_bap_fakultas_', '');
                const targetType = isIgraciasBtn ? 'igracias' : 'fakultas';
                const isActive = window.activeBapPopups.some(p => p.nim === nim && p.type === targetType);

                if (isActive) {
                    btn.classList.add('ring-2', 'ring-orange-400', 'scale-105', 'shadow-md', 'bg-orange-600');
                    btn.innerHTML = `<i class="bi bi-x-circle-fill"></i> <span>Tutup ${isIgraciasBtn ? 'BAP (IGRACIAS)' : 'BAP FAKULTAS'}</span>`;
                } else {
                    btn.classList.remove('ring-2', 'ring-orange-400', 'scale-105', 'shadow-md', 'bg-orange-600');
                    if (isIgraciasBtn) {
                        btn.className = 'btn-bap-action w-full px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1 cursor-pointer';
                        btn.innerHTML = '<i class="bi bi-file-earmark-text-fill"></i> <span>BAP (IGRACIAS)</span>';
                    } else {
                        btn.className = 'btn-bap-action w-full px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1 cursor-pointer';
                        btn.innerHTML = '<i class="bi bi-award-fill"></i> <span>BAP FAKULTAS</span>';
                    }
                }
            });
        }

        function renderBapFloatingPopups() {
            const container = document.getElementById('floatingBapContainer');
            if (!container) return;

            if (window.activeBapPopups.length === 0) {
                container.style.display = 'none';
                container.innerHTML = '';
                return;
            }

            container.style.display = 'flex';
            const total = window.activeBapPopups.length;

            let html = '';
            
            // Header summary pill if > 1 popup
            if (total > 1) {
                html += `
                    <div class="pointer-events-auto shrink-0 self-start mb-1 bg-slate-900/95 backdrop-blur text-white px-3.5 py-1.5 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold">${total} Dokumen BAP Terbuka</span>
                        <button type="button" onclick="closeAllBapPopups()" class="ml-1 px-2 py-0.5 rounded-lg bg-rose-600/80 hover:bg-rose-600 text-white font-bold text-[10px] transition cursor-pointer">
                            Tutup Semua
                        </button>
                    </div>
                `;
            }

            window.activeBapPopups.forEach(p => {
                const isIgracias = p.type === 'igracias';
                const previewUrl = isIgracias 
                    ? '<?= site_url('adminlayanan/preview_bap_igracias/'); ?>' + p.nim 
                    : '<?= site_url('adminlayanan/preview_bap_fakultas/'); ?>' + p.nim;
                const printUrl = isIgracias 
                    ? '<?= site_url('adminlayanan/cetak_bap_igracias/'); ?>' + p.nim 
                    : '<?= site_url('adminlayanan/cetak_bap_fakultas/'); ?>' + p.nim;
                const title = isIgracias ? 'BAP IGrACIAS — ' + p.nama : 'BAP Fakultas — ' + p.nama;
                const sub = isIgracias ? 'Dokumen 1: Berita Acara Sidang (IGrACIAS)' : 'Dokumen 2: Berita Acara & Nilai FIK Tel-U';
                const badgeColor = isIgracias ? 'bg-orange-500/20 text-orange-400 border border-orange-500/40' : 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40';
                const icon = isIgracias ? 'bi bi-file-earmark-text-fill' : 'bi bi-award-fill';

                const posStyle = '';

                if (p.isMinimized) {
                    html += `
                        <div id="bapCard_${p.id}" style="${posStyle}" class="pointer-events-auto bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700 shrink-0 w-72 p-2.5 flex items-center justify-between gap-2 transition-all hover:border-orange-500 cursor-pointer" onclick="toggleMinimizeBap('${p.id}')">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-lg ${badgeColor} flex items-center justify-center text-xs shrink-0">
                                    <i class="${icon}"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold truncate max-w-[150px]">${title}</h4>
                                    <p class="text-[9px] text-slate-400 truncate">${p.nim}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                <button type="button" onclick="toggleMinimizeBap('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Perbesar">
                                    <i class="bi bi-arrows-angle-expand text-[10px]"></i>
                                </button>
                                <button type="button" onclick="closeBapPopup('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs transition ml-0.5 cursor-pointer" title="Tutup">
                                    <i class="bi bi-x-lg text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div id="bapCard_${p.id}" style="${posStyle}" class="pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-300 flex flex-col overflow-hidden shrink-0 transition-shadow duration-200 w-[92vw] sm:w-[480px] xl:w-[520px] h-[560px] max-h-[78vh]">
                            <!-- Static Header -->
                            <div id="bapHeader_${p.id}" class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800 select-none">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-6 h-6 rounded-lg ${badgeColor} flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="${icon} text-[11px]"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-white truncate max-w-[170px] sm:max-w-[210px]">${title}</h4>
                                        <p class="text-[10px] text-slate-300 truncate">${sub}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                    <a href="${printUrl}" target="_blank" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka Cetak / PDF Layar Penuh">
                                        <i class="bi bi-arrow-up-right-square text-[10px]"></i>
                                    </a>
                                    <button type="button" onclick="toggleMinimizeBap('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Minimize Dokumen">
                                        <i class="bi bi-dash-lg text-[10px] font-bold"></i>
                                    </button>
                                    <button type="button" onclick="closeBapPopup('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition ml-0.5 cursor-pointer" title="Tutup">
                                        <i class="bi bi-x-lg text-[10px]"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Fast Switcher Pill Strip ("Tek Tek" BAP Switcher) -->
                            <div class="bg-slate-950 p-1.5 px-3 flex items-center gap-2 border-b border-slate-800 shrink-0">
                                <button type="button" 
                                        onclick="switchBapType('${p.id}', 'igracias')"
                                        class="flex-1 py-1 px-2.5 rounded-lg text-[10.5px] font-bold flex items-center justify-center gap-1.5 transition cursor-pointer ${isIgracias ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white'}">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                    <span>1. BAP (IGRACIAS)</span>
                                </button>
                                <button type="button" 
                                        onclick="switchBapType('${p.id}', 'fakultas')"
                                        class="flex-1 py-1 px-2.5 rounded-lg text-[10.5px] font-bold flex items-center justify-center gap-1.5 transition cursor-pointer ${!isIgracias ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white'}">
                                    <i class="bi bi-award-fill"></i>
                                    <span>2. BAP FAKULTAS</span>
                                </button>
                            </div>

                            <!-- Window Body / Iframe -->
                            <div class="flex-1 bg-slate-100 p-1.5 overflow-hidden flex flex-col relative">
                                <iframe src="${previewUrl}" class="w-full h-full bg-white rounded-2xl shadow-inner border border-slate-200" frameborder="0"></iframe>
                            </div>

                            <!-- Window Footer -->
                            <div class="p-2 px-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs shrink-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="font-mono text-[11px] font-bold text-orange-600">${p.nim}</span>
                                    <span class="text-slate-400 text-[11px]">|</span>
                                    <span class="text-[11px] text-slate-600 truncate font-semibold">${p.nama}</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="${printUrl}" target="_blank" class="px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 cursor-pointer">
                                        <i class="bi bi-printer-fill text-[10px]"></i> Cetak PDF
                                    </a>
                                    <button type="button" onclick="closeBapPopup('${p.id}')" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[11px] transition cursor-pointer">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            container.innerHTML = html;
            initDraggableBapCards();
        }

        function initDraggableBapCards() {}
    </script>
</body>
</html>
