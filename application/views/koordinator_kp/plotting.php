<?php
/**
 * Halaman Plotting Pembimbing 1 Kerja Praktik (KP)
 * Path: application/views/koordinator_kp/plotting.php
 *
 * Akses: Koordinator KP (role_id 23) & Admin System (role_id 1)
 *
 * Syarat Tampil: Mahasiswa HANYA muncul jika berkas persyaratannya sudah di-ACC oleh Admin Layanan (LAA) di Tab 1.
 * Fitur: Multi-search bar, pagination, rotating border table, modal plotting pembimbing, & update realtime.
 */

$sub_tab = $sub_tab ?? 'belum_diplot';
$total_belum_diplot = count($belum_diplot ?? []);
$total_sudah_diplot = count($sudah_diplot ?? []);
$total_semua        = $total_belum_diplot + $total_sudah_diplot;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Plotting Pembimbing 1 Kerja Praktik (KP) - Koordinator KP'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1),
                        width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }
        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }
            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }
        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Filter Multi Kriteria */
        .filter-row-dw {
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeInDownSmooth 0.3s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        .filter-col-dw {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 12px;
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
            border-radius: 12px;
            padding: 8px 14px;
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
            width: 32px;
            height: 32px;
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

        /* Rotating Border Table Wrap */
        @keyframes spinRotatingBorder { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .table-rotating-border-wrap {
            position: relative; border-radius: 18px; padding: 2px; overflow: hidden;
            box-shadow: 0 12px 36px -8px rgba(234, 88, 12, 0.15), 0 4px 16px rgba(71, 85, 105, 0.06); background: #ffffff;
        }
        .table-rotating-border-spin {
            position: absolute; inset: -350%; pointer-events: none; opacity: 0.95;
            background: conic-gradient(from 90deg at 50% 50%, #ea580c 0%, #f97316 12%, #ffffff 22%, #cbd5e1 35%, #475569 48%, #1e293b 58%, #ea580c 68%, #ffffff 80%, #94a3b8 90%, #ea580c 100%);
            animation: spinRotatingBorder 8s linear infinite;
        }
        .table-rotating-border-inner { position: relative; z-index: 10; width: 100%; background: #ffffff; border-radius: 16px; overflow: hidden; }
        .table-custom-rounded { border-collapse: separate !important; border-spacing: 0 !important; width: 100%; }
        .table-custom-rounded thead tr th:first-child { border-top-left-radius: 16px; }
        .table-custom-rounded thead tr th:last-child { border-top-right-radius: 16px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; gap: 0.375rem; border: 1px solid transparent; }
        .badge-success { background-color: #d1fae5; color: #065f46; border-color: #34d399; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border-color: #fbbf24; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; border-color: #fb7185; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .badge-primary { background-color: #e0e7ff; color: #3730a3; border-color: #818cf8; }

        /* Pagination Buttons */
        .pagination-btn {
            width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 11px; border: 1.5px solid #e2e8f0; background: #fff; color: #475569;
            font-size: 0.75rem; font-weight: 800; cursor: pointer; transition: all 0.15s ease;
        }
        .pagination-btn:hover:not(:disabled):not(.active) { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
        .pagination-btn.active {
            background: linear-gradient(135deg, #ea580c, #f97316); border-color: #ea580c;
            color: #fff; box-shadow: 0 4px 12px rgba(234,88,12,0.3); cursor: default;
        }
        .pagination-btn:disabled { opacity: 0.35; cursor: not-allowed; }
        .pagination-ellipsis { padding: 0 4px; color: #94a3b8; font-weight: 800; font-size: 0.75rem; }

        @keyframes popInCard { 0% { opacity: 0; transform: scale(0.92) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes fadeInDownSmooth { 0% { opacity: 0; transform: translateY(-12px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-pop-in { animation: popInCard 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }

        /* Multi-Preview Container (Matching dosen_bimbingan) */
        #lihatBerkasContainer {
            position: fixed; inset: 0; pointer-events: none; z-index: 9999; display: none;
            align-items: stretch; justify-content: flex-start; padding: 1rem 1.25rem; gap: 1.25rem;
            overflow: hidden; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px);
        }
        #lihatBerkasContainer.active { display: flex; pointer-events: auto; }

        #wrapperDaftarMhs {
            display: flex; flex-direction: column; gap: 1rem;
            width: 480px; min-width: 420px; max-width: 520px;
            overflow-y: auto; overflow-x: hidden; flex-shrink: 0;
            padding: 0.25rem; height: 94vh; max-height: 96vh; pointer-events: none;
        }
        #wrapperDaftarMhs::-webkit-scrollbar { width: 4px; }
        #wrapperDaftarMhs::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 9999px; }

        #wrapperPreviewBerkas {
            display: none; flex: 1; overflow-x: auto; overflow-y: hidden; gap: 1.25rem;
            padding: 0.25rem; align-items: stretch; height: 94vh; max-height: 96vh; pointer-events: none;
        }
        #wrapperPreviewBerkas.active { display: flex; }
        #wrapperPreviewBerkas::-webkit-scrollbar { height: 6px; }
        #wrapperPreviewBerkas::-webkit-scrollbar-thumb { background: rgba(249, 115, 22, 0.6); border-radius: 9999px; }

        .student-card-item {
            pointer-events: auto; background: white; border-radius: 1.25rem;
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.25);
            border: 1px solid #e2e8f0; overflow: hidden; display: flex;
            flex-direction: column; flex-shrink: 0; width: 100%; max-height: 94vh;
        }

        .preview-card-item {
            pointer-events: auto; background: white; border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);
            border: 1px solid #e2e8f0; overflow: hidden; display: flex;
            flex-direction: column; flex-shrink: 0;
            width: 820px; max-width: 72vw; height: 94vh; max-height: 96vh;
        }

        /* Fullscreen mode via CSS overlay */
        .preview-card-item.preview-fullscreen-mode {
            position: fixed !important;
            top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
            width: 100vw !important; height: 100vh !important;
            max-width: 100vw !important; max-height: 100vh !important;
            z-index: 999999 !important;
            border-radius: 0 !important; border: none !important;
            box-shadow: none !important; margin: 0 !important;
        }
        .preview-card-item.preview-fullscreen-mode .preview-header {
            background: rgba(15, 23, 42, 0.96) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .preview-card-item.preview-fullscreen-mode .preview-footer {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 -6px 20px rgba(0, 0, 0, 0.12);
        }
        .preview-card-item.preview-fullscreen-mode .preview-body {
            flex: 1 !important; min-height: 0 !important;
        }

        .preview-card-item .preview-body {
            flex: 1; min-height: 0; position: relative; background: #e2e8f0; overflow: hidden;
        }
        .preview-card-item .preview-body iframe {
            width: 100%; height: 100%; border: 0; position: relative; z-index: 10;
        }
        .preview-card-item .preview-body .loader {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            z-index: 5; background: #e2e8f0; color: #94a3b8;
            font-size: 0.75rem; font-weight: 600; gap: 0.5rem;
        }
        .preview-card-item .preview-body .loader i { font-size: 1.5rem; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .preview-card-item .preview-header { flex-shrink: 0; }
        .preview-card-item .preview-footer { flex-shrink: 0; gap: 0.5rem; }
        .btn-3d-orange {
            background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; border: none;
            transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(234,88,12,0.3);
        }
        .btn-3d-orange:hover { transform: scale(1.03); box-shadow: 0 6px 20px rgba(234,88,12,0.4); }

        @media (max-width: 768px) {
            #lihatBerkasContainer {
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: flex-start !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding: 0.75rem !important;
                gap: 0.75rem !important;
            }
            #wrapperDaftarMhs {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                max-height: none !important;
                overflow-y: visible !important;
                padding: 0 !important;
            }
            #wrapperPreviewBerkas {
                width: 100% !important;
                flex: none !important;
                max-height: none !important;
                overflow-x: auto !important;
                overflow-y: visible !important;
                padding: 0 !important;
            }
            .preview-card-item {
                width: 90vw !important;
                max-width: 90vw !important;
                height: 60vh !important;
                max-height: 60vh !important;
            }
            .student-card-item { max-height: none !important; }
        }

        /* Modal Overlay Plotting (z-index above multi-preview) */
        #modalPlottingOverlay {
            position: fixed; inset: 0; z-index: 10005; display: none;
            background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px);
            align-items: center; justify-content: center; padding: 1rem;
        }
        #modalPlottingOverlay.active { display: flex; }
        .plotting-modal-box {
            background: #ffffff; border-radius: 1.5rem; width: 620px; max-width: 96vw;
            overflow: hidden; display: flex; flex-direction: column;
            box-shadow: 0 25px 60px -15px rgba(15,23,42,0.5);
            border: 1px solid #fed7aa;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <!-- Unified Curved Animated Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow">
    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-rose-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <!-- HERO HEADER (Gaya Dosen Bimbingan / Koordinator TA) -->
        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold">
                        <i class="bi bi-person-fill-gear text-amber-200"></i> Modul Koordinator Kerja Praktik (KP)
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Plotting Dosen Pembimbing 1 KP
                    </h1>
                    <p class="text-xs sm:text-sm text-orange-100 font-normal leading-relaxed">
                        Tentukan Dosen Pembimbing 1 untuk mahasiswa yang seluruh berkas persyaratannya telah diverifikasi dan disetujui (ACC) oleh Admin Layanan (LAA).
                    </p>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-black/25 border border-white/20 text-xs font-semibold text-orange-100">
                        <i class="bi bi-info-circle-fill text-amber-300"></i>
                        <span>Mahasiswa hanya muncul setelah seluruh berkas persyaratan berstatus <strong>Disetujui LAA</strong>.</span>
                    </div>
                </div>

                <!-- Sub-tab Navigasi Plotting -->
                <div class="w-full xl:w-auto bg-black/25 backdrop-blur-xl rounded-3xl p-3 border border-white/20 shadow-2xl shrink-0">
                    <div class="flex items-center gap-2">
                        <!-- Sub-tab Belum Diplot -->
                        <button type="button" onclick="switchSubTabPlotting('belum_diplot')" id="btnTabBelumDiplot"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($sub_tab === 'belum_diplot') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-person-plus-fill text-base"></i>
                            <div class="text-left">
                                <span class="block">Belum Di-Plot</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Menunggu Penetapan P1</span>
                            </div>
                            <span class="ml-1 px-2.5 py-0.5 rounded-full bg-black/30 text-amber-200 text-xs font-black" id="badgeCountBelumDiplot">
                                <?= $total_belum_diplot ?>
                            </span>
                        </button>

                        <!-- Sub-tab Sudah Diplot -->
                        <button type="button" onclick="switchSubTabPlotting('sudah_diplot')" id="btnTabSudahDiplot"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($sub_tab === 'sudah_diplot') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-person-check-fill text-base"></i>
                            <div class="text-left">
                                <span class="block">Sudah Di-Plot</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Telah Memiliki P1</span>
                            </div>
                            <span class="ml-1 px-2.5 py-0.5 rounded-full bg-black/30 text-emerald-300 text-xs font-black" id="badgeCountSudahDiplot">
                                <?= $total_sudah_diplot ?>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTAINER CARD TABEL PLOTTING -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-orange-500/5 space-y-6 border border-slate-200">

            <div class="flex flex-wrap items-center justify-between border-b border-orange-100 pb-4 gap-3">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-people-fill text-orange-500 text-xl"></i>
                        Daftar Mahasiswa Siap Bimbingan KP
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5" id="descSubTabInfo">
                        Mahasiswa yang belum ditetapkan Dosen Pembimbing 1.
                    </p>
                </div>
                <div class="text-xs font-bold text-slate-600 px-3.5 py-1.5 rounded-full bg-orange-50 border border-orange-200">
                    Total Mahasiswa Siap: <span id="badgeTotalSiap" class="text-orange-600 font-extrabold"><?= $total_semua ?></span> Mahasiswa
                </div>
            </div>

            <!-- Multi-kriteria Search Bar (Identik Dosen Bimbingan) -->
            <div id="multiFilterPanelPlotting" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                        <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnAddFilterPlotting" onclick="addFilterRowPlotting()"
                                class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-plus-lg"></i> Tambah Filter
                            <span id="filterCountBadgePlotting" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                        </button>
                        <button type="button" onclick="resetFiltersPlotting()"
                                class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>

                <div id="filterRowsPlotting" class="space-y-2 mb-3"></div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                    <p class="text-[10px] text-slate-500 font-medium italic">
                        <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk memfilter daftar mahasiswa.
                    </p>
                    <button type="button" onclick="applyFiltersPlotting()"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                        <i class="bi bi-search"></i> Cari Data
                    </button>
                </div>
            </div>

            <!-- Kontrol Pagination -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                    <select id="perPagePlotting" onchange="changePerPagePlotting()" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer outline-none">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">Semua</option>
                    </select>
                    <span class="text-[11px] text-slate-500 font-medium" id="paginationInfoPlotting"></span>
                </div>
                <div class="flex items-center gap-1 flex-wrap" id="paginationButtonsPlotting"></div>
            </div>

            <!-- Tabel Rotating Border -->
            <div class="table-rotating-border-wrap">
                <span class="table-rotating-border-spin"></span>
                <div class="table-rotating-border-inner overflow-x-auto">
                    <table class="table-custom-rounded text-left text-sm w-full">
                        <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-3 text-center w-10">
                                    <input type="checkbox" id="selectAllPlotting" onchange="toggleSelectAllPlotting(this)" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer" title="Pilih Semua">
                                </th>
                                <th class="py-4 px-3 text-center w-12">No</th>
                                <th class="py-4 px-4">Mahasiswa &amp; Program Studi</th>
                                <th class="py-4 px-4">Instansi Tempat KP &amp; Posisi</th>
                                <th class="py-4 px-4 text-center">Verifikasi Syarat LAA</th>
                                <th class="py-4 px-4">Dosen Pembimbing 1</th>
                                <th class="py-4 px-4 text-center">Aksi Plotting</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodyPlotting">
                            <tr>
                                <td colspan="7" class="text-center py-10 text-slate-400">
                                    <i class="bi bi-arrow-repeat animate-spin text-lg mr-2"></i> Memuat data mahasiswa siap bimbingan...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    
        <!-- FLOATING BATCH ACTION BAR (PLOTTING MASSAL) -->
        <div id="batchActionBarPlotting" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300 animate-pop-in">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="selectedCountBadgePlotting">0</span>
                <span class="text-xs font-bold tracking-tight">Mahasiswa Terpilih</span>
            </div>
            <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="openModalPlottingMassal()" class="px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                    <i class="bi bi-person-fill-gear text-base"></i> <span id="btnBatchPlotLabel">Plot Pembimbing Massal</span>
                </button>
                <button type="button" onclick="unselectAllStudentsPlotting()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                    <i class="bi bi-x-lg"></i> Batal
                </button>
            </div>
        </div>
    </main>
    </div>

    <!-- =========================================================================
         MODAL DIALOG PLOTTING DOSEN PEMBIMBING 1
         ========================================================================= -->
    <!-- =========================================================================
         MULTI-PREVIEW BERKAS CONTAINER (MATCHING DOSEN BIMBINGAN)
         ========================================================================= -->
    <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-50 flex items-stretch p-3 sm:p-5 gap-4 overflow-hidden" style="display: none;" onclick="handleContainerBackdropClick(event)">
        <div id="wrapperDaftarMhs" class="flex flex-col gap-3 overflow-y-auto flex-shrink-0"></div>
        <div id="wrapperPreviewBerkas" class="flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch hidden"></div>
    </div>

    <div id="modalPlottingOverlay" onclick="if(event.target===this) closeModalPlotting()">
        <div class="plotting-modal-box animate-pop-in">
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-orange-600 to-amber-500 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg">
                        <i class="bi bi-person-fill-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold leading-tight">Plotting Dosen Pembimbing 1 KP</h3>
                        <p class="text-xs text-orange-100">Pilih dosen yang akan membimbing mahasiswa dalam Kerja Praktik.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalPlotting()" class="text-white hover:text-orange-200 text-2xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 overflow-y-auto max-h-[75vh]">
                <!-- Ringkasan Mahasiswa -->
                <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-200/80 space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-orange-700 block">Identitas Mahasiswa</span>
                            <h4 class="text-sm font-extrabold text-slate-900" id="plotModalNamaMhs">-</h4>
                            <p class="text-xs text-orange-600 font-bold" id="plotModalNimMhs">-</p>
                        </div>
                        <span class="badge badge-success text-[10px]">Syarat Disetujui LAA</span>
                    </div>
                    <div class="pt-2 border-t border-orange-200/60 text-xs text-slate-700 grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-bold">Program Studi:</span>
                            <span class="font-semibold text-slate-800" id="plotModalProdiMhs">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-bold">Tempat KP:</span>
                            <span class="font-semibold text-slate-800 truncate block" id="plotModalPerusahaanMhs">-</span>
                        </div>
                    </div>
                </div>

                <!-- Form Pilihan Dosen Pembimbing 1 -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Pilih Dosen Pembimbing 1 <span class="text-rose-500">*</span>
                    </label>

                    <!-- Filter / Cari Dosen Cepat -->
                    <div class="relative">
                        <input type="text" id="plotDosenSearchInput" oninput="filterDosenOptions(this.value)"
                               placeholder="Ketik nama atau NIP dosen untuk memfilter..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-orange-400 outline-none">
                        <i class="bi bi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>

                    <!-- Select List Dosen -->
                    <select id="plotDosenSelect" size="8" class="w-full p-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-orange-400 outline-none cursor-pointer bg-white">
                        <!-- Diisi via JS -->
                    </select>
                    <p class="text-[11px] text-slate-500 italic">
                        <i class="bi bi-info-circle"></i> Klik salah satu dosen dari daftar di atas, kemudian klik <strong>Simpan Plotting</strong>.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 px-6 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModalPlotting()"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitPlotting()" id="btnSubmitPlotting"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Simpan Plotting</span>
                </button>
            </div>
        </div>
    </div>

    <!-- DATASET DARI SERVER UNTUK JAVASCRIPT TABLE & FILTER -->
    <script>
        const RAW_DATA = {
            belum_diplot: <?= json_encode($belum_diplot ?? []); ?>,
            sudah_diplot: <?= json_encode($sudah_diplot ?? []); ?>,
            dosen_list:   <?= json_encode($dosen_list ?? []); ?>
        };

        const BASE_URL = '<?= rtrim(base_url(), '/') . '/'; ?>';
        const SITE_URL = '<?= rtrim(site_url(), '/') . '/'; ?>';

        let currentSubTabPlotting = '<?= $sub_tab ?>';

        const COLUMNS_PLOTTING = [
            { value: 'all', label: 'Semua Kolom' },
            { value: 'nim', label: 'NIM Mahasiswa' },
            { value: 'nama', label: 'Nama Mahasiswa' },
            { value: 'prodi', label: 'Program Studi' },
            { value: 'perusahaan', label: 'Instansi / Tempat KP' },
            { value: 'pembimbing', label: 'Dosen Pembimbing 1' }
        ];

        let filtersPlotting = [{ col: 'all', val: '' }];
        let appliedFiltersPlotting = [{ col: 'all', val: '' }];
        let pagePlotting = 1;
        let perPagePlotting = 25;

        let currentActiveNimToPlot = null;

        // =========================================================================
        // SUB-TAB SWITCHING (BELUM DI-PLOT VS SUDAH DI-PLOT)
        // =========================================================================
        function switchSubTabPlotting(sub) {
            currentSubTabPlotting = sub;
            const btnBelum = document.getElementById('btnTabBelumDiplot');
            const btnSudah = document.getElementById('btnTabSudahDiplot');
            const desc = document.getElementById('descSubTabInfo');

            if (sub === 'belum_diplot') {
                btnBelum.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnSudah.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                desc.textContent = 'Mahasiswa yang seluruh berkas persyaratannya telah di-ACC dan belum ditetapkan Dosen Pembimbing 1.';
            } else {
                btnSudah.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnBelum.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                desc.textContent = 'Mahasiswa yang sudah memiliki Dosen Pembimbing 1 (Anda dapat mengubah / re-plotting jika diperlukan).';
            }

            pagePlotting = 1;
            unselectAllStudentsPlotting();
            renderTablePlotting();
                        if (window.activeLihatBerkasNims && window.activeLihatBerkasNims.length > 0) {
                            refreshLihatBerkasView();
                        }

            const url = new URL(window.location);
            url.searchParams.set('subtab', sub);
            window.history.replaceState({}, '', url);
        }

        // =========================================================================
        // MULTI-FILTER ROWS RENDERING
        // =========================================================================
        function renderFilterRowsPlotting() {
            const container = document.getElementById('filterRowsPlotting');
            if (!container) return;

            container.innerHTML = filtersPlotting.map((f, i) => `
                <div class="filter-row-dw">
                    <span class="text-[10px] font-bold text-slate-500 w-16 shrink-0">Kriteria #${i + 1}:</span>
                    <select class="filter-col-dw" data-idx="${i}">
                        ${COLUMNS_PLOTTING.map(c => `<option value="${c.value}" ${c.value === f.col ? 'selected' : ''}>${c.label}</option>`).join('')}
                    </select>
                    <input type="text" class="filter-val-dw" data-idx="${i}"
                           value="${(f.val || '').replace(/"/g, '&quot;')}"
                           placeholder="Ketik kata kunci pencarian..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault();applyFiltersPlotting();}">
                    ${i > 0 ? `<button type="button" class="btn-remove-filter-dw" onclick="removeFilterRowPlotting(${i})" title="Hapus filter"><i class="bi bi-x-lg"></i></button>` : ''}
                </div>
            `).join('');

            updateFilterBadgePlotting();
        }

        function updateFilterBadgePlotting() {
            const badge = document.getElementById('filterCountBadgePlotting');
            const btnAdd = document.getElementById('btnAddFilterPlotting');
            if (badge) badge.textContent = `${filtersPlotting.length}/4`;
            if (btnAdd) {
                btnAdd.style.opacity = filtersPlotting.length >= 4 ? '0.5' : '1';
                btnAdd.style.pointerEvents = filtersPlotting.length >= 4 ? 'none' : 'auto';
            }
        }

        function addFilterRowPlotting() {
            if (filtersPlotting.length >= 4) return;
            filtersPlotting.push({ col: 'all', val: '' });
            renderFilterRowsPlotting();
        }

        function removeFilterRowPlotting(idx) {
            if (filtersPlotting.length <= 1) return;
            filtersPlotting.splice(idx, 1);
            renderFilterRowsPlotting();
        }

        function applyFiltersPlotting() {
            document.querySelectorAll('#filterRowsPlotting .filter-col-dw').forEach(sel => {
                const idx = parseInt(sel.dataset.idx);
                if (filtersPlotting[idx]) filtersPlotting[idx].col = sel.value;
            });
            document.querySelectorAll('#filterRowsPlotting .filter-val-dw').forEach(inp => {
                const idx = parseInt(inp.dataset.idx);
                if (filtersPlotting[idx]) filtersPlotting[idx].val = inp.value;
            });

            appliedFiltersPlotting = filtersPlotting.map(f => ({ ...f }));
            pagePlotting = 1;
            renderTablePlotting();
        }

        function resetFiltersPlotting() {
            filtersPlotting = [{ col: 'all', val: '' }];
            appliedFiltersPlotting = [{ col: 'all', val: '' }];
            pagePlotting = 1;
            renderFilterRowsPlotting();
            renderTablePlotting();
        }

        function matchMultiFiltersPlotting(item, filters) {
            return filters.every(f => {
                const query = (f.val || '').trim().toLowerCase();
                if (!query) return true;
                const col = f.col;

                if (col === 'nim') return (item.nim || '').toLowerCase().includes(query);
                if (col === 'nama') return (item.nama || '').toLowerCase().includes(query);
                if (col === 'prodi') return (item.prodi || '').toLowerCase().includes(query);
                if (col === 'perusahaan') return (item.nama_perusahaan || '').toLowerCase().includes(query);
                if (col === 'pembimbing') return (item.pembimbing1_nama || '').toLowerCase().includes(query);
                // 'all'
                return (
                    (item.nim || '').toLowerCase().includes(query) ||
                    (item.nama || '').toLowerCase().includes(query) ||
                    (item.prodi || '').toLowerCase().includes(query) ||
                    (item.nama_perusahaan || '').toLowerCase().includes(query) ||
                    (item.pembimbing1_nama || '').toLowerCase().includes(query)
                );
            });
        }

        // =========================================================================
        // RENDERING TABEL PLOTTING
        // =========================================================================
        function renderTablePlotting() {
            const rawList = (currentSubTabPlotting === 'belum_diplot') ? RAW_DATA.belum_diplot : RAW_DATA.sudah_diplot;
            const filtered = rawList.filter(item => matchMultiFiltersPlotting(item, appliedFiltersPlotting));
            const total = filtered.length;

            const tbody = document.getElementById('tbodyPlotting');
            if (!tbody) return;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400">
                            <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-sm">Tidak ada mahasiswa ${currentSubTabPlotting === 'belum_diplot' ? 'yang belum di-plot' : 'yang sudah di-plot'}.</p>
                            <p class="text-xs text-slate-400 mt-1">Hanya mahasiswa yang persyaratannya disetujui Admin LAA yang akan muncul di halaman ini.</p>
                        </td>
                    </tr>
                `;
                renderPaginationControlsPlotting(0, 0, 0);
                return;
            }

            const effectivePerPage = perPagePlotting >= 999999 ? total : perPagePlotting;
            const totalPages = Math.ceil(total / effectivePerPage);
            if (pagePlotting > totalPages) pagePlotting = totalPages;

            const startIdx = (pagePlotting - 1) * effectivePerPage;
            const endIdx = Math.min(startIdx + effectivePerPage, total);
            const pagedItems = filtered.slice(startIdx, endIdx);

            let rowsHtml = '';
            pagedItems.forEach((mhs, idx) => {
                const rowNo = startIdx + idx + 1;
                const hasP1 = mhs.has_pembimbing1 && mhs.pembimbing1_nama;

                let p1Display = '';
                if (hasP1) {
                    p1Display = `
                        <div>
                            <span class="text-xs font-bold text-slate-900 block truncate">${mhs.pembimbing1_nama}</span>
                            <span class="text-[10px] text-slate-500 block">NIP / Kode: ${mhs.dosen_pembimbing1 || '-'}</span>
                        </div>
                    `;
                } else {
                    p1Display = `<span class="badge badge-warning text-xs"><i class="bi bi-hourglass-split"></i> Belum Di-Plot</span>`;
                }

                const isChecked = window.selectedPlottingNims.has(mhs.nim);
                rowsHtml += `
                    <tr class="hover:bg-orange-50/40 transition ${isChecked ? 'bg-orange-50/70' : ''}">
                        <td class="py-4 px-3 text-center">
                            <input type="checkbox" class="cb-mhs-plotting w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer"
                                   value="${mhs.nim}" ${isChecked ? 'checked' : ''} onchange="handleCheckboxChangePlotting()">
                        </td>
                        <td class="py-4 px-3 text-center font-bold text-slate-500">${rowNo}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 font-extrabold flex items-center justify-center shrink-0 text-xs">
                                    ${(mhs.nama || 'M').charAt(0).toUpperCase()}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm truncate">${mhs.nama || 'Mahasiswa'}</h4>
                                    <span class="text-orange-600 font-bold text-xs">${mhs.nim}</span>
                                    <span class="text-slate-400 text-xs ml-1">• ${mhs.prodi || 'Informatika'}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="min-w-0">
                                <span class="font-bold text-slate-800 text-xs block truncate">${mhs.nama_perusahaan || '-'}</span>
                                <span class="text-[11px] text-slate-500 block truncate">${mhs.posisi_pekerjaan || mhs.kota || '-'}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="badge badge-success text-xs">
                                <i class="bi bi-patch-check-fill"></i> Disetujui LAA
                            </span>
                        </td>
                        <td class="py-4 px-4">${p1Display}</td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                <button type="button" onclick="toggleLihatBerkasPanel('${mhs.nim}')" id="btnLihatBerkas_${mhs.nim}"
                                        class="inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform btn-3d-orange">
                                    <i class="bi bi-folder-open text-xs"></i>
                                    <span>Lihat Berkas</span>
                                    <span class="bg-white/30 text-white px-1.5 py-0.5 rounded-full text-[10px] font-black">${mhs.uploaded_count || (mhs.files ? mhs.files.filter(f => f.file && f.file.length > 0).length : 0)}</span>
                                </button>
                                <button type="button" onclick="openModalPlotting('${mhs.nim}')"
                                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 hover:from-black hover:to-slate-900 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                    <i class="bi bi-person-fill-gear text-orange-400"></i>
                                    <span>${hasP1 ? 'Ubah Pembimbing' : 'Plot Pembimbing 1'}</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            renderPaginationControlsPlotting(total, startIdx, endIdx);
            updateBatchActionBarPlotting();
        }

        // =========================================================================
        // PAGINATION CONTROLS
        // =========================================================================
        function changePerPagePlotting() {
            const select = document.getElementById('perPagePlotting');
            perPagePlotting = select.value === 'all' ? 999999 : parseInt(select.value);
            pagePlotting = 1;
            renderTablePlotting();
        }

        function goToPagePlotting(page) {
            if (page < 1) return;
            pagePlotting = page;
            renderTablePlotting();
        }

        function renderPaginationControlsPlotting(totalItems, startIdx, endIdx) {
            const info = document.getElementById('paginationInfoPlotting');
            const btns = document.getElementById('paginationButtonsPlotting');
            if (!info || !btns) return;

            if (totalItems === 0) {
                info.textContent = 'Tidak ada data';
                btns.innerHTML = '';
                return;
            }

            info.textContent = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} data`;
            if (perPagePlotting >= 999999) { btns.innerHTML = ''; return; }

            const totalPages = Math.ceil(totalItems / perPagePlotting);
            let html = '';
            html += `<button class="pagination-btn" onclick="goToPagePlotting(${pagePlotting - 1})" ${pagePlotting === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;

            const maxButtons = 5;
            let startPage = Math.max(1, pagePlotting - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) startPage = Math.max(1, endPage - maxButtons + 1);

            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPagePlotting(1)">1</button>`;
                if (startPage > 2) html += `<span class="pagination-ellipsis">…</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="pagination-btn ${i === pagePlotting ? 'active' : ''}" onclick="goToPagePlotting(${i})">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += `<span class="pagination-ellipsis">…</span>`;
                html += `<button class="pagination-btn" onclick="goToPagePlotting(${totalPages})">${totalPages}</button>`;
            }

            html += `<button class="pagination-btn" onclick="goToPagePlotting(${pagePlotting + 1})" ${pagePlotting === totalPages ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
            btns.innerHTML = html;
        }

        // =========================================================================
        // MULTIPLE SELECT & PLOTTING MASSAL LOGIC
        // =========================================================================
        window.selectedPlottingNims = new Set();
        let currentActiveNimsToPlot = [];

        function handleCheckboxChangePlotting() {
            const cbs = document.querySelectorAll('.cb-mhs-plotting');
            cbs.forEach(cb => {
                if (cb.checked) window.selectedPlottingNims.add(cb.value);
                else window.selectedPlottingNims.delete(cb.value);
            });
            updateBatchActionBarPlotting();
        }

        function toggleSelectAllPlotting(masterCb) {
            const cbs = document.querySelectorAll('.cb-mhs-plotting');
            cbs.forEach(cb => {
                cb.checked = masterCb.checked;
                if (cb.checked) window.selectedPlottingNims.add(cb.value);
                else window.selectedPlottingNims.delete(cb.value);
            });
            updateBatchActionBarPlotting();
        }

        function updateBatchActionBarPlotting() {
            const bar = document.getElementById('batchActionBarPlotting');
            const badge = document.getElementById('selectedCountBadgePlotting');
            const label = document.getElementById('btnBatchPlotLabel');
            if (!bar || !badge) return;

            const count = window.selectedPlottingNims.size;
            badge.textContent = count;

            if (count > 0) {
                bar.classList.remove('hidden');
                bar.classList.add('flex');
                if (label) label.textContent = 'Plot Pembimbing Massal (' + count + ' Mahasiswa)';
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
            }

            const master = document.getElementById('selectAllPlotting');
            const cbs = document.querySelectorAll('.cb-mhs-plotting');
            if (master && cbs.length > 0) {
                master.checked = Array.from(cbs).every(cb => cb.checked);
            }
        }

        function unselectAllStudentsPlotting() {
            window.selectedPlottingNims.clear();
            document.querySelectorAll('.cb-mhs-plotting').forEach(cb => cb.checked = false);
            const m = document.getElementById('selectAllPlotting');
            if (m) m.checked = false;
            updateBatchActionBarPlotting();
        }

        /**
     * Membuka Modal Penetapan Dosen Pembimbing 1 Secara Massal (Bulk Plotting)
     * 
     * Mengumpulkan seluruh mahasiswa yang sedang dicentang pada Set selectedNimsPlotting,
     * menampilkan ringkasan chips nama mahasiswa di modal, me-reset pencarian dosen,
     * serta menyiapkan form untuk menetapkan satu Dosen Pembimbing 1 untuk seluruh mahasiswa terpilih.
     */
    function openModalPlottingMassal() {
            if (window.selectedPlottingNims.size === 0) return;
            currentActiveNimsToPlot = Array.from(window.selectedPlottingNims);

            const allList = RAW_DATA.belum_diplot.concat(RAW_DATA.sudah_diplot);
            const selectedStudents = currentActiveNimsToPlot.map(n => allList.find(m => m.nim === n)).filter(Boolean);

            document.getElementById('plotModalNamaMhs').textContent = 'Plotting Massal (' + currentActiveNimsToPlot.length + ' Mahasiswa)';
            document.getElementById('plotModalNimMhs').textContent = currentActiveNimsToPlot.join(', ');
            document.getElementById('plotModalProdiMhs').textContent = 'Kerja Praktik';
            document.getElementById('plotModalPerusahaanMhs').textContent = selectedStudents.map(s => s.nama).slice(0, 5).join(', ') + (selectedStudents.length > 5 ? '... +' + (selectedStudents.length - 5) + ' lainnya' : '');
            document.getElementById('plotDosenSearchInput').value = '';

            renderDosenOptions(RAW_DATA.dosen_list, null);
            document.getElementById('modalPlottingOverlay').classList.add('active');
        }

        // =========================================================================
        // MULTI-PREVIEW BERKAS LOGIC (MATCHING DOSEN BIMBINGAN)
        // =========================================================================
        window.activeLihatBerkasNims = []; // ['1301210000', ...]
        window.activePreviews        = []; // [{ nim: '...', fileCode: '...' }]

        function previewItemKey(nim, fileCode) {
            return (nim + '_' + fileCode).replace(/[^a-zA-Z0-9_-]/g, '_');
        }

        function isPreviewItemActive(nim, fileCode) {
            return window.activePreviews.some(p => p.nim === nim && p.fileCode === fileCode);
        }

        function toggleLihatBerkasPanel(nim) {
            const existingIdx = window.activeLihatBerkasNims.indexOf(nim);
            if (existingIdx > -1) {
                removeStudentFromLihatBerkas(nim);
                return;
            }

            if (window.activeLihatBerkasNims.length >= 5) {
                const removedNim = window.activeLihatBerkasNims.shift();
                window.activePreviews = window.activePreviews.filter(p => p.nim !== removedNim);
            }

            window.activeLihatBerkasNims.push(nim);
            showLihatBerkasContainer();
            refreshLihatBerkasView();
        }

        function removeStudentFromLihatBerkas(nim) {
            const idx = window.activeLihatBerkasNims.indexOf(nim);
            if (idx > -1) window.activeLihatBerkasNims.splice(idx, 1);
            window.activePreviews = window.activePreviews.filter(p => p.nim !== nim);
            if (window.activeLihatBerkasNims.length === 0) {
                closeLihatBerkasPanel();
            } else {
                refreshLihatBerkasView();
            }
        }

        function showLihatBerkasContainer() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) {
                container.style.display = 'flex';
                container.classList.add('active');
            }
            updateLihatBerkasLayout();
        }

        function closeLihatBerkasPanel() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) {
                container.style.display = 'none';
                container.classList.remove('active');
            }
            const wrapperDaftar = document.getElementById('wrapperDaftarMhs');
            if (wrapperDaftar) wrapperDaftar.innerHTML = '';
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (wrapperPreview) {
                wrapperPreview.innerHTML = '';
                wrapperPreview.classList.remove('active');
                wrapperPreview.classList.add('hidden');
            }
            window.activeLihatBerkasNims = [];
            window.activePreviews = [];
            updateTableButtonHighlights();
        }

        function updateLihatBerkasLayout() {
            const container      = document.getElementById('lihatBerkasContainer');
            const wrapperDaftar  = document.getElementById('wrapperDaftarMhs');
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (!container || !wrapperDaftar || !wrapperPreview) return;

            const isMobile = window.innerWidth <= 768;
            const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;

            container.style.display = 'flex';
            if (isMobile) {
                container.style.flexDirection = 'column';
                container.style.alignItems = 'stretch';
                container.style.justifyContent = 'flex-start';
                container.style.overflowY = 'auto';
                container.style.overflowX = 'hidden';
                wrapperDaftar.className = 'flex flex-col gap-3 w-full overflow-y-auto flex-shrink-0';
            } else {
                container.style.flexDirection = 'row';
                container.style.alignItems = 'stretch';
                container.style.justifyContent = isPreviewActive ? 'flex-start' : 'center';
                container.style.overflowY = 'hidden';
                container.style.overflowX = 'hidden';
                wrapperDaftar.className = 'flex flex-col gap-3 w-[500px] min-w-[440px] max-w-[540px] overflow-y-auto flex-shrink-0';
            }

            if (isPreviewActive) {
                wrapperPreview.classList.add('active');
                wrapperPreview.classList.remove('hidden');
                if (isMobile) wrapperPreview.className = 'flex w-full overflow-x-auto overflow-y-visible gap-3 flex-row items-stretch flex-shrink-0';
                else          wrapperPreview.className = 'flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch';
            } else {
                wrapperPreview.classList.remove('active');
                wrapperPreview.classList.add('hidden');
            }
        }

        function previewBerkasItem(nim, fileCode) {
            const isExisting = window.activePreviews.findIndex(p => p.nim === nim && p.fileCode === fileCode);
            if (isExisting > -1) {
                window.activePreviews.splice(isExisting, 1);
                refreshLihatBerkasView();
                return;
            }

            const allList = RAW_DATA.belum_diplot.concat(RAW_DATA.sudah_diplot);
            const student = allList.find(m => m.nim === nim);
            const fileObj = student ? (student.files || []).find(f => f.kode_berkas === fileCode) : null;

            if (!fileObj || !fileObj.file_url) {
                Swal.fire({
                    icon: 'info',
                    title: 'Berkas Belum Diunggah',
                    text: 'Mahasiswa belum mengunggah berkas ini.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            if (window.activePreviews.length >= 5) {
                window.activePreviews.shift();
            }

            window.activePreviews.push({ nim, fileCode });
            refreshLihatBerkasView();

            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    const previewWrap = document.getElementById('wrapperPreviewBerkas');
                    if (previewWrap) previewWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 150);
            }
        }

        function closeSinglePreview(index) {
            if (index < 0 || index >= window.activePreviews.length) return;
            window.activePreviews.splice(index, 1);
            refreshLihatBerkasView();
        }

        function togglePreviewIframeInteraction(uniqueKey) {
            const iframe = document.getElementById('iframePreviewBerkas_' + uniqueKey);
            const btn    = document.getElementById('btnPreviewInteract_' + uniqueKey);
            const icon   = document.getElementById('iconPreviewInteract_' + uniqueKey);
            if (!iframe) return;

            const isLocked = iframe.classList.contains('pointer-events-none');
            if (isLocked) {
                iframe.classList.remove('pointer-events-none');
                if (btn) {
                    btn.className = 'w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center text-xs transition cursor-pointer shadow-2xs';
                    btn.title = 'Mode Scroll Aktif';
                }
                if (icon) icon.className = 'bi bi-hand';
            } else {
                iframe.classList.add('pointer-events-none');
                if (btn) {
                    btn.className = 'w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer';
                    btn.title = 'Kursor Normal (Terkunci)';
                }
                if (icon) icon.className = 'bi bi-arrow-pointer';
            }
        }

        function toggleFullscreenPreview(uniqueKey) {
            const card = document.getElementById('previewCard_' + uniqueKey);
            if (!card) return;
            const isFs = card.classList.contains('preview-fullscreen-mode');
            document.querySelectorAll('.preview-card-item.preview-fullscreen-mode').forEach(c => {
                c.classList.remove('preview-fullscreen-mode');
                const ic = c.querySelector('[data-fs-icon]');
                if (ic) ic.className = 'bi bi-arrows-fullscreen';
            });
            if (!isFs) {
                card.classList.add('preview-fullscreen-mode');
                const icon = card.querySelector('[data-fs-icon][data-fs-key="' + uniqueKey + '"]');
                if (icon) icon.className = 'bi bi-fullscreen-exit';
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
            let headerSummaryBar = '';
            if (totalActive > 1) {
                headerSummaryBar = `
                    <div class="bg-slate-900 text-white px-4 py-2.5 rounded-2xl flex items-center justify-between shadow-lg border border-slate-800 shrink-0 pointer-events-auto w-full animate-bar-in">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-people-fill text-orange-400 text-sm"></i>
                            <span class="text-xs font-bold">${totalActive} Mahasiswa <span class="text-slate-400 font-normal text-[11px]">(Maks. 5)</span></span>
                        </div>
                        <button type="button" onclick="closeLihatBerkasPanel()" class="text-xs text-slate-300 hover:text-rose-400 font-bold transition cursor-pointer">Tutup Semua</button>
                    </div>
                `;
            }

            const allList = RAW_DATA.belum_diplot.concat(RAW_DATA.sudah_diplot);
            let cardsHtml = '';
            window.activeLihatBerkasNims.forEach((nim, cardIdx) => {
                const mhs = allList.find(m => m.nim === nim);
                if (!mhs) return;

                const cardNum = cardIdx + 1;
                const files = mhs.files || [];
                const uploadedCount = files.filter(f => f.file && f.file.length > 0).length;
                const totalFiles = files.length || 4;
                const hasP1 = mhs.has_pembimbing1;

                let itemsHtml = `
                    <div class="mb-2 flex items-center justify-between gap-2 px-1">
                        <div class="flex items-center gap-1.5">
                            <i class="bi bi-folder2-open text-orange-500 text-sm"></i>
                            <span class="text-xs font-bold text-slate-700">Berkas Persyaratan (${uploadedCount}/${totalFiles} Terunggah)</span>
                        </div>
                        <span class="badge badge-success text-[10px] font-bold">
                            <i class="bi bi-patch-check-fill"></i> Disetujui LAA
                        </span>
                    </div>
                `;

                files.forEach(f => {
                    const isUp = f.file && f.file.length > 0;
                    const fileUrl = f.file_url || (isUp ? BASE_URL + f.file : '');
                    const fileName = f.file_basename || (isUp ? f.file : 'Belum diunggah');
                    const isAct = isPreviewItemActive(mhs.nim, f.kode_berkas);

                    const activeCardBorder = isAct ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' : 'border-slate-200 bg-white hover:border-slate-300';
                    const previewBtnStyle  = isAct ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';
                    const previewBtnLabel  = isAct ? 'Tutup' : 'Lihat';
                    const previewBtnIcon   = isAct ? 'bi-x-circle-fill' : 'bi-eye-fill';

                    itemsHtml += `
                        <div class="p-3 px-3.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex flex-col gap-2 ${activeCardBorder}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <i class="bi bi-file-earmark-pdf-fill text-orange-500 text-sm shrink-0"></i>
                                        <span class="font-bold text-slate-800 text-xs truncate">${f.nama_berkas}</span>
                                        <span class="badge badge-success text-[9px]"><i class="bi bi-check2"></i> Disetujui LAA</span>
                                    </div>
                                    <p class="text-[11px] font-mono text-slate-400 truncate mt-0.5" title="${fileName}">
                                        ${fileName}
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    ${isUp ? `
                                        <button type="button"
                                                onclick="event.stopPropagation(); previewBerkasItem('${mhs.nim}', '${f.kode_berkas}')"
                                                class="px-3 h-8 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap ${previewBtnStyle}">
                                            <i class="bi ${previewBtnIcon} text-xs"></i><span>${previewBtnLabel}</span>
                                        </button>
                                        <a href="${fileUrl}" download="${fileName}" target="_blank"
                                            class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition flex items-center justify-center cursor-pointer shadow-2xs active:scale-95"
                                            title="Unduh Berkas">
                                            <i class="bi bi-download text-xs"></i>
                                        </a>
                                    ` : `
                                        <span class="text-[11px] text-slate-400 italic px-2 py-1">Belum diunggah</span>
                                    `}
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Tombol Plotting di bawah card mahasiswa
                itemsHtml += `
                    <div class="pt-3 border-t border-slate-200 mt-2">
                        <button type="button" onclick="openModalPlotting('${mhs.nim}')"
                                class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="bi bi-person-fill-gear text-base"></i>
                            <span>${hasP1 ? 'Ubah Pembimbing 1' : 'Tetapkan / Plot Pembimbing 1'}</span>
                        </button>
                    </div>
                `;

                cardsHtml += `
                    <div class="student-card-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 w-full animate-pop-in" id="cardMhs_${mhs.nim}">
                        <div class="p-3 px-4 bg-slate-900 text-white flex items-center justify-between gap-3 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">${cardNum}</div>
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm font-bold text-white truncate max-w-[200px] sm:max-w-[240px]">${mhs.nama || 'Mahasiswa'}</h4>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="px-1.5 py-0.2 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${mhs.nim}</span>
                                        <span class="text-[10px] text-slate-400 truncate">${mhs.prodi || 'Informatika'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="removeStudentFromLihatBerkas('${mhs.nim}')" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Info Ringkas -->
                        <div class="p-3 bg-stone-50/80 border-b border-stone-200 text-[11px] text-slate-600 space-y-1">
                            <p class="truncate"><i class="bi bi-building mr-1.5 text-slate-400"></i><strong>${mhs.nama_perusahaan || '-'}</strong></p>
                            <p class="truncate"><i class="bi bi-geo-alt mr-1.5 text-slate-400"></i>${mhs.posisi_pekerjaan || mhs.kota || '-'}</p>
                            <p class="truncate"><i class="bi bi-person-badge mr-1.5 text-slate-400"></i>${hasP1 ? '<strong>Pembimbing 1:</strong> ' + mhs.pembimbing1_nama : '<span class="text-orange-600 font-bold">Menunggu Plotting Pembimbing</span>'}</p>
                        </div>

                        <div class="p-3 space-y-2 bg-slate-50/50 overflow-y-auto">${itemsHtml}</div>
                    </div>
                `;
            });

            wrapper.innerHTML = headerSummaryBar + cardsHtml;
        }

        function renderAllPreviewCards() {
            const wrapper = document.getElementById('wrapperPreviewBerkas');
            if (!wrapper) return;
            if (!window.activePreviews || window.activePreviews.length === 0) {
                wrapper.innerHTML = '';
                wrapper.classList.remove('active');
                wrapper.classList.add('hidden');
                return;
            }

            wrapper.classList.add('active');
            wrapper.classList.remove('hidden');

            const allList = RAW_DATA.belum_diplot.concat(RAW_DATA.sudah_diplot);
            let html = '';

            window.activePreviews.forEach((p, idx) => {
                const nim = p.nim;
                const fileCode = p.fileCode;
                const mhs = allList.find(m => m.nim === nim);
                if (!mhs) return;

                const fileObj = (mhs.files || []).find(f => f.kode_berkas === fileCode);
                if (!fileObj) return;

                const fileUrl = fileObj.file_url || (fileObj.file ? BASE_URL + fileObj.file : '');
                const fileName = fileObj.file_basename || (fileObj.file ? fileObj.file : '-');
                const fileLabel = fileObj.nama_berkas || 'Berkas';
                const fullName = mhs.nama || 'Mahasiswa';
                const slotNum = idx + 1;
                const uniqueKey = previewItemKey(nim, fileCode);

                html += `
                    <div class="preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 animate-preview-in" id="previewCard_${uniqueKey}">
                        <!-- Header Preview Card -->
                        <div class="preview-header p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="preview-slot-badge w-7 h-7 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                    ${window.activePreviews.length > 1 ? slotNum : '<i class="bi bi-file-earmark-pdf"></i>'}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[210px] sm:max-w-[280px]">${fileLabel}</h4>
                                    <p class="text-[10px] text-slate-300 font-medium truncate">${fullName} · <span class="font-mono text-slate-400">${nim}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="togglePreviewIframeInteraction('${uniqueKey}')" id="btnPreviewInteract_${uniqueKey}" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Kursor Terkunci (Normal)">
                                    <i class="bi bi-arrow-pointer" id="iconPreviewInteract_${uniqueKey}"></i>
                                </button>
                                <button type="button" onclick="toggleFullscreenPreview('${uniqueKey}')" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Perbesar Tampilan">
                                    <i class="bi bi-arrows-fullscreen" data-fs-icon data-fs-key="${uniqueKey}"></i>
                                </button>
                                <a href="${fileUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka di tab baru">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer ml-0.5" title="Tutup Pratinjau Ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Body Preview Card (Iframe) -->
                        <div class="preview-body relative flex-1 bg-slate-800">
                            <div class="loader absolute inset-0 flex items-center justify-center bg-slate-900 text-slate-400 text-xs font-bold gap-2">
                                <i class="bi bi-arrow-repeat text-xl text-orange-500 animate-spin"></i>
                                <span>Memuat Dokumen...</span>
                            </div>
                            <iframe id="iframePreviewBerkas_${uniqueKey}" src="${fileUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 relative z-10 pointer-events-none" onload="this.previousElementSibling.style.display='none'"></iframe>
                        </div>

                        <!-- Footer Preview Card -->
                        <div class="preview-footer p-2.5 px-4 bg-white border-t border-slate-200 flex items-center justify-between gap-3 shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-500">Status Dokumen:</span>
                                <span class="badge badge-success text-[10px]"><i class="bi bi-patch-check-fill"></i> Disetujui LAA</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="${fileUrl}" download="${fileName}" target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <i class="bi bi-download"></i>
                                    <span>Unduh</span>
                                </a>
                                <button type="button" onclick="openModalPlotting('${nim}')"
                                        class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                    <i class="bi bi-person-fill-gear"></i>
                                    <span>${mhs.has_pembimbing1 ? 'Ubah Pembimbing' : 'Plot Pembimbing 1'}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            wrapper.innerHTML = html;
        }

        function refreshLihatBerkasView() {
            updateLihatBerkasLayout();
            renderAllLihatBerkasCards();
            renderAllPreviewCards();
            updateTableButtonHighlights();
        }

        function updateTableButtonHighlights() {
            document.querySelectorAll('button[id^="btnLihatBerkas_"]').forEach(btn => {
                const nim = btn.id.replace('btnLihatBerkas_', '');
                const isActive = window.activeLihatBerkasNims.includes(nim);
                if (isActive) {
                    btn.classList.add('ring-2', 'ring-orange-400', 'bg-orange-600');
                } else {
                    btn.classList.remove('ring-2', 'ring-orange-400', 'bg-orange-600');
                }
            });
        }

        function handleContainerBackdropClick(e) {
            if (e.target.id === 'lihatBerkasContainer' || e.target.id === 'wrapperDaftarMhs' || e.target.id === 'wrapperPreviewBerkas') {
                closeLihatBerkasPanel();
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const fsCard = document.querySelector('.preview-card-item.preview-fullscreen-mode');
                if (fsCard) {
                    fsCard.classList.remove('preview-fullscreen-mode');
                    const icon = fsCard.querySelector('[data-fs-icon]');
                    if (icon) icon.className = 'bi bi-arrows-fullscreen';
                    e.preventDefault();
                    return;
                }
                const mp = document.getElementById('modalPlottingOverlay');
                if (mp && mp.classList.contains('active')) {
                    closeModalPlotting();
                    return;
                }
                if (window.activeLihatBerkasNims && window.activeLihatBerkasNims.length > 0) {
                    closeLihatBerkasPanel();
                }
            }
        });

        window.addEventListener('resize', () => {
            if (window.activeLihatBerkasNims && window.activeLihatBerkasNims.length > 0) updateLihatBerkasLayout();
        });

        // =========================================================================
        // MODAL PLOTTING LOGIC
        // =========================================================================
        /**
     * Membuka Modal Penetapan Dosen Pembimbing 1 untuk Mahasiswa Tunggal
     * 
     * Menampilkan informasi nama & NIM mahasiswa, menyorot dosen yang sedang aktif membimbing
     * (jika sedang dalam mode ubah pembimbing), dan menampilkan modal pilihan dosen.
     * 
     * @param {string} nim NIM mahasiswa target
     */
    function openModalPlotting(nim) {
            const allList = RAW_DATA.belum_diplot.concat(RAW_DATA.sudah_diplot);
            const mhs = allList.find(m => m.nim === nim);
            if (!mhs) return;

            currentActiveNimsToPlot = [nim];

            document.getElementById('plotModalNamaMhs').textContent = mhs.nama || 'Mahasiswa';
            document.getElementById('plotModalNimMhs').textContent = mhs.nim;
            document.getElementById('plotModalProdiMhs').textContent = mhs.prodi || 'Informatika';
            document.getElementById('plotModalPerusahaanMhs').textContent = mhs.nama_perusahaan || '-';
            document.getElementById('plotDosenSearchInput').value = '';

            renderDosenOptions(RAW_DATA.dosen_list, mhs.dosen_pembimbing1);

            document.getElementById('modalPlottingOverlay').classList.add('active');
        }

        function closeModalPlotting() {
            document.getElementById('modalPlottingOverlay').classList.remove('active');
            currentActiveNimToPlot = null;
        }

        function renderDosenOptions(list, selectedIdentifier) {
            const select = document.getElementById('plotDosenSelect');
            if (!select) return;

            if (!list || list.length === 0) {
                select.innerHTML = '<option value="" disabled class="p-2 text-slate-400">Tidak ada data dosen yang tersedia.</option>';
                return;
            }

            select.innerHTML = list.map(d => {
                const isSelected = (d.nip === selectedIdentifier || d.id === selectedIdentifier);
                const nipText = d.nip ? `[${d.nip}] ` : (d.kode_dosen ? `[${d.kode_dosen}] ` : '');
                return `
                    <option value="${d.nip || d.id}" ${isSelected ? 'selected' : ''} class="p-2.5 rounded-lg border-b border-slate-100 hover:bg-orange-50 font-medium">
                        ${nipText}${d.nama_dosen} (${d.prodi || 'Informatika'})
                    </option>
                `;
            }).join('');
        }

        function filterDosenOptions(keyword) {
            const q = (keyword || '').trim().toLowerCase();
            const filtered = RAW_DATA.dosen_list.filter(d => {
                return (d.nama_dosen || '').toLowerCase().includes(q) ||
                       (d.nip || '').toLowerCase().includes(q) ||
                       (d.kode_dosen || '').toLowerCase().includes(q);
            });
            renderDosenOptions(filtered, null);
        }

        /**
     * Validasi & Eksekusi Simpan Plotting Dosen Pembimbing 1
     * 
     * Memvalidasi bahwa Koordinator KP telah memilih salah satu dosen dari daftar radio card dosen,
     * lalu memanggil doAjaxSubmitPlotting() untuk mengirimkan data ke server secara asinkron.
     */
    function submitPlotting() {
            if (!currentActiveNimToPlot) return;

            const select = document.getElementById('plotDosenSelect');
            const dosenIdentifier = select.value;

            if (!dosenIdentifier) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Dosen',
                    text: 'Silakan pilih Dosen Pembimbing 1 dari daftar terlebih dahulu.'
                });
                return;
            }

            const selectedOptionText = select.options[select.selectedIndex]?.text || 'Dosen Terpilih';

            Swal.fire({
                title: 'Konfirmasi Plotting',
                html: `Tetapkan <strong>${selectedOptionText}</strong> sebagai Dosen Pembimbing 1 untuk mahasiswa NIM <strong>${currentActiveNimToPlot}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tetapkan Pembimbing',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    doAjaxSubmitPlotting(currentActiveNimToPlot, dosenIdentifier);
                }
            });
        }

        function doAjaxSubmitPlotting(nim, dosenIdentifier) {
            Swal.fire({ title: 'Menyimpan Plotting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('dosen_identifier', dosenIdentifier);

            fetch(SITE_URL + 'koordinatorkp/ajax_plot_pembimbing', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Plotting Berhasil!',
                        text: res.message || 'Dosen Pembimbing 1 berhasil ditetapkan.',
                        confirmButtonColor: '#ea580c'
                    });
                    closeModalPlotting();
                    reloadPlottingData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Plotting',
                        text: res.message || 'Terjadi kesalahan sistem.'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Koneksi',
                    text: 'Gagal menghubungi server: ' + err.message
                });
            });
        }

        /**
     * Sinkronisasi Realtime Data Plotting KP (Tanpa Reload Halaman Penuh)
     * 
     * Mengambil payload JSON terbaru dari /koordinatorkp/ajax_get_plotting_data,
     * memperbarui dataset lokal JavaScript (currentDataPlotting), dan me-render ulang tabel secara instan.
     */
    function reloadPlottingData() {
            fetch(SITE_URL + 'koordinatorkp/ajax_get_plotting_data')
                .then(r => r.json())
                .then(res => {
                    if (res.status) {
                        RAW_DATA.belum_diplot = res.belum_diplot || [];
                        RAW_DATA.sudah_diplot = res.sudah_diplot || [];

                        document.getElementById('badgeCountBelumDiplot').textContent = RAW_DATA.belum_diplot.length;
                        document.getElementById('badgeCountSudahDiplot').textContent = RAW_DATA.sudah_diplot.length;
                        document.getElementById('badgeTotalSiap').textContent = RAW_DATA.belum_diplot.length + RAW_DATA.sudah_diplot.length;

                        renderTablePlotting();
                    }
                })
                .catch(err => console.error('Gagal memuat ulang data plotting:', err));
        }

        // =========================================================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function() {
            renderFilterRowsPlotting();
            renderTablePlotting();
        });
    </script>
</body>
</html>
