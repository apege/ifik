<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
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

        .custom-dropdown-menu {
            z-index: 140 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

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

    <main class="max-w-7xl mx-auto px-4 sm:px-6">

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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Mahasiswa TA</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $totalMhs; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Pengajuan Tugas Akhir</p>
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

            <!-- 2. Menunggu Approval Card (Cyan) -->
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">Menunggu Approval</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $pendingCount; ?> <span class="text-xs font-semibold text-cyan-600 font-normal">(<?= $totalMhs > 0 ? round(($pendingCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu Ditolak / Disetujui</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-clock text-lg"></i>
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

            <!-- 3. Disetujui Card (Emerald) -->
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Disetujui</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $approvedCount; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $totalMhs > 0 ? round(($approvedCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Lanjut ke Ketua KK</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-check-circle text-lg"></i>
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

            <!-- 4. Perlu Revisi Card (Amber) -->
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Perlu Revisi</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $rejectedCount; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Telah Ditolak / Perlu Revisi</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                            <div class="relative p-3.5 rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-circle-xmark text-lg"></i>
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
                            <span id="label-filter-main-cat">Cari Kata Kunci</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-cat"></i>
                        </button>
                        <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600">
                                <span>🔍 Kata Kunci (Semua)</span>
                                <i class="fa-solid fa-check text-xs check-icon"></i>
                            </div>
                            <div onclick="selectMainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>🏷️ Nama Mahasiswa</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>🆔 NIM Mahasiswa</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>📖 Judul Tugas Akhir</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('konsentrasi', '🎯 Bidang / Peminatan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>🎯 Bidang / Peminatan</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('status', '⚡ Status Approval', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>⚡ Status Approval</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                            <div onclick="selectMainCategory('tahap', '🔄 Tahap Saat Ini', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">
                                <span>🔄 Tahap Saat Ini</span>
                                <i class="fa-solid fa-check text-xs check-icon hidden"></i>
                            </div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <!-- Main Text Input Container -->
                    <div id="mainValueContainer" class="flex-1 flex items-center relative">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                        <input type="text" id="mainSearchInput" oninput="handleUnifiedMultiSearch()" placeholder="Cari Nama, NIM, Judul TA, Tahap..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800">
                    </div>

                    <!-- Main Custom Select Dropdown Container (When Category == status or tahap) -->
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

            <!-- Row 2: Page Size & Records Count (Exact Import Akun Style) -->
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

        <!-- Table Container Card -->
        <div class="card-custom overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                            <th class="py-3.5 px-5 pl-6">NIM</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa</th>
                            <th class="py-3.5 px-5">Usulan Judul TA (Utama)</th>
                            <th class="py-3.5 px-5">Status Approval</th>
                            <th class="py-3.5 px-5">Tahap Saat Ini</th>
                            <th class="py-3.5 px-5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tableBodyMhs">
                        <!-- Injected via renderTable() -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-slate-500">
                    Menampilkan <span id="pageStart" class="font-bold text-slate-800">1</span> - <span id="pageEnd" class="font-bold text-slate-800">10</span> dari <span id="totalRecords" class="font-bold text-slate-800">0</span> records
                </div>
                <div class="flex items-center gap-1.5" id="paginationNav">
                    <!-- Buttons generated dynamically -->
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <!-- Unified Multi-Search & Pagination JS Engine (Exact Copy from import_email.php) -->
    <script>
    const state = {
        list: <?= json_encode($list_mahasiswa ?? []); ?>,
        currentPage: 1,
        pageSize: 10,
    };

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
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
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

    function isTextCategory(cat) {
        return cat !== 'status' && cat !== 'tahap';
    }

    function getPlaceholderForCategory(cat) {
        if (cat === 'nama') return 'Cari nama mahasiswa (misal: Budi)...';
        if (cat === 'nim') return 'Cari NIM (misal: 1301210045)...';
        if (cat === 'judul') return 'Cari topik atau judul TA...';
        if (cat === 'konsentrasi') return 'Cari bidang/konsentrasi (misal: AI, Cyber)...';
        return 'Cari Nama, NIM, Judul TA, Tahap...';
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

        const allCriteria = ['query', 'nama', 'nim', 'judul', 'konsentrasi', 'status', 'tahap'];
        const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'query';
        const usedCriteria = [mainCat];
        document.querySelectorAll('.extra-cat-select').forEach(el => usedCriteria.push(el.value));

        const defaultCrit = allCriteria.find(c => !usedCriteria.includes(c)) || 'status';

        let defaultLabel = '⚡ Status Approval';
        if (defaultCrit === 'nama') defaultLabel = '🏷️ Nama Mahasiswa';
        else if (defaultCrit === 'nim') defaultLabel = '🆔 NIM Mahasiswa';
        else if (defaultCrit === 'judul') defaultLabel = '📖 Judul Tugas Akhir';
        else if (defaultCrit === 'konsentrasi') defaultLabel = '🎯 Bidang / Peminatan';
        else if (defaultCrit === 'tahap') defaultLabel = '🔄 Tahap Saat Ini';
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
                        <div onclick="selectExtraCategory(${rowId}, 'nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nama' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                            <span>🏷️ Nama Mahasiswa</span>
                            <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'nama' ? '' : 'hidden'}"></i>
                        </div>
                        <div onclick="selectExtraCategory(${rowId}, 'nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'nim' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                            <span>🆔 NIM Mahasiswa</span>
                            <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'nim' ? '' : 'hidden'}"></i>
                        </div>
                        <div onclick="selectExtraCategory(${rowId}, 'judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'judul' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                            <span>📖 Judul Tugas Akhir</span>
                            <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'judul' ? '' : 'hidden'}"></i>
                        </div>
                        <div onclick="selectExtraCategory(${rowId}, 'konsentrasi', '🎯 Bidang / Peminatan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'konsentrasi' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                            <span>🎯 Bidang / Peminatan</span>
                            <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'konsentrasi' ? '' : 'hidden'}"></i>
                        </div>
                        <div onclick="selectExtraCategory(${rowId}, 'status', '⚡ Status Approval', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'status' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                            <span>⚡ Status Approval</span>
                            <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'status' ? '' : 'hidden'}"></i>
                        </div>
                        <div onclick="selectExtraCategory(${rowId}, 'tahap', '🔄 Tahap Saat Ini', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${defaultCrit === 'tahap' ? 'active bg-orange-50 text-brand-600' : 'text-slate-700 hover:bg-orange-50 hover:text-brand-600'}">
                            <span>🔄 Tahap Saat Ini</span>
                            <i class="fa-solid fa-check text-xs check-icon ${defaultCrit === 'tahap' ? '' : 'hidden'}"></i>
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
            if (mainInput) {
                mainInput.placeholder = getPlaceholderForCategory(cat);
            }
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
        if (cat === 'status') {
            label.innerText = 'Semua Status';
            html = `
                <div onclick="selectMainSelectVal('', 'Semua Status', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectMainSelectVal('Pending', 'Pending (Menunggu)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainSelectVal('Approved', 'Approved (Disetujui)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainSelectVal('Rejected', 'Rejected (Ditolak)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Rejected</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        } else if (cat === 'tahap') {
            label.innerText = 'Semua Tahap';
            html = `
                <div onclick="selectMainSelectVal('', 'Semua Tahap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Tahap</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectMainSelectVal('Dosen Wali', 'Dosen Wali', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Dosen Wali</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainSelectVal('Admin Layanan', 'Admin Layanan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Admin Layanan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainSelectVal('Koordinator TA', 'Koordinator TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Koordinator TA</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainSelectVal('Ketua KK', 'Ketua KK', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Ketua KK</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectMainSelectVal('Selesai Approval', 'Selesai Approval', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Selesai Approval</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
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
        if (cat === 'status') {
            label.innerText = 'Semua Status';
            html = `
                <div onclick="selectExtraVal(${rowId}, '', 'Semua Status', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Status</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Pending', 'Pending', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Pending</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Approved', 'Approved', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Rejected', 'Rejected', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Rejected</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
            `;
        } else if (cat === 'tahap') {
            label.innerText = 'Semua Tahap';
            html = `
                <div onclick="selectExtraVal(${rowId}, '', 'Semua Tahap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-brand-600"><span>Semua Tahap</span><i class="fa-solid fa-check text-xs check-icon"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Dosen Wali', 'Dosen Wali', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Dosen Wali</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Admin Layanan', 'Admin Layanan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Admin Layanan</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Koordinator TA', 'Koordinator TA', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Koordinator TA</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Ketua KK', 'Ketua KK', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Ketua KK</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
                <div onclick="selectExtraVal(${rowId}, 'Selesai Approval', 'Selesai Approval', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600"><span>Selesai Approval</span><i class="fa-solid fa-check text-xs check-icon hidden"></i></div>
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
        
        const mainInput = document.getElementById('mainSearchInput');
        if (mainInput) {
            mainInput.value = '';
            mainInput.placeholder = 'Cari Nama, NIM, Judul TA, Tahap...';
        }

        const textWrap = document.getElementById('mainValueContainer');
        const selectWrap = document.getElementById('mainCustomSelectWrap');
        if (textWrap) {
            textWrap.classList.remove('hidden');
            textWrap.classList.add('flex-1', 'flex');
        }
        if (selectWrap) selectWrap.classList.add('hidden');

        document.getElementById('mainCustomSelectVal').value = '';

        const container = document.getElementById('additionalFilterRowsContainer');
        if (container) container.innerHTML = '';

        hideExtraCard();
        updateFilterBadge();

        // Reset quick filter button styles
        document.querySelectorAll('.btn-quick-filter').forEach(b => {
            b.className = 'btn-quick-filter px-3 py-1 rounded-xl text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 transition';
        });
        const allBtn = document.querySelector('.btn-quick-filter');
        if (allBtn) allBtn.className = 'btn-quick-filter px-3 py-1 rounded-xl text-xs bg-slate-900 text-white font-bold transition shadow-xs';

        state.currentPage = 1;
        renderTable();
    }

    function quickFilterStatus(statusVal, btnEl) {
        document.querySelectorAll('.btn-quick-filter').forEach(b => {
            b.className = 'btn-quick-filter px-3 py-1 rounded-xl text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 transition';
        });
        if (btnEl) btnEl.className = 'btn-quick-filter px-3 py-1 rounded-xl text-xs bg-slate-900 text-white font-bold transition shadow-xs';

        if (statusVal === 'all') {
            document.getElementById('mainCategorySelect').value = 'query';
            document.getElementById('mainCustomSelectVal').value = '';
            document.getElementById('label-filter-main-cat').innerText = 'Cari Kata Kunci';
            const textWrap = document.getElementById('mainValueContainer');
            const selectWrap = document.getElementById('mainCustomSelectWrap');
            if (textWrap) {
                textWrap.classList.remove('hidden');
                textWrap.classList.add('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.add('hidden');
        } else {
            document.getElementById('mainCategorySelect').value = 'status';
            document.getElementById('mainCustomSelectVal').value = statusVal;
            document.getElementById('label-filter-main-cat').innerText = '⚡ Status Approval';
            document.getElementById('label-filter-main-select').innerText = statusVal;
            
            const textWrap = document.getElementById('mainValueContainer');
            const selectWrap = document.getElementById('mainCustomSelectWrap');
            if (textWrap) {
                textWrap.classList.add('hidden');
                textWrap.classList.remove('flex-1', 'flex');
            }
            if (selectWrap) selectWrap.classList.remove('hidden');
        }

        state.currentPage = 1;
        renderTable();
    }

    function changePageSize(size) {
        state.pageSize = parseInt(size) || 10;
        state.currentPage = 1;
        renderTable();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#multiSearchWrapper')) {
            closeAllCustomDropdowns();
        }
    });

    // 5. GET ACTIVE CRITERIA & FILTER
    function getActiveFilterCriteria() {
        const criteria = [];

        // 1. Main Search Bar Criteria
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

    function getFilteredMahasiswa() {
        const activeFilters = getActiveFilterCriteria();

        return state.list.filter(mhs => {
            const nim = (mhs.nim || '').toLowerCase();
            const nama = ((mhs.nama_depan || '') + ' ' + (mhs.nama_belakang || '')).toLowerCase();
            const judul = (mhs.judul_1 || '').toLowerCase();
            const status = (mhs.status_approval_koor || 'Pending').toLowerCase();
            const stage = (mhs.current_stage || 'Koordinator TA').toLowerCase();
            const prodi = (mhs.konsentrasi_dkv || 'Informatika').toLowerCase();

            for (let filter of activeFilters) {
                const valLower = filter.val.toLowerCase();
                if (filter.type === 'query') {
                    const match = nim.includes(valLower) || 
                                  nama.includes(valLower) || 
                                  judul.includes(valLower) || 
                                  status.includes(valLower) || 
                                  stage.includes(valLower) || 
                                  prodi.includes(valLower);
                    if (!match) return false;
                } else if (filter.type === 'nama') {
                    if (!nama.includes(valLower)) return false;
                } else if (filter.type === 'nim') {
                    if (!nim.includes(valLower)) return false;
                } else if (filter.type === 'judul') {
                    if (!judul.includes(valLower)) return false;
                } else if (filter.type === 'konsentrasi') {
                    if (!prodi.includes(valLower)) return false;
                } else if (filter.type === 'status') {
                    if (status !== valLower) return false;
                } else if (filter.type === 'tahap') {
                    if (!stage.includes(valLower)) return false;
                }
            }
            return true;
        });
    }

    function renderTable() {
        const tbody = document.getElementById('tableBodyMhs');
        const filtered = getFilteredMahasiswa();

        const totalRecords = filtered.length;
        const totalPages = Math.ceil(totalRecords / state.pageSize) || 1;
        if (state.currentPage > totalPages) state.currentPage = totalPages;

        const startIdx = (state.currentPage - 1) * state.pageSize;
        const endIndex = Math.min(startIdx + state.pageSize, totalRecords);
        const pageData = filtered.slice(startIdx, endIndex);

        // Update Info Bar & Toolbar Counter
        if (document.getElementById('pageStart')) document.getElementById('pageStart').textContent = totalRecords > 0 ? (startIdx + 1) : 0;
        if (document.getElementById('pageEnd')) document.getElementById('pageEnd').textContent = endIndex;
        if (document.getElementById('totalRecords')) document.getElementById('totalRecords').textContent = totalRecords;
        if (document.getElementById('toolbarTotalCount')) document.getElementById('toolbarTotalCount').textContent = totalRecords;

        if (pageData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                        <p class="font-medium text-xs">Tidak ada data pengajuan yang ditemukan.</p>
                    </td>
                </tr>
            `;
            renderPagination(totalPages);
            return;
        }

        let html = '';
        pageData.forEach(mhs => {
            const st = mhs.status_approval_koor || 'Pending';
            let badgeClass = 'bg-amber-50 text-amber-700 border-amber-300';
            if (st === 'Approved') badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-300';
            else if (st === 'Rejected') badgeClass = 'bg-rose-50 text-rose-700 border-rose-300';

            const fullName = `${mhs.nama_depan || ''} ${mhs.nama_belakang || ''}`.trim();
            const judul = mhs.judul_1 || 'Belum Mendaftar';
            const stage = mhs.current_stage || 'Koordinator TA';

            html += `
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-4 px-5 pl-6 font-bold text-slate-900">${mhs.nim}</td>
                    <td class="py-4 px-5 font-semibold text-slate-800">${fullName}</td>
                    <td class="py-4 px-5 text-slate-600 max-w-xs truncate font-normal" title="${judul}">${judul}</td>
                    <td class="py-4 px-5">
                        <span class="px-3 py-1 font-bold text-[11px] rounded-full border shadow-xs inline-block ${badgeClass}">${st}</span>
                    </td>
                    <td class="py-4 px-5">
                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 inline-block">${stage}</span>
                    </td>
                    <td class="py-4 px-5 pr-6 text-right">
                        <a href="<?= site_url('koordinatorta/detail_mahasiswa/'); ?>${mhs.nim}" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md shadow-orange-600/20 inline-flex items-center gap-1.5 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i> Detail & Approval
                        </a>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const navContainer = document.getElementById('paginationNav');
        if (!navContainer) return;
        navContainer.innerHTML = '';

        if (totalPages <= 1) return;

        // First Button
        const btnFirst = document.createElement('button');
        btnFirst.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnFirst.innerHTML = '&laquo; Awal';
        btnFirst.disabled = (state.currentPage === 1);
        btnFirst.addEventListener('click', () => goToPage(1));
        navContainer.appendChild(btnFirst);

        // Prev Button
        const btnPrev = document.createElement('button');
        btnPrev.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnPrev.innerHTML = '&lsaquo; Prev';
        btnPrev.disabled = (state.currentPage === 1);
        btnPrev.addEventListener('click', () => goToPage(state.currentPage - 1));
        navContainer.appendChild(btnPrev);

        // Numbered Pages
        const maxVisibleButtons = 5;
        let startPage = Math.max(1, state.currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);
        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            const btnPage = document.createElement('button');
            const isActive = (p === state.currentPage);
            btnPage.className = `px-3 py-1 rounded-lg text-xs font-bold transition ${isActive ? 'bg-orange-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'}`;
            btnPage.textContent = p;
            btnPage.addEventListener('click', () => goToPage(p));
            navContainer.appendChild(btnPage);
        }

        // Next Button
        const btnNext = document.createElement('button');
        btnNext.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnNext.innerHTML = 'Next &rsaquo;';
        btnNext.disabled = (state.currentPage === totalPages);
        btnNext.addEventListener('click', () => goToPage(state.currentPage + 1));
        navContainer.appendChild(btnNext);

        // Last Button
        const btnLast = document.createElement('button');
        btnLast.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${state.currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnLast.innerHTML = 'Akhir &raquo;';
        btnLast.disabled = (state.currentPage === totalPages);
        btnLast.addEventListener('click', () => goToPage(totalPages));
        navContainer.appendChild(btnLast);
    }

    function goToPage(page) {
        if (page < 1) return;
        state.currentPage = page;
        renderTable();
    }

    // Initial Render
    document.addEventListener('DOMContentLoaded', () => {
        updateFilterBadge();
        renderTable();
    });
    </script>
</body>
</html>
