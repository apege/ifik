<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Spring Physics Card Hover */
        .clean-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        .clean-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px -5px rgba(0, 0, 0, 0.06), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
        }

        /* Stat Card Glow & Icon Bounce */
        .stat-card-glow {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.5, 0.64, 1);
        }
        .stat-card-glow:hover {
            transform: translateY(-5px);
            border-color: #fb923c;
            box-shadow: 0 14px 30px -4px rgba(249, 115, 22, 0.16);
        }
        .stat-card-glow .icon-box {
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease, color 0.2s ease;
        }
        .stat-card-glow:hover .icon-box {
            transform: scale(1.15) rotate(7deg);
        }

        /* Table Row Interactive Hover Accent */
        .mhs-row {
            transition: all 0.2s ease;
            position: relative;
        }
        .mhs-row:hover {
            background-color: #fffaf5 !important;
            box-shadow: inset 4px 0 0 0 #ea580c;
        }
        .mhs-row:hover .avatar-box {
            transform: scale(1.1) rotate(-3deg);
            background-color: #ffedd5;
            color: #c2410c;
            border-color: #fdba74;
        }
        .avatar-box {
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Interactive Action Button */
        .btn-action-primary {
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-action-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 16px -2px rgba(234, 88, 12, 0.35);
        }
        .btn-action-primary:active {
            transform: scale(0.96);
        }

        /* 4 Berkas Badge Hover */
        .berkas-pill-container {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .berkas-pill-container:hover {
            transform: scale(1.06);
            border-color: #fdba74;
        }

        /* Custom Tooltip */
        .tooltip-box {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.2s, transform 0.2s cubic-bezier(0.34, 1.4, 0.64, 1);
            transform: translateY(6px);
        }
        .has-tooltip:hover .tooltip-box {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        /* Toast notification */
        #toastNotice {
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #toastNotice.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Layanan Akademik (LAA)',
        'user_display_name' => 'Admin Layanan FIK',
        'user_display_sub'  => 'Unit Akademik & Kelulusan'
    ]); ?>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">

        <!-- Flash Message -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs flex items-center justify-between text-xs animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <p class="font-semibold"><?= $this->session->flashdata('success'); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    Verifikasi Berkas Mahasiswa (LAA)
                </h1>
                <p class="text-slate-500 text-xs mt-0.5">Validasi 4 berkas persyaratan pendaftaran Tugas Akhir, tandai berkas kurang, dan setujui atau minta perbaikan.</p>
            </div>

            <!-- Quick Action Toolbar -->
            <div class="flex items-center gap-2.5">
                <button onclick="resetAllFilters()" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-200 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all active:scale-95" title="Reset filter dan pencarian">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                </button>
                <button onclick="window.location.reload()" class="px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all active:scale-95">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Data
                </button>
            </div>
        </div>

        <!-- Metric Stat Cards Grid (Interactive Count-Up & Quick-Filter on Click) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pengajuan -->
            <div onclick="setFilterTab('all')" role="button" class="clean-card stat-card-glow rounded-2xl p-5 cursor-pointer flex items-center justify-between group">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Pengajuan</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight block count-up" data-target="<?= $stats['total']; ?>">0</span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block flex items-center gap-1">
                        <span>Mahasiswa Terdaftar</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover:opacity-100 transition-opacity text-orange-600"></i>
                    </span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-orange-600 group-hover:text-white transition-colors flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-folder2-open"></i>
                </div>
            </div>

            <!-- Perlu Validasi -->
            <div onclick="setFilterTab('Pending')" role="button" class="clean-card stat-card-glow rounded-2xl p-5 cursor-pointer flex items-center justify-between group">
                <div>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block mb-1">Perlu Validasi</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 tracking-tight block count-up" data-target="<?= $stats['pending']; ?>">0</span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block flex items-center gap-1">
                        <span>Menunggu Cek Dokumen</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover:opacity-100 transition-opacity text-amber-600"></i>
                    </span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 group-hover:bg-amber-500 group-hover:text-white transition-colors flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <!-- Berkas Lengkap (Approved) -->
            <div onclick="setFilterTab('Approved')" role="button" class="clean-card stat-card-glow rounded-2xl p-5 cursor-pointer flex items-center justify-between group">
                <div>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Berkas Lengkap</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight block count-up" data-target="<?= $stats['approved']; ?>">0</span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block flex items-center gap-1">
                        <span>Disetujui Admin LAA</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover:opacity-100 transition-opacity text-emerald-600"></i>
                    </span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition-colors flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>

            <!-- Dikembalikan (Revisi) -->
            <div onclick="setFilterTab('Rejected')" role="button" class="clean-card stat-card-glow rounded-2xl p-5 cursor-pointer flex items-center justify-between group">
                <div>
                    <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block mb-1">Dikembalikan</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight block count-up" data-target="<?= $stats['rejected']; ?>">0</span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block flex items-center gap-1">
                        <span>Perlu Revisi Mahasiswa</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover:opacity-100 transition-opacity text-rose-600"></i>
                    </span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 group-hover:bg-rose-600 group-hover:text-white transition-colors flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
            </div>
        </div>

        <!-- Interactive Filter & Live Instant Search Bar -->
        <div class="clean-card rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0" id="filterTabs">
                <button onclick="setFilterTab('all')" data-status="all" 
                        class="tab-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap bg-orange-600 text-white shadow-xs">
                    Semua (<span id="count-all"><?= $stats['total']; ?></span>)
                </button>
                <button onclick="setFilterTab('Pending')" data-status="Pending" 
                        class="tab-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                    Menunggu Cek (<span id="count-pending"><?= $stats['pending']; ?></span>)
                </button>
                <button onclick="setFilterTab('Approved')" data-status="Approved" 
                        class="tab-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                    Disetujui (<span id="count-approved"><?= $stats['approved']; ?></span>)
                </button>
                <button onclick="setFilterTab('Rejected')" data-status="Rejected" 
                        class="tab-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                    Dikembalikan (<span id="count-rejected"><?= $stats['rejected']; ?></span>)
                </button>
            </div>

            <!-- Instant Live Search Input -->
            <div class="relative w-full sm:w-80">
                <input type="text" id="liveSearchInput" oninput="handleLiveSearch()" 
                       placeholder="Ketik nama, NIM, atau judul untuk mencari..." 
                       class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition-all">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <button id="clearSearchBtn" onclick="clearSearch()" class="hidden absolute right-2.5 top-2 text-slate-400 hover:text-slate-700 text-xs font-bold w-5 h-5 rounded-full hover:bg-slate-200 items-center justify-center">
                    &times;
                </button>
            </div>
        </div>

        <!-- Table Card with Live Filtering & Micro-interactions -->
        <div class="clean-card rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="mhsTable">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3.5 px-4 whitespace-nowrap">Mahasiswa</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Program Studi &amp; KK</th>
                            <th class="py-3.5 px-4">Judul Rencana TA</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status 4 Berkas</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Dosen Wali</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Admin LAA</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white" id="tableBody">
                        <?php if(empty($list_pengajuan)): ?>
                            <tr id="emptyRow">
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-3xl text-slate-300 block mb-2"></i>
                                    Tidak ada data pengajuan berkas yang cocok.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($list_pengajuan as $row): ?>
                                <?php
                                    $is_wali_app = (($row['status_approval_wali'] ?? '') === 'Approved');
                                    $laa_status  = $row['status_approval_admin'] ?? 'Pending';
                                    
                                    // 4 Berkas checks
                                    $ksm_st = $row['status_ksm'] ?? 'Pending';
                                    $trs_st = $row['status_transkrip'] ?? 'Pending';
                                    $prn_st = $row['status_pernyataan'] ?? 'Pending';
                                    $lab_st = $row['status_bebas_lab'] ?? 'Pending';
                                    
                                    $full_name = trim(($row['nama_depan'] ?? '') . ' ' . ($row['nama_belakang'] ?? ''));
                                    if (empty($full_name)) $full_name = 'Mahasiswa ' . ($row['nim'] ?? '');
                                ?>
                                <tr class="mhs-row hover:bg-orange-50/30 transition-colors group" 
                                    data-status="<?= $laa_status; ?>"
                                    data-search="<?= strtolower($full_name . ' ' . ($row['nim'] ?? '') . ' ' . ($row['judul_1'] ?? '') . ' ' . ($row['kode_kk'] ?? '') . ' ' . ($row['prodi'] ?? '')); ?>">
                                    
                                    <!-- Mahasiswa -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center flex-shrink-0 group-hover:scale-105 group-hover:bg-orange-100 group-hover:text-orange-600 transition-all">
                                                <?= strtoupper(substr($row['nama_depan'] ?? 'M', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs searchable-text"><?= htmlspecialchars($full_name); ?></div>
                                                <div class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                                                    <span class="searchable-text"><?= htmlspecialchars($row['nim'] ?? ''); ?></span>
                                                    <button type="button" onclick="copyToClipboard('<?= $row['nim'] ?? ''; ?>')" class="text-slate-300 hover:text-orange-600 transition-colors" title="Salin NIM">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Prodi & KK -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-700 searchable-text"><?= htmlspecialchars($row['prodi'] ?? 'DKV'); ?></div>
                                        <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[10px] font-semibold border border-slate-200">
                                            <i class="bi bi-diagram-3 text-orange-600 text-[9px]"></i>
                                            <span class="searchable-text"><?= htmlspecialchars($row['kode_kk'] ?? 'KK-VCM'); ?></span>
                                        </span>
                                    </td>

                                    <!-- Judul TA -->
                                    <td class="py-3.5 px-4 min-w-[240px] max-w-xs">
                                        <div class="font-medium text-slate-800 line-clamp-2 text-xs leading-relaxed searchable-text" title="<?= htmlspecialchars($row['judul_1'] ?? ''); ?>">
                                            <?= htmlspecialchars($row['judul_1'] ?? ''); ?>
                                        </div>
                                    </td>

                                    <!-- Status 4 Berkas with Global Floating Tooltip -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="has-global-tooltip inline-block cursor-pointer"
                                             data-ksm="<?= $ksm_st; ?>"
                                             data-trs="<?= $trs_st; ?>"
                                             data-prn="<?= $prn_st; ?>"
                                             data-lab="<?= $lab_st; ?>">
                                            <div class="inline-flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 text-[10px] font-mono shadow-2xs hover:border-orange-300 transition-colors">
                                                <span class="<?= $ksm_st === 'Valid' ? 'text-emerald-600 font-bold' : ($ksm_st === 'Invalid' ? 'text-rose-600 font-bold' : 'text-slate-400'); ?>">KSM</span>
                                                <span class="text-slate-300">·</span>
                                                <span class="<?= $trs_st === 'Valid' ? 'text-emerald-600 font-bold' : ($trs_st === 'Invalid' ? 'text-rose-600 font-bold' : 'text-slate-400'); ?>">TRS</span>
                                                <span class="text-slate-300">·</span>
                                                <span class="<?= $prn_st === 'Valid' ? 'text-emerald-600 font-bold' : ($prn_st === 'Invalid' ? 'text-rose-600 font-bold' : 'text-slate-400'); ?>">SRT</span>
                                                <span class="text-slate-300">·</span>
                                                <span class="<?= $lab_st === 'Valid' ? 'text-emerald-600 font-bold' : ($lab_st === 'Invalid' ? 'text-rose-600 font-bold' : 'text-slate-400'); ?>">LAB</span>
                                            </div>
                                        </div>

                                        <?php if(!empty($row['berkas_kurang'])): ?>
                                            <div class="text-[9px] text-rose-600 font-semibold mt-1">Revisi: <?= htmlspecialchars($row['berkas_kurang']); ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status Dosen Wali -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php if($is_wali_app): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                                                <i class="bi bi-check-circle-fill text-emerald-500"></i> Disetujui
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">
                                                <i class="bi bi-hourglass text-slate-400"></i> Belum Wali
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status Admin LAA -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php if($laa_status === 'Approved'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs whitespace-nowrap">
                                                <i class="bi bi-check2-all text-emerald-600"></i> Approved
                                            </span>
                                        <?php elseif($laa_status === 'Rejected'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs whitespace-nowrap">
                                                <i class="bi bi-arrow-return-left text-rose-600"></i> Dikembalikan
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs whitespace-nowrap">
                                                <i class="bi bi-clock text-amber-600"></i> Menunggu Cek
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="<?= site_url('adminlayanan/detail_berkas/' . $row['nim']); ?>" 
                                           class="btn-action-primary inline-flex items-center gap-1.5 px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-semibold shadow-xs whitespace-nowrap">
                                            <i class="bi bi-search"></i> Periksa Berkas
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- No Match Fallback Row for Live Search -->
                        <tr id="noMatchRow" class="hidden">
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="bi bi-search text-3xl text-slate-300 block mb-2"></i>
                                Tidak ditemukan data mahasiswa yang sesuai dengan kata kunci pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Floating Interactive Toast Notification -->
    <div id="toastNotice" class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs border border-slate-700">
        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">
            <i class="bi bi-check-lg"></i>
        </div>
        <span id="toastMsg" class="font-medium">NIM disalin ke clipboard!</span>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-8 text-center text-xs text-slate-500">
        &copy; <?= date('Y'); ?> Fakultas Industri Kreatif - Telkom University. Modul Admin Layanan Akademik (LAA).
    </footer>

    <!-- Interactive Client Scripts -->
    <script>
        // Count Up Animation
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.count-up');
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                let count = 0;
                const speed = 25;
                const increment = Math.max(1, Math.ceil(target / 20));
                
                const updateCount = () => {
                    count += increment;
                    if (count < target) {
                        counter.innerText = count;
                        setTimeout(updateCount, speed);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        });

        // Live Filter State
        let currentStatusFilter = 'all';

        function setFilterTab(status) {
            currentStatusFilter = status;
            
            // Update active tab buttons UI
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.getAttribute('data-status') === status) {
                    btn.className = 'tab-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap bg-orange-600 text-white shadow-xs';
                } else {
                    btn.className = 'tab-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200';
                }
            });

            handleLiveSearch();
        }

        function handleLiveSearch() {
            const query = document.getElementById('liveSearchInput').value.trim().toLowerCase();
            const rows = document.querySelectorAll('.mhs-row');
            const clearBtn = document.getElementById('clearSearchBtn');
            let matchCount = 0;

            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
                clearBtn.classList.add('flex');
            } else {
                clearBtn.classList.add('hidden');
                clearBtn.classList.remove('flex');
            }

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowSearchData = row.getAttribute('data-search');

                const statusMatches = (currentStatusFilter === 'all' || rowStatus === currentStatusFilter);
                const queryMatches = (query === '' || rowSearchData.includes(query));

                if (statusMatches && queryMatches) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noMatchRow = document.getElementById('noMatchRow');
            if (noMatchRow) {
                if (matchCount === 0 && rows.length > 0) {
                    noMatchRow.classList.remove('hidden');
                } else {
                    noMatchRow.classList.add('hidden');
                }
            }
        }

        function clearSearch() {
            document.getElementById('liveSearchInput').value = '';
            handleLiveSearch();
            document.getElementById('liveSearchInput').focus();
        }

        function resetAllFilters() {
            document.getElementById('liveSearchInput').value = '';
            setFilterTab('all');
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('NIM ' + text + ' berhasil disalin!');
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toastNotice');
            document.getElementById('toastMsg').textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }
    </script>

    <!-- Global Unclipped Floating Tooltip Container -->
    <div id="globalFloatingTooltip" class="fixed z-[9999] hidden pointer-events-none p-3 bg-slate-900 text-white rounded-xl shadow-2xl text-[10px] w-52 border border-slate-700 font-sans transition-opacity duration-100 opacity-0">
        <div class="font-bold border-b border-slate-700 pb-1 mb-1.5 text-slate-200">Detail Status 4 Berkas:</div>
        <div class="space-y-1 font-mono" id="globalTooltipContent"></div>
    </div>

    <!-- Interactive Client Scripts -->
    <script>
        // Global Unclipped Tooltip Handler (Flicker-Free)
        document.addEventListener('mouseover', (e) => {
            const target = e.target.closest('.has-global-tooltip');
            const tooltip = document.getElementById('globalFloatingTooltip');
            const content = document.getElementById('globalTooltipContent');
            if (!target || !tooltip || !content) return;

            const ksm = target.dataset.ksm || 'Pending';
            const trs = target.dataset.trs || 'Pending';
            const prn = target.dataset.prn || 'Pending';
            const lab = target.dataset.lab || 'Pending';

            const getClass = (st) => st === 'Valid' ? 'text-emerald-400 font-bold' : (st === 'Invalid' ? 'text-rose-400 font-bold' : 'text-slate-400');

            content.innerHTML = `
                <div class="flex justify-between"><span>1. KSM:</span> <strong class="${getClass(ksm)}">${ksm}</strong></div>
                <div class="flex justify-between"><span>2. Transkrip:</span> <strong class="${getClass(trs)}">${trs}</strong></div>
                <div class="flex justify-between"><span>3. Pernyataan:</span> <strong class="${getClass(prn)}">${prn}</strong></div>
                <div class="flex justify-between"><span>4. Bebas Lab:</span> <strong class="${getClass(lab)}">${lab}</strong></div>
            `;

            const rect = target.getBoundingClientRect();
            const tooltipWidth = 208;
            const tooltipHeight = 110;

            let left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
            let top = rect.top - tooltipHeight - 8;

            if (top < 10) {
                top = rect.bottom + 8;
            }

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
            tooltip.classList.remove('hidden');
            requestAnimationFrame(() => {
                tooltip.classList.add('opacity-100');
            });
        });

        document.addEventListener('mouseout', (e) => {
            const target = e.target.closest('.has-global-tooltip');
            const tooltip = document.getElementById('globalFloatingTooltip');
            if (!target || !tooltip) return;

            // Pastikan kursor benar-benar keluar dari wadah utama (bukan cuma pindah antar span di dalamnya)
            if (!target.contains(e.relatedTarget)) {
                tooltip.classList.remove('opacity-100');
                tooltip.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
