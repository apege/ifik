<?php
/**
 * Rekomendasi Sidang & Rollback Tahap - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6, Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
            border-radius: 1.25rem;
        }

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
        .btn-3d-kinetic .bg {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: linear-gradient(180deg, #fb923c 0%, #ea580c 60%, #c2410c 100%);
            box-shadow: 0 10px 24px -4px rgba(234, 88, 12, 0.5), inset 0 1.5px 2px rgba(255, 255, 255, 0.7);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-3d-kinetic .wrap {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 16px;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Multi Preview Cards (Matching dosen_bimbingan) */
        .preview-card-item {
            pointer-events: auto;
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid #cbd5e1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            width: 780px;
            max-width: 72vw;
            height: 88vh;
            max-height: 90vh;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .preview-card-item.preview-fullscreen-mode {
            position: fixed !important;
            top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
            width: 100vw !important; height: 100vh !important;
            max-width: 100vw !important; max-height: 100vh !important;
            z-index: 999999 !important;
            border-radius: 0 !important; border: none !important;
            box-shadow: none !important; margin: 0 !important;
        }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-card-in {
            animation: cardFadeIn 0.2s ease-out forwards;
        }

        @media (max-width: 768px) {
            .preview-card-item {
                width: 92vw !important;
                max-width: 92vw !important;
                height: 80vh !important;
                max-height: 80vh !important;
            }
        }

        /* Multi Filter Search Styles (Persis dosen_bimbingan) */
        .filter-row-dw {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-col-dw {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            background: #fff;
            color: #1e293b;
            outline: none;
            cursor: pointer;
            transition: all 0.18s ease;
            flex-shrink: 0;
            min-width: 170px;
        }
        .filter-col-dw:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .filter-val-dw {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
            transition: all 0.18s ease;
        }
        .filter-val-dw:focus {
            border-color: #ea580c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .btn-remove-filter-dw {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: #ffe4e6;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .btn-remove-filter-dw:hover { background: #fecdd3; transform: scale(1.05); }
        .btn-remove-filter-dw:active { transform: scale(0.95); }
        @media (max-width: 768px) {
            .filter-row-dw { flex-wrap: wrap; }
            .filter-col-dw { min-width: 100%; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- Dedicated Admin LAA Sidebar Component -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>

    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow pb-16">
        <!-- Sub Navigation Page Title Bar -->
        <div class="glass-header sticky top-0 z-30 px-4 sm:px-6 lg:px-8 py-3.5 mb-6">
            <div class="w-full flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="<?= site_url('adminlayanan'); ?>" class="w-10 h-10 rounded-xl bg-orange-100/70 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-sm shadow-xs shrink-0 hover:bg-orange-200/80 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-orange-100 text-brand-600 text-[10px] font-extrabold uppercase tracking-wide border border-orange-200">
                                Admin Layanan LAA
                            </span>
                            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Rekomendasi &amp; Kelola Tahap TA</h1>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Peninjauan berkas submission rekomendasi Pembimbing 1 dan kontrol penyesuaian (downgrade) tahap bimbingan.</p>
                    </div>
                </div>

                <!-- Profile & Quick Actions -->
                <div class="flex items-center gap-2.5 sm:gap-3 justify-end">
                    <a href="<?= site_url('adminlayanan/pengaturan_jalur'); ?>" class="btn-3d-kinetic inline-flex items-center gap-2 shrink-0">
                        <span class="bg"></span>
                        <span class="wrap">
                            <i class="bi bi-sliders text-white text-xs mr-1.5"></i>
                            <span class="char">Pengaturan Jalur</span>
                        </span>
                    </a>
                    <div class="hidden lg:flex flex-col text-right ml-2">
                        <span class="text-xs font-bold text-slate-800 leading-tight">Admin Layanan LAA</span>
                        <span class="text-[10px] font-semibold text-slate-500">Layanan Akademik FIK</span>
                    </div>
                    <div class="hidden sm:flex w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 items-center justify-center font-bold text-base shadow-xs shrink-0">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Container -->
        <main class="w-full px-4 sm:px-6 lg:px-8 flex-grow">

        <!-- Stats Overview Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
            <!-- 1. Total Mahasiswa -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=all'); ?>" class="group block text-left">
                <div class="rounded-2xl border <?= $filter_type === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200/90' ?> bg-gradient-to-br from-white via-orange-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Mahasiswa TA</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1"><?= $stats['total']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Peserta Bimbingan</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-orange-200 bg-orange-50 text-brand-600 shadow-sm">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 2. Jalur Sidang Reguler -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=sidang'); ?>" class="group block text-left">
                <div class="rounded-2xl border <?= $filter_type === 'sidang' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/90' ?> bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Rekomendasi Sidang</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 text-emerald-600"><?= $stats['sidang']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Sidang Majelis Reguler</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-sm">
                            <i class="bi bi-mortarboard-fill text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 3. Jalur Non-Sidang (Ekuivalensi) -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=non_sidang'); ?>" class="group block text-left">
                <div class="rounded-2xl border <?= $filter_type === 'non_sidang' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200/90' ?> bg-gradient-to-br from-white via-purple-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-purple-600 transition-colors">Rekomendasi Non-Sidang</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 text-purple-600"><?= $stats['non_sidang']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Prestasi / HKI / Industri</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-purple-200 bg-purple-50 text-purple-600 shadow-sm">
                            <i class="bi bi-award-fill text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 4. Menunggu Rekomendasi P1 -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=pending'); ?>" class="group block text-left">
                <div class="rounded-2xl border <?= $filter_type === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/90' ?> bg-gradient-to-br from-white via-amber-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Belum Rekomendasi</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 text-amber-600"><?= $stats['pending']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Menunggu Pembimbing 1</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-amber-200 bg-amber-50 text-amber-600 shadow-sm">
                            <i class="fa-solid fa-hourglass-half text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Multi-Criteria Search Panel -->
        <div class="card-custom p-4 sm:p-5 mb-6">
            <!-- Multi-Criteria Filter Panel (Identik dosen_bimbingan.php) -->
            <div id="multiFilterPanelDW" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                        <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnAddFilterDW" onclick="addFilterRowDW()"
                                class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-plus-lg"></i> Tambah Filter
                            <span id="filterCountBadgeDW" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                        </button>
                        <button type="button" onclick="resetFiltersDW()"
                                class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>

                <div id="filterRowsDW" class="space-y-2 mb-3"></div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200">
                    <p class="text-[10px] text-slate-400 font-medium italic">
                        <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk menyaring data seketika.
                    </p>
                    <button type="button" onclick="doMultiSearchDW()"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Data Card -->
        <div class="card-custom overflow-hidden mb-12">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="rekomendasiTable">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 min-w-[240px]">Mahasiswa &amp; Judul Tugas Akhir</th>
                            <th class="py-3.5 px-4 min-w-[180px]">Tim Pembimbing &amp; Penguji</th>
                            <th class="py-3.5 px-4 min-w-[120px] text-center">Tahap Bimbingan</th>
                            <th class="py-3.5 px-4 min-w-[200px]">Rekomendasi P1</th>
                            <th class="py-3.5 px-4 min-w-[150px] text-center">Aksi LAA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <!-- Row state saat filter tidak menemukan hasil -->
                        <tr id="noDataFilterRow" class="hidden">
                            <td colspan="6" class="py-16 text-center text-slate-400 font-medium">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                    <i class="bi bi-search"></i>
                                </div>
                                <p class="font-bold text-slate-600">Tidak ada data mahasiswa ditemukan</p>
                                <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian pada filter.</p>
                            </td>
                        </tr>

                        <?php if(empty($list)): ?>
                            <tr id="emptyDbRow">
                                <td colspan="6" class="py-16 text-center text-slate-400 font-medium">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                        <i class="bi bi-inbox-fill"></i>
                                    </div>
                                    <p class="font-bold text-slate-600">Tidak ada data mahasiswa ditemukan</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau tab filter.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($list as $r): ?>
                                <tr class="mhs-row hover:bg-orange-50/30 transition-colors group"
                                    data-nim="<?= htmlspecialchars(strtolower($r['nim'])); ?>"
                                    data-nama="<?= htmlspecialchars(strtolower($r['nama_mahasiswa'])); ?>"
                                    data-judul="<?= htmlspecialchars(strtolower($r['judul'] ?? '')); ?>"
                                    data-dosen="<?= htmlspecialchars(strtolower($r['pembimbing_1'] . ' ' . $r['pembimbing_2'] . ' ' . $r['penguji_1'])); ?>"
                                    data-tahap="<?= htmlspecialchars(strtolower($r['tahap_terkini'] . ' ' . $r['tahap_key'])); ?>"
                                    data-jalur="<?= htmlspecialchars(strtolower(($r['recommendation_type'] ?? '') . ' ' . ($r['jalur_title'] ?? ''))); ?>"
                                    data-all="<?= htmlspecialchars(strtolower($r['nim'] . ' ' . $r['nama_mahasiswa'] . ' ' . ($r['judul'] ?? '') . ' ' . $r['pembimbing_1'] . ' ' . $r['pembimbing_2'] . ' ' . $r['penguji_1'] . ' ' . $r['tahap_terkini'] . ' ' . ($r['recommendation_type'] ?? '') . ' ' . ($r['jalur_title'] ?? ''))); ?>">
                                    
                                    <!-- 1. No -->
                                    <td class="py-4 px-4 text-center font-bold text-slate-400 row-number">
                                        <?= $no++; ?>
                                    </td>

                                    <!-- 2. Mahasiswa & Judul -->
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900 group-hover:text-brand-600 transition-colors">
                                            <?= htmlspecialchars($r['nama_mahasiswa']); ?>
                                        </div>
                                        <div class="font-mono text-[11px] text-slate-500 font-semibold mt-0.5">
                                            NIM: <?= htmlspecialchars($r['nim']); ?>
                                        </div>
                                        <?php if(!empty($r['judul']) && $r['judul'] !== '-'): ?>
                                            <div class="mt-1 text-[11px] text-slate-600 line-clamp-2 italic leading-relaxed" title="<?= htmlspecialchars($r['judul']); ?>">
                                                "<?= htmlspecialchars($r['judul']); ?>"
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 3. Tim Dosen -->
                                    <td class="py-4 px-4 space-y-1">
                                        <div class="flex items-center gap-1.5" title="Pembimbing 1">
                                            <span class="w-5 h-5 rounded-md bg-orange-100 text-brand-700 font-black text-[9px] flex items-center justify-center shrink-0">P1</span>
                                            <span class="truncate text-slate-700 font-medium max-w-[180px]"><?= htmlspecialchars($r['pembimbing_1']); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5" title="Pembimbing 2">
                                            <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-700 font-black text-[9px] flex items-center justify-center shrink-0">P2</span>
                                            <span class="truncate text-slate-700 font-medium max-w-[180px]"><?= htmlspecialchars($r['pembimbing_2']); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5" title="Penguji 1">
                                            <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 font-black text-[9px] flex items-center justify-center shrink-0">U1</span>
                                            <span class="truncate text-slate-700 font-medium max-w-[180px]"><?= htmlspecialchars($r['penguji_1']); ?></span>
                                        </div>
                                    </td>

                                    <!-- 4. Tahap Bimbingan Terkini -->
                                    <td class="py-4 px-4 text-center">
                                        <?php
                                            $tKey = $r['tahap_key'];
                                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                            if ($tKey === 'preview1') $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            elseif ($tKey === 'preview2') $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                            elseif ($tKey === 'preview3') $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                            elseif ($tKey === 'persyaratan_sidang') $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            elseif ($tKey === 'sidang')   $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-300 font-extrabold';
                                        ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold border <?= $badgeClass; ?>">
                                            <i class="bi bi-mortarboard-fill"></i>
                                            <?= htmlspecialchars($r['tahap_terkini']); ?>
                                        </span>
                                    </td>

                                    <!-- 5. Rekomendasi P1 -->
                                    <td class="py-4 px-4">
                                        <?php if(empty($r['id_rekomen'])): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-semibold text-[11px]">
                                                <i class="fa-solid fa-hourglass-start animate-pulse"></i>
                                                Belum Ada Rekomendasi
                                            </span>
                                        <?php else: ?>
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-2">
                                                    <?php if($r['recommendation_type'] === 'sidang'): ?>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-300">
                                                            <i class="bi bi-mortarboard-fill"></i> SIDANG REGULER
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-purple-100 text-purple-800 font-bold text-[10px] border border-purple-300">
                                                            <i class="bi bi-award-fill"></i> NON-SIDANG
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if(!empty($r['jalur_title']) && $r['jalur_title'] !== 'SIDANG'): ?>
                                                        <span class="font-bold text-slate-800 text-[11px] truncate max-w-[130px]" title="<?= htmlspecialchars($r['jalur_title']); ?>">
                                                            <?= htmlspecialchars($r['jalur_title']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if(!empty($r['catatan_dosen'])): ?>
                                                    <div class="text-[11px] text-slate-500 italic line-clamp-1" title="<?= htmlspecialchars($r['catatan_dosen']); ?>">
                                                        "<?= htmlspecialchars($r['catatan_dosen']); ?>"
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 6. Aksi LAA (Downgrade & Lihat Eviden Button) -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Downgrade -->
                                            <button type="button" 
                                                    onclick="openModalDowngrade('<?= htmlspecialchars($r['nim']); ?>', '<?= htmlspecialchars(addslashes($r['nama_mahasiswa'])); ?>', '<?= htmlspecialchars($r['tahap_key']); ?>', '<?= htmlspecialchars($r['tahap_terkini']); ?>')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 hover:border-rose-300 font-bold text-[11px] shadow-xs hover:shadow transition cursor-pointer"
                                                    title="Turunkan / Downgrade Tahap Bimbingan">
                                                <i class="bi bi-arrow-down-circle-fill text-rose-600"></i>
                                                <span>Downgrade</span>
                                            </button>

                                            <?php if(!empty($r['files_uploaded'])): ?>
                                                <button type="button"
                                                        onclick="openModalDetailBerkas('<?= htmlspecialchars($r['nim']); ?>', '<?= htmlspecialchars(addslashes($r['nama_mahasiswa'])); ?>', '<?= htmlspecialchars($r['jalur_title'] ?? ''); ?>', '<?= htmlspecialchars(addslashes($r['catatan_dosen'] ?? '')); ?>', <?= htmlspecialchars(json_encode($r['files_uploaded'])); ?>)"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 hover:border-slate-300 font-bold text-[11px] shadow-xs hover:shadow transition cursor-pointer"
                                                        title="Lihat Detail Eviden Rekomendasi">
                                                    <i class="bi bi-eye-fill text-xs"></i>
                                                    <span>Lihat Eviden</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
    </div> <!-- End .page-wrapper-for-sidebar -->

    <!-- ============================================================== -->
    <!-- MODAL DOWNGRADE TAHAP PREVIEW (LAA) -->
    <!-- ============================================================== -->
    <div id="modalDowngrade" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 w-full max-w-lg shadow-2xl border border-slate-100 transform transition-all">
            <button type="button" onclick="closeModalDowngrade()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center font-bold text-lg leading-none transition">&times;</button>
            
            <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Downgrade Tahap Bimbingan</h3>
                    <p class="text-xs text-slate-500">Penurunan tahapan bimbingan Tugas Akhir mahasiswa.</p>
                </div>
            </div>

            <form id="formDowngradeSubmit" onsubmit="submitDowngrade(event)" class="space-y-4">
                <input type="hidden" name="nim" id="dgNim" value="">
                <input type="hidden" id="dgTahapCurrentKey" value="">
                
                <!-- Info Mahasiswa -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Nama Mahasiswa:</span>
                        <span class="font-bold text-slate-800 text-right" id="dgNama">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">NIM:</span>
                        <span class="font-mono font-bold text-slate-700" id="dgNimText">-</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400 font-semibold">Tahap Saat Ini:</span>
                        <span class="font-extrabold text-brand-600" id="dgTahapCurrent">-</span>
                    </div>
                </div>

                <!-- Pilihan Tujuan Downgrade -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Pilih Tujuan Downgrade ke Tahap: <span class="text-rose-500">*</span></label>
                    <select name="ke_tahap" id="dgKeTahap" onchange="updateDowngradeImpacts()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                        <!-- Options populated dynamically based on current stage -->
                    </select>
                </div>

                <!-- Notification Box: Data yang akan dihapus / di-reset -->
                <div id="dgImpactContainer" class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950">
                    <div class="flex items-start gap-2.5">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <span class="font-bold text-xs text-rose-900 block mb-1">Pemberitahuan Data yang Akan Dihapus / Di-reset:</span>
                            <ul id="dgImpactList" class="space-y-1 list-disc list-inside text-[11px] font-medium text-rose-900">
                                <!-- Populated dynamically based on selected target stage -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Alasan Downgrade -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Alasan / Catatan Downgrade: <span class="text-rose-500">*</span></label>
                    <textarea name="alasan" id="dgAlasan" rows="3" required placeholder="Tuliskan alasan penurunan tahap bimbingan..." 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalDowngrade()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitDowngrade" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-sm"></i>
                        <span>Konfirmasi Downgrade</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL DETAIL REKOMENDASI & BERKAS -->
    <!-- ============================================================== -->
    <div id="modalDetailBerkas" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 w-full max-w-lg shadow-2xl border border-slate-100 transform transition-all">
            <button type="button" onclick="closeModalDetailBerkas()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center font-bold text-lg leading-none transition">&times;</button>
            
            <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Berkas Rekomendasi</h3>
                    <p class="text-xs text-slate-500" id="detailMhsSub">-</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jalur Rekomendasi</span>
                    <span class="text-sm font-extrabold text-purple-700" id="detailJalurTitle">-</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Pembimbing 1</span>
                    <p class="text-xs text-slate-700 font-medium bg-slate-50 p-3 rounded-xl border border-slate-200 mt-1" id="detailCatatanDosen">-</p>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Berkas Pendukung Terlampir</span>
                    <div id="detailListFiles" class="space-y-2">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeModalDetailBerkas()" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- OVERLAY MULTI-DOKUMEN PREVIEW (MIRIP DOSEN_BIMBINGAN DINAMIS) -->
    <!-- ============================================================== -->
    <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-[10001] flex flex-col p-3 sm:p-5 gap-3 overflow-hidden bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <!-- Top Summary Bar -->
        <div id="previewHeaderBar" class="pointer-events-auto shrink-0 flex items-center justify-between bg-slate-900 text-white px-4 py-2.5 rounded-2xl shadow-xl border border-slate-700 max-w-full">
            <div class="flex items-center gap-2.5 truncate mr-3">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                <span class="font-extrabold text-xs" id="previewCountBadge">1 Dokumen Terbuka</span>
                <span class="text-[11px] text-slate-400 hidden md:inline">• Anda dapat membuka lebih dari 1 file sekaligus dan menggeser ke samping</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="closeAllPreviews()" class="px-3 py-1 rounded-xl bg-rose-600/90 hover:bg-rose-600 text-white font-bold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-xs">
                    <i class="bi bi-x-circle-fill"></i>
                    <span>Tutup Semua</span>
                </button>
            </div>
        </div>

        <!-- Horizontal Scrollable Cards Container -->
        <div id="wrapperPreviewBerkas" class="pointer-events-auto flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch pb-1">
            <!-- Populated dynamically via JS -->
        </div>
    </div>

    <!-- Script Handling -->
    <script>
        // ==============================================================
        // MULTI-CRITERIA FILTER (IDENTIK DOSEN_BIMBINGAN)
        // ==============================================================
        let filtersDW = [{ col: 'all', val: <?= !empty($search) ? json_encode($search) : "''" ?> }];

        function addFilterRowDW(col = 'all', val = '') {
            if (filtersDW.length >= 4) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Maksimal 4 kriteria filter.', timer: 1500, showConfirmButton: false });
                } else {
                    alert('Maksimal 4 kriteria filter.');
                }
                return;
            }
            filtersDW.push({ col, val });
            renderFilterRowsDW();
        }

        function removeFilterRowDW(idx) {
            if (filtersDW.length <= 1) return;
            filtersDW.splice(idx, 1);
            renderFilterRowsDW();
            doMultiSearchDW();
        }

        function renderFilterRowsDW() {
            const container = document.getElementById('filterRowsDW');
            if (!container) return;
            const COLUMNS = [
                { label: 'Semua Kolom', value: 'all' },
                { label: 'Nama Mahasiswa', value: 'nama' },
                { label: 'NIM', value: 'nim' },
                { label: 'Judul Tugas Akhir', value: 'judul' },
                { label: 'Pembimbing / Penguji', value: 'dosen' },
                { label: 'Tahap Bimbingan', value: 'tahap' },
                { label: 'Jalur Rekomendasi', value: 'jalur' }
            ];

            container.innerHTML = filtersDW.map((f, i) => `
                <div class="filter-row-dw">
                    <span class="text-[10px] font-bold text-slate-500 w-14 shrink-0">Filter #${i + 1}:</span>
                    <select class="filter-col-dw" data-idx="${i}">
                        ${COLUMNS.map(c => `<option value="${c.value}" ${c.value === f.col ? 'selected' : ''}>${c.label}</option>`).join('')}
                    </select>
                    <input type="text" class="filter-val-dw" data-idx="${i}"
                           value="${(f.val || '').replace(/"/g, '&quot;')}"
                           placeholder="Ketik kata kunci pencarian..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault();doMultiSearchDW();}">
                    ${i > 0 ? `<button type="button" class="btn-remove-filter-dw" onclick="removeFilterRowDW(${i})" title="Hapus filter"><i class="bi bi-x-lg"></i></button>` : ''}
                </div>
            `).join('');

            updateFilterBadgeDW();
        }

        function updateFilterBadgeDW() {
            const badge = document.getElementById('filterCountBadgeDW');
            const btnAdd = document.getElementById('btnAddFilterDW');
            if (badge) badge.textContent = `${filtersDW.length}/4`;
            if (btnAdd) {
                btnAdd.style.opacity = filtersDW.length >= 4 ? '0.5' : '1';
                btnAdd.style.pointerEvents = filtersDW.length >= 4 ? 'none' : 'auto';
            }
        }

        function doMultiSearchDW() {
            document.querySelectorAll('.filter-col-dw').forEach(sel => {
                const idx = parseInt(sel.dataset.idx);
                if (filtersDW[idx]) filtersDW[idx].col = sel.value;
            });
            document.querySelectorAll('.filter-val-dw').forEach(inp => {
                const idx = parseInt(inp.dataset.idx);
                if (filtersDW[idx]) filtersDW[idx].val = inp.value;
            });

            const rows = document.querySelectorAll('.mhs-row');
            const noDataRow = document.getElementById('noDataFilterRow');
            let visibleCount = 0;

            rows.forEach(row => {
                let matchAll = true;

                for (let f of filtersDW) {
                    const query = (f.val || '').trim().toLowerCase();
                    if (!query) continue;

                    let haystack = '';
                    if (f.col === 'all') {
                        haystack = row.getAttribute('data-all') || '';
                    } else {
                        haystack = row.getAttribute('data-' + f.col) || '';
                    }

                    if (!haystack.toLowerCase().includes(query)) {
                        matchAll = false;
                        break;
                    }
                }

                if (matchAll) {
                    row.style.display = '';
                    visibleCount++;
                    const numCell = row.querySelector('.row-number');
                    if (numCell) numCell.textContent = visibleCount;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noDataRow) {
                if (visibleCount === 0 && rows.length > 0) {
                    noDataRow.classList.remove('hidden');
                } else {
                    noDataRow.classList.add('hidden');
                }
            }
        }

        function resetFiltersDW() {
            filtersDW = [{ col: 'all', val: '' }];
            renderFilterRowsDW();
            doMultiSearchDW();
        }

        window.addFilterRowDW = addFilterRowDW;
        window.removeFilterRowDW = removeFilterRowDW;
        window.renderFilterRowsDW = renderFilterRowsDW;
        window.doMultiSearchDW = doMultiSearchDW;
        window.resetFiltersDW = resetFiltersDW;

        // ==============================================================
        // DYNAMIC MULTI-FILE PREVIEW SYSTEM (MIRIP DOSEN_BIMBINGAN)
        // ==============================================================
        window.activePreviews = [];

        function previewBerkasDynamic(fileUrl, label, studentName, nim) {
            if (!fileUrl) return;

            // Check if already open
            const existingIdx = window.activePreviews.findIndex(p => p.fileUrl === fileUrl);
            if (existingIdx > -1) {
                const card = document.getElementById('previewCard_' + window.activePreviews[existingIdx].id);
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', inline: 'center' });
                    card.classList.add('ring-4', 'ring-orange-500');
                    setTimeout(() => card.classList.remove('ring-4', 'ring-orange-500'), 1000);
                }
                return;
            }

            // Limit to max 6 documents simultaneously
            if (window.activePreviews.length >= 6) {
                window.activePreviews.shift();
            }

            const uniqueId = 'pv_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            window.activePreviews.push({
                id: uniqueId,
                fileUrl: fileUrl,
                label: label || 'Dokumen',
                studentName: studentName || 'Mahasiswa',
                nim: nim || '-'
            });

            renderAllPreviewCards();

            // Smooth scroll to newest card
            setTimeout(() => {
                const newCard = document.getElementById('previewCard_' + uniqueId);
                if (newCard) newCard.scrollIntoView({ behavior: 'smooth', inline: 'end' });
            }, 60);
        }

        function renderAllPreviewCards() {
            const container = document.getElementById('lihatBerkasContainer');
            const wrapper   = document.getElementById('wrapperPreviewBerkas');
            const countEl   = document.getElementById('previewCountBadge');

            if (!container || !wrapper) return;

            if (window.activePreviews.length === 0) {
                container.style.display = 'none';
                wrapper.innerHTML = '';
                document.body.style.overflow = '';
                return;
            }

            container.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            if (countEl) {
                countEl.textContent = `${window.activePreviews.length} Dokumen Terbuka`;
            }

            const total = window.activePreviews.length;
            let html = '';

            window.activePreviews.forEach((p, idx) => {
                const uniqueKey = p.id;
                const slotNum = idx + 1;

                html += `
                    <div class="preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-300 overflow-hidden flex flex-col shrink-0 animate-card-in" id="previewCard_${uniqueKey}">
                        <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                    ${total > 1 ? slotNum : '<i class="bi bi-file-pdf"></i>'}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[200px] sm:max-w-[280px]" title="${p.label}">${p.label}</h4>
                                    <p class="text-[10px] text-slate-300 font-medium truncate">${p.studentName} · <span class="font-mono text-slate-400">${p.nim}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="toggleFullscreenPreview('${uniqueKey}')" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Layar Penuh">
                                    <i class="bi bi-arrows-fullscreen" id="fsIcon_${uniqueKey}"></i>
                                </button>
                                <a href="${p.fileUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka di Tab Baru">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition cursor-pointer ml-0.5" title="Tutup Dokumen Ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex-1 relative bg-slate-100 overflow-hidden" style="min-height: 250px;">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-xs z-0" id="previewLoader_${uniqueKey}">
                                <i class="bi bi-arrow-repeat animate-spin text-2xl mb-1 text-orange-500"></i>
                                <span>Memuat dokumen...</span>
                            </div>
                            <iframe src="${p.fileUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 relative z-10" onload="document.getElementById('previewLoader_${uniqueKey}').style.display='none'" title="${p.label}"></iframe>
                        </div>

                        <div class="p-2.5 px-3.5 bg-white flex items-center justify-between text-xs shrink-0 gap-2 border-t border-slate-200">
                            <div class="flex items-center gap-2 truncate mr-2">
                                <span class="font-mono text-[11px] font-bold text-orange-600">${p.nim}</span>
                                <span class="text-slate-300">|</span>
                                <span class="text-[11px] text-slate-600 font-semibold truncate">${p.studentName}</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a href="${p.fileUrl}" download target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs text-[11px]">
                                    <i class="bi bi-download"></i><span>Unduh</span>
                                </a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold transition cursor-pointer text-[11px] shadow-2xs">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            wrapper.innerHTML = html;
        }

        function closeSinglePreview(index) {
            if (index < 0 || index >= window.activePreviews.length) return;
            window.activePreviews.splice(index, 1);
            renderAllPreviewCards();
        }

        function closeAllPreviews() {
            window.activePreviews = [];
            renderAllPreviewCards();
        }

        function toggleFullscreenPreview(uniqueKey) {
            const card = document.getElementById('previewCard_' + uniqueKey);
            const icon = document.getElementById('fsIcon_' + uniqueKey);
            if (!card) return;

            const isFs = card.classList.toggle('preview-fullscreen-mode');
            if (icon) {
                icon.className = isFs ? 'bi bi-fullscreen-exit text-orange-400 font-bold' : 'bi bi-arrows-fullscreen';
            }
        }

        // Backward compatibility fallback
        function openPdfPreview(url, title, subtitle) {
            previewBerkasDynamic(url, title, subtitle || '', '');
        }
        function closePdfModal() {
            closeAllPreviews();
        }

        // Logic dampak data yang dihapus / di-reset berdasarkan tahap asal & tujuan
        function getDowngradeImpacts(fromKey, toKey) {
            const impacts = [];
            const from = (fromKey || '').toLowerCase();
            const to = (toKey || '').toLowerCase();

            // Jika dari Sidang
            if (from === 'sidang') {
                impacts.push('Seluruh berkas naskah Sidang, nilai sidang dari 4 dosen (Pembimbing 1, Pembimbing 2, Penguji 1, Penguji 2), dan skor akhir dihapus.');
                impacts.push('Jadwal dan tempat sidang (tanggal, waktu, ruangan, dan tautan sidang) dihapus.');
                impacts.push('Akses upload BAP serta berkas BAP dari mahasiswa dicabut dan dihapus.');
            }

            // Jika dari Sidang atau Persyaratan Sidang turun ke Persyaratan Sidang atau di bawahnya
            if (from === 'sidang' || from === 'persyaratan_sidang') {
                impacts.push('Seluruh file berkas Persyaratan Sidang yang telah diunggah mahasiswa beserta status verifikasi Admin LAA dihapus.');
            }

            // Jika target Preview 3 atau di bawahnya (dari Sidang, Persyaratan Sidang, atau Preview 3)
            if (to === 'preview3' || to === 'preview2' || to === 'preview1') {
                impacts.push('Seluruh file berkas naskah dan riwayat upload di Preview 3 dihapus dari sistem.');
                impacts.push('Form rekomendasi Pembimbing 1 (Sidang/Non-Sidang), berkas pendukung, dan status lulus Preview 3 dicabut.');
            }

            // Jika target Preview 2 atau Preview 1
            if (to === 'preview2' || to === 'preview1') {
                impacts.push('Seluruh file berkas naskah dan riwayat upload di Preview 2 dihapus dari sistem.');
                impacts.push('Nilai Desk Evaluation (DE) dari 4 dosen, jadwal DE, dan status lulus Preview 2 di-reset.');
            }

            // Jika target Preview 1
            if (to === 'preview1') {
                impacts.push('Seluruh file berkas naskah dan riwayat upload di Preview 1 dihapus dari sistem serta status lulus Preview 1 dicabut.');
            }

            // Penegasan untuk tahap tujuan
            const stageLabels = {
                'preview1': 'Preview 1',
                'preview2': 'Preview 2',
                'preview3': 'Preview 3',
                'persyaratan_sidang': 'Persyaratan Sidang'
            };
            const targetLabel = stageLabels[to] || to;
            impacts.push(`Semua file pada tahap tujuan (${targetLabel}) ikut terhapus sehingga mahasiswa wajib mengunggah ulang berkas dari awal pada tahap ${targetLabel}.`);

            return impacts;
        }

        function updateDowngradeImpacts() {
            const fromKey = document.getElementById('dgTahapCurrentKey').value;
            const toKey   = document.getElementById('dgKeTahap').value;
            const listEl  = document.getElementById('dgImpactList');
            if (!listEl) return;

            listEl.innerHTML = '';
            const impacts = getDowngradeImpacts(fromKey, toKey);
            impacts.forEach(text => {
                const li = document.createElement('li');
                li.textContent = text;
                listEl.appendChild(li);
            });
        }

        function openModalDowngrade(nim, nama, tahapKey, tahapDisplay) {
            document.getElementById('dgNim').value = nim;
            document.getElementById('dgNimText').innerText = nim;
            document.getElementById('dgNama').innerText = nama;
            document.getElementById('dgTahapCurrent').innerText = tahapDisplay;
            document.getElementById('dgTahapCurrentKey').value = tahapKey;
            document.getElementById('dgAlasan').value = '';

            const sel = document.getElementById('dgKeTahap');
            sel.innerHTML = '';

            // Hierarchy: preview1=1, preview2=2, preview3=3, persyaratan_sidang=4, sidang=5
            const stages = [
                { key: 'preview1', label: 'Preview 1' },
                { key: 'preview2', label: 'Preview 2' },
                { key: 'preview3', label: 'Preview 3' },
                { key: 'persyaratan_sidang', label: 'Persyaratan Sidang' }
            ];

            const rankMap = { 'preview1': 1, 'preview2': 2, 'preview3': 3, 'persyaratan_sidang': 4, 'sidang': 5 };
            const currRank = rankMap[tahapKey] || 1;

            const allowed = stages.filter(s => rankMap[s.key] < currRank);

            if (allowed.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Dapat Downgrade',
                    text: 'Mahasiswa sudah berada di tahap terendah (Preview 1).'
                });
                return;
            }

            allowed.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.key;
                opt.innerText = s.label;
                sel.appendChild(opt);
            });

            // Perbarui list dampak secara langsung
            updateDowngradeImpacts();

            document.getElementById('modalDowngrade').classList.remove('hidden');
        }

        function closeModalDowngrade() {
            document.getElementById('modalDowngrade').classList.add('hidden');
        }

        function submitDowngrade(e) {
            e.preventDefault();

            const nim     = document.getElementById('dgNim').value;
            const keTahap = document.getElementById('dgKeTahap').value;
            const alasan  = document.getElementById('dgAlasan').value;
            const btn     = document.getElementById('btnSubmitDowngrade');

            if (!alasan.trim()) {
                Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Mohon masukkan alasan downgrade.' });
                return;
            }

            const currentTahapKey = document.getElementById('dgTahapCurrentKey').value;
            const targetSelect    = document.getElementById('dgKeTahap');
            const targetLabel     = targetSelect.options[targetSelect.selectedIndex]?.text || keTahap;
            const currentDisplay  = document.getElementById('dgTahapCurrent').innerText;
            const namaMhs         = document.getElementById('dgNama').innerText;
            const impacts         = getDowngradeImpacts(currentTahapKey, keTahap);

            const impactHtml = impacts.map(item => `<li class="flex items-start gap-1.5 text-xs text-rose-900"><i class="bi bi-x-circle-fill text-rose-600 mt-0.5 shrink-0 text-[10px]"></i><span>${item}</span></li>`).join('');

            Swal.fire({
                title: 'Konfirmasi Penurunan Tahap',
                html: `
                    <div class="space-y-3 text-left">
                        <p class="text-xs text-slate-600">
                            Apakah Anda yakin ingin menurunkan mahasiswa <strong>${nim}</strong> (${namaMhs}) dari <strong>${currentDisplay}</strong> ke <strong>${targetLabel}</strong>?
                        </p>
                        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200">
                            <p class="text-[11px] font-extrabold text-rose-800 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                                Data yang akan dihapus / di-reset:
                            </p>
                            <ul class="space-y-1.5">
                                ${impactHtml}
                            </ul>
                        </div>
                        <p class="text-[11px] text-slate-400 italic text-center">Tindakan ini tidak dapat dibatalkan secara otomatis.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-arrow-down-circle-fill mr-1"></i> Ya, Downgrade!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

                    const formData = new FormData();
                    formData.append('nim', nim);
                    formData.append('dari_tahap', currentTahapKey);
                    formData.append('ke_tahap', keTahap);
                    formData.append('alasan', alasan);

                    fetch('<?= site_url("adminlayanan/downgrade_preview_ajax") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan komunikasi server.' });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check2-circle text-sm"></i> <span>Konfirmasi Downgrade</span>';
                    });
                }
            });
        }

        function openModalDetailBerkas(nim, nama, jalur, catatan, files) {
            document.getElementById('detailMhsSub').innerText = `${nama} (${nim})`;
            document.getElementById('detailJalurTitle').innerText = jalur || 'Non-Sidang';
            document.getElementById('detailCatatanDosen').innerText = catatan || 'Tidak ada catatan khusus dari dosen.';

            const container = document.getElementById('detailListFiles');
            container.innerHTML = '';

            let hasFiles = false;
            if (files && typeof files === 'object') {
                Object.keys(files).forEach(k => {
                    const item = files[k];
                    if (item && item.file) {
                        hasFiles = true;
                        const labelText = item.label || k;
                        const safeLabel = labelText.replace(/'/g, "\\'");
                        const safeNama  = nama.replace(/'/g, "\\'");
                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs';
                        div.innerHTML = `
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="truncate">
                                    <span class="font-bold text-slate-800 block truncate">${labelText}</span>
                                    <span class="text-[10px] text-slate-400 truncate block">${item.original_name || 'Dokumen'}</span>
                                </div>
                            </div>
                            <button type="button" onclick="previewBerkasDynamic('${item.file}', '${safeLabel}', '${safeNama}', '${nim}')" class="px-3 py-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white font-bold text-[11px] shrink-0 transition flex items-center gap-1 shadow-xs cursor-pointer">
                                <i class="bi bi-eye-fill text-[11px]"></i>
                                <span>Buka File</span>
                            </button>
                        `;
                        container.appendChild(div);
                    }
                });
            }

            if (!hasFiles) {
                container.innerHTML = '<div class="text-center py-4 text-slate-400 italic text-xs">Tidak ada file fisik yang diunggah.</div>';
            }

            document.getElementById('modalDetailBerkas').classList.remove('hidden');
        }

        function closeModalDetailBerkas() {
            document.getElementById('modalDetailBerkas').classList.add('hidden');
        }

        // Initialize filters and close modals on dark backdrop click & Escape key
        document.addEventListener('DOMContentLoaded', () => {
            renderFilterRowsDW();
            if (filtersDW[0].val) {
                doMultiSearchDW();
            }

            const lbContainer = document.getElementById('lihatBerkasContainer');
            if (lbContainer) {
                lbContainer.addEventListener('click', (e) => {
                    if (e.target === lbContainer) closeAllPreviews();
                });
            }

            const modalDowngrade = document.getElementById('modalDowngrade');
            if (modalDowngrade) {
                modalDowngrade.addEventListener('click', (e) => {
                    if (e.target === modalDowngrade) closeModalDowngrade();
                });
            }

            const modalDetail = document.getElementById('modalDetailBerkas');
            if (modalDetail) {
                modalDetail.addEventListener('click', (e) => {
                    if (e.target === modalDetail) closeModalDetailBerkas();
                });
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const fsCard = document.querySelector('.preview-card-item.preview-fullscreen-mode');
                if (fsCard) {
                    fsCard.classList.remove('preview-fullscreen-mode');
                    const icon = fsCard.querySelector('[id^="fsIcon_"]');
                    if (icon) icon.className = 'bi bi-arrows-fullscreen';
                    return;
                }
                if (window.activePreviews && window.activePreviews.length > 0) {
                    closeSinglePreview(window.activePreviews.length - 1);
                    return;
                }
                const modalDetail = document.getElementById('modalDetailBerkas');
                if (modalDetail && !modalDetail.classList.contains('hidden')) {
                    closeModalDetailBerkas();
                    return;
                }
                const modalDowngrade = document.getElementById('modalDowngrade');
                if (modalDowngrade && !modalDowngrade.classList.contains('hidden')) {
                    closeModalDowngrade();
                    return;
                }
            }
        });
    </script>

</body>
</html>
