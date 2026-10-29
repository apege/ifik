<?php
/**
 * Halaman Verifikasi Berkas KP - Admin Layanan (LAA)
 * Path: application/views/admin_layanan/verifikasi_kp.php
 *
 * Tab 1: Melihat & Meng-ACC Berkas Persyaratan KP (/ifik/mahasiswa/pengajuan_kp)
 *         - Dipisahkan antara: Belum Disetujui Semua & Sudah Disetujui Semua
 * Tab 2: Melihat & Meng-ACC File Laporan KP (/ifik/mahasiswa/laporan_kp)
 *
 * Visual & Fitur: Diselaraskan dengan dosen_bimbingan (Multi-search bar, pagination, rotating border table, & split-view preview modal).
 */

$active_tab = $active_tab ?? 'persyaratan';
$sub_tab    = $sub_tab ?? 'belum_acc';

$total_belum_acc = count($belum_acc ?? []);
$total_sudah_acc = count($sudah_acc ?? []);
$total_laporan   = count($laporan ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi Berkas Kerja Praktik (KP) - Layanan Akademik'; ?></title>
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

        /* Animation */
        @keyframes popInCard { 0% { opacity: 0; transform: scale(0.92) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes fadeInSlideRight { 0% { opacity: 0; transform: scale(0.96) translateX(40px); } 100% { opacity: 1; transform: scale(1) translateX(0); } }
        @keyframes fadeInDownSmooth { 0% { opacity: 0; transform: translateY(-12px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-pop-in { animation: popInCard 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-preview-in { animation: fadeInSlideRight 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }

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

        <!-- HERO HEADER (Mirip Dosen Bimbingan & Pendaftaran TA) -->
        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold">
                        <i class="bi bi-shield-check text-amber-200"></i> Portal Verifikasi Layanan Akademik (LAA)
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Verifikasi Berkas Kerja Praktik (KP)
                    </h1>
                    <p class="text-xs sm:text-sm text-orange-100 font-normal leading-relaxed">
                        Kelola evaluasi, verifikasi keabsahan, dan setujui (ACC) berkas persyaratan KP serta laporan akhir kerja praktik mahasiswa secara akurat dan transparan.
                    </p>
                </div>

                <!-- Navigasi 2 Tab Utama -->
                <div class="w-full xl:w-auto bg-black/25 backdrop-blur-xl rounded-3xl p-3 border border-white/20 shadow-2xl shrink-0">
                    <div class="flex items-center gap-2">
                        <!-- Tab 1 Button -->
                        <button type="button" onclick="switchMainTab('persyaratan')" id="btnTabPersyaratan"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($active_tab === 'persyaratan') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-file-earmark-check-fill text-base"></i>
                            <div class="text-left">
                                <span class="block">Tab 1: Persyaratan KP</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Pengajuan Berkas (Step 2)</span>
                            </div>
                            <span class="ml-1 px-2 py-0.5 rounded-full bg-black/30 text-amber-200 text-xs font-black">
                                <?= $total_belum_acc + $total_sudah_acc ?>
                            </span>
                        </button>

                        <!-- Tab 2 Button -->
                        <button type="button" onclick="switchMainTab('laporan')" id="btnTabLaporan"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($active_tab === 'laporan') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-journal-richtext text-base"></i>
                            <div class="text-left">
                                <span class="block">Tab 2: Laporan KP</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Laporan &amp; Nilai Lapangan</span>
                            </div>
                            <span class="ml-1 px-2 py-0.5 rounded-full bg-black/30 text-amber-200 text-xs font-black">
                                <?= $total_laporan ?>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             KONTEN TAB 1: PERSYARATAN KP (Pemisahan Belum ACC vs Sudah ACC)
             ========================================================================= -->
        <div id="contentTabPersyaratan" class="<?= ($active_tab === 'persyaratan') ? '' : 'hidden' ?> space-y-6">

            <!-- Sub-tab Switcher: Pemisahan Tabel Mahasiswa Belum di-ACC vs Sudah di-ACC Semua -->
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-folder-check text-orange-500 text-xl"></i>
                        Verifikasi Persyaratan Pengajuan KP
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Data berkas yang diunggah mahasiswa pada menu <code>/ifik/mahasiswa/pengajuan_kp</code>.
                    </p>
                </div>

                <!-- Sub-tab Pill -->
                <div class="inline-flex p-1.5 rounded-2xl bg-slate-100 border border-slate-200 gap-1.5">
                    <button type="button" onclick="switchSubTabPersyaratan('belum_acc')" id="btnSubBelumAcc"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer <?= ($sub_tab === 'belum_acc') ? 'bg-orange-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'; ?>">
                        <i class="bi bi-hourglass-split"></i>
                        <span>Belum Disetujui Semua</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($sub_tab === 'belum_acc') ? 'bg-white text-orange-600' : 'bg-slate-200 text-slate-700'; ?>" id="badgeCountBelumAcc">
                            <?= $total_belum_acc ?>
                        </span>
                    </button>
                    <button type="button" onclick="switchSubTabPersyaratan('sudah_acc')" id="btnSubSudahAcc"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer <?= ($sub_tab === 'sudah_acc') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'; ?>">
                        <i class="bi bi-check-all"></i>
                        <span>Sudah Disetujui Semua (Lengkap)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($sub_tab === 'sudah_acc') ? 'bg-white text-emerald-600' : 'bg-slate-200 text-slate-700'; ?>" id="badgeCountSudahAcc">
                            <?= $total_sudah_acc ?>
                        </span>
                    </button>
                </div>
            </div>

            <!-- CONTAINER CARD TABEL PERSYARATAN -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-orange-500/5 space-y-6 border border-slate-200">

                <!-- Multi-kriteria Search Bar (Identik Dosen Bimbingan) -->
                <div id="multiFilterPanelPersyaratan" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnAddFilterPersyaratan" onclick="addFilterRow('persyaratan')"
                                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                                <i class="bi bi-plus-lg"></i> Tambah Filter
                                <span id="filterCountBadgePersyaratan" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                            </button>
                            <button type="button" onclick="resetFilters('persyaratan')"
                                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div id="filterRowsPersyaratan" class="space-y-2 mb-3"></div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <p class="text-[10px] text-slate-500 font-medium italic">
                            <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk memfilter mahasiswa.
                        </p>
                        <button type="button" onclick="applyFilters('persyaratan')"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-search"></i> Cari Data
                        </button>
                    </div>
                </div>

                <!-- Kontrol Pagination -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                        <select id="perPagePersyaratan" onchange="changePerPage('persyaratan')" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer outline-none">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[11px] text-slate-500 font-medium" id="paginationInfoPersyaratan"></span>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap" id="paginationButtonsPersyaratan"></div>
                </div>

                <!-- Tabel Rotating Border -->
                <div class="table-rotating-border-wrap">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-3 text-center w-10">
                                        <input type="checkbox" id="selectAllPersyaratan" onchange="toggleSelectAllPersyaratan(this)" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer" title="Pilih Semua">
                                    </th>
                                    <th class="py-4 px-3 text-center w-12">No</th>
                                    <th class="py-4 px-4">Mahasiswa &amp; Program Studi</th>
                                    <th class="py-4 px-4">Perusahaan / Instansi KP</th>
                                    <th class="py-4 px-4 text-center">Kelengkapan Berkas</th>
                                    <th class="py-4 px-4 text-center">Status LAA</th>
                                    <th class="py-4 px-4 text-center">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodyPersyaratan">
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-slate-400">
                                        <i class="bi bi-arrow-repeat animate-spin text-lg mr-2"></i> Memuat data pengajuan KP...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- =========================================================================
             KONTEN TAB 2: LAPORAN & PENILAIAN KP
             ========================================================================= -->
        <div id="contentTabLaporan" class="<?= ($active_tab === 'laporan') ? '' : 'hidden' ?> space-y-6">

            <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-journal-text text-orange-500 text-xl"></i>
                        Verifikasi File Laporan KP &amp; Form Penilaian
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Data laporan akhir dan lembar penilaian pembimbing lapangan yang diunggah mahasiswa pada menu <code>/ifik/mahasiswa/laporan_kp</code>.
                    </p>
                </div>
                <div class="text-xs font-bold text-slate-600 px-3.5 py-1.5 rounded-full bg-orange-50 border border-orange-200">
                    Total: <span id="badgeTotalLaporan"><?= $total_laporan ?></span> Laporan Mahasiswa
                </div>
            </div>

            <!-- CONTAINER CARD TABEL LAPORAN -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-orange-500/5 space-y-6 border border-slate-200">

                <!-- Multi-kriteria Search Bar Laporan -->
                <div id="multiFilterPanelLaporan" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnAddFilterLaporan" onclick="addFilterRow('laporan')"
                                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                                <i class="bi bi-plus-lg"></i> Tambah Filter
                                <span id="filterCountBadgeLaporan" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                            </button>
                            <button type="button" onclick="resetFilters('laporan')"
                                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div id="filterRowsLaporan" class="space-y-2 mb-3"></div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <p class="text-[10px] text-slate-500 font-medium italic">
                            <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk memfilter laporan.
                        </p>
                        <button type="button" onclick="applyFilters('laporan')"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-search"></i> Cari Data
                        </button>
                    </div>
                </div>

                <!-- Kontrol Pagination Laporan -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                        <select id="perPageLaporan" onchange="changePerPage('laporan')" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer outline-none">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[11px] text-slate-500 font-medium" id="paginationInfoLaporan"></span>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap" id="paginationButtonsLaporan"></div>
                </div>

                <!-- Tabel Rotating Border Laporan -->
                <div class="table-rotating-border-wrap">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-3 text-center w-10">
                                        <input type="checkbox" id="selectAllLaporan" onchange="toggleSelectAllLaporan(this)" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer" title="Pilih Semua">
                                    </th>
                                    <th class="py-4 px-3 text-center w-12">No</th>
                                    <th class="py-4 px-4">Mahasiswa &amp; Program Studi</th>
                                    <th class="py-4 px-4">Instansi KP &amp; Pembimbing Lapangan</th>
                                    <th class="py-4 px-4">Dosen Pembimbing 1</th>
                                    <th class="py-4 px-4 text-center">File Laporan</th>
                                    <th class="py-4 px-4 text-center">Nilai Lapangan</th>
                                    <th class="py-4 px-4 text-center">Status LAA</th>
                                    <th class="py-4 px-4 text-center">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodyLaporan">
                                <tr>
                                    <td colspan="9" class="text-center py-10 text-slate-400">
                                        <i class="bi bi-arrow-repeat animate-spin text-lg mr-2"></i> Memuat data laporan KP...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    
        <!-- FLOATING BATCH ACTION BAR (ACC MASSAL) -->
        <div id="batchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300 animate-pop-in">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="selectedCountBadge">0</span>
                <span class="text-xs font-bold tracking-tight">Mahasiswa Terpilih</span>
            </div>
            <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="submitBatchAcc()" id="btnBatchAcc" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                    <i class="bi bi-check2-all text-base"></i> <span id="btnBatchAccLabel">ACC Massal Terpilih</span>
                </button>
                <button type="button" onclick="unselectAllStudents()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                    <i class="bi bi-x-lg"></i> Batal
                </button>
            </div>
        </div>
    </main>
    </div>

    <!-- =========================================================================
         MODAL PREVIEW & VERIFIKASI BERKAS SPLIT-VIEW (IDENTIK DOSEN BIMBINGAN)
         ========================================================================= -->
    <!-- =========================================================================
         MULTI-PREVIEW BERKAS CONTAINER (MATCHING DOSEN BIMBINGAN)
         ========================================================================= -->
    <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-50 flex items-stretch p-3 sm:p-5 gap-4 overflow-hidden" style="display: none;" onclick="handleContainerBackdropClick(event)">
        <div id="wrapperDaftarMhs" class="flex flex-col gap-3 overflow-y-auto flex-shrink-0"></div>
        <div id="wrapperPreviewBerkas" class="flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch hidden"></div>
    </div>

    <!-- DATASET DARI SERVER UNTUK JAVASCRIPT TABLE & FILTER -->
    <script>
        const RAW_DATA = {
            belum_acc: <?= json_encode($belum_acc ?? []); ?>,
            sudah_acc: <?= json_encode($sudah_acc ?? []); ?>,
            laporan:   <?= json_encode($laporan ?? []); ?>,
            syarat_kp: <?= json_encode($syarat_list ?? []); ?>
        };

        const BASE_URL = '<?= rtrim(base_url(), '/') . '/'; ?>';
        const SITE_URL = '<?= rtrim(site_url(), '/') . '/'; ?>';

        let currentMainTab = '<?= $active_tab ?>';
        let currentSubTabPersyaratan = '<?= $sub_tab ?>';

        // Filter State
        const COLUMNS_PERSYARATAN = [
            { value: 'all', label: 'Semua Kolom' },
            { value: 'nim', label: 'NIM Mahasiswa' },
            { value: 'nama', label: 'Nama Mahasiswa' },
            { value: 'prodi', label: 'Program Studi' },
            { value: 'perusahaan', label: 'Nama Perusahaan / Tempat KP' },
            { value: 'status', label: 'Status Pengajuan' }
        ];

        const COLUMNS_LAPORAN = [
            { value: 'all', label: 'Semua Kolom' },
            { value: 'nim', label: 'NIM Mahasiswa' },
            { value: 'nama', label: 'Nama Mahasiswa' },
            { value: 'prodi', label: 'Program Studi' },
            { value: 'perusahaan', label: 'Instansi / Perusahaan' },
            { value: 'pembimbing', label: 'Dosen Pembimbing 1' },
            { value: 'status', label: 'Status Verifikasi' }
        ];

        let filtersPersyaratan = [{ col: 'all', val: '' }];
        let appliedFiltersPersyaratan = [{ col: 'all', val: '' }];
        let pagePersyaratan = 1;
        let perPagePersyaratan = 25;

        let filtersLaporan = [{ col: 'all', val: '' }];
        let appliedFiltersLaporan = [{ col: 'all', val: '' }];
        let pageLaporan = 1;
        let perPageLaporan = 25;

        // Modal Active Item State
        let activeModalData = {
            mode: 'persyaratan', // 'persyaratan' or 'laporan'
            mhs: null,
            activeFileIndex: 0
        };

        // =========================================================================
        // TAB SWITCHING
        // =========================================================================
        function switchMainTab(tab) {
            currentMainTab = tab;
            const btnP = document.getElementById('btnTabPersyaratan');
            const btnL = document.getElementById('btnTabLaporan');
            const cntP = document.getElementById('contentTabPersyaratan');
            const cntL = document.getElementById('contentTabLaporan');

            if (tab === 'persyaratan') {
                btnP.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnL.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                cntP.classList.remove('hidden');
                cntL.classList.add('hidden');
                renderTablePersyaratan();
            } else {
                btnL.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnP.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                cntL.classList.remove('hidden');
                cntP.classList.add('hidden');
                renderTableLaporan();
            }

            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }

        function switchSubTabPersyaratan(sub) {
            currentSubTabPersyaratan = sub;
            const btnBelum = document.getElementById('btnSubBelumAcc');
            const btnSudah = document.getElementById('btnSubSudahAcc');
            const badgeBelum = document.getElementById('badgeCountBelumAcc');
            const badgeSudah = document.getElementById('badgeCountSudahAcc');

            if (sub === 'belum_acc') {
                btnBelum.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer bg-orange-600 text-white shadow-md';
                btnSudah.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-white/60';
                badgeBelum.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-white text-orange-600';
                badgeSudah.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-700';
            } else {
                btnSudah.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer bg-emerald-600 text-white shadow-md';
                btnBelum.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-white/60';
                badgeSudah.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-white text-emerald-600';
                badgeBelum.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-700';
            }

            pagePersyaratan = 1;
            renderTablePersyaratan();

            const url = new URL(window.location);
            url.searchParams.set('subtab', sub);
            window.history.replaceState({}, '', url);
        }

        // =========================================================================
        // MULTI-FILTER ROWS RENDERING (PERSYARATAN & LAPORAN)
        // =========================================================================
        function renderFilterRows(target) {
            const isPersyaratan = (target === 'persyaratan');
            const container = document.getElementById(isPersyaratan ? 'filterRowsPersyaratan' : 'filterRowsLaporan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            const columns = isPersyaratan ? COLUMNS_PERSYARATAN : COLUMNS_LAPORAN;

            if (!container) return;

            container.innerHTML = filters.map((f, i) => `
                <div class="filter-row-dw">
                    <span class="text-[10px] font-bold text-slate-500 w-16 shrink-0">Kriteria #${i + 1}:</span>
                    <select class="filter-col-dw" data-target="${target}" data-idx="${i}">
                        ${columns.map(c => `<option value="${c.value}" ${c.value === f.col ? 'selected' : ''}>${c.label}</option>`).join('')}
                    </select>
                    <input type="text" class="filter-val-dw" data-target="${target}" data-idx="${i}"
                           value="${(f.val || '').replace(/"/g, '&quot;')}"
                           placeholder="Ketik kata kunci pencarian..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault();applyFilters('${target}');}">
                    ${i > 0 ? `<button type="button" class="btn-remove-filter-dw" onclick="removeFilterRow('${target}', ${i})" title="Hapus filter"><i class="bi bi-x-lg"></i></button>` : ''}
                </div>
            `).join('');

            updateFilterBadge(target);
        }

        function updateFilterBadge(target) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            const badge = document.getElementById(isPersyaratan ? 'filterCountBadgePersyaratan' : 'filterCountBadgeLaporan');
            const btnAdd = document.getElementById(isPersyaratan ? 'btnAddFilterPersyaratan' : 'btnAddFilterLaporan');

            if (badge) badge.textContent = `${filters.length}/4`;
            if (btnAdd) {
                btnAdd.style.opacity = filters.length >= 4 ? '0.5' : '1';
                btnAdd.style.pointerEvents = filters.length >= 4 ? 'none' : 'auto';
            }
        }

        function addFilterRow(target) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            if (filters.length >= 4) return;
            filters.push({ col: 'all', val: '' });
            renderFilterRows(target);
        }

        function removeFilterRow(target, idx) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            if (filters.length <= 1) return;
            filters.splice(idx, 1);
            renderFilterRows(target);
        }

        function applyFilters(target) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;

            document.querySelectorAll(`.filter-col-dw[data-target="${target}"]`).forEach(sel => {
                const idx = parseInt(sel.dataset.idx);
                if (filters[idx]) filters[idx].col = sel.value;
            });
            document.querySelectorAll(`.filter-val-dw[data-target="${target}"]`).forEach(inp => {
                const idx = parseInt(inp.dataset.idx);
                if (filters[idx]) filters[idx].val = inp.value;
            });

            if (isPersyaratan) {
                appliedFiltersPersyaratan = filters.map(f => ({ ...f }));
                pagePersyaratan = 1;
                renderTablePersyaratan();
            } else {
                appliedFiltersLaporan = filters.map(f => ({ ...f }));
                pageLaporan = 1;
                renderTableLaporan();
            }
        }

        function resetFilters(target) {
            if (target === 'persyaratan') {
                filtersPersyaratan = [{ col: 'all', val: '' }];
                appliedFiltersPersyaratan = [{ col: 'all', val: '' }];
                pagePersyaratan = 1;
                renderFilterRows('persyaratan');
                renderTablePersyaratan();
            } else {
                filtersLaporan = [{ col: 'all', val: '' }];
                appliedFiltersLaporan = [{ col: 'all', val: '' }];
                pageLaporan = 1;
                renderFilterRows('laporan');
                renderTableLaporan();
            }
        }

        // =========================================================================
        // FILTERING LOGIC
        // =========================================================================
        function matchMultiFilters(item, filters, target) {
            return filters.every(f => {
                const query = (f.val || '').trim().toLowerCase();
                if (!query) return true;
                const col = f.col;

                if (target === 'persyaratan') {
                    if (col === 'nim') return (item.nim || '').toLowerCase().includes(query);
                    if (col === 'nama') return (item.nama || '').toLowerCase().includes(query);
                    if (col === 'prodi') return (item.prodi || '').toLowerCase().includes(query);
                    if (col === 'perusahaan') return (item.nama_perusahaan || '').toLowerCase().includes(query);
                    if (col === 'status') return (item.status_file || '').toLowerCase().includes(query);
                    // 'all'
                    return (
                        (item.nim || '').toLowerCase().includes(query) ||
                        (item.nama || '').toLowerCase().includes(query) ||
                        (item.prodi || '').toLowerCase().includes(query) ||
                        (item.nama_perusahaan || '').toLowerCase().includes(query) ||
                        (item.status_file || '').toLowerCase().includes(query)
                    );
                } else {
                    if (col === 'nim') return (item.nim || '').toLowerCase().includes(query);
                    if (col === 'nama') return (item.nama || '').toLowerCase().includes(query);
                    if (col === 'prodi') return (item.prodi || '').toLowerCase().includes(query);
                    if (col === 'perusahaan') return (item.nama_perusahaan || '').toLowerCase().includes(query);
                    if (col === 'pembimbing') return (item.pembimbing1_nama || '').toLowerCase().includes(query);
                    if (col === 'status') return (item.status_adminlaa || '').toLowerCase().includes(query);
                    // 'all'
                    return (
                        (item.nim || '').toLowerCase().includes(query) ||
                        (item.nama || '').toLowerCase().includes(query) ||
                        (item.prodi || '').toLowerCase().includes(query) ||
                        (item.nama_perusahaan || '').toLowerCase().includes(query) ||
                        (item.pembimbing1_nama || '').toLowerCase().includes(query) ||
                        (item.status_adminlaa || '').toLowerCase().includes(query)
                    );
                }
            });
        }

        // =========================================================================
        // RENDERING TABEL TAB 1: PERSYARATAN KP
        // =========================================================================
        function renderTablePersyaratan() {
            const rawList = (currentSubTabPersyaratan === 'belum_acc') ? RAW_DATA.belum_acc : RAW_DATA.sudah_acc;
            const filtered = rawList.filter(item => matchMultiFilters(item, appliedFiltersPersyaratan, 'persyaratan'));
            const total = filtered.length;

            const tbody = document.getElementById('tbodyPersyaratan');
            if (!tbody) return;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400">
                            <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-sm">Tidak ada data mahasiswa ${currentSubTabPersyaratan === 'belum_acc' ? 'yang belum di-ACC' : 'yang sudah di-ACC'}.</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian pada filter multi-kriteria.</p>
                        </td>
                    </tr>
                `;
                renderPaginationControls('persyaratan', 0, 0, 0);
                return;
            }

            const effectivePerPage = perPagePersyaratan >= 999999 ? total : perPagePersyaratan;
            const totalPages = Math.ceil(total / effectivePerPage);
            if (pagePersyaratan > totalPages) pagePersyaratan = totalPages;

            const startIdx = (pagePersyaratan - 1) * effectivePerPage;
            const endIdx = Math.min(startIdx + effectivePerPage, total);
            const pagedItems = filtered.slice(startIdx, endIdx);

            let rowsHtml = '';
            pagedItems.forEach((mhs, idx) => {
                const rowNo = startIdx + idx + 1;
                const totalReq = mhs.total_wajib || 0;
                const appCount = mhs.approved_wajib || 0;
                const upCount  = mhs.uploaded_wajib || 0;
                const rejCount = mhs.rejected_count || 0;

                let progressBadge = '';
                if (mhs.is_all_approved) {
                    progressBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Lengkap (${appCount}/${totalReq})</span>`;
                } else if (rejCount > 0) {
                    progressBadge = `<span class="badge badge-danger"><i class="bi bi-exclamation-octagon-fill"></i> ${rejCount} Revisi (${appCount}/${totalReq})</span>`;
                } else if (upCount === 0) {
                    progressBadge = `<span class="badge badge-secondary"><i class="bi bi-dash-circle"></i> Belum Upload</span>`;
                } else {
                    progressBadge = `<span class="badge badge-warning"><i class="bi bi-hourglass-split"></i> Pending (${appCount}/${totalReq} ACC)</span>`;
                }

                let statusFileBadge = '';
                const st = mhs.status_file || 'Sudah Upload Persyaratan';
                if (st === 'Disetujui LAA' || st === 'Selesai' || st === 'Sudah Plot Pembimbing') {
                    statusFileBadge = `<span class="badge badge-success">${st}</span>`;
                } else if (st === 'Revisi Persyaratan' || st === 'Revisi Laporan') {
                    statusFileBadge = `<span class="badge badge-danger">${st}</span>`;
                } else {
                    statusFileBadge = `<span class="badge badge-primary">${st}</span>`;
                }

                const isChecked = window.selectedPersyaratanNims.has(mhs.nim);
                rowsHtml += `
                    <tr class="hover:bg-orange-50/40 transition ${isChecked ? 'bg-orange-50/70' : ''}">
                        <td class="py-4 px-3 text-center">
                            <input type="checkbox" class="cb-mhs-persyaratan w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer"
                                   value="${mhs.nim}" ${isChecked ? 'checked' : ''} onchange="handleCheckboxChangePersyaratan()">
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
                        <td class="py-4 px-4 text-center">${progressBadge}</td>
                        <td class="py-4 px-4 text-center">${statusFileBadge}</td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <button type="button" onclick="toggleLihatBerkasPanel('${mhs.nim}', 'persyaratan')" id="btnLihatBerkas_${mhs.nim}"
                                        class="inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform btn-3d-orange">
                                    <i class="bi bi-folder-open text-xs"></i>
                                    <span>Lihat Berkas</span>
                                    <span class="bg-white/30 text-white px-1.5 py-0.5 rounded-full text-[10px] font-black">${(mhs.files || []).filter(f => f.file && f.file.length > 0).length}/${(mhs.files || []).length || 4}</span>
                                </button>
                                ${!mhs.is_all_approved ? `
                                    <button type="button" onclick="confirmBulkAccPersyaratan('${mhs.nim}', '${(mhs.nama || '').replace(/'/g, "\\'")}')"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs"
                                            title="ACC Semua Berkas Mahasiswa Ini">
                                        <i class="bi bi-check-all"></i> ACC Semua
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            renderPaginationControls('persyaratan', total, startIdx, endIdx);
            updateBatchActionBar();
        }

        // =========================================================================
        // RENDERING TABEL TAB 2: LAPORAN KP
        // =========================================================================
        function renderTableLaporan() {
            const rawList = RAW_DATA.laporan;
            const filtered = rawList.filter(item => matchMultiFilters(item, appliedFiltersLaporan, 'laporan'));
            const total = filtered.length;

            const tbody = document.getElementById('tbodyLaporan');
            if (!tbody) return;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-12 text-slate-400">
                            <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-sm">Tidak ada data laporan KP mahasiswa yang cocok.</p>
                            <p class="text-xs text-slate-400 mt-1">Laporan akan muncul setelah mahasiswa mengunggah berkas di menu Laporan KP.</p>
                        </td>
                    </tr>
                `;
                renderPaginationControls('laporan', 0, 0, 0);
                return;
            }

            const effectivePerPage = perPageLaporan >= 999999 ? total : perPageLaporan;
            const totalPages = Math.ceil(total / effectivePerPage);
            if (pageLaporan > totalPages) pageLaporan = totalPages;

            const startIdx = (pageLaporan - 1) * effectivePerPage;
            const endIdx = Math.min(startIdx + effectivePerPage, total);
            const pagedItems = filtered.slice(startIdx, endIdx);

            let rowsHtml = '';
            pagedItems.forEach((mhs, idx) => {
                const rowNo = startIdx + idx + 1;
                const hasFile = mhs.has_uploaded && mhs.file_laporan;
                const statusLaa = mhs.status_adminlaa || 'Pending';

                let statusBadge = '';
                if (statusLaa === 'Approved') {
                    statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Disetujui (Selesai)</span>`;
                } else if (statusLaa === 'Rejected') {
                    statusBadge = `<span class="badge badge-danger"><i class="bi bi-exclamation-triangle-fill"></i> Revisi Laporan</span>`;
                } else {
                    statusBadge = `<span class="badge badge-warning"><i class="bi bi-hourglass-split"></i> Pending Review</span>`;
                }

                const isChecked = window.selectedLaporanNims.has(mhs.nim);
                rowsHtml += `
                    <tr class="hover:bg-orange-50/40 transition ${isChecked ? 'bg-orange-50/70' : ''}">
                        <td class="py-4 px-3 text-center">
                            <input type="checkbox" class="cb-mhs-laporan w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer"
                                   value="${mhs.nim}" ${isChecked ? 'checked' : ''} onchange="handleCheckboxChangeLaporan()">
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
                                <span class="text-[11px] text-slate-500 block truncate">Pembimbing: ${mhs.nama_tujuan || '-'}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-xs font-bold text-slate-800 block truncate">${mhs.pembimbing1_nama || '-'}</span>
                            <span class="text-[10px] text-slate-500 block">${mhs.dosen_pembimbing1 ? 'NIP: ' + mhs.dosen_pembimbing1 : 'Belum di-plot'}</span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            ${hasFile ? `
                                <button type="button" onclick="toggleLihatBerkasPanel('${mhs.nim}', 'laporan')" id="btnLihatBerkas_${mhs.nim}"
                                        class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-300 text-xs font-bold hover:bg-emerald-100 transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i> Lihat PDF
                                </button>
                            ` : `<span class="badge badge-secondary text-xs">Belum Upload</span>`}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-extrabold text-xs border border-slate-200">
                                ${mhs.poin || 0}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">${statusBadge}</td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="toggleLihatBerkasPanel('${mhs.nim}', 'laporan')" id="btnLihatBerkas_${mhs.nim}"
                                        class="px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                    <i class="bi bi-shield-check"></i> Verifikasi
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            renderPaginationControls('laporan', total, startIdx, endIdx);
            updateBatchActionBar();
        }

        // =========================================================================
        // PAGINATION CONTROLS
        // =========================================================================
        function changePerPage(target) {
            const isPersyaratan = (target === 'persyaratan');
            const select = document.getElementById(isPersyaratan ? 'perPagePersyaratan' : 'perPageLaporan');
            const val = select.value === 'all' ? 999999 : parseInt(select.value);

            if (isPersyaratan) {
                perPagePersyaratan = val;
                pagePersyaratan = 1;
                renderTablePersyaratan();
            } else {
                perPageLaporan = val;
                pageLaporan = 1;
                renderTableLaporan();
            }
        }

        function goToPage(target, page) {
            if (page < 1) return;
            if (target === 'persyaratan') {
                pagePersyaratan = page;
                renderTablePersyaratan();
            } else {
                pageLaporan = page;
                renderTableLaporan();
            }
        }

        function renderPaginationControls(target, totalItems, startIdx, endIdx) {
            const isPersyaratan = (target === 'persyaratan');
            const info = document.getElementById(isPersyaratan ? 'paginationInfoPersyaratan' : 'paginationInfoLaporan');
            const btns = document.getElementById(isPersyaratan ? 'paginationButtonsPersyaratan' : 'paginationButtonsLaporan');
            const curPage = isPersyaratan ? pagePersyaratan : pageLaporan;
            const perP = isPersyaratan ? perPagePersyaratan : perPageLaporan;

            if (!info || !btns) return;

            if (totalItems === 0) {
                info.textContent = 'Tidak ada data';
                btns.innerHTML = '';
                return;
            }

            info.textContent = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} data`;
            if (perP >= 999999) { btns.innerHTML = ''; return; }

            const totalPages = Math.ceil(totalItems / perP);
            let html = '';
            html += `<button class="pagination-btn" onclick="goToPage('${target}', ${curPage - 1})" ${curPage === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;

            const maxButtons = 5;
            let startPage = Math.max(1, curPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) startPage = Math.max(1, endPage - maxButtons + 1);

            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPage('${target}', 1)">1</button>`;
                if (startPage > 2) html += `<span class="pagination-ellipsis">…</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="pagination-btn ${i === curPage ? 'active' : ''}" onclick="goToPage('${target}', ${i})">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += `<span class="pagination-ellipsis">…</span>`;
                html += `<button class="pagination-btn" onclick="goToPage('${target}', ${totalPages})">${totalPages}</button>`;
            }

            html += `<button class="pagination-btn" onclick="goToPage('${target}', ${curPage + 1})" ${curPage === totalPages ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
            btns.innerHTML = html;
        }

        // =========================================================================
        // MULTIPLE SELECT & ACC MASSAL LOGIC (MATCHING DOSEN BIMBINGAN)
        // =========================================================================
        window.selectedPersyaratanNims = new Set();
        window.selectedLaporanNims     = new Set();

        function handleCheckboxChangePersyaratan() {
            const cbs = document.querySelectorAll('.cb-mhs-persyaratan');
            cbs.forEach(cb => {
                if (cb.checked) window.selectedPersyaratanNims.add(cb.value);
                else window.selectedPersyaratanNims.delete(cb.value);
            });
            updateBatchActionBar();
        }

        function toggleSelectAllPersyaratan(masterCb) {
            const cbs = document.querySelectorAll('.cb-mhs-persyaratan');
            cbs.forEach(cb => {
                cb.checked = masterCb.checked;
                if (cb.checked) window.selectedPersyaratanNims.add(cb.value);
                else window.selectedPersyaratanNims.delete(cb.value);
            });
            updateBatchActionBar();
        }

        function handleCheckboxChangeLaporan() {
            const cbs = document.querySelectorAll('.cb-mhs-laporan');
            cbs.forEach(cb => {
                if (cb.checked) window.selectedLaporanNims.add(cb.value);
                else window.selectedLaporanNims.delete(cb.value);
            });
            updateBatchActionBar();
        }

        function toggleSelectAllLaporan(masterCb) {
            const cbs = document.querySelectorAll('.cb-mhs-laporan');
            cbs.forEach(cb => {
                cb.checked = masterCb.checked;
                if (cb.checked) window.selectedLaporanNims.add(cb.value);
                else window.selectedLaporanNims.delete(cb.value);
            });
            updateBatchActionBar();
        }

        function updateBatchActionBar() {
            const bar = document.getElementById('batchActionBar');
            const badge = document.getElementById('selectedCountBadge');
            const label = document.getElementById('btnBatchAccLabel');
            if (!bar || !badge) return;

            const isPersyaratan = (currentMainTab === 'persyaratan');
            const count = isPersyaratan ? window.selectedPersyaratanNims.size : window.selectedLaporanNims.size;
            badge.textContent = count;

            if (count > 0) {
                bar.classList.remove('hidden');
                bar.classList.add('flex');
                if (label) {
                    label.textContent = isPersyaratan
                        ? 'ACC Semua Persyaratan (' + count + ' Mahasiswa)'
                        : 'ACC Laporan (' + count + ' Mahasiswa)';
                }
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
            }

            // Sync select all master checkbox
            if (isPersyaratan) {
                const master = document.getElementById('selectAllPersyaratan');
                const cbs = document.querySelectorAll('.cb-mhs-persyaratan');
                if (master && cbs.length > 0) {
                    master.checked = Array.from(cbs).every(cb => cb.checked);
                }
            } else {
                const master = document.getElementById('selectAllLaporan');
                const cbs = document.querySelectorAll('.cb-mhs-laporan');
                if (master && cbs.length > 0) {
                    master.checked = Array.from(cbs).every(cb => cb.checked);
                }
            }
        }

        function unselectAllStudents() {
            if (currentMainTab === 'persyaratan') {
                window.selectedPersyaratanNims.clear();
                document.querySelectorAll('.cb-mhs-persyaratan').forEach(cb => cb.checked = false);
                const m = document.getElementById('selectAllPersyaratan');
                if (m) m.checked = false;
            } else {
                window.selectedLaporanNims.clear();
                document.querySelectorAll('.cb-mhs-laporan').forEach(cb => cb.checked = false);
                const m = document.getElementById('selectAllLaporan');
                if (m) m.checked = false;
            }
            updateBatchActionBar();
        }

        function submitBatchAcc() {
            const isPersyaratan = (currentMainTab === 'persyaratan');
            const nims = isPersyaratan ? Array.from(window.selectedPersyaratanNims) : Array.from(window.selectedLaporanNims);
            if (nims.length === 0) return;

            const title = isPersyaratan ? 'ACC Massal Persyaratan KP?' : 'ACC Massal Laporan KP?';
            const htmlText = isPersyaratan
                ? 'Apakah Anda yakin ingin menyetujui (ACC) seluruh berkas persyaratan untuk <strong>' + nims.length + ' mahasiswa terpilih</strong> sekaligus?<br><br><span class="text-xs text-slate-500">Status seluruh mahasiswa tersebut akan berubah menjadi <strong>Disetujui LAA</strong> dan siap di-plotting oleh Koordinator KP.</span>'
                : 'Apakah Anda yakin ingin menyetujui (ACC) Laporan KP untuk <strong>' + nims.length + ' mahasiswa terpilih</strong> sekaligus?<br><br><span class="text-xs text-slate-500">Status Kerja Praktik mahasiswa tersebut akan dinyatakan <strong>Selesai</strong>.</span>';

            Swal.fire({
                title: title,
                html: htmlText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ACC Massal (' + nims.length + ')',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses ACC Massal...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    const formData = new FormData();
                    nims.forEach(nim => formData.append('nims[]', nim));

                    const endpoint = isPersyaratan ? 'adminlayanan/ajax_acc_semua_kp' : 'adminlayanan/ajax_acc_laporan_kp';

                    fetch(SITE_URL + endpoint, { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(res => {
                            if (res.status === 'success') {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
                                unselectAllStudents();
                                reloadVerifikasiData();
                            } else {
                                Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message || 'Terjadi kesalahan sistem.' });
                            }
                        })
                        .catch(err => {
                            Swal.fire({ icon: 'error', title: 'Error Koneksi', text: 'Gagal menghubungi server: ' + err.message });
                        });
                }
            });
        }

        // =========================================================================
        // MULTI-PREVIEW BERKAS LOGIC (PERSYARATAN & LAPORAN - MATCHING DOSEN BIMBINGAN)
        // =========================================================================
        window.activeLihatBerkas = []; // [{ nim: '...', mode: 'persyaratan'|'laporan' }]
        window.activePreviews    = []; // [{ nim: '...', fileCode: '...', mode: 'persyaratan'|'laporan' }]

        function previewItemKey(nim, fileCode, mode) {
            return (nim + '_' + fileCode + '_' + mode).replace(/[^a-zA-Z0-9_-]/g, '_');
        }

        function isPreviewItemActive(nim, fileCode, mode) {
            return window.activePreviews.some(p => p.nim === nim && p.fileCode === fileCode && p.mode === mode);
        }

        function toggleLihatBerkasPanel(nim, mode) {
            if (!mode) mode = currentMainTab;
            const existingIdx = window.activeLihatBerkas.findIndex(item => item.nim === nim && item.mode === mode);
            if (existingIdx > -1) {
                removeStudentFromLihatBerkas(nim, mode);
                return;
            }

            if (window.activeLihatBerkas.length >= 5) {
                const removed = window.activeLihatBerkas.shift();
                window.activePreviews = window.activePreviews.filter(p => !(p.nim === removed.nim && p.mode === removed.mode));
            }

            window.activeLihatBerkas.push({ nim, mode });
            showLihatBerkasContainer();
            refreshLihatBerkasView();
        }

        function removeStudentFromLihatBerkas(nim, mode) {
            const idx = window.activeLihatBerkas.findIndex(item => item.nim === nim && item.mode === mode);
            if (idx > -1) window.activeLihatBerkas.splice(idx, 1);
            window.activePreviews = window.activePreviews.filter(p => !(p.nim === nim && p.mode === mode));
            if (window.activeLihatBerkas.length === 0) {
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
            window.activeLihatBerkas = [];
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

        function previewBerkasItem(nim, fileCode, mode) {
            if (!mode) mode = 'persyaratan';
            const isExisting = window.activePreviews.findIndex(p => p.nim === nim && p.fileCode === fileCode && p.mode === mode);
            if (isExisting > -1) {
                window.activePreviews.splice(isExisting, 1);
                refreshLihatBerkasView();
                return;
            }

            let student = null;
            let fileObj = null;
            if (mode === 'persyaratan') {
                student = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc).find(m => m.nim === nim);
                if (student) {
                    fileObj = (student.files || []).find(f => f.kode_berkas === fileCode);
                }
            } else {
                student = RAW_DATA.laporan.find(m => m.nim === nim);
                if (student) {
                    fileObj = {
                        file: student.file_laporan,
                        file_url: student.file_laporan_url || (student.file_laporan ? BASE_URL + student.file_laporan : ''),
                        file_basename: student.file_laporan_name || 'laporan_kp.pdf',
                        nama_berkas: 'Laporan KP & Form Penilaian',
                        status: student.status_adminlaa || 'Pending'
                    };
                }
            }

            if (!fileObj || !fileObj.file_url) {
                Swal.fire({
                    icon: 'info',
                    title: 'Berkas Belum Diunggah',
                    text: 'Mahasiswa belum mengunggah dokumen ini.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            if (window.activePreviews.length >= 5) {
                window.activePreviews.shift();
            }

            window.activePreviews.push({ nim, fileCode, mode });
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
            if (!window.activeLihatBerkas || window.activeLihatBerkas.length === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const totalActive = window.activeLihatBerkas.length;
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

            let cardsHtml = '';
            window.activeLihatBerkas.forEach((item, cardIdx) => {
                const nim = item.nim;
                const mode = item.mode;
                let mhs = null;

                if (mode === 'persyaratan') {
                    mhs = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc).find(m => m.nim === nim);
                } else {
                    mhs = RAW_DATA.laporan.find(m => m.nim === nim);
                }
                if (!mhs) return;

                const cardNum = cardIdx + 1;
                let itemsHtml = '';

                if (mode === 'persyaratan') {
                    const files = mhs.files || [];
                    const uploadedCount = files.filter(f => f.file && f.file.length > 0).length;
                    const totalFiles = files.length || 4;

                    itemsHtml += `
                        <div class="mb-2 flex items-center justify-between gap-2 px-1">
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-folder2-open text-orange-500 text-sm"></i>
                                <span class="text-xs font-bold text-slate-700">Berkas Persyaratan (${uploadedCount}/${totalFiles} Terunggah)</span>
                            </div>
                            <span class="badge ${mhs.is_all_approved ? 'badge-success' : 'badge-warning'} text-[10px] font-bold">
                                ${mhs.status_file || 'Pending'}
                            </span>
                        </div>
                    `;

                    files.forEach(f => {
                        const isUp = f.file && f.file.length > 0;
                        const fileUrl = f.file_url || (isUp ? BASE_URL + f.file : '');
                        const fileName = f.file_basename || (isUp ? f.file : 'Belum diunggah');
                        const isAct = isPreviewItemActive(mhs.nim, f.kode_berkas, 'persyaratan');

                        const activeCardBorder = isAct ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' : 'border-slate-200 bg-white hover:border-slate-300';
                        const previewBtnStyle  = isAct ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';
                        const previewBtnLabel  = isAct ? 'Tutup' : 'Lihat';
                        const previewBtnIcon   = isAct ? 'bi-x-circle-fill' : 'bi-eye-fill';

                        let stBadge = '';
                        if (!isUp) stBadge = '<span class="badge badge-secondary text-[9px]">Belum Upload</span>';
                        else if (f.status === 'Approved') stBadge = '<span class="badge badge-success text-[9px]"><i class="bi bi-check2"></i> Disetujui</span>';
                        else if (f.status === 'Rejected') stBadge = '<span class="badge badge-danger text-[9px]"><i class="bi bi-x"></i> Revisi</span>';
                        else stBadge = '<span class="badge badge-warning text-[9px]"><i class="bi bi-clock"></i> Pending</span>';

                        itemsHtml += `
                            <div class="p-3 px-3.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex flex-col gap-2 ${activeCardBorder}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <i class="bi bi-file-earmark-pdf-fill text-orange-500 text-sm shrink-0"></i>
                                            <span class="font-bold text-slate-800 text-xs truncate">${f.nama_berkas}</span>
                                            ${stBadge}
                                        </div>
                                        <p class="text-[11px] font-mono text-slate-400 truncate mt-0.5" title="${fileName}">
                                            ${fileName}
                                        </p>
                                        ${f.komentar ? `<p class="text-[10px] text-rose-600 bg-rose-50 border border-rose-200 rounded px-1.5 py-0.5 mt-1 italic"><i class="bi bi-info-circle mr-1"></i>${f.komentar}</p>` : ''}
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        ${isUp ? `
                                            <button type="button"
                                                    onclick="event.stopPropagation(); previewBerkasItem('${mhs.nim}', '${f.kode_berkas}', 'persyaratan')"
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

                    // Tombol Bulk ACC di bawah card
                    itemsHtml += `
                        <div class="pt-3 border-t border-slate-200 mt-2">
                            <button type="button" onclick="confirmBulkAccPersyaratan('${mhs.nim}', '${mhs.nama}')"
                                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="bi bi-check-all text-base"></i>
                                <span>ACC Semua Berkas Persyaratan</span>
                            </button>
                        </div>
                    `;
                } else {
                    // Mode Laporan KP
                    const isUp = mhs.file_laporan && mhs.file_laporan.length > 0;
                    const fileUrl = mhs.file_laporan_url || (isUp ? BASE_URL + mhs.file_laporan : '');
                    const fileName = mhs.file_laporan_name || (isUp ? mhs.file_laporan : 'Belum diunggah');
                    const isAct = isPreviewItemActive(mhs.nim, 'laporan_kp', 'laporan');

                    const activeCardBorder = isAct ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' : 'border-slate-200 bg-white hover:border-slate-300';
                    const previewBtnStyle  = isAct ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';
                    const previewBtnLabel  = isAct ? 'Tutup' : 'Lihat';
                    const previewBtnIcon   = isAct ? 'bi-x-circle-fill' : 'bi-eye-fill';

                    let stBadge = '';
                    if (!isUp) stBadge = '<span class="badge badge-secondary text-[9px]">Belum Upload</span>';
                    else if (mhs.status_adminlaa === 'Approved') stBadge = '<span class="badge badge-success text-[9px]"><i class="bi bi-check2"></i> Disetujui</span>';
                    else if (mhs.status_adminlaa === 'Rejected') stBadge = '<span class="badge badge-danger text-[9px]"><i class="bi bi-x"></i> Revisi</span>';
                    else stBadge = '<span class="badge badge-warning text-[9px]"><i class="bi bi-clock"></i> Pending</span>';

                    itemsHtml += `
                        <div class="mb-2 flex items-center justify-between gap-2 px-1">
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-journal-check text-orange-500 text-sm"></i>
                                <span class="text-xs font-bold text-slate-700">Berkas Laporan KP</span>
                            </div>
                            ${stBadge}
                        </div>
                        <div class="p-3 px-3.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex flex-col gap-2 ${activeCardBorder}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <i class="bi bi-file-earmark-pdf-fill text-orange-500 text-sm shrink-0"></i>
                                        <span class="font-bold text-slate-800 text-xs truncate">Laporan KP &amp; Form Penilaian</span>
                                        ${stBadge}
                                    </div>
                                    <p class="text-[11px] font-mono text-slate-400 truncate mt-0.5" title="${fileName}">${fileName}</p>
                                    ${mhs.komentar ? `<p class="text-[10px] text-rose-600 bg-rose-50 border border-rose-200 rounded px-1.5 py-0.5 mt-1 italic"><i class="bi bi-info-circle mr-1"></i>${mhs.komentar}</p>` : ''}
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    ${isUp ? `
                                        <button type="button"
                                                onclick="event.stopPropagation(); previewBerkasItem('${mhs.nim}', 'laporan_kp', 'laporan')"
                                                class="px-3 h-8 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap ${previewBtnStyle}">
                                            <i class="bi ${previewBtnIcon} text-xs"></i><span>${previewBtnLabel}</span>
                                        </button>
                                        <a href="${fileUrl}" download="${fileName}" target="_blank"
                                            class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition flex items-center justify-center cursor-pointer shadow-2xs active:scale-95"
                                            title="Unduh Laporan">
                                            <i class="bi bi-download text-xs"></i>
                                        </a>
                                    ` : `
                                        <span class="text-[11px] text-slate-400 italic px-2 py-1">Belum diunggah</span>
                                    `}
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200 mt-2 flex items-center gap-2">
                            <button type="button" onclick="promptRejectLaporanPreview('${mhs.nim}')"
                                    class="flex-1 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="bi bi-x-circle-fill text-rose-600"></i>
                                <span>Tolak / Revisi</span>
                            </button>
                            <button type="button" onclick="confirmAccLaporanPreview('${mhs.nim}')"
                                    class="flex-1 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ACC Laporan</span>
                            </button>
                        </div>
                    `;
                }

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
                                <button type="button" onclick="removeStudentFromLihatBerkas('${mhs.nim}', '${mode}')" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Info Ringkas -->
                        <div class="p-3 bg-stone-50/80 border-b border-stone-200 text-[11px] text-slate-600 space-y-1">
                            <p class="truncate"><i class="bi bi-building mr-1.5 text-slate-400"></i><strong>${mhs.nama_perusahaan || '-'}</strong></p>
                            <p class="truncate"><i class="bi bi-geo-alt mr-1.5 text-slate-400"></i>${mhs.kota || mhs.alamat_perusahaan || '-'}</p>
                            <p class="truncate"><i class="bi bi-person-badge mr-1.5 text-slate-400"></i>${mhs.pembimbing1_nama ? 'Pembimbing: ' + mhs.pembimbing1_nama : 'Pembimbing: Menunggu Plotting'}</p>
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

            let html = '';
            window.activePreviews.forEach((p, idx) => {
                const nim = p.nim;
                const fileCode = p.fileCode;
                const mode = p.mode;

                let mhs = null;
                let fileObj = null;

                if (mode === 'persyaratan') {
                    mhs = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc).find(m => m.nim === nim);
                    if (mhs) fileObj = (mhs.files || []).find(f => f.kode_berkas === fileCode);
                } else {
                    mhs = RAW_DATA.laporan.find(m => m.nim === nim);
                    if (mhs) {
                        fileObj = {
                            id_file: 'laporan_kp',
                            nama_berkas: 'Laporan KP & Lembar Penilaian',
                            file: mhs.file_laporan,
                            file_url: mhs.file_laporan_url || (mhs.file_laporan ? BASE_URL + mhs.file_laporan : ''),
                            file_basename: mhs.file_laporan_name || 'laporan_kp.pdf',
                            status: mhs.status_adminlaa || 'Pending',
                            komentar: mhs.komentar || ''
                        };
                    }
                }

                if (!mhs || !fileObj) return;

                const fileUrl = fileObj.file_url || (fileObj.file ? BASE_URL + fileObj.file : '');
                const fileName = fileObj.file_basename || (fileObj.file ? fileObj.file : '-');
                const fileLabel = fileObj.nama_berkas || 'Berkas';
                const fullName = mhs.nama || 'Mahasiswa';
                const slotNum = idx + 1;
                const uniqueKey = previewItemKey(nim, fileCode, mode);

                let fileStatusBadge = '';
                if (fileObj.status === 'Approved') {
                    fileStatusBadge = '<span class="badge badge-success text-[10px]"><i class="bi bi-check2"></i> Disetujui</span>';
                } else if (fileObj.status === 'Rejected') {
                    fileStatusBadge = '<span class="badge badge-danger text-[10px]"><i class="bi bi-x"></i> Revisi</span>';
                } else {
                    fileStatusBadge = '<span class="badge badge-warning text-[10px]"><i class="bi bi-clock"></i> Pending</span>';
                }

                let footerActionButtons = '';
                if (mode === 'persyaratan') {
                    footerActionButtons = `
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="promptRejectBerkasFromPreview('${nim}', '${fileCode}', '${fileObj.id_file || ''}')"
                                    class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                <i class="bi bi-x-circle-fill text-rose-600"></i>
                                <span>Tolak / Revisi</span>
                            </button>
                            <button type="button" onclick="confirmAccBerkasFromPreview('${nim}', '${fileCode}', '${fileObj.id_file || ''}')"
                                    class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Setujui (ACC)</span>
                            </button>
                        </div>
                    `;
                } else {
                    footerActionButtons = `
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="promptRejectLaporanPreview('${nim}')"
                                    class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                <i class="bi bi-x-circle-fill text-rose-600"></i>
                                <span>Tolak / Revisi</span>
                            </button>
                            <button type="button" onclick="confirmAccLaporanPreview('${nim}')"
                                    class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Setujui Laporan</span>
                            </button>
                        </div>
                    `;
                }

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
                                <span class="text-xs font-bold text-slate-500">Status:</span>
                                ${fileStatusBadge}
                            </div>
                            ${footerActionButtons}
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
                const isActive = window.activeLihatBerkas.some(item => item.nim === nim);
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
                if (window.activeLihatBerkas && window.activeLihatBerkas.length > 0) {
                    closeLihatBerkasPanel();
                }
            }
        });

        window.addEventListener('resize', () => {
            if (window.activeLihatBerkas && window.activeLihatBerkas.length > 0) updateLihatBerkasLayout();
        });

        // Helper Aksi dari Preview Card
        /**
         * Konfirmasi Persetujuan (ACC) Berkas Persyaratan Tunggal dari Panel Preview
         * 
         * Mengirimkan request AJAX POST ke /adminlayanan/ajax_acc_berkas_kp.
         * Jika seluruh berkas wajib telah berstatus Approved, status mahasiswa di tb_kp otomatis
         * naik menjadi 'Disetujui LAA' dan siap di-plotting oleh Koordinator KP.
         * 
         * @param {string} nim NIM mahasiswa
         * @param {string} fileCode Kode berkas (formulir_kp, krs, transkrip, surat_perusahaan)
         * @param {string} idFile ID baris pada tabel file_pendaftaran_kp
         */
        function confirmAccBerkasFromPreview(nim, fileCode, idFile) {
            const mhs = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc).find(m => m.nim === nim);
            const fileObj = mhs ? (mhs.files || []).find(f => f.kode_berkas === fileCode) : null;
            const docName = fileObj ? fileObj.nama_berkas : 'Berkas';

            Swal.fire({
                title: 'Setujui Berkas?',
                html: `Apakah Anda yakin ingin menyetujui (ACC) berkas <strong>${docName}</strong> untuk mahasiswa <strong>${mhs ? mhs.nama : ''}</strong> (${nim})?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui (ACC)',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    doAjaxAccBerkas(idFile || (fileObj ? fileObj.id_file : ''), nim);
                }
            });
        }

        /**
         * Tolak / Minta Revisi Berkas Persyaratan Tunggal Beserta Catatan Kekurangan
         * 
         * Menampilkan prompt modal SweetAlert2 untuk menginput alasan penolakan/catatan revisi,
         * lalu mengirimkan request AJAX POST ke /adminlayanan/ajax_reject_berkas_kp.
         * 
         * @param {string} nim NIM mahasiswa
         * @param {string} fileCode Kode berkas
         * @param {string} idFile ID baris berkas di database
         */
        function promptRejectBerkasFromPreview(nim, fileCode, idFile) {
            const mhs = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc).find(m => m.nim === nim);
            const fileObj = mhs ? (mhs.files || []).find(f => f.kode_berkas === fileCode) : null;
            const docName = fileObj ? fileObj.nama_berkas : 'Berkas';

            Swal.fire({
                title: 'Tolak / Minta Revisi',
                html: `<p class="text-xs text-slate-600 mb-3">Masukkan catatan perbaikan untuk mahasiswa <strong>${mhs ? mhs.nama : ''}</strong> (${nim}) terkait dokumen <strong>${docName}</strong>:</p>`,
                input: 'textarea',
                inputPlaceholder: 'Tuliskan alasan penolakan atau petunjuk revisi berkas...',
                inputAttributes: { 'aria-label': 'Catatan Revisi' },
                showCancelButton: true,
                confirmButtonText: 'Kirim Revisi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Alasan / catatan revisi wajib diisi agar mahasiswa mengetahui bagian yang perlu diperbaiki!';
                    }
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const komentar = result.value.trim();
                    doAjaxRejectBerkas(idFile || (fileObj ? fileObj.id_file : ''), nim, komentar);
                }
            });
        }

        /**
         * Konfirmasi Persetujuan (ACC) Naskah Laporan Akhir Kerja Praktik (Tab 2)
         * 
         * Mengirimkan request AJAX POST ke /adminlayanan/ajax_acc_laporan_kp.
         * Memperbarui status laporan menjadi 'Approved' dan status pengajuan di tb_kp menjadi 'Selesai'.
         * 
         * @param {string} nim NIM mahasiswa
         */
        function confirmAccLaporanPreview(nim) {
            const mhs = RAW_DATA.laporan.find(m => m.nim === nim);
            Swal.fire({
                title: 'Setujui Laporan KP?',
                html: `Apakah Anda yakin ingin menyetujui (ACC) Laporan KP untuk mahasiswa <strong>${mhs ? mhs.nama : ''}</strong> (${nim})? Status KP mahasiswa akan menjadi <strong>Selesai</strong>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ACC Laporan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    doAjaxAccLaporan(nim);
                }
            });
        }

        /**
         * Tolak / Minta Revisi Naskah Laporan Akhir KP Beserta Catatan Evaluasi (Tab 2)
         * 
         * Menampilkan modal input catatan revisi, lalu POST ke /adminlayanan/ajax_reject_laporan_kp.
         * Mengubah status_file di tb_kp menjadi 'Revisi Laporan'.
         * 
         * @param {string} nim NIM mahasiswa
         */
        function promptRejectLaporanPreview(nim) {
            const mhs = RAW_DATA.laporan.find(m => m.nim === nim);
            Swal.fire({
                title: 'Tolak / Minta Revisi',
                html: `<p class="text-xs text-slate-600 mb-3">Masukkan catatan perbaikan untuk mahasiswa <strong>${mhs ? mhs.nama : ''}</strong> (${nim}) terkait Laporan KP:</p>`,
                input: 'textarea',
                inputPlaceholder: 'Tuliskan alasan penolakan atau petunjuk revisi berkas...',
                inputAttributes: { 'aria-label': 'Catatan Revisi' },
                showCancelButton: true,
                confirmButtonText: 'Kirim Revisi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Alasan / catatan revisi wajib diisi agar mahasiswa mengetahui bagian yang perlu diperbaiki!';
                    }
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const komentar = result.value.trim();
                    doAjaxRejectLaporan(nim, komentar);
                }
            });
        }

        function confirmBulkAccPersyaratan(nim, nama) {
            Swal.fire({
                title: 'ACC Seluruh Berkas?',
                html: `Apakah Anda yakin ingin menyetujui (ACC) <strong>seluruh berkas persyaratan</strong> untuk mahasiswa <strong>${nama || ''}</strong> (${nim})? Status mahasiswa akan otomatis berubah menjadi <strong>Disetujui LAA</strong>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ACC Semua Berkas',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    doAjaxBulkAccPersyaratan(nim);
                }
            });
        }

        // =========================================================================
        // AJAX CALLS
        // =========================================================================
        function doAjaxAccBerkas(idFile, nim) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('id_file', idFile);
            formData.append('nim', nim);

            fetch(SITE_URL + 'adminlayanan/ajax_acc_berkas_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1800, showConfirmButton: false });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxRejectBerkas(idFile, nim, komentar) {
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('id_file', idFile);
            formData.append('nim', nim);
            formData.append('komentar', komentar);

            fetch(SITE_URL + 'adminlayanan/ajax_reject_berkas_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Tersimpan', text: res.message, timer: 1800, showConfirmButton: false });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxBulkAccPersyaratan(nim) {
            Swal.fire({ title: 'Memproses ACC Semua...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('nim', nim);

            fetch(SITE_URL + 'adminlayanan/ajax_acc_semua_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Disetujui!', text: res.message, confirmButtonColor: '#059669' });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxAccLaporan(nim) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('nim', nim);

            fetch(SITE_URL + 'adminlayanan/ajax_acc_laporan_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Laporan Disetujui!', text: res.message, confirmButtonColor: '#059669' });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxRejectLaporan(nim, komentar) {
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('komentar', komentar);

            fetch(SITE_URL + 'adminlayanan/ajax_reject_laporan_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Diminta Revisi', text: res.message, timer: 1800, showConfirmButton: false });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        /**
         * Sinkronisasi Realtime Data Verifikasi KP (Tanpa Reload Halaman Penuh)
         * 
         * Mengambil payload JSON terbaru dari /adminlayanan/ajax_get_verifikasi_kp_data,
         * memperbarui cache lokal RAW_DATA, dan me-render ulang tabel pada tab aktif.
         */
        function reloadVerifikasiData() {
            fetch(SITE_URL + 'adminlayanan/ajax_get_verifikasi_kp_data')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        RAW_DATA.belum_acc = res.pengajuan.belum_acc || [];
                        RAW_DATA.sudah_acc = res.pengajuan.sudah_acc || [];
                        RAW_DATA.laporan   = res.laporan || [];

                        document.getElementById('badgeCountBelumAcc').textContent = RAW_DATA.belum_acc.length;
                        document.getElementById('badgeCountSudahAcc').textContent = RAW_DATA.sudah_acc.length;
                        document.getElementById('badgeTotalLaporan').textContent = RAW_DATA.laporan.length;

                        renderTablePersyaratan();
                        renderTableLaporan();

                        // Update isi Multi-Preview Berkas jika sedang terbuka
                        if (window.activeLihatBerkas && window.activeLihatBerkas.length > 0) {
                            refreshLihatBerkasView();
                        }
                    }
                })
                .catch(err => console.error('Gagal memuat ulang data realtime:', err));
        }

        // =========================================================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function() {
            renderFilterRows('persyaratan');
            renderFilterRows('laporan');
            renderTablePersyaratan();
            renderTableLaporan();
        });
    </script>
</body>
</html>
