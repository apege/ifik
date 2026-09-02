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

    <!-- Timepicker Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/timepicker.css?v=' . time()); ?>">

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

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
            border-radius: 9999px;
            overflow: hidden;
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

        .btn-3d-kinetic.btn-emerald .bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            box-shadow: 0 4px 14px -2px rgba(16, 185, 129, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.25) inset;
        }

        .btn-3d-kinetic.btn-emerald:hover .bg {
            background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
            box-shadow: 0 6px 20px -2px rgba(16, 185, 129, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
            transform: scale(1.02);
        }

        .btn-3d-kinetic.btn-amber .bg {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            box-shadow: 0 4px 14px -2px rgba(245, 158, 11, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.25) inset;
        }

        .btn-3d-kinetic.btn-amber:hover .bg {
            background: linear-gradient(135deg, #d97706 0%, #b45309 50%, #92400e 100%);
            box-shadow: 0 6px 20px -2px rgba(245, 158, 11, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
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

        /* CUSTOM FLATPICKR CALENDAR MATCHING AJUKAN BOOKING */
        .flatpickr-calendar {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 16px 35px -5px rgba(0, 0, 0, 0.15) !important;
            z-index: 999999 !important;
            background: #ffffff !important;
        }
        .flatpickr-months {
            padding: 4px 0 !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 800 !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #ea580c !important;
            border-color: #ea580c !important;
            color: #ffffff !important;
        }
        .flatpickr-day.today {
            border-color: #ea580c !important;
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

        <!-- Section Tab Switcher (Tahap Pendaftaran TA vs Preview 2 vs Penjadwalan Sidang) -->
        <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
            <div class="inline-flex p-1.5 bg-slate-200/70 backdrop-blur-md rounded-2xl border border-slate-300/60 shadow-inner">
                <button type="button" id="tabBtnPendaftaran" onclick="switchDashboardTab('pendaftaran')" class="dashboard-tab-btn active px-5 py-2.5 rounded-xl font-bold text-xs transition-all duration-300 flex items-center gap-2 bg-white text-orange-600 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                    <span>1. Pendaftaran TA (Plotting Pembimbing)</span>
                </button>
                <button type="button" id="tabBtnPreview2" onclick="switchDashboardTab('preview2')" class="dashboard-tab-btn px-5 py-2.5 rounded-xl font-bold text-xs transition-all duration-300 flex items-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-white/50 cursor-pointer">
                    <i class="fa-solid fa-chalkboard-user text-sm text-indigo-500"></i>
                    <span>2. Tahap Preview 2 (Plotting Penguji)</span>
                </button>
                <button type="button" id="tabBtnSidang" onclick="switchDashboardTab('sidang')" class="dashboard-tab-btn px-5 py-2.5 rounded-xl font-bold text-xs transition-all duration-300 flex items-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-white/50 cursor-pointer">
                    <i class="fa-solid fa-calendar-check text-sm text-amber-500"></i>
                    <span>3. Penjadwalan Sidang TA & Ruangan</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full bg-amber-100 text-amber-800 ml-1">Dinamis</span>
                </button>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 1: PENDAFTARAN TA (PLOTTING PEMBIMBING)               -->
        <!-- ========================================================= -->
        <div id="tabContentPendaftaran" class="space-y-6">

        <?php
            $totalMhs = count($list_mahasiswa ?? []);
            $siapDiplotCount = 0;
            $approvedCount = 0;
            $rejectedCount = 0;
            $kkApprovedCount = 0;

            if(!empty($list_mahasiswa)) {
                foreach($list_mahasiswa as $row) {
                    $stKoor = $row['status_approval_koor'] ?? 'Pending';
                    $stWali = $row['status_approval_wali'] ?? 'Pending';
                    $stAdmin = $row['status_approval_admin'] ?? 'Pending';
                    $stKk = $row['status_approval_kk'] ?? 'Pending';

                    $isWaliApproved = (strcasecmp($stWali, 'Approved') === 0);
                    $isAdminApproved = (strcasecmp($stAdmin, 'Approved') === 0);

                    if (strcasecmp($stKoor, 'Approved') === 0) {
                        $approvedCount++;
                    } else if (strcasecmp($stKoor, 'Rejected') === 0) {
                        $rejectedCount++;
                    } else {
                        if ($isWaliApproved && $isAdminApproved) {
                            $siapDiplotCount++;
                        }
                    }

                    if (strcasecmp($stKk, 'Approved') === 0) {
                        $kkApprovedCount++;
                    }
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

            <!-- 2. Siap Diplot Pembimbing Card (Cyan) -->
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Siap Diplot Pembimbing</p>
                            <h3 id="statPendingCount" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $siapDiplotCount; ?> <span class="text-xs font-semibold text-cyan-600 font-normal">(<?= $totalMhs > 0 ? round(($siapDiplotCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Lolos Wali &amp; Admin Layanan</p>
                        </div>
                        
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-user-plus text-lg"></i>
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Disetujui Koordinator</p>
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

            <!-- 4. Persetujuan Ketua KK Card (Indigo / Amber) -->
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-indigo-600 transition-colors">Persetujuan Ketua KK</p>
                            <h3 id="statKkCount" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $kkApprovedCount; ?> <span class="text-xs font-semibold text-indigo-600 font-normal">(<?= $totalMhs > 0 ? round(($kkApprovedCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Disetujui Final oleh KK</p>
                        </div>
                        
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-indigo-500/20 blur-md group-hover:blur-lg group-hover:bg-indigo-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-indigo-100/70 shadow-md text-indigo-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-user-shield text-lg"></i>
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

                <!-- Tombol Riwayat Histori Pembimbing -->
                <button type="button" onclick="openHistoryPlottingModal('Pembimbing')" class="px-4 py-2 bg-gradient-to-r from-slate-800 to-orange-950 hover:from-slate-700 hover:to-orange-900 text-white font-bold rounded-xl text-xs shadow-md border border-orange-900/50 flex items-center gap-2 transition cursor-pointer self-start lg:self-center active:scale-95">
                    <i class="fa-solid fa-clock-rotate-left text-orange-400"></i>
                    <span>Riwayat Histori Pembimbing</span>
                </button>
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
                    <div id="mainValueContainer" class="flex-1 flex items-center min-w-0">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                        <input type="text" id="mainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handleUnifiedMultiSearch(); }" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
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

                    <!-- Tombol Cari -->
                    <button type="button" onclick="handleUnifiedMultiSearch()" class="px-3.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                    </button>
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
            <div class="table-rotating-border-inner overflow-x-auto no-scrollbar">
                <table class="table-custom-rounded text-left text-xs w-full">
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
        <!-- FLOATING BATCH ACTION BAR (Tab 1: Pendaftaran TA) -->
        <div id="floatingBatchBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 w-full max-w-4xl px-4 animate-in fade-in slide-in-from-bottom-5 duration-200">
            <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                <div class="flex items-center gap-3.5 min-w-0 flex-1">
                    <div class="w-10 h-10 min-w-[2.5rem] min-h-[2.5rem] aspect-square rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-orange-600/30 shrink-0">
                        <span id="selectedCountBadge">0</span>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-white tracking-wide">Mahasiswa Terpilih</h4>
                        <div id="selectedStudentsPreview" class="flex flex-wrap items-center gap-1.5 mt-1"></div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                    <!-- Button 1: Cek Dokumen Massal (Multi-Detail Review) -->
                    <button type="button" onclick="event.stopPropagation(); openP1BatchReviewModal();" class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-extrabold rounded-xl text-xs shadow-md shadow-orange-600/30 transition flex items-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-layer-group text-sm"></i> 📂 Cek Dokumen Massal (Multi-Detail)
                    </button>

                    <!-- Button 2: Quick Batch Plotting -->
                    <button type="button" onclick="event.stopPropagation(); openBatchModal('Approved');" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-bolt text-amber-300"></i> Plot Cepat
                    </button>

                    <!-- Button 3: Cancel -->
                    <button type="button" onclick="event.stopPropagation(); clearAllSelection();" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition cursor-pointer" title="Batal Pilihan">
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
                $totalP2        = count($list_preview2 ?? []);
                $pengujiLengkap = 0; // P1 & P2 sudah ditetapkan
                $belumPenguji   = 0; // Belum ada penguji sama sekali

                if (!empty($list_preview2)) {
                    foreach ($list_preview2 as $r2) {
                        $hasP1 = !empty($r2['penguji_1']);
                        $hasP2 = !empty($r2['penguji_2']);
                        if ($hasP1 && $hasP2) {
                            $pengujiLengkap++;
                        } else {
                            $belumPenguji++;
                        }
                    }
                }

                $pctLengkap = $totalP2 > 0 ? round(($pengujiLengkap / $totalP2) * 100) : 0;
                $pctBelum   = $totalP2 > 0 ? round(($belumPenguji   / $totalP2) * 100) : 0;
            ?>

            <!-- Stats Overview Cards (Tab 2 — Penguji & Jadwal Preview 2) -->
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
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-indigo-600 transition-colors">Total Mahasiswa Siap Plot</p>
                                <h3 id="statP2Total" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $totalP2; ?></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Masuk antrian plotting penguji</p>
                            </div>

                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-indigo-500/20 blur-md group-hover:blur-lg group-hover:bg-indigo-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-indigo-100/70 shadow-md text-indigo-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-users text-lg"></i>
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

                <!-- 2. Penguji Lengkap Card (Emerald) -->
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
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Penguji Lengkap</p>
                                <h3 id="statP2Terjadwal" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $pengujiLengkap; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $pctLengkap; ?>%)</span></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Penguji 1 &amp; 2 sudah ditetapkan ✓</p>
                            </div>

                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-circle-check text-lg"></i>
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

                <!-- 3. Belum Diplot Penguji Card (Rose) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-rose-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-rose-500/40 hover:shadow-2xl hover:shadow-rose-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-rose-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-rose-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-rose-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-rose-600 transition-colors">Belum Diplot Penguji</p>
                                <h3 id="statP2Belum" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $belumPenguji; ?> <span class="text-xs font-semibold text-rose-600 font-normal">(<?= $pctBelum; ?>%)</span></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu segera ditetapkan penguji</p>
                            </div>

                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-rose-500/20 blur-md group-hover:blur-lg group-hover:bg-rose-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-rose-200/80 bg-gradient-to-br from-rose-50 to-rose-100/70 shadow-md text-rose-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-user-xmark text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-rose-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Progress Plotting Card (Violet) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-violet-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-violet-500/40 hover:shadow-2xl hover:shadow-violet-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-violet-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-violet-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-violet-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-violet-600 transition-colors">Progress Plotting</p>
                                <h3 id="statP2ProgressPct" class="text-2xl font-black text-violet-700 mt-1 tracking-tight"><?= $pctLengkap; ?>%</h3>
                                <p id="statP2ProgressSub" class="text-xs font-medium text-slate-500 mt-1 line-clamp-1"><?= $pengujiLengkap; ?> dari <?= $totalP2; ?> sudah diplot</p>
                            </div>

                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-violet-500/20 blur-md group-hover:blur-lg group-hover:bg-violet-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-violet-200/80 bg-gradient-to-br from-violet-50 to-violet-100/70 shadow-md text-violet-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-chart-pie text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 mt-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div id="statP2ProgressBar" class="h-1.5 rounded-full bg-gradient-to-r from-violet-500 to-violet-400 transition-all duration-700"
                                         style="width: <?= $pctLengkap; ?>%"></div>
                                </div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300 shrink-0">
                                    <div class="w-1.5 h-1.5 bg-violet-500 rounded-full animate-bounce"></div>
                                    <div class="w-1.5 h-1.5 bg-violet-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1.5 h-1.5 bg-violet-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
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

                    <!-- Tombol Riwayat Histori Penguji -->
                    <button type="button" onclick="openHistoryPengujiModal()" class="px-4 py-2 bg-gradient-to-r from-slate-800 to-indigo-950 hover:from-slate-700 hover:to-indigo-900 text-white font-bold rounded-xl text-xs shadow-md border border-indigo-900/50 flex items-center gap-2 transition cursor-pointer self-start lg:self-center active:scale-95">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-400"></i>
                        <span>Riwayat Histori Penguji</span>
                    </button>
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
                        <div id="p2MainValueContainer" class="flex-1 flex items-center min-w-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" id="p2MainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handleUnifiedMultiSearchP2(); }" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
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

                        <!-- Tombol Cari Preview 2 -->
                        <button type="button" onclick="handleUnifiedMultiSearchP2()" class="px-3.5 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                        </button>
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
                                <th class="w-28 py-3.5 px-2 text-center">Status</th>
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
            <!-- FLOATING BATCH ACTION BAR (Tab 2: Preview 2) -->
            <div id="floatingP2BatchBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 w-full max-w-4xl px-4 animate-in fade-in slide-in-from-bottom-5 duration-200">
                <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 shadow-2xl rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-10 h-10 min-w-[2.5rem] min-h-[2.5rem] aspect-square rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-indigo-600/30 shrink-0">
                            <span id="p2SelectedCountBadge">0</span>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-white tracking-wide">Mahasiswa Terpilih untuk Penetapan Penguji Massal</h4>
                            <div id="p2SelectedStudentsPreview" class="flex flex-wrap items-center gap-1.5 mt-1"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" onclick="event.stopPropagation(); openP2BatchModal();" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-xs shadow-md shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-users-gear"></i> Plot Penguji Massal
                        </button>
                        <button type="button" onclick="event.stopPropagation(); clearAllP2Selection();" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition cursor-pointer" title="Batal Pilihan">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </button>
                    </div>
                </div>
            </div>

        </div> <!-- /#tabContentPreview2 -->

        <!-- ========================================================= -->
        <!-- TAB 3: PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN DINAMIS -->
        <!-- ========================================================= -->
        <div id="tabContentSidang" class="hidden space-y-6">

            <?php
                $totalSidang = count($list_sidang ?? []);
                $terjadwalSidang = 0;
                $belumSetSidang = 0;

                if(!empty($list_sidang)) {
                    foreach($list_sidang as $rs) {
                        $stSd = $rs['status_sidang'] ?? 'Belum Dijadwalkan';
                        if($stSd === 'Terjadwal') $terjadwalSidang++;
                        else $belumSetSidang++;
                    }
                }
                $totalRuangan = count($ruangan_list ?? []);
            ?>

            <!-- Stats Overview Cards (Tahap Sidang) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- 1. Total Mahasiswa Sidang (Amber) -->
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
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Total Mahasiswa Sidang</p>
                                <h3 id="statSidangTotal" class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $totalSidang; ?></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Siap Dijadwalkan Sidang</p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-graduation-cap text-lg"></i>
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

                <!-- 2. Terjadwal Sidang (Emerald) -->
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
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Sudah Terjadwal</p>
                                <h3 id="statSidangTerjadwal" class="text-2xl font-black text-emerald-600 mt-1 tracking-tight"><?= $terjadwalSidang; ?></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Waktu & Ruangan Lengkap</p>
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

                <!-- 3. Belum Dijadwalkan (Rose/Amber) -->
                <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-rose-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-rose-500/40 hover:shadow-2xl hover:shadow-rose-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-rose-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-rose-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-rose-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-rose-600 transition-colors">Belum Dijadwalkan</p>
                                <h3 id="statSidangBelumSet" class="text-2xl font-black text-rose-600 mt-1 tracking-tight"><?= $belumSetSidang; ?></h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu Penentuan Jadwal</p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-rose-500/20 blur-md group-hover:blur-lg group-hover:bg-rose-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-rose-200/80 bg-gradient-to-br from-rose-50 to-rose-100/70 shadow-md text-rose-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-rose-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Ruangan Sidang Aktif Dinamis (Cyan/Teal) -->
                <div onclick="openModalKelolaRuangan()" class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                    <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                            <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                        </div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Ruangan Sidang Aktif</p>
                                <h3 id="statSidangRuanganCount" class="text-2xl font-black text-cyan-700 mt-1 tracking-tight"><?= $totalRuangan; ?></h3>
                                <p class="text-xs font-medium text-cyan-600 mt-1 flex items-center gap-1 font-semibold">
                                    <i class="fa-solid fa-sliders text-[10px]"></i> Kelola Ruangan
                                </p>
                            </div>
                            
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                                <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                    <i class="fa-solid fa-door-open text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                            <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                            <span class="text-[10px] font-bold text-cyan-600 group-hover:underline">Tambah / Hapus →</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Toolbar & Filters (Exact Card Container from Tab 1 & Tab 2) -->
            <div class="card-custom p-5 mb-8 space-y-4">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                            <i class="fa-solid fa-calendar-check text-amber-500 text-lg"></i> Daftar Pendaftaran &amp; Penjadwalan Sidang TA
                        </h2>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Kelola tanggal sidang, rentang waktu, dan alokasi ruangan sidang mahasiswa secara dinamis.</p>
                    </div>
                    
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" onclick="openModalKelolaRuangan()" class="px-4 py-2.5 bg-cyan-50 hover:bg-cyan-100 text-cyan-800 border border-cyan-200 font-bold rounded-xl text-xs shadow-2xs transition inline-flex items-center gap-2 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-door-open text-cyan-600"></i>
                            <span>Kelola Ruangan Sidang</span>
                        </button>
                    </div>
                </div>

                <!-- Row 1: Unified Multi-Search Bar for Sidang -->
                <div class="relative search-pill-container" id="sidangMultiSearchWrapper">
                    <div class="unified-search-pill">
                        <!-- Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" id="sidangMainCategorySelect" value="query">
                            <button type="button" onclick="toggleCustomDropdown('sidang-main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-amber-600 focus:outline-none">
                                <span id="label-filter-sidang-main-cat" class="truncate max-w-[130px]">Cari Kata Kunci</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-sidang-main-cat"></i>
                            </button>
                            <div id="menu-filter-sidang-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                <div onclick="selectSidangMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-amber-50 text-amber-600"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectSidangMainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectSidangMainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectSidangMainCategory('judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>📖 Judul Tugas Akhir</span></div>
                                <div onclick="selectSidangMainCategory('pembimbing', '👔 Dosen Pembimbing', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>👔 Dosen Pembimbing</span></div>
                                <div onclick="selectSidangMainCategory('penguji', '👨‍🏫 Dosen Penguji', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>👨‍🏫 Dosen Penguji</span></div>
                                <div onclick="selectSidangMainCategory('ruangan', '🏛️ Ruangan Sidang', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>🏛️ Ruangan Sidang</span></div>
                                <div onclick="selectSidangMainCategory('status', '⚡ Status Sidang', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-600"><span>⚡ Status Sidang</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Text Value Container -->
                        <div id="sidangMainValueContainer" class="flex-1 flex items-center min-w-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" id="sidangMainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handleUnifiedMultiSearchSidang(); }" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                        </div>

                        <!-- Main Custom Select Dropdown Container -->
                        <div id="sidangMainCustomSelectWrap" class="hidden flex-1 relative custom-dropdown-container">
                            <input type="hidden" id="sidangMainCustomSelectVal" value="">
                            <button type="button" onclick="toggleCustomDropdown('sidang-main-select', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                                <span id="label-filter-sidang-main-select" class="flex items-center gap-1.5 truncate">Semua Status</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-sidang-main-select"></i>
                            </button>
                            <div id="menu-filter-sidang-main-select" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            </div>
                        </div>

                        <!-- Tombol Cari Sidang -->
                        <button type="button" onclick="handleUnifiedMultiSearchSidang()" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnSidang" onclick="toggleOrAddFilterRowSidang(event)" class="btn-standalone-add hover:border-amber-500 hover:text-amber-600" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgeSidang" class="badge-standalone-count bg-amber-600">1/4</span>
                    </button>

                    <!-- Extra Filter Rows Card Popover -->
                    <div id="extraRowsCardSidang" class="extra-rows-card space-y-2.5">
                        <div id="additionalFilterRowsContainerSidang" class="space-y-2.5">
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 mt-2 text-xs">
                            <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian jadwal sidang.</span>
                            <button type="button" onclick="resetSidangMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors cursor-pointer">
                                Reset All Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Page Size & Records Count -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="text-xs text-slate-500 font-medium">
                        <span>Kelola jadwal dan ruangan sidang tugas akhir mahasiswa secara terstruktur.</span>
                    </div>

                    <!-- Page Size & Counter Right -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-9 rounded-xl shadow-2xs">
                            <span class="font-medium">Tampilkan</span>
                            <select id="sidangPageSizeSelect" onchange="changeSidangPageSize(this.value)" class="h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500 cursor-pointer">
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="font-medium">data/hal</span>
                            <span class="text-slate-300">|</span>
                            <span>Total: <strong class="total-rows-count text-slate-900 font-bold" id="sidangToolbarTotalCount"><?= $totalSidang; ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Table with Rotating Conic-Gradient Border (Exact Tab 1 & Tab 2 Style) -->
            <div class="table-rotating-border-wrap">
                <span class="table-rotating-border-spin"></span>
                <div class="table-rotating-border-inner overflow-hidden">
                    <table class="table-custom-rounded text-left text-xs w-full">
                        <thead class="bg-white text-slate-700 font-semibold text-xs border-b border-slate-200/90">
                            <tr>
                                <th class="w-8 py-3.5 px-3 text-center">
                                    <input type="checkbox" id="selectAllCheckboxSidang" onchange="toggleSelectAllSidang(this)" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300 cursor-pointer" title="Pilih Semua di Halaman Ini">
                                </th>
                                <th class="w-24 py-3.5 px-2 font-bold">NIM</th>
                                <th class="w-36 py-3.5 px-2 font-semibold">Nama Mahasiswa</th>
                                <th class="py-3.5 px-2">Usulan Judul TA</th>
                                <th class="w-36 py-3.5 px-2">Dosen Pembimbing</th>
                                <th class="w-36 py-3.5 px-2">Dosen Penguji</th>
                                <th class="w-32 py-3.5 px-2">Waktu Sidang</th>
                                <th class="w-28 py-3.5 px-2">Ruangan</th>
                                <th class="w-28 py-3.5 px-2 text-center">Status</th>
                                <th class="w-32 py-3.5 px-3 pr-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodySidang">
                            <!-- Injected via JS renderSidangTable() -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Bottom Pagination Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 text-xs text-slate-500 font-medium">
                <div>
                    Menampilkan data <strong id="sidangPageStart" class="text-slate-800 font-bold">1</strong> - <strong id="sidangPageEnd" class="text-slate-800 font-bold">10</strong> dari total <strong id="sidangTotalRecords" class="text-slate-800 font-bold"><?= $totalSidang; ?></strong> mahasiswa
                </div>
                <div class="pagination-controls-bottom flex items-center gap-1" id="sidangPaginationNav">
                    <!-- Dynamic pagination buttons -->
                </div>
            </div>

            <!-- Floating Action Bar for Sidang Multi-Selection -->
            <div id="floatingSidangBatchBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white backdrop-blur-md px-6 py-3.5 rounded-2xl shadow-2xl border border-slate-700/60 flex items-center gap-4 animate-in fade-in slide-in-from-bottom-5 duration-200">
                <div class="flex items-center gap-2">
                    <span id="floatingSidangCount" class="w-6 h-6 rounded-full bg-amber-500 text-white font-black text-xs flex items-center justify-center">0</span>
                    <span class="text-xs font-bold text-slate-200">Mahasiswa Terpilih</span>
                </div>
                <div class="h-4 w-px bg-slate-700"></div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openModalBatchSidang()" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-amber-500/20 transition cursor-pointer flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Jadwalkan Massal (<span id="floatingSidangBatchCountText">0</span>)</span>
                    </button>
                    <button type="button" onclick="clearAllSidangSelection()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-semibold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                </div>
            </div>

        </div> <!-- /#tabContentSidang -->

    </main>

    <!-- ========================================================= -->
    <!-- MODAL 1: MANAJEMEN RUANGAN SIDANG DINAMIS                 -->
    <!-- ========================================================= -->
    <div id="modalKelolaRuangan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeModalKelolaRuangan()"></div>

        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-cyan-50/80 via-white to-white shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-600 text-white flex items-center justify-center font-bold text-base shadow-md shadow-cyan-600/20 shrink-0">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">Manajemen Ruangan Sidang Dinamis</h3>
                        <p class="text-xs text-slate-500">Tambah ruangan baru atau hapus ruangan yang sudah tidak digunakan untuk sidang.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalKelolaRuangan()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 sm:p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                <!-- Form Tambah Ruangan Baru -->
                <div class="p-4 sm:p-5 bg-gradient-to-br from-cyan-50/50 to-slate-50 border border-cyan-200/70 rounded-2xl space-y-3.5">
                    <h4 class="text-xs font-bold text-cyan-950 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle text-cyan-600"></i> Tambah Ruangan Sidang Baru
                    </h4>
                    
                    <form id="formTambahRuangan" onsubmit="submitTambahRuangan(event)" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-slate-700">Kode Ruangan <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode_ruangan" id="inputKodeRuangan" placeholder="Contoh: IK.02.04" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none uppercase shadow-2xs">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-slate-700">Nama Ruangan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_ruangan" id="inputNamaRuangan" placeholder="Contoh: Ruang Sidang 3 FIK" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none shadow-2xs">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-slate-700">Lokasi / Gedung</label>
                            <input type="text" name="lokasi" id="inputLokasiRuangan" placeholder="Gedung FIK Lantai 2" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none shadow-2xs">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" id="btnSubmitRuangan" class="w-full py-2.5 bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-500 hover:to-teal-500 text-white font-bold rounded-xl text-xs shadow-md shadow-cyan-600/20 transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-plus text-xs"></i> Simpan Ruangan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Table Daftar Ruangan Aktif -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Ruangan Sidang Terdaftar:</label>
                        <span id="badgeTotalRuanganModal" class="text-[11px] font-bold text-cyan-700 bg-cyan-50 border border-cyan-200 px-2.5 py-0.5 rounded-full">0 Ruangan</span>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3.5">Kode</th>
                                    <th class="py-2.5 px-3.5">Nama Ruangan</th>
                                    <th class="py-2.5 px-3.5">Lokasi</th>
                                    <th class="py-2.5 px-3.5 text-center">Status</th>
                                    <th class="py-2.5 px-3.5 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRuanganList" class="divide-y divide-slate-100">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-100 flex items-center justify-end bg-slate-50/50">
                <button type="button" onclick="closeModalKelolaRuangan()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL 2: SET JADWAL SIDANG SINGLE MAHASISWA               -->
    <!-- ========================================================= -->
    <div id="modalSingleSidang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeModalSingleSidang()"></div>

        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-amber-50/80 via-white to-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-amber-500/20 shrink-0">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Penjadwalan Sidang Tugas Akhir</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5" id="singleSidangStudentInfo">Atur tanggal, ruangan, dan jam pelaksanaan sidang.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalSingleSidang()" class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="formSingleSidang" onsubmit="submitSingleSidang(event)" class="p-6 sm:p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                <input type="hidden" name="nim" id="singleSidangNim">

                <!-- 1. TANGGAL SIDANG -->
                <div>
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-2">
                        Tanggal Sidang <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="tgl_sidang" id="singleSidangTgl" required placeholder="Pilih Tanggal Sidang..." class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border border-slate-300 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs cursor-pointer transition">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-500 pointer-events-none">
                            <i class="fa-solid fa-calendar-day text-base"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. RUANGAN SIDANG (SEARCH AUTOCOMPLETE & DYNAMIC INPUT) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                            Ruangan Sidang <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="openModalKelolaRuangan()" class="text-xs text-cyan-600 hover:text-cyan-700 hover:underline font-bold flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus-circle text-[11px]"></i> Kelola Ruangan
                        </button>
                    </div>
                    <div class="relative custom-combobox-wrap" id="singleRuanganCombobox">
                        <input type="text" 
                               id="singleSidangRuanganInput" 
                               placeholder="Cari ruangan atau ketik nama ruangan baru..." 
                               autocomplete="off"
                               class="w-full pl-11 pr-11 py-3.5 bg-slate-50/70 border border-slate-300 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs transition cursor-pointer" 
                               oninput="filterRuanganDropdown('single', this.value)" 
                               onfocus="openRuanganDropdown('single')"
                               onclick="openRuanganDropdown('single')">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-cyan-600 pointer-events-none">
                            <i class="fa-solid fa-door-open text-base"></i>
                        </div>
                        <button type="button" onclick="toggleRuanganDropdown('single')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <i class="fa-solid fa-chevron-down text-sm transition duration-200" id="singleRuanganArrow"></i>
                        </button>
                        <input type="hidden" name="ruangan_sidang" id="singleSidangRuangan" required>

                        <!-- Dropdown Menu List -->
                        <div id="singleRuanganDropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 max-h-52 overflow-y-auto divide-y divide-slate-100 text-xs custom-scrollbar">
                            <!-- Injected dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- 3. WAKTU SIDANG (EXACT INTERACTIVE RADIAL CLOCK PICKER) -->
                <div>
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-2">
                        Waktu Sidang <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 mb-1.5 block uppercase tracking-wider">Jam Mulai</label>
                            <input type="text" name="jam_mulai_sidang" id="singleSidangJamMulai"
                                   placeholder="-- : --" readonly style="cursor: pointer; background: #fff;"
                                   class="w-full px-4 py-3.5 bg-white border-2 border-slate-200 hover:border-amber-400 focus:border-amber-500 rounded-2xl text-base font-extrabold text-slate-800 text-center focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition"
                                   onclick="openSidangInlinePicker('single', 'mulai')" required>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 mb-1.5 block uppercase tracking-wider">Jam Selesai</label>
                            <input type="text" name="jam_selesai_sidang" id="singleSidangJamSelesai"
                                   placeholder="-- : --" readonly style="cursor: pointer; background: #fff;"
                                   class="w-full px-4 py-3.5 bg-white border-2 border-slate-200 hover:border-amber-400 focus:border-amber-500 rounded-2xl text-base font-extrabold text-slate-800 text-center focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition"
                                   onclick="openSidangInlinePicker('single', 'selesai')">
                        </div>
                    </div>

                    <!-- Inline Radial Clock Picker Panel (Single) -->
                    <div id="singleInlineClockPanel" style="display:none; margin-top: 18px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 22px; box-shadow: 0 12px 30px rgba(0,0,0,0.04);">
                        <div style="display: flex; gap: 20px; align-items: stretch; flex-wrap: wrap;">
                            <!-- Kiri: Display Waktu & Quick Drag Slots -->
                            <div style="flex: 1.15; min-width: 280px; background: #ffffff; border-radius: 16px; padding: 22px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <div id="singleInlineTpLabel" style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;">PILIH JAM MULAI</div>
                                <div style="font-size: 3rem; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 8px; letter-spacing: -0.02em;">
                                    <span id="singleTpDisplayHour" onclick="setSidangClockMode('single', 'hour')" style="cursor:pointer;">14</span><span style="color:#cbd5e1; margin:0 3px;">:</span><span id="singleTpDisplayMinute" onclick="setSidangClockMode('single', 'minute')" style="cursor:pointer; color:#94a3b8;">00</span>
                                </div>
                                <div style="display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.75rem; font-weight:700; border-radius:20px; padding:3px 12px; margin-bottom:16px;">24 Jam</div>

                                <div style="font-size: 0.8rem; color: #7c3aed; font-weight: 700; margin-bottom: 12px; width:100%; display:flex; justify-content:space-between; align-items:center;">
                                    <span>⚡ Slot Waktu Cepat</span>
                                    <span style="font-size:0.7rem; color:#94a3b8; font-weight:500;">(drag untuk rentang)</span>
                                </div>
                                <div id="singleTpTimeSlots" style="display:grid; grid-template-columns:1fr 1fr; gap:7px; user-select:none; width:100%;">
                                    <div class="tp-slot" data-start="08:00" data-end="09:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">08:00 – 09:00</div>
                                    <div class="tp-slot" data-start="09:00" data-end="10:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">09:00 – 10:00</div>
                                    <div class="tp-slot" data-start="10:00" data-end="11:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">10:00 – 11:00</div>
                                    <div class="tp-slot" data-start="11:00" data-end="12:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">11:00 – 12:00</div>
                                    <div class="tp-slot" data-start="12:00" data-end="13:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">12:00 – 13:00</div>
                                    <div class="tp-slot" data-start="13:00" data-end="14:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">13:00 – 14:00</div>
                                    <div class="tp-slot" data-start="14:00" data-end="15:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">14:00 – 15:00</div>
                                    <div class="tp-slot" data-start="15:00" data-end="16:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">15:00 – 16:00</div>
                                    <div class="tp-slot" data-start="16:00" data-end="17:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">16:00 – 17:00</div>
                                    <div class="tp-slot" data-start="17:00" data-end="18:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">17:00 – 18:00</div>
                                </div>
                            </div>

                            <!-- Kanan: Radial Analog Clock -->
                            <div style="flex: 1.25; min-width: 280px; display:flex; flex-direction:column; align-items:center; background:#ffffff; border-radius:16px; padding:22px; border:1px solid #f1f5f9; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <div class="tp-tab-wrap" style="display:flex; width:100%; border-radius:12px; background:#f1f5f9; padding:4px; margin-bottom:16px;">
                                    <div id="singleTpTabHour" class="active" onclick="setSidangClockMode('single', 'hour')" style="flex:1; text-align:center; padding:8px; font-size:0.82rem; font-weight:700; cursor:pointer; border-radius:10px;">🕐 Jam</div>
                                    <div id="singleTpTabMinute" onclick="setSidangClockMode('single', 'minute')" style="flex:1; text-align:center; padding:8px; font-size:0.82rem; font-weight:700; cursor:pointer; border-radius:10px;">⏱ Menit</div>
                                </div>
                                <div id="singleTpClockContainer" style="position:relative; width:240px; height:240px; border-radius:50%; background:#f8fafc; border:2px solid #e2e8f0; box-shadow:inset 0 2px 6px rgba(0,0,0,0.03); flex-shrink:0; margin:0 auto;">
                                    <div id="singleTpClockHand" style="position:absolute; bottom:50%; left:50%; width:2px; height:95px; background:#7c3aed; border-radius:2px; transform-origin:bottom center; transform:translateX(-50%) rotate(0deg); transition:transform 0.15s ease; z-index:5;"></div>
                                    <div style="position:absolute; top:50%; left:50%; width:10px; height:10px; background:#7c3aed; border-radius:50%; transform:translate(-50%,-50%); z-index:10;"></div>
                                    <div id="singleTpClockNumbers"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:18px; padding-top:16px; border-top:1px solid #e2e8f0;">
                            <button type="button" onclick="closeSidangInlinePicker('single')" style="padding:10px 22px; border-radius:12px; border:1.5px solid #e2e8f0; background:#fff; color:#64748b; font-size:0.85rem; font-weight:700; cursor:pointer;">Batal</button>
                            <button type="button" onclick="applySidangInlinePicker('single')" style="padding:10px 26px; border-radius:12px; border:none; background:#7c3aed; color:#fff; font-size:0.85rem; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(124,58,237,0.3);">✔ Terapkan</button>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModalSingleSidang()" class="px-5 py-3 bg-white border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm rounded-2xl hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitSingleSidang" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-md shadow-amber-500/20 transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-save text-xs sm:text-sm"></i> Simpan Jadwal Sidang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL 3: BATCH PENJADWALAN SIDANG MASSAL                  -->
    <!-- ========================================================= -->
    <div id="modalBatchSidang" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeModalBatchSidang()"></div>

        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-amber-50/80 via-white to-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-amber-500/20 shrink-0">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Jadwalkan Sidang Massal</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Terapkan tanggal, ruangan, dan jam sidang ke seluruh mahasiswa terpilih.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalBatchSidang()" class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="formBatchSidang" onsubmit="submitBatchSidang(event)" class="p-6 sm:p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                <!-- Selected Students List -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                            Mahasiswa Terpilih
                        </label>
                        <span id="badgeBatchSidangCount" class="text-xs font-extrabold text-amber-800 bg-amber-100/80 border border-amber-300 px-3 py-1 rounded-full">0 Mahasiswa</span>
                    </div>
                    <div id="batchSidangSelectedList" class="max-h-36 overflow-y-auto space-y-1.5 border border-slate-200 p-3 rounded-2xl bg-slate-50/60 text-xs custom-scrollbar">
                        <!-- Populated via JS -->
                    </div>
                </div>

                <!-- 1. TANGGAL SIDANG -->
                <div>
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-2">
                        Tanggal Sidang <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="tgl_sidang" id="batchSidangTgl" required placeholder="Pilih Tanggal Sidang..." class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border border-slate-300 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs cursor-pointer transition">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-500 pointer-events-none">
                            <i class="fa-solid fa-calendar-day text-base"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. RUANGAN SIDANG (SEARCH AUTOCOMPLETE & DYNAMIC INPUT) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                            Ruangan Sidang <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="openModalKelolaRuangan()" class="text-xs text-cyan-600 hover:text-cyan-700 hover:underline font-bold flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus-circle text-[11px]"></i> Kelola Ruangan
                        </button>
                    </div>
                    <div class="relative custom-combobox-wrap" id="batchRuanganCombobox">
                        <input type="text" 
                               id="batchSidangRuanganInput" 
                               placeholder="Cari ruangan atau ketik nama ruangan baru..." 
                               autocomplete="off"
                               class="w-full pl-11 pr-11 py-3.5 bg-slate-50/70 border border-slate-300 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs transition cursor-pointer" 
                               oninput="filterRuanganDropdown('batch', this.value)" 
                               onfocus="openRuanganDropdown('batch')"
                               onclick="openRuanganDropdown('batch')">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-cyan-600 pointer-events-none">
                            <i class="fa-solid fa-door-open text-base"></i>
                        </div>
                        <button type="button" onclick="toggleRuanganDropdown('batch')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <i class="fa-solid fa-chevron-down text-sm transition duration-200" id="batchRuanganArrow"></i>
                        </button>
                        <input type="hidden" name="ruangan_sidang" id="batchSidangRuangan" required>

                        <!-- Dropdown Menu List -->
                        <div id="batchRuanganDropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 max-h-52 overflow-y-auto divide-y divide-slate-100 text-xs custom-scrollbar">
                            <!-- Injected dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- 3. WAKTU SIDANG (EXACT INTERACTIVE RADIAL CLOCK PICKER BATCH) -->
                <div>
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-2">
                        Waktu Sidang <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 mb-1.5 block uppercase tracking-wider">Jam Mulai</label>
                            <input type="text" name="jam_mulai_sidang" id="batchSidangJamMulai"
                                   placeholder="-- : --" readonly style="cursor: pointer; background: #fff;"
                                   class="w-full px-4 py-3.5 bg-white border-2 border-slate-200 hover:border-amber-400 focus:border-amber-500 rounded-2xl text-base font-extrabold text-slate-800 text-center focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition"
                                   onclick="openSidangInlinePicker('batch', 'mulai')" required>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 mb-1.5 block uppercase tracking-wider">Jam Selesai</label>
                            <input type="text" name="jam_selesai_sidang" id="batchSidangJamSelesai"
                                   placeholder="-- : --" readonly style="cursor: pointer; background: #fff;"
                                   class="w-full px-4 py-3.5 bg-white border-2 border-slate-200 hover:border-amber-400 focus:border-amber-500 rounded-2xl text-base font-extrabold text-slate-800 text-center focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition"
                                   onclick="openSidangInlinePicker('batch', 'selesai')">
                        </div>
                    </div>

                    <!-- Inline Radial Clock Picker Panel (Batch) -->
                    <div id="batchInlineClockPanel" style="display:none; margin-top: 18px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 22px; box-shadow: 0 12px 30px rgba(0,0,0,0.04);">
                        <div style="display: flex; gap: 20px; align-items: stretch; flex-wrap: wrap;">
                            <!-- Kiri: Display Waktu & Quick Drag Slots -->
                            <div style="flex: 1.15; min-width: 280px; background: #ffffff; border-radius: 16px; padding: 22px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <div id="batchInlineTpLabel" style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;">PILIH JAM MULAI</div>
                                <div style="font-size: 3rem; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 8px; letter-spacing: -0.02em;">
                                    <span id="batchTpDisplayHour" onclick="setSidangClockMode('batch', 'hour')" style="cursor:pointer;">14</span><span style="color:#cbd5e1; margin:0 3px;">:</span><span id="batchTpDisplayMinute" onclick="setSidangClockMode('batch', 'minute')" style="cursor:pointer; color:#94a3b8;">00</span>
                                </div>
                                <div style="display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.75rem; font-weight:700; border-radius:20px; padding:3px 12px; margin-bottom:16px;">24 Jam</div>

                                <div style="font-size: 0.8rem; color: #7c3aed; font-weight: 700; margin-bottom: 12px; width:100%; display:flex; justify-content:space-between; align-items:center;">
                                    <span>⚡ Slot Waktu Cepat</span>
                                    <span style="font-size:0.7rem; color:#94a3b8; font-weight:500;">(drag untuk rentang)</span>
                                </div>
                                <div id="batchTpTimeSlots" style="display:grid; grid-template-columns:1fr 1fr; gap:7px; user-select:none; width:100%;">
                                    <div class="tp-slot" data-start="08:00" data-end="09:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">08:00 – 09:00</div>
                                    <div class="tp-slot" data-start="09:00" data-end="10:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">09:00 – 10:00</div>
                                    <div class="tp-slot" data-start="10:00" data-end="11:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">10:00 – 11:00</div>
                                    <div class="tp-slot" data-start="11:00" data-end="12:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">11:00 – 12:00</div>
                                    <div class="tp-slot" data-start="12:00" data-end="13:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">12:00 – 13:00</div>
                                    <div class="tp-slot" data-start="13:00" data-end="14:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">13:00 – 14:00</div>
                                    <div class="tp-slot" data-start="14:00" data-end="15:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">14:00 – 15:00</div>
                                    <div class="tp-slot" data-start="15:00" data-end="16:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">15:00 – 16:00</div>
                                    <div class="tp-slot" data-start="16:00" data-end="17:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">16:00 – 17:00</div>
                                    <div class="tp-slot" data-start="17:00" data-end="18:00" style="padding:10px 8px;border:1.5px solid #e2e8f0;border-radius:12px;background:#fff;font-size:0.75rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;">17:00 – 18:00</div>
                                </div>
                            </div>

                            <!-- Kanan: Radial Analog Clock -->
                            <div style="flex: 1.25; min-width: 280px; display:flex; flex-direction:column; align-items:center; background:#ffffff; border-radius:16px; padding:22px; border:1px solid #f1f5f9; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <div class="tp-tab-wrap" style="display:flex; width:100%; border-radius:12px; background:#f1f5f9; padding:4px; margin-bottom:16px;">
                                    <div id="batchTpTabHour" class="active" onclick="setSidangClockMode('batch', 'hour')" style="flex:1; text-align:center; padding:8px; font-size:0.82rem; font-weight:700; cursor:pointer; border-radius:10px;">🕐 Jam</div>
                                    <div id="batchTpTabMinute" onclick="setSidangClockMode('batch', 'minute')" style="flex:1; text-align:center; padding:8px; font-size:0.82rem; font-weight:700; cursor:pointer; border-radius:10px;">⏱ Menit</div>
                                </div>
                                <div id="batchTpClockContainer" style="position:relative; width:240px; height:240px; border-radius:50%; background:#f8fafc; border:2px solid #e2e8f0; box-shadow:inset 0 2px 6px rgba(0,0,0,0.03); flex-shrink:0; margin:0 auto;">
                                    <div id="batchTpClockHand" style="position:absolute; bottom:50%; left:50%; width:2px; height:95px; background:#7c3aed; border-radius:2px; transform-origin:bottom center; transform:translateX(-50%) rotate(0deg); transition:transform 0.15s ease; z-index:5;"></div>
                                    <div style="position:absolute; top:50%; left:50%; width:10px; height:10px; background:#7c3aed; border-radius:50%; transform:translate(-50%,-50%); z-index:10;"></div>
                                    <div id="batchTpClockNumbers"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:18px; padding-top:16px; border-top:1px solid #e2e8f0;">
                            <button type="button" onclick="closeSidangInlinePicker('batch')" style="padding:10px 22px; border-radius:12px; border:1.5px solid #e2e8f0; background:#fff; color:#64748b; font-size:0.85rem; font-weight:700; cursor:pointer;">Batal</button>
                            <button type="button" onclick="applySidangInlinePicker('batch')" style="padding:10px 26px; border-radius:12px; border:none; background:#7c3aed; color:#fff; font-size:0.85rem; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(124,58,237,0.3);">✔ Terapkan</button>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModalBatchSidang()" class="px-5 py-3 bg-white border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm rounded-2xl hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitBatchSidang" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-md shadow-amber-500/20 transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-save text-xs sm:text-sm"></i> Terapkan Jadwal Massal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PREVIEW 2 PLOTTING MODAL (PER-MAHASISWA PLOTTING SAMA SEPERTI TA) -->
    <div id="modalPreview2Plotting" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden" onclick="if(event.target===this)closeP2Modal()">

        <!-- Modal Dialog Card -->
        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-3xl sm:max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header (Fixed at top) -->
            <div class="p-4 sm:p-5 px-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-indigo-50/70 via-white to-white shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white flex items-center justify-center font-extrabold text-base shadow-md shadow-indigo-600/25 shrink-0">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-snug">Aksi Plotting Dosen Penguji per Mahasiswa</h3>
                        <p class="text-xs text-slate-500">Tentukan Dosen Penguji 1 &amp; 2 untuk tiap mahasiswa terpilih (dapat berbeda-beda).</p>
                    </div>
                </div>
                <button type="button" onclick="closeP2Modal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Sub Navigation Bar: Quick Jump Toolbar for Preview 2 (Lompat Cepat) -->
            <div class="px-4 sm:px-6 py-2.5 bg-slate-900 border-b border-slate-800 flex items-center gap-2.5 relative shrink-0">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 shrink-0 flex items-center gap-1.5 hidden sm:flex">
                    <i class="fa-solid fa-compass text-indigo-400"></i> Lompat Cepat:
                </span>

                <!-- Left Scroll Arrow -->
                <button type="button" id="btnScrollP2QuickLeft" onclick="scrollP2QuickStudentTabs('left')" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-indigo-600 border border-slate-700 hover:border-indigo-500 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-all cursor-pointer shrink-0 shadow-xs active:scale-95" title="Geser ke kiri">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <!-- Scroll Container with Grab/Wheel support -->
                <div id="p2QuickModalStudentTabs" class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5 flex-1 min-w-0 select-none cursor-grab">
                    <!-- Quick Jump Anchors injected dynamically via JS -->
                </div>

                <!-- Right Scroll Arrow -->
                <button type="button" id="btnScrollP2QuickRight" onclick="scrollP2QuickStudentTabs('right')" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-indigo-600 border border-slate-700 hover:border-indigo-500 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-all cursor-pointer shrink-0 shadow-xs active:scale-95 animate-pulse hover:animate-none" title="Geser ke kanan">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formP2Plotting" onsubmit="submitP2Plotting(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                <!-- Scrollable Body Content -->
                <div class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1 bg-slate-50/60">
                    <!-- Top Bar: Summary & Action -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Daftar Mahasiswa &amp; Plotting Penguji:</label>
                                <span id="p2ModalSelectedCountBadge" class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">0 Mahasiswa</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Pilih Dosen Penguji 1 &amp; 2 secara mandiri pada setiap kartu mahasiswa di bawah.</p>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="applyQuickFirstP2ToAll()" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer active:scale-95 shadow-2xs" title="Salin dosen penguji dari Mahasiswa #1 ke semua mahasiswa lainnya">
                                <i class="fa-solid fa-copy"></i> Salin Mahasiswa #1 ke Semua
                            </button>
                        </div>
                    </div>

                    <!-- Per-Student Accordion / Card List Container -->
                    <div id="p2ModalSelectedList" class="space-y-3.5">
                        <!-- Rendered dynamically via JS -->
                    </div>

                    <!-- Catatan Global Koordinator TA -->
                    <div class="pt-3 border-t border-slate-200">
                        <label class="text-xs font-bold text-slate-700 block mb-1">Catatan Koordinator TA (Opsional untuk Seluruh Mahasiswa):</label>
                        <textarea id="p2ModalGlobalCatatanKoor" name="catatan_koor" rows="2" placeholder="Masukkan catatan atau arahan umum (opsional)..." class="w-full text-xs p-2.5 border border-slate-300 rounded-xl bg-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 resize-none shadow-2xs"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 sm:px-6 border-t border-slate-200 bg-white flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="closeP2Modal()" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="modalP2BtnSubmit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-black text-xs rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="modalP2BtnSubmitText">Simpan Penetapan Dosen Penguji</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- BATCH APPROVAL & DOSEN PLOTTING MODAL -->
    <div id="batchApprovalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 modal-backdrop overflow-hidden" onclick="if(event.target===this)closeBatchModal()">

        <!-- Modal Dialog Card -->
        <div class="relative z-10 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-3xl sm:max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Modal Header (Fixed at top) -->
            <div class="p-4 sm:p-5 px-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50/70 via-white to-white shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-extrabold text-base shadow-md shadow-orange-600/25 shrink-0">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-snug">Aksi Approval &amp; Plotting Dosen per Mahasiswa</h3>
                        <p class="text-xs text-slate-500">Tentukan Dosen Pembimbing 1 &amp; 2 untuk tiap mahasiswa terpilih (dapat berbeda-beda).</p>
                    </div>
                </div>
                <button type="button" onclick="closeBatchModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Sub Navigation Bar: Quick Jump Toolbar with Navigation Arrows (Lompat Cepat) -->
            <div class="px-4 sm:px-6 py-2.5 bg-slate-900 border-b border-slate-800 flex items-center gap-2.5 relative shrink-0">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 shrink-0 flex items-center gap-1.5 hidden sm:flex">
                    <i class="fa-solid fa-compass text-orange-400"></i> Lompat Cepat:
                </span>

                <!-- Left Scroll Arrow -->
                <button type="button" id="btnScrollQuickLeft" onclick="scrollQuickStudentTabs('left')" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-orange-600 border border-slate-700 hover:border-orange-500 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-all cursor-pointer shrink-0 shadow-xs active:scale-95" title="Geser ke kiri">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <!-- Scroll Container with Grab/Wheel support -->
                <div id="quickModalStudentTabs" class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5 flex-1 min-w-0 select-none cursor-grab">
                    <!-- Quick Jump Anchors injected dynamically via JS -->
                </div>

                <!-- Right Scroll Arrow -->
                <button type="button" id="btnScrollQuickRight" onclick="scrollQuickStudentTabs('right')" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-orange-600 border border-slate-700 hover:border-orange-500 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-all cursor-pointer shrink-0 shadow-xs active:scale-95 animate-pulse hover:animate-none" title="Geser ke kanan">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- Modal Form (Flex Column with scrollable middle area and fixed footer) -->
            <form id="formBatchApproval" onsubmit="submitBatchApproval(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                <!-- Scrollable Body Content -->
                <div class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1 bg-slate-50/60">
                    <input type="hidden" id="batchStatusInput" name="status" value="Approved">

                    <!-- Top Bar: Summary & Action -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Daftar Mahasiswa &amp; Plotting Pembimbing:</label>
                                <span id="modalSelectedCountBadge" class="text-[11px] font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full border border-orange-200">0 Mahasiswa</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Pilih Dosen Pembimbing 1 &amp; 2 secara mandiri pada setiap kartu mahasiswa di bawah.</p>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="applyQuickFirstToAll()" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer active:scale-95 shadow-2xs" title="Salin dosen pembimbing dari Mahasiswa #1 ke semua mahasiswa lainnya">
                                <i class="fa-solid fa-copy"></i> Salin Mahasiswa #1 ke Semua
                            </button>
                        </div>
                    </div>

                    <!-- Per-Student Accordion / Card List Container -->
                    <div id="modalSelectedList" class="space-y-3.5">
                        <!-- Rendered dynamically via JS -->
                    </div>

                    <!-- Catatan Global Koordinator TA -->
                    <div class="pt-3 border-t border-slate-200">
                        <label class="text-xs font-bold text-slate-700 block mb-1">Catatan Koordinator TA (Opsional untuk Seluruh Mahasiswa):</label>
                        <textarea id="modalGlobalCatatanKoor" name="catatan_koor" rows="2" placeholder="Masukkan catatan atau arahan umum (opsional)..." class="w-full text-xs p-2.5 border border-slate-300 rounded-xl bg-white focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 resize-none shadow-2xs"></textarea>
                    </div>
                </div>

                <!-- Modal Footer (Fixed at bottom) -->
                <div class="p-4 sm:px-6 border-t border-slate-200 bg-white flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="closeBatchModal()" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="modalBtnSubmit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="modalBtnSubmitText">Simpan &amp; Lanjutkan ke Ketua KK</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MULTI-STUDENT BATCH REVIEW MODAL (CEK DOKUMEN & PLOTTING INDIVIDU MASSAL) -->
    <div id="p1BatchReviewModal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-3 sm:p-5 overflow-y-auto" onclick="if(event.target===this)closeP1BatchReviewModal()">
        <div class="bg-white rounded-3xl max-w-6xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header: Multi-Student Summary & Quick Nav Anchors -->
            <div class="bg-slate-900 text-white shrink-0 border-b border-slate-800">
                <!-- Top Row: Title & Action -->
                <div class="p-4 sm:p-5 px-6 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-orange-600/30 shrink-0">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h3 class="text-base font-extrabold text-white tracking-tight leading-snug">
                                    Peninjauan Dokumen &amp; Penetapan Pembimbing Massal
                                </h3>
                                <span class="bg-orange-500/90 text-white px-3 py-0.5 rounded-full text-xs font-bold whitespace-nowrap shadow-xs" id="p1ModalStudentCounter">
                                    0 Mahasiswa Terpilih
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">Tinjau berkas persyaratan dan tetapkan Dosen Pembimbing 1 &amp; 2 per individu mahasiswa.</p>
                        </div>
                    </div>

                    <button type="button" onclick="closeP1BatchReviewModal()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer shrink-0">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Sub Navigation Bar: Quick Jump Toolbar with Navigation Arrows -->
                <div class="px-4 sm:px-6 py-2.5 bg-slate-950/80 border-t border-slate-800/80 flex items-center gap-2.5 relative">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 shrink-0 flex items-center gap-1.5 hidden sm:flex">
                        <i class="fa-solid fa-compass text-orange-400"></i> Lompat Cepat:
                    </span>

                    <!-- Left Scroll Arrow -->
                    <button type="button" id="btnScrollP1Left" onclick="scrollP1StudentTabs('left')" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-orange-600 border border-slate-700 hover:border-orange-500 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-all cursor-pointer shrink-0 shadow-xs active:scale-95" title="Geser ke kiri">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <!-- Scroll Container with Grab/Wheel support -->
                    <div id="p1ModalStudentTabs" class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5 flex-1 min-w-0 select-none cursor-grab">
                        <!-- Quick Jump Anchors injected dynamically via JS -->
                    </div>

                    <!-- Right Scroll Arrow -->
                    <button type="button" id="btnScrollP1Right" onclick="scrollP1StudentTabs('right')" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-orange-600 border border-slate-700 hover:border-orange-500 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-all cursor-pointer shrink-0 shadow-xs active:scale-95 animate-pulse hover:animate-none" title="Geser ke kanan">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content Body (Stacked View for All Selected Students) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-8 flex-1 bg-slate-100/80 custom-scrollbar" id="p1BatchModalBody">
                <div class="py-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500 mb-3 block"></i>
                    Memuat data dokumen &amp; profil seluruh mahasiswa terpilih...
                </div>
            </div>

            <!-- Modal Footer Actions Bar -->
            <div class="p-4 px-6 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="applyFirstStudentDosenToAll()" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer active:scale-95 shadow-2xs" title="Salin dosen pembimbing yang dipilih pada Mahasiswa #1 ke seluruh mahasiswa lainnya">
                        <i class="fa-solid fa-copy"></i> Salin Pembimbing Mahasiswa #1 ke Semua
                    </button>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeP1BatchReviewModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="btnSubmitP1BatchReview" onclick="submitP1MultiDetailPlottings()" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-black shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition cursor-pointer active:scale-95">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="btnSubmitP1BatchReviewText">SIMPAN SEMUA PEMBIMBING &amp; SETUJUI</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- DOCUMENT PDF PREVIEW MODAL -->
    <div id="p1PdfModal" class="fixed inset-0 z-[60] bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-2xl max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200">
            <div class="p-3.5 px-5 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white flex items-center gap-2" id="p1PdfModalTitle">Pratinjau Dokumen PDF</h3>
                        <p class="text-[10px] text-slate-400" id="p1PdfModalSubtitle">Memuat tampilan dokumen...</p>
                    </div>
                </div>
                <button type="button" onclick="closeP1PdfModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 p-2 overflow-hidden">
                <iframe id="p1PdfModalFrame" src="about:blank" class="w-full h-full border-none rounded-xl bg-white shadow-inner"></iframe>
            </div>
        </div>
    </div>

    <!-- MODAL RIWAYAT HISTORI PLOTTING TERPADU (PEMBIMBING & PENGUJI) -->
    <div id="modalHistoryPlotting" class="fixed inset-0 z-[60] bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-4 px-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-700/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500/20 border border-orange-500/30 text-orange-400 flex items-center justify-center font-bold text-base shadow-inner">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2" id="historyModalTitle">
                            Riwayat Histori Plotting &amp; Perubahan Dosen
                        </h3>
                        <p class="text-[11px] text-slate-300 font-medium">Audit log setiap penetapan dan perubahan Dosen Pembimbing &amp; Penguji Tugas Akhir</p>
                    </div>
                </div>
                <button type="button" onclick="closeHistoryPlottingModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Category Filter & Search Bar -->
            <div class="p-3.5 px-6 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                <!-- Category Tabs -->
                <div class="flex items-center bg-slate-200/80 p-1 rounded-xl gap-1 text-xs font-bold text-slate-600">
                    <button type="button" id="tabHistoryFilterAll" onclick="switchHistoryCategoryTab('All')" class="px-3 py-1.5 rounded-lg transition cursor-pointer bg-white text-slate-900 shadow-2xs">
                        Semua
                    </button>
                    <button type="button" id="tabHistoryFilterPembimbing" onclick="switchHistoryCategoryTab('Pembimbing')" class="px-3 py-1.5 rounded-lg transition cursor-pointer hover:text-orange-600 text-slate-600">
                        👨‍🏫 Pembimbing (TA)
                    </button>
                    <button type="button" id="tabHistoryFilterPenguji" onclick="switchHistoryCategoryTab('Penguji')" class="px-3 py-1.5 rounded-lg transition cursor-pointer hover:text-indigo-600 text-slate-600">
                        👔 Penguji (Preview 2)
                    </button>
                </div>

                <div class="flex items-center gap-3 flex-1 max-w-sm">
                    <div class="flex items-center gap-2 flex-1 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-2xs">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                        <input type="text" id="inputSearchHistoryPlotting" oninput="filterHistoryPlottingRows(this.value)" placeholder="Cari nama, NIM, atau dosen..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                    </div>
                    <span id="historyRecordCount" class="text-xs font-bold text-slate-500 font-mono shrink-0">0 Catatan</span>
                </div>
            </div>

            <!-- Modal Body (Timeline List) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-3.5 custom-scrollbar bg-slate-50/50" id="historyPlottingTimelineContainer">
                <div class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 text-indigo-600"></i>
                    <p class="text-xs">Memuat data histori...</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-3.5 px-6 bg-white border-t border-slate-200 flex items-center justify-end shrink-0">
                <button type="button" onclick="closeHistoryPlottingModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Global Config for External Dashboard Script -->
    <script>
        window.DASHBOARD_CONFIG = {
            list: <?= json_encode($list_mahasiswa ?? []); ?>,
            listPreview2: <?= json_encode($list_preview2 ?? []); ?>,
            listSidang: <?= json_encode($list_sidang ?? []); ?>,
            dosenList: <?= json_encode($dosen_list ?? []); ?>,
            ruanganList: <?= json_encode($ruangan_list ?? []); ?>,
            ajaxBatchUrl: "<?= site_url('koordinatorta/ajax_batch_approval'); ?>",
            ajaxBatchDetailsUrl: "<?= site_url('koordinatorta/ajax_get_batch_details'); ?>",
            ajaxRealtimeUrl: "<?= site_url('koordinatorta/ajax_realtime_dashboard'); ?>",
            ajaxPreview2UpdateUrl: "<?= site_url('koordinatorta/ajax_update_preview2_penguji'); ?>",
            ajaxPreview2BatchUrl: "<?= site_url('koordinatorta/ajax_batch_preview2_penguji'); ?>",
            ajaxPreview2RealtimeUrl: "<?= site_url('koordinatorta/ajax_realtime_preview2'); ?>",
            ajaxHistoryTaUrl: "<?= site_url('koordinatorta/ajax_get_history_ta'); ?>",
            ajaxHistoryPengujiUrl: "<?= site_url('koordinatorta/ajax_get_history_penguji'); ?>",
            ajaxSidangUpdateUrl: "<?= site_url('koordinatorta/ajax_update_jadwal_sidang'); ?>",
            ajaxSidangBatchUrl: "<?= site_url('koordinatorta/ajax_batch_jadwal_sidang'); ?>",
            ajaxSidangRealtimeUrl: "<?= site_url('koordinatorta/ajax_realtime_sidang'); ?>",
            ajaxTambahRuanganUrl: "<?= site_url('koordinatorta/ajax_tambah_ruangan'); ?>",
            ajaxHapusRuanganUrl: "<?= site_url('koordinatorta/ajax_hapus_ruangan'); ?>",
            ajaxGetRuanganUrl: "<?= site_url('koordinatorta/ajax_get_ruangan_list'); ?>",
            detailUrlPrefix: "<?= site_url('koordinatorta/detail_mahasiswa/'); ?>"
        };
    </script>

    <!-- Modular Dashboard JavaScript Engine -->
    <script src="<?= base_url('assets/js/koordinator_ta_dashboard.js?v=' . time()); ?>"></script>
</body>
</html>
