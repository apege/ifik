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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Flatpickr Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <!-- Koordinator TA Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/koordinator_ta.css?v=' . time()); ?>">

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

        /* Radial Analog Clock Styles (Tahap Preview 2) */
        #p2TpClockHand {
            position: absolute;
            bottom: 50%;
            left: 50%;
            width: 2px;
            background: #4f46e5;
            border-radius: 2px;
            transform-origin: bottom center;
            transform: translate(-50%, 0) rotate(0deg);
            transition: none;
            z-index: 5;
            pointer-events: none;
        }

        #p2TpClockHand::after {
            content: '';
            position: absolute;
            top: -14px;
            left: -13px;
            width: 28px;
            height: 28px;
            background: #4f46e5;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.45);
            z-index: 1;
        }

        .p2-tp-clock-number {
            position: absolute;
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            border-radius: 50%;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transform: translate(-50%, -50%);
            transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
            user-select: none;
        }

        .p2-tp-clock-number.inner {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 500;
        }

        .p2-tp-clock-number.active {
            background: #4f46e5 !important;
            color: #ffffff !important;
            border-radius: 50% !important;
            font-weight: 800 !important;
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.45) !important;
            z-index: 20 !important;
        }

        /* Unified Multi-Search Pill Component (Exact Import Akun Style) */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 2px 12px;
            flex: 1;
            height: 42px;
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
            height: 42px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
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
            width: 42px;
            height: 42px;
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
        }
        .extra-filter-row.open-dropdown {
            z-index: 120 !important;
        }
        .custom-dropdown-container.open {
            z-index: 130 !important;
        }

        @keyframes spinRotatingBorder {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .table-rotating-border-wrap {
            position: relative;
            border-radius: 16px;
            padding: 2px;
            overflow: hidden;
            box-shadow: 0 12px 36px -8px rgba(234, 88, 12, 0.15), 0 4px 16px rgba(71, 85, 105, 0.06);
            margin-bottom: 8px;
            background: #ffffff;
        }

        .table-rotating-border-spin {
            position: absolute;
            inset: -350%;
            background: conic-gradient(
                from 90deg at 50% 50%,
                #ea580c 0%,
                #f97316 12%,
                #ffffff 22%,
                #cbd5e1 35%,
                #475569 48%,
                #1e293b 58%,
                #ea580c 68%,
                #ffffff 80%,
                #94a3b8 90%,
                #ea580c 100%
            );
            animation: spinRotatingBorder 7s linear infinite;
            opacity: 0.95;
            pointer-events: none;
        }

        .table-rotating-border-inner {
            position: relative;
            z-index: 10;
            width: 100%;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
        }

        .table-custom-rounded {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100%;
        }
        .table-custom-rounded thead tr th:first-child {
            border-top-left-radius: 14px;
        }
        .table-custom-rounded thead tr th:last-child {
            border-top-right-radius: 14px;
        }

        @keyframes rowSlideDown {
            0% {
                opacity: 0;
                transform: translateY(-22px) scale(0.975);
                filter: blur(6px);
            }
            65% {
                opacity: 0.9;
                transform: translateY(2px) scale(1.002);
                filter: blur(0px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0px);
            }
        }
        .table-row-animate {
            animation: rowSlideDown 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
            animation-delay: calc(var(--row-index, 0) * 0.045s);
            will-change: transform, opacity, filter;
        }

        /* 3D KINETIC INTERACTIVE BUTTON */
        @keyframes charAppear {
            0% {
                transform: translateY(120%);
                opacity: 0;
                filter: blur(8px);
            }
            40% {
                transform: translateY(30%);
                opacity: 0.8;
                filter: blur(2px);
            }
            70% {
                transform: translateY(-15%);
                opacity: 1;
                filter: blur(0);
            }
            100% {
                transform: translateY(0);
                opacity: 1;
                filter: blur(0);
            }
        }

        @keyframes charDisappear {
            0% {
                transform: translateY(0);
                opacity: 1;
                filter: blur(0);
            }
            30% {
                transform: translateY(-20%);
                opacity: 0.9;
                filter: blur(1px);
            }
            70% {
                transform: translateY(50%);
                opacity: 0.3;
                filter: blur(4px);
            }
            100% {
                transform: translateY(120%);
                opacity: 0;
                filter: blur(8px);
            }
        }

        @keyframes splashAnimation {
            0% {
                transform: translate(-50%, -50%) scale(0.6) rotate(0deg);
                opacity: 0.8;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.15) rotate(180deg);
                opacity: 0.5;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.3) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes pathAnimation {
            0% { stroke-dashoffset: 200; }
            100% { stroke-dashoffset: 0; }
        }

        .btn-3d-kinetic {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            outline: none;
            border: none;
            background: transparent;
            padding: 0;
            text-decoration: none;
            user-select: none;
            font-family: inherit;
        }

        .btn-3d-kinetic .bg {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%);
            box-shadow: 0 4px 14px -2px rgba(234, 88, 12, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.25) inset;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-3d-kinetic:hover .bg {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 50%, #9a3412 100%);
            box-shadow: 0 6px 20px -2px rgba(234, 88, 12, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
            transform: scale(1.02);
        }

        .btn-3d-kinetic.btn-indigo .bg {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            box-shadow: 0 4px 14px -2px rgba(79, 70, 229, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.25) inset;
        }

        .btn-3d-kinetic.btn-indigo:hover .bg {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #3730a3 100%);
            box-shadow: 0 6px 20px -2px rgba(79, 70, 229, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
            transform: scale(1.02);
        }

        .btn-3d-kinetic.btn-compact .wrap {
            min-width: 114px;
            height: 32px;
            padding: 4px 10px;
        }

        .btn-3d-kinetic.btn-compact .char {
            font-size: 10.5px;
        }

        .btn-3d-kinetic.btn-compact .icon-action {
            font-size: 10px;
        }

        .btn-3d-kinetic:active .bg {
            transform: scale(0.98);
        }

        .btn-3d-kinetic .splash {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            width: 140px;
            height: 90px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .btn-3d-kinetic:hover .splash {
            opacity: 0.9;
            animation: splashAnimation 0.7s ease-out infinite;
        }

        .btn-3d-kinetic .wrap {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 18px;
            min-width: 146px;
            height: 38px;
            border-radius: 9999px;
        }

        .btn-3d-kinetic .path {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            stroke: rgba(255, 255, 255, 0.6);
            stroke-dasharray: 200;
            stroke-dashoffset: 0;
            pointer-events: none;
        }

        .btn-3d-kinetic:hover .path {
            animation: pathAnimation 1.3s linear infinite;
        }

        .btn-3d-kinetic .outline {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            pointer-events: none;
        }

        .btn-3d-kinetic .content {
            position: relative;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .btn-3d-kinetic .char {
            display: flex;
            align-items: center;
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
            position: relative;
            white-space: nowrap;
        }

        .btn-3d-kinetic .char span {
            display: inline-block;
            animation-duration: 0.45s;
            animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
            animation-fill-mode: both;
        }

        .btn-3d-kinetic .char.state-1 span {
            animation-delay: calc(var(--i) * 0.022s);
        }

        .btn-3d-kinetic .char.state-2 {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-3d-kinetic .char.state-2 span {
            opacity: 0;
            transform: translateY(120%);
            filter: blur(8px);
            animation-delay: calc(var(--i) * 0.022s);
        }

        .btn-3d-kinetic:hover .char.state-1 span {
            animation-name: charDisappear;
        }
        .btn-3d-kinetic:hover .char.state-2 span {
            animation-name: charAppear;
        }

        .btn-3d-kinetic .icon-action {
            color: #ffffff;
            font-size: 11px;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-block;
        }
        .btn-3d-kinetic:hover .icon-action {
            transform: translateX(4px) scale(1.15);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-24">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg shadow-sm">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Portal Koordinator Tugas Akhir</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Peninjauan berkas pendaftaran dan persetujuan alur Tugas Akhir mahasiswa IFIK.</p>
                </div>
            </div>

            <!-- Profile Badge Right -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-bold text-slate-800 leading-tight">Koordinator TA IFIK</span>
                    <span class="text-[10px] font-semibold text-slate-500">NIP: <?= $nip_koor ?? '19800202002'; ?></span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-base shadow-xs">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Tab Switcher (Tahap Pendaftaran TA vs Tahap Preview 2) -->
        <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
            <div class="inline-flex p-1.5 bg-slate-200/70 backdrop-blur-md rounded-2xl border border-slate-300/60 shadow-inner">
                <button type="button" id="tabBtnPendaftaran" onclick="switchDashboardTab('pendaftaran')" class="dashboard-tab-btn active px-5 py-2.5 rounded-xl font-bold text-xs transition-all duration-300 flex items-center gap-2 bg-white text-orange-600 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                    <span>Pendaftaran TA (Plotting Pembimbing)</span>
                </button>
                <button type="button" id="tabBtnPreview2" onclick="switchDashboardTab('preview2')" class="dashboard-tab-btn px-5 py-2.5 rounded-xl font-bold text-xs transition-all duration-300 flex items-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-white/50 cursor-pointer">
                    <i class="fa-solid fa-chalkboard-user text-sm text-indigo-500"></i>
                    <span>Tahap Preview 2 (Plotting Penguji & Sidang)</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full bg-indigo-100 text-indigo-700 ml-1">Baru</span>
                </button>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 1: PENDAFTARAN TA (PLOTTING PEMBIMBING)               -->
        <!-- ========================================================= -->
        <div id="tabContentPendaftaran" class="space-y-6">

        <?php
            $totalMhs = count($list_mahasiswa ?? []);
            $pendingCount = 0;
            $approvedCount = 0;
            $rejectedCount = 0;

            if(!empty($list_mahasiswa)) {
                foreach($list_mahasiswa as $row) {
                    $st = $row['status_approval_koor'] ?? 'Pending';
                    if($st === 'Approved') $approvedCount++;
                    else if($st === 'Rejected') $rejectedCount++;
                    else $pendingCount++;
                }
            }
        ?>

        <!-- Stats Overview Cards (Exact Interactive Design from Import Akun) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- 1. Total Mahasiswa TA Card -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-brand-500/40 hover:shadow-2xl hover:shadow-brand-500/10 p-5">
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-brand-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-brand-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-brand-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Mahasiswa TA</p>
                            <h3 id="statTotalCount" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $totalMhs; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Pengajuan Tugas Akhir</p>
                        </div>
                        
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-brand-500/20 blur-md group-hover:blur-lg group-hover:bg-brand-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-md text-brand-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-brand-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Menunggu Approval Card (Cyan) -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Menunggu Approval</p>
                            <h3 id="statPendingCount" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $pendingCount; ?> <span class="text-xs font-semibold text-cyan-600 font-normal">(<?= $totalMhs > 0 ? round(($pendingCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu Ditolak / Disetujui</p>
                        </div>
                        
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-clock text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Disetujui Card (Emerald) -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5">
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Disetujui</p>
                            <h3 id="statApprovedCount" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $approvedCount; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $totalMhs > 0 ? round(($approvedCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Lanjut ke Ketua KK</p>
                        </div>
                        
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-check-circle text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Perlu Revisi Card (Amber) -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 p-5">
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-amber-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-amber-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Perlu Revisi</p>
                            <h3 id="statRejectedCount" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $rejectedCount; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Telah Ditolak / Perlu Revisi</p>
                        </div>
                        
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-circle-xmark text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-amber-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Toolbar & Filters (Exact Card Container from Import Akun) -->
        <div class="card-custom p-5 mb-8 space-y-4">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                        <i class="fa-solid fa-list-check text-brand-600 text-lg"></i> Daftar Pengajuan Tugas Akhir
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Pilih mahasiswa untuk meninjau berkas dan melakukan persetujuan Koordinator TA.</p>
                </div>
            </div>

            <!-- Row 1: Unified Multi-Search Bar (Exact Import Akun Style) -->
            <div class="relative search-pill-container" id="multiSearchWrapper">
                <!-- Main Search Pill -->
                <div class="unified-search-pill">
                    <!-- Main Category Selector Dropdown -->
                    <div class="relative custom-dropdown-container">
                        <input type="hidden" id="mainCategorySelect" value="query">
                        <button type="button" onclick="toggleCustomDropdown('main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-brand-600 focus:outline-none">
                            <span id="label-filter-main-cat" class="truncate max-w-[130px]">Cari Kata Kunci</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-cat"></i>
                        </button>
                        <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>🔍 Kata Kunci (Semua)</span></div>
                            <div onclick="selectMainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🏷️ Nama Mahasiswa</span></div>
                            <div onclick="selectMainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🆔 NIM Mahasiswa</span></div>
                            <div onclick="selectMainCategory('judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>📖 Judul Tugas Akhir</span></div>
                            <div onclick="selectMainCategory('konsentrasi', '🎯 Bidang / Peminatan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🎯 Bidang / Peminatan</span></div>
                            <div onclick="selectMainCategory('status', '⚡ Status Approval', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>⚡ Status Approval</span></div>
                            <div onclick="selectMainCategory('tahap', '🔄 Tahap Saat Ini', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>🔄 Tahap Saat Ini</span></div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <!-- Input Text Value Container -->
                    <div id="mainValueContainer" class="flex-1 flex items-center">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="mainSearchInput" oninput="handleUnifiedMultiSearch()" placeholder="Cari Nama, NIM, Judul TA, Tahap..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
                    </div>

                    <!-- Main Custom Select Dropdown Container -->
                    <div id="mainCustomSelectWrap" class="hidden flex-1 relative custom-dropdown-container">
                        <input type="hidden" id="mainCustomSelectVal" value="">
                        <button type="button" onclick="toggleCustomDropdown('main-select', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                            <span id="label-filter-main-select" class="flex items-center gap-1.5 truncate">Semua Data</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-select"></i>
                        </button>
                        <div id="menu-filter-main-select" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                        </div>
                    </div>
                </div>

                <!-- Standalone Add Filter Button (+ 1/4) -->
                <button type="button" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)" class="btn-standalone-add" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
                </button>

                <!-- Extra Filter Rows Card Popover -->
                <div id="extraRowsCard" class="extra-rows-card space-y-2.5">
                    <div id="additionalFilterRowsContainer" class="space-y-2.5">
                    </div>
                    
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian data akun.</span>
                        <button type="button" onclick="resetImportMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Row 2: Page Size & Records Count -->
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500 font-medium">
                    <span>Kelola & telusuri data pengajuan tugas akhir mahasiswa secara langsung.</span>
                </div>

                <!-- Page Size & Counter Right -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-9 rounded-xl shadow-2xs">
                        <span class="font-medium">Tampilkan</span>
                        <select id="selectPerPage" onchange="changePageSize(this.value)" class="h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-brand-500 cursor-pointer">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="font-medium">data/hal</span>
                        <span class="text-slate-300">|</span>
                        <span>Total: <strong class="total-rows-count text-slate-900 font-bold" id="toolbarTotalCount">0</strong></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Table with Rotating Conic-Gradient Border (Exact Import Akun Style) -->
        <div class="table-rotating-border-wrap">
            <span class="table-rotating-border-spin"></span>
            <div class="table-rotating-border-inner overflow-x-auto">
                <table class="table-custom-rounded text-left text-xs">
                    <thead class="bg-white text-slate-700 font-semibold text-xs border-b border-slate-200/90">
                        <tr>
                            <th class="w-10 py-4 px-4 pl-6 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-slate-300 cursor-pointer" title="Pilih Semua di Halaman Ini">
                            </th>
                            <th class="py-4 px-4 font-bold">NIM</th>
                            <th class="py-4 px-4">Nama Mahasiswa</th>
                            <th class="py-4 px-4">Usulan Judul TA (Utama)</th>
                            <th class="py-4 px-4 text-center">Status Approval</th>
                            <th class="py-4 px-4 text-center">Tahap Saat Ini</th>
                            <th class="py-4 px-4 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tableBodyMhs">
                        <!-- Injected via JS renderTable() -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Bottom Pagination Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan data <strong id="pageStart" class="text-slate-800 font-bold">1</strong> - <strong id="pageEnd" class="text-slate-800 font-bold">10</strong> dari total <strong id="totalRecordsBottom" class="text-slate-800 font-bold">0</strong> mahasiswa
            </div>
            <div class="pagination-controls-bottom flex items-center gap-1" id="paginationNav">
                <!-- Pagination buttons rendered via JS -->
            </div>
        </div>

        <!-- FLOATING BATCH ACTION BAR (Slide up from bottom when checked) -->
        <div id="floatingBatchBar" class="floating-batch-bar w-full max-w-4xl px-4">
            <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                        <span id="selectedCountBadge">0</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white tracking-wide">Mahasiswa Terpilih untuk Aksi Massal</h4>
                        <div id="selectedStudentsPreview" class="flex flex-wrap items-center gap-1.5 mt-1">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <button type="button" onclick="openBatchModal('Approved')" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-user-check"></i> Setujui & Plot Dosen
                    </button>
                    <button type="button" onclick="openBatchModal('Rejected')" class="px-4 py-2.5 bg-rose-600/90 hover:bg-rose-600 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-ban"></i> Tolak
                    </button>
                    <button type="button" onclick="clearAllSelection()" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition cursor-pointer" title="Batal Pilihan">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                </div>
            </div>
        </div>

        </div> <!-- /#tabContentPendaftaran -->

        <!-- ========================================================= -->
        <!-- TAB 2: TAHAP PREVIEW 2 (PLOTTING PENGUJI & SIDANG)        -->
        <!-- ========================================================= -->
        <div id="tabContentPreview2" class="hidden space-y-6">

            <?php
                $totalP2 = count($list_preview2 ?? []);
                $terjadwalP2 = 0;
                $pengujiSetP2 = 0;
                $belumSetP2 = 0;

                if(!empty($list_preview2)) {
                    foreach($list_preview2 as $r2) {
                        $stP2 = $r2['status_preview2'] ?? 'Belum Diplot';
                        if($stP2 === 'Terjadwal') $terjadwalP2++;
                        else if($stP2 === 'Penguji Ditetapkan') $pengujiSetP2++;
                        else $belumSetP2++;
                    }
                }
            ?>

            <!-- Stats Overview Cards (Exact Interactive Design from Import Akun / Tab 1) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- 1. Total Mahasiswa Preview 2 (Indigo) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-indigo-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-indigo-500/40 hover:shadow-2xl hover:shadow-indigo-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-indigo-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-indigo-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-indigo-600 transition-colors">Total Mahasiswa Preview 2</p>
                                <h3 id="statP2Total" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $totalP2; ?></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Siap Sidang Preview 2</p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-indigo-500/20 blur-md group-hover:blur-lg group-hover:bg-indigo-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-indigo-100/70 shadow-md text-indigo-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-chalkboard-user text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-indigo-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Terjadwal Lengkap Card (Emerald) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Terjadwal Lengkap</p>
                                <h3 id="statP2Terjadwal" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $terjadwalP2; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $totalP2 > 0 ? round(($terjadwalP2/$totalP2)*100) : 0; ?>%)</span></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Penguji & Ruangan Lengkap</p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-calendar-check text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Penguji Ditetapkan Card (Cyan) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Penguji Ditetapkan</p>
                                <h3 id="statP2Penguji" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $pengujiSetP2; ?></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Belum Pilih Ruangan / Waktu</p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-user-group text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Belum Diplot Penguji Card (Amber) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-amber-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-amber-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Belum Diplot Penguji</p>
                                <h3 id="statP2Belum" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $belumSetP2; ?> <span class="text-xs font-semibold text-amber-600 font-normal">(<?= $totalP2 > 0 ? round(($belumSetP2/$totalP2)*100) : 0; ?>%)</span></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu Ditetapkan Penguji</p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-clock text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-amber-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Toolbar & Filters (Exact Card Container from Import Akun / Tab 1) -->
            <div class="card-custom p-5 mb-8 space-y-4">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                            <i class="fa-solid fa-chalkboard-user text-indigo-600 text-lg"></i> Daftar Plotting Penguji & Jadwal Sidang Preview 2
                        </h2>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Tetapkan Dosen Penguji 1 & 2 serta jadwalkan ruangan sidang preview 2 untuk mahasiswa.</p>
                    </div>
                </div>

                <!-- Row 1: Unified Multi-Search Bar for Preview 2 -->
                <div class="relative search-pill-container" id="p2MultiSearchWrapper">
                    <div class="unified-search-pill">
                        <!-- Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" id="p2MainCategorySelect" value="query">
                            <button type="button" onclick="toggleCustomDropdown('p2-main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-indigo-600 focus:outline-none">
                                <span id="label-filter-p2-main-cat" class="truncate max-w-[130px]">Cari Kata Kunci</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-p2-main-cat"></i>
                            </button>
                            <div id="menu-filter-p2-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                <div onclick="selectP2MainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-indigo-50 text-indigo-600"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectP2MainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectP2MainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectP2MainCategory('judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>📖 Judul Tugas Akhir</span></div>
                                <div onclick="selectP2MainCategory('pembimbing', '👔 Dosen Pembimbing', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>👔 Dosen Pembimbing</span></div>
                                <div onclick="selectP2MainCategory('penguji', '👨‍🏫 Dosen Penguji', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>👨‍🏫 Dosen Penguji</span></div>
                                <div onclick="selectP2MainCategory('ruangan', '🏛️ Ruangan Sidang', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>🏛️ Ruangan Sidang</span></div>
                                <div onclick="selectP2MainCategory('status', '⚡ Status Plotting', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600"><span>⚡ Status Plotting</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Text Value Container -->
                        <div id="p2MainValueContainer" class="flex-1 flex items-center">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                            <input type="text" id="p2MainSearchInput" oninput="handleUnifiedMultiSearchP2()" placeholder="Cari Nama, NIM, Judul TA, Dosen Penguji, Ruangan..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
                        </div>

                        <!-- Main Custom Select Dropdown Container -->
                        <div id="p2MainCustomSelectWrap" class="hidden flex-1 relative custom-dropdown-container">
                            <input type="hidden" id="p2MainCustomSelectVal" value="">
                            <button type="button" onclick="toggleCustomDropdown('p2-main-select', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                                <span id="label-filter-p2-main-select" class="flex items-center gap-1.5 truncate">Semua Data</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-p2-main-select"></i>
                            </button>
                            <div id="menu-filter-p2-main-select" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnP2" onclick="toggleOrAddFilterRowP2(event)" class="btn-standalone-add hover:border-indigo-500 hover:text-indigo-600" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgeP2" class="badge-standalone-count bg-indigo-600">1/4</span>
                    </button>

                    <!-- Extra Filter Rows Card Popover -->
                    <div id="extraRowsCardP2" class="extra-rows-card space-y-2.5">
                        <div id="additionalFilterRowsContainerP2" class="space-y-2.5">
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 mt-2 text-xs">
                            <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian data preview 2.</span>
                            <button type="button" onclick="resetP2MultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors cursor-pointer">
                                Reset All Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Page Size & Records Count -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="text-xs text-slate-500 font-medium">
                        <span>Kelola jadwal dan plotting dosen penguji preview 2 mahasiswa secara terstruktur.</span>
                    </div>

                    <!-- Page Size & Counter Right -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-9 rounded-xl shadow-2xs">
                            <span class="font-medium">Tampilkan</span>
                            <select id="p2PageSizeSelect" onchange="changeP2PageSize(this.value)" class="h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="font-medium">data/hal</span>
                            <span class="text-slate-300">|</span>
                            <span>Total: <strong class="total-rows-count text-slate-900 font-bold" id="p2ToolbarTotalCount"><?= $totalP2; ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Table with Rotating Conic-Gradient Border (Exact Import Akun Style) -->
            <div class="table-rotating-border-wrap">
                <span class="table-rotating-border-spin"></span>
                <div class="table-rotating-border-inner overflow-hidden">
                    <table class="table-custom-rounded text-left text-xs w-full">
                        <thead class="bg-white text-slate-700 font-semibold text-xs border-b border-slate-200/90">
                            <tr>
                                <th class="w-8 py-3.5 px-3 text-center">
                                    <input type="checkbox" id="selectAllCheckboxP2" onchange="toggleSelectAllP2(this)" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer" title="Pilih Semua di Halaman Ini">
                                </th>
                                <th class="w-24 py-3.5 px-2 font-bold">NIM</th>
                                <th class="w-36 py-3.5 px-2 font-semibold">Nama Mahasiswa</th>
                                <th class="py-3.5 px-2">Usulan Judul TA</th>
                                <th class="w-36 py-3.5 px-2">Dosen Pembimbing</th>
                                <th class="w-36 py-3.5 px-2">Dosen Penguji</th>
                                <th class="w-28 py-3.5 px-2 text-center">Jadwal & Ruangan</th>
                                <th class="w-24 py-3.5 px-2 text-center">Status</th>
                                <th class="w-32 py-3.5 px-3 pr-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tableBodyP2">
                            <!-- Injected via JS renderP2Table() -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Bottom Pagination Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 text-xs text-slate-500 font-medium">
                <div>
                    Menampilkan data <strong id="p2PageStart" class="text-slate-800 font-bold">1</strong> - <strong id="p2PageEnd" class="text-slate-800 font-bold">10</strong> dari total <strong id="p2TotalRecords" class="text-slate-800 font-bold">0</strong> mahasiswa
                </div>
                <div class="pagination-controls-bottom flex items-center gap-1" id="p2PaginationNav">
                    <!-- Pagination buttons rendered via JS -->
                </div>
            </div>

            <!-- FLOATING BATCH ACTION BAR FOR PREVIEW 2 -->
            <div id="floatingP2BatchBar" class="floating-batch-bar w-full max-w-4xl px-4">
                <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                            <span id="p2SelectedCountBadge">0</span>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white tracking-wide">Mahasiswa Terpilih untuk Penetapan Penguji Massal</h4>
                            <div id="p2SelectedStudentsPreview" class="flex flex-wrap items-center gap-1.5 mt-1">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" onclick="openP2BatchModal()" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-xs shadow-md shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-users-gear"></i> Plot Penguji & Jadwal Massal
                        </button>
                        <button type="button" onclick="clearAllP2Selection()" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition cursor-pointer" title="Batal Pilihan">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </button>
                    </div>
                </div>
            </div>

        </div> <!-- /#tabContentPreview2 -->

    </main>

    <!-- PREVIEW 2 PLOTTING MODAL (SINGLE & BATCH) -->
    <div id="modalPreview2Plotting" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden">
        <!-- Backdrop click listener -->
        <div class="fixed inset-0" onclick="closeP2Modal()"></div>

        <!-- Modal Dialog Card -->
        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-indigo-50/60 via-white to-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-md shadow-indigo-600/20 shrink-0">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">Plotting Dosen Penguji & Jadwal Sidang Preview 2</h3>
                        <p class="text-xs text-slate-500">Tetapkan Dosen Penguji 1 & 2 serta tentukan ruangan dan waktu sidang.</p>
                    </div>
                </div>
                <button type="button" onclick="closeP2Modal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formP2Plotting" onsubmit="submitP2Plotting(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                <!-- Scrollable Body Content -->
                <div class="p-5 sm:p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                    
                    <!-- Selected Student(s) Summary -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Mahasiswa Terpilih:</label>
                            <span id="p2ModalSelectedCountBadge" class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">1 Mahasiswa</span>
                        </div>
                        <div id="p2ModalSelectedList" class="max-h-32 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
                        </div>
                    </div>

                    <!-- Dosen Penguji Section -->
                    <div class="space-y-4 pt-2 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-tie text-indigo-600"></i> Penetapan Dosen Penguji
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Dosen Penguji 1 -->
                            <div class="modal-combobox-wrapper relative" data-p2-slot="1">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">Dosen Penguji 1 <span class="text-rose-500">*</span></label>
                                <input type="hidden" id="p2ModalInputPenguji1" name="penguji_1" value="">

                                <!-- Chip Preview -->
                                <div id="p2ModalChipPenguji1" class="hidden p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</div>
                                        <span id="p2ModalChipPenguji1Text" class="text-xs font-bold text-indigo-950 truncate"></span>
                                    </div>
                                    <button type="button" onclick="changeP2ModalDosen(1)" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="p2ModalSearchContainer1" class="relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="p2ModalSearchPenguji1" onfocus="openP2ModalDosenList(1)" onclick="openP2ModalDosenList(1)" oninput="filterP2ModalDosen(1)" placeholder="Cari nama / NIP penguji 1..." class="w-full text-xs bg-transparent border-none focus:outline-none text-slate-800" autocomplete="off">
                                        <button type="button" id="p2ModalClear1" onclick="clearP2ModalSearch(1)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="p2ModalDropdownList1" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[100] max-h-52 overflow-y-auto custom-scrollbar p-1 divide-y divide-slate-100"></div>
                                </div>
                            </div>

                            <!-- Dosen Penguji 2 -->
                            <div class="modal-combobox-wrapper relative" data-p2-slot="2">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">Dosen Penguji 2 <span class="text-rose-500">*</span></label>
                                <input type="hidden" id="p2ModalInputPenguji2" name="penguji_2" value="">

                                <!-- Chip Preview -->
                                <div id="p2ModalChipPenguji2" class="hidden p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                                        <span id="p2ModalChipPenguji2Text" class="text-xs font-bold text-indigo-950 truncate"></span>
                                    </div>
                                    <button type="button" onclick="changeP2ModalDosen(2)" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="p2ModalSearchContainer2" class="relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="p2ModalSearchPenguji2" onfocus="openP2ModalDosenList(2)" onclick="openP2ModalDosenList(2)" oninput="filterP2ModalDosen(2)" placeholder="Cari nama / NIP penguji 2..." class="w-full text-xs bg-transparent border-none focus:outline-none text-slate-800" autocomplete="off">
                                        <button type="button" id="p2ModalClear2" onclick="clearP2ModalSearch(2)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="p2ModalDropdownList2" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[100] max-h-52 overflow-y-auto custom-scrollbar p-1 divide-y divide-slate-100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal & Ruangan Section -->
                    <div class="space-y-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-calendar-check text-indigo-600"></i> Penjadwalan & Ruangan Sidang
                            </h4>
                            <span class="text-[11px] font-semibold text-slate-400">Urutan: Ruangan &rarr; Tanggal &rarr; Waktu</span>
                        </div>

                        <!-- 1. Pilih Ruangan Sidang (Search Autocomplete Combobox) -->
                        <div class="modal-combobox-wrapper relative" data-p2-slot="ruangan">
                            <label class="text-xs font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-door-open text-indigo-600"></i> Pilih Ruangan Sidang:
                            </label>
                            <input type="hidden" id="p2ModalRuangan" name="ruangan_sidang" value="">

                            <!-- Chip Preview -->
                            <div id="p2ModalChipRuangan" class="hidden p-2.5 bg-indigo-50 border border-indigo-300 rounded-xl flex items-center justify-between shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p id="p2ModalChipRuanganName" class="text-xs font-bold text-indigo-950 truncate"></p>
                                        <p id="p2ModalChipRuanganDetails" class="text-[10px] text-indigo-700 truncate"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="changeP2ModalRuangan()" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                            </div>

                            <!-- Search Input -->
                            <div id="p2ModalSearchRuanganContainer" class="relative">
                                <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 shadow-2xs">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                    <input type="text" id="p2ModalSearchRuangan" onfocus="openP2ModalRuanganList()" onclick="openP2ModalRuanganList()" oninput="filterP2ModalRuangan(this.value)" placeholder="Cari ruangan sidang (contoh: Aula Utama, Lab 3D, Ruang Sidang 1)..." class="w-full text-xs bg-transparent border-none focus:outline-none text-slate-800" autocomplete="off">
                                    <button type="button" id="p2ModalClearRuangan" onclick="clearP2ModalRuanganSearch()" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                </div>
                                <div id="p2ModalDropdownRuangan" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[100] max-h-52 overflow-y-auto custom-scrollbar p-1 divide-y divide-slate-100"></div>
                            </div>
                        </div>

                        <!-- 2. Tanggal & Jam Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Tanggal Sidang (Flatpickr) -->
                            <div class="sm:col-span-1">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-day text-indigo-600"></i> Tanggal Sidang:
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-calendar-days absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                    <input type="text" id="p2ModalTanggal" name="tgl_sidang" placeholder="Pilih Tanggal..." class="w-full text-xs pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer font-bold text-slate-800" readonly>
                                </div>
                            </div>

                            <!-- Jam Mulai -->
                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-hourglass-start text-indigo-600"></i> Jam Mulai:
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                    <input type="text" id="p2ModalJamMulai" name="jam_mulai_sidang" placeholder="-- : --" class="w-full text-xs pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer font-bold text-slate-800" readonly onclick="openP2InlineTimePicker('mulai')">
                                </div>
                            </div>

                            <!-- Jam Selesai -->
                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-hourglass-end text-indigo-600"></i> Jam Selesai:
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                    <input type="text" id="p2ModalJamSelesai" name="jam_selesai_sidang" placeholder="-- : --" class="w-full text-xs pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer font-bold text-slate-800" readonly onclick="openP2InlineTimePicker('selesai')">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Interactive Radial Analog Clock & Quick Slot Picker Panel -->
                        <div id="p2InlineClockPanel" class="hidden mt-2 bg-gradient-to-br from-slate-50 via-indigo-50/20 to-slate-50 border border-indigo-200/80 rounded-2xl p-4 shadow-lg animate-in fade-in duration-200">
                            <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start justify-between">
                                
                                <!-- Left: Time Display & Preset Sidang Slots -->
                                <div class="w-full sm:w-1/2 bg-white rounded-xl p-3.5 border border-slate-200/90 shadow-2xs flex flex-col items-center">
                                    <div id="p2TpTargetLabel" class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider mb-1 flex items-center gap-1">
                                        <i class="fa-solid fa-stopwatch"></i> PENGATURAN JAM MULAI
                                    </div>
                                    <div class="text-3xl font-black text-slate-900 tracking-tight my-0.5 flex items-center">
                                        <span id="p2TpDisplayHour" onclick="setP2ClockMode('hour')" class="cursor-pointer hover:text-indigo-600 px-1 rounded-lg hover:bg-indigo-50 transition">08</span>
                                        <span class="text-slate-300 mx-0.5">:</span>
                                        <span id="p2TpDisplayMinute" onclick="setP2ClockMode('minute')" class="cursor-pointer hover:text-indigo-600 px-1 rounded-lg hover:bg-indigo-50 transition text-slate-400">00</span>
                                    </div>
                                    <span class="text-[9px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full mb-2.5">Format 24 Jam</span>

                                    <!-- Quick Sidang 1-Hour Time Slots (Draggable Range) -->
                                    <div class="w-full">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <p class="text-[10px] font-bold text-slate-700 flex items-center gap-1">
                                                <i class="fa-solid fa-bolt text-amber-500"></i> Slot Waktu Sidang (1 Jam):
                                            </p>
                                            <span class="text-[9px] text-slate-400 font-medium">(Bisa drag untuk rentang)</span>
                                        </div>
                                        <div id="p2TimeSlots" class="grid grid-cols-2 gap-1.5 w-full text-[11px] select-none">
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="08:00" data-end="09:00">08:00 – 09:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="09:00" data-end="10:00">09:00 – 10:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="10:00" data-end="11:00">10:00 – 11:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="11:00" data-end="12:00">11:00 – 12:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="13:00" data-end="14:00">13:00 – 14:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="14:00" data-end="15:00">14:00 – 15:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="15:00" data-end="16:00">15:00 – 16:00</div>
                                            <div class="p2-tp-slot p-2 border border-slate-200 rounded-lg text-center font-bold text-slate-700 bg-white hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition shadow-2xs select-none" data-start="16:00" data-end="17:00">16:00 – 17:00</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Radial Analog Clock Dial (240px x 240px) -->
                                <div class="w-full sm:w-1/2 bg-white rounded-xl p-3.5 border border-slate-200/90 shadow-2xs flex flex-col items-center">
                                    <div class="flex items-center gap-1.5 p-0.5 bg-slate-100 rounded-lg mb-3 w-full max-w-[200px] text-xs font-bold text-center">
                                        <div id="p2TpTabHour" onclick="setP2ClockMode('hour')" class="flex-1 py-1 px-2 rounded-md bg-indigo-600 text-white cursor-pointer shadow-2xs transition">🕐 Jam</div>
                                        <div id="p2TpTabMinute" onclick="setP2ClockMode('minute')" class="flex-1 py-1 px-2 rounded-md text-slate-600 hover:text-slate-900 cursor-pointer transition">⏱ Menit</div>
                                    </div>
                                    
                                    <div id="p2TpClockContainer" class="relative w-[240px] h-[240px] rounded-full bg-slate-50 border-2 border-slate-200 shadow-inner flex-shrink-0 cursor-pointer select-none">
                                        <div id="p2TpClockHand"></div>
                                        <div class="absolute top-1/2 left-1/2 w-2.5 h-2.5 bg-indigo-600 rounded-full -translate-x-1/2 -translate-y-1/2 z-[10] pointer-events-none"></div>
                                        <div id="p2TpClockNumbers"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Clock Action Footer -->
                            <div class="flex items-center justify-end gap-2 pt-2.5 mt-2.5 border-t border-slate-200/80">
                                <button type="button" onclick="closeP2InlineTimePicker()" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">Tutup</button>
                                <button type="button" onclick="applyP2InlineTimePicker()" class="px-4 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-600/30 transition cursor-pointer flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i> Terapkan Waktu
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 sm:px-6 border-t border-slate-100 bg-slate-50/90 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="closeP2Modal()" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="modalP2BtnSubmit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="modalP2BtnSubmitText">Simpan Penetapan Penguji & Jadwal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- BATCH APPROVAL & DOSEN PLOTTING MODAL -->
    <div id="batchApprovalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden">
        <!-- Backdrop click listener -->
        <div class="fixed inset-0" onclick="closeBatchModal()"></div>

        <!-- Modal Dialog Card -->
        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[88vh] flex flex-col overflow-hidden">
            <!-- Modal Header (Fixed at top) -->
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50/60 via-white to-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-600 text-white flex items-center justify-center font-bold text-base shadow-md shadow-orange-600/20 shrink-0">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">Aksi Approval & Plotting Dosen Massal</h3>
                        <p class="text-xs text-slate-500">Terapkan keputusan persetujuan dan dosen pembimbing ke beberapa mahasiswa sekaligus.</p>
                    </div>
                </div>
                <button type="button" onclick="closeBatchModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Form (Flex Column with scrollable middle area and fixed footer) -->
            <form id="formBatchApproval" onsubmit="submitBatchApproval(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                <!-- Scrollable Body Content -->
                <div class="p-5 sm:p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                    <input type="hidden" id="batchStatusInput" name="status" value="Approved">

                    <!-- 1. Selected Students Summary -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Mahasiswa yang Dipilih:</label>
                            <span id="modalSelectedCountBadge" class="text-[11px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full border border-orange-200">0 Mahasiswa</span>
                        </div>
                        <div id="modalSelectedList" class="max-h-32 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
                        </div>
                    </div>

                    <!-- 2. Decision Selector (Approve / Reject) -->
                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">Keputusan Koordinator TA:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div id="modalOptApprove" onclick="setBatchDecisionStatus('Approved')" class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50 text-emerald-950 font-bold text-xs cursor-pointer shadow-xs">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                                <div>
                                    <p class="leading-none">Setujui (Approve)</p>
                                    <p class="text-[10px] text-emerald-700 font-normal mt-0.5">Lanjut ke Ketua KK + Tetapkan Pembimbing</p>
                                </div>
                            </div>
                            <div id="modalOptReject" onclick="setBatchDecisionStatus('Rejected')" class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-medium text-xs cursor-pointer hover:bg-slate-50 shadow-xs">
                                <i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i>
                                <div>
                                    <p class="leading-none font-bold">Tolak (Minta Revisi)</p>
                                    <p class="text-[10px] text-slate-500 font-normal mt-0.5">Kirim catatan revisi ke mahasiswa</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Section Dosen Pembimbing (Shown when Approve) -->
                    <div id="modalSectionDosen" class="space-y-4 pt-2 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-chalkboard-user text-brand-600"></i> Penetapan Dosen Pembimbing Massal
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Dosen Pembimbing 1 -->
                            <div class="modal-combobox-wrapper relative" data-slot="1">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">Dosen Pembimbing 1 <span class="text-rose-500">*</span></label>
                                <input type="hidden" id="modalInputPembimbing1" name="pembimbing_1" value="">

                                <!-- Chip Preview -->
                                <div id="modalChipP1" class="hidden p-2.5 bg-orange-50 border border-orange-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</div>
                                        <span id="modalChipNameP1" class="text-xs font-bold text-orange-950 truncate"></span>
                                    </div>
                                    <button type="button" onclick="changeModalDosen(1)" class="text-xs text-orange-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="modalSearchContainer1" class="relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="modalSearchP1" onfocus="openModalDosenDropdown(1)" onclick="openModalDosenDropdown(1)" oninput="filterModalDosen(1)" placeholder="Cari nama / NIP pembimbing 1..." class="w-full text-xs bg-transparent border-none focus:outline-none text-slate-800" autocomplete="off">
                                        <button type="button" id="modalClearP1" onclick="clearModalSearch(1)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="modalDropdownList1" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[100] max-h-52 overflow-y-auto custom-scrollbar p-1 divide-y divide-slate-100"></div>
                                </div>
                            </div>

                            <!-- Dosen Pembimbing 2 -->
                            <div class="modal-combobox-wrapper relative" data-slot="2">
                                <label class="text-xs font-bold text-slate-700 block mb-1.5">Dosen Pembimbing 2 <span class="text-rose-500">*</span></label>
                                <input type="hidden" id="modalInputPembimbing2" name="pembimbing_2" value="">

                                <!-- Chip Preview -->
                                <div id="modalChipP2" class="hidden p-2.5 bg-orange-50 border border-orange-300 rounded-xl flex items-center justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</div>
                                        <span id="modalChipNameP2" class="text-xs font-bold text-orange-950 truncate"></span>
                                    </div>
                                    <button type="button" onclick="changeModalDosen(2)" class="text-xs text-orange-600 font-bold hover:underline cursor-pointer ml-2 shrink-0">Ganti</button>
                                </div>

                                <!-- Search Input -->
                                <div id="modalSearchContainer2" class="relative">
                                    <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/20 shadow-2xs">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                        <input type="text" id="modalSearchP2" onfocus="openModalDosenDropdown(2)" onclick="openModalDosenDropdown(2)" oninput="filterModalDosen(2)" placeholder="Cari nama / NIP pembimbing 2..." class="w-full text-xs bg-transparent border-none focus:outline-none text-slate-800" autocomplete="off">
                                        <button type="button" id="modalClearP2" onclick="clearModalSearch(2)" class="hidden text-slate-400 hover:text-slate-600 text-xs ml-1 shrink-0"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </div>
                                    <div id="modalDropdownList2" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-[100] max-h-52 overflow-y-auto custom-scrollbar p-1 divide-y divide-slate-100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Catatan Koordinator TA -->
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Catatan Koordinator TA:</label>
                        <textarea name="catatan_koor" rows="2" placeholder="Masukkan catatan atau arahan untuk seluruh mahasiswa terpilih..." class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 resize-none"></textarea>
                    </div>
                </div>

                <!-- Modal Footer (Fixed at bottom) -->
                <div class="p-4 sm:px-6 border-t border-slate-100 bg-slate-50/90 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="closeBatchModal()" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="modalBtnSubmit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="modalBtnSubmitText">Terapkan Keputusan Massal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Config for External Dashboard Script -->
    <script>
        window.DASHBOARD_CONFIG = {
            list: <?= json_encode($list_mahasiswa ?? []); ?>,
            listPreview2: <?= json_encode($list_preview2 ?? []); ?>,
            dosenList: <?= json_encode($dosen_list ?? []); ?>,
            ruanganList: <?= json_encode($ruangan_list ?? []); ?>,
            ajaxBatchUrl: "<?= site_url('koordinatorta/ajax_batch_approval'); ?>",
            ajaxRealtimeUrl: "<?= site_url('koordinatorta/ajax_realtime_dashboard'); ?>",
            ajaxPreview2UpdateUrl: "<?= site_url('koordinatorta/ajax_update_preview2_penguji'); ?>",
            ajaxPreview2BatchUrl: "<?= site_url('koordinatorta/ajax_batch_preview2_penguji'); ?>",
            ajaxPreview2RealtimeUrl: "<?= site_url('koordinatorta/ajax_realtime_preview2'); ?>",
            detailUrlPrefix: "<?= site_url('koordinatorta/detail_mahasiswa/'); ?>"
        };
    </script>

    <!-- Modular Dashboard JavaScript Engine -->
    <script src="<?= base_url('assets/js/koordinator_ta_dashboard.js?v=' . time()); ?>"></script>
</body>
</html>
