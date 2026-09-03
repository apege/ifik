<?php
    $total_logs   = $total_logs ?? (isset($logs) ? count($logs) : 0);
    $total_pages  = $total_pages ?? 1;
    $current_page = $current_page ?? ($page ?? 1);
    $filter_modul  = $filter_modul ?? 'all';
    $filter_action = $filter_action ?? 'all';
    $search        = $search ?? '';
?>
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

        .autocomplete-box {
            max-height: 280px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Admin Panel',
        'user_display_name' => 'Super Administrator',
        'user_display_sub'  => 'Audit Trail & Log History'
    ]); ?>

    <!-- Sub Navigation Page Title Bar -->
    <div class="glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg shadow-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Catatan Riwayat (Log History) Approval</h1>
                        <span class="px-2.5 py-0.5 bg-orange-100 text-brand-700 font-extrabold text-[10px] uppercase tracking-wider rounded-full border border-orange-200">
                            Audit Trail System
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Rekam jejak otomatis tanggal, waktu, aktor pelaksana, serta catatan persetujuan & penolakan seluruh modul.</p>
                </div>
            </div>

            <!-- Header Action Right -->
            <div class="flex items-center gap-3">
                <a href="<?= site_url('admin'); ?>" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-200 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-arrow-left text-brand-600"></i> Admin Panel
                </a>
                <a href="<?= site_url('admin/log_history'); ?>" class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-rotate-right"></i> Refresh Data
                </a>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Banner Counter Card -->
        <div class="card-custom p-6 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border-l-4 border-l-brand-600">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-3 py-1 bg-amber-50 text-amber-800 text-xs font-bold rounded-full border border-amber-200/80">
                        <i class="fa-solid fa-shield-halved text-amber-600"></i> System Audit Log
                    </span>
                    <span class="text-xs text-slate-500 font-semibold">Total Record Log: <strong class="text-slate-900 font-extrabold"><?= number_format($total_logs); ?></strong></span>
                </div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Pusat Catatan Riwayat Persetujuan & Penolakan</h2>
                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Seluruh tindakan Approve, Reject, maupun Reset yang dilakukan oleh Dosen Wali, Admin Layanan LAA, Koordinator TA, Ketua KK, dan Admin Peminjaman Ruangan tersimpan otomatis secara real-time.
                </p>
            </div>
        </div>

        <!-- Filter Tab Group for Modul (Samain dengan style Admin LAA) -->
        <div class="card-custom p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0" id="filterTabsContainerModul">
                    <button type="button" onclick="switchModulTab('all')" id="tabModul_all" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_modul === 'all' ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Semua Modul
                    </button>
                    <button type="button" onclick="switchModulTab('Dosen Wali')" id="tabModul_DosenWali" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_modul === 'Dosen Wali' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Dosen Wali
                    </button>
                    <button type="button" onclick="switchModulTab('Admin Layanan')" id="tabModul_AdminLayanan" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_modul === 'Admin Layanan' ? 'bg-orange-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Admin Layanan (LAA)
                    </button>
                    <button type="button" onclick="switchModulTab('Koordinator TA')" id="tabModul_KoordinatorTA" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_modul === 'Koordinator TA' ? 'bg-purple-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Koordinator TA
                    </button>
                    <button type="button" onclick="switchModulTab('Ketua KK')" id="tabModul_KetuaKK" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_modul === 'Ketua KK' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Ketua KK
                    </button>
                    <button type="button" onclick="switchModulTab('Peminjaman Ruangan')" id="tabModul_PeminjamanRuangan" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer <?= $filter_modul === 'Peminjaman Ruangan' ? 'bg-sky-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
                        Peminjaman Ruangan
                    </button>
                </div>
            </div>
        </div>

        <!-- Seamless Integrated Search Bar with Multi-Category Filter (Identik dengan Admin LAA) -->
        <div class="card-custom p-3.5 mb-6 relative">
            <form onsubmit="doLogSearch(); return false;" id="formSearchLog" class="relative">
                <input type="hidden" name="modul" id="inputModulFilter" value="<?= htmlspecialchars($filter_modul); ?>">
                <input type="hidden" name="action" id="inputActionFilter" value="<?= htmlspecialchars($filter_action); ?>">

                <div class="flex items-center gap-2 bg-slate-50 hover:bg-white focus-within:bg-white border border-slate-200 focus-within:border-brand-500/80 rounded-2xl px-3.5 py-2 transition-all shadow-2xs focus-within:shadow-md">
                    
                    <!-- Integrated Category Dropdown (Clean, borderless) -->
                    <div class="flex items-center gap-1 text-slate-500 font-medium shrink-0 border-r border-slate-200 pr-2">
                        <select id="searchCategoryLog" class="bg-transparent text-xs font-bold text-slate-700 focus:outline-none cursor-pointer py-1 pr-1">
                            <option value="ref_id">👤 NIM / Ref ID</option>
                            <option value="target_name">📄 Nama Target</option>
                            <option value="actor">⚙️ Aktor Pelaksana</option>
                            <option value="catatan">📝 Catatan / Alasan</option>
                        </select>
                    </div>

                    <!-- Search Input Field -->
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <input type="text" name="q" id="inputSearchLog" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>" 
                               placeholder="Ketik kata kunci pencarian log..." 
                               onkeydown="if(event.key==='Enter'){doLogSearch();}"
                               class="w-full bg-transparent text-xs text-slate-800 font-semibold focus:outline-none py-1">
                    </div>

                    <!-- Filter Action Selector Dropdown -->
                    <div class="hidden md:flex items-center gap-1 text-slate-500 font-medium shrink-0 border-l border-slate-200 pl-2">
                        <select name="action_select" id="actionSelectLog" onchange="setActionFilter(this.value)" class="bg-transparent text-xs font-bold text-slate-700 focus:outline-none cursor-pointer py-1">
                            <option value="all" <?= ($filter_action === 'all') ? 'selected' : ''; ?>>Semua Aksi</option>
                            <option value="Approved" <?= ($filter_action === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="Rejected" <?= ($filter_action === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                            <option value="Reset" <?= ($filter_action === 'Reset') ? 'selected' : ''; ?>>Reset</option>
                            <option value="Approve All" <?= ($filter_action === 'Approve All') ? 'selected' : ''; ?>>Approve All</option>
                        </select>
                    </div>

                    <!-- Search & Action Buttons -->
                    <div class="flex items-center gap-2 shrink-0 border-l border-slate-200 pl-2">
                        <button type="button" id="btnClearSearchLog" onclick="clearLogSearch()" class="<?= empty($search) ? 'hidden' : ''; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1 cursor-pointer" title="Bersihkan Pencarian">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>

                        <button type="button" id="btnSubmitSearchLog" onclick="doLogSearch()" 
                                title="Cari Data (Tekan Enter atau Klik Cari)"
                                class="flex items-center gap-1.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 active:scale-95 cursor-pointer py-1.5 px-3 rounded-xl transition-all shadow-2xs">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            <span class="font-extrabold">Cari</span>
                        </button>

                        <button type="button" id="btnAddFilterLog" onclick="addCategoryFilterLog()" 
                                title="Tambah ke Filter Kategori (Maks 4)"
                                class="flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-brand-600 cursor-pointer py-1.5 px-2.5 rounded-xl hover:bg-slate-100 transition-all border border-slate-200">
                            <i class="fa-solid fa-plus text-xs text-brand-600"></i>
                            <span class="hidden sm:inline font-bold">Filter</span>
                            <span id="filterCountBadgeLog" class="text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded-full font-mono font-bold">0/4</span>
                        </button>
                    </div>
                </div>

                <!-- Active Filter Tags / Chips Bar -->
                <div id="activeFilterContainerLog" class="mt-3 hidden flex flex-wrap items-center gap-2">
                    <!-- Dynamic Filter Chips Injected via JS -->
                </div>

                <!-- Autocomplete Dropdown -->
                <div id="autocompleteDropdownLog" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsLog" class="divide-y divide-slate-100 text-xs"></div>
                </div>
            </form>
        </div>

        <!-- Log History Data Table Card -->
        <div class="card-custom overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-4 px-4 sm:px-6">Waktu & Tanggal</th>
                            <th class="py-4 px-4 sm:px-6">Modul / Bagian</th>
                            <th class="py-4 px-4 sm:px-6">Aktor Pelaksana</th>
                            <th class="py-4 px-4 sm:px-6">Ref ID & Target</th>
                            <th class="py-4 px-4 sm:px-6 text-center">Status Aksi</th>
                            <th class="py-4 px-4 sm:px-6">Catatan / Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-amber-50/40 transition-colors">
                                    
                                    <!-- Tanggal & Waktu -->
                                    <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-orange-50 text-brand-600 border border-orange-200 flex items-center justify-center font-bold text-xs shrink-0">
                                                <i class="fa-regular fa-calendar-check"></i>
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-900 block"><?= date('d M Y', strtotime($log['created_at'])); ?></span>
                                                <span class="text-[10px] text-slate-500 font-bold font-mono"><?= date('H:i:s', strtotime($log['created_at'])); ?> WIB</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Modul -->
                                    <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                        <?php
                                            $mod = $log['modul'];
                                            $badge_class = 'bg-slate-100 text-slate-700 border-slate-200';
                                            if ($mod === 'Dosen Wali') $badge_class = 'bg-amber-50 text-amber-800 border-amber-200 font-bold';
                                            elseif ($mod === 'Admin Layanan') $badge_class = 'bg-orange-50 text-brand-700 border-orange-200 font-bold';
                                            elseif ($mod === 'Koordinator TA') $badge_class = 'bg-purple-50 text-purple-800 border-purple-200 font-bold';
                                            elseif ($mod === 'Ketua KK') $badge_class = 'bg-emerald-50 text-emerald-800 border-emerald-200 font-bold';
                                            elseif ($mod === 'Peminjaman Ruangan') $badge_class = 'bg-sky-50 text-sky-800 border-sky-200 font-bold';
                                        ?>
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] border inline-block <?= $badge_class; ?>">
                                            <?= htmlspecialchars($mod); ?>
                                        </span>
                                    </td>

                                    <!-- Aktor Pelaksana -->
                                    <td class="py-4 px-4 sm:px-6">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                                <i class="fa-solid fa-user-gear"></i>
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-900 block leading-tight"><?= htmlspecialchars($log['actor_name']); ?></span>
                                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-0.5">
                                                    <span class="font-bold text-slate-700"><?= htmlspecialchars($log['actor_role']); ?></span>
                                                    <?php if(!empty($log['actor_nip_nim'])): ?>
                                                        <span>•</span>
                                                        <span class="font-mono text-slate-500"><?= htmlspecialchars($log['actor_nip_nim']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Ref ID & Target -->
                                    <td class="py-4 px-4 sm:px-6">
                                        <div>
                                            <span class="font-mono font-bold text-brand-600 block text-[11px]"><?= htmlspecialchars($log['ref_id']); ?></span>
                                            <span class="text-[11px] text-slate-600 font-medium block truncate max-w-xs"><?= htmlspecialchars($log['target_name'] ?? '-'); ?></span>
                                        </div>
                                    </td>

                                    <!-- Status Aksi -->
                                    <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                        <?php
                                            $act = $log['action'];
                                            if (in_array($act, array('Approved', 'Approve All', 'Disetujui Admin'))) {
                                                echo '<span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-extrabold text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> ' . htmlspecialchars($act) . '</span>';
                                            } else if (in_array($act, array('Rejected', 'Reject All', 'Ditolak'))) {
                                                echo '<span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full font-extrabold text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-rose-600"></i> ' . htmlspecialchars($act) . '</span>';
                                            } else {
                                                echo '<span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full font-extrabold text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-info text-amber-600"></i> ' . htmlspecialchars($act) . '</span>';
                                            }
                                        ?>
                                    </td>

                                    <!-- Catatan / Alasan -->
                                    <td class="py-4 px-4 sm:px-6">
                                        <p class="text-xs text-slate-700 leading-relaxed max-w-md bg-slate-50 p-2.5 rounded-xl border border-slate-200 font-medium">
                                            <?= nl2br(htmlspecialchars($log['catatan'] ?: '- Tidak ada catatan -')); ?>
                                        </p>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-brand-600 flex items-center justify-center text-xl mx-auto mb-3 border border-orange-100 shadow-xs">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="font-extrabold text-sm text-slate-800">Belum Ada Catatan Riwayat Log Approval</p>
                                    <p class="text-xs text-slate-500 mt-1">Setiap tindakan approve atau reject pada modul akan tercatat secara otomatis di sini.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            <?php if($total_pages > 1): ?>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-600">
                    <div>
                        Menampilkan halaman <strong class="text-slate-900 font-bold"><?= $current_page; ?></strong> dari <strong class="text-slate-900 font-bold"><?= $total_pages; ?></strong> (Total <strong class="text-slate-900 font-bold"><?= $total_logs; ?></strong> record)
                    </div>
                    <div class="flex items-center gap-1.5">
                        <?php if($current_page > 1): ?>
                            <a href="<?= site_url('admin/log_history?modul='.$filter_modul.'&action='.$filter_action.'&q='.urlencode($search).'&page='.($current_page - 1)); ?>" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg border border-slate-200 font-bold transition shadow-xs">
                                &laquo; Prev
                            </a>
                        <?php endif; ?>

                        <?php for($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <a href="<?= site_url('admin/log_history?modul='.$filter_modul.'&action='.$filter_action.'&q='.urlencode($search).'&page='.$i); ?>" class="px-3 py-1.5 rounded-lg border font-bold transition <?= ($i == $current_page) ? 'bg-brand-600 text-white border-brand-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'; ?>">
                                <?= $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if($current_page < $total_pages): ?>
                            <a href="<?= site_url('admin/log_history?modul='.$filter_modul.'&action='.$filter_action.'&q='.urlencode($search).'&page='.($current_page + 1)); ?>" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 rounded-lg border border-slate-200 font-bold transition shadow-xs">
                                Next &raquo;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500 mt-12">
        <p>&copy; <?= date('Y'); ?> IFIK Telkom University - Audit Trail Log History System</p>
    </footer>

    <!-- Search System JS (Identik dengan Admin LAA Search System) -->
    <script>
        let activeFiltersLog = [];

        function switchModulTab(modul) {
            document.getElementById('inputModulFilter').value = modul;
            doLogSearch();
        }

        function setActionFilter(actionVal) {
            document.getElementById('inputActionFilter').value = actionVal;
            doLogSearch();
        }

        function doLogSearch() {
            const modul = document.getElementById('inputModulFilter').value || 'all';
            const action = document.getElementById('inputActionFilter').value || 'all';
            const q = document.getElementById('inputSearchLog').value.trim();
            
            let url = '<?= site_url("admin/log_history"); ?>?modul=' + encodeURIComponent(modul) + '&action=' + encodeURIComponent(action);
            if (q) {
                url += '&q=' + encodeURIComponent(q);
            }
            window.location.href = url;
        }

        function clearLogSearch() {
            document.getElementById('inputSearchLog').value = '';
            doLogSearch();
        }

        function addCategoryFilterLog() {
            if (activeFiltersLog.length >= 4) {
                alert('Maksimal 4 kriteria filter!');
                return;
            }
            const catSelect = document.getElementById('searchCategoryLog');
            const catVal = catSelect.value;
            const catText = catSelect.options[catSelect.selectedIndex].text;
            const inputVal = document.getElementById('inputSearchLog').value.trim();

            if (!inputVal) {
                alert('Masukkan kata kunci sebelum menambah filter!');
                return;
            }

            activeFiltersLog.push({ category: catVal, label: catText, value: inputVal });
            renderActiveFilterChipsLog();
            document.getElementById('inputSearchLog').value = '';
        }

        function removeFilterChipLog(idx) {
            activeFiltersLog.splice(idx, 1);
            renderActiveFilterChipsLog();
        }

        function renderActiveFilterChipsLog() {
            const container = document.getElementById('activeFilterContainerLog');
            const badge = document.getElementById('filterCountBadgeLog');
            badge.innerText = activeFiltersLog.length + '/4';

            if (activeFiltersLog.length === 0) {
                container.classList.add('hidden');
                container.innerHTML = '';
                return;
            }

            container.classList.remove('hidden');
            let html = '';
            activeFiltersLog.forEach((f, idx) => {
                html += `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 border border-orange-200 text-brand-700 rounded-full text-xs font-bold shadow-2xs">
                        <span>${f.label}: <strong>${f.value}</strong></span>
                        <button type="button" onclick="removeFilterChipLog(${idx})" class="text-orange-500 hover:text-rose-600 font-bold ml-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </span>
                `;
            });
            container.innerHTML = html;
        }

        // Autocomplete JS Handler
        const searchInputLog = document.getElementById('inputSearchLog');
        const dropdownLog = document.getElementById('autocompleteDropdownLog');
        const resultsBoxLog = document.getElementById('autocompleteResultsLog');
        let autoTimeoutLog = null;

        if (searchInputLog) {
            searchInputLog.addEventListener('input', function() {
                const val = this.value.trim();
                if (autoTimeoutLog) clearTimeout(autoTimeoutLog);

                if (val.length < 2) {
                    dropdownLog.classList.add('hidden');
                    return;
                }

                autoTimeoutLog = setTimeout(() => {
                    fetch('<?= site_url("admin/autocomplete_logs"); ?>?q=' + encodeURIComponent(val))
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                dropdownLog.classList.add('hidden');
                                return;
                            }

                            let html = '';
                            data.forEach(item => {
                                html += `
                                    <div onclick="selectAutocompleteLog('${item.ref_id}')" class="p-3 hover:bg-amber-50/60 cursor-pointer transition-colors flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs">${item.target_name} <span class="font-mono text-brand-600">(${item.ref_id})</span></div>
                                            <div class="text-[10px] text-slate-500 mt-0.5">${item.modul} • ${item.actor_name} (${item.actor_role})</div>
                                        </div>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md ${item.action === 'Approved' ? 'bg-emerald-100 text-emerald-800' : (item.action === 'Rejected' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700')}">${item.action}</span>
                                    </div>
                                `;
                            });

                            resultsBoxLog.innerHTML = html;
                            dropdownLog.classList.remove('hidden');
                        })
                        .catch(() => {
                            dropdownLog.classList.add('hidden');
                        });
                }, 250);
            });

            document.addEventListener('click', function(e) {
                if (!searchInputLog.contains(e.target) && !dropdownLog.contains(e.target)) {
                    dropdownLog.classList.add('hidden');
                }
            });
        }

        function selectAutocompleteLog(refId) {
            document.getElementById('inputSearchLog').value = refId;
            dropdownLog.classList.add('hidden');
            doLogSearch();
        }
    </script>

</body>
</html>
