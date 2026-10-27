<?php
/**
 * Verifikasi & Approval Rencana Studi Mahasiswa (Tahap 1) - Dosen Wali
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Perwalian Tahap 1 (Kontrak KRS) - Dosen Wali'); ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                            800: '#9a3412', 900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .swal2-container { z-index: 99999999 !important; }

        /* Unified Multi-Search Bar Styles */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 2px 10px;
            flex: 1;
            height: 44px;
            transition: all 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider {
            width: 1.5px;
            height: 20px;
            background-color: #cbd5e1;
            margin: 0 10px;
            flex-shrink: 0;
        }
        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 14px;
            padding: 6px 14px;
            height: 44px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
            flex-shrink: 0;
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
            padding: 2px 8px;
            border-radius: 99px;
        }
        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 12px;
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
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.16);
            z-index: 50;
            padding: 16px;
        }
        .extra-filter-row {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
            width: 100%;
        }
        .extra-filter-row .unified-search-pill {
            flex: 1;
            min-width: 0;
            width: 100%;
        }
        .custom-dropdown-container.open .custom-dropdown-menu {
            display: block !important;
        }

        @media (max-width: 639.98px) {
            .search-pill-container {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .btn-standalone-add {
                width: 100% !important;
                justify-content: center !important;
                height: 38px !important;
            }
            .extra-rows-card {
                position: static !important;
                margin-top: 8px !important;
                box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1) !important;
                padding: 12px !important;
            }
        }
        
        /* Stats Mobile Horizontal Snap Slider */
        .stats-slider-container {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        .stats-slider-container::-webkit-scrollbar {
            display: none !important;
        }

        .clay-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8);
            transition: all 0.2s ease;
        }

        .stat-card-3d {
            border-radius: 18px;
            padding: 16px 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 20px -4px rgba(0,0,0,0.05);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .stat-card-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -4px rgba(0,0,0,0.08);
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            transition: all 0.3s ease;
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
            .header-title-shift {
                padding-left: 3rem !important;
            }
        }
        body.curved-sidebar-desktop-collapsed .header-title-shift {
            padding-left: 3.25rem !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-gradient-to-br from-amber-100/70 via-orange-50/60 to-amber-100/80 text-slate-800 font-sans antialiased min-h-screen flex flex-col relative">

    <!-- Sidebar Partial -->
    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Page Wrapper -->
    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow">

        <!-- Top Header Bar -->
        <header class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-3.5 sm:px-8 py-2.5 sm:py-3.5 flex items-center justify-between gap-2 sm:gap-4">
            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 header-title-shift">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white shadow-md shadow-orange-500/20 shrink-0">
                    <i class="fa-solid fa-file-contract text-sm sm:text-lg"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center space-x-1.5 sm:space-x-2">
                        <span class="px-1.5 sm:px-2 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-orange-100 text-orange-800 rounded-md shrink-0">Tahap 1</span>
                        <h1 class="text-xs sm:text-lg font-bold text-slate-900 leading-tight truncate">Kontrak Rencana Studi</h1>
                    </div>
                </div>
            </div>

            <!-- Quick Link to Tahap 2 -->
            <a href="<?= site_url('dosen/wali/perwalian_tahap2'); ?>" class="inline-flex items-center px-2.5 sm:px-3.5 py-1.5 sm:py-2 text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-xl border border-purple-200 transition shrink-0 shadow-2xs whitespace-nowrap" title="Beralih ke Tahap 2 (Nilai)">
                <i class="fa-solid fa-arrow-right-arrow-left text-[11px] sm:mr-1.5 text-purple-500"></i>
                <span class="hidden sm:inline">Beralih ke </span><span class="ml-1 sm:ml-0 font-bold">Tahap 2</span><span class="hidden md:inline"> (Nilai)</span>
            </a>
        </header>

        <!-- Main Content -->
        <main class="w-full px-3 sm:px-8 py-4 sm:py-8 flex-grow">

            <?php
                $totMhs = (int)($stats['total_bimbingan'] ?? 0);
                $diajukan = (int)($stats['tahap1_diajukan'] ?? 0);
                $disetujui = (int)($stats['tahap1_disetujui'] ?? 0);
                $revisi = (int)($stats['tahap1_revisi'] ?? 0);
                $draft = (int)($stats['tahap1_draft'] ?? 0);
                $revDraft = $revisi + $draft;

                $pctDiajukan = $totMhs > 0 ? round(($diajukan / $totMhs) * 100) : 0;
                $pctDisetujui = $totMhs > 0 ? round(($disetujui / $totMhs) * 100) : 0;
                $pctRevDraft = $totMhs > 0 ? round(($revDraft / $totMhs) * 100) : 0;
            ?>

            <!-- Stats Overview Cards (Highlight Card Design & Mobile Snap Slider) -->
            <div class="relative mb-4 sm:mb-6">
                <div id="statsCardsSliderT1" class="stats-slider-container flex sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 overflow-x-auto sm:overflow-visible snap-x snap-mandatory py-1 scrollbar-none items-stretch">
                    <!-- 1. Total Mahasiswa Card -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-orange-500/40 hover:shadow-2xl hover:shadow-orange-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-orange-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-orange-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-orange-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-orange-600 transition-colors">TOTAL MAHASISWA</p>
                                    <h3 id="statT1TotalMhs" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight"><?= $totMhs; ?></h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">Mahasiswa bimbingan Anda</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-orange-500/20 blur-md group-hover:blur-lg group-hover:bg-orange-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-sm sm:shadow-md text-orange-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-users text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-orange-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-orange-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- 2. Menunggu Persetujuan Card (Cyan) -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">MENUNGGU REVIEW</p>
                                    <h3 id="statT1Diajukan" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight">
                                        <?= $diajukan; ?> <span class="text-[11px] sm:text-xs font-semibold text-cyan-600 font-normal">(<?= $pctDiajukan; ?>%)</span>
                                    </h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">Membutuhkan verifikasi KRS</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-sm sm:shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-clock text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-cyan-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- 3. KRS Disetujui Card (Emerald) -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">KRS DISETUJUI</p>
                                    <h3 id="statT1Disetujui" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight">
                                        <?= $disetujui; ?> <span class="text-[11px] sm:text-xs font-semibold text-emerald-600 font-normal">(<?= $pctDisetujui; ?>%)</span>
                                    </h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">Telah disetujui & siap kuliah</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-sm sm:shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-circle-check text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-emerald-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- 4. Revisi & Draft Card (Amber) -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-amber-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-amber-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">REVISI / DRAFT</p>
                                    <h3 id="statT1RevDraft" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight">
                                        <?= $revDraft; ?> <span class="text-[11px] sm:text-xs font-semibold text-amber-600 font-normal">(<?= $pctRevDraft; ?>%)</span>
                                    </h3>
                                    <p id="statT1SubRevDraft" class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1"><?= $revisi; ?> revisi, <?= $draft; ?> draft krs</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-sm sm:shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-clock-rotate-left text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-amber-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-amber-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-amber-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Slider Indicator Bar & Dots -->
                <div class="flex sm:hidden items-center justify-center gap-1.5 mt-2" id="statsSliderIndicatorsT1">
                    <button type="button" onclick="scrollToStatSlideT1(0)" class="stats-indicator-dot h-1.5 w-5 rounded-full bg-orange-500 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 1"></button>
                    <button type="button" onclick="scrollToStatSlideT1(1)" class="stats-indicator-dot h-1.5 w-1.5 rounded-full bg-slate-300 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 2"></button>
                    <button type="button" onclick="scrollToStatSlideT1(2)" class="stats-indicator-dot h-1.5 w-1.5 rounded-full bg-slate-300 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 3"></button>
                    <button type="button" onclick="scrollToStatSlideT1(3)" class="stats-indicator-dot h-1.5 w-1.5 rounded-full bg-slate-300 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 4"></button>
                </div>
            </div>

            <!-- Unified Multi-Search Bar Component (Exact Reference Style) -->
            <div class="clay-card p-3 sm:p-5 mb-5 sm:mb-6 space-y-2.5 sm:space-y-3">
                <div class="relative search-pill-container" id="multiSearchWrapperT1">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill">
                        <!-- Main Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container" id="dropdownMainCatT1">
                            <input type="hidden" id="mainCatT1" value="query">
                            <button type="button" onclick="toggleDropdownT1('dropdownMainCatT1', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none" title="Kategori Pencarian">
                                <span id="labelMainCatT1" class="text-sm sm:text-base leading-none block">🔍</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200"></i>
                            </button>
                            <div class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                <div onclick="selectCategoryT1('main', 'query', '🔍 Semua Kriteria', '🔍')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-orange-600"><span>🔍 Semua Kriteria</span></div>
                                <div onclick="selectCategoryT1('main', 'nama', '🏷️ Nama Mahasiswa', '🏷️')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectCategoryT1('main', 'nim', '🆔 NIM Mahasiswa', '🆔')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectCategoryT1('main', 'prodi', '🎓 Program Studi', '🎓')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🎓 Program Studi</span></div>
                                <div onclick="selectCategoryT1('main', 'status', '⚡ Status KRS', '⚡')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>⚡ Status KRS</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Text Value -->
                        <div class="flex-1 flex items-center min-w-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" id="mainInputT1" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); executeMultiSearchT1(); }" placeholder="Cari nama, NIM, prodi... (Tekan Enter)" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                        </div>

                        <!-- Tombol Cari -->
                        <button type="button" onclick="executeMultiSearchT1()" class="px-3 sm:px-3.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                        </button>
                    </div>

                    <!-- Filter Counter Button -->
                    <button type="button" onclick="toggleExtraPopoverT1(event)" class="btn-standalone-add" title="Buka / Tutup Kriteria Filter Tambahan">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-filter text-orange-500 text-xs"></i>
                            <span class="font-bold text-xs text-orange-700">Filter Tambahan</span>
                        </span>
                        <span id="filterBadgeT1" class="badge-standalone-count">1/4</span>
                    </button>

                    <!-- Extra Filter Rows Card Popover -->
                    <div id="extraCardT1" class="extra-rows-card space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-filter text-orange-500"></i> Kriteria Filter Tambahan
                            </span>
                            <button type="button" onclick="closeExtraPopoverT1()" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs transition cursor-pointer" title="Tutup Card Filter">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Container of Extra Filter Rows -->
                        <div id="extraRowsContainerT1" class="space-y-2.5"></div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1">
                            <button type="button" onclick="addExtraRowT1()" id="btnAddExtraRowT1" class="w-full sm:w-auto px-3.5 py-2 sm:py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs border border-orange-200 flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs active:scale-95 shrink-0 whitespace-nowrap">
                                <i class="fa-solid fa-plus text-[11px]"></i> <span>Tambah Kriteria Filter</span>
                            </button>
                            <span id="extraCountTextT1" class="text-[11px] text-slate-500 font-medium text-center sm:text-right shrink-0">1/4 kriteria aktif (Maks. 4)</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                            <span class="text-slate-400 text-[11px] leading-relaxed text-center sm:text-left">Gunakan kombinasi kriteria untuk mempersempit pencarian data perwalian KRS.</span>
                            <button type="button" onclick="resetMultiSearchT1()" class="w-full sm:w-auto text-rose-600 hover:text-rose-700 font-bold text-xs transition-colors cursor-pointer text-center sm:text-right py-1 sm:py-0 shrink-0 inline-flex items-center justify-center gap-1 whitespace-nowrap">
                                <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset Semua Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Search Counter & Active Filter Tags -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 text-xs text-slate-500 pt-1 border-t border-slate-100">
                    <div id="searchResultSummaryT1" class="text-[11px] sm:text-xs">
                        Menampilkan <strong class="text-slate-900 font-bold" id="visibleRowCountT1"><?= count($list_perwalian); ?></strong> dari <strong class="text-slate-900 font-bold" id="totalRowCountT1"><?= count($list_perwalian); ?></strong> mahasiswa bimbingan.
                    </div>
                    <div id="activeTagsContainerT1" class="flex flex-wrap items-center gap-1.5"></div>
                </div>
            </div>

            <!-- Table Mahasiswa Bimbingan Tahap 1 -->
            <div class="clay-card overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-4">
                    <div class="space-y-0.5">
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 leading-tight">Daftar Pengajuan Rencana Studi Mahasiswa (Tahap 1)</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 leading-normal">
                            Periksa kesesuaian mata kuliah, beban SKS, dan berkas KRS mahasiswa secara langsung (side-by-side cross-check)
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <div class="flex items-center gap-1.5 text-[11px] sm:text-xs text-slate-600 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-xl shadow-2xs">
                            <span class="font-medium">Tampilkan</span>
                            <select id="recordsPerPageT1" onchange="changePageSizeT1(this.value)" class="h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="font-medium">data/hal</span>
                        </div>
                        <span data-sync-count class="text-[11px] sm:text-xs font-bold px-2.5 py-1.5 bg-slate-100 text-slate-700 rounded-xl whitespace-nowrap shrink-0 border border-slate-200/80">
                            <?= count($list_perwalian); ?> Mahasiswa
                        </span>
                    </div>
                </div>

                <!-- 1. Desktop Table View (>= 768px) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center whitespace-nowrap">No</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Mahasiswa</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Program Studi</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Periode</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Beban SKS</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Status KRS</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php if (empty($list_perwalian)): ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Tidak ada pengajuan KRS untuk filter ini.</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Pastikan mahasiswa bimbingan telah mengajukan kontrak perwalian.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr id="noSearchResultRowT1" style="display: none;">
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Tidak ada mahasiswa yang cocok dengan kriteria pencarian.</p>
                                        <button type="button" onclick="resetMultiSearchT1()" class="mt-2 text-xs font-bold text-orange-600 hover:text-orange-700 underline cursor-pointer">Reset Semua Filter</button>
                                    </td>
                                </tr>
                                <?php $no = 1; foreach ($list_perwalian as $item): 
                                    $rowNim = strtolower(htmlspecialchars($item['nim'] ?? ''));
                                    $rowNama = strtolower(htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: '')));
                                    $rowProdi = strtolower(htmlspecialchars($item['prodi'] ?? ''));
                                    $rowStatus = strtolower(htmlspecialchars($item['status_tahap1'] ?? ''));
                                    $rowPeriode = strtolower(htmlspecialchars(($item['tahun_ajaran'] ?? '') . ' ' . ($item['semester'] ?? '')));
                                ?>
                                    <tr class="t1-table-row hover:bg-orange-50/40 transition" 
                                        id="t1Row_<?= (int)$item['id_perwalian']; ?>"
                                        data-id="<?= (int)$item['id_perwalian']; ?>"
                                        data-nim="<?= $rowNim; ?>" 
                                        data-nama="<?= $rowNama; ?>" 
                                        data-prodi="<?= $rowProdi; ?>" 
                                        data-status="<?= $rowStatus; ?>" 
                                        data-periode="<?= $rowPeriode; ?>"
                                    >
                                        <td class="py-3.5 px-4 text-center font-mono text-slate-400"><?= $no++; ?></td>
                                        
                                        <!-- Mahasiswa Identity -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 text-sm">
                                                <?= htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: 'Mahasiswa')); ?>
                                            </div>
                                            <div class="text-[11px] font-mono text-slate-500">
                                                NIM: <?= htmlspecialchars($item['nim']); ?>
                                            </div>
                                        </td>

                                        <!-- Prodi -->
                                        <td class="py-3.5 px-4">
                                            <span class="inline-block px-2 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-700 rounded-md">
                                                <?= htmlspecialchars($item['prodi'] ?: '-'); ?>
                                            </span>
                                        </td>

                                        <!-- Periode -->
                                        <td class="py-3.5 px-4 text-center font-mono text-[11px]">
                                            <div><?= htmlspecialchars($item['tahun_ajaran']); ?></div>
                                            <div class="text-slate-400"><?= htmlspecialchars($item['semester']); ?></div>
                                        </td>

                                        <!-- SKS Kontrak -->
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="font-mono font-bold text-slate-900 text-sm">
                                                <?= (int)$item['total_sks']; ?>
                                            </span>
                                            <span class="text-slate-500 text-xs font-semibold">SKS</span>
                                            <div class="text-[10px] text-slate-500">(<?= (int)$item['total_mk']; ?> MK Diajukan)</div>
                                        </td>

                                        <!-- Badge Status KRS -->
                                        <td class="py-3.5 px-4 text-center" id="t1StatusCol_<?= (int)$item['id_perwalian']; ?>">
                                            <?php
                                                $st1 = isset($item['status_tahap1']) && $item['status_tahap1'] !== '' ? $item['status_tahap1'] : 'Draft';
                                                $badgeClassMap1 = [
                                                    'Disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                    'Diajukan'  => 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse',
                                                    'Revisi'    => 'bg-rose-100 text-rose-800 border-rose-300',
                                                ];
                                                $badgeIconMap1 = [
                                                    'Disetujui' => 'fa-circle-check',
                                                    'Diajukan'  => 'fa-clock',
                                                    'Revisi'    => 'fa-triangle-exclamation',
                                                ];
                                                $badgeClass = isset($badgeClassMap1[$st1]) ? $badgeClassMap1[$st1] : 'bg-slate-100 text-slate-700 border-slate-300';
                                                $badgeIcon  = isset($badgeIconMap1[$st1]) ? $badgeIconMap1[$st1] : 'fa-pen-to-square';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold rounded-full border <?= $badgeClass; ?>">
                                                <i class="fa-solid <?= $badgeIcon; ?> mr-1 text-[10px]"></i>
                                                <?= htmlspecialchars($st1); ?>
                                            </span>
                                        </td>



                                        <!-- Action Review -->
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <button type="button" onclick="openReviewKRSModal(<?= (int)$item['id_perwalian']; ?>)" class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-semibold text-white bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 rounded-xl shadow-sm transition">
                                                <i class="fa-solid fa-code-compare mr-1.5"></i> Review & Cross-Check
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 2. Mobile Card View (< 768px) - Zero Horizontal Scrolling -->
                <div class="block md:hidden p-3 sm:p-4 space-y-3 bg-slate-50/50" id="t1MobileCardsContainer">
                    <?php if (empty($list_perwalian)): ?>
                        <div class="py-10 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-5">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <p class="font-medium text-slate-600 text-xs">Tidak ada pengajuan KRS untuk filter ini.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pastikan mahasiswa bimbingan telah mengajukan kontrak perwalian.</p>
                        </div>
                    <?php else: ?>
                        <div id="noSearchResultCardT1" class="py-10 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-5" style="display: none;">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <p class="font-medium text-slate-600 text-xs">Tidak ada mahasiswa yang cocok dengan kriteria pencarian.</p>
                            <button type="button" onclick="resetMultiSearchT1()" class="mt-2 text-xs font-bold text-orange-600 hover:text-orange-700 underline cursor-pointer">Reset Semua Filter</button>
                        </div>

                        <?php foreach ($list_perwalian as $item): 
                            $rowNim = strtolower(htmlspecialchars($item['nim'] ?? ''));
                            $rowNama = strtolower(htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: '')));
                            $rowProdi = strtolower(htmlspecialchars($item['prodi'] ?? ''));
                            $rowStatus = strtolower(htmlspecialchars($item['status_tahap1'] ?? ''));
                            $rowPeriode = strtolower(htmlspecialchars(($item['tahun_ajaran'] ?? '') . ' ' . ($item['semester'] ?? '')));
                            $full_name = htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: 'Mahasiswa'));
                            $st1 = isset($item['status_tahap1']) && $item['status_tahap1'] !== '' ? $item['status_tahap1'] : 'Draft';
                            $badgeClass = isset($badgeClassMap1[$st1]) ? $badgeClassMap1[$st1] : 'bg-slate-100 text-slate-700 border-slate-300';
                            $badgeIcon  = isset($badgeIconMap1[$st1]) ? $badgeIconMap1[$st1] : 'fa-pen-to-square';
                        ?>
                            <div class="t1-card-row bg-white border border-slate-200/90 rounded-2xl shadow-xs p-3.5 space-y-3"
                                 id="t1Card_<?= (int)$item['id_perwalian']; ?>"
                                 data-id="<?= (int)$item['id_perwalian']; ?>"
                                 data-nim="<?= $rowNim; ?>"
                                 data-nama="<?= $rowNama; ?>"
                                 data-prodi="<?= $rowProdi; ?>"
                                 data-status="<?= $rowStatus; ?>"
                                 data-periode="<?= $rowPeriode; ?>"
                            >
                                <!-- Card Header: Avatar, Name, NIM & Status -->
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-orange-100 border border-orange-200 text-orange-700 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            <?= strtoupper(substr($full_name, 0, 1)); ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs sm:text-sm truncate"><?= $full_name; ?></div>
                                            <div class="text-[11px] text-slate-500 font-mono">NIM: <?= htmlspecialchars($item['nim']); ?></div>
                                        </div>
                                    </div>
                                    <span id="t1CardStatusBadge_<?= (int)$item['id_perwalian']; ?>" class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full border shrink-0 <?= $badgeClass; ?>">
                                        <i class="fa-solid <?= $badgeIcon; ?> mr-1 text-[9px]"></i>
                                        <?= htmlspecialchars($st1); ?>
                                    </span>
                                </div>

                                <!-- Card Info Details -->
                                <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/70 space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="inline-block px-2 py-0.5 font-medium bg-white text-slate-700 rounded-md border border-slate-200">
                                            <?= htmlspecialchars($item['prodi'] ?: '-'); ?>
                                        </span>
                                        <span class="font-mono text-slate-500">
                                            <?= htmlspecialchars($item['tahun_ajaran']); ?> (<?= htmlspecialchars($item['semester']); ?>)
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                        <span class="text-slate-500">Beban Rencana Studi:</span>
                                        <span class="font-mono font-bold text-slate-900">
                                            <?= (int)$item['total_sks']; ?> SKS <span class="font-normal text-slate-500 text-[11px]">(<?= (int)$item['total_mk']; ?> MK)</span>
                                        </span>
                                    </div>

                                </div>

                                <!-- Card Action Button -->
                                <button type="button" onclick="openReviewKRSModal(<?= (int)$item['id_perwalian']; ?>)" class="w-full py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition active:scale-98 cursor-pointer">
                                    <i class="fa-solid fa-code-compare text-[11px]"></i> Review & Cross-Check
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Table Bottom Pagination Bar -->
                <?php if (!empty($list_perwalian)): ?>
                <div class="px-4 sm:px-5 py-3 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2.5 text-xs text-slate-500 font-medium">
                    <div class="text-center sm:text-left">
                        Menampilkan data <strong id="pageStartT1" class="text-slate-800 font-bold">1</strong> - <strong id="pageEndT1" class="text-slate-800 font-bold"><?= min(10, count($list_perwalian)); ?></strong> dari total <strong id="totalRecordsBottomT1" class="text-slate-800 font-bold"><?= count($list_perwalian); ?></strong> mahasiswa
                    </div>
                    <div class="flex items-center justify-center sm:justify-end gap-1 max-w-full overflow-x-auto scrollbar-none py-0.5" id="paginationContainerT1"></div>
                </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: REVIEW & CROSS-CHECK KRS TAHAP 1 (RESPONSIVE SPLIT/TABS)           -->
    <!-- ========================================================================= -->
    <div id="modalReviewKRS" class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm z-[100000] hidden flex items-center justify-center p-2.5 pb-6 sm:p-3 md:p-4">
        <div class="clay-card w-full max-w-[96vw] xl:max-w-7xl h-[80vh] max-h-[82dvh] sm:h-[92vh] sm:max-h-[92vh] flex flex-col overflow-hidden bg-white shadow-2xl rounded-2xl border border-slate-200">
            <!-- Modal Header -->
            <div class="px-3.5 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white flex items-center justify-between gap-2 sm:gap-3 shrink-0 shadow-xs">
                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0">
                        <i class="fa-solid fa-code-compare text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                            <h3 class="font-bold text-xs sm:text-base leading-tight truncate" id="m1MhsNama">Nama Mahasiswa</h3>
                            <span id="m1StatusBadge" class="px-2 py-0.5 text-[9px] sm:text-[11px] font-bold rounded-full bg-white/20 text-white border border-white/30 shrink-0"></span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-orange-100 truncate mt-0.5">
                            NIM: <span class="font-mono font-bold text-white" id="m1MhsNim">-</span> | 
                            Prodi: <span class="text-white font-medium" id="m1MhsProdi">-</span> | 
                            <span class="hidden sm:inline">Periode: <span class="text-white font-medium" id="m1MhsPeriode">-</span> | </span>
                            Beban: <span class="font-mono font-bold text-white"><span id="m1TotalSks">0</span> SKS</span> (<span id="m1CountMk">0 Mata Kuliah</span>)
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="closeReviewModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm sm:text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Tab Switcher (Hanya Muncul di Tampilan Layar HP / < lg) -->
            <div class="block lg:hidden px-3 py-2 bg-slate-100 border-b border-slate-200 shrink-0">
                <div class="grid grid-cols-2 p-1 bg-slate-200/90 rounded-xl text-[11px] sm:text-xs font-bold gap-1 select-none">
                    <button type="button" onclick="switchMobileModalTab('input')" id="m1TabBtnInput" class="py-2 px-2 rounded-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap bg-white text-orange-700 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-list-check shrink-0"></i>
                        <span class="truncate">Input KRS</span>
                        <span class="text-[10px] leading-none px-1.5 py-0.5 rounded-full bg-orange-100 text-orange-800 font-mono whitespace-nowrap shrink-0" id="m1TabBadgeMk">0 MK</span>
                    </button>
                    <button type="button" onclick="switchMobileModalTab('berkas')" id="m1TabBtnBerkas" class="py-2 px-2 rounded-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer">
                        <i class="fa-solid fa-file-pdf text-rose-500 shrink-0"></i>
                        <span class="truncate">Berkas KRS</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body: Side-by-Side on Desktop (lg), Tabbed Full-Height on Mobile (< lg) -->
            <div class="flex-grow grid grid-cols-1 lg:grid-cols-12 overflow-hidden bg-slate-100 min-h-0 relative">
                <!-- Panel Dokumen Berkas KRS Mahasiswa -->
                <div id="m1PanelBerkas" class="hidden lg:flex lg:col-span-6 xl:col-span-7 flex-col h-full border-b lg:border-b-0 lg:border-r border-slate-200 bg-slate-900 overflow-hidden min-h-0">
                    <!-- Header Panel Berkas -->
                    <div class="px-3.5 py-2 sm:py-2.5 bg-slate-800 text-slate-200 border-b border-slate-700/80 flex items-center justify-between gap-2 shrink-0">
                        <div class="flex items-center space-x-2 min-w-0">
                            <i class="fa-solid fa-file-invoice text-orange-400 text-xs sm:text-sm shrink-0"></i>
                            <span class="text-xs font-bold text-slate-100 truncate">Berkas Draft / Slip KRS Mahasiswa</span>
                        </div>
                        <div class="flex items-center space-x-1.5 shrink-0" id="m1DocActions">
                            <a id="m1DocOpenNewTab" href="#" target="_blank" class="px-2.5 py-1 text-[11px] font-medium bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg flex items-center gap-1.5 transition">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka Tab Baru
                            </a>
                        </div>
                    </div>

                    <!-- Viewer Container (PDF / Image Iframe) -->
                    <div class="flex-grow relative bg-slate-950 overflow-hidden flex items-center justify-center min-h-0" id="m1DocViewerBox">
                        <iframe id="m1DocFrame" src="" class="w-full h-full border-0 bg-white"></iframe>
                        <div id="m1DocEmpty" class="hidden absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-slate-400 bg-slate-900/95 z-10">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mx-auto mb-2 text-xl">
                                <i class="fa-solid fa-file-circle-xmark"></i>
                            </div>
                            <p class="font-bold text-xs sm:text-sm text-slate-300">Belum Ada Berkas KRS Diunggah</p>
                            <p class="text-[11px] text-slate-400 mt-1 max-w-xs leading-relaxed">Mahasiswa belum mengunggah file slip/draft KRS fisik. Anda tetap dapat memverifikasi rencana studi pada panel input.</p>
                        </div>
                    </div>

                    <!-- Tombol Beralih Cepat ke Input di Mobile -->
                    <div class="p-2.5 bg-slate-800 border-t border-slate-700 block lg:hidden shrink-0">
                        <button type="button" onclick="switchMobileModalTab('input')" class="w-full py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                            <i class="fa-solid fa-list-check"></i> Kembali ke Form & Setujui KRS ➔
                        </button>
                    </div>
                </div>

                <!-- Panel Hasil Input Rencana Studi & Keputusan Persetujuan -->
                <div id="m1PanelInput" class="flex lg:flex lg:col-span-6 xl:col-span-5 flex-col h-full bg-white overflow-hidden min-h-0">
                    <!-- Header Panel Input (Desktop) -->
                    <div class="px-3.5 py-2 sm:py-2.5 bg-slate-50 border-b border-slate-200 hidden lg:flex items-center justify-between gap-2 shrink-0">
                        <div class="flex items-center space-x-2 min-w-0">
                            <i class="fa-solid fa-list-check text-orange-600 text-xs sm:text-sm"></i>
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Hasil Input Rencana Studi</span>
                        </div>
                        <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-800 border border-orange-200" id="m1RightTotalInfo">0 SKS (0 MK)</span>
                    </div>

                    <!-- Petunjuk Cross-Check di Desktop -->
                    <div class="px-3.5 pt-2.5 pb-1 hidden lg:block shrink-0">
                        <div class="text-[11px] text-slate-600 bg-orange-50/70 border border-orange-200/80 rounded-xl p-2.5 flex items-start gap-2">
                            <i class="fa-solid fa-code-compare text-orange-600 mt-0.5 text-xs shrink-0"></i>
                            <div>Cocokkan mata kuliah hasil input di bawah ini dengan dokumen pada panel sebelah kiri sebelum menentukan persetujuan.</div>
                        </div>
                    </div>

                    <!-- Scrollable Course List (Grouped by Semester) -->
                    <div class="flex-grow overflow-y-auto px-3 sm:px-3.5 py-2 space-y-3 min-h-0">
                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-100/70 text-slate-600 font-bold uppercase text-[10px]">
                                        <tr>
                                            <th class="py-2 px-2.5 w-8 text-center">No</th>
                                            <th class="py-2 px-2.5">Kode MK</th>
                                            <th class="py-2 px-2.5">Nama Mata Kuliah</th>
                                            <th class="py-2 px-3 text-center w-16">SKS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="m1MkTableBody" class="divide-y divide-slate-100 text-slate-700"></tbody>
                                </table>
                            </div>
                            <div class="block sm:hidden divide-y divide-slate-100" id="m1MkMobileList"></div>
                        </div>
                    </div>

                    <!-- Dynamic Decision Section (1-Click Action) -->
                    <div id="m1KeputusanSection" class="p-2.5 sm:p-3 bg-slate-50 border-t border-slate-200 shrink-0"></div>
                </div>
            </div>

            <!-- Modal Footer (Desktop / Tablet) -->
            <div class="hidden sm:flex px-4 py-2 sm:py-2.5 bg-slate-50 border-t border-slate-200 items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-500 italic">Periksa seluruh mata kuliah yang dikontrak sebelum mengambil keputusan.</span>
                <button type="button" onclick="closeReviewModal()" class="px-4 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl transition text-center ml-auto cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <!-- Script Tahap 1 -->
    <script>
        let currentPerwalian = null;

        function switchMobileModalTab(tab) {
            const panelBerkas = document.getElementById('m1PanelBerkas');
            const panelInput = document.getElementById('m1PanelInput');
            const btnBerkas = document.getElementById('m1TabBtnBerkas');
            const btnInput = document.getElementById('m1TabBtnInput');

            if (tab === 'berkas') {
                if (panelBerkas) {
                    panelBerkas.classList.remove('hidden');
                    panelBerkas.classList.add('flex');
                }
                if (panelInput) {
                    panelInput.classList.add('hidden');
                    panelInput.classList.remove('flex');
                }
                if (btnBerkas) {
                    btnBerkas.className = "py-2 px-2 rounded-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap bg-white text-orange-700 shadow-xs cursor-pointer";
                }
                if (btnInput) {
                    btnInput.className = "py-2 px-2 rounded-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer";
                }
            } else {
                if (panelBerkas) {
                    panelBerkas.classList.add('hidden');
                    panelBerkas.classList.remove('flex');
                }
                if (panelInput) {
                    panelInput.classList.remove('hidden');
                    panelInput.classList.add('flex');
                }
                if (btnInput) {
                    btnInput.className = "py-2 px-2 rounded-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap bg-white text-orange-700 shadow-xs cursor-pointer";
                }
                if (btnBerkas) {
                    btnBerkas.className = "py-2 px-2 rounded-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer";
                }
            }
        }


        async function openReviewKRSModal(idPerwalian) {
            Swal.fire({
                title: 'Memuat Rencana Studi...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`<?= site_url('dosen/wali/perwalian_detail_ajax/'); ?>${idPerwalian}`);
                const json = await res.json();
                Swal.close();

                if (!json.status || !json.data) {
                    Swal.fire('Gagal', json.message || 'Tidak dapat memuat detail perwalian.', 'error');
                    return;
                }

                currentPerwalian = json.data;
                const p = currentPerwalian.perwalian;
                const mkList = currentPerwalian.mata_kuliah || [];

                const elNama = document.getElementById('m1MhsNama');
                if (elNama) elNama.innerText = p.nama_lengkap || p.nama_mahasiswa || 'Mahasiswa';

                const elNim = document.getElementById('m1MhsNim');
                if (elNim) elNim.innerText = p.nim || '-';

                const elProdi = document.getElementById('m1MhsProdi');
                if (elProdi) elProdi.innerText = p.prodi || p.user_prodi || '-';

                const elPeriode = document.getElementById('m1MhsPeriode');
                if (elPeriode) elPeriode.innerText = `${p.tahun_ajaran} (${p.semester})`;

                // Status Badge
                const stEl = document.getElementById('m1StatusBadge');
                if (stEl) {
                    stEl.innerText = p.status_tahap1 || 'Draft';
                    stEl.className = 'px-2 py-0.5 text-[10px] sm:text-[11px] font-bold rounded-full border ' + 
                        (p.status_tahap1 === 'Disetujui' ? 'bg-emerald-500/20 text-emerald-100 border-emerald-300/40' : 
                        (p.status_tahap1 === 'Diajukan' ? 'bg-amber-500/20 text-amber-100 border-amber-300/40' : 
                        'bg-slate-500/20 text-slate-100 border-slate-300/40'));
                }

                const elTotalSks = document.getElementById('m1TotalSks');
                if (elTotalSks) elTotalSks.innerText = currentPerwalian.total_sks;

                const elCountMk = document.getElementById('m1CountMk');
                if (elCountMk) elCountMk.innerText = `${mkList.length} Mata Kuliah`;

                // Load Berkas Dokumen Langsung ke Panel Kiri (Split View Cross-Check)
                const docFrame = document.getElementById('m1DocFrame');
                const docEmpty = document.getElementById('m1DocEmpty');
                const docOpenNewTab = document.getElementById('m1DocOpenNewTab');
                const docActions = document.getElementById('m1DocActions');
                const rightTotalInfo = document.getElementById('m1RightTotalInfo');

                if (rightTotalInfo) {
                    rightTotalInfo.innerText = `${currentPerwalian.total_sks} SKS (${mkList.length} MK)`;
                }

                if (p.file_krs && p.file_krs.trim() !== '') {
                    const docUrl = `<?= base_url('uploads/krs/'); ?>${p.file_krs}`;
                    if (docFrame) {
                        docFrame.src = docUrl;
                        docFrame.classList.remove('hidden');
                    }
                    if (docEmpty) docEmpty.classList.add('hidden');
                    if (docOpenNewTab) docOpenNewTab.href = docUrl;
                    if (docActions) docActions.classList.remove('hidden');
                } else {
                    if (docFrame) {
                        docFrame.src = '';
                        docFrame.classList.add('hidden');
                    }
                    if (docEmpty) docEmpty.classList.remove('hidden');
                    if (docActions) docActions.classList.add('hidden');
                }



                // Table Rows & Mobile List
                const tbody = document.getElementById('m1MkTableBody');
                const mobList = document.getElementById('m1MkMobileList');
                if (tbody) tbody.innerHTML = '';
                if (mobList) mobList.innerHTML = '';

                // Kelompokkan mata kuliah sesuai dengan semester
                const groupedBySemester = {};
                mkList.forEach(mk => {
                    const sem = parseInt(mk.semester_mk || mk.semester || 1);
                    if (!groupedBySemester[sem]) {
                        groupedBySemester[sem] = [];
                    }
                    groupedBySemester[sem].push(mk);
                });

                const sortedSemesters = Object.keys(groupedBySemester).sort((a, b) => parseInt(a) - parseInt(b));

                if (sortedSemesters.length === 0) {
                    if (tbody) tbody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-slate-400 italic">Belum ada mata kuliah yang dikontrak.</td></tr>`;
                    if (mobList) mobList.innerHTML = `<div class="p-4 text-center text-slate-400 text-xs italic">Belum ada mata kuliah yang dikontrak.</div>`;
                } else {
                    sortedSemesters.forEach(sem => {
                        const mks = groupedBySemester[sem];
                        const totalSksSem = mks.reduce((acc, curr) => acc + parseInt(curr.sks || 0), 0);

                        // Header Pemisah Semester di Tabel Desktop
                        if (tbody) {
                            tbody.innerHTML += `
                                <tr class="bg-gradient-to-r from-orange-50/90 via-amber-50/60 to-orange-50/90 border-y border-orange-200/80">
                                    <td colspan="4" class="py-2 px-3.5">
                                        <div class="flex items-center justify-between text-xs font-bold text-orange-950">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-graduation-cap text-orange-600 text-sm"></i>
                                                <span class="tracking-wide uppercase text-[11px]">Semester ${sem}</span>
                                            </span>
                                            <span class="text-[11px] font-mono font-bold px-2 py-0.5 bg-white text-orange-800 rounded-md border border-orange-200 shadow-2xs">
                                                Subtotal: ${totalSksSem} SKS (${mks.length} MK)
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            `;

                            mks.forEach((mk, idx) => {
                                tbody.innerHTML += `
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-2.5 px-3 text-center font-mono text-slate-400 text-xs">${idx + 1}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-slate-900">${escapeHtml(mk.kode_mk)}</td>
                                        <td class="py-2.5 px-3 font-medium text-slate-800">${escapeHtml(mk.nama_mk)}</td>
                                        <td class="py-2.5 px-3 text-center font-mono font-bold text-slate-700">${mk.sks}</td>
                                    </tr>
                                `;
                            });
                        }

                        // Header Pemisah Semester di Card Mobile
                        if (mobList) {
                            mobList.innerHTML += `
                                <div class="px-3 py-2 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 border-y border-orange-200/80 flex items-center justify-between text-xs font-bold text-orange-950">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-graduation-cap text-orange-600 text-sm"></i>
                                        <span class="tracking-wide uppercase text-[11px]">Semester ${sem}</span>
                                    </span>
                                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 bg-white text-orange-800 rounded border border-orange-200 shadow-2xs">
                                        ${totalSksSem} SKS
                                    </span>
                                </div>
                            `;

                            mks.forEach((mk) => {
                                mobList.innerHTML += `
                                    <div class="p-3 flex items-center justify-between gap-2.5 hover:bg-slate-50/80 transition">
                                        <div class="min-w-0 flex-1">
                                            <div class="font-mono font-bold text-xs text-slate-900">${escapeHtml(mk.kode_mk)}</div>
                                            <div class="text-xs font-medium text-slate-800 mt-0.5 leading-snug">${escapeHtml(mk.nama_mk)}</div>
                                        </div>
                                        <span class="text-[11px] font-mono font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md shrink-0">${mk.sks} SKS</span>
                                    </div>
                                `;
                            });
                        }
                    });
                }

                // Render Section Keputusan Dosen Wali Secara Langsung & Sederhana (Tanpa Komentar)
                const keputusanContainer = document.getElementById('m1KeputusanSection');
                const st = p.status_tahap1 || 'Draft';

                if (st === 'Disetujui') {
                    keputusanContainer.innerHTML = `
                        <div class="p-3 rounded-xl border border-emerald-300 bg-emerald-50/80 space-y-2.5">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-circle-check text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wide">KRS Mahasiswa Telah Disetujui (ACC)</h4>
                                    <p class="text-[11px] text-emerald-700 leading-snug mt-0.5">Tahap 1 selesai. Mahasiswa dapat mengikuti perkuliahan dan masuk antrean penilaian.</p>
                                </div>
                            </div>
                            <button type="button" onclick="submitDecisionTahap1('Revisi')" class="w-full px-3.5 py-2 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-300 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                                <i class="fa-solid fa-rotate-left text-rose-500"></i> Batalkan & Minta Revisi
                            </button>
                        </div>
                    `;
                } else if (st === 'Revisi') {
                    keputusanContainer.innerHTML = `
                        <div class="p-3 rounded-xl border border-amber-300 bg-amber-50/80 space-y-2.5">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wide">Status: Perlu Revisi</h4>
                                    <p class="text-[11px] text-amber-700 leading-snug mt-0.5">Mahasiswa diminta memperbaiki kontrak rencana studi sebelum disetujui.</p>
                                </div>
                            </div>
                            <button type="button" onclick="submitDecisionTahap1('Disetujui')" class="w-full px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-check-double"></i> Langsung Setujui KRS Ini
                            </button>
                        </div>
                    `;
                } else if (st === 'Draft') {
                    keputusanContainer.innerHTML = `
                        <div class="p-3 rounded-xl border border-slate-300 bg-slate-50 flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                                <i class="fa-solid fa-pen-ruler text-slate-400 text-sm"></i>
                                <span>Rencana studi masih berupa Draft (Belum diajukan resmi oleh mahasiswa).</span>
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-200 text-slate-600 rounded shrink-0">Draft</span>
                        </div>
                    `;
                } else {
                    // Default: 'Diajukan'
                    keputusanContainer.innerHTML = `
                        <div class="p-3 rounded-xl border-2 border-orange-200 bg-orange-50/50 space-y-2.5">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center">
                                    <i class="fa-solid fa-pen-nib text-orange-600 mr-1.5"></i> Keputusan Persetujuan Rencana Studi
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Tentukan persetujuan kontrak KRS mahasiswa ini secara langsung.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="submitDecisionTahap1('Revisi')" class="w-full px-3 py-2 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-300 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-rotate-left"></i> Minta Revisi KRS
                                </button>
                                <button type="button" onclick="submitDecisionTahap1('Disetujui')" class="w-full px-3 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-md transition cursor-pointer flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check-double"></i> Setujui KRS Mahasiswa
                                </button>
                            </div>
                        </div>
                    `;
                }

                switchMobileModalTab('input');
                const tabBadge = document.getElementById('m1TabBadgeMk');
                if (tabBadge) tabBadge.innerText = `${mkList.length} MK`;
                document.getElementById('modalReviewKRS').classList.remove('hidden');
            } catch (err) {
                console.error('Error openReviewKRSModal:', err);
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data: ' + (err.message || 'Error tidak diketahui'), 'error');
            }
        }



        function closeReviewModal() {
            document.getElementById('modalReviewKRS').classList.add('hidden');
            const docFrame = document.getElementById('m1DocFrame');
            if (docFrame) docFrame.src = '';
            currentPerwalian = null;
        }

        function previewKRSModal(url, filename) {
            if (url) window.open(url, '_blank');
        }

        async function submitDecisionTahap1(status) {
            if (!currentPerwalian) return;
            const idPerwalian = currentPerwalian.perwalian.id_perwalian;

            const actionLabel = (status === 'Disetujui') ? 'menyetujui kontrak rencana studi (KRS) mahasiswa ini' : 'meminta revisi rencana studi mahasiswa ini';
            const btnColor = (status === 'Disetujui') ? '#10b981' : '#f43f5e';

            const conf = await Swal.fire({
                title: 'Konfirmasi Keputusan',
                text: `Apakah Anda yakin ingin ${actionLabel}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: (status === 'Disetujui') ? 'Ya, Setujui KRS!' : 'Ya, Minta Revisi!',
                cancelButtonText: 'Batal'
            });

            if (!conf.isConfirmed) return;

            Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            const fd = new FormData();
            fd.append('id_perwalian', idPerwalian);
            fd.append('status', status);

            try {
                const res = await fetch(`<?= site_url('dosen/wali/perwalian_approve_tahap1'); ?>`, {
                    method: 'POST',
                    body: fd
                });
                const json = await res.json();
                Swal.close();

                if (json.status) {
                    // Update state perwalian jika masih dibuka
                    if (currentPerwalian && currentPerwalian.perwalian) {
                        currentPerwalian.perwalian.status_tahap1 = status;
                    }
                    closeReviewModal();

                    // Update UI realtime tanpa reload
                    updatePerwalianTahap1UI(idPerwalian, status, json.stats);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: json.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('Gagal', json.message, 'error');
                }
            } catch (err) {
                Swal.close();
                Swal.fire('Error', 'Gagal memproses keputusan.', 'error');
            }
        }

        function updatePerwalianTahap1UI(idPerwalian, newStatus, stats) {
            let badgeClass = 'bg-slate-100 text-slate-700 border-slate-300';
            let badgeIcon = 'fa-pen-to-square';

            if (newStatus === 'Disetujui') {
                badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                badgeIcon = 'fa-circle-check';
            } else if (newStatus === 'Diajukan') {
                badgeClass = 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse';
                badgeIcon = 'fa-clock';
            } else if (newStatus === 'Revisi') {
                badgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
                badgeIcon = 'fa-triangle-exclamation';
            }

            // 1. Update status di table row desktop
            const row = document.getElementById(`t1Row_${idPerwalian}`);
            if (row) {
                row.setAttribute('data-status', newStatus.toLowerCase());
            }
            const stCol = document.getElementById(`t1StatusCol_${idPerwalian}`);
            if (stCol) {
                stCol.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold rounded-full border ${badgeClass}">
                        <i class="fa-solid ${badgeIcon} mr-1 text-[10px]"></i>
                        ${escapeHtml(newStatus)}
                    </span>
                `;
            }

            // 2. Update status di mobile card
            const card = document.getElementById(`t1Card_${idPerwalian}`);
            if (card) {
                card.setAttribute('data-status', newStatus.toLowerCase());
            }
            const cardBadge = document.getElementById(`t1CardStatusBadge_${idPerwalian}`);
            if (cardBadge) {
                cardBadge.className = `inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full border shrink-0 ${badgeClass}`;
                cardBadge.innerHTML = `<i class="fa-solid ${badgeIcon} mr-1 text-[9px]"></i> ${escapeHtml(newStatus)}`;
            }

            // 3. Update top summary statistics
            if (stats) {
                const tot = parseInt(stats.total_bimbingan || 0);
                const diajukan = parseInt(stats.tahap1_diajukan || 0);
                const disetujui = parseInt(stats.tahap1_disetujui || 0);
                const revisi = parseInt(stats.tahap1_revisi || 0);
                const draft = parseInt(stats.tahap1_draft || 0);
                const revDraft = revisi + draft;

                const pctDiajukan = tot > 0 ? Math.round((diajukan / tot) * 100) : 0;
                const pctDisetujui = tot > 0 ? Math.round((disetujui / tot) * 100) : 0;
                const pctRevDraft = tot > 0 ? Math.round((revDraft / tot) * 100) : 0;

                const elTot = document.getElementById('statT1TotalMhs');
                if (elTot) elTot.innerText = tot;
                const elDiajukan = document.getElementById('statT1Diajukan');
                if (elDiajukan) elDiajukan.innerHTML = `${diajukan} <span class="text-[11px] sm:text-xs font-semibold text-cyan-600 font-normal">(${pctDiajukan}%)</span>`;
                const elDisetujui = document.getElementById('statT1Disetujui');
                if (elDisetujui) elDisetujui.innerHTML = `${disetujui} <span class="text-[11px] sm:text-xs font-semibold text-emerald-600 font-normal">(${pctDisetujui}%)</span>`;
                const elRevDraft = document.getElementById('statT1RevDraft');
                if (elRevDraft) elRevDraft.innerHTML = `${revDraft} <span class="text-[11px] sm:text-xs font-semibold text-amber-600 font-normal">(${pctRevDraft}%)</span>`;
                const elSub = document.getElementById('statT1SubRevDraft');
                if (elSub) elSub.innerText = `${revisi} revisi, ${draft} draft krs`;
            }
        }



        // ===== Auto-sync data tanpa refresh (polling ringan) =====
        let __syncSig = null;
        let __syncBusy = false;

        async function syncListFromServer() {
            if (__syncBusy || document.hidden) return;
            __syncBusy = true;
            try {
                const res = await fetch(window.location.href, {
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const newBody = doc.querySelector('table tbody.divide-y');
                const newCards = doc.getElementById('t1MobileCardsContainer');
                if (!newBody || !newCards) return; // mis. sesi habis -> halaman login

                const sig = newBody.innerHTML + '||' + newCards.innerHTML;
                if (sig === __syncSig) return;
                __syncSig = sig;

                const curBody = document.querySelector('table tbody.divide-y');
                const curCards = document.getElementById('t1MobileCardsContainer');
                if (curBody) curBody.innerHTML = newBody.innerHTML;
                if (curCards) curCards.innerHTML = newCards.innerHTML;

                ['statT1TotalMhs', 'statT1Diajukan', 'statT1Disetujui', 'statT1RevDraft', 'statT1SubRevDraft'].forEach(id => {
                    const n = doc.getElementById(id), c = document.getElementById(id);
                    if (n && c) c.innerHTML = n.innerHTML;
                });

                const nt = doc.getElementById('totalRowCountT1'), ct = document.getElementById('totalRowCountT1');
                if (nt && ct) ct.textContent = nt.textContent.trim();
                const nb = doc.querySelector('[data-sync-count]'), cb = document.querySelector('[data-sync-count]');
                if (nb && cb) cb.textContent = nb.textContent.trim();

                // terapkan ulang filter pencarian yang sedang aktif
                if (typeof executeMultiSearchT1 === 'function') executeMultiSearchT1();
            } catch (e) {
                /* abaikan error jaringan sesaat, coba lagi di siklus berikutnya */
            } finally {
                __syncBusy = false;
            }
        }

        setInterval(syncListFromServer, 4000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) syncListFromServer(); });

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
            });
        }

        // =========================================================================
        // MULTI-SEARCH CRITERIA ENGINE (Tahap 1)
        // Hanya menyaring data saat tombol Cari diklik atau tombol Enter ditekan!
        // =========================================================================
        let extraRowCounterT1 = 0;

        function toggleDropdownT1(dropdownId, e) {
            if (e) e.stopPropagation();
            const el = document.getElementById(dropdownId);
            if (!el) return;
            const menu = el.querySelector('.custom-dropdown-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                if (!m.contains(e.target) && !m.parentElement.contains(e.target)) {
                    m.classList.add('hidden');
                }
            });
            const popover = document.getElementById('extraCardT1');
            const toggleBtn = document.querySelector('.btn-standalone-add');
            if (popover && !popover.contains(e.target) && (!toggleBtn || !toggleBtn.contains(e.target))) {
                popover.style.display = 'none';
            }
        });

        function selectCategoryT1(scope, cat, label, icon) {
            if (scope === 'main') {
                document.getElementById('mainCatT1').value = cat;
                document.getElementById('labelMainCatT1').innerText = icon;
                const inp = document.getElementById('mainInputT1');
                inp.placeholder = getPlaceholderT1(cat);
                inp.focus();
                const menu = document.querySelector('#dropdownMainCatT1 .custom-dropdown-menu');
                if (menu) menu.classList.add('hidden');
            }
        }

        function getPlaceholderT1(cat) {
            switch(cat) {
                case 'nama': return 'Cari nama mahasiswa...';
                case 'nim': return 'Cari NIM mahasiswa...';
                case 'prodi': return 'Cari program studi...';
                case 'status': return 'Cari status (Diajukan, Disetujui, Revisi, Draft)...';
                default: return 'Ketik kata kunci lalu tekan Enter atau klik Cari...';
            }
        }

        function getCategoryEmojiT1(cat) {
            switch(cat) {
                case 'nama': return '🏷️';
                case 'nim': return '🆔';
                case 'prodi': return '🎓';
                case 'status': return '⚡';
                default: return '🔍';
            }
        }

        function toggleExtraPopoverT1(e) {
            if (e) e.stopPropagation();
            const card = document.getElementById('extraCardT1');
            if (card) {
                card.style.display = (card.style.display === 'block') ? 'none' : 'block';
            }
        }

        function closeExtraPopoverT1() {
            const card = document.getElementById('extraCardT1');
            if (card) card.style.display = 'none';
        }

        function updateFilterBadgeT1() {
            const container = document.getElementById('extraRowsContainerT1');
            const extraCount = container ? container.querySelectorAll('.extra-filter-row').length : 0;
            const total = 1 + extraCount;
            const badge = document.getElementById('filterBadgeT1');
            if (badge) badge.innerText = `${total}/4`;
            const countText = document.getElementById('extraCountTextT1');
            if (countText) countText.innerText = `${total}/4 kriteria aktif (Maks. 4)`;
        }

        function addExtraRowT1(defaultCat = 'nama') {
            const container = document.getElementById('extraRowsContainerT1');
            if (!container) return;

            const currentRows = container.querySelectorAll('.extra-filter-row').length;
            if (currentRows >= 3) {
                Swal.fire({
                    icon: 'info',
                    title: 'Maksimal 4 Kriteria',
                    text: 'Anda telah mencapai batas maksimal 4 kriteria pencarian sekaligus.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            extraRowCounterT1++;
            const rowId = extraRowCounterT1;

            const row = document.createElement('div');
            row.className = 'extra-filter-row';
            row.id = `extraRowT1_${rowId}`;
            row.innerHTML = `
                <div class="unified-search-pill">
                    <div class="relative custom-dropdown-container" id="dropdownExtraCatT1_${rowId}">
                        <input type="hidden" class="extra-cat-val" id="extraCatValT1_${rowId}" value="${defaultCat}">
                        <button type="button" onclick="toggleDropdownT1('dropdownExtraCatT1_${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                            <span id="labelExtraCatT1_${rowId}" class="text-sm sm:text-base leading-none block">${getCategoryEmojiT1(defaultCat)}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200"></i>
                        </button>
                        <div class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectExtraCategoryT1(${rowId}, 'query', '🔍 Semua Kriteria', '🔍')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🔍 Semua Kriteria</span></div>
                            <div onclick="selectExtraCategoryT1(${rowId}, 'nama', '🏷️ Nama Mahasiswa', '🏷️')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🏷️ Nama Mahasiswa</span></div>
                            <div onclick="selectExtraCategoryT1(${rowId}, 'nim', '🆔 NIM Mahasiswa', '🆔')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🆔 NIM Mahasiswa</span></div>
                            <div onclick="selectExtraCategoryT1(${rowId}, 'prodi', '🎓 Program Studi', '🎓')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🎓 Program Studi</span></div>
                            <div onclick="selectExtraCategoryT1(${rowId}, 'status', '⚡ Status KRS', '⚡')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>⚡ Status KRS</span></div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <div class="flex-1 flex items-center min-w-0">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                        <input type="text" id="extraInputT1_${rowId}" class="extra-input-val w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); executeMultiSearchT1(); }" placeholder="${getPlaceholderT1(defaultCat)}">
                    </div>
                </div>

                <button type="button" onclick="removeExtraRowT1(${rowId})" class="btn-remove-row" title="Hapus Kriteria Ini">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            `;

            container.appendChild(row);
            updateFilterBadgeT1();
            const input = document.getElementById(`extraInputT1_${rowId}`);
            if (input) input.focus();
        }

        function removeExtraRowT1(rowId) {
            const row = document.getElementById(`extraRowT1_${rowId}`);
            if (row) {
                row.remove();
                updateFilterBadgeT1();
                executeMultiSearchT1();
            }
        }

        function selectExtraCategoryT1(rowId, cat, label, icon) {
            const valInput = document.getElementById(`extraCatValT1_${rowId}`);
            const labelSpan = document.getElementById(`labelExtraCatT1_${rowId}`);
            const inp = document.getElementById(`extraInputT1_${rowId}`);
            if (valInput) valInput.value = cat;
            if (labelSpan) labelSpan.innerText = icon;
            if (inp) {
                inp.placeholder = getPlaceholderT1(cat);
                inp.focus();
            }
            const menu = document.querySelector(`#dropdownExtraCatT1_${rowId} .custom-dropdown-menu`);
            if (menu) menu.classList.add('hidden');
        }

        let currentPageT1 = 1;
        let pageSizeT1 = 10;

        function changePageSizeT1(size) {
            pageSizeT1 = parseInt(size) || 10;
            currentPageT1 = 1;
            renderPaginationT1();
        }

        function goToPageT1(page) {
            currentPageT1 = page;
            renderPaginationT1();
        }

        function renderPaginationT1() {
            const allRows = Array.from(document.querySelectorAll('.t1-table-row'));
            const allCards = Array.from(document.querySelectorAll('.t1-card-row'));

            const matchedRows = allRows.filter(r => r.dataset.filtered !== '0');
            const matchedCards = allCards.filter(c => c.dataset.filtered !== '0');
            const totalMatched = matchedRows.length;

            const totalPages = Math.max(1, Math.ceil(totalMatched / pageSizeT1));
            if (currentPageT1 > totalPages) currentPageT1 = totalPages;
            if (currentPageT1 < 1) currentPageT1 = 1;

            const startIdx = (currentPageT1 - 1) * pageSizeT1;
            const endIdx = startIdx + pageSizeT1;

            // Hide all first
            allRows.forEach(r => r.style.display = 'none');
            allCards.forEach(c => c.style.display = 'none');

            // Show current page slice & update row number
            matchedRows.slice(startIdx, endIdx).forEach((row, idx) => {
                row.style.display = '';
                const firstTd = row.querySelector('td');
                if (firstTd) firstTd.innerText = startIdx + idx + 1;
            });
            matchedCards.slice(startIdx, endIdx).forEach(card => {
                card.style.display = '';
            });

            // Update info counters
            const startNum = totalMatched > 0 ? startIdx + 1 : 0;
            const endNum = Math.min(endIdx, totalMatched);

            const elStart = document.getElementById('pageStartT1');
            const elEnd = document.getElementById('pageEndT1');
            const elTotalBottom = document.getElementById('totalRecordsBottomT1');
            if (elStart) elStart.innerText = startNum;
            if (elEnd) elEnd.innerText = endNum;
            if (elTotalBottom) elTotalBottom.innerText = totalMatched;

            const counterEl = document.getElementById('visibleRowCountT1');
            if (counterEl) counterEl.innerText = totalMatched;

            const emptyRow = document.getElementById('noSearchResultRowT1');
            if (emptyRow) emptyRow.style.display = (totalMatched === 0 && allRows.length > 0) ? '' : 'none';
            const emptyCard = document.getElementById('noSearchResultCardT1');
            if (emptyCard) emptyCard.style.display = (totalMatched === 0 && allCards.length > 0) ? '' : 'none';

            // Render windowed pagination buttons
            const container = document.getElementById('paginationContainerT1');
            if (!container) return;
            container.innerHTML = '';

            const btnBase = 'min-w-[30px] h-7 px-2 rounded-lg text-xs font-bold flex items-center justify-center transition border select-none ';
            const btnActive = 'bg-gradient-to-r from-orange-500 to-amber-600 text-white border-orange-600 shadow-xs cursor-default';
            const btnInactive = 'bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 border-slate-200 cursor-pointer';
            const btnDisabled = 'bg-slate-100 text-slate-300 border-slate-200 cursor-not-allowed pointer-events-none';

            // Prev button
            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = btnBase + (currentPageT1 <= 1 ? btnDisabled : btnInactive);
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
            if (currentPageT1 > 1) prevBtn.onclick = () => goToPageT1(currentPageT1 - 1);
            container.appendChild(prevBtn);

            // Calculate page numbers with ellipsis for large datasets
            const pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                if (currentPageT1 > 3) pages.push('...');
                const startP = Math.max(2, currentPageT1 - 1);
                const endP = Math.min(totalPages - 1, currentPageT1 + 1);
                for (let i = startP; i <= endP; i++) pages.push(i);
                if (currentPageT1 < totalPages - 2) pages.push('...');
                pages.push(totalPages);
            }

            pages.forEach(p => {
                if (p === '...') {
                    const span = document.createElement('span');
                    span.className = 'px-1.5 text-slate-400 font-bold text-xs';
                    span.innerText = '...';
                    container.appendChild(span);
                } else {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = btnBase + (p === currentPageT1 ? btnActive : btnInactive);
                    btn.innerText = p;
                    if (p !== currentPageT1) btn.onclick = () => goToPageT1(p);
                    container.appendChild(btn);
                }
            });

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = btnBase + (currentPageT1 >= totalPages ? btnDisabled : btnInactive);
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
            if (currentPageT1 < totalPages) nextBtn.onclick = () => goToPageT1(currentPageT1 + 1);
            container.appendChild(nextBtn);
        }

        // EKSEKUSI PENCARIAN (HANYA KETIKA ENTER / CARI DIKLIK)
        function executeMultiSearchT1() {
            const filters = [];

            // 1. Kriteria Utama
            const mainCat = document.getElementById('mainCatT1').value;
            const mainVal = document.getElementById('mainInputT1').value.trim().toLowerCase();
            if (mainVal) {
                filters.push({ cat: mainCat, val: mainVal });
            }

            // 2. Kriteria Tambahan
            const extraRows = document.querySelectorAll('#extraRowsContainerT1 .extra-filter-row');
            extraRows.forEach(row => {
                const catEl = row.querySelector('.extra-cat-val');
                const inpEl = row.querySelector('.extra-input-val');
                if (catEl && inpEl) {
                    const cat = catEl.value;
                    const val = inpEl.value.trim().toLowerCase();
                    if (val) {
                        filters.push({ cat: cat, val: val });
                    }
                }
            });

            function checkMatchT1(el) {
                for (const f of filters) {
                    if (f.cat === 'query') {
                        const haystack = [
                            el.dataset.nim || '',
                            el.dataset.nama || '',
                            el.dataset.prodi || '',
                            el.dataset.status || '',
                            el.dataset.periode || ''
                        ].join(' ').toLowerCase();
                        if (!haystack.includes(f.val)) return false;
                    } else if (f.cat === 'nama') {
                        if (!(el.dataset.nama || '').includes(f.val)) return false;
                    } else if (f.cat === 'nim') {
                        if (!(el.dataset.nim || '').includes(f.val)) return false;
                    } else if (f.cat === 'prodi') {
                        if (!(el.dataset.prodi || '').includes(f.val)) return false;
                    } else if (f.cat === 'status') {
                        if (!(el.dataset.status || '').includes(f.val)) return false;
                    }
                }
                return true;
            }

            // Tandai hasil saringan pada Baris Tabel Desktop & Kartu Mobile
            const rows = document.querySelectorAll('.t1-table-row');
            rows.forEach(row => {
                row.dataset.filtered = checkMatchT1(row) ? '1' : '0';
            });

            const cards = document.querySelectorAll('.t1-card-row');
            cards.forEach(card => {
                card.dataset.filtered = checkMatchT1(card) ? '1' : '0';
            });

            currentPageT1 = 1;
            renderPaginationT1();

            // Update Active Filter Tags
            renderActiveFilterTagsT1(filters);
        }

        function renderActiveFilterTagsT1(filters) {
            const container = document.getElementById('activeTagsContainerT1');
            if (!container) return;
            container.innerHTML = '';

            if (filters.length === 0) return;

            filters.forEach(f => {
                container.innerHTML += `
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                        <span>${getCategoryEmojiT1(f.cat)} ${f.cat.toUpperCase()}: <em>"${escapeHtml(f.val)}"</em></span>
                    </span>
                `;
            });
        }

        function resetMultiSearchT1() {
            document.getElementById('mainInputT1').value = '';
            document.getElementById('mainCatT1').value = 'query';
            document.getElementById('labelMainCatT1').innerText = '🔍';
            document.getElementById('mainInputT1').placeholder = getPlaceholderT1('query');

            const container = document.getElementById('extraRowsContainerT1');
            if (container) container.innerHTML = '';
            updateFilterBadgeT1();

            closeExtraPopoverT1();
            executeMultiSearchT1();
        }

        // --- Mobile Stats Slider Controls ---
        function scrollToStatSlideT1(index) {
            const slider = document.getElementById('statsCardsSliderT1');
            if (!slider) return;
            const items = slider.querySelectorAll('.stats-slide-item');
            if (items[index]) {
                const targetLeft = items[index].offsetLeft - (slider.clientWidth - items[index].clientWidth) / 2;
                slider.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
                updateStatsIndicatorsT1(index);
            }
        }

        function updateStatsIndicatorsT1(activeIndex) {
            const dots = document.querySelectorAll('#statsSliderIndicatorsT1 .stats-indicator-dot');
            dots.forEach((dot, idx) => {
                if (idx === activeIndex) {
                    dot.classList.remove('w-1.5', 'bg-slate-300');
                    dot.classList.add('w-5', 'bg-orange-500');
                } else {
                    dot.classList.remove('w-5', 'bg-orange-500');
                    dot.classList.add('w-1.5', 'bg-slate-300');
                }
            });
        }

        function initStatsSliderListenerT1() {
            const slider = document.getElementById('statsCardsSliderT1');
            if (!slider) return;
            slider.addEventListener('scroll', () => {
                const items = slider.querySelectorAll('.stats-slide-item');
                const scrollLeft = slider.scrollLeft;
                const containerCenter = scrollLeft + slider.clientWidth / 2;
                let closestIdx = 0;
                let minDiff = Infinity;
                items.forEach((item, idx) => {
                    const itemCenter = item.offsetLeft + item.clientWidth / 2;
                    const diff = Math.abs(containerCenter - itemCenter);
                    if (diff < minDiff) {
                        minDiff = diff;
                        closestIdx = idx;
                    }
                });
                updateStatsIndicatorsT1(closestIdx);
            }, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStatsSliderListenerT1();
            renderPaginationT1();
        });
    </script>
</body>
</html>
