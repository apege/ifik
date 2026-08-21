<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Import Email & Token Dispatcher | IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
    
    <!-- SheetJS for XLSX parsing & export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <!-- PapaParse for CSV parsing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/papaparse/5.4.1/papaparse.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

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

        .drop-zone {
            border: 2px dashed #ea580c;
            background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%);
            transition: all 0.25s ease-in-out;
        }

        .drop-zone:hover, .drop-zone.dragover {
            background-color: #ffedd5;
            border-color: #c2410c;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(234, 88, 12, 0.2);
        }

        .token-badge {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 1.5px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            text-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(234, 88, 12, 0); }
        }
        .pulse-glow {
            animation: pulseGlow 2s infinite;
        }

        /* Unified Multi-Search Pill Component (Kalender Style) */
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

        .custom-dropdown-menu {
            z-index: 140 !important;
        }

        /* ============================================================
           ANIMATED GRADIENT BUTTONS (Ultra High-End Micro-Interactions)
           ============================================================ */

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .btn-gradient-base {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            overflow: hidden;
            user-select: none;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
            background-size: 220% 220% !important;
            z-index: 1;
        }

        /* Ambient Light Sweep Shimmer Effect on Hover */
        .btn-gradient-base::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                115deg,
                transparent 0%,
                rgba(255, 255, 255, 0.05) 30%,
                rgba(255, 255, 255, 0.4) 50%,
                rgba(255, 255, 255, 0.05) 70%,
                transparent 100%
            );
            transform: skewX(-20deg);
            transition: left 0.75s ease;
            pointer-events: none;
            z-index: 2;
        }

        .btn-gradient-base:hover::after {
            left: 150%;
        }

        .btn-gradient-base:hover {
            transform: translateY(-2px) scale(1.025);
            animation: gradientFlow 3s ease infinite;
        }

        .btn-gradient-base:active {
            transform: translateY(0) scale(0.97);
        }

        /* 1. Primary Orange Flame (Generate Token Selected) */
        .btn-gradient-orange-solid {
            background: linear-gradient(120deg, #ea580c 0%, #f97316 25%, #fb923c 50%, #f97316 75%, #ea580c 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
        }
        .btn-gradient-orange-solid:hover {
            box-shadow: 0 8px 24px rgba(234, 88, 12, 0.55), 0 0 12px rgba(251, 146, 60, 0.4);
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* 2. Soft Peach Gradient (Generate All Kosong) */
        .btn-gradient-orange-soft {
            background: linear-gradient(120deg, #fff7ed 0%, #ffedd5 30%, #fed7aa 50%, #ffedd5 70%, #fff7ed 100%);
            color: #c2410c !important;
            border: 1.5px solid #fed7aa;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.08);
        }
        .btn-gradient-orange-soft:hover {
            border-color: #f97316;
            color: #9a3412 !important;
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.22);
        }

        /* 3. Emerald Solid (Kirim Email Selected) */
        .btn-gradient-emerald-solid {
            background: linear-gradient(120deg, #059669 0%, #10b981 25%, #34d399 50%, #10b981 75%, #047857 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }
        .btn-gradient-emerald-solid:hover {
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.55), 0 0 12px rgba(52, 211, 153, 0.4);
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* 4. Mint Soft (Export Excel) */
        .btn-gradient-emerald-soft {
            background: linear-gradient(120deg, #ecfdf5 0%, #d1fae5 30%, #a7f3d0 50%, #d1fae5 70%, #ecfdf5 100%);
            color: #065f46 !important;
            border: 1.5px solid #a7f3d0;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
        }
        .btn-gradient-emerald-soft:hover {
            border-color: #10b981;
            color: #047857 !important;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.22);
        }

        /* 5. Rose Soft (Hapus) */
        .btn-gradient-rose-soft {
            background: linear-gradient(120deg, #fff1f2 0%, #ffe4e6 30%, #fecdd3 50%, #ffe4e6 70%, #fff1f2 100%);
            color: #be123c !important;
            border: 1.5px solid #fecdd3;
            box-shadow: 0 2px 8px rgba(225, 29, 72, 0.08);
        }
        .btn-gradient-rose-soft:hover {
            border-color: #f43f5e;
            color: #9f1239 !important;
            box-shadow: 0 6px 18px rgba(225, 29, 72, 0.22);
        }

        /* 6. Light Glass (Template XLSX) */
        .btn-gradient-slate-light {
            background: linear-gradient(120deg, #ffffff 0%, #f8fafc 30%, #e2e8f0 50%, #f8fafc 70%, #ffffff 100%);
            color: #334155 !important;
            border: 1.5px solid #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .btn-gradient-slate-light:hover {
            border-color: #0ea5e9;
            color: #0f172a !important;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
        }

        /* 7. Dark Cosmic Obsidian (Template Email) */
        .btn-gradient-dark-obsidian {
            background: linear-gradient(120deg, #0f172a 0%, #1e293b 25%, #334155 50%, #1e293b 75%, #020617 100%);
            color: #f8fafc !important;
            border: 1px solid #334155;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.35);
        }
        .btn-gradient-dark-obsidian:hover {
            border-color: #fbbf24;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.6), 0 0 12px rgba(245, 158, 11, 0.35);
        }

        /* ROTATING CONIC-GRADIENT BORDER FOR TABLE CONTAINER */
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

        /* PROGRESSIVE FLUX LOADER STYLES */
        .flux-bar-glow {
            box-shadow: 0 0 22px rgba(29, 111, 251, 0.65), 0 0 40px rgba(116, 225, 255, 0.45), inset 0 1.5px 0 rgba(255, 255, 255, 0.6), inset 0 -2px 3px rgba(0, 40, 120, 0.4);
        }

        .flux-sheen-sweep {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.85) 50%, transparent 100%);
            mix-blend-mode: screen;
            animation: fluxSheenMove 1.4s linear infinite;
        }

        @keyframes fluxSheenMove {
            0% { transform: translateX(-110%); }
            100% { transform: translateX(250%); }
        }

        /* BLUR REVEAL STAGGER ANIMATION (Inspired by blur-reveal.tsx) */
        @keyframes blurRevealChar {
            0% {
                opacity: 0;
                filter: blur(14px);
                transform: translateY(10px) scale(0.9);
            }
            100% {
                opacity: 1;
                filter: blur(0px);
                transform: translateY(0) scale(1);
            }
        }

        .blur-char-animate {
            display: inline-block;
            opacity: 0;
            animation: blurRevealChar 0.42s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            will-change: transform, filter, opacity;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="<?= site_url('dashboard') ?>" class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Import Email & Dispatcher Token</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola impor Excel (XLSX), generate token 8 karakter, dan kirim email pemberitahuan.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="downloadSampleTemplate('xlsx')" class="btn-gradient-base btn-gradient-slate-light h-10 px-4 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
                    <span>Template XLSX</span>
                </button>
                <button onclick="openEmailTemplateModal()" class="btn-gradient-base btn-gradient-dark-obsidian h-10 px-4 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-amber-400"></i>
                    <span>Template Email</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Stats Overview Cards (Highlight Card Design) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- 1. Total Accounts Card -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-brand-500/40 hover:shadow-2xl hover:shadow-brand-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-brand-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-brand-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-brand-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Akun</p>
                            <h3 id="stat-total" class="text-2xl font-black text-slate-900 mt-1 tracking-tight">0</h3>
                            <p id="stat-total-desc" class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">0 Dosen, 0 Mahasiswa</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-brand-500/20 blur-md group-hover:blur-lg group-hover:bg-brand-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-md text-brand-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient Divider Line & Floating Pulse Dots -->
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-brand-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>

                    <!-- Corner Accents -->
                    <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-brand-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                </div>
            </div>

            <!-- 2. Token Generated Card -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Token Generated</p>
                            <h3 id="stat-token" class="text-2xl font-black text-slate-900 mt-1 tracking-tight">0 <span class="text-xs font-semibold text-cyan-600 font-normal">(0%)</span></h3>
                            <p id="stat-token-desc" class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">0 akun sudah siap token</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-key text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient Divider Line & Floating Pulse Dots -->
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>

                    <!-- Corner Accents -->
                    <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-cyan-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                </div>
            </div>

            <!-- 3. Email Sent Card -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Email Terkirim</p>
                            <h3 id="stat-sent" class="text-2xl font-black text-slate-900 mt-1 tracking-tight">0 <span class="text-xs font-semibold text-emerald-600 font-normal">(0%)</span></h3>
                            <p id="stat-sent-desc" class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">0 email berhasil dikirim</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-paper-plane text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient Divider Line & Floating Pulse Dots -->
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>

                    <!-- Corner Accents -->
                    <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-emerald-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                </div>
            </div>

            <!-- 4. Email Pending Card -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-amber-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-amber-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Belum Terkirim</p>
                            <h3 id="stat-pending" class="text-2xl font-black text-slate-900 mt-1 tracking-tight">0</h3>
                            <p id="stat-pending-desc" class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Memerlukan pengiriman</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-clock text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient Divider Line & Floating Pulse Dots -->
                    <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div class="w-1/3 h-0.5 bg-gradient-to-r from-amber-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                        <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>

                    <!-- Corner Accents -->
                    <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-amber-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                </div>
            </div>
        </div>

        <!-- File Upload Drag & Drop Area -->
        <div class="card-custom p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-file-import"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Upload File Import Email (CSV / XLSX / XLS)</h2>
                </div>
                <button onclick="openAddAccountModal()" class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tambah Manual</span>
                </button>
            </div>

            <div id="drop-zone" class="drop-zone rounded-2xl p-8 text-center cursor-pointer relative overflow-hidden group">
                <!-- Interactive Elastic Mesh / Neural Grid Canvas -->
                <canvas id="neural-dropzone-canvas" class="absolute inset-0 w-full h-full pointer-events-none rounded-2xl z-0"></canvas>

                <input type="file" id="file-input" accept=".csv, .xlsx, .xls" class="hidden" onchange="handleFileSelect(event)">
                
                <div class="relative z-10 flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-white text-brand-600 shadow-md border border-orange-100 flex items-center justify-center text-2xl mb-1 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">
                            Tarik & Lepas File CSV / XLSX / XLS di sini, atau <span class="text-brand-600 underline">Pilih File</span>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Mendukung format .CSV, .XLSX, .XLS hingga 10MB (Kolom: Nama, Email, Role, NIM/NIP)</p>
                    </div>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200">
                            <i class="fa-solid fa-file-excel text-emerald-600 mr-1"></i> Auto Detect Column Headers
                        </span>
                        <span class="px-2.5 py-1 text-[11px] font-semibold bg-blue-50 text-blue-700 rounded-md border border-blue-200">
                            <i class="fa-solid fa-shield-halved text-blue-600 mr-1"></i> Instant Browser Validation
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Toolbar & Filters -->
        <div class="card-custom p-5 mb-8 space-y-4">
            <!-- Row 1: Unified Multi-Search Bar (Kalender Style) -->
            <div class="relative search-pill-container" id="multiSearchWrapper">
                <!-- Main Search Pill -->
                <div class="unified-search-pill">
                    <!-- Main Category Selector Dropdown -->
                    <div class="relative custom-dropdown-container">
                        <input type="hidden" id="mainCategorySelect" value="query">
                        <button type="button" onclick="toggleCustomDropdown('main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-brand-600 focus:outline-none">
                            <span id="label-filter-main-cat">Cari Kata Kunci</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-cat"></i>
                        </button>
                        <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600">
                                <span>🔍 Kata Kunci (Semua)</span>
                                <i class="fa-solid fa-check text-xs check-icon"></i>
                            </div>
                            <div onclick="selectMainCategory('name', '🏷️ Nama Lengkap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>🏷️ Nama Lengkap</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('nim_nip', '🆔 NIM / NIP / ID', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>🆔 NIM / NIP / ID</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('email_addr', '📧 Email Telkom', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>📧 Email Telkom</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('role', '👤 Peran / Role', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>👤 Peran / Role</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('token', '⚡ Status Token', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>⚡ Status Token</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('email', '✉️ Status Kirim Email', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>✉️ Status Kirim Email</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <!-- Main Text Input Container -->
                    <div id="mainValueContainer" class="flex-1 flex items-center relative">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="mainSearchInput" oninput="handleUnifiedMultiSearch()" placeholder="Cari Nama, Email, Token, NIM..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
                    </div>

                    <!-- Main Custom Select Dropdown Container (When Category != query) -->
                    <div id="mainCustomSelectWrap" class="hidden flex-1 relative custom-dropdown-container">
                        <input type="hidden" id="mainCustomSelectVal" value="">
                        <button type="button" onclick="toggleCustomDropdown('main-select', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                            <span id="label-filter-main-select" class="flex items-center gap-1.5 truncate">Semua Data</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-select"></i>
                        </button>
                        <div id="menu-filter-main-select" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <!-- Options injected dynamically -->
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
                        <!-- Extra filter rows injected dynamically -->
                    </div>
                    
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian data akun.</span>
                        <button type="button" onclick="resetImportMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Row 2: Batch Actions, Page Size & Tools -->
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <!-- Batch Actions Left -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <button onclick="bulkGenerateTokenSelected()" class="btn-gradient-base btn-gradient-orange-solid h-9 px-3.5 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>Generate Token (Selected)</span>
                    </button>
                    <button onclick="bulkGenerateTokenAll()" class="btn-gradient-base btn-gradient-orange-soft h-9 px-3.5 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-key text-brand-600 text-xs"></i>
                        <span>Generate All (Kosong)</span>
                    </button>
                    <button onclick="bulkSendEmailSelected()" class="btn-gradient-base btn-gradient-emerald-solid h-9 px-3.5 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Kirim Email (Selected)</span>
                    </button>
                </div>

                <!-- Page Size & Tools Right -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Page Size Selector Top -->
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-2.5 h-9 rounded-xl shadow-2xs">
                        <span class="font-medium">Tampilkan</span>
                        <select onchange="changePageSize(this.value)" class="page-size-select h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-brand-500 cursor-pointer">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="font-medium">data/hal</span>
                        <span class="text-slate-300">|</span>
                        <span>Total: <strong class="total-rows-count text-slate-900 font-bold">0</strong></span>
                        <span class="selected-rows-count hidden text-brand-600 font-bold ml-1">(0 terpilih)</span>
                    </div>

                    <div class="h-6 w-px bg-slate-200 mx-0.5 hidden sm:block"></div>

                    <!-- Tools Right -->
                    <button onclick="exportData('xlsx')" class="btn-gradient-base btn-gradient-emerald-soft h-9 px-3.5 text-xs flex items-center gap-2" title="Export to Excel XLSX">
                        <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
                        <span>Export Excel</span>
                    </button>
                    <button onclick="bulkDeleteSelected()" class="btn-gradient-base btn-gradient-rose-soft h-9 px-3.5 text-xs flex items-center gap-2" title="Hapus Selected">
                        <i class="fa-solid fa-trash-can text-sm text-rose-600"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Accounts Table with Rotating Conic-Gradient Border -->
        <div class="table-rotating-border-wrap">
            <span class="table-rotating-border-spin"></span>
            <div class="table-rotating-border-inner overflow-x-auto">
                <table class="table-custom-rounded text-left text-xs">
                    <thead class="bg-white text-slate-700 font-semibold text-xs border-b border-slate-200/90">
                        <tr>
                            <th class="p-4 w-12 text-center">
                                <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" class="rounded text-brand-600 focus:ring-brand-500 cursor-pointer">
                            </th>
                            <th class="p-4 w-12 text-center whitespace-nowrap">No</th>
                            <th class="p-4 whitespace-nowrap">Akun / Pengguna</th>
                            <th class="p-4 whitespace-nowrap text-center">NIM / NIP / ID</th>
                            <th class="p-4 whitespace-nowrap text-center">Token Access (8-char)</th>
                            <th class="p-4 whitespace-nowrap text-center">Status Token</th>
                            <th class="p-4 whitespace-nowrap text-center">Status Kirim Email</th>
                            <th class="p-4 whitespace-nowrap text-center">Tgl Import</th>
                            <th class="p-4 text-center min-w-[90px] whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="accounts-table-body" class="divide-y divide-slate-100 bg-white font-medium">
                        <!-- Rows rendered dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Bottom Pagination Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan data dari total <strong class="total-rows-count text-slate-800">0</strong> akun
            </div>
            <div class="pagination-controls-bottom flex items-center gap-1" id="pagination-controls">
                <!-- Pagination buttons rendered via JS -->
            </div>
        </div>
        </div>

    </main>

    <!-- MODAL: Email Template Editor & Visual Preview -->
    <div id="modal-template" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-900 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Pengaturan & Template Email Dispatcher</h3>
                        <p class="text-xs text-slate-400">Kustomisasi konten email pemberitahuan token akun</p>
                    </div>
                </div>
                <button onclick="closeEmailTemplateModal()" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 flex-1">
                <!-- Variables Tags -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Tag Variabel Dinamis (Klik untuk menyisipkan):</label>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="insertTag('{NAMA}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{NAMA}</button>
                        <button onclick="insertTag('{EMAIL}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{EMAIL}</button>
                        <button onclick="insertTag('{TOKEN}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{TOKEN}</button>
                        <button onclick="insertTag('{ROLE}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{ROLE}</button>
                        <button onclick="insertTag('{NIM_NIP}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{NIM_NIP}</button>
                    </div>
                </div>

                <!-- Email Subject -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subjek Email:</label>
                    <input type="text" id="template-subject" class="w-full px-3.5 py-2 text-xs font-semibold bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Template Body -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pesan Email:</label>
                    <textarea id="template-body" rows="6" class="w-full px-3.5 py-2 text-xs font-medium bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 font-mono"></textarea>
                </div>

                <!-- Visual Live HTML Preview -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Live Visual Email Preview:</label>
                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50">
                        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-lg mx-auto">
                            <!-- Header Banner -->
                            <div class="border-b border-slate-100 pb-4 mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-black text-sm">IF</div>
                                    <span class="font-extrabold text-sm text-slate-900">IFIK Telkom University</span>
                                </div>
                                <span class="text-[10px] text-slate-400">Pemberitahuan Resmi</span>
                            </div>

                            <p class="text-xs text-slate-500 mb-1">Subjek: <strong id="preview-subject-text" class="text-slate-800"></strong></p>
                            
                            <div id="preview-html-body" class="text-xs text-slate-700 space-y-3 my-4 bg-orange-50/50 p-4 rounded-lg border border-orange-100">
                                <!-- Rendered dynamically -->
                            </div>

                            <div class="border-t border-slate-100 pt-3 text-[11px] text-slate-400 text-center">
                                Telecommunication University &bull; Informatika & Custom Systems
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-100 border-t border-slate-200 flex items-center justify-end gap-3">
                <button onclick="closeEmailTemplateModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition-all">
                    Batal
                </button>
                <button onclick="saveEmailTemplate()" class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-all shadow-sm">
                    Simpan Template
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Email Sending Progress Simulation Modal -->
    <div id="modal-send-progress" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-200">
            <div class="px-6 py-4 bg-brand-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 text-white flex items-center justify-center font-bold">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Mengirim Email Dispatcher</h3>
                        <p class="text-xs text-orange-100" id="send-modal-subtitle">Proses pengiriman email sedang berjalan...</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Circular/Linear Progress -->
                <div class="mb-5">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span id="send-progress-status" class="text-slate-700">Menginisialisasi SMTP...</span>
                        <span id="send-progress-percent" class="text-brand-600 font-extrabold text-sm">0%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden p-0.5 border border-slate-200">
                        <div id="send-progress-bar" class="bg-gradient-to-r from-brand-500 to-emerald-500 h-full rounded-full transition-all duration-300 w-0"></div>
                    </div>
                </div>

                <!-- Activity Terminal Output -->
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Live Log Dispatcher:</label>
                <div id="send-terminal-log" class="bg-slate-950 text-slate-200 font-mono text-[11px] p-4 rounded-xl h-48 overflow-y-auto space-y-1.5 border border-slate-800 shadow-inner">
                    <div class="text-slate-500">[SYSTEM] Ready to send emails...</div>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500" id="send-count-summary">0 / 0 Terkirim</span>
                <button id="send-modal-close-btn" disabled onclick="closeSendProgressModal()" class="px-4 py-2 text-xs font-semibold text-white bg-slate-400 cursor-not-allowed rounded-lg transition-all">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Add / Edit Account Manual -->
    <div id="modal-account" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h3 id="modal-account-title" class="font-bold text-sm">Tambah Akun Manual</h3>
                <button onclick="closeAccountModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="account-form" onsubmit="saveAccountForm(event)" class="p-6 space-y-4">
                <input type="hidden" id="account-id">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" id="acc-name" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                    <input type="email" id="acc-email" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Peran / Role *</label>
                        <select id="acc-role" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Laboran">Laboran</option>
                            <option value="Ka. Ur">Ka. Ur</option>
                            <option value="Koordinator TA">Koordinator TA</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIM / NIP / ID</label>
                        <input type="text" id="acc-nim-nip" placeholder="Contoh: 1301210001" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Token Access (8 Karakter: Besar, Kecil, Angka, Simbol)</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="acc-token" maxlength="8" placeholder="Otomatis / Isi manual" class="flex-1 px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <button type="button" onclick="generateTokenForInput()" class="px-3 py-2 text-xs font-semibold text-brand-600 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100">
                            Generate 8-Char
                        </button>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Progressive Flux Loader for Excel/CSV Upload -->
    <div id="modal-flux-loader" class="fixed inset-0 z-50 hidden bg-slate-950/85 backdrop-blur-xl flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-slate-900/90 backdrop-blur-2xl rounded-3xl max-w-lg w-full shadow-2xl p-10 border border-slate-800 flex flex-col items-center justify-center text-center relative overflow-hidden">
            <!-- Decorative Ambient Blurs -->
            <div class="absolute -top-16 -left-16 w-44 h-44 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -right-16 w-44 h-44 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>

            <!-- Animated Phase Label with 3D / Blur Transition -->
            <div class="h-16 flex items-center justify-center mb-6 w-full">
                <h2 id="flux-phase-label" class="text-3xl sm:text-4xl font-semibold tracking-tight text-slate-200 flux-label-animate">
                    uploading
                </h2>
            </div>

            <!-- Signature Progressive Flux Glowing Bar -->
            <div class="w-full relative h-5 rounded-full bg-slate-950 p-0.5 border border-slate-800 overflow-hidden shadow-[inset_0_2px_4px_rgba(0,0,0,0.6)] mb-4">
                <div id="flux-progress-fill" class="relative h-full rounded-full transition-all duration-150 ease-out w-0 flux-bar-glow overflow-hidden" style="background: linear-gradient(90deg, #1d6ffb 0%, #38bdf8 35%, #74e1ff 55%, #38bdf8 78%, #1d6ffb 100%);">
                    <!-- Dynamic Sheen Sweep Streak -->
                    <span class="flux-sheen-sweep"></span>
                </div>
            </div>

            <!-- Progress Percent & Filename Footer -->
            <div class="flex items-center justify-between w-full text-xs font-semibold px-1">
                <span id="flux-filename" class="font-mono text-slate-400 truncate max-w-[280px]">file.xlsx</span>
                <span id="flux-percent-text" class="font-mono text-sky-400 font-bold">0%</span>
            </div>
        </div>
    </div>

    <!-- MODAL: Preview & Validasi Import Data Excel/CSV -->
    <div id="modal-import-preview" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-5xl w-full shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-brand-600 via-orange-600 to-amber-600 text-white flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-xs">
                        <i class="fa-solid fa-file-circle-check text-xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm tracking-wide">Preview & Validasi Data Import</h3>
                        <p class="text-[11px] text-orange-100 font-medium" id="preview-filename-subtitle">Tinjau dan pastikan validasi data sebelum disimpan ke database</p>
                    </div>
                </div>
                <button onclick="closeImportPreviewModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body Content -->
            <div class="p-6 overflow-y-auto space-y-4 flex-1 bg-slate-50/50">
                <!-- Summary Stats Bar (Clickable as Filters) -->
                <div class="grid grid-cols-4 gap-3">
                    <div onclick="filterPreviewRows('all')" class="p-3.5 bg-white border border-slate-200 hover:border-slate-400 rounded-xl shadow-2xs cursor-pointer transition-all hover:scale-[1.02]">
                        <div class="text-[10px] font-bold tracking-wider uppercase text-slate-400">TOTAL DIBACA</div>
                        <div class="text-xl font-extrabold text-slate-800 mt-0.5" id="preview-stat-total">0</div>
                    </div>
                    <div onclick="filterPreviewRows('valid')" class="p-3.5 bg-emerald-50/70 border border-emerald-200 hover:border-emerald-400 rounded-xl shadow-2xs cursor-pointer transition-all hover:scale-[1.02]">
                        <div class="text-[10px] font-bold tracking-wider uppercase text-emerald-600 flex items-center justify-between">
                            <span>VALID SIAP IMPOR</span>
                            <i class="fa-solid fa-circle-check text-xs"></i>
                        </div>
                        <div class="text-xl font-extrabold text-emerald-700 mt-0.5" id="preview-stat-valid">0</div>
                    </div>
                    <div onclick="filterPreviewRows('duplicate')" class="p-3.5 bg-amber-50/70 border border-amber-200 hover:border-amber-400 rounded-xl shadow-2xs cursor-pointer transition-all hover:scale-[1.02]">
                        <div class="text-[10px] font-bold tracking-wider uppercase text-amber-600 flex items-center justify-between">
                            <span>DUPLIKAT (DIHAPUS)</span>
                            <i class="fa-solid fa-ban text-xs"></i>
                        </div>
                        <div class="text-xl font-extrabold text-amber-700 mt-0.5 flex items-baseline gap-1.5">
                            <span id="preview-stat-duplicate">0</span>
                            <span class="text-[10px] font-normal text-amber-600">(auto-skip)</span>
                        </div>
                    </div>
                    <div onclick="filterPreviewRows('invalid_domain')" class="p-3.5 bg-rose-50/70 border border-rose-200 hover:border-rose-400 rounded-xl shadow-2xs cursor-pointer transition-all hover:scale-[1.02]">
                        <div class="text-[10px] font-bold tracking-wider uppercase text-rose-600 flex items-center justify-between">
                            <span>DOMAIN DITOLAK</span>
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                        </div>
                        <div class="text-xl font-extrabold text-rose-700 mt-0.5" id="preview-stat-invalid">0</div>
                    </div>
                </div>

                <!-- Simple Clean Alert Banner when duplicates exist -->
                <div id="preview-duplicate-alert" class="hidden px-4 py-2.5 bg-amber-50 border border-amber-200/80 rounded-xl text-xs text-amber-800 flex items-center gap-2.5 font-medium">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
                    <span id="preview-duplicate-alert-text">Data duplikat otomatis diabaikan agar tidak ada akun ganda.</span>
                </div>

                <!-- Clean Filter Toolbar -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-white px-3.5 py-2.5 rounded-xl border border-slate-200 shadow-2xs">
                    <div class="flex items-center gap-1.5 text-xs">
                        <button type="button" onclick="filterPreviewRows('valid')" id="tab-preview-valid" class="px-3 py-1.5 font-bold rounded-lg bg-brand-600 text-white shadow-2xs transition-all">Hanya Valid (<span id="count-tab-valid">0</span>)</button>
                        <button type="button" onclick="filterPreviewRows('invalid_domain')" id="tab-preview-invalid_domain" class="px-3 py-1.5 font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all text-rose-700">Domain Ditolak (<span id="count-tab-invalid_domain">0</span>)</button>
                        <button type="button" onclick="filterPreviewRows('duplicate')" id="tab-preview-duplicate" class="px-3 py-1.5 font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all text-amber-700">Duplikat (<span id="count-tab-duplicate">0</span>)</button>
                        <button type="button" onclick="filterPreviewRows('all')" id="tab-preview-all" class="px-3 py-1.5 font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Semua (<span id="count-tab-all">0</span>)</button>
                    </div>

                    <div class="flex items-center gap-3 text-xs font-semibold">
                        <button type="button" onclick="toggleAllPreviewCheckboxes(true)" class="text-brand-600 hover:text-brand-700 hover:underline cursor-pointer">Centang Valid</button>
                        <span class="text-slate-300">•</span>
                        <button type="button" onclick="toggleAllPreviewCheckboxes(false)" class="text-slate-500 hover:text-slate-700 hover:underline cursor-pointer">Uncheck</button>
                    </div>
                </div>

                <!-- Preview Table -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs max-h-80 overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-100 text-[11px] font-bold text-slate-600 uppercase tracking-wider sticky top-0 border-b border-slate-200 z-10">
                            <tr>
                                <th class="p-3 text-center w-10">
                                    <input type="checkbox" id="preview-select-all-cb" onchange="toggleAllPreviewCheckboxes(this.checked)" class="rounded text-brand-600 focus:ring-brand-500 cursor-pointer">
                                </th>
                                <th class="p-3 text-center w-12 font-mono">NO</th>
                                <th class="p-3">NAMA PENGGUNA</th>
                                <th class="p-3">EMAIL TELKOM</th>
                                <th class="p-3">ROLE</th>
                                <th class="p-3">NIM / NIP</th>
                                <th class="p-3 text-center">STATUS VALIDASI</th>
                            </tr>
                        </thead>
                        <tbody id="preview-table-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            <!-- Injected dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-white border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500" id="preview-selected-summary">0 dari 0 data terpilih untuk diimpor</span>
                <div class="flex items-center gap-2.5">
                    <button type="button" onclick="closeImportPreviewModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">Batal</button>
                    <button type="button" id="btn-submit-import-preview" onclick="submitImportFromPreview()" class="px-5 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md flex items-center gap-2 transition-all cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                        <span id="btn-submit-import-text">Simpan Akun Valid Ke Database</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT APPLICATION LOGIC -->
    <script>
        // Initial State Data from Database
        let state = {
            accounts: <?= isset($initial_accounts_json) ? $initial_accounts_json : '[]' ?>,
            selectedIds: [],
            searchQuery: '',
            filterRole: '',
            filterTokenStatus: '',
            filterEmailStatus: '',
            currentPage: 1,
            pageSize: 10,
            emailTemplate: {
                subject: '[IFIK Telkom University] Token Akses Portal Akun Anda: {TOKEN}',
                body: 'Halo {NAMA},\n\nAkun portal IFIK Telkom University Anda telah didaftarkan sebagai {ROLE}.\n\nBerikut adalah Kode Token Akses 8-Karakter unik Anda:\n===============================\nKODE TOKEN : {TOKEN}\nNIM / NIP  : {NIM_NIP}\nEMAIL      : {EMAIL}\n===============================\n\nGunakan token ini untuk melakukan verifikasi awal dan aktivasi kata sandi akun Anda.\n\nSalam hangat,\nTim Layanan Informatika (IFIK) Telkom University'
            }
        };

        async function fetchUsersFromBackend() {
            try {
                const res = await fetch('<?= site_url("import-email/get_users_json") ?>');
                const data = await res.json();
                if (data.status === 'success' && Array.isArray(data.accounts)) {
                    state.accounts = data.accounts;
                    renderStats();
                    renderTable();
                }
            } catch (err) {
                console.error('Failed to sync users from backend:', err);
            }
        }

        // Initialize application on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            initDropZone();
            renderStats();
            renderTable();
            initEmailTemplateFields();
            fetchUsersFromBackend();
        });

        // 1. GENERATE 8-CHARACTER TOKEN UTILITY (Huruf Besar, Huruf Kecil, Simbol, Angka)
        function generate8CharToken() {
            const uppers = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            const lowers = 'abcdefghijkmnpqrstuvwxyz';
            const numbers = '23456789';
            const symbols = '!@#$%^&*_-';
            const allChars = uppers + lowers + numbers + symbols;

            // Minimal 1 karakter dari tiap kelompok (Huruf Besar, Huruf Kecil, Angka, Simbol)
            let tokenArr = [
                uppers.charAt(Math.floor(Math.random() * uppers.length)),
                lowers.charAt(Math.floor(Math.random() * lowers.length)),
                numbers.charAt(Math.floor(Math.random() * numbers.length)),
                symbols.charAt(Math.floor(Math.random() * symbols.length))
            ];

            // 4 karakter sisanya acak dari seluruh kombinasi
            for (let i = 0; i < 4; i++) {
                tokenArr.push(allChars.charAt(Math.floor(Math.random() * allChars.length)));
            }

            // Acak urutan posisi (Fisher-Yates Shuffle)
            for (let i = tokenArr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [tokenArr[i], tokenArr[j]] = [tokenArr[j], tokenArr[i]];
            }

            return tokenArr.join('');
        }

        // 1.5 DOMAIN VALIDATION HELPER (@telkomuniversity.ac.id or @student.telkomuniversity.ac.id)
        function isValidTelkomEmail(email) {
            if (!email) return false;
            const lower = email.trim().toLowerCase();
            return lower.endsWith('@telkomuniversity.ac.id') || lower.endsWith('@student.telkomuniversity.ac.id');
        }

        // 2. FILE DRAG & DROP & PARSING LOGIC
        function initDropZone() {
            const dropZone = document.getElementById('drop-zone');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    processUploadedFile(files[0]);
                }
            });

            dropZone.addEventListener('click', (e) => {
                if (e.target.closest('#file-input')) return;
                document.getElementById('file-input').click();
            });

            // Initialize Elastic Neural Grid Warp on Dropzone
            initDropzoneNeuralGrid();
        }

        function initDropzoneNeuralGrid() {
            const dropzone = document.getElementById('drop-zone');
            const canvas = document.getElementById('neural-dropzone-canvas');
            if (!dropzone || !canvas) return;

            const ctx = canvas.getContext('2d');
            let width = 0;
            let height = 0;
            let dpr = window.devicePixelRatio || 1;

            const SPACING = 24; // grid cell size
            let points = [];
            let rows = 0;
            let cols = 0;

            const mouse = {
                x: -9999,
                y: -9999,
                targetX: -9999,
                targetY: -9999,
                radius: 175, // stretch attraction radius
                strength: 55, // maximum stretch force
                active: false
            };

            function resize() {
                const rect = dropzone.getBoundingClientRect();
                width = rect.width;
                height = rect.height;
                dpr = Math.min(window.devicePixelRatio || 1, 2);

                canvas.width = Math.floor(width * dpr);
                canvas.height = Math.floor(height * dpr);
                canvas.style.width = width + 'px';
                canvas.style.height = height + 'px';
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.scale(dpr, dpr);

                cols = Math.ceil(width / SPACING) + 1;
                rows = Math.ceil(height / SPACING) + 1;

                points = [];
                for (let r = 0; r <= rows; r++) {
                    points[r] = [];
                    for (let c = 0; c <= cols; c++) {
                        const origX = c * SPACING;
                        const origY = r * SPACING;
                        points[r][c] = {
                            origX: origX,
                            origY: origY,
                            x: origX,
                            y: origY,
                            vx: 0,
                            vy: 0
                        };
                    }
                }
            }

            window.addEventListener('resize', resize);
            setTimeout(resize, 50);

            dropzone.addEventListener('mouseenter', () => {
                mouse.active = true;
            });

            dropzone.addEventListener('mousemove', (e) => {
                const rect = dropzone.getBoundingClientRect();
                mouse.targetX = e.clientX - rect.left;
                mouse.targetY = e.clientY - rect.top;
                mouse.active = true;
                mouse.isDraggingFile = false;
            });

            dropzone.addEventListener('mouseleave', () => {
                if (!mouse.isDraggingFile) {
                    mouse.active = false;
                    mouse.targetX = -9999;
                    mouse.targetY = -9999;
                }
            });

            // Native File Drag & Drop tracking (Warp effect follows the dragged file)
            dropzone.addEventListener('dragenter', (e) => {
                const rect = dropzone.getBoundingClientRect();
                mouse.targetX = e.clientX - rect.left;
                mouse.targetY = e.clientY - rect.top;
                mouse.active = true;
                mouse.isDraggingFile = true;
            });

            dropzone.addEventListener('dragover', (e) => {
                const rect = dropzone.getBoundingClientRect();
                mouse.targetX = e.clientX - rect.left;
                mouse.targetY = e.clientY - rect.top;
                mouse.active = true;
                mouse.isDraggingFile = true;
            });

            dropzone.addEventListener('dragleave', () => {
                mouse.isDraggingFile = false;
                mouse.active = false;
                mouse.targetX = -9999;
                mouse.targetY = -9999;
            });

            dropzone.addEventListener('drop', () => {
                mouse.isDraggingFile = false;
                mouse.active = false;
                mouse.targetX = -9999;
                mouse.targetY = -9999;
            });

            let time = 0;

            function animate() {
                requestAnimationFrame(animate);
                time += 0.025;

                ctx.clearRect(0, 0, width, height);

                // Smooth mouse interpolation
                if (mouse.active) {
                    mouse.x += (mouse.targetX - mouse.x) * 0.22;
                    mouse.y += (mouse.targetY - mouse.y) * 0.22;
                } else {
                    mouse.x += (-9999 - mouse.x) * 0.1;
                    mouse.y += (-9999 - mouse.y) * 0.1;
                }

                const currentRadius = mouse.isDraggingFile ? mouse.radius * 1.3 : mouse.radius;
                const currentStrength = mouse.isDraggingFile ? mouse.strength * 1.4 : mouse.strength;

                // Physics update on each grid intersection point (Elastic Mesh Stretching)
                for (let r = 0; r <= rows; r++) {
                    for (let c = 0; c <= cols; c++) {
                        const pt = points[r]?.[c];
                        if (!pt) continue;

                        const dx = mouse.x - pt.origX;
                        const dy = mouse.y - pt.origY;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        let targetX = pt.origX;
                        let targetY = pt.origY;

                        if (mouse.active && dist < currentRadius && dist > 0.01) {
                            // Magnetic stretch pull factor (smooth cosine curve)
                            const norm = 1 - dist / currentRadius;
                            const pull = Math.sin(norm * (Math.PI / 2)) * currentStrength;
                            const angle = Math.atan2(dy, dx);

                            // Organic neural ripple wave
                            const wave = Math.sin(dist * 0.08 - time * 3) * (norm * 7.5);

                            targetX = pt.origX + Math.cos(angle) * (pull + wave);
                            targetY = pt.origY + Math.sin(angle) * (pull + wave);
                        }

                        // Spring dampening physics (rubber stretching effect)
                        const ax = (targetX - pt.x) * 0.25;
                        const ay = (targetY - pt.y) * 0.25;
                        pt.vx = (pt.vx + ax) * 0.72;
                        pt.vy = (pt.vy + ay) * 0.72;
                        pt.x += pt.vx;
                        pt.y += pt.vy;
                    }
                }

                // 1. Draw Horizontal Elastic Lines
                for (let r = 0; r <= rows; r++) {
                    ctx.beginPath();
                    for (let c = 0; c <= cols; c++) {
                        const pt = points[r]?.[c];
                        if (!pt) continue;
                        if (c === 0) {
                            ctx.moveTo(pt.x, pt.y);
                        } else {
                            ctx.lineTo(pt.x, pt.y);
                        }
                    }

                    const rowMidY = r * SPACING;
                    const distToMouse = Math.abs(mouse.y - rowMidY);
                    const proximity = mouse.active && distToMouse < currentRadius ? (1 - distToMouse / currentRadius) : 0;

                    ctx.strokeStyle = proximity > 0 
                        ? `rgba(234, 88, 12, ${0.12 + proximity * (mouse.isDraggingFile ? 0.55 : 0.42)})`
                        : 'rgba(234, 88, 12, 0.08)';
                    ctx.lineWidth = proximity > 0 ? (0.8 + proximity * (mouse.isDraggingFile ? 1.3 : 0.9)) : 0.7;
                    ctx.stroke();
                }

                // 2. Draw Vertical Elastic Lines
                for (let c = 0; c <= cols; c++) {
                    ctx.beginPath();
                    for (let r = 0; r <= rows; r++) {
                        const pt = points[r]?.[c];
                        if (!pt) continue;
                        if (r === 0) {
                            ctx.moveTo(pt.x, pt.y);
                        } else {
                            ctx.lineTo(pt.x, pt.y);
                        }
                    }

                    const colMidX = c * SPACING;
                    const distToMouse = Math.abs(mouse.x - colMidX);
                    const proximity = mouse.active && distToMouse < currentRadius ? (1 - distToMouse / currentRadius) : 0;

                    ctx.strokeStyle = proximity > 0 
                        ? `rgba(234, 88, 12, ${0.12 + proximity * (mouse.isDraggingFile ? 0.55 : 0.42)})`
                        : 'rgba(234, 88, 12, 0.08)';
                    ctx.lineWidth = proximity > 0 ? (0.8 + proximity * (mouse.isDraggingFile ? 1.3 : 0.9)) : 0.7;
                    ctx.stroke();
                }

                // 3. Ambient Glowing Spotlight around Cursor
                if (mouse.active && mouse.x > 0 && mouse.y > 0) {
                    const gradient = ctx.createRadialGradient(mouse.x, mouse.y, 0, mouse.x, mouse.y, currentRadius);
                    const alpha1 = mouse.isDraggingFile ? 0.35 : 0.22;
                    const alpha2 = mouse.isDraggingFile ? 0.16 : 0.10;
                    gradient.addColorStop(0, `rgba(234, 88, 12, ${alpha1})`);
                    gradient.addColorStop(0.4, `rgba(251, 146, 60, ${alpha2})`);
                    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

                    ctx.fillStyle = gradient;
                    ctx.beginPath();
                    ctx.arc(mouse.x, mouse.y, currentRadius, 0, Math.PI * 2);
                    ctx.fill();
                }

                // 4. Grid Intersection Nodes
                for (let r = 0; r <= rows; r++) {
                    for (let c = 0; c <= cols; c++) {
                        const pt = points[r]?.[c];
                        if (!pt) continue;
                        const dx = mouse.x - pt.x;
                        const dy = mouse.y - pt.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (mouse.active && dist < currentRadius) {
                            const intensity = 1 - dist / currentRadius;
                            ctx.fillStyle = `rgba(234, 88, 12, ${0.35 + intensity * (mouse.isDraggingFile ? 0.65 : 0.55)})`;
                            ctx.beginPath();
                            ctx.arc(pt.x, pt.y, 1.2 + intensity * (mouse.isDraggingFile ? 2.2 : 1.8), 0, Math.PI * 2);
                            ctx.fill();
                        }
                    }
                }
            }

            animate();
        }

        function handleFileSelect(event) {
            const files = event.target.files;
            if (files.length > 0) {
                processUploadedFile(files[0]);
            }
        }

        function renderBlurRevealText(element, text) {
            if (!element) return;
            element.innerHTML = '';
            const words = text.split(' ');
            let charOffset = 0;

            words.forEach((word, wIdx) => {
                const wordSpan = document.createElement('span');
                wordSpan.className = 'inline-block whitespace-nowrap' + (wIdx < words.length - 1 ? ' mr-3' : '');
                
                word.split('').forEach((char) => {
                    const charSpan = document.createElement('span');
                    charSpan.className = 'blur-char-animate';
                    charSpan.innerText = char;
                    charSpan.style.animationDelay = `${charOffset * 0.035}s`;
                    wordSpan.appendChild(charSpan);
                    charOffset++;
                });
                
                element.appendChild(wordSpan);
            });
        }

        function showProgressiveFluxLoader(filename, onComplete) {
            const modal = document.getElementById('modal-flux-loader');
            const label = document.getElementById('flux-phase-label');
            const fileText = document.getElementById('flux-filename');
            const fill = document.getElementById('flux-progress-fill');
            const percentText = document.getElementById('flux-percent-text');

            if (!modal) {
                if (typeof onComplete === 'function') onComplete();
                return;
            }

            fileText.innerText = filename;
            fill.style.width = '0%';
            percentText.innerText = '0%';
            renderBlurRevealText(label, 'uploading');
            modal.classList.remove('hidden');

            const phases = [
                { at: 0, label: 'uploading' },
                { at: 28, label: 'processing' },
                { at: 58, label: 'validating' },
                { at: 82, label: 'finalizing' },
                { at: 100, label: 'complete' }
            ];

            let progress = 0;
            let currentLabel = 'uploading';

            const interval = setInterval(() => {
                progress += Math.floor(Math.random() * 2) + 1.8;
                if (progress > 100) progress = 100;

                fill.style.width = progress + '%';
                percentText.innerText = Math.round(progress) + '%';

                for (let i = phases.length - 1; i >= 0; i--) {
                    if (progress >= phases[i].at) {
                        if (currentLabel !== phases[i].label) {
                            currentLabel = phases[i].label;
                            renderBlurRevealText(label, currentLabel);
                        }
                        break;
                    }
                }

                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        if (typeof onComplete === 'function') onComplete();
                    }, 500);
                }
            }, 70);
        }

        function processUploadedFile(file) {
            const ext = file.name.split('.').pop().toLowerCase();

            if (ext === 'csv') {
                Papa.parse(file, {
                    header: true,
                    skipEmptyLines: true,
                    complete: function(results) {
                        showProgressiveFluxLoader(file.name, () => {
                            openImportPreviewModal(results.data, file.name);
                        });
                    },
                    error: function(err) {
                        Swal.fire('Format Error', 'Gagal membaca CSV: ' + err.message, 'error');
                    }
                });
            } else if (ext === 'xlsx' || ext === 'xls') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];
                        const json = XLSX.utils.sheet_to_json(worksheet);
                        showProgressiveFluxLoader(file.name, () => {
                            openImportPreviewModal(json, file.name);
                        });
                    } catch(err) {
                        Swal.fire('File Error', 'Gagal mengekstrak Excel file: ' + err.message, 'error');
                    }
                };
                reader.readAsArrayBuffer(file);
            } else {
                Swal.fire('Format Tidak Didukung', 'Silakan pilih file berektensi .CSV atau .XLSX', 'warning');
            }
        }

        // 2. TWO-STEP IMPORT PREVIEW & VALIDATION MODAL
        let previewState = {
            filename: '',
            rawRows: [],
            rows: [],
            filter: 'valid'
        };

        function openImportPreviewModal(rows, filename) {
            Swal.close();
            if (!rows || rows.length === 0) {
                Swal.fire('File Kosong', 'Tidak ada data yang dapat dibaca pada file tersebut.', 'info');
                return;
            }

            previewState.filename = filename;
            previewState.rawRows = rows;
            previewState.filter = 'valid';
            previewState.rows = [];

            let nowStr = new Date().toISOString().slice(0, 16).replace('T', ' ');
            const seenInFile = new Set();

            rows.forEach((row, idx) => {
                let name = row.Nama || row.nama || row.Name || row.name || 'User ' + (idx + 1);
                let email = row.Email || row.email || row.EmailAddress || '';
                let role = row.Role || row.role || row.Peran || row.peran || 'Mahasiswa';
                let nim_nip = row.NIM || row.nim || row.NIP || row.nip || row.ID || row.id || '';
                let token = row.Token || row.token || '';

                email = email ? email.trim() : '';
                name = name ? name.trim() : 'User';
                role = role ? role.trim() : 'Mahasiswa';
                nim_nip = nim_nip ? nim_nip.toString().trim() : '';

                let status = 'valid';
                let statusText = 'Siap Diimpor';
                let isChecked = true;
                const emailLower = email.toLowerCase();

                if (!email || !email.includes('@')) {
                    status = 'invalid_email';
                    statusText = 'Email Kosong / Format Salah';
                    isChecked = false;
                } else if (!isValidTelkomEmail(email)) {
                    status = 'invalid_domain';
                    statusText = 'Non-Telkom Domain';
                    isChecked = false;
                } else if (state.accounts.some(a => a.email.toLowerCase() === emailLower)) {
                    status = 'duplicate';
                    statusText = 'Duplikat di Database';
                    isChecked = false;
                } else if (seenInFile.has(emailLower)) {
                    status = 'duplicate';
                    statusText = 'Duplikat di File Excel';
                    isChecked = false;
                } else {
                    seenInFile.add(emailLower);
                }

                previewState.rows.push({
                    origIdx: idx,
                    name,
                    email,
                    role,
                    nim_nip,
                    token: token.trim(),
                    status,
                    statusText,
                    checked: isChecked,
                    token_status: token.trim() ? 'ready' : 'empty',
                    password_changed: false,
                    email_status: 'belum',
                    email_sent_at: '-',
                    date_imported: nowStr
                });
            });

            updatePreviewStatsCounters();
            renderPreviewTable();
            document.getElementById('modal-import-preview').classList.remove('hidden');
        }

        function updatePreviewStatsCounters() {
            const totalCount = previewState.rows.length;
            const validCount = previewState.rows.filter(r => r.status === 'valid').length;
            const dupCount = previewState.rows.filter(r => r.status === 'duplicate').length;
            const invalidCount = previewState.rows.filter(r => r.status === 'invalid_domain' || r.status === 'invalid_email').length;

            document.getElementById('preview-stat-total').innerText = totalCount;
            document.getElementById('preview-stat-valid').innerText = validCount;
            document.getElementById('preview-stat-duplicate').innerText = dupCount;
            document.getElementById('preview-stat-invalid').innerText = invalidCount;

            document.getElementById('count-tab-all').innerText = totalCount;
            document.getElementById('count-tab-valid').innerText = validCount;
            document.getElementById('count-tab-invalid_domain').innerText = invalidCount;
            document.getElementById('count-tab-duplicate').innerText = dupCount;
            document.getElementById('preview-filename-subtitle').innerText = `File: ${previewState.filename} • Tinjau & centang data sebelum disimpan`;

            const alertBanner = document.getElementById('preview-duplicate-alert');
            const alertText = document.getElementById('preview-duplicate-alert-text');
            if (alertBanner) {
                if (validCount === 0 && dupCount > 0) {
                    alertBanner.classList.remove('hidden');
                    alertText.innerHTML = `Semua <b>${dupCount}</b> akun pada file ini sudah terdaftar di database (Duplikat otomatis diabaikan).`;
                } else if (dupCount > 0) {
                    alertBanner.classList.remove('hidden');
                    alertText.innerHTML = `<b>${dupCount}</b> data duplikat otomatis diabaikan agar tidak ada akun ganda.`;
                } else {
                    alertBanner.classList.add('hidden');
                }
            }

            // Sync tab active styles
            ['valid', 'invalid_domain', 'duplicate', 'all'].forEach(f => {
                const btn = document.getElementById(`tab-preview-${f}`);
                if (btn) {
                    if (f === previewState.filter) {
                        btn.className = 'px-3 py-1.5 font-bold rounded-lg bg-brand-600 text-white shadow-2xs transition-all';
                    } else {
                        btn.className = 'px-3 py-1.5 font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all';
                    }
                }
            });
        }

        function purgeDuplicatePreviewRows() {
            const initialCount = previewState.rows.length;
            previewState.rows = previewState.rows.filter(r => r.status === 'valid');
            const removedCount = initialCount - previewState.rows.length;

            previewState.rows.forEach((r, idx) => r.origIdx = idx);
            previewState.filter = 'valid';
            updatePreviewStatsCounters();
            renderPreviewTable();

            Swal.fire({
                icon: 'success',
                title: 'Duplikat Dihapus!',
                text: `${removedCount} baris data duplikat & bermasalah berhasil dibuang dari daftar impor.`,
                timer: 1600,
                showConfirmButton: false
            });
        }

        function confirmResetAndReimport() {
            const totalRows = previewState.rawRows ? previewState.rawRows.length : 0;
            Swal.fire({
                title: 'Kosongkan DB & Impor Ulang?',
                html: `Apakah Anda ingin <b>mengosongkan data testing di database</b> dan mengimpor ulang <b>${totalRows}</b> akun dari file ini?<br><br><span class="text-xs text-slate-500">*6 Akun master sistem (Admin, Dosen, Ka.Ur, dll) tetap aman dan tidak terhapus.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Reset & Impor Ulang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mereset Database...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('<?= site_url("import-email/reset_data") ?>', {
                        method: 'POST'
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            state.accounts = res.accounts;
                            renderStats();
                            renderTable();
                            closeImportPreviewModal();
                            // Re-open preview with fresh DB state
                            if (previewState.rawRows && previewState.rawRows.length > 0) {
                                openImportPreviewModal(previewState.rawRows, previewState.filename);
                            }
                        } else {
                            Swal.fire('Gagal Reset', res.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', err.message, 'error'));
                }
            });
        }

        function closeImportPreviewModal() {
            document.getElementById('modal-import-preview').classList.add('hidden');
        }

        function filterPreviewRows(filterType) {
            previewState.filter = filterType;
            updatePreviewStatsCounters();
            renderPreviewTable();
        }

        function renderPreviewTable() {
            const tbody = document.getElementById('preview-table-body');
            let filteredRows = previewState.rows;

            if (previewState.filter === 'valid') {
                filteredRows = previewState.rows.filter(r => r.status === 'valid');
            } else if (previewState.filter === 'invalid_domain') {
                filteredRows = previewState.rows.filter(r => r.status === 'invalid_domain' || r.status === 'invalid_email');
            } else if (previewState.filter === 'duplicate') {
                filteredRows = previewState.rows.filter(r => r.status === 'duplicate');
            }

            if (filteredRows.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400 font-medium">
                            <i class="fa-solid fa-inbox text-2xl text-slate-300 mb-2 block"></i>
                            Tidak ada data untuk kategori ini (semua duplikat telah otomatis diabaikan / dibuang).
                        </td>
                    </tr>
                `;
            } else {
                let html = '';
                filteredRows.forEach((r, idx) => {
                    let badgeHtml = '';
                    if (r.status === 'valid') {
                        badgeHtml = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full"><i class="fa-solid fa-circle-check text-[10px]"></i> Siap Diimpor</span>`;
                    } else if (r.status === 'duplicate') {
                        badgeHtml = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 rounded-full line-through"><i class="fa-solid fa-ban text-[10px]"></i> Duplikat (Auto-Skip)</span>`;
                    } else if (r.status === 'invalid_domain') {
                        badgeHtml = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 rounded-full"><i class="fa-solid fa-circle-xmark text-[10px]"></i> Non-Telkom Domain</span>`;
                    } else {
                        badgeHtml = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 rounded-full"><i class="fa-solid fa-circle-xmark text-[10px]"></i> Format Email Salah</span>`;
                    }

                    html += `
                        <tr class="hover:bg-slate-50 transition-colors ${r.checked ? 'bg-orange-50/30' : ''} ${r.status !== 'valid' ? 'opacity-60 bg-slate-50/50' : ''}">
                            <td class="p-3 text-center">
                                <input type="checkbox" ${r.checked ? 'checked' : ''} onchange="togglePreviewRowCheck(${r.origIdx}, this.checked)" class="rounded text-brand-600 focus:ring-brand-500 cursor-pointer">
                            </td>
                            <td class="p-3 text-center font-mono text-slate-400 text-xs">${idx + 1}</td>
                            <td class="p-3 font-bold text-slate-800">${r.name}</td>
                            <td class="p-3 font-mono text-slate-600">${r.email || '-'}</td>
                            <td class="p-3"><span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 border border-slate-200 rounded text-slate-700">${r.role}</span></td>
                            <td class="p-3 font-mono text-slate-600">${r.nim_nip || '-'}</td>
                            <td class="p-3 text-center">${badgeHtml}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            updatePreviewSelectedSummary();
        }

        function togglePreviewRowCheck(origIdx, isChecked) {
            if (previewState.rows[origIdx]) {
                previewState.rows[origIdx].checked = isChecked;
            }
            updatePreviewSelectedSummary();
            renderPreviewTable();
        }

        function toggleAllPreviewCheckboxes(checkedState) {
            previewState.rows.forEach(r => {
                if (typeof checkedState === 'boolean') {
                    if (checkedState === true) {
                        if (r.status === 'valid') r.checked = true;
                    } else {
                        r.checked = false;
                    }
                }
            });
            const mainCb = document.getElementById('preview-select-all-cb');
            if (mainCb && typeof checkedState === 'boolean') mainCb.checked = checkedState;
            renderPreviewTable();
        }

        function updatePreviewSelectedSummary() {
            const checkedCount = previewState.rows.filter(r => r.checked).length;
            const totalCount = previewState.rows.length;

            document.getElementById('preview-selected-summary').innerText = `${checkedCount} dari ${totalCount} data terpilih untuk diimpor`;
            document.getElementById('btn-submit-import-text').innerText = `Simpan ${checkedCount} Akun Terpilih Ke Database`;

            const btnSubmit = document.getElementById('btn-submit-import-preview');
            if (checkedCount === 0) {
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function submitImportFromPreview() {
            const checkedRows = previewState.rows.filter(r => r.checked);
            if (checkedRows.length === 0) {
                Swal.fire('Tidak Ada Data Terpilih', 'Centang minimal 1 akun valid yang ingin disimpan ke database.', 'warning');
                return;
            }

            closeImportPreviewModal();

            Swal.fire({
                title: 'Menyimpan Ke Database...',
                html: `Mengimpor <b>${checkedRows.length}</b> akun terpilih ke database MySQL...`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= site_url("import-email/import_data") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accounts: checkedRows })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.accounts = res.accounts;
                    renderStats();
                    renderTable();

                    confetti({ particleCount: 60, spread: 70, origin: { y: 0.6 } });

                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        html: `Berhasil mengimpor dan menyimpan <b>${checkedRows.length}</b> akun baru ke database MySQL.`,
                        confirmColor: '#ea580c'
                    });
                } else {
                    Swal.fire('Gagal Import', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Server Error', err.message, 'error'));
        }

        // 3. STATS CALCULATOR
        function renderStats() {
            const total = state.accounts.length;
            const tokenReadyCount = state.accounts.filter(a => a.token_status === 'ready' && a.token.length > 0 && !a.password_changed).length;
            const pwdChangedCount = state.accounts.filter(a => a.password_changed).length;
            const sentCount = state.accounts.filter(a => a.email_status === 'terkirim').length;
            const pendingCount = state.accounts.filter(a => a.email_status !== 'terkirim').length;

            const dosenCount = state.accounts.filter(a => a.role === 'Dosen').length;
            const mhsCount = state.accounts.filter(a => a.role === 'Mahasiswa').length;
            const laboranCount = state.accounts.filter(a => a.role === 'Laboran').length;
            const kaurCount = state.accounts.filter(a => a.role === 'Ka. Ur' || a.role === 'Kepala Urusan').length;
            const koorCount = state.accounts.filter(a => a.role === 'Koordinator TA').length;
            const adminCount = state.accounts.filter(a => a.role === 'Admin').length;

            const tokenPct = total > 0 ? Math.round(((tokenReadyCount + pwdChangedCount) / total) * 100) : 0;
            const sentPct = total > 0 ? Math.round((sentCount / total) * 100) : 0;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-total-desc').innerText = `${dosenCount} Dosen, ${mhsCount} Mhs, ${koorCount} Koor.TA, ${kaurCount} Ka.Ur, ${laboranCount} Laboran`;

            document.getElementById('stat-token').innerHTML = `${tokenReadyCount + pwdChangedCount} <span class="text-xs font-semibold text-cyan-600 font-normal">(${tokenPct}%)</span>`;
            document.getElementById('stat-token-desc').innerText = `${tokenReadyCount} token ready, ${pwdChangedCount} custom password (locked)`;

            document.getElementById('stat-sent').innerHTML = `${sentCount} <span class="text-xs font-semibold text-emerald-600 font-normal">(${sentPct}%)</span>`;
            document.getElementById('stat-sent-desc').innerText = `${sentCount} email berhasil dikirim`;

            document.getElementById('stat-pending').innerText = pendingCount;
        }

        // 4. UNIFIED MULTI-SEARCH EVALUATOR (Kalender Style)
        const textCategories = ['query', 'name', 'nim_nip', 'email_addr', 'token_code'];

        function isTextCategory(cat) {
            return textCategories.includes(cat);
        }

        function getActiveFilterCriteria() {
            let criteria = [];

            // 1. Main Search Pill Criterion
            const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'query';
            let mainVal = '';
            if (isTextCategory(mainCat)) {
                mainVal = document.getElementById('mainSearchInput') ? document.getElementById('mainSearchInput').value.trim() : '';
            } else {
                mainVal = document.getElementById('mainCustomSelectVal') ? document.getElementById('mainCustomSelectVal').value : '';
            }
            if (mainVal) criteria.push({ type: mainCat, val: mainVal });

            // 2. Extra Filter Rows Criteria
            document.querySelectorAll('.extra-filter-row').forEach(row => {
                const rowId = row.id.replace('extraRow_', '');
                const cat = document.getElementById('extraCatSelect_' + rowId) ? document.getElementById('extraCatSelect_' + rowId).value : 'query';
                let val = '';
                if (isTextCategory(cat)) {
                    val = document.getElementById('extraInput_' + rowId) ? document.getElementById('extraInput_' + rowId).value.trim() : '';
                } else {
                    val = document.getElementById('extraValueVal_' + rowId) ? document.getElementById('extraValueVal_' + rowId).value : '';
                }
                if (val) criteria.push({ type: cat, val: val });
            });

            return criteria;
        }

        function getFilteredAccounts() {
            const activeFilters = getActiveFilterCriteria();

            return state.accounts.filter(acc => {
                for (let filter of activeFilters) {
                    const valLower = filter.val.toLowerCase();
                    if (filter.type === 'query') {
                        const match = acc.name.toLowerCase().includes(valLower) || 
                                      acc.email.toLowerCase().includes(valLower) || 
                                      acc.token.toLowerCase().includes(valLower) || 
                                      acc.nim_nip.toLowerCase().includes(valLower);
                        if (!match) return false;
                    } else if (filter.type === 'name') {
                        if (!acc.name.toLowerCase().includes(valLower)) return false;
                    } else if (filter.type === 'nim_nip') {
                        if (!acc.nim_nip.toLowerCase().includes(valLower)) return false;
                    } else if (filter.type === 'email_addr') {
                        if (!acc.email.toLowerCase().includes(valLower)) return false;
                    } else if (filter.type === 'token_code') {
                        if (!acc.token.toLowerCase().includes(valLower)) return false;
                    } else if (filter.type === 'role') {
                        if (acc.role !== filter.val) return false;
                    } else if (filter.type === 'token') {
                        if (filter.val === 'ready' && (acc.token_status !== 'ready' || acc.password_changed)) return false;
                        if (filter.val === 'empty' && (acc.token_status !== 'empty' || acc.password_changed)) return false;
                        if (filter.val === 'password_changed' && !acc.password_changed) return false;
                    } else if (filter.type === 'email') {
                        if (acc.email_status !== filter.val) return false;
                    }
                }
                return true;
            });
        }

        function renderTable() {
            const tbody = document.getElementById('accounts-table-body');
            const filtered = getFilteredAccounts();

            document.querySelectorAll('.total-rows-count').forEach(el => el.innerText = filtered.length);
            document.querySelectorAll('.page-size-select').forEach(el => el.value = state.pageSize);

            // Pagination slice
            const totalPages = Math.ceil(filtered.length / state.pageSize) || 1;
            if (state.currentPage > totalPages) state.currentPage = totalPages;
            
            const startIdx = (state.currentPage - 1) * state.pageSize;
            const pageData = filtered.slice(startIdx, startIdx + state.pageSize);

            if (pageData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="p-8 text-center text-slate-400">
                            <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300"></i>
                            <p class="font-medium text-xs">Tidak ada data akun yang ditemukan.</p>
                        </td>
                    </tr>
                `;
                renderPagination(totalPages);
                return;
            }

            let html = '';
            pageData.forEach((acc, idx) => {
                const isSelected = state.selectedIds.includes(acc.id);
                const rowNo = startIdx + idx + 1;

                // Role Badge Style (Soft Pastel)
                let roleClass = 'bg-slate-100 text-slate-700 border-slate-200/70';
                if (acc.role === 'Dosen') roleClass = 'bg-blue-50 text-blue-700 border-blue-200/70';
                else if (acc.role === 'Mahasiswa') roleClass = 'bg-amber-50 text-amber-700 border-amber-200/70';
                else if (acc.role === 'Laboran') roleClass = 'bg-purple-50 text-purple-700 border-purple-200/70';
                else if (acc.role === 'Ka. Ur') roleClass = 'bg-indigo-50 text-indigo-700 border-indigo-200/70';
                else if (acc.role === 'Koordinator TA') roleClass = 'bg-teal-50 text-teal-700 border-teal-200/70';
                else if (acc.role === 'Admin') roleClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/70';

                // Token Badge Column
                let tokenHtml = '';
                if (acc.password_changed) {
                    tokenHtml = `
                        <div class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-indigo-50/70 text-indigo-700 border border-indigo-200/70 rounded-lg w-[130px] cursor-pointer hover:bg-indigo-100 transition-colors mx-auto" onclick="showProtectedAccountInfo('${acc.id}')" title="Password diubah mandiri pada ${acc.password_changed_at || 'portal'}">
                            <i class="fa-solid fa-user-lock text-indigo-600 text-xs"></i>
                            <span>Custom Password</span>
                        </div>
                    `;
                } else if (acc.token && acc.token.length > 0) {
                    tokenHtml = `
                        <div class="inline-flex items-center justify-between w-[130px] px-3 py-1.5 bg-slate-50 border border-slate-200/80 rounded-lg text-xs font-mono font-medium text-slate-700 mx-auto">
                            <span class="tracking-wide cursor-pointer font-bold" onclick="copyToClipboard('${acc.token}')" title="Klik untuk Salin Token">${acc.token}</span>
                            <button onclick="copyToClipboard('${acc.token}')" class="text-slate-400 hover:text-slate-700 transition-colors p-0.5 cursor-pointer" title="Salin Token">
                                <i class="fa-regular fa-copy text-xs"></i>
                            </button>
                        </div>
                    `;
                } else {
                    tokenHtml = `
                        <button onclick="generateIndividualToken('${acc.id}')" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-all w-[130px] mx-auto cursor-pointer">
                            <i class="fa-solid fa-bolt text-brand-600 text-xs"></i>
                            <span>Generate</span>
                        </button>
                    `;
                }

                // Status Token Column
                let tokenStatusBadge = '';
                if (acc.password_changed) {
                    tokenStatusBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[100px] py-1 text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/70 rounded-full cursor-pointer hover:bg-indigo-100 transition-colors mx-auto" onclick="showProtectedAccountInfo('${acc.id}')" title="Password diubah oleh pengguna">
                        <i class="fa-solid fa-lock text-[10px]"></i> Protected
                    </span>`;
                } else if (acc.token_status === 'ready') {
                    tokenStatusBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[100px] py-1 text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/70 rounded-full mx-auto">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i> Ready
                    </span>`;
                } else {
                    tokenStatusBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[100px] py-1 text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200/70 rounded-full mx-auto">
                        <i class="fa-solid fa-circle-minus text-slate-400 text-[10px]"></i> Kosong
                    </span>`;
                }

                // Email Status Badge
                let emailBadge = '';
                if (acc.email_status === 'terkirim') {
                    const sentTime = (acc.email_sent_at && acc.email_sent_at.includes(' ')) ? acc.email_sent_at.split(' ')[1].slice(0, 5) : (acc.email_sent_at && acc.email_sent_at !== '-' ? acc.email_sent_at : '');
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/70 rounded-full mx-auto">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i> Terkirim${sentTime ? ' (' + sentTime + ')' : ''}
                    </span>`;
                } else if (acc.email_status === 'mengirim') {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/70 rounded-full animate-pulse mx-auto">
                        <i class="fa-solid fa-spinner fa-spin text-blue-600 text-[10px]"></i> Mengirim...
                    </span>`;
                } else if (acc.email_status === 'gagal') {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/70 rounded-full mx-auto">
                        <i class="fa-solid fa-circle-xmark text-rose-600 text-[10px]"></i> Gagal Kirim
                    </span>`;
                } else {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-slate-100/80 text-slate-600 border border-slate-200/70 rounded-full mx-auto">
                        <i class="fa-regular fa-clock text-slate-400 text-[10px]"></i> Belum Terkirim
                    </span>`;
                }

                html += `
                    <tr class="hover:bg-slate-50/80 transition-colors ${isSelected ? 'bg-orange-50/40' : ''}">
                        <td class="p-4 text-center">
                            <input type="checkbox" value="${acc.id}" ${isSelected ? 'checked' : ''} onchange="toggleSelectRow('${acc.id}', this.checked)" class="rounded text-brand-600 focus:ring-brand-500 cursor-pointer">
                        </td>
                        <td class="p-4 text-center text-slate-500 font-normal text-xs whitespace-nowrap">${rowNo}</td>
                        <td class="p-4 whitespace-nowrap">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                        <span>${acc.name}</span>
                                        ${acc.password_changed ? '<i class="fa-solid fa-circle-check text-emerald-500 text-xs" title="Password diubah mandiri oleh pengguna"></i>' : ''}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-normal">
                                        <span>${acc.email}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-medium border rounded-md ${roleClass}">${acc.role}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-center font-normal text-xs text-slate-600 whitespace-nowrap">${acc.nim_nip || '-'}</td>
                        <td class="p-4 text-center whitespace-nowrap">${tokenHtml}</td>
                        <td class="p-4 text-center whitespace-nowrap">${tokenStatusBadge}</td>
                        <td class="p-4 text-center whitespace-nowrap">${emailBadge}</td>
                        <td class="p-4 text-center text-slate-500 font-normal text-xs whitespace-nowrap">${acc.date_imported || '-'}</td>
                        <td class="p-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="sendIndividualEmail('${acc.id}')" class="p-1.5 ${acc.password_changed ? 'text-slate-300 hover:text-indigo-600 hover:bg-indigo-50' : 'text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50'} rounded-md transition-colors cursor-pointer" title="${acc.password_changed ? 'Akun Telah Mengubah Password (Protected)' : 'Kirim Email Akun Ini'}">
                                    <i class="fa-solid ${acc.password_changed ? 'fa-user-shield' : 'fa-paper-plane'} text-sm"></i>
                                </button>
                                <button onclick="openEditAccountModal('${acc.id}')" class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors cursor-pointer" title="Edit Akun">
                                    <i class="fa-regular fa-pen-to-square text-sm"></i>
                                </button>
                                <button onclick="deleteSingleAccount('${acc.id}')" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-md transition-colors cursor-pointer" title="Hapus Akun">
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            renderPagination(totalPages);
            updateSelectedCounter();
        }

        function renderPagination(totalPages) {
            let html = `
                <button onclick="goToPage(${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''} class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-600 hover:bg-slate-200 disabled:opacity-40">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === state.currentPage) {
                    html += `<button class="px-2.5 py-1 text-xs font-bold rounded bg-brand-600 text-white">${i}</button>`;
                } else {
                    html += `<button onclick="goToPage(${i})" class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-600 hover:bg-slate-200">${i}</button>`;
                }
            }

            html += `
                <button onclick="goToPage(${state.currentPage + 1})" ${state.currentPage === totalPages || totalPages === 0 ? 'disabled' : ''} class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-600 hover:bg-slate-200 disabled:opacity-40">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            `;

            const topCtrl = document.querySelector('.pagination-controls-top');
            if (topCtrl) topCtrl.innerHTML = html;

            const bottomCtrl = document.querySelector('.pagination-controls-bottom');
            if (bottomCtrl) bottomCtrl.innerHTML = html;
        }

        function goToPage(page) {
            if (page < 1) return;
            state.currentPage = page;
            renderTable();
        }

        function changePageSize(size) {
            state.pageSize = parseInt(size);
            state.currentPage = 1;
            renderTable();
        }

        // 5. UNIFIED MULTI-SEARCH HANDLERS (Kalender Style)
        let extraRowCounter = 0;

        function showExtraCard() {
            const extraCard = document.getElementById('extraRowsCard');
            if (extraCard) extraCard.style.display = 'block';
        }

        function hideExtraCard() {
            const extraCard = document.getElementById('extraRowsCard');
            if (extraCard) extraCard.style.display = 'none';
        }

        function isExtraCardVisible() {
            const extraCard = document.getElementById('extraRowsCard');
            return extraCard && (extraCard.style.display === 'block' || window.getComputedStyle(extraCard).display !== 'none');
        }

        function handleUnifiedMultiSearch() {
            state.currentPage = 1;
            renderTable();
        }

        function updateFilterBadge() {
            const totalRows = document.querySelectorAll('.extra-filter-row').length + 1;
            const badge = document.getElementById('filterCountBadge');
            if (badge) badge.innerText = `${totalRows}/4`;
        }

        function toggleCustomDropdown(type, event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('menu-filter-' + type);
            const arrow = document.getElementById('arrow-filter-' + type);
            const isHidden = menu ? menu.classList.contains('hidden') : true;

            closeAllCustomDropdowns();

            if (menu && isHidden) {
                menu.classList.remove('hidden');
                if (arrow) arrow.classList.add('rotate-180');

                const parentRow = menu.closest('.extra-filter-row');
                if (parentRow) parentRow.classList.add('open-dropdown');

                const parentContainer = menu.closest('.custom-dropdown-container');
                if (parentContainer) parentContainer.classList.add('open');
            }
        }

        function closeAllCustomDropdowns() {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
            document.querySelectorAll('.extra-filter-row').forEach(r => r.classList.remove('open-dropdown'));
            document.querySelectorAll('.custom-dropdown-container').forEach(c => c.classList.remove('open'));
        }

        function toggleOrAddFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            closeAllCustomDropdowns();

            const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;

            if (extraRowsCount > 0 && !isExtraCardVisible()) {
                showExtraCard();
                return;
            }

            if (extraRowsCount >= 3) {
                const extraCard = document.getElementById('extraRowsCard');
                if (extraCard && extraCard.style.display === 'block') {
                    hideExtraCard();
                } else {
                    showExtraCard();
                }
                return;
            }

            addAdditionalFilterRow(e);
        }

        function getPlaceholderForCategory(cat) {
            if (cat === 'name') return 'Cari nama lengkap pengguna (misal: Budi)...';
            if (cat === 'nim_nip') return 'Cari NIM / NIP / ID (misal: 1301210045)...';
            if (cat === 'email_addr') return 'Cari email Telkom (misal: @student.telkomuniversity.ac.id)...';
            if (cat === 'token_code') return 'Cari 8-char kode token (misal: X8#kP2w!)...';
            return 'Cari Nama, Email, Token, NIM...';
        }

        function addAdditionalFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;
            if (extraRowsCount >= 3) {
                Swal.fire({
                    icon: 'info',
                    title: 'Maksimal 4 Filter',
                    text: 'Maksimal 4 kriteria filter pencarian yang dapat aktif secara bersamaan.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            showExtraCard();

            extraRowCounter++;
            const rowId = extraRowCounter;

            const allCriteria = ['query', 'name', 'nim_nip', 'email_addr', 'role', 'token', 'email'];
            const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'query';
            const usedCriteria = [mainCat];
            document.querySelectorAll('.extra-cat-select').forEach(el => usedCriteria.push(el.value));

            const defaultCrit = allCriteria.find(c => !usedCriteria.includes(c)) || 'role';

            let defaultLabel = '👤 Peran / Role';
            if (defaultCrit === 'name') defaultLabel = '🏷️ Nama Lengkap';
            else if (defaultCrit === 'nim_nip') defaultLabel = '🆔 NIM / NIP / ID';
            else if (defaultCrit === 'email_addr') defaultLabel = '📧 Email Telkom';
            else if (defaultCrit === 'token') defaultLabel = '⚡ Status Token';
            else if (defaultCrit === 'email') defaultLabel = '✉️ Status Email';
            else if (defaultCrit === 'query') defaultLabel = '🔍 Kata Kunci (Semua)';

            const container = document.getElementById('additionalFilterRowsContainer');
            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row';
            rowDiv.id = `extraRow_${rowId}`;

            rowDiv.innerHTML = `
                <div class="unified-search-pill">
                    <!-- Extra Category Dropdown -->
                    <div class="relative custom-dropdown-container">
                        <input type="hidden" id="extraCatSelect_${rowId}" class="extra-cat-select" value="${defaultCrit}">
                        <button type="button" onclick="toggleCustomDropdown('extra-cat-${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-brand-600 focus:outline-none">
                            <span id="label-filter-extra-cat-${rowId}">${defaultLabel}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-cat-${rowId}"></i>
                        </button>
                        <div id="menu-filter-extra-cat-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectExtraCategory(${rowId}, 'query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'query' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>🔍 Kata Kunci (Semua)</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'query' ? '' : 'hidden'}"></i>
                            </div>
                            <div onclick="selectExtraCategory(${rowId}, 'name', '🏷️ Nama Lengkap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'name' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>🏷️ Nama Lengkap</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'name' ? '' : 'hidden'}"></i>
                            </div>
                            <div onclick="selectExtraCategory(${rowId}, 'nim_nip', '🆔 NIM / NIP / ID', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nim_nip' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>🆔 NIM / NIP / ID</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'nim_nip' ? '' : 'hidden'}"></i>
                            </div>
                            <div onclick="selectExtraCategory(${rowId}, 'email_addr', '📧 Email Telkom', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'email_addr' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>📧 Email Telkom</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'email_addr' ? '' : 'hidden'}"></i>
                            </div>
                            <div onclick="selectExtraCategory(${rowId}, 'role', '👤 Peran / Role', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'role' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>👤 Peran / Role</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'role' ? '' : 'hidden'}"></i>
                            </div>
                            <div onclick="selectExtraCategory(${rowId}, 'token', '⚡ Status Token', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'token' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>⚡ Status Token</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'token' ? '' : 'hidden'}"></i>
                            </div>
                            <div onclick="selectExtraCategory(${rowId}, 'email', '✉️ Status Email', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'email' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                                <span>✉️ Status Email</span>
                                <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'email' ? '' : 'hidden'}"></i>
                            </div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <!-- Extra Text Input Container -->
                    <div id="extraValueContainer_${rowId}" class="${isTextCategory(defaultCrit) ? 'flex-1 flex items-center relative' : 'hidden flex-1 items-center relative'}">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="extraInput_${rowId}" oninput="handleUnifiedMultiSearch()" placeholder="${getPlaceholderForCategory(defaultCrit)}" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
                    </div>

                    <!-- Extra Custom Select Container -->
                    <div id="extraCustomSelectWrap_${rowId}" class="${!isTextCategory(defaultCrit) ? 'flex-1 relative custom-dropdown-container' : 'hidden flex-1 relative custom-dropdown-container'}">
                        <input type="hidden" id="extraValueVal_${rowId}" value="">
                        <button type="button" onclick="toggleCustomDropdown('extra-val-${rowId}', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-none">
                            <span id="label-filter-extra-val-${rowId}" class="flex items-center gap-1.5 truncate">Pilih opsi...</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-val-${rowId}"></i>
                        </button>
                        <div id="menu-filter-extra-val-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Remove Row Button (x) -->
                <button type="button" onclick="removeExtraFilterRow(${rowId}, event)" class="btn-remove-row" title="Hapus Baris Filter Ini">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            `;

            container.appendChild(rowDiv);
            updateExtraValueOptions(rowId, defaultCrit);
            updateFilterBadge();
            handleUnifiedMultiSearch();
        }

        function removeExtraFilterRow(rowId, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const row = document.getElementById(`extraRow_${rowId}`);
            if (row) row.remove();

            updateFilterBadge();

            const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;
            if (extraRowsCount === 0) {
                hideExtraCard();
            }

            handleUnifiedMultiSearch();
        }

        function selectMainCategory(cat, label, el) {
            document.getElementById('mainCategorySelect').value = cat;
            document.getElementById('label-filter-main-cat').innerText = label;

            const textWrap = document.getElementById('mainValueContainer');
            const selectWrap = document.getElementById('mainCustomSelectWrap');
            const mainInput = document.getElementById('mainSearchInput');

            if (isTextCategory(cat)) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
                selectWrap.classList.add('hidden');
                if (mainInput) mainInput.placeholder = getPlaceholderForCategory(cat);
            } else {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
                selectWrap.classList.remove('hidden');
                updateMainSelectOptions(cat);
            }

            closeAllCustomDropdowns();
            handleUnifiedMultiSearch();
        }

        function updateMainSelectOptions(cat) {
            const menu = document.getElementById('menu-filter-main-select');
            const label = document.getElementById('label-filter-main-select');
            const valInput = document.getElementById('mainCustomSelectVal');
            valInput.value = '';

            let html = '';
            if (cat === 'role') {
                label.innerText = 'Semua Role';
                html = `
                    <div onclick="selectMainSelectVal('', 'Semua Role', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Role</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                    <div onclick="selectMainSelectVal('Dosen', 'Dosen', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Dosen</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('Mahasiswa', 'Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Mahasiswa</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('Laboran', 'Laboran', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Laboran</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('Ka. Ur', 'Ka. Ur', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> Ka. Ur</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('Koordinator TA', 'Koordinator TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-500"></span> Koordinator TA</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('Admin', 'Admin', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Admin</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                `;
            } else if (cat === 'token') {
                label.innerText = 'Semua Status Token';
                html = `
                    <div onclick="selectMainSelectVal('', 'Semua Status Token', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status Token</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                    <div onclick="selectMainSelectVal('ready', 'Ready (Ada Token)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-cyan-500 text-xs"></i> Ready (Ada Token)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('empty', 'Belum Generated', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-minus text-slate-400 text-xs"></i> Belum Generated</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('password_changed', '🔒 Password Diubah', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-indigo-500 text-xs"></i> Password Diubah (Protected)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                `;
            } else if (cat === 'email') {
                label.innerText = 'Semua Status Email';
                html = `
                    <div onclick="selectMainSelectVal('', 'Semua Status Email', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status Email</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                    <div onclick="selectMainSelectVal('terkirim', 'Terkirim', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i> Terkirim</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('belum', 'Belum Terkirim', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-clock text-slate-400 text-xs"></i> Belum Terkirim</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectMainSelectVal('gagal', 'Gagal Kirim', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i> Gagal Kirim</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                `;
            }

            if (menu) menu.innerHTML = html;
        }

        function selectMainSelectVal(val, labelText, el) {
            document.getElementById('mainCustomSelectVal').value = val;
            document.getElementById('label-filter-main-select').innerText = labelText;

            const menu = document.getElementById('menu-filter-main-select');
            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(i => {
                    i.classList.remove('bg-orange-50', 'text-brand-600');
                    i.classList.add('text-slate-700');
                    const c = i.querySelector('.check-icon');
                    if (c) c.classList.add('hidden');
                });
            }

            if (el) {
                el.classList.add('bg-orange-50', 'text-brand-600');
                el.classList.remove('text-slate-700');
                const c = el.querySelector('.check-icon');
                if (c) c.classList.remove('hidden');
            }

            closeAllCustomDropdowns();
            handleUnifiedMultiSearch();
        }

        function selectExtraCategory(rowId, cat, label, el) {
            document.getElementById(`extraCatSelect_${rowId}`).value = cat;
            document.getElementById(`label-filter-extra-cat-${rowId}`).innerText = label;

            const textWrap = document.getElementById(`extraValueContainer_${rowId}`);
            const selectWrap = document.getElementById(`extraCustomSelectWrap_${rowId}`);
            const inputEl = document.getElementById(`extraInput_${rowId}`);

            if (isTextCategory(cat)) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
                selectWrap.classList.add('hidden');
                if (inputEl) inputEl.placeholder = getPlaceholderForCategory(cat);
            } else {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
                selectWrap.classList.remove('hidden');
                updateExtraValueOptions(rowId, cat);
            }

            closeAllCustomDropdowns();
            handleUnifiedMultiSearch();
        }

        function updateExtraValueOptions(rowId, cat) {
            const menu = document.getElementById(`menu-filter-extra-val-${rowId}`);
            const label = document.getElementById(`label-filter-extra-val-${rowId}`);
            const valInput = document.getElementById(`extraValueVal_${rowId}`);
            valInput.value = '';

            let html = '';
            if (cat === 'role') {
                label.innerText = 'Semua Role';
                html = `
                    <div onclick="selectExtraVal(${rowId}, '', 'Semua Role', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Role</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'Dosen', 'Dosen', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Dosen</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'Mahasiswa', 'Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Mahasiswa</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'Laboran', 'Laboran', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Laboran</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'Ka. Ur', 'Ka. Ur', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> Ka. Ur</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'Koordinator TA', 'Koordinator TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-500"></span> Koordinator TA</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'Admin', 'Admin', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Admin</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                `;
            } else if (cat === 'token') {
                label.innerText = 'Semua Status Token';
                html = `
                    <div onclick="selectExtraVal(${rowId}, '', 'Semua Status Token', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status Token</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'ready', 'Ready (Ada Token)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-cyan-500 text-xs"></i> Ready (Ada Token)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'empty', 'Belum Generated', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-minus text-slate-400 text-xs"></i> Belum Generated</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'password_changed', '🔒 Password Diubah', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-indigo-500 text-xs"></i> Password Diubah (Protected)</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                `;
            } else if (cat === 'email') {
                label.innerText = 'Semua Status Email';
                html = `
                    <div onclick="selectExtraVal(${rowId}, '', 'Semua Status Email', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status Email</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'terkirim', 'Terkirim', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i> Terkirim</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'belum', 'Belum Terkirim', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-clock text-slate-400 text-xs"></i> Belum Terkirim</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                    <div onclick="selectExtraVal(${rowId}, 'gagal', 'Gagal Kirim', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i> Gagal Kirim</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                `;
            }

            if (menu) menu.innerHTML = html;
        }

        function selectExtraVal(rowId, val, labelText, el) {
            document.getElementById(`extraValueVal_${rowId}`).value = val;
            document.getElementById(`label-filter-extra-val-${rowId}`).innerText = labelText;

            const menu = document.getElementById(`menu-filter-extra-val-${rowId}`);
            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(i => {
                    i.classList.remove('bg-orange-50', 'text-brand-600');
                    i.classList.add('text-slate-700');
                    const c = i.querySelector('.check-icon');
                    if (c) c.classList.add('hidden');
                });
            }

            if (el) {
                el.classList.add('bg-orange-50', 'text-brand-600');
                el.classList.remove('text-slate-700');
                const c = el.querySelector('.check-icon');
                if (c) c.classList.remove('hidden');
            }

            closeAllCustomDropdowns();
            handleUnifiedMultiSearch();
        }

        function resetImportMultiSearch() {
            document.getElementById('mainCategorySelect').value = 'query';
            document.getElementById('label-filter-main-cat').innerText = 'Cari Kata Kunci';

            const textWrap = document.getElementById('mainValueContainer');
            const selectWrap = document.getElementById('mainCustomSelectWrap');
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
            selectWrap.classList.add('hidden');

            const mainInput = document.getElementById('mainSearchInput');
            if (mainInput) mainInput.value = '';

            document.getElementById('additionalFilterRowsContainer').innerHTML = '';
            hideExtraCard();

            updateFilterBadge();
            handleUnifiedMultiSearch();
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#multiSearchWrapper')) {
                closeAllCustomDropdowns();
            }
        });

        // 6. SELECTION HANDLERS
        function toggleSelectAll(checkbox) {
            const filtered = getFilteredAccounts();
            if (checkbox.checked) {
                state.selectedIds = filtered.map(a => a.id);
            } else {
                state.selectedIds = [];
            }
            renderTable();
        }

        function toggleSelectRow(id, checked) {
            if (checked) {
                if (!state.selectedIds.includes(id)) state.selectedIds.push(id);
            } else {
                state.selectedIds = state.selectedIds.filter(item => item !== id);
            }
            updateSelectAllState();
        }

        function updateSelectedCounter() {
            document.querySelectorAll('.selected-rows-count').forEach(counter => {
                if (state.selectedIds.length > 0) {
                    counter.innerText = `(${state.selectedIds.length} terpilih)`;
                    counter.classList.remove('hidden');
                } else {
                    counter.classList.add('hidden');
                }
            });
        }

        function updateSelectAllState() {
            const filtered = getFilteredAccounts();
            const selectAllCb = document.getElementById('select-all-cb');
            if (selectAllCb) {
                selectAllCb.checked = filtered.length > 0 && filtered.every(a => state.selectedIds.includes(a.id));
            }
            updateSelectedCounter();
        }

        // 7. INDIVIDUAL & BULK TOKEN GENERATION
        function generateIndividualToken(id) {
            const acc = state.accounts.find(a => a.id == id);
            if (!acc) return;

            Swal.fire({
                title: 'Generasi Token...',
                html: `Membuat token 8 karakter untuk <b>${acc.name}</b>...`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= site_url("import-email/generate_tokens") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_ids: [id] })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.accounts = res.accounts;
                    renderStats();
                    renderTable();
                    Swal.fire('Token Dibuat!', res.message, 'success');
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Server Error', err.message, 'error'));
        }

        function bulkGenerateTokenSelected() {
            if (state.selectedIds.length === 0) {
                Swal.fire('Pilih Akun', 'Silakan centang minimal satu akun untuk generate token.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Generasi Token...',
                html: `Membuat token 8 karakter untuk ${state.selectedIds.length} akun terpilih...`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= site_url("import-email/generate_tokens") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_ids: state.selectedIds })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.accounts = res.accounts;
                    renderStats();
                    renderTable();
                    confetti({ particleCount: 50, spread: 60 });
                    Swal.fire('Token Generated!', res.message, 'success');
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Server Error', err.message, 'error'));
        }

        function bulkGenerateTokenAll() {
            const emptyAccounts = state.accounts.filter(a => (!a.token || a.token.length === 0) && !a.password_changed);
            if (emptyAccounts.length === 0) {
                Swal.fire('Semua Sudah Memiliki Token', 'Seluruh akun telah memiliki token atau mengubah password.', 'info');
                return;
            }

            Swal.fire({
                title: 'Bulk Generate Token...',
                html: `Membuat token untuk ${emptyAccounts.length} akun yang belum memiliki token...`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= site_url("import-email/generate_tokens") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_ids: emptyAccounts.map(a => a.id) })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.accounts = res.accounts;
                    renderStats();
                    renderTable();
                    confetti({ particleCount: 70, spread: 80 });
                    Swal.fire('Bulk Token Success!', res.message, 'success');
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Server Error', err.message, 'error'));
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                toast.fire({
                    icon: 'success',
                    title: `Token [ ${text} ] disalin ke clipboard!`
                });
            });
        }

        // 8. EMAIL DISPATCHER & MAILTO CLIENT INTEGRATION
        function triggerMailtoEmail(acc) {
            if (!acc || !acc.email) return;
            const tokenStr = acc.token || (acc.password_changed ? '[CUSTOM PASSWORD]' : '');
            let subject = state.emailTemplate ? state.emailTemplate.subject : '[IFIK Telkom University] Token Akses Portal Akun Anda: {TOKEN}';
            let body = state.emailTemplate ? state.emailTemplate.body : 'Halo {NAMA},\n\nBerikut adalah Kode Token Akses 8-Karakter unik Anda: {TOKEN}';

            subject = subject
                .replace(/{TOKEN}/g, tokenStr)
                .replace(/{NAMA}/g, acc.name)
                .replace(/{ROLE}/g, acc.role)
                .replace(/{EMAIL}/g, acc.email)
                .replace(/{NIM_NIP}/g, acc.nim_nip || '-');

            body = body
                .replace(/{TOKEN}/g, tokenStr)
                .replace(/{NAMA}/g, acc.name)
                .replace(/{ROLE}/g, acc.role)
                .replace(/{EMAIL}/g, acc.email)
                .replace(/{NIM_NIP}/g, acc.nim_nip || '-');

            const mailtoUrl = `mailto:${encodeURIComponent(acc.email)}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = mailtoUrl;
        }

        function sendIndividualEmail(id) {
            const acc = state.accounts.find(a => a.id == id);
            if (!acc) return;
            triggerMailtoEmail(acc);
            startEmailDispatchSimulation([acc]);
        }

        function bulkSendEmailSelected() {
            if (state.selectedIds.length === 0) {
                Swal.fire('Pilih Akun', 'Silakan centang minimal satu akun untuk pengiriman email.', 'warning');
                return;
            }

            const targetAccounts = state.accounts.filter(a => state.selectedIds.includes(a.id));
            startEmailDispatchSimulation(targetAccounts);
        }

        function startEmailDispatchSimulation(targetList) {
            const modal = document.getElementById('modal-send-progress');
            const subTitle = document.getElementById('send-modal-subtitle');
            const progressStatus = document.getElementById('send-progress-status');
            const progressPercent = document.getElementById('send-progress-percent');
            const progressBar = document.getElementById('send-progress-bar');
            const terminalLog = document.getElementById('send-terminal-log');
            const closeBtn = document.getElementById('send-modal-close-btn');
            const countSummary = document.getElementById('send-count-summary');

            modal.classList.remove('hidden');
            subTitle.innerText = `Mengirim email ke ${targetList.length} akun...`;
            progressPercent.innerText = '0%';
            progressBar.style.width = '0%';
            closeBtn.disabled = true;
            closeBtn.className = 'px-4 py-2 text-xs font-semibold text-white bg-slate-400 cursor-not-allowed rounded-lg transition-all';
            terminalLog.innerHTML = `<div class="text-slate-500">[SYSTEM] Connecting to SMTP Server & MySQL Database...</div>`;

            // Call Backend API to update DB
            fetch('<?= site_url("import-email/send_emails") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_ids: targetList.map(a => a.id) })
            })
            .then(res => res.json())
            .then(res => {
                let current = 0;
                const total = targetList.length;

                const interval = setInterval(() => {
                    if (current >= total) {
                        clearInterval(interval);
                        if (res.accounts) state.accounts = res.accounts;

                        progressPercent.innerText = '100%';
                        progressBar.style.width = '100%';
                        progressStatus.innerText = 'Selesai! Seluruh email telah diproses dan tersimpan ke database.';
                        countSummary.innerText = `${total} / ${total} Terkirim`;

                        terminalLog.innerHTML += `<div class="text-emerald-400 font-bold">[SUCCESS] Dispatch completed! Database updated cleanly.</div>`;
                        terminalLog.scrollTop = terminalLog.scrollHeight;

                        closeBtn.disabled = false;
                        closeBtn.className = 'px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm cursor-pointer';

                        renderStats();
                        renderTable();
                        confetti({ particleCount: 80, spread: 90 });
                        return;
                    }

                    const acc = targetList[current];
                    current++;
                    const pct = Math.round((current / total) * 100);
                    progressPercent.innerText = pct + '%';
                    progressBar.style.width = pct + '%';
                    progressStatus.innerText = `Mengirim ke: ${acc.email}`;
                    countSummary.innerText = `${current} / ${total} Diproses`;

                    terminalLog.innerHTML += `
                        <div class="text-slate-300">
                            <span class="text-slate-500">[${new Date().toLocaleTimeString()}]</span>
                            <span class="text-cyan-400">EMAIL DISPATCHED</span> -> 
                            <strong class="text-white">${acc.email}</strong> 
                            <span class="text-amber-300">[Token: ${acc.token || 'AUTOGEN'}]</span>
                        </div>
                    `;
                    terminalLog.scrollTop = terminalLog.scrollHeight;
                }, 300);
            })
            .catch(err => {
                terminalLog.innerHTML += `<div class="text-rose-400">[ERROR] ${err.message}</div>`;
                closeBtn.disabled = false;
                closeBtn.className = 'px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm cursor-pointer';
            });
        }

        function closeSendProgressModal() {
            document.getElementById('modal-send-progress').classList.add('hidden');
        }

        // 9. EMAIL TEMPLATE MODAL & LIVE PREVIEW
        function openEmailTemplateModal() {
            document.getElementById('modal-template').classList.remove('hidden');
            document.getElementById('template-subject').value = state.emailTemplate.subject;
            document.getElementById('template-body').value = state.emailTemplate.body;
            updateEmailPreview();
        }

        function closeEmailTemplateModal() {
            document.getElementById('modal-template').classList.add('hidden');
        }

        function initEmailTemplateFields() {
            const subjInput = document.getElementById('template-subject');
            const bodyInput = document.getElementById('template-body');

            if (subjInput && bodyInput) {
                subjInput.addEventListener('input', updateEmailPreview);
                bodyInput.addEventListener('input', updateEmailPreview);
            }
        }

        function insertTag(tag) {
            const bodyInput = document.getElementById('template-body');
            bodyInput.value += tag;
            updateEmailPreview();
        }

        function updateEmailPreview() {
            const subjVal = document.getElementById('template-subject').value;
            const bodyVal = document.getElementById('template-body').value;

            // Replace mock tags with sample values
            let renderedBody = bodyVal
                .replace(/{NAMA}/g, '<strong>Budi Santoso</strong>')
                .replace(/{EMAIL}/g, '<u>budi.santoso@student.telkomuniversity.ac.id</u>')
                .replace(/{TOKEN}/g, '<span class="token-badge px-2 py-0.5 rounded text-cyan-300 font-bold">X8K9P2W4</span>')
                .replace(/{ROLE}/g, '<span class="bg-amber-100 text-amber-800 font-semibold px-2 py-0.5 rounded">Mahasiswa</span>')
                .replace(/{NIM_NIP}/g, '<code>1301210045</code>')
                .replace(/\n/g, '<br>');

            let renderedSubj = subjVal.replace(/{TOKEN}/g, 'X8K9P2W4');

            document.getElementById('preview-subject-text').innerText = renderedSubj;
            document.getElementById('preview-html-body').innerHTML = renderedBody;
        }

        function saveEmailTemplate() {
            state.emailTemplate.subject = document.getElementById('template-subject').value;
            state.emailTemplate.body = document.getElementById('template-body').value;
            closeEmailTemplateModal();

            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
            toast.fire({
                icon: 'success',
                title: 'Template Email berhasil disimpan!'
            });
        }

        // 10. ADD / EDIT MANUAL ACCOUNT MODAL
        function openAddAccountModal() {
            document.getElementById('modal-account-title').innerText = 'Tambah Akun Manual';
            document.getElementById('account-id').value = '';
            document.getElementById('account-form').reset();
            document.getElementById('acc-token').value = generate8CharToken();
            document.getElementById('modal-account').classList.remove('hidden');
        }

        function openEditAccountModal(id) {
            const acc = state.accounts.find(a => a.id == id);
            if (!acc) return;

            document.getElementById('modal-account-title').innerText = 'Edit Data Akun';
            document.getElementById('account-id').value = acc.id;
            document.getElementById('acc-name').value = acc.name;
            document.getElementById('acc-email').value = acc.email;
            document.getElementById('acc-role').value = acc.role;
            document.getElementById('acc-nim-nip').value = acc.nim_nip || '';
            
            const tokenInput = document.getElementById('acc-token');
            tokenInput.value = acc.password_changed ? '••• Custom Password (Protected) •••' : (acc.token || '');
            tokenInput.disabled = acc.password_changed;
            if (acc.password_changed) {
                tokenInput.classList.add('bg-indigo-50', 'text-indigo-600', 'cursor-not-allowed');
            } else {
                tokenInput.classList.remove('bg-indigo-50', 'text-indigo-600', 'cursor-not-allowed');
            }

            document.getElementById('modal-account').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('modal-account').classList.add('hidden');
        }

        function generateTokenForInput() {
            document.getElementById('acc-token').value = generate8CharToken();
        }

        function saveAccountForm(e) {
            e.preventDefault();
            const name = document.getElementById('acc-name').value.trim();
            const emailInput = document.getElementById('acc-email');
            const email = emailInput.value.trim();
            const role = document.getElementById('acc-role').value;
            const nim_nip = document.getElementById('acc-nim-nip').value.trim();
            const token = document.getElementById('acc-token').value.trim();

            if (!isValidTelkomEmail(email)) {
                emailInput.focus();
                emailInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-300');
                Swal.fire('Domain Email Ditolak!', 'Email harus berektensi @telkomuniversity.ac.id atau @student.telkomuniversity.ac.id', 'error');
                return;
            }

            emailInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-300');

            Swal.fire({
                title: 'Menyimpan Akun...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= site_url("import-email/save_user") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, role, nim_nip, token })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.accounts = res.accounts;
                    closeAccountModal();
                    renderStats();
                    renderTable();
                    Swal.fire('Berhasil!', res.message, 'success');
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Server Error', err.message, 'error'));
        }

        // 11. DELETE HANDLERS
        function deleteSingleAccount(id) {
            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Data akun akan dihapus dari database MySQL.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmColor: '#e11d48'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch('<?= site_url("import-email/delete_users") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user_ids: [id] })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            state.accounts = res.accounts;
                            state.selectedIds = state.selectedIds.filter(i => i !== id);
                            renderStats();
                            renderTable();
                            Swal.fire('Terhapus!', res.message, 'success');
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Server Error', err.message, 'error'));
                }
            });
        }

        function bulkDeleteSelected() {
            if (state.selectedIds.length === 0) {
                Swal.fire('Pilih Akun', 'Centang akun yang ingin dihapus.', 'warning');
                return;
            }

            Swal.fire({
                title: `Hapus ${state.selectedIds.length} Akun?`,
                text: 'Data terpilih akan dihapus permanen dari database MySQL.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua Terpilih',
                confirmColor: '#e11d48'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch('<?= site_url("import-email/delete_users") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user_ids: state.selectedIds })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            state.accounts = res.accounts;
                            state.selectedIds = [];
                            renderStats();
                            renderTable();
                            Swal.fire('Terhapus!', res.message, 'success');
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Server Error', err.message, 'error'));
                }
            });
        }

        // 12. EXPORT & SAMPLE TEMPLATE DOWNLOADERS
        function exportData(format) {
            const dataToExport = state.selectedIds.length > 0 ? 
                state.accounts.filter(a => state.selectedIds.includes(a.id)) : 
                getFilteredAccounts();

            if (dataToExport.length === 0) {
                Swal.fire('Data Kosong', 'Tidak ada data untuk diexport.', 'info');
                return;
            }

            const cleanRows = dataToExport.map((a, idx) => ({
                'No': idx + 1,
                'Nama Lengkap': a.name,
                'Email': a.email,
                'Role': a.role,
                'NIM/NIP': a.nim_nip,
                'Token Access (8-Char)': a.token,
                'Status Token': a.token_status,
                'Status Email': a.email_status,
                'Tgl Email Terkirim': a.email_sent_at,
                'Tgl Import': a.date_imported
            }));

            if (format === 'csv') {
                const csv = Papa.unparse(cleanRows);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', `ifik_export_email_token_${Date.now()}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else if (format === 'xlsx') {
                const worksheet = XLSX.utils.json_to_sheet(cleanRows);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Email & Token Data');
                XLSX.writeFile(workbook, `ifik_export_email_token_${Date.now()}.xlsx`);
            }
        }

        function downloadSampleTemplate(type) {
            const sampleData = [
                { 'Nama': 'Dr. Ir. Ahmad Sudrajat, M.T.', 'Email': 'ahmad.sudrajat@telkomuniversity.ac.id', 'Role': 'Dosen', 'NIM': '197804122005011002' },
                { 'Nama': 'Budi Santoso', 'Email': 'budi.santoso@student.telkomuniversity.ac.id', 'Role': 'Mahasiswa', 'NIM': '1301210045' },
                { 'Nama': 'Siti Rahmawati, S.Kom.', 'Email': 'siti.rahmawati@telkomuniversity.ac.id', 'Role': 'Ka. Ur', 'NIM': '2019080104' },
                { 'Nama': 'Dewi Lestari', 'Email': 'dewi.lestari@student.telkomuniversity.ac.id', 'Role': 'Mahasiswa', 'NIM': '1301210088' },
                { 'Nama': 'Prof. Dr. Hendra Wijaya', 'Email': 'hendra.wijaya@telkomuniversity.ac.id', 'Role': 'Dosen', 'NIM': '196503151990021001' }
            ];

            if (type === 'csv') {
                const csv = Papa.unparse(sampleData);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'template_import_email_telkom.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                const worksheet = XLSX.utils.json_to_sheet(sampleData);
                // Formatting column widths for professional Excel preview
                worksheet['!cols'] = [
                    { wch: 32 }, // Nama
                    { wch: 48 }, // Email
                    { wch: 16 }, // Role
                    { wch: 22 }  // NIM/NIP
                ];
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Template Import');
                XLSX.writeFile(workbook, 'template_import_email_telkom.xlsx');
            }
        }

        // 13. PROTECTED ACCOUNT INFO POPUP
        function showProtectedAccountInfo(id) {
            const acc = state.accounts.find(a => a.id === id);
            if (!acc) return;

            Swal.fire({
                icon: 'info',
                title: '<i class="fa-solid fa-user-lock text-indigo-500"></i> Akun Dilindungi',
                html: `
                    <div class="text-left text-xs space-y-2 mt-2">
                        <p><strong>Nama:</strong> ${acc.name}</p>
                        <p><strong>Email:</strong> ${acc.email}</p>
                        <p><strong>Role:</strong> ${acc.role}</p>
                        <hr class="border-slate-200">
                        <div class="bg-indigo-50 border border-indigo-200 p-3 rounded-lg">
                            <p class="font-bold text-indigo-700"><i class="fa-solid fa-shield-halved mr-1"></i> Status: Password Diubah Mandiri</p>
                            <p class="text-indigo-600 mt-1">Mahasiswa/pengguna ini telah mengubah password awal (token) dengan password buatan sendiri pada <b>${acc.password_changed_at || 'waktu tidak tercatat'}</b>.</p>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg">
                            <p class="font-bold text-amber-700"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Perhatian Admin</p>
                            <p class="text-amber-600 mt-1">Token awal akun ini sudah <b>tidak berlaku lagi</b>. Admin <b>tidak dapat mereset</b> atau mengubah password pengguna ini. Hanya pengguna yang bisa mengelola passwordnya.</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmColor: '#6366f1',
                width: 480
            });
        }
    </script>
</body>
</html>
