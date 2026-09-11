<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
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
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <style>
        @keyframes popInCard {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(24px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        @keyframes fadeInSlideRight {
            0% {
                opacity: 0;
                transform: scale(0.95) translateX(60px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateX(0);
            }
        }
        @keyframes fadeInDownSmooth {
            0% {
                opacity: 0;
                transform: translateY(-16px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-pop-in {
            animation: popInCard 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        .animate-preview-in {
            animation: fadeInSlideRight 1.1s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        .animate-bar-in {
            animation: fadeInDownSmooth 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        .smooth-dock-panel {
            transition: width 1.1s cubic-bezier(0.2, 0.9, 0.2, 1),
                        max-width 1.1s cubic-bezier(0.2, 0.9, 0.2, 1),
                        opacity 0.8s ease,
                        transform 1.1s cubic-bezier(0.2, 0.9, 0.2, 1);
            will-change: width, max-width, opacity, transform;
        }
        .student-card-item {
            will-change: transform;
            transition: box-shadow 0.4s ease, border-color 0.4s ease;
        }
        .preview-card-item {
            will-change: transform, width, max-width, opacity;
        }
        #wrapperPreviewBerkas {
            transition: all 1.1s cubic-bezier(0.2, 0.9, 0.2, 1);
        }
        #wrapperDaftarMhs::-webkit-scrollbar {
            width: 5px;
        }
        #wrapperDaftarMhs::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 9999px;
        }
        #wrapperDaftarMhs::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white relative">

    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Main Container -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-grow">

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">OVERVIEW BIMBINGAN</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Wali</h1>
                <p class="text-slate-600 text-xs mt-1 font-normal">Kelola persetujuan pendaftaran Tugas Akhir mahasiswa bimbingan Anda secara praktis.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2.5 px-3.5 py-2 bg-white/90 rounded-xl border border-orange-200 shadow-xs text-xs">
                    <span class="px-2 py-0.5 bg-orange-100/90 text-orange-700 rounded-md border border-orange-200/80 font-bold text-[11px]"><?= $dosen_info['kode_dosen'] ?? 'DW-001'; ?></span>
                    <span class="text-slate-600 font-semibold">Prodi: <strong class="text-slate-800"><?= $dosen_info['kejuruan'] ?? 'Informatika / DKV'; ?></strong></span>
                </div>
                <div class="px-4 py-2 bg-white/90 rounded-xl border border-orange-200 shadow-xs text-xs font-semibold text-slate-700 flex items-center gap-2 w-fit">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Approval System Active</span>
                </div>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm box-3d">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Stat Summary Cards Grid (3D Claymorphic) -->
        <?php
            $totalMhs = !empty($list_mahasiswa) ? count($list_mahasiswa) : 0;
            $pendingCount = 0;
            $approvedCount = 0;
            $rejectedCount = 0;

            if(!empty($list_mahasiswa)) {
                foreach($list_mahasiswa as $row) {
                    $st = $row['status_approval_wali'] ?? 'Pending';
                    if($st === 'Approved') $approvedCount++;
                    else if($st === 'Rejected') $rejectedCount++;
                    else $pendingCount++;
                }
            }
        ?>

        <!-- Stats Overview Cards (Exact Interactive Design from Import Akun) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- 1. Total Mahasiswa Bimbingan Card -->
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full">
                <div class="h-full flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-brand-500/40 hover:shadow-2xl hover:shadow-brand-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-brand-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-brand-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-brand-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] xl:text-[11px] font-bold uppercase tracking-normal xl:tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors truncate" title="Total Mahasiswa">Total Mahasiswa</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statTotalMhs"><?= $totalMhs; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 truncate">Bimbingan Akademik</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-brand-500/20 blur-md group-hover:blur-lg group-hover:bg-brand-500/30 transition-all"></div>
                            <div class="relative w-11 h-11 flex items-center justify-center rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-md text-brand-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-users text-base"></i>
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
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full">
                <div class="h-full flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] xl:text-[11px] font-bold uppercase tracking-normal xl:tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors truncate" title="Menunggu Approval">Menunggu Approval</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statPendingMhs"><?= $pendingCount; ?> <span class="text-xs font-semibold text-cyan-600 font-normal">(<?= $totalMhs > 0 ? round(($pendingCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 truncate">Perlu Ditolak / Disetujui</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                            <div class="relative w-11 h-11 flex items-center justify-center rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-key text-base"></i>
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
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full">
                <div class="h-full flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] xl:text-[11px] font-bold uppercase tracking-normal xl:tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors truncate" title="Disetujui">Disetujui</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statApprovedMhs"><?= $approvedCount; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $totalMhs > 0 ? round(($approvedCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 truncate">Lanjut ke Admin</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                            <div class="relative w-11 h-11 flex items-center justify-center rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-paper-plane text-base"></i>
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
            <div class="group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full">
                <div class="h-full flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 p-5">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-amber-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-amber-500/10 blur-lg"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 flex items-start justify-between gap-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] xl:text-[11px] font-bold uppercase tracking-normal xl:tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors truncate" title="Perlu Revisi">Perlu Revisi</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statRejectedMhs"><?= $rejectedCount; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 truncate">Telah Ditolak / Perlu Revisi</p>
                        </div>
                        
                        <!-- Glowing Halo Icon -->
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                            <div class="relative w-11 h-11 flex items-center justify-center rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                <i class="fa-solid fa-clock text-base"></i>
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

        <!-- Table Container Card (3D Warm) -->
        <div class="card-3d-warm card-no-hover rounded-2xl border border-orange-200/60 shadow-card-clean overflow-hidden">

            <!-- Table Header -->
            <div class="p-5 border-b border-orange-200/60 flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                        <i class="bi bi-people-fill text-orange-500 text-lg"></i> Daftar Mahasiswa Bimbingan [LIVE UPDATED]
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Pilih mahasiswa untuk meninjau berkas dan melakukan persetujuan massal. [READY]</p>
                </div>
                <!-- Controls -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <div class="flex items-center gap-2 bg-white border border-orange-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-600">
                        <i class="bi bi-list-ul text-slate-400"></i>
                        <span>Tampilkan</span>
                        <select id="recordsPerPage" class="bg-transparent font-bold text-slate-800 outline-none cursor-pointer">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span>records / hal</span>
                    </div>
                    <button id="btnAddFilter" class="btn-3d-orange flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white">
                        <i class="bi bi-plus-lg"></i> Tambah Filter
                        <span id="filterCountBadge" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                    </button>
                    <button id="btnReset" class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-orange-300 bg-white text-xs font-semibold text-slate-600 hover:bg-orange-50 transition">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Filter Panel -->
            <div id="filterPanel" class="border-b border-orange-200/60 bg-orange-50/40 px-5 py-4">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                        <i class="bi bi-funnel-fill"></i> Dynamic Multi-Filter (Maksimal 4 Kriteria)
                    </span>
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold">
                        <span class="text-slate-500 mr-1">Pintas Status:</span>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-slate-800 text-white transition" data-status="all">Semua</button>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition" data-status="Pending">Pending</button>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition" data-status="Approved">Approved</button>
                        <button class="btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition" data-status="Rejected">Rejected</button>
                    </div>
                </div>
                <div id="filterRows" class="space-y-2"></div>
            </div>
            

            
            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase tracking-wider text-[10px] sm:text-[11px] font-black border-b border-slate-800">
                            <th class="py-3 px-2 text-center w-8">
                                <input type="checkbox" id="checkAllStudents" onchange="toggleAllCheckboxesDW(this)" title="Pilih Semua Mahasiswa" class="w-4 h-4 text-orange-600 rounded border-slate-600 focus:ring-orange-500 cursor-pointer">
                            </th>
                            <th class="py-3 px-3">MAHASISWA</th>
                            <th class="py-3 px-3">JUDUL RENCANA TA</th>
                            <th class="py-3 px-3 text-center">STATUS <?= !empty($syarat_berkas) ? count($syarat_berkas) : 4; ?> BERKAS</th>
                            <th class="py-3 px-3 text-center">DOSEN WALI</th>
                            <th class="py-3 px-3 text-center">TAHAP SAAT INI</th>
                            <th class="py-3 px-3 pr-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100/80 font-medium bg-white" id="tableBodyMhs">
                        <?php if(!empty($list_mahasiswa)): ?>
                            <?php foreach($list_mahasiswa as $mhs): ?>
                                <?php 
                                     $st = $mhs['status_approval_wali'] ?? 'Pending';
                                     $badgeStyle = ($st === 'Approved') ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : (($st === 'Rejected') ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-amber-100 text-amber-700 border-amber-300');
                                     $full_name = trim($mhs['nama_depan'] . ' ' . $mhs['nama_belakang']);
                                     if(empty($full_name)) $full_name = 'Mahasiswa ' . $mhs['nim'];
                                     
                                     $ksm_st = $mhs['status_file_ksm'] ?? 'Pending';
                                     $trs_st = $mhs['status_file_transkrip'] ?? 'Pending';
                                     $prn_st = $mhs['status_file_pernyataan'] ?? 'Pending';
                                     $lab_st = $mhs['status_file_bebas_lab'] ?? 'Pending';
                                 ?>
                                <tr class="hover:bg-orange-50/50 transition-all duration-150 mhs-row" data-status="<?= $st; ?>" data-nim="<?= strtolower($mhs['nim']); ?>" data-nama="<?= strtolower($full_name); ?>" data-judul="<?= strtolower($mhs['judul_1'] ?? ''); ?>" data-stage="<?= strtolower($mhs['current_stage'] ?? 'draft'); ?>">
                                    <!-- Checkbox Column -->
                                    <td class="py-3 px-2 text-center whitespace-nowrap">
                                        <input type="checkbox" name="batch_select[]" value="<?= $mhs['nim']; ?>" 
                                               data-name="<?= htmlspecialchars($full_name); ?>" 
                                               onchange="updateBatchBarDW()"
                                               class="student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                    </td>
                                    
                                    <!-- Mahasiswa Info -->
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-orange-100 border border-orange-200 text-orange-600 font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs">
                                                <?= strtoupper(substr($mhs['nama_depan'] ?? 'M', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs mhs-nama"><?= htmlspecialchars($full_name); ?></div>
                                                <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                                                    <span class="mhs-nim"><?= $mhs['nim']; ?></span>
                                                    <?php if(!empty($mhs['mhs_konsentrasi'])): ?>
                                                        <span>•</span>
                                                        <span class="text-orange-600 font-medium"><?= htmlspecialchars($mhs['mhs_konsentrasi']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Judul TA -->
                                    <td class="py-3 px-3 text-slate-700 max-w-[180px] xl:max-w-[220px] leading-snug text-xs">
                                        <div class="line-clamp-2" title="<?= htmlspecialchars($mhs['judul_1'] ?? ''); ?>">
                                            <?= !empty($mhs['judul_1']) ? htmlspecialchars($mhs['judul_1']) : '<span class="text-slate-400 italic font-normal">Belum Mendaftar</span>'; ?>
                                        </div>
                                    </td>

                                    <!-- Status Berkas Dinamis (Ringkasan Jumlah & Singkatan Berkas) -->
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <?php 
                                            $active_sb = !empty($syarat_berkas) ? $syarat_berkas : [
                                                ['kode_berkas' => 'ksm', 'nama_berkas' => 'KSM'],
                                                ['kode_berkas' => 'transkrip', 'nama_berkas' => 'Transkrip'],
                                                ['kode_berkas' => 'pernyataan', 'nama_berkas' => 'Surat Pernyataan'],
                                                ['kode_berkas' => 'bebas_lab', 'nama_berkas' => 'Bebas Lab']
                                            ];
                                            $v_cnt = 0;
                                            $i_cnt = 0;
                                            $p_cnt = 0;
                                            $item_pills = [];

                                            $map_abbr = ['ksm' => 'KSM', 'transkrip' => 'TRS', 'pernyataan' => 'SRT', 'bebas_lab' => 'LAB'];

                                            foreach ($active_sb as $sb) {
                                                $k_code = $sb['kode_berkas'];
                                                $b_st = $mhs['status_file_' . $k_code] ?? 'Pending';
                                                if ($b_st === 'Pending' && !empty($mhs['berkas_map'][$k_code]['status_verifikasi'])) {
                                                    $ver = $mhs['berkas_map'][$k_code]['status_verifikasi'];
                                                    $b_st = ($ver === 'Valid') ? 'Approved' : (($ver === 'Invalid') ? 'Rejected' : 'Pending');
                                                }

                                                if ($b_st === 'Approved') {
                                                    $v_cnt++;
                                                } elseif ($b_st === 'Rejected') {
                                                    $i_cnt++;
                                                } else {
                                                    $p_cnt++;
                                                }

                                                $btn_class = ($b_st === 'Approved') ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : (($b_st === 'Rejected') ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60');
                                                
                                                if (isset($map_abbr[$k_code])) {
                                                    $abbr = $map_abbr[$k_code];
                                                } else {
                                                    $w = preg_split('/[\s_-]+/', trim($sb['nama_berkas']));
                                                    $abbr = strtoupper(substr($w[0], 0, 4));
                                                }

                                                $item_pills[] = [
                                                    'code' => $k_code,
                                                    'name' => $sb['nama_berkas'],
                                                    'abbr' => $abbr,
                                                    'class' => $btn_class
                                                ];
                                            }
                                        ?>
                                        <div class="flex flex-col items-center gap-1.5">
                                            <!-- Ringkasan Status Berkas (Valid, Direvisi, Menunggu) persis Admin LAA -->
                                            <div id="berkas_summary_badges_<?= $mhs['nim']; ?>" class="flex items-center gap-1 flex-wrap justify-center">
                                                <?php if($v_cnt > 0): ?>
                                                    <button type="button" onclick="openStudentBerkasPreview('<?= $mhs['nim']; ?>')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="<?= $v_cnt; ?> Berkas Disetujui/Valid — Klik untuk Lihat Berkas">
                                                        <i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i>
                                                        <span><?= $v_cnt; ?> Valid</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($i_cnt > 0): ?>
                                                    <button type="button" onclick="openStudentBerkasPreview('<?= $mhs['nim']; ?>')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="<?= $i_cnt; ?> Berkas Ditolak/Direvisi — Klik untuk Lihat Berkas">
                                                        <i class="fa-solid fa-circle-xmark text-rose-500 text-[9px]"></i>
                                                        <span><?= $i_cnt; ?> Direvisi</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($p_cnt > 0): ?>
                                                    <button type="button" onclick="openStudentBerkasPreview('<?= $mhs['nim']; ?>')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="<?= $p_cnt; ?> Berkas Menunggu Verifikasi — Klik untuk Lihat Berkas">
                                                        <i class="fa-solid fa-clock text-amber-500 text-[9px]"></i>
                                                        <span><?= $p_cnt; ?> Menunggu</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($v_cnt === 0 && $i_cnt === 0 && $p_cnt === 0): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                        Belum ada berkas
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Singkatan Berkas Persyaratan -->
                                            <div class="inline-flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 text-[10px] font-mono shadow-2xs">
                                                <?php foreach ($item_pills as $idx => $p): ?>
                                                    <button type="button" 
                                                            onclick="openStudentBerkasPreview('<?= $mhs['nim']; ?>', '<?= $p['code']; ?>')" 
                                                            id="badge_doc_<?= $mhs['nim']; ?>_<?= $p['code']; ?>"
                                                            title="Review <?= htmlspecialchars($p['name']); ?> - <?= htmlspecialchars($full_name); ?>"
                                                            class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer <?= $p['class']; ?>">
                                                        <?= $p['abbr']; ?>
                                                    </button>
                                                    <?php if($idx < count($item_pills) - 1): ?>
                                                        <span class="text-slate-300">·</span>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status Approval Dosen Wali -->
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 font-semibold text-[11px] rounded-full border shadow-xs inline-block <?= $badgeStyle; ?>"><?= $st; ?></span>
                                    </td>

                                    <!-- Tahap Saat Ini -->
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block"><?= $mhs['current_stage'] ?? 'Draft'; ?></span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3 px-3 pr-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="<?= site_url('dosen/wali/detail_mahasiswa/' . $mhs['nim']); ?>" 
                                               class="btn-3d-orange inline-flex items-center gap-1 text-white font-bold px-2.5 py-1.5 rounded-xl text-xs"
                                               title="Detail & Approval Mahasiswa">
                                                <i class="bi bi-search text-xs"></i> Detail
                                            </a>
                                            <button type="button" 
                                                    onclick="toggleLihatBerkasPanel('<?= $mhs['nim']; ?>')" 
                                                    id="btn_lihat_berkas_<?= $mhs['nim']; ?>"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 font-bold text-xs transition cursor-pointer shadow-2xs"
                                                    title="Lihat Berkas">
                                                <i class="fa-solid fa-folder-open text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Clean Empty State Row -->
                            <tr>
                                <td colspan="7" class="py-12 text-center bg-white">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-orange-100/80 text-orange-600 flex items-center justify-center text-2xl font-bold box-3d shadow-2xs">
                                            <i class="bi bi-inbox-fill"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <h4 class="text-sm font-bold text-slate-800">Belum Ada Mahasiswa Mengirim Pendaftaran TA</h4>
                                            <p class="text-xs text-slate-500 max-w-md mx-auto">Daftar ini akan otomatis terisi begitu mahasiswa menyelesaikan Formulir Pendaftaran TA (Langkah 6).</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Footer: Info + Pagination -->
            <div class="px-5 py-3.5 border-t border-orange-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-orange-50/30">
                <span id="recordsInfo" class="text-[11px] text-slate-500 font-medium"></span>
                <div id="paginationContainer" class="flex items-center gap-1 text-xs font-semibold"></div>
            </div>
        </div>
    </main>

    <!-- Floating Batch Action Bar -->
    <div id="batchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden items-center gap-3.5 transition-all duration-300 whitespace-nowrap max-w-[95vw]">
        <div class="flex items-center gap-2.5 shrink-0">
            <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="selectedCountBadge">0</span>
            <span class="text-xs font-bold tracking-tight text-slate-200">Mahasiswa Terpilih</span>
        </div>
        
        <div class="h-5 w-px bg-slate-700 shrink-0"></div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Button 1: Popup Batch Review -->
            <button type="button" onclick="openBatchModalDW()" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md flex items-center gap-2 transition-all active:scale-95 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-layer-group text-sm"></i> CEK DOKUMEN MASSAL (POPUP)
            </button>

            <!-- Button 2: Direct Batch Approve -->
            <button type="button" onclick="submitDirectBatchApproveDW()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-check-double"></i> SETUJUI MASSAL (APPROVE)
            </button>

            <!-- Button 3: Uncheck All -->
            <button type="button" onclick="unselectAllStudentsDW()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-xmark"></i> BATAL
            </button>
        </div>
    </div>

    <!-- Multi-Student Batch Review Modal Popup -->
    <div id="batchReviewModalDW" style="display: none;" onclick="if(event.target === this) closeBatchModalDW()" class="fixed inset-0 z-[60] bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-3 sm:p-5 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-6xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
            
            <!-- Modal Header: Multi-Student Summary & Quick Nav Anchors -->
            <div class="p-4 px-6 bg-slate-900 text-white flex flex-col md:flex-row items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-extrabold text-base shadow-md">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            Verifikasi Massal Berkas Mahasiswa (Tampil Semua)
                            <span class="bg-orange-600 text-white px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold" id="modalStudentCounterDW">0 Mahasiswa Terpilih</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Seluruh dokumen dari semua mahasiswa terpilih ditampilkan secara langsung dalam satu halaman scroll.</p>
                    </div>
                </div>

                <!-- Student Quick Jump Anchor Chips -->
                <div id="modalStudentTabsDW" class="flex items-center gap-2 overflow-x-auto max-w-xl py-1 px-2 bg-slate-800/80 rounded-2xl border border-slate-700">
                    <!-- Dynamic tabs -->
                </div>

                <button type="button" onclick="closeBatchModalDW()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Content Body (Stacked View for All Selected Students) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-6 flex-1 bg-slate-100/80" id="batchModalBodyDW">
                <div class="py-16 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500 mb-3 block"></i>
                    Memuat data seluruh mahasiswa terpilih...
                </div>
            </div>

            <!-- Modal Footer Actions Bar -->
            <div class="p-4 px-6 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
                <button type="button" onclick="markAllBatchDWApproved()" class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-check-double"></i> TANDAI SEMUA VALID (APPROVE ALL)
                </button>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeBatchModalDW()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold uppercase tracking-wider transition-all cursor-pointer">
                        BATAL
                    </button>
                    <button type="button" onclick="submitFinalBatchApprovalDW()" class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-xs sm:text-sm font-black uppercase tracking-wider shadow-lg shadow-orange-600/20 flex items-center gap-2 transition-all cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i> SIMPAN &amp; PROSES SEMUA VERIFIKASI MASSAL
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Document PDF Preview Modal -->
    <div id="pdfModalDW" style="display: none;" onclick="if(event.target === this) closePdfModalDW()" class="fixed inset-0 z-[70] bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-3xl max-w-5xl w-full h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="p-4 px-6 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2 truncate" id="pdfModalTitleDW">Pratinjau Dokumen PDF</h3>
                        <p class="text-[11px] text-slate-400 truncate mt-0.5" id="pdfModalSubtitleDW">Memuat tampilan dokumen...</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="pdfModalOpenTabDW" href="#" target="_blank" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer" title="Buka berkas di tab baru">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                        <span class="hidden sm:inline">Buka Tab Baru</span>
                    </a>
                    <button type="button" onclick="closePdfModalDW()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-sm transition-colors cursor-pointer" title="Tutup (ESC)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <!-- Modal PDF Viewer Iframe -->
            <div class="flex-1 bg-slate-100 relative overflow-hidden">
                <iframe id="pdfFrameDW" src="about:blank" class="w-full h-full border-none" title="PDF Viewer"></iframe>
            </div>
            <!-- Modal Footer -->
            <div class="p-3 px-6 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-orange-500 text-xs"></i> 
                    <span>Tekan <strong>ESC</strong> atau klik di luar kotak untuk menutup</span>
                </span>
                <button type="button" onclick="closePdfModalDW()" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer shadow-xs">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Single Document Review Modal (Mirip Admin Layanan dengan Slip Tombol Judul & Skema TA) -->
    <div id="quickDocReviewModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4 bg-slate-900/65 backdrop-blur-md animate-fade-in" tabindex="-1" onclick="if(event.target === this) closeQuickDocReviewModal()">
        <div id="quickDocModalDialog" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden transform transition-all relative" onclick="event.stopPropagation()">
            
            <!-- In-Modal Toast Notification -->
            <div id="quickDocToast" class="absolute top-4 left-1/2 -translate-x-1/2 z-[70] transform transition-all duration-300 -translate-y-16 opacity-0 pointer-events-none">
                <div class="bg-slate-900/95 text-white px-4 py-2 rounded-2xl shadow-2xl flex items-center gap-2.5 border border-slate-700 backdrop-blur-md">
                    <div class="w-5 h-5 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" id="quickDocToastIcon">
                        <i class="fa-solid fa-circle-info text-amber-400"></i>
                    </div>
                    <span class="text-xs font-bold whitespace-nowrap" id="quickDocToastMsg">Pemberitahuan</span>
                </div>
            </div>

            <!-- Modal Header (Icon, Judul Dokumen, Mahasiswa, Tombol Selipan Judul, Status Badge, Tutup X) -->
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/90 flex items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="quickDocIconBox" class="w-10 h-10 rounded-xl bg-orange-100 border border-orange-200 text-orange-600 flex items-center justify-center font-bold text-lg shadow-2xs shrink-0">
                        <i id="quickDocIcon" class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 id="quickDocTitle" class="text-base font-extrabold text-slate-900 leading-tight truncate">Nama Dokumen</h3>
                        <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5 truncate">
                            <span id="quickDocStudent" class="font-semibold text-slate-700">Mahasiswa (NIM)</span>
                        </div>
                    </div>
                </div>

                <!-- Bagian Kanan Header: Status Badge + Tutup X -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- Status Badge -->
                    <div id="quickDocStatusBadge"></div>

                    <!-- Tombol Tutup X -->
                    <button type="button" onclick="closeQuickDocReviewModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center font-bold transition-colors cursor-pointer" title="Tutup Modal (Esc)">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Single Document View: Hanya 1 item aktif) -->
            <div class="p-4 bg-slate-100 flex-grow overflow-hidden flex flex-col relative" onmouseenter="if(window.hideCircleCursor) window.hideCircleCursor();" onmouseleave="if(window.showCircleCursor) window.showCircleCursor();">
                <!-- PDF Viewer (Ketika menampilkan Berkas Persyaratan) -->
                <div id="quickDocFileViewerWrapper" class="w-full flex-grow flex flex-col">
                    <iframe id="quickDocPreviewIframe" src="about:blank" class="w-full h-[55vh] rounded-xl border border-slate-300 bg-white shadow-inner" title="Pratinjau Berkas"></iframe>
                </div>
            </div>

            <!-- Modal Footer (Setujui, Minta Revisi, Netral, Tab Baru, Detail Halaman, Tutup) -->
            <div class="px-6 py-3.5 border-t border-slate-200 bg-white flex flex-col gap-3 shrink-0">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <!-- Action Approval/Revisi/Reset Netral Cepat -->
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="submitQuickSingleVerify('Approved')" id="btnQuickValid" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-circle-check"></i> Setujui (Valid)
                        </button>
                        <button type="button" onclick="toggleQuickRejectBox()" id="btnQuickRejectToggle" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-arrow-rotate-left"></i> Minta Revisi
                        </button>
                        <button type="button" onclick="submitQuickSingleVerify('Pending')" id="btnQuickPending" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-bold border border-slate-300 flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer" title="Kembalikan status ke Netral / Belum Dicek">
                            <i class="fa-solid fa-rotate text-slate-500"></i> Netral (Reset)
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <a id="quickDocBtnTab" href="#" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-300 flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Tab Baru
                        </a>
                        <a id="quickDocBtnDetail" href="#" class="px-3.5 py-2 bg-orange-50 text-orange-700 hover:bg-orange-100 rounded-xl text-xs font-bold border border-orange-200 flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-magnifying-glass"></i> Detail Halaman
                        </a>
                        <button type="button" onclick="closeQuickDocReviewModal()" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Box Catatan Revisi Cepat (Expandable) -->
                <div id="quickRejectBox" class="hidden pt-3 border-t border-slate-100 flex flex-col gap-2 animate-fade-in">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 flex-wrap gap-1">
                        <span class="flex items-center gap-1 text-rose-600"><i class="fa-solid fa-comment-dots"></i> Catatan Revisi untuk Mahasiswa:</span>
                        <div id="quickRejectPresetChips" class="flex gap-1 flex-wrap">
                            <!-- Preset chips injected dynamically -->
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="inputQuickCatatan" placeholder="Ketik alasan revisi..." class="flex-grow text-xs px-3.5 py-2 rounded-xl border border-rose-300 focus:ring-2 focus:ring-rose-500 focus:outline-none bg-rose-50/30">
                        <button type="button" onclick="submitQuickSingleVerify('Rejected')" id="btnSubmitQuickReject" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer whitespace-nowrap flex items-center gap-1">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Revisi
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating Non-Blocking Container: Lihat & Pratinjau Berkas (Layar Luar Tetap Bebas Diklik) -->
    <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-[60] flex items-center justify-center p-3 sm:p-5 gap-4 sm:gap-5 overflow-x-auto" style="display: none;">
        
        <!-- Wrapper Kartu Mahasiswa (Kanan-Kiri saat tanpa preview, Atas-Bawah di Kiri Ujung saat preview aktif) -->
        <div id="wrapperDaftarMhs" class="flex flex-row items-center gap-4 shrink-0 max-h-[92vh] overflow-y-auto">
            <!-- Kartu mahasiswa dirender dinamis di sini -->
        </div>

        <!-- Wrapper Pratinjau Dokumen Berkas (Dapat Menampilkan Hingga 2 Panel Pratinjau Berdampingan) -->
        <div id="wrapperPreviewBerkas" class="flex items-center gap-4 shrink-0 hidden">
            <!-- 1 atau 2 Panel Pratinjau dirender dinamis di sini -->
        </div>

    </div>

    <!-- Toast Notification -->
    <div id="dwToast" class="fixed top-6 right-6 z-[999] transform transition-all duration-300 translate-y-[-150%] opacity-0 pointer-events-none">
        <div class="bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700">
            <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold" id="dwToastIcon">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="text-xs font-bold" id="dwToastMsg">Pemberitahuan</span>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script>
    window.mhsDataMap = <?= json_encode(array_column($list_mahasiswa ?: array(), null, 'nim')); ?>;
    window.SYARAT_BERKAS = <?= json_encode(!empty($syarat_berkas) ? $syarat_berkas : [
        ['kode_berkas' => 'ksm', 'nama_berkas' => 'KSM (Kartu Studi Mahasiswa)'],
        ['kode_berkas' => 'transkrip', 'nama_berkas' => 'Transkrip Nilai Akademik'],
        ['kode_berkas' => 'pernyataan', 'nama_berkas' => 'Surat Pernyataan TA'],
        ['kode_berkas' => 'bebas_lab', 'nama_berkas' => 'Surat Bebas Pinjam Lab']
    ]); ?>;
    let allRows      = Array.from(document.querySelectorAll('.mhs-row'));
    const tableBody  = document.getElementById('tableBodyMhs');
    const filterRows = document.getElementById('filterRows');
    const btnAdd     = document.getElementById('btnAddFilter');
    const btnReset   = document.getElementById('btnReset');
    const badge      = document.getElementById('filterCountBadge');
    const perPageSel = document.getElementById('recordsPerPage');
    const info       = document.getElementById('recordsInfo');
    const pagination = document.getElementById('paginationContainer');
    const checkAll   = document.getElementById('checkAllStudents');

        const COLUMNS = [
            { label: 'Semua Kolom (Pencarian Umum)', value: 'all' },
            { label: 'NIM',      value: 'nim'    },
            { label: 'Nama',     value: 'nama'   },
            { label: 'Judul TA', value: 'judul'  },
            { label: 'Status',   value: 'status' },
            { label: 'Tahap',    value: 'stage'  },
        ];

        let filters      = [];
        let pintasStatus = 'all';
        let currentPage  = 1;
        let perPage      = parseInt(perPageSel.value) || 5;
        let lastDataHash = '';

        function colSelect(selected = 'all') {
            return `<select class="filter-col border border-orange-200 rounded-lg px-2 py-1.5 text-xs font-medium bg-white outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition cursor-pointer">${COLUMNS.map(c => `<option value="${c.value}" ${c.value === selected ? 'selected' : ''}>${c.label}</option>`).join('')}</select>`;
        }

        function addFilterRow(col = 'all', val = '') {
            if (filters.length >= 4) return;
            const filterObj = { col, val };
            filters.push(filterObj);
            const rowNumber = filters.length;

            const div = document.createElement('div');
            div.className = 'filter-row flex items-center gap-2';
            div.innerHTML = `
                <span class="text-[10px] font-bold text-slate-500 w-14 shrink-0">Filter #${rowNumber}:</span>
                ${colSelect(col)}
                <input type="text" placeholder="Ketik kata kunci pencarian..." value="${val}"
                    class="filter-val flex-1 border border-orange-200 rounded-lg px-3 py-1.5 text-xs font-medium bg-white outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition placeholder:text-slate-300">
                ${rowNumber > 1 ? `<button type="button" class="btn-remove-filter w-7 h-7 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-200 transition flex items-center justify-center text-xs cursor-pointer"><i class="bi bi-x-lg"></i></button>` : ''}`;
            filterRows.appendChild(div);

            const colSel = div.querySelector('.filter-col');
            const valInput = div.querySelector('.filter-val');
            const rmBtn = div.querySelector('.btn-remove-filter');

            colSel.addEventListener('change', () => {
                filterObj.col = colSel.value;
                applyAll();
            });

            valInput.addEventListener('input', () => {
                filterObj.val = valInput.value;
                currentPage = 1;
                applyAll();
            });

            if (rmBtn) {
                rmBtn.addEventListener('click', () => {
                    const idx = filters.indexOf(filterObj);
                    if (idx !== -1) filters.splice(idx, 1);
                    rebuildFilterUI();
                    applyAll();
                });
            }

            updateBadge();
            applyAll();
        }

        function rebuildFilterUI() {
            filterRows.innerHTML = '';
            const copy = [...filters];
            filters = [];
            copy.forEach(f => addFilterRow(f.col, f.val));
        }

        function updateBadge() {
            badge.textContent = `${filters.length}/4`;
            btnAdd.style.opacity = filters.length >= 4 ? '0.5' : '1';
            btnAdd.style.pointerEvents = filters.length >= 4 ? 'none' : 'auto';
        }

        function rowMatches(row) {
            if (pintasStatus !== 'all') {
                const rowSt = (row.dataset.status || '').trim().toLowerCase();
                if (rowSt !== pintasStatus.toLowerCase()) return false;
            }
            for (const f of filters) {
                if (!f || !f.val) continue;
                const q = f.val.trim().toLowerCase();
                if (!q) continue;
                let h = '';
                if (f.col === 'all') {
                    h = [
                        row.dataset.nim || '',
                        row.dataset.nama || '',
                        row.dataset.judul || '',
                        row.dataset.status || '',
                        row.dataset.stage || ''
                    ].join(' ');
                } else {
                    h = row.dataset[f.col] || '';
                }
                if (!h.toLowerCase().includes(q)) return false;
            }
            return true;
        }

        function applyAll() {
            allRows = Array.from(document.querySelectorAll('.mhs-row'));
            const visible = allRows.filter(rowMatches);
            const total = visible.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            const start = (currentPage - 1) * perPage;

            allRows.forEach(r => {
                r.style.display = 'none';
            });

            visible.slice(start, start + perPage).forEach(r => {
                r.style.display = '';
            });

            const from = total === 0 ? 0 : start + 1;
            const to = Math.min(start + perPage, total);
            if (info) info.textContent = `Menampilkan ${from}–${to} dari ${total} data`;
            renderPagination(totalPages);
            rebindCheckboxes();
        }

        function renderPagination(totalPages) {
            pagination.innerHTML = '';
            if (totalPages <= 1) return;
            const mk = (label, page, disabled, active) => {
                const btn = document.createElement('button');
                btn.innerHTML = label;
                btn.className = `px-3 py-1.5 rounded-lg border text-[11px] font-semibold transition cursor-pointer ${ active ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : disabled ? 'bg-white text-slate-300 border-slate-200 cursor-not-allowed' : 'bg-white text-slate-600 border-slate-200 hover:bg-orange-50 hover:border-orange-300'}`;
                btn.disabled = disabled;
                if (!disabled && !active) btn.addEventListener('click', () => { currentPage = page; applyAll(); });
                return btn;
            };
            pagination.appendChild(mk('<i class="bi bi-chevron-left"></i>', currentPage - 1, currentPage === 1, false));
            for (let i = 1; i <= totalPages; i++) {
                if (totalPages > 7 && i > 2 && i < totalPages - 1 && Math.abs(i - currentPage) > 1) {
                    if (i === 3 || i === totalPages - 2) { const d = document.createElement('span'); d.textContent = '…'; d.className = 'px-1 text-slate-400'; pagination.appendChild(d); }
                    continue;
                }
                pagination.appendChild(mk(i, i, false, i === currentPage));
            }
            pagination.appendChild(mk('<i class="bi bi-chevron-right"></i>', currentPage + 1, currentPage === totalPages, false));
        }

        document.querySelectorAll('.btn-pintas').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.btn-pintas').forEach(b => { 
                    b.className = 'btn-pintas px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:bg-orange-50 transition text-[11px] font-semibold cursor-pointer'; 
                });
                btn.className = 'btn-pintas px-3 py-1 rounded-full bg-slate-800 text-white transition text-[11px] font-semibold cursor-pointer';
                pintasStatus = btn.dataset.status || 'all';
                currentPage = 1;
                applyAll();
            });
        });

        btnAdd.addEventListener('click', () => addFilterRow());

        btnReset.addEventListener('click', () => {
            filters = [];
            pintasStatus = 'all';
            currentPage = 1;
            filterRows.innerHTML = '';
            document.querySelectorAll('.btn-pintas').forEach((b, i) => {
                b.className = `btn-pintas px-3 py-1 rounded-full transition text-[11px] font-semibold cursor-pointer ${i === 0 ? 'bg-slate-800 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-orange-50'}`;
            });
            perPageSel.value = '5';
            perPage = 5;
            addFilterRow();
        });

        perPageSel.addEventListener('change', () => { 
            perPage = parseInt(perPageSel.value) || 5; 
            currentPage = 1; 
            applyAll(); 
        });

        // Checkbox Batch Handler
        function rebindCheckboxes() {
            const checkAll = document.getElementById('checkAllStudents');
            const studentCbs = document.querySelectorAll('.student-cb');

            studentCbs.forEach(cb => {
                cb.onchange = () => {
                    updateBatchBar();
                    syncCheckAllStatus();
                };
            });

            syncCheckAllStatus();
            updateBatchBar();
        }

        function syncCheckAllStatus() {
            const checkAll = document.getElementById('checkAllStudents');
            if (!checkAll) return;
            const visibleCbs = Array.from(document.querySelectorAll('.student-cb')).filter(cb => {
                const tr = cb.closest('.mhs-row');
                return tr && tr.style.display !== 'none' && !cb.disabled;
            });
            const checkedCount = visibleCbs.filter(c => c.checked).length;
            checkAll.checked = (visibleCbs.length > 0 && checkedCount === visibleCbs.length);
        }

        function toggleAllCheckboxes(masterCb) {
            const isChecked = masterCb ? masterCb.checked : false;
            const studentCbs = document.querySelectorAll('.student-cb');
            studentCbs.forEach(cb => {
                const tr = cb.closest('.mhs-row');
                if (tr && tr.style.display !== 'none') {
                    if (!cb.disabled) cb.checked = isChecked;
                } else if (!isChecked) {
                    cb.checked = false;
                }
            });
            updateBatchBar();
        }
        window.toggleAllCheckboxes = toggleAllCheckboxes;
        window.toggleAllCheckboxesDW = toggleAllCheckboxes;

        function updateBatchBar() {
            const checkedCbs = document.querySelectorAll('.student-cb:checked');
            const batchBar = document.getElementById('batchActionBar');
            const badgeCount = document.getElementById('selectedCountBadge');

            const count = checkedCbs.length;

            if (count > 0) {
                if (badgeCount) badgeCount.textContent = count;
                if (batchBar) {
                    batchBar.classList.remove('hidden');
                    batchBar.classList.add('flex');
                }
            } else {
                if (batchBar) {
                    batchBar.classList.add('hidden');
                    batchBar.classList.remove('flex');
                }
            }
        }
        window.updateBatchBar = updateBatchBar;
        window.updateBatchBarDW = updateBatchBar;

        function unselectAllStudents() {
            document.querySelectorAll('.student-cb').forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('checkAllStudents');
            if (checkAll) checkAll.checked = false;
            updateBatchBar();
        }
        window.unselectAllStudents = unselectAllStudents;
        window.unselectAllStudentsDW = unselectAllStudents;

        // Global Event Delegation untuk Checkbox
        document.addEventListener('change', (e) => {
            if (e.target && e.target.id === 'checkAllStudents') {
                toggleAllCheckboxes(e.target);
            } else if (e.target && e.target.classList.contains('student-cb')) {
                syncCheckAllStatus();
                updateBatchBar();
            }
        });

        // Realtime Polling
        async function pollRealtimeData() {
            try {
                // If modal is currently open, don't re-render table to avoid disrupting review
                const modal = document.getElementById('batchReviewModalDW');
                if (modal && !modal.classList.contains('hidden')) return;
                const quickModal = document.getElementById('quickDocReviewModal');
                if (quickModal && !quickModal.classList.contains('hidden')) return;

                const res = await fetch('<?= site_url("dosenwali/get_mahasiswa_ajax"); ?>', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const json = await res.json();
                if (!json || !json.success) return;

                const currentHash = JSON.stringify(json.data);
                if (currentHash === lastDataHash) return;
                lastDataHash = currentHash;

                // Update Stats
                if (json.stats) {
                    const elTotal = document.getElementById('statTotalMhs');
                    const elPending = document.getElementById('statPendingMhs');
                    const elApproved = document.getElementById('statApprovedMhs');
                    const elRejected = document.getElementById('statRejectedMhs');

                    if (elTotal) elTotal.textContent = json.stats.total;
                    if (elPending) elPending.innerHTML = `${json.stats.pending} <span class="text-xs font-semibold text-cyan-600 font-normal">(${json.stats.total > 0 ? Math.round((json.stats.pending / json.stats.total)*100) : 0}%)</span>`;
                    if (elApproved) elApproved.innerHTML = `${json.stats.approved} <span class="text-xs font-semibold text-emerald-600 font-normal">(${json.stats.approved_pct}%)</span>`;
                    if (elRejected) elRejected.textContent = json.stats.rejected;
                }

                // Preserve checked NIMs
                const currentlyChecked = Array.from(document.querySelectorAll('.student-cb:checked')).map(cb => cb.value);

                // Update Table Rows
                if (json.data && json.data.length > 0) {
                    json.data.forEach(m => {
                        if (m && m.nim) {
                            if (!window.mhsDataMap) window.mhsDataMap = {};
                            if (!window.mhsDataMap[m.nim]) window.mhsDataMap[m.nim] = m;
                            else Object.assign(window.mhsDataMap[m.nim], m);
                        }
                    });
                    tableBody.innerHTML = json.data.map(mhs => {
                        const st = mhs.status_approval_wali || 'Pending';
                        const badgeStyle = (st === 'Approved') 
                            ? 'bg-emerald-100 text-emerald-700 border-emerald-300' 
                            : ((st === 'Rejected') ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-amber-100 text-amber-700 border-amber-300');
                        const judulShort = mhs.judul 
                            ? (mhs.judul.length > 50 ? mhs.judul.substring(0, 50) + '...' : mhs.judul)
                            : '<span class="text-slate-400 italic font-normal">Belum Mendaftar</span>';
                        const isChecked = currentlyChecked.includes(mhs.nim) ? 'checked' : '';

                        // Bangun badge status berkas dinamis & ringkasan jumlah
                        const activeSyaratList = (window.SYARAT_BERKAS && window.SYARAT_BERKAS.length > 0) ? window.SYARAT_BERKAS : [
                            { kode_berkas: 'ksm', nama_berkas: 'KSM' },
                            { kode_berkas: 'transkrip', nama_berkas: 'Transkrip' },
                            { kode_berkas: 'pernyataan', nama_berkas: 'Surat Pernyataan' },
                            { kode_berkas: 'bebas_lab', nama_berkas: 'Bebas Lab' }
                        ];
                        let vCnt = 0, iCnt = 0, pCnt = 0;
                        const berkasPillsHtml = activeSyaratList.map((sb, bIdx) => {
                            const k = sb.kode_berkas;
                            const bSt = mhs[`status_file_${k}`] || (mhs.berkas_map && mhs.berkas_map[k] ? (mhs.berkas_map[k].status_verifikasi === 'Valid' ? 'Approved' : (mhs.berkas_map[k].status_verifikasi === 'Invalid' ? 'Rejected' : 'Pending')) : 'Pending');
                            if (bSt === 'Approved') vCnt++;
                            else if (bSt === 'Rejected') iCnt++;
                            else pCnt++;

                            const bClass = (bSt === 'Approved') ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ((bSt === 'Rejected') ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60');
                            
                            const mapAbbr = { ksm: 'KSM', transkrip: 'TRS', pernyataan: 'SRT', bebas_lab: 'LAB' };
                            let abbr = mapAbbr[k];
                            if (!abbr) {
                                const w = (sb.nama_berkas || k).trim().split(/[\s_-]+/);
                                abbr = (w[0] || 'DOC').substring(0, 4).toUpperCase();
                            }
                            const dot = (bIdx < activeSyaratList.length - 1) ? '<span class="text-slate-300">·</span>' : '';
                            return `<button type="button" onclick="openStudentBerkasPreview('${mhs.nim}', '${k}')" id="badge_doc_${mhs.nim}_${k}" title="Review ${sb.nama_berkas} - ${mhs.nama}" class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer ${bClass}">${abbr}</button>${dot}`;
                        }).join('');

                        let summaryBadgesHtml = '';
                        if (vCnt > 0) {
                            summaryBadgesHtml += `<button type="button" onclick="openStudentBerkasPreview('${mhs.nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${vCnt} Berkas Disetujui/Valid — Klik untuk Lihat Berkas"><i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i> <span>${vCnt} Valid</span></button>`;
                        }
                        if (iCnt > 0) {
                            summaryBadgesHtml += `<button type="button" onclick="openStudentBerkasPreview('${mhs.nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${iCnt} Berkas Ditolak/Direvisi — Klik untuk Lihat Berkas"><i class="fa-solid fa-circle-xmark text-rose-500 text-[9px]"></i> <span>${iCnt} Direvisi</span></button>`;
                        }
                        if (pCnt > 0) {
                            summaryBadgesHtml += `<button type="button" onclick="openStudentBerkasPreview('${mhs.nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${pCnt} Berkas Menunggu Verifikasi — Klik untuk Lihat Berkas"><i class="fa-solid fa-clock text-amber-500 text-[9px]"></i> <span>${pCnt} Menunggu</span></button>`;
                        }
                        if (vCnt === 0 && iCnt === 0 && pCnt === 0) {
                            summaryBadgesHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">Belum ada berkas</span>`;
                        }

                        const prodiHtml = mhs.konsentrasi ? `<span>•</span><span class="text-orange-600 font-medium">${mhs.konsentrasi}</span>` : '';

                        return `
                            <tr class="hover:bg-orange-50/50 transition-all duration-150 mhs-row" data-status="${st}" data-nim="${(mhs.nim || '').toLowerCase()}" data-nama="${(mhs.nama || '').toLowerCase()}" data-judul="${(mhs.judul || '').toLowerCase()}" data-stage="${(mhs.current_stage || 'draft').toLowerCase()}">
                                <td class="py-3 px-2 text-center whitespace-nowrap">
                                    <input type="checkbox" name="batch_select[]" value="${mhs.nim}" data-name="${mhs.nama}" ${isChecked} onchange="updateBatchBarDW()" class="student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-orange-100 border border-orange-200 text-orange-600 font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs">
                                            ${(mhs.nama || 'M').charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs mhs-nama">${mhs.nama}</div>
                                            <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                                                <span class="mhs-nim">${mhs.nim}</span>
                                                ${prodiHtml}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-slate-700 max-w-[180px] xl:max-w-[220px] leading-snug text-xs">
                                    <div class="line-clamp-2" title="${mhs.judul || ''}">
                                        ${judulShort}
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div id="berkas_summary_badges_${mhs.nim}" class="flex items-center gap-1 flex-wrap justify-center">
                                            ${summaryBadgesHtml}
                                        </div>
                                        <div class="inline-flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 text-[10px] font-mono shadow-2xs">
                                            ${berkasPillsHtml}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 font-semibold text-[11px] rounded-full border shadow-xs inline-block ${badgeStyle}">${st}</span>
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block">${mhs.current_stage || 'Draft'}</span>
                                </td>
                                <td class="py-3 px-3 pr-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="${mhs.detail_url}" 
                                           class="btn-3d-orange inline-flex items-center gap-1 text-white font-bold px-2.5 py-1.5 rounded-xl text-xs"
                                           title="Detail & Approval Mahasiswa">
                                            <i class="bi bi-search text-xs"></i> Detail
                                        </a>
                                        <button type="button" 
                                                onclick="toggleLihatBerkasPanel('${mhs.nim}')" 
                                                id="btn_lihat_berkas_${mhs.nim}"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 font-bold text-xs transition cursor-pointer shadow-2xs"
                                                title="Lihat Berkas">
                                            <i class="fa-solid fa-folder-open text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                allRows = Array.from(document.querySelectorAll('.mhs-row'));
                applyAll();
                if (typeof updateTableButtonHighlights === 'function') {
                    updateTableButtonHighlights();
                }
            } catch (err) {
                console.warn('Realtime polling error:', err);
            }
        }

        addFilterRow();
        rebindCheckboxes();
        setInterval(pollRealtimeData, 8000);

    // Batch Action Functions
    window.batchStudentsDW = [];

    function showDWToast(msg, isSuccess = true) {
        const toast = document.getElementById('dwToast');
        const msgEl = document.getElementById('dwToastMsg');
        const iconEl = document.getElementById('dwToastIcon');
        if (!toast) return;

        msgEl.textContent = msg;
        if (isSuccess) {
            iconEl.className = 'w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold';
            iconEl.innerHTML = '<i class="fa-solid fa-check"></i>';
        } else {
            iconEl.className = 'w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center text-xs font-bold';
            iconEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
        }

        toast.classList.remove('translate-y-[-150%]', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-[-150%]', 'opacity-0', 'pointer-events-none');
        }, 4000);
    }

    function submitDirectBatchApproveDW() {
        const checkedCbs = document.querySelectorAll('.student-cb:checked');
        if (checkedCbs.length === 0) return;

        if (!confirm(`Yakin ingin MENYETUJUI (Approve) ${checkedCbs.length} mahasiswa sekaligus?\n\nPengajuan akan otomatis diteruskan ke tahap Admin Layanan (LAA).`)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'approve_all');
        checkedCbs.forEach(cb => formData.append('nims[]', cb.value));

        fetch('<?= site_url("dosenwali/submit_batch_approval"); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            unselectAllStudentsDW();
            showDWToast(res.message || 'Persetujuan massal berhasil disimpan!');
            setTimeout(() => location.reload(), 1200);
        })
        .catch(err => {
            console.error('Batch approve error:', err);
            location.reload();
        });
    }

    function openBatchModalDW() {
        const checkedCbs = document.querySelectorAll('.student-cb:checked');
        if (checkedCbs.length === 0) return;

        const selectedNims = Array.from(checkedCbs).map(cb => cb.value);
        const modal = document.getElementById('batchReviewModalDW');
        const modalBody = document.getElementById('batchModalBodyDW');
        const counter = document.getElementById('modalStudentCounterDW');

        if (counter) counter.textContent = `${selectedNims.length} Mahasiswa Terpilih`;
        modalBody.innerHTML = `
            <div class="py-16 text-center text-slate-400">
                <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500 mb-3 block"></i>
                Memuat data dokumen <strong>${selectedNims.length} mahasiswa</strong> terpilih...
            </div>
        `;
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden'; // Kunci scroll halaman luar

        const formData = new FormData();
        selectedNims.forEach(nim => formData.append('nims[]', nim));

        fetch('<?= site_url("dosenwali/get_batch_details"); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data.length > 0) {
                window.batchStudentsDW = res.data.map(st => {
                    const getInitStatus = (val) => {
                        if (val === 'Approved' || val === 'Rejected') return val;
                        return 'Pending';
                    };

                    const rawJenis = getInitStatus(st.status_jenis_ta);
                    const rawJudul = getInitStatus(st.status_judul);

                    const processedFiles = {};
                    const fileStatuses = [];
                    if (st.files) {
                        Object.keys(st.files).forEach(k => {
                            const fStatus = getInitStatus(st.files[k]?.status);
                            fileStatuses.push(fStatus);
                            processedFiles[k] = {
                                ...st.files[k],
                                status: fStatus,
                                note: st.files[k]?.note || ''
                            };
                        });
                    }

                    const allStatuses = [rawJenis, rawJudul, ...fileStatuses];
                    const hasRej = allStatuses.some(s => s === 'Rejected');
                    const allApp = allStatuses.every(s => s === 'Approved');
                    const initAction = hasRej ? 'reject' : (allApp ? 'approve' : 'pending');

                    return {
                        ...st,
                        action: initAction,
                        status_jenis_ta: rawJenis,
                        catatan_jenis_ta: st.catatan_jenis_ta || '',
                        status_judul: rawJudul,
                        catatan_judul: st.catatan_judul || '',
                        catatan_wali: st.catatan_wali || '',
                        files: processedFiles
                    };
                });
                renderAllBatchStudentsContentDW();
            } else {
                modalBody.innerHTML = `<div class="py-12 text-center text-rose-500 font-bold text-xs">${res.message || 'Gagal memuat data.'}</div>`;
            }
        })
        .catch(err => {
            console.error('Load batch details error:', err);
            modalBody.innerHTML = `<div class="py-12 text-center text-rose-500 font-bold text-xs">Terjadi kesalahan koneksi server.</div>`;
        });
    }

    function closeBatchModalDW() {
        const modal = document.getElementById('batchReviewModalDW');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.body.style.overflow = ''; // Kembalikan scroll halaman luar
    }

    function renderAllBatchStudentsContentDW() {
        const modalBody = document.getElementById('batchModalBodyDW');
        const tabsContainer = document.getElementById('modalStudentTabsDW');
        if (!modalBody || !window.batchStudentsDW) return;

        // Render Quick Anchor Tabs
        if (tabsContainer) {
            tabsContainer.innerHTML = window.batchStudentsDW.map((st, idx) => {
                const items = [st.status_jenis_ta, st.status_judul, ...Object.values(st.files).map(f => f.status)];
                const hasRej = items.some(s => s === 'Rejected');
                const allApp = items.every(s => s === 'Approved');

                let tabClass = 'bg-slate-800 text-slate-300 border-slate-700';
                let iconClass = 'fa-clock text-amber-400';
                if (hasRej) {
                    tabClass = 'bg-rose-600/30 text-rose-300 border-rose-500/40';
                    iconClass = 'fa-xmark text-rose-400';
                } else if (allApp) {
                    tabClass = 'bg-emerald-600/30 text-emerald-300 border-emerald-500/40';
                    iconClass = 'fa-check text-emerald-400';
                }

                return `
                    <a href="#batch_card_${st.nim}" class="px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap transition-all flex items-center gap-1.5 ${tabClass} border">
                        <span>${idx + 1}.</span>
                        <span>${st.nama.split(' ')[0]}</span>
                        <i class="fa-solid ${iconClass} text-[9px]"></i>
                    </a>
                `;
            }).join('');
        }

        const docNames = {
            'ksm': { 
                title: '1. KSM (Kartu Studi Mahasiswa)', 
                short: 'KSM',
                icon: 'fa-file-lines'
            },
            'transkrip': { 
                title: '2. Transkrip Nilai Akademik', 
                short: 'Transkrip',
                icon: 'fa-file-invoice'
            },
            'pernyataan': { 
                title: '3. Surat Pernyataan Mahasiswa', 
                short: 'Surat Pernyataan',
                icon: 'fa-file-signature'
            },
            'bebas_lab': { 
                title: '4. Surat Bebas Lab & Perpustakaan', 
                short: 'Bebas Lab',
                icon: 'fa-building-columns'
            }
        };

        if (window.SYARAT_BERKAS && Array.isArray(window.SYARAT_BERKAS)) {
            window.SYARAT_BERKAS.forEach((sb, idx) => {
                const k = sb.kode_berkas;
                if (!docNames[k]) {
                    docNames[k] = {
                        title: `${idx + 1}. ${sb.nama_berkas}`,
                        short: sb.nama_berkas,
                        icon: 'fa-file-pdf'
                    };
                }
            });
        }

        // Render Student Review Cards
        let html = '';
        window.batchStudentsDW.forEach((st, idx) => {
            const studentItems = [st.status_jenis_ta, st.status_judul, ...Object.values(st.files).map(f => f.status)];
            const hasReject = studentItems.some(s => s === 'Rejected');
            const isAllApprove = studentItems.every(s => s === 'Approved');

            const isJenisApprove = (st.status_jenis_ta === 'Approved');
            const isJenisReject  = (st.status_jenis_ta === 'Rejected');

            const isJudulApprove = (st.status_judul === 'Approved');
            const isJudulReject  = (st.status_judul === 'Rejected');

            // Generate 4 document cards with Pop-up Modal Preview trigger
            let docsHtml = '';
            Object.keys(docNames).forEach(key => {
                const info = docNames[key];
                const fileObj = (st.files && st.files[key]) ? st.files[key] : { name: 'Belum diunggah', url: '', status: 'Pending', note: '' };
                const hasFile = fileObj.url && fileObj.url !== '#' && fileObj.name && fileObj.name !== 'Belum diunggah';
                const isDocApprove = (fileObj.status === 'Approved');
                const isDocReject  = (fileObj.status === 'Rejected');

                let badgeHtml = '<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200"><i class="fa-solid fa-clock text-[9px] mr-1 text-slate-400"></i>Belum Ditinjau</span>';
                if (isDocApprove) {
                    badgeHtml = '<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check text-[9px] mr-1 text-emerald-600"></i>Valid</span>';
                } else if (isDocReject) {
                    badgeHtml = '<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-xmark text-[9px] mr-1 text-rose-600"></i>Kurang/Revisi</span>';
                }

                docsHtml += `
                    <div class="bg-white rounded-2xl p-4 sm:p-5 border ${isDocReject ? 'border-rose-300 bg-rose-50/10' : (isDocApprove ? 'border-emerald-200' : 'border-slate-200')} shadow-xs flex flex-col justify-between space-y-4 transition-all">
                        <div>
                            <!-- Header Doc with Status in Top Right -->
                            <div class="flex items-center justify-between mb-3 gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 font-bold text-xs flex items-center justify-center shrink-0">
                                        <i class="fa-solid ${info.icon}"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm truncate" title="${info.title}">${info.title}</span>
                                </div>
                                ${badgeHtml}
                            </div>

                            <!-- Interactive PDF File Box (Click to Open Pop-up Modal) -->
                            ${hasFile ? `
                                <div onclick="previewDocPdfDW('${fileObj.url}', '${info.title} - ${st.nama}')" 
                                     class="group relative rounded-xl border border-slate-200 bg-gradient-to-r from-slate-50 to-orange-50/20 p-3.5 hover:border-orange-400 hover:bg-orange-50/50 hover:shadow-xs transition-all cursor-pointer flex items-center justify-between gap-3"
                                     title="Klik untuk pratinjau dokumen ${info.title}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate group-hover:text-orange-600 transition-colors" title="${fileObj.name}">
                                                ${fileObj.name}
                                            </p>
                                            <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1 font-medium">
                                                <i class="fa-solid fa-expand text-orange-500 text-[9px]"></i>
                                                <span>Klik untuk pratinjau berkas (Pop-up)</span>
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="event.stopPropagation(); previewDocPdfDW('${fileObj.url}', '${info.title} - ${st.nama}')" 
                                            class="shrink-0 px-3 py-2 rounded-xl bg-white border border-slate-200 group-hover:border-orange-500 group-hover:bg-orange-500 group-hover:text-white text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                        <i class="fa-solid fa-eye text-[11px]"></i>
                                        <span>Lihat PDF</span>
                                    </button>
                                </div>
                            ` : `
                                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/80 p-3.5 flex items-center gap-3 text-slate-400">
                                    <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-400 flex items-center justify-center text-base shrink-0">
                                        <i class="fa-solid fa-file-excel"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-500">Berkas belum diunggah</p>
                                        <p class="text-[10px] text-slate-400">Mahasiswa belum mengunggah berkas ini</p>
                                    </div>
                                </div>
                            `}
                        </div>

                        <!-- Bottom Controls: Valid vs Kurang/Revisi Checkboxes & Conditional Revision Box -->
                        <div class="space-y-3" id="batch_sec_file_${key}_${st.nim}">
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <label class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border font-bold cursor-pointer transition-all ${isDocApprove ? 'bg-emerald-50 text-emerald-700 border-emerald-300 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                                    <input type="checkbox" onchange="setFileDecisionDW('${st.nim}', '${key}', 'approve')" ${isDocApprove ? 'checked' : ''} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                    <span>Valid</span>
                                </label>
                                <label class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border font-bold cursor-pointer transition-all ${isDocReject ? 'bg-rose-50 text-rose-700 border-rose-300 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                                    <input type="checkbox" onchange="setFileDecisionDW('${st.nim}', '${key}', 'reject')" ${isDocReject ? 'checked' : ''} class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    <span>Kurang / Revisi</span>
                                </label>
                            </div>

                            <!-- Per-Document Comment (HANYA MUNCUL KETIKA DI-REJECT) -->
                            ${isDocReject ? `
                                <div class="pt-3 border-t border-rose-200 space-y-1.5 transition-all">
                                    <label class="text-[11px] font-bold text-rose-700 uppercase flex items-center gap-1.5">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>CATATAN REVISI KHUSUS ${info.title.split('.')[1] ? info.title.split('.')[1].trim().toUpperCase() : info.title.toUpperCase()}:</span>
                                    </label>
                                    <input type="text" 
                                           id="batch_note_file_${key}_${st.nim}"
                                           value="${fileObj.note || ''}" 
                                           oninput="updateFileNoteDW('${st.nim}', '${key}', this.value)" 
                                           placeholder="Tuliskan catatan perbaikan spesifik berkas ini..." 
                                           class="w-full px-4 py-2.5 bg-white border border-rose-300 rounded-xl text-xs font-medium text-slate-800 placeholder-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 shadow-2xs">
                                    <p id="batch_err_file_${key}_${st.nim}" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 hidden">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span>Catatan belum ditambahkan. Wajib diisi alasan revisi berkas ini.</span>
                                    </p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            
            let jenisBadgeHtml = '<span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200"><i class="fa-solid fa-clock text-slate-400 mr-1 text-[10px]"></i>Belum Ditinjau</span>';
            if (isJenisApprove) {
                jenisBadgeHtml = '<span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="fa-solid fa-check text-emerald-600 mr-1 text-[10px]"></i>Valid / Disetujui</span>';
            } else if (isJenisReject) {
                jenisBadgeHtml = '<span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300"><i class="fa-solid fa-xmark text-rose-600 mr-1 text-[10px]"></i>Kurang / Revisi</span>';
            }

            let judulBadgeHtml = '<span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200"><i class="fa-solid fa-clock text-slate-400 mr-1 text-[10px]"></i>Belum Ditinjau</span>';
            if (isJudulApprove) {
                judulBadgeHtml = '<span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="fa-solid fa-check text-emerald-600 mr-1 text-[10px]"></i>Valid / Disetujui</span>';
            } else if (isJudulReject) {
                judulBadgeHtml = '<span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300"><i class="fa-solid fa-xmark text-rose-600 mr-1 text-[10px]"></i>Kurang / Revisi</span>';
            }

            let overallBadgeHtml = '';
            if (hasReject) {
                overallBadgeHtml = '<span class="px-3.5 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shadow-xs bg-rose-500 text-white"><i class="fa-solid fa-circle-xmark text-xs"></i> Ada Revisi</span>';
            } else if (isAllApprove) {
                overallBadgeHtml = '<span class="px-3.5 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shadow-xs bg-emerald-500 text-white"><i class="fa-solid fa-circle-check text-xs"></i> Valid / Approved</span>';
            } else {
                overallBadgeHtml = '<span class="px-3.5 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shadow-xs bg-amber-500/90 text-white"><i class="fa-solid fa-clock text-xs"></i> Menunggu Keputusan</span>';
            }

            html += `
                <div id="batch_card_${st.nim}" class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8 transition-all space-y-8">
                    <!-- Student Header Summary -->
                    <div class="bg-slate-900 text-white rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-orange-500 text-white font-black text-base flex items-center justify-center shadow-md shrink-0">
                                ${idx + 1}
                            </div>
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <h4 class="font-extrabold text-white text-sm sm:text-base">${st.nama}</h4>
                                    <span class="px-2.5 py-0.5 rounded-md bg-orange-600/40 text-orange-200 font-mono text-[11px] font-bold border border-orange-500/40">${st.nim}</span>
                                </div>
                                <div class="text-xs text-slate-300 flex items-center gap-2 mt-1">
                                    <span>${st.prodi}</span>
                                    <span>•</span>
                                    <span class="font-bold text-orange-400">${st.kode_kk || 'KK-VCM'}</span>
                                    <span>•</span>
                                    <span class="text-slate-300 font-semibold">${st.jenis_ta || 'Reguler'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge in Top-Right Corner of Header -->
                        <div class="flex items-center gap-3">
                            ${overallBadgeHtml}
                        </div>
                    </div>

                    <!-- SECTION 1: JENIS TUGAS AKHIR -->
                    <div class="rounded-2xl border ${isJenisReject ? 'border-rose-300 bg-rose-50/30' : (isJenisApprove ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50')} p-5 sm:p-6 space-y-4 transition-all shadow-xs" id="batch_sec_jenis_${st.nim}">
                        <!-- Top Header with Status Badge in Right Corner -->
                        <div class="flex items-center justify-between pb-3.5 border-b border-orange-200/60 gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-sm shadow-2xs">
                                    <i class="fa-solid fa-shapes"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm uppercase tracking-wider">1. Jenis &amp; Skema Tugas Akhir</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Tinjau kesesuaian jenis TA dan konsentrasi keilmuan mahasiswa</p>
                                </div>
                            </div>
                            ${jenisBadgeHtml}
                        </div>

                        <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex flex-wrap items-center gap-3">
                            <span class="text-xs font-semibold text-slate-500">Skema Terpilih:</span>
                            <span class="px-3 py-1 rounded-lg bg-orange-100 text-orange-800 font-black text-xs border border-orange-200">${st.jenis_ta || 'Reguler'}</span>
                            <span class="text-slate-300">•</span>
                            <span class="text-xs font-semibold text-slate-500">Kelompok Keahlian:</span>
                            <span class="font-bold text-slate-800 text-xs">${st.nama_kk || st.kode_kk || 'Visual & Communication Media'}</span>
                        </div>

                        <!-- Bottom Controls: Checkboxes & Conditional Comment Box -->
                        <div class="space-y-4 pt-3 border-t border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <label class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border font-bold cursor-pointer transition-all ${isJenisApprove ? 'bg-emerald-50 text-emerald-700 border-emerald-300 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                                    <input type="checkbox" onchange="setJenisDecisionDW('${st.nim}', 'approve')" ${isJenisApprove ? 'checked' : ''} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                    <span>Valid / Disetujui</span>
                                </label>
                                <label class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border font-bold cursor-pointer transition-all ${isJenisReject ? 'bg-rose-50 text-rose-700 border-rose-300 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                                    <input type="checkbox" onchange="setJenisDecisionDW('${st.nim}', 'reject')" ${isJenisReject ? 'checked' : ''} class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    <span>Kurang / Revisi</span>
                                </label>
                            </div>

                            <!-- Comment (HANYA MUNCUL KETIKA DI-REJECT) -->
                            ${isJenisReject ? `
                                <div class="pt-3 border-t border-rose-200 space-y-2 transition-all">
                                    <label class="text-[11px] font-bold text-rose-700 uppercase flex items-center gap-1.5">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>CATATAN REVISI KHUSUS JENIS &amp; SKEMA TA:</span>
                                    </label>
                                    <input type="text" 
                                           id="batch_note_jenis_${st.nim}"
                                           value="${st.catatan_jenis_ta || ''}" 
                                           oninput="updateJenisNoteDW('${st.nim}', this.value)" 
                                           placeholder="Tuliskan catatan perbaikan atau alasan penolakan jenis TA..." 
                                           class="w-full px-4 py-2.5 bg-white border border-rose-300 rounded-xl text-xs font-medium text-slate-800 placeholder-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 shadow-2xs">
                                    <p id="batch_err_jenis_${st.nim}" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 hidden">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span>Catatan belum ditambahkan. Wajib diisi alasan penolakan jenis TA.</span>
                                    </p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- SECTION 2: USULAN JUDUL TUGAS AKHIR -->
                    <div class="rounded-2xl border ${isJudulReject ? 'border-rose-300 bg-rose-50/30' : (isJudulApprove ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50')} p-5 sm:p-6 space-y-4 transition-all shadow-xs" id="batch_sec_judul_${st.nim}">
                        <!-- Top Header with Status Badge in Right Corner -->
                        <div class="flex items-center justify-between pb-3.5 border-b border-orange-200/60 gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-sm shadow-2xs">
                                    <i class="fa-solid fa-heading"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm uppercase tracking-wider">2. Usulan Judul Tugas Akhir</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Tinjau topik judul tugas akhir mahasiswa dan berikan persetujuan</p>
                                </div>
                            </div>
                            ${judulBadgeHtml}
                        </div>

                        <div class="p-4 sm:p-5 rounded-xl bg-white border border-slate-200/80 shadow-2xs space-y-3">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-orange-700 block mb-1">Judul Utama (Judul 1):</span>
                                <p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-relaxed">${st.judul_1 || '<span class="text-slate-400 italic font-normal">Tidak ada judul</span>'}</p>
                            </div>
                            
                            ${st.judul_en ? `
                                <div class="pt-2.5 border-t border-slate-100">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Judul Bahasa Inggris:</span>
                                    <p class="text-xs font-medium text-slate-700 italic">${st.judul_en}</p>
                                </div>
                            ` : ''}

                            ${(st.judul_2 || st.judul_3) ? `
                                <div class="pt-2.5 border-t border-slate-100 flex flex-col sm:flex-row gap-4 text-xs text-slate-600">
                                    ${st.judul_2 ? `<div><span class="font-bold text-[10px] text-slate-500 uppercase">Alternatif 2:</span> ${st.judul_2}</div>` : ''}
                                    ${st.judul_3 ? `<div><span class="font-bold text-[10px] text-slate-500 uppercase">Alternatif 3:</span> ${st.judul_3}</div>` : ''}
                                </div>
                            ` : ''}
                        </div>

                        <!-- Bottom Controls: Checkboxes & Conditional Comment Box -->
                        <div class="space-y-4 pt-3 border-t border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <label class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border font-bold cursor-pointer transition-all ${isJudulApprove ? 'bg-emerald-50 text-emerald-700 border-emerald-300 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                                    <input type="checkbox" onchange="setJudulDecisionDW('${st.nim}', 'approve')" ${isJudulApprove ? 'checked' : ''} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                    <span>Valid / Disetujui</span>
                                </label>
                                <label class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border font-bold cursor-pointer transition-all ${isJudulReject ? 'bg-rose-50 text-rose-700 border-rose-300 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}">
                                    <input type="checkbox" onchange="setJudulDecisionDW('${st.nim}', 'reject')" ${isJudulReject ? 'checked' : ''} class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    <span>Kurang / Revisi</span>
                                </label>
                            </div>

                            <!-- Comment (HANYA MUNCUL KETIKA DI-REJECT) -->
                            ${isJudulReject ? `
                                <div class="pt-3 border-t border-rose-200 space-y-2 transition-all">
                                    <label class="text-[11px] font-bold text-rose-700 uppercase flex items-center gap-1.5">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>CATATAN REVISI KHUSUS USULAN JUDUL TA:</span>
                                    </label>
                                    <input type="text" 
                                           id="batch_note_judul_${st.nim}"
                                           value="${st.catatan_judul || ''}" 
                                           oninput="updateJudulNoteDW('${st.nim}', this.value)" 
                                           placeholder="Tuliskan saran revisi atau alasan penolakan judul TA..." 
                                           class="w-full px-4 py-2.5 bg-white border border-rose-300 rounded-xl text-xs font-medium text-slate-800 placeholder-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 shadow-2xs">
                                    <p id="batch_err_judul_${st.nim}" class="text-[11px] font-bold text-rose-600 flex items-center gap-1 hidden">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span>Catatan belum ditambahkan. Wajib diisi saran/alasan revisi judul TA.</span>
                                    </p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- SECTION 3: 4 BERKAS DOKUMEN PERSYARATAN (Live Embedded + Approve/Reject & Comment Per File) -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-sm shadow-2xs">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm uppercase tracking-wider">3. Berkas Persyaratan PDF</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tinjau dan berikan keputusan validasi serta catatan khusus pada setiap dokumen</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            ${docsHtml}
                        </div>
                    </div>

                    <!-- SECTION 4: RINGKASAN CATATAN DOSEN WALI -->
                    <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200 space-y-3">
                        <label class="text-xs font-bold text-slate-800 block">
                            <i class="fa-solid fa-comment-dots text-orange-600 mr-1.5 text-sm"></i> Ringkasan Catatan Umum Dosen Wali (Terkirim ke Mahasiswa):
                        </label>
                        <textarea rows="3" oninput="updateStudentNoteDW('${st.nim}', this.value)" 
                                  placeholder="Catatan rangkuman umum untuk mahasiswa ini..." 
                                  class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-orange-500 shadow-2xs leading-relaxed">${st.catatan_wali || ''}</textarea>
                    </div>
                </div>
            `;
        });

        modalBody.innerHTML = html;
    }

    function setStudentDecisionDW(nim, decision) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            student.action = decision;
            const targetStatus = (decision === 'approve') ? 'Approved' : 'Rejected';
            student.status_jenis_ta = targetStatus;
            student.status_judul = targetStatus;
            if (decision === 'approve') {
                student.catatan_jenis_ta = '';
                student.catatan_judul = '';
            }
            Object.keys(student.files).forEach(k => {
                student.files[k].status = targetStatus;
                if (decision === 'approve') {
                    student.files[k].note = '';
                }
            });
            syncStudentCatatanWaliDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function setJenisDecisionDW(nim, decision) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            if (decision === 'approve') {
                student.status_jenis_ta = (student.status_jenis_ta === 'Approved') ? 'Pending' : 'Approved';
                if (student.status_jenis_ta === 'Approved') {
                    student.catatan_jenis_ta = '';
                }
            } else if (decision === 'reject') {
                student.status_jenis_ta = (student.status_jenis_ta === 'Rejected') ? 'Pending' : 'Rejected';
            }
            checkStudentOverallStatusDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function updateJenisNoteDW(nim, note) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            student.catatan_jenis_ta = note;
            syncStudentCatatanWaliDW(student);
        }
    }

    function setJenisPresetNoteDW(nim, presetText) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            const current = student.catatan_jenis_ta ? student.catatan_jenis_ta.trim() : '';
            if (!current) {
                student.catatan_jenis_ta = presetText;
            } else if (!current.includes(presetText)) {
                student.catatan_jenis_ta = current + ', ' + presetText;
            }
            syncStudentCatatanWaliDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function setJudulDecisionDW(nim, decision) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            if (decision === 'approve') {
                student.status_judul = (student.status_judul === 'Approved') ? 'Pending' : 'Approved';
                if (student.status_judul === 'Approved') {
                    student.catatan_judul = '';
                }
            } else if (decision === 'reject') {
                student.status_judul = (student.status_judul === 'Rejected') ? 'Pending' : 'Rejected';
            }
            checkStudentOverallStatusDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function updateJudulNoteDW(nim, note) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            student.catatan_judul = note;
            syncStudentCatatanWaliDW(student);
        }
    }

    function setJudulPresetNoteDW(nim, presetText) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            const current = student.catatan_judul ? student.catatan_judul.trim() : '';
            if (!current) {
                student.catatan_judul = presetText;
            } else if (!current.includes(presetText)) {
                student.catatan_judul = current + ', ' + presetText;
            }
            syncStudentCatatanWaliDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function setFileDecisionDW(nim, fileKey, decision) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student && student.files && student.files[fileKey]) {
            if (decision === 'approve') {
                student.files[fileKey].status = (student.files[fileKey].status === 'Approved') ? 'Pending' : 'Approved';
                if (student.files[fileKey].status === 'Approved') {
                    student.files[fileKey].note = '';
                }
            } else if (decision === 'reject') {
                student.files[fileKey].status = (student.files[fileKey].status === 'Rejected') ? 'Pending' : 'Rejected';
            }
            checkStudentOverallStatusDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function updateFileNoteDW(nim, fileKey, note) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student && student.files && student.files[fileKey]) {
            student.files[fileKey].note = note;
            syncStudentCatatanWaliDW(student);
        }
    }

    function setFilePresetNoteDW(nim, fileKey, presetText) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student && student.files && student.files[fileKey]) {
            const current = student.files[fileKey].note ? student.files[fileKey].note.trim() : '';
            if (!current) {
                student.files[fileKey].note = presetText;
            } else if (!current.includes(presetText)) {
                student.files[fileKey].note = current + ', ' + presetText;
            }
            syncStudentCatatanWaliDW(student);
            renderAllBatchStudentsContentDW();
        }
    }

    function checkStudentOverallStatusDW(st) {
        const items = [st.status_jenis_ta, st.status_judul, ...Object.values(st.files).map(f => f.status)];
        const hasReject = items.some(s => s === 'Rejected');
        const allApproved = items.every(s => s === 'Approved');

        if (hasReject) {
            st.action = 'reject';
        } else if (allApproved) {
            st.action = 'approve';
        } else {
            st.action = 'pending';
        }
        syncStudentCatatanWaliDW(st);
    }

    function syncStudentCatatanWaliDW(st) {
        // Catatan umum terpisah murni dari catatan spesifik berkas / judul / jenis TA
    }

    function updateStudentNoteDW(nim, note) {
        const student = window.batchStudentsDW.find(s => s.nim === nim);
        if (student) {
            student.catatan_wali = note;
        }
    }

    function markAllBatchDWApproved() {
        if (!window.batchStudentsDW) return;
        window.batchStudentsDW.forEach(st => {
            st.action = 'approve';
            st.status_jenis_ta = 'Approved';
            st.status_judul = 'Approved';
            st.catatan_jenis_ta = '';
            st.catatan_judul = '';
            Object.keys(st.files).forEach(k => {
                st.files[k].status = 'Approved';
                st.files[k].note = '';
            });
            syncStudentCatatanWaliDW(st);
        });
        renderAllBatchStudentsContentDW();
        showDWToast('Semua bagian mahasiswa terpilih telah ditandai Disetujui (Approve).');
    }

    function submitFinalBatchApprovalDW() {
        if (!window.batchStudentsDW || window.batchStudentsDW.length === 0) return;

        function highlightBatchErrorField(inputEl, errEl, msg) {
            if (inputEl) {
                inputEl.classList.remove('border-rose-300');
                inputEl.classList.add('border-rose-600', 'ring-4', 'ring-rose-500/30', 'bg-rose-50/80');
                inputEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => inputEl.focus(), 300);
                
                inputEl.addEventListener('input', function onInputClear() {
                    inputEl.classList.remove('ring-4', 'ring-rose-500/30', 'bg-rose-50/80', 'border-rose-600');
                    inputEl.classList.add('border-rose-300');
                    if (errEl) errEl.classList.add('hidden');
                    inputEl.removeEventListener('input', onInputClear);
                });
            }
            if (errEl) {
                if (msg) {
                    const spanEl = errEl.querySelector('span');
                    if (spanEl) spanEl.textContent = msg;
                }
                errEl.classList.remove('hidden');
            }
        }

        // Validasi jika ada bagian yang belum diputuskan atau catatan revisi belum diisi
        for (const st of window.batchStudentsDW) {
            const items = [
                { name: 'Jenis & Skema TA', status: st.status_jenis_ta, secId: `batch_sec_jenis_${st.nim}` },
                { name: 'Usulan Judul TA', status: st.status_judul, secId: `batch_sec_judul_${st.nim}` }
            ];
            if (st.files) {
                Object.keys(st.files).forEach(k => {
                    const docInfo = (typeof docNames !== 'undefined' && docNames[k]) ? docNames[k].short : k.toUpperCase();
                    items.push({
                        name: docInfo,
                        status: st.files[k]?.status,
                        secId: `batch_sec_file_${k}_${st.nim}`
                    });
                });
            }
            const pendingItems = items.filter(i => i.status !== 'Approved' && i.status !== 'Rejected');
            if (pendingItems.length > 0) {
                const firstPending = pendingItems[0];
                const secEl = document.getElementById(firstPending.secId) || document.getElementById(`batch_card_${st.nim}`);
                if (secEl) {
                    secEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    secEl.classList.add('ring-4', 'ring-amber-500/40');
                    setTimeout(() => secEl.classList.remove('ring-4', 'ring-amber-500/40'), 3000);
                }
                showDWToast(`⚠️ Mahasiswa ${st.nama}: Bagian ${firstPending.name} belum diputuskan (Valid / Kurang).`, false);
                return;
            }

            if (st.status_jenis_ta === 'Rejected' && !st.catatan_jenis_ta.trim()) {
                const inputEl = document.getElementById(`batch_note_jenis_${st.nim}`);
                const errEl = document.getElementById(`batch_err_jenis_${st.nim}`);
                highlightBatchErrorField(inputEl, errEl, `Catatan revisi Jenis TA untuk ${st.nama} belum ditambahkan.`);
                showDWToast(`⚠️ Catatan belum ditambahkan! Harap isi catatan revisi Jenis TA (${st.nama}).`, false);
                return;
            }
            if (st.status_judul === 'Rejected' && !st.catatan_judul.trim()) {
                const inputEl = document.getElementById(`batch_note_judul_${st.nim}`);
                const errEl = document.getElementById(`batch_err_judul_${st.nim}`);
                highlightBatchErrorField(inputEl, errEl, `Catatan revisi Usulan Judul TA untuk ${st.nama} belum ditambahkan.`);
                showDWToast(`⚠️ Catatan belum ditambahkan! Harap isi saran/catatan revisi Judul TA (${st.nama}).`, false);
                return;
            }
            const docLabels = { 'ksm': 'KSM', 'transkrip': 'Transkrip Nilai', 'pernyataan': 'Surat Pernyataan', 'bebas_lab': 'Bebas Lab' };
            for (const [k, f] of Object.entries(st.files)) {
                if (f.status === 'Rejected' && !f.note.trim()) {
                    const inputEl = document.getElementById(`batch_note_file_${k}_${st.nim}`);
                    const errEl = document.getElementById(`batch_err_file_${k}_${st.nim}`);
                    const labelName = docLabels[k] || k.toUpperCase();
                    highlightBatchErrorField(inputEl, errEl, `Catatan revisi berkas ${labelName} untuk ${st.nama} belum ditambahkan.`);
                    showDWToast(`⚠️ Catatan belum ditambahkan! Harap isi catatan revisi berkas ${labelName} (${st.nama}).`, false);
                    return;
                }
            }
        }

        const totalCount = window.batchStudentsDW.length;
        const approveCount = window.batchStudentsDW.filter(s => s.action === 'approve').length;
        const rejectCount = window.batchStudentsDW.filter(s => s.action === 'reject').length;

        Swal.fire({
            title: 'Konfirmasi Verifikasi',
            html: `
                <div class="text-xs text-slate-600 text-left space-y-2.5 mt-2">
                    <p class="leading-relaxed">Apakah Anda yakin ingin memproses verifikasi untuk <strong>${totalCount} mahasiswa</strong> ini?</p>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5 font-medium">
                        <div class="flex items-center justify-between text-emerald-700">
                            <span><i class="fa-solid fa-circle-check mr-1.5"></i> Disetujui (Lanjut ke Admin LAA):</span>
                            <span class="font-bold">${approveCount} Mahasiswa</span>
                        </div>
                        <div class="flex items-center justify-between text-rose-700">
                            <span><i class="fa-solid fa-circle-xmark mr-1.5"></i> Ditolak / Perlu Revisi:</span>
                            <span class="font-bold">${rejectCount} Mahasiswa</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 italic">Pastikan seluruh keputusan telah sesuai sebelum menyimpan.</p>
                </div>
            `,
            icon: 'question',
            iconColor: '#f97316',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1.5"></i> Ya, Simpan &amp; Proses',
            cancelButtonText: 'Periksa Kembali',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-orange-100',
                confirmButton: 'rounded-xl font-bold px-4 py-2.5 text-xs shadow-md cursor-pointer',
                cancelButton: 'rounded-xl font-semibold px-4 py-2.5 text-xs cursor-pointer'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading swal
                Swal.fire({
                    title: 'Memproses Verifikasi...',
                    text: 'Mohon tunggu sebentar, sistem sedang menyimpan data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData();
                formData.append('action', 'batch_update');
                formData.append('decisions_json', JSON.stringify(window.batchStudentsDW.map(st => {
                    const dec = {
                        nim: st.nim,
                        action: st.action,
                        status_jenis_ta: st.status_jenis_ta,
                        catatan_jenis_ta: st.catatan_jenis_ta,
                        status_judul: st.status_judul,
                        catatan_judul: st.catatan_judul,
                        catatan_wali: st.catatan_wali
                    };
                    if (st.files) {
                        Object.keys(st.files).forEach(k => {
                            dec[`status_file_${k}`] = st.files[k]?.status;
                            dec[`catatan_file_${k}`] = st.files[k]?.note || '';
                        });
                    }
                    return dec;
                })));
                window.batchStudentsDW.forEach(st => formData.append('nims[]', st.nim));

                fetch('<?= site_url("dosenwali/submit_batch_approval"); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    closeBatchModalDW();
                    unselectAllStudentsDW();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message || 'Persetujuan massal berhasil diproses!',
                        icon: 'success',
                        iconColor: '#10b981',
                        confirmButtonColor: '#10b981',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(err => {
                    console.error('Submit final batch error:', err);
                    location.reload();
                });
            }
        });
    }

    // PDF Preview Modal Handler
    function previewDocPdfDW(url, title) {
        if (!url || url === '#' || url === 'about:blank') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Berkas Belum Diunggah',
                    text: 'Mahasiswa belum mengunggah dokumen ini atau tautan berkas tidak ditemukan.',
                    confirmButtonColor: '#f97316'
                });
            } else {
                alert('Dokumen ini belum diunggah oleh mahasiswa.');
            }
            return;
        }

        const modal = document.getElementById('pdfModalDW');
        const frame = document.getElementById('pdfFrameDW');
        const titleEl = document.getElementById('pdfModalTitleDW');
        const subtitleEl = document.getElementById('pdfModalSubtitleDW');
        const openTabBtn = document.getElementById('pdfModalOpenTabDW');

        if (!modal || !frame) return;

        titleEl.textContent = title || 'Pratinjau Dokumen Persyaratan';
        const cleanName = url.split('/').pop() || 'Dokumen PDF';
        if (subtitleEl) subtitleEl.textContent = cleanName;
        if (openTabBtn) openTabBtn.href = url;
        frame.src = url;

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden'; // Kunci scroll halaman luar
    }

    function closePdfModalDW() {
        const modal = document.getElementById('pdfModalDW');
        const frame = document.getElementById('pdfFrameDW');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        if (frame) frame.src = 'about:blank';

        // Jika modal batch review masih aktif, jangan kembalikan overflow body
        const batchModal = document.getElementById('batchReviewModalDW');
        if (!batchModal || batchModal.style.display === 'none' || batchModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    }

    // Explicit Window Function Registration
    window.openBatchModalDW = openBatchModalDW;
    window.submitDirectBatchApproveDW = submitDirectBatchApproveDW;
    window.closeBatchModalDW = closeBatchModalDW;
    window.previewDocPdfDW = previewDocPdfDW;
    window.closePdfModalDW = closePdfModalDW;
    window.markAllBatchDWApproved = markAllBatchDWApproved;
    window.submitFinalBatchApprovalDW = submitFinalBatchApprovalDW;
    window.setStudentDecisionDW = setStudentDecisionDW;
    window.setJenisDecisionDW = setJenisDecisionDW;
    window.updateJenisNoteDW = updateJenisNoteDW;
    window.setJenisPresetNoteDW = setJenisPresetNoteDW;
    window.setJudulDecisionDW = setJudulDecisionDW;
    window.updateJudulNoteDW = updateJudulNoteDW;
    window.setJudulPresetNoteDW = setJudulPresetNoteDW;
    window.setFileDecisionDW = setFileDecisionDW;
    window.updateFileNoteDW = updateFileNoteDW;
    window.setFilePresetNoteDW = setFilePresetNoteDW;
    window.updateStudentNoteDW = updateStudentNoteDW;
    window.renderAllBatchStudentsContentDW = renderAllBatchStudentsContentDW;

    // ==========================================
    // QUICK SINGLE DOC REVIEW (IDENTIK GAMBAR 2 / ADMIN LAYANAN)
    // DENGAN SELIPAN TOMBOL USULAN JUDUL & SKEMA TA
    // ==========================================
    window.currentReviewNim = null;
    window.currentReviewDocKey = null;

    const docDefinitions = {
        'ksm': {
            type: 'file',
            title: '1. KSM (Kartu Studi Mahasiswa)',
            short: 'KSM',
            desc: 'Bukti KRS semester aktif yang memuat mata kuliah Tugas Akhir.',
            fileField: 'file_ksm',
            statusField: 'status_file_ksm',
            noteField: 'catatan_file_ksm',
            icon: 'fa-solid fa-file-lines',
            presets: ['Tanpa TTD Dosen Wali', 'Mata Kuliah TA Belum Ada', 'File Buram / Tidak Jelas', 'Format Berkas Salah']
        },
        'transkrip': {
            type: 'file',
            title: '2. Transkrip Nilai Akademik Terakhir',
            short: 'TRS',
            desc: 'Transkrip nilai resmi yang sudah divalidasi dan memenuhi syarat SKS kelulusan.',
            fileField: 'file_transkrip',
            statusField: 'status_file_transkrip',
            noteField: 'catatan_file_transkrip',
            icon: 'fa-solid fa-file-spreadsheet',
            presets: ['Belum Update Semester Terbaru', 'SKS Kelulusan Kurang', 'Belum Tervalidasi Resmi', 'File Buram / Kurang Jelas']
        },
        'pernyataan': {
            type: 'file',
            title: '3. Surat Pernyataan Mahasiswa',
            short: 'SRT',
            desc: 'Surat kesanggupan menyelesaikan TA bermaterai dan ditandatangani.',
            fileField: 'file_pernyataan',
            statusField: 'status_file_pernyataan',
            noteField: 'catatan_file_pernyataan',
            icon: 'fa-solid fa-file-contract',
            presets: ['Tanpa Materai Rp 10.000', 'Belum Ditandatangani', 'Format Surat Salah', 'Dokumen Belum Lengkap']
        },
        'bebas_lab': {
            type: 'file',
            title: '4. Surat Bebas Laboratorium & Perpustakaan',
            short: 'LAB',
            desc: 'Surat keterangan bebas pinjaman alat lab FIK dan buku perpustakaan.',
            fileField: 'file_bebas_lab',
            statusField: 'status_file_bebas_lab',
            noteField: 'catatan_file_bebas_lab',
            icon: 'fa-solid fa-building-circle-check',
            presets: ['Tanpa Stempel Resmi Lab', 'Pinjaman Alat Lab Belum Lunas', 'Buku Perpus Belum Kembali', 'File Buram']
        }
    };

    // Daftarkan seluruh syarat berkas dinamis ke docDefinitions
    if (window.SYARAT_BERKAS && Array.isArray(window.SYARAT_BERKAS)) {
        window.SYARAT_BERKAS.forEach((sb, idx) => {
            const k = sb.kode_berkas;
            if (!docDefinitions[k]) {
                const mapAbbr = { ksm: 'KSM', transkrip: 'TRS', pernyataan: 'SRT', bebas_lab: 'LAB' };
                let abbr = mapAbbr[k];
                if (!abbr) {
                    const w = (sb.nama_berkas || k).trim().split(/[\s_-]+/);
                    abbr = (w[0] || 'DOC').substring(0, 4).toUpperCase();
                }
                docDefinitions[k] = {
                    type: 'file',
                    title: `${idx + 1}. ${sb.nama_berkas}`,
                    short: abbr,
                    desc: sb.deskripsi || `Dokumen persyaratan ${sb.nama_berkas}.`,
                    fileField: `file_${k}`,
                    statusField: `status_file_${k}`,
                    noteField: `catatan_file_${k}`,
                    icon: 'fa-solid fa-file-pdf',
                    presets: ['Format Berkas Salah', 'Berkas Buram / Kurang Jelas', 'Dokumen Belum Lengkap', 'TTD / Stempel Belum Ada']
                };
            }
        });
    }

    function resolveDocPdfUrl(filename) {
        if (!filename) return '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
        if (filename.startsWith('http://') || filename.startsWith('https://')) return filename;
        if (filename.startsWith('uploads/')) return '<?= base_url(); ?>' + filename;
        return '<?= base_url("uploads/persyaratan_ta/"); ?>' + filename;
    }

    let quickDocToastTimer = null;
    function showQuickDocToast(msg, type = 'info') {
        const toast = document.getElementById('quickDocToast');
        const msgEl = document.getElementById('quickDocToastMsg');
        const iconEl = document.getElementById('quickDocToastIcon');
        if (!toast || !msgEl || !iconEl) {
            showDWToast(msg, type === 'success');
            return;
        }

        msgEl.innerHTML = msg;
        if (type === 'success') {
            iconEl.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>';
        } else if (type === 'warning' || type === 'error') {
            iconEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm"></i>';
        } else {
            iconEl.innerHTML = '<i class="fa-solid fa-circle-info text-amber-400 text-sm"></i>';
        }

        toast.classList.remove('-translate-y-16', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        if (quickDocToastTimer) clearTimeout(quickDocToastTimer);
        quickDocToastTimer = setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('-translate-y-16', 'opacity-0', 'pointer-events-none');
        }, 3200);
    }

    function openQuickDocReview(nim, docKey) {
        if (!nim || !docDefinitions[docKey]) return;

        if (!window.mhsDataMap || !window.mhsDataMap[nim]) {
            fetch('<?= site_url("dosenwali/get_batch_details"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ 'nims[]': nim })
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    if (!window.mhsDataMap) window.mhsDataMap = {};
                    window.mhsDataMap[nim] = data[0];
                    setupAndShowQuickDocReview(nim, docKey);
                } else {
                    alert('Data mahasiswa tidak ditemukan.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat detail mahasiswa.');
            });
            return;
        }

        setupAndShowQuickDocReview(nim, docKey);
    }

    function setupAndShowQuickDocReview(nim, docKey) {
        window.currentReviewNim = nim;
        window.currentReviewDocKey = docKey || 'ksm';
        renderQuickDocReviewModal();
    }

    function updateModalStatusBadge(st) {
        const badgeContainer = document.getElementById('quickDocStatusBadge');
        if (!badgeContainer) return;

        if (st === 'Approved' || st === 'Valid') {
            badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-circle-check text-emerald-500"></i> Valid</span>`;
        } else if (st === 'Rejected' || st === 'Invalid') {
            badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-circle-xmark text-rose-500"></i> Direvisi</span>`;
        } else {
            badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-clock text-amber-500"></i> Menunggu Verifikasi</span>`;
        }
    }

    function renderQuickDocReviewModal() {
        const mhs = window.mhsDataMap ? window.mhsDataMap[window.currentReviewNim] : null;
        if (!mhs) return;

        const modal = document.getElementById('quickDocReviewModal');
        const docKey = window.currentReviewDocKey;
        const docMeta = docDefinitions[docKey];
        if (!modal || !docMeta) return;

        const fullName = (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : (mhs.nama || 'Mahasiswa')).trim();
        document.getElementById('quickDocStudent').textContent = `${fullName} (${mhs.nim})`;

        const iframe = document.getElementById('quickDocPreviewIframe');
        const btnTab = document.getElementById('quickDocBtnTab');
        const btnDetail = document.getElementById('quickDocBtnDetail');
        const rejectBox = document.getElementById('quickRejectBox');
        const inputCatatan = document.getElementById('inputQuickCatatan');
        const chipsContainer = document.getElementById('quickRejectPresetChips');
        const iconEl = document.getElementById('quickDocIcon');
        const titleEl = document.getElementById('quickDocTitle');

        if (rejectBox) rejectBox.classList.add('hidden');
        if (btnDetail) btnDetail.href = '<?= site_url("dosen/wali/detail_mahasiswa/"); ?>' + mhs.nim;

        // Header icon & title
        if (iconEl) iconEl.className = 'fa-solid fa-file-pdf';
        if (titleEl) titleEl.textContent = docMeta.title || `Berkas ${docMeta.short}`;

        // Resolve file URL
        let rawFilename = mhs[docMeta.fileField];
        if (!rawFilename && mhs.berkas_map && mhs.berkas_map[docKey]) {
            rawFilename = mhs.berkas_map[docKey].file_name;
        }
        if (!rawFilename) {
            rawFilename = `${docKey}_${mhs.nim}.pdf`;
        }
        const pdfUrl = resolveDocPdfUrl(rawFilename);

        if (iframe) iframe.src = pdfUrl;
        if (btnTab) {
            btnTab.href = pdfUrl;
            btnTab.style.display = 'inline-flex';
        }

        const currentStatus = mhs[docMeta.statusField] || (mhs.berkas_map && mhs.berkas_map[docKey] ? (mhs.berkas_map[docKey].status_verifikasi === 'Valid' ? 'Approved' : (mhs.berkas_map[docKey].status_verifikasi === 'Invalid' ? 'Rejected' : 'Pending')) : 'Pending');
        const currentNote = mhs[docMeta.noteField] || (mhs.berkas_map && mhs.berkas_map[docKey] ? mhs.berkas_map[docKey].catatan : '') || '';

        // Update status badge
        updateModalStatusBadge(currentStatus);

        // Update Catatan Input & Preset Chips
        if (inputCatatan) {
            inputCatatan.value = (currentStatus === 'Rejected') ? currentNote : '';
            inputCatatan.placeholder = `Ketik alasan revisi ${docMeta.short}...`;
        }
        if (chipsContainer) {
            chipsContainer.innerHTML = (docMeta.presets || []).map(ps => `
                <button type="button" onclick="setQuickCatatan('${ps.replace(/'/g, "\\'")}')" 
                        class="px-2 py-0.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 rounded text-[10px] border border-slate-200 transition-colors cursor-pointer">
                    ${ps}
                </button>
            `).join('');
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function toggleQuickRejectBox() {
        const box = document.getElementById('quickRejectBox');
        if (box) {
            box.classList.toggle('hidden');
            if (!box.classList.contains('hidden')) {
                const input = document.getElementById('inputQuickCatatan');
                if (input) input.focus();
            }
        }
    }

    function setQuickCatatan(text) {
        const input = document.getElementById('inputQuickCatatan');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function submitQuickSingleVerify(status) {
        const nim = window.currentReviewNim;
        const docKey = window.currentReviewDocKey;
        if (!nim || !docKey || !docDefinitions[docKey]) return;

        const docMeta = docDefinitions[docKey];
        const catatanInput = document.getElementById('inputQuickCatatan');
        const comment = (catatanInput ? catatanInput.value : '').trim();

        if (status === 'Rejected' && !comment) {
            alert('Harap masukkan atau pilih alasan/catatan revisi terlebih dahulu!');
            if (catatanInput) catatanInput.focus();
            return;
        }

        const btnValid = document.getElementById('btnQuickValid');
        const btnRejectToggle = document.getElementById('btnQuickRejectToggle');
        const btnPending = document.getElementById('btnQuickPending');
        const btnSubmitReject = document.getElementById('btnSubmitQuickReject');

        if (btnValid) btnValid.disabled = true;
        if (btnRejectToggle) btnRejectToggle.disabled = true;
        if (btnPending) btnPending.disabled = true;
        if (btnSubmitReject) btnSubmitReject.disabled = true;

        // Simpan status Berkas (PDF)
        const formData = new FormData();
        formData.append('nim', nim);
        formData.append('file_type', docKey);
        formData.append('status', status);
        formData.append('comment', status === 'Rejected' ? comment : '');

        fetch('<?= site_url("dosenwali/update_file_approval_ajax"); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (btnValid) btnValid.disabled = false;
            if (btnRejectToggle) btnRejectToggle.disabled = false;
            if (btnPending) btnPending.disabled = false;
            if (btnSubmitReject) btnSubmitReject.disabled = false;

            if (res.success) {
                if (window.mhsDataMap && window.mhsDataMap[nim]) {
                    window.mhsDataMap[nim][docMeta.statusField] = status;
                    window.mhsDataMap[nim][docMeta.noteField] = (status === 'Rejected' ? comment : '');
                    if (window.mhsDataMap[nim].berkas_map && window.mhsDataMap[nim].berkas_map[docKey]) {
                        window.mhsDataMap[nim].berkas_map[docKey].status_verifikasi = (status === 'Approved' ? 'Valid' : (status === 'Rejected' ? 'Invalid' : 'Pending'));
                        window.mhsDataMap[nim].berkas_map[docKey].catatan = (status === 'Rejected' ? comment : '');
                    }
                }

                updateModalStatusBadge(status);

                const tableBadge = document.getElementById(`badge_doc_${nim}_${docKey}`);
                if (tableBadge) {
                    if (status === 'Approved') {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200';
                    } else if (status === 'Rejected') {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-rose-100/90 text-rose-700 hover:bg-rose-200';
                    } else {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60';
                    }
                }

                const box = document.getElementById('quickRejectBox');
                if (box) box.classList.add('hidden');

                const toastMsg = (status === 'Approved') ? `Berkas ${docMeta.short} disetujui (Valid)!` : ((status === 'Rejected') ? `Catatan revisi ${docMeta.short} berhasil dikirim!` : `Status ${docMeta.short} dikembalikan ke Netral.`);
                showQuickDocToast(toastMsg, 'success');

                if (typeof pollRealtimeData === 'function') {
                    lastDataHash = '';
                    pollRealtimeData();
                }
            } else {
                alert(res.message || 'Gagal menyimpan status berkas.');
            }
        })
        .catch(err => {
            if (btnValid) btnValid.disabled = false;
            if (btnRejectToggle) btnRejectToggle.disabled = false;
            if (btnPending) btnPending.disabled = false;
            if (btnSubmitReject) btnSubmitReject.disabled = false;
            console.error(err);
            alert('Terjadi kesalahan jaringan saat menyimpan status.');
        });
    }

    function closeQuickDocReviewModal() {
        const modal = document.getElementById('quickDocReviewModal');
        const iframe = document.getElementById('quickDocPreviewIframe');
        if (modal) {
            modal.classList.add('hidden');
            if (iframe) iframe.src = 'about:blank';
        }
        window.currentReviewNim = null;
        window.currentReviewDocKey = null;

        const batchModal = document.getElementById('batchReviewModalDW');
        if (!batchModal || batchModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    }

    window.openQuickDocReview = openQuickDocReview;
    window.setupAndShowQuickDocReview = setupAndShowQuickDocReview;
    window.renderQuickDocReviewModal = renderQuickDocReviewModal;
    window.toggleQuickRejectBox = toggleQuickRejectBox;
    window.setQuickCatatan = setQuickCatatan;
    window.submitQuickSingleVerify = submitQuickSingleVerify;
    window.closeQuickDocReviewModal = closeQuickDocReviewModal;

    // ==========================================
    // FLOATING NON-BLOCKING: LIHAT & PREVIEW BERKAS (DUAL PREVIEW & DUAL STUDENT)
    // ==========================================
    window.activeLihatBerkasNims = []; // Array NIM aktif (Maksimal 4 mahasiswa)
    window.activePreviews = [];        // Array Pratinjau Aktif (Maksimal 2 dokumen berdampingan: [{nim, docKey}])

    // ==========================================
    // LIHAT BERKAS & PREVIEW VIEW REFRESH (NATURAL WALKING MOTION)
    // ==========================================
    function refreshLihatBerkasView() {
        // 1. Snapshot posisi koordinat kartu sebelum layout berganti
        const oldPos = {};
        document.querySelectorAll('.student-card-item').forEach(el => {
            const r = el.getBoundingClientRect();
            if (r.width > 0 && r.height > 0) {
                oldPos[el.id] = { left: r.left, top: r.top };
            }
        });

        // 2. Terapkan layout baru seketika (DOM langsung diperbarui tanpa delay)
        updateLihatBerkasLayout();
        renderAllLihatBerkasCards();
        renderAllPreviewCards();
        updateTableButtonHighlights();

        // 3. Jalankan animasi perpindahan objek nyata (terlihat jelas "berjalan" berpindah tempat)
        document.querySelectorAll('.student-card-item').forEach(el => {
            const old = oldPos[el.id];
            if (old) {
                const cur = el.getBoundingClientRect();
                const dx = old.left - cur.left;
                const dy = old.top - cur.top;

                if (Math.abs(dx) > 2 || Math.abs(dy) > 2) {
                    el.animate([
                        { transform: `translate3d(${dx}px, ${dy}px, 0)`, zIndex: 30, boxShadow: '0 20px 40px -10px rgba(0, 0, 0, 0.28)' },
                        { transform: 'translate3d(0, 0, 0)', zIndex: 30, boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)' }
                    ], {
                        duration: 650, // Kecepatan berjalan natural yang terlihat jelas perpindahannya
                        easing: 'cubic-bezier(0.25, 1, 0.4, 1)'
                    });
                }
            }
        });
    }

    function toggleLihatBerkasPanel(nim) {
        if (!nim) return;

        // Jika mahasiswa sudah aktif, toggle off (tutup)
        const idx = window.activeLihatBerkasNims.indexOf(nim);
        if (idx > -1) {
            removeStudentFromLihatBerkas(nim);
            return;
        }

        // Jika belum aktif, tambahkan (maksimal 4 mahasiswa)
        if (window.activeLihatBerkasNims.length >= 4) {
            window.activeLihatBerkasNims.shift();
            window.activeLihatBerkasNims.push(nim);
            if (typeof showDWToast === 'function') {
                showDWToast(`Maksimal 4 mahasiswa. Menampilkan 4 mahasiswa terbaru.`, true);
            }
        } else {
            window.activeLihatBerkasNims.push(nim);
        }

        // Pastikan detail data mahasiswa sudah ada di window.mhsDataMap
        if (!window.mhsDataMap || !window.mhsDataMap[nim]) {
            fetch('<?= site_url("dosenwali/get_batch_details"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ 'nims[]': nim })
            })
            .then(res => res.json())
            .then(data => {
                const list = Array.isArray(data) ? data : (data.data || []);
                if (list && list.length > 0) {
                    if (!window.mhsDataMap) window.mhsDataMap = {};
                    window.mhsDataMap[nim] = list[0];
                    showLihatBerkasContainer();
                    refreshLihatBerkasView();
                } else {
                    alert('Data mahasiswa tidak ditemukan.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat berkas mahasiswa.');
            });
            return;
        }

        showLihatBerkasContainer();
        refreshLihatBerkasView();
    }

    function showLihatBerkasContainer() {
        const container = document.getElementById('lihatBerkasContainer');
        if (container) {
            container.style.display = 'flex';
            container.classList.remove('hidden');
            container.classList.add('flex');
        }
    }

    function updateLihatBerkasLayout() {
        const container = document.getElementById('lihatBerkasContainer');
        const wrapper = document.getElementById('wrapperDaftarMhs');
        if (!container || !wrapper) return;

        const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;

        if (isPreviewActive) {
            // Mode Preview: Geser ke KIRI UJUNG dan susun ATAS-BAWAH (flex-col)
            container.classList.remove('justify-center');
            container.classList.add('justify-start');

            wrapper.className = 'flex flex-col gap-3 max-h-[92vh] overflow-y-auto pr-1.5 shrink-0 w-[330px] sm:w-[350px]';
        } else {
            // Mode Standar (2 orang tetap KANAN-KIRI): Tampil di TENGAH (flex-row)
            container.classList.remove('justify-start');
            container.classList.add('justify-center');

            wrapper.className = 'flex flex-row items-center gap-4 max-h-[92vh] overflow-x-auto p-1 shrink-0';
        }
    }

    function getDocList() {
        return (window.SYARAT_BERKAS && window.SYARAT_BERKAS.length > 0)
            ? window.SYARAT_BERKAS.map((sb, i) => {
                const k = sb.kode_berkas;
                const iconMap = {
                    ksm: 'fa-solid fa-file-lines',
                    transkrip: 'fa-solid fa-file-spreadsheet',
                    pernyataan: 'fa-solid fa-file-contract',
                    bebas_lab: 'fa-solid fa-building-circle-check'
                };
                return {
                    key: k,
                    title: `${i + 1}. ${sb.nama_berkas}`,
                    shortTitle: sb.nama_berkas,
                    fileField: `file_${k}`,
                    statusField: `status_file_${k}`,
                    noteField: `catatan_file_${k}`,
                    icon: iconMap[k] || 'fa-solid fa-file-pdf'
                };
            })
            : [
                { key: 'ksm', title: '1. KSM', shortTitle: 'KSM', fileField: 'file_ksm', statusField: 'status_file_ksm', noteField: 'catatan_file_ksm', icon: 'fa-solid fa-file-lines' },
                { key: 'transkrip', title: '2. Transkrip Nilai', shortTitle: 'Transkrip Nilai', fileField: 'file_transkrip', statusField: 'status_file_transkrip', noteField: 'catatan_file_transkrip', icon: 'fa-solid fa-file-spreadsheet' },
                { key: 'pernyataan', title: '3. Surat Pernyataan', shortTitle: 'Surat Pernyataan', fileField: 'file_pernyataan', statusField: 'status_file_pernyataan', noteField: 'catatan_file_pernyataan', icon: 'fa-solid fa-file-contract' },
                { key: 'bebas_lab', title: '4. Bebas Lab & Perpus', shortTitle: 'Bebas Lab & Perpus', fileField: 'file_bebas_lab', statusField: 'status_file_bebas_lab', noteField: 'catatan_file_bebas_lab', icon: 'fa-solid fa-building-circle-check' }
            ];
    }

    function getMhsDocStatus(nim, docKey) {
        const mhs = window.mhsDataMap ? window.mhsDataMap[nim] : null;
        if (!mhs) return 'Pending';
        const directStatus = mhs[`status_file_${docKey}`];
        if (directStatus === 'Approved' || directStatus === 'Rejected') return directStatus;
        if (mhs.berkas_map && mhs.berkas_map[docKey]) {
            const bv = mhs.berkas_map[docKey].status_verifikasi;
            if (bv === 'Valid') return 'Approved';
            if (bv === 'Invalid') return 'Rejected';
        }
        if (mhs.files && mhs.files[docKey] && mhs.files[docKey].status) {
            const fs = mhs.files[docKey].status;
            if (fs === 'Approved' || fs === 'Rejected') return fs;
        }
        return 'Pending';
    }

    function isMhsStageLocked(nim) {
        const mhs = window.mhsDataMap ? window.mhsDataMap[nim] : null;
        if (!mhs) return false;
        const stage = mhs.current_stage || '';
        const stWali = mhs.status_approval_wali || '';
        return (stWali === 'Approved' || ['Admin Layanan', 'Koordinator TA', 'Ketua KK', 'Selesai Approval'].includes(stage));
    }

    function getMhsValidDocCount(nim, docList) {
        let count = 0;
        docList.forEach(d => {
            if (getMhsDocStatus(nim, d.key) === 'Approved') {
                count++;
            }
        });
        return count;
    }

    function getDocStatusBadgeHtml(status) {
        if (status === 'Approved') {
            return `<span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-bold inline-flex items-center gap-1"><i class="fa-solid fa-circle-check text-emerald-600"></i> Valid</span>`;
        } else if (status === 'Rejected') {
            return `<span class="px-2 py-1 rounded-lg bg-rose-100 text-rose-800 border border-rose-300 text-[10px] font-bold inline-flex items-center gap-1"><i class="fa-solid fa-circle-exclamation text-rose-600"></i> Revisi</span>`;
        } else {
            return `<span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-300 text-[10px] font-bold inline-flex items-center gap-1"><i class="fa-solid fa-clock text-slate-400"></i> Pending</span>`;
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
        const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;
        const cardWidthClass = isPreviewActive ? 'w-full' : (totalActive > 1 ? 'w-[350px] sm:w-[370px]' : 'w-[390px] sm:w-[410px]');

        const docList = getDocList();

        // Bar header jika ada lebih dari 1 mahasiswa aktif di mode preview
        let headerSummaryBar = '';
        if (totalActive > 1 && isPreviewActive) {
            headerSummaryBar = `
                <div class="bg-slate-900 text-white px-3.5 py-2 rounded-2xl flex items-center justify-between shadow-lg border border-slate-800 shrink-0 pointer-events-auto w-full animate-bar-in">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users text-orange-400 text-xs"></i>
                        <span class="text-xs font-bold">${totalActive} Mahasiswa <span class="text-slate-400 font-normal text-[10px]">(Maks. 4)</span></span>
                    </div>
                    <button type="button" onclick="closeLihatBerkasPanel()" class="text-[11px] text-slate-300 hover:text-rose-400 font-bold transition cursor-pointer">
                        Tutup Semua
                    </button>
                </div>
            `;
        }

        const cardsHtml = window.activeLihatBerkasNims.map((nim, index) => {
            const mhs = window.mhsDataMap[nim] || {};
            const fullName = (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : (mhs.nama || 'Mahasiswa ' + nim)).trim();
            const cardNum = index + 1;
            const isLocked = isMhsStageLocked(nim);
            const validCount = getMhsValidDocCount(nim, docList);
            const totalDocs = docList.length;

            const itemsHtml = docList.map(doc => {
                const rawFilename = mhs[doc.fileField] || `${doc.key}_${nim}.pdf`;
                const pdfUrl = resolveDocPdfUrl(rawFilename);
                const status = getMhsDocStatus(nim, doc.key);
                // Berkas ini aktif jika sedang ada di salah satu slot window.activePreviews
                const isCurrentlyPreviewed = window.activePreviews && window.activePreviews.some(p => p.nim === nim && p.docKey === doc.key);

                let statusBadge = '';
                if (status === 'Approved') {
                    statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Valid</span>';
                } else if (status === 'Rejected') {
                    statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Revisi</span>';
                } else {
                    statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Pending</span>';
                }

                const activeCardBorder = isCurrentlyPreviewed 
                    ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' 
                    : 'border-slate-200 bg-white hover:border-slate-300';
                
                // Tombol Preview MATA SAJA
                const previewBtnStyle = isCurrentlyPreviewed 
                    ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' 
                    : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';

                return `
                    <div class="p-2 px-2.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex items-center justify-between gap-2 ${activeCardBorder}">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <i class="${doc.icon} text-orange-500 text-xs shrink-0"></i>
                                <span class="font-bold text-slate-800 text-[11px] truncate">${doc.title}</span>
                                <span id="cardDocBadge_${nim}_${doc.key}">${statusBadge}</span>
                            </div>
                            <p class="text-[10px] font-mono text-slate-400 truncate mt-0.5" title="${rawFilename}">
                                <i class="fa-solid fa-file-pdf text-rose-500 mr-1 text-[9px]"></i>${rawFilename}
                            </p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" 
                                    onclick="previewBerkasItem('${nim}', '${doc.key}')" 
                                    class="w-7 h-7 rounded-lg border text-xs font-bold transition flex items-center justify-center cursor-pointer active:scale-95 ${previewBtnStyle}" 
                                    title="${isCurrentlyPreviewed ? 'Tutup Pratinjau Ini' : 'Pratinjau Berkas'}">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                            <a href="${pdfUrl}" 
                               download="${rawFilename}" 
                               target="_blank" 
                               class="w-7 h-7 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition flex items-center justify-center cursor-pointer shadow-2xs active:scale-95" 
                               title="Unduh Berkas">
                                <i class="fa-solid fa-download text-xs"></i>
                            </a>
                        </div>
                    </div>
                `;
            }).join('');

            return `
                <div id="cardMhs_${nim}" class="student-card-item pointer-events-auto ${cardWidthClass} bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0">
                    <!-- Header -->
                    <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                                ${cardNum}
                            </div>
                            <div class="min-w-0 flex items-center gap-2">
                                <h4 class="text-xs font-bold text-white truncate max-w-[140px] sm:max-w-[170px]">${fullName}</h4>
                                <span class="px-1.5 py-0.2 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${nim}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-white/10 text-slate-300 rounded uppercase tracking-wider hidden sm:inline-block">Daftar Berkas</span>
                            <button type="button" onclick="removeStudentFromLihatBerkas('${nim}')" class="w-6 h-6 rounded-md bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-[11px] font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- List of Berkas -->
                    <div class="p-2.5 space-y-1.5 bg-slate-50/50 overflow-y-auto max-h-[50vh] sm:max-h-[55vh]">
                        ${itemsHtml}
                    </div>
                </div>
            `;
        }).join('');

        wrapper.innerHTML = headerSummaryBar + cardsHtml;
    }

    function previewBerkasItem(nim, docKey) {
        const mhs = window.mhsDataMap[nim];
        if (!mhs) return;

        // Cek apakah berkas ini sudah sedang dibuka di salah satu slot pratinjau
        const existingIdx = window.activePreviews.findIndex(p => p.nim === nim && p.docKey === docKey);
        if (existingIdx > -1) {
            // Toggle off jika diklik ulang
            closeSinglePreview(existingIdx);
            return;
        }

        // Tambahkan ke slot (maksimal 5 pratinjau berdampingan seperti Admin LAA)
        if (window.activePreviews.length >= 5) {
            window.activePreviews.shift();
            window.activePreviews.push({ nim, docKey });
            if (typeof showDWToast === 'function') {
                showDWToast(`Maksimal 5 pratinjau. Menampilkan 5 dokumen terbaru.`, true);
            }
        } else {
            window.activePreviews.push({ nim, docKey });
        }

        refreshLihatBerkasView();

        setTimeout(() => {
            const previewWrapper = document.getElementById('wrapperPreviewBerkas');
            if (previewWrapper) {
                previewWrapper.scrollLeft = previewWrapper.scrollWidth;
            }
        }, 100);
    }

    async function openStudentBerkasPreview(nim, docKey) {
        if (!nim) return;
        nim = String(nim).trim();

        if (!window.activeLihatBerkasNims) window.activeLihatBerkasNims = [];
        if (!window.activePreviews) window.activePreviews = [];

        // Pastikan panel mahasiswa terbuka
        const activeNims = window.activeLihatBerkasNims.map(n => String(n).trim());
        if (!activeNims.includes(nim)) {
            if (window.activeLihatBerkasNims.length >= 4) {
                window.activeLihatBerkasNims.shift();
            }
            window.activeLihatBerkasNims.push(nim);
        }

        const showAndRender = () => {
            showLihatBerkasContainer();
            if (docKey) {
                docKey = String(docKey).trim();
                const isPreviewed = window.activePreviews.some(p => String(p.nim).trim() === nim && String(p.docKey).trim() === docKey);
                if (!isPreviewed) {
                    if (window.activePreviews.length >= 5) {
                        window.activePreviews.shift();
                    }
                    window.activePreviews.push({ nim, docKey });
                }
            }
            refreshLihatBerkasView();
            if (docKey) {
                setTimeout(() => {
                    const previewWrapper = document.getElementById('wrapperPreviewBerkas');
                    if (previewWrapper) {
                        previewWrapper.scrollLeft = previewWrapper.scrollWidth;
                    }
                }, 100);
            }
        };

        if (!window.mhsDataMap || !window.mhsDataMap[nim]) {
            try {
                const res = await fetch('<?= site_url("dosenwali/get_batch_details"); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({ 'nims[]': nim })
                }).then(r => r.json());

                const list = Array.isArray(res) ? res : (res.data || []);
                if (list && list.length > 0) {
                    if (!window.mhsDataMap) window.mhsDataMap = {};
                    window.mhsDataMap[nim] = list[0];
                    showAndRender();
                } else {
                    alert('Data mahasiswa tidak ditemukan.');
                }
            } catch (err) {
                console.error(err);
                alert('Gagal memuat berkas mahasiswa.');
            }
            return;
        }

        showAndRender();
    }

    function updateTableBerkasSummaryBadges(nim) {
        if (!nim) return;
        const container = document.getElementById(`berkas_summary_badges_${nim}`);
        if (!container) return;

        const docList = getDocList();
        let vCnt = 0, iCnt = 0, pCnt = 0;
        docList.forEach(d => {
            const st = getMhsDocStatus(nim, d.key);
            if (st === 'Approved') vCnt++;
            else if (st === 'Rejected') iCnt++;
            else pCnt++;
        });

        let summaryBadgesHtml = '';
        if (vCnt > 0) {
            summaryBadgesHtml += `<button type="button" onclick="openStudentBerkasPreview('${nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${vCnt} Berkas Disetujui/Valid — Klik untuk Lihat Berkas"><i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i> <span>${vCnt} Valid</span></button>`;
        }
        if (iCnt > 0) {
            summaryBadgesHtml += `<button type="button" onclick="openStudentBerkasPreview('${nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${iCnt} Berkas Ditolak/Direvisi — Klik untuk Lihat Berkas"><i class="fa-solid fa-circle-xmark text-rose-500 text-[9px]"></i> <span>${iCnt} Direvisi</span></button>`;
        }
        if (pCnt > 0) {
            summaryBadgesHtml += `<button type="button" onclick="openStudentBerkasPreview('${nim}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 hover:scale-105 transition-all cursor-pointer shadow-2xs" title="${pCnt} Berkas Menunggu Verifikasi — Klik untuk Lihat Berkas"><i class="fa-solid fa-clock text-amber-500 text-[9px]"></i> <span>${pCnt} Menunggu</span></button>`;
        }
        if (vCnt === 0 && iCnt === 0 && pCnt === 0) {
            summaryBadgesHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">Belum ada berkas</span>`;
        }

        container.innerHTML = summaryBadgesHtml;
    }

    function renderAllPreviewCards() {
        const wrapper = document.getElementById('wrapperPreviewBerkas');
        if (!wrapper) return;

        if (!window.activePreviews || window.activePreviews.length === 0) {
            wrapper.innerHTML = '';
            wrapper.classList.add('hidden');
            return;
        }

        wrapper.classList.remove('hidden');
        wrapper.className = 'flex items-center gap-3 shrink-0 max-w-[65vw] sm:max-w-[70vw] overflow-x-auto p-1.5 scroll-smooth';

        const totalPreviews = window.activePreviews.length;
        const panelWidthClass = totalPreviews >= 3 ? 'w-[340px] sm:w-[370px] lg:w-[400px] shrink-0' : (totalPreviews > 1 ? 'w-[400px] sm:w-[440px] lg:w-[470px] shrink-0' : 'w-[500px] sm:w-[540px] shrink-0');

        const docList = getDocList();

        // 1. Hapus card yang sudah tidak aktif
        const activeCardIds = window.activePreviews.map(p => `previewCard_${p.nim}_${p.docKey}`);
        Array.from(wrapper.children).forEach(child => {
            if (!activeCardIds.includes(child.id)) {
                child.remove();
            }
        });

        // 2. Tambahkan card baru atau perbarui card yang sudah ada tanpa reload iframe
        window.activePreviews.forEach((p, index) => {
            const cardId = `previewCard_${p.nim}_${p.docKey}`;
            let cardEl = document.getElementById(cardId);
            const slotNum = index + 1;
            const currentStatus = getMhsDocStatus(p.nim, p.docKey);
            const isLocked = isMhsStageLocked(p.nim);

            if (cardEl) {
                cardEl.className = `preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 transition-all duration-300 ${panelWidthClass}`;
                const badgeEl = cardEl.querySelector('.preview-slot-badge');
                if (badgeEl) {
                    badgeEl.innerHTML = totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>';
                }
                const closeBtn = cardEl.querySelector('.preview-close-btn');
                if (closeBtn) {
                    closeBtn.setAttribute('onclick', `closeSinglePreview(${index})`);
                }
                const footerCloseBtn = cardEl.querySelector('.preview-footer-close-btn');
                if (footerCloseBtn) {
                    footerCloseBtn.setAttribute('onclick', `closeSinglePreview(${index})`);
                }
                const statusBadgeEl = cardEl.querySelector(`#previewStatusBadge_${p.nim}_${p.docKey}`);
                if (statusBadgeEl) {
                    statusBadgeEl.innerHTML = getDocStatusBadgeHtml(currentStatus);
                }
            } else {
                const mhs = window.mhsDataMap[p.nim] || {};
                const doc = docList.find(d => d.key === p.docKey) || { title: p.docKey, fileField: `file_${p.docKey}` };
                const fileField = doc.fileField || `file_${p.docKey}`;
                const rawFilename = mhs[fileField] || (mhs.berkas_map && mhs.berkas_map[p.docKey]?.file_name) || `${p.docKey}_${p.nim}.pdf`;
                const pdfUrl = resolveDocPdfUrl(rawFilename);
                const fullName = (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : (mhs.nama || 'Mahasiswa ' + p.nim)).trim();

                const div = document.createElement('div');
                div.id = cardId;
                div.className = `preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 transition-all duration-300 ${panelWidthClass}`;
                div.innerHTML = `
                    <!-- Header Pratinjau Kompak dengan Label Sub-Pratinjau Jelas -->
                    <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="preview-slot-badge w-7 h-7 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                ${totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>'}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.2 text-[8px] font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded uppercase tracking-wider">Sub-Pratinjau</span>
                                    <h4 class="text-xs font-bold text-white truncate max-w-[160px] sm:max-w-[210px]">${doc.title}</h4>
                                </div>
                                <p class="text-[10px] text-slate-300 font-medium truncate mt-0.5">${fullName} · <span class="font-mono text-slate-400">${rawFilename}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" onclick="togglePreviewIframeInteraction('${p.nim}', '${p.docKey}')" id="btnPreviewInteract_${p.nim}_${p.docKey}" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Kursor Terkunci (Normal). Klik jika ingin mengaktifkan scroll di dalam berkas">
                                <i class="fa-solid fa-arrow-pointer text-[10px]" id="iconPreviewInteract_${p.nim}_${p.docKey}"></i>
                            </button>
                            <a href="${pdfUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka Layar Penuh di Tab Baru">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                            <button type="button" onclick="closeSinglePreview(${index})" class="preview-close-btn w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer ml-0.5" title="Tutup Pratinjau Ini">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Body Frame Pratinjau PDF -->
                    <div class="h-[390px] sm:h-[430px] md:h-[450px] bg-slate-200 relative border-b border-slate-200 overflow-hidden cursor-default select-none">
                        <iframe id="iframePreviewBerkas_${p.nim}_${p.docKey}" src="${pdfUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 pointer-events-none" title="Pratinjau Dokumen PDF"></iframe>
                    </div>

                    <!-- Footer Pratinjau dengan Aksi Approve & Reject Berkas -->
                    <div class="p-2.5 px-3.5 bg-white flex flex-col gap-2 shrink-0 border-t border-slate-200/90">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <!-- Status Berkas & Unduh -->
                            <div class="flex items-center gap-2">
                                <span id="previewStatusBadge_${p.nim}_${p.docKey}">
                                    ${getDocStatusBadgeHtml(currentStatus)}
                                </span>
                                <a href="${pdfUrl}" download="${rawFilename}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs text-[11px]" title="Unduh Berkas Ini">
                                    <i class="fa-solid fa-download text-[10px]"></i>
                                    <span>Unduh</span>
                                </a>
                            </div>

                            <!-- Tombol Aksi Verifikasi Berkas -->
                            <div class="flex items-center gap-1.5">
                                ${isLocked ? `
                                    <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1.5 rounded-xl border border-slate-200">Terkunci</span>
                                ` : `
                                    <button type="button" 
                                            onclick="togglePreviewRejectBox('${p.nim}', '${p.docKey}')" 
                                            id="btnPreviewReject_${p.nim}_${p.docKey}" 
                                            class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs flex items-center gap-1.5 transition active:scale-95 cursor-pointer shadow-2xs" 
                                            title="Tolak dokumen ini dan minta revisi">
                                        <i class="fa-solid fa-arrow-rotate-left text-[10px]"></i>
                                        <span>Revisi</span>
                                    </button>
                                    <button type="button" 
                                            onclick="submitPreviewDocApproval('${p.nim}', '${p.docKey}', 'Approved')" 
                                            id="btnPreviewApprove_${p.nim}_${p.docKey}" 
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 transition active:scale-95 cursor-pointer shadow-xs shadow-emerald-600/20" 
                                            title="Setujui dokumen ini sebagai Valid">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                        <span>Setujui</span>
                                    </button>
                                `}
                                <button type="button" 
                                        onclick="closeSinglePreview(${index})" 
                                        class="preview-footer-close-btn px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition cursor-pointer text-xs ml-0.5" 
                                        title="Tutup Pratinjau">
                                    Tutup
                                </button>
                            </div>
                        </div>

                        <!-- Box Catatan Revisi Berkas (Expandable) -->
                        <div id="previewRejectBox_${p.nim}_${p.docKey}" class="hidden pt-2 border-t border-rose-100 flex flex-col gap-1.5 animate-fade-in">
                            <div class="flex items-center justify-between text-[10px] font-bold text-rose-700 flex-wrap gap-1">
                                <span><i class="fa-solid fa-comment-dots mr-1"></i>Catatan Revisi Dokumen:</span>
                                <div class="flex items-center gap-1 flex-wrap">
                                    <button type="button" onclick="setPreviewPresetNote('${p.nim}', '${p.docKey}', 'Dokumen buram / tidak terbaca')" class="px-1.5 py-0.5 rounded bg-rose-100/70 hover:bg-rose-200 text-rose-800 text-[9px] font-medium border border-rose-200 cursor-pointer">+ Buram</button>
                                    <button type="button" onclick="setPreviewPresetNote('${p.nim}', '${p.docKey}', 'Format / tipe berkas tidak sesuai')" class="px-1.5 py-0.5 rounded bg-rose-100/70 hover:bg-rose-200 text-rose-800 text-[9px] font-medium border border-rose-200 cursor-pointer">+ Format Salah</button>
                                    <button type="button" onclick="setPreviewPresetNote('${p.nim}', '${p.docKey}', 'Tanda tangan / cap belum lengkap')" class="px-1.5 py-0.5 rounded bg-rose-100/70 hover:bg-rose-200 text-rose-800 text-[9px] font-medium border border-rose-200 cursor-pointer">+ Belum TTD</button>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <input type="text" 
                                       id="inputPreviewCatatan_${p.nim}_${p.docKey}" 
                                       placeholder="Alasan penolakan / revisi dokumen..." 
                                       oninput="clearPreviewDocNoteError('${p.nim}', '${p.docKey}')"
                                       class="flex-1 text-xs px-2.5 py-1.5 rounded-lg border border-rose-300 focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 focus:outline-none bg-rose-50/20 text-slate-800">
                                <button type="button" 
                                        onclick="submitPreviewDocApproval('${p.nim}', '${p.docKey}', 'Rejected')" 
                                        id="btnSubmitPreviewReject_${p.nim}_${p.docKey}" 
                                        class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-2xs transition active:scale-95 cursor-pointer shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                    <span>Kirim Revisi</span>
                                </button>
                                <button type="button" 
                                        onclick="togglePreviewRejectBox('${p.nim}', '${p.docKey}')" 
                                        class="px-2 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-700 cursor-pointer">
                                    Batal
                                </button>
                            </div>
                            <p id="errPreviewCatatan_${p.nim}_${p.docKey}" class="text-[10px] font-bold text-rose-600 flex items-center gap-1 hidden">
                                <i class="fa-solid fa-circle-exclamation"></i> <span>Alasan / catatan revisi dokumen wajib diisi!</span>
                            </p>
                        </div>
                    </div>
                `;
                wrapper.appendChild(div);
                div.animate([
                    { opacity: 0, transform: 'translateX(35px) scale(0.97)' },
                    { opacity: 1, transform: 'translateX(0) scale(1)' }
                ], {
                    duration: 500,
                    easing: 'cubic-bezier(0.25, 1, 0.4, 1)'
                });
            }
        });
    }

    function togglePreviewRejectBox(nim, docKey) {
        const box = document.getElementById(`previewRejectBox_${nim}_${docKey}`);
        if (!box) return;
        box.classList.toggle('hidden');
        if (!box.classList.contains('hidden')) {
            clearPreviewDocNoteError(nim, docKey);
            const input = document.getElementById(`inputPreviewCatatan_${nim}_${docKey}`);
            if (input) input.focus();
        }
    }

    function setPreviewPresetNote(nim, docKey, noteText) {
        const input = document.getElementById(`inputPreviewCatatan_${nim}_${docKey}`);
        if (input) {
            input.value = noteText;
            clearPreviewDocNoteError(nim, docKey);
            input.focus();
        }
    }

    function clearPreviewDocNoteError(nim, docKey) {
        const input = document.getElementById(`inputPreviewCatatan_${nim}_${docKey}`);
        const err = document.getElementById(`errPreviewCatatan_${nim}_${docKey}`);
        if (input) {
            input.classList.remove('border-rose-600', 'ring-2', 'ring-rose-500/30', 'bg-rose-50/80');
            input.classList.add('border-rose-300');
        }
        if (err) {
            err.classList.add('hidden');
        }
    }

    function submitPreviewDocApproval(nim, docKey, status) {
        if (isMhsStageLocked(nim)) {
            showDWToast('Pendaftaran telah disetujui di tahap berikutnya. Perubahan dikunci.', false);
            return;
        }

        let comment = '';
        if (status === 'Rejected') {
            const inputCatatan = document.getElementById(`inputPreviewCatatan_${nim}_${docKey}`);
            const errEl = document.getElementById(`errPreviewCatatan_${nim}_${docKey}`);
            comment = inputCatatan ? inputCatatan.value.trim() : '';

            if (!comment) {
                if (inputCatatan) {
                    inputCatatan.classList.remove('border-rose-300');
                    inputCatatan.classList.add('border-rose-600', 'ring-2', 'ring-rose-500/30', 'bg-rose-50/80');
                    inputCatatan.focus();
                }
                if (errEl) {
                    errEl.classList.remove('hidden');
                }
                showDWToast('⚠️ Alasan / catatan revisi dokumen wajib diisi sebelum mengirim revisi!', false);
                return;
            }
        }

        const btnApprove = document.getElementById(`btnPreviewApprove_${nim}_${docKey}`);
        const btnReject = document.getElementById(`btnPreviewReject_${nim}_${docKey}`);
        const btnSubmitReject = document.getElementById(`btnSubmitPreviewReject_${nim}_${docKey}`);

        if (btnApprove) btnApprove.disabled = true;
        if (btnReject) btnReject.disabled = true;
        if (btnSubmitReject) btnSubmitReject.disabled = true;

        const formData = new FormData();
        formData.append('nim', nim);
        formData.append('file_type', docKey);
        formData.append('status', status);
        formData.append('comment', comment);

        fetch('<?= site_url("dosenwali/update_file_approval_ajax"); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (btnApprove) btnApprove.disabled = false;
            if (btnReject) btnReject.disabled = false;
            if (btnSubmitReject) btnSubmitReject.disabled = false;

            if (res.success) {
                // Update local state in window.mhsDataMap
                if (window.mhsDataMap && window.mhsDataMap[nim]) {
                    window.mhsDataMap[nim][`status_file_${docKey}`] = status;
                    window.mhsDataMap[nim][`catatan_file_${docKey}`] = comment;
                    if (window.mhsDataMap[nim].berkas_map && window.mhsDataMap[nim].berkas_map[docKey]) {
                        window.mhsDataMap[nim].berkas_map[docKey].status_verifikasi = (status === 'Approved' ? 'Valid' : (status === 'Rejected' ? 'Invalid' : 'Pending'));
                        window.mhsDataMap[nim].berkas_map[docKey].catatan = comment;
                    }
                    if (window.mhsDataMap[nim].files && window.mhsDataMap[nim].files[docKey]) {
                        window.mhsDataMap[nim].files[docKey].status = status;
                        window.mhsDataMap[nim].files[docKey].note = comment;
                    }
                }

                // 1. Tutup / hilangkan tab preview berkas yang baru saja di-aksi
                const pIdx = (window.activePreviews || []).findIndex(p => String(p.nim).trim() === String(nim).trim() && String(p.docKey).trim() === String(docKey).trim());
                if (pIdx > -1) {
                    window.activePreviews.splice(pIdx, 1);
                }

                // 2. Refresh tampilan panel berkas & sisa kartu preview
                refreshLihatBerkasView();

                // 3. Update Main Table Badge & Summary Badges
                const tableBadge = document.getElementById(`badge_doc_${nim}_${docKey}`);
                if (tableBadge) {
                    if (status === 'Approved') {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200';
                    } else if (status === 'Rejected') {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-rose-100/90 text-rose-700 hover:bg-rose-200';
                    } else {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60';
                    }
                }
                updateTableBerkasSummaryBadges(nim);

                const docTitle = docKey.toUpperCase();
                showDWToast((status === 'Approved') ? `Berkas ${docTitle} disetujui (Valid)! Tab pratinjau ditutup.` : `Catatan revisi berkas ${docTitle} berhasil dikirim! Tab pratinjau ditutup.`, true);
            } else {
                showDWToast(res.message || 'Gagal memperbarui status berkas.', false);
            }
        })
        .catch(err => {
            if (btnApprove) btnApprove.disabled = false;
            if (btnReject) btnReject.disabled = false;
            if (btnSubmitReject) btnSubmitReject.disabled = false;
            console.error(err);
            showDWToast('Terjadi kesalahan jaringan.', false);
        });
    }

    function toggleStudentRejectBox(nim) {
        const box = document.getElementById(`studentRejectBox_${nim}`);
        if (!box) return;
        box.classList.toggle('hidden');
        if (!box.classList.contains('hidden')) {
            const ta = document.getElementById(`catatanMhs_${nim}`);
            if (ta) ta.focus();
        }
    }

    function setStudentPresetNote(nim, noteText) {
        const ta = document.getElementById(`catatanMhs_${nim}`);
        if (ta) {
            ta.value = noteText;
            ta.focus();
        }
    }

    function submitStudentRejectWali(nim) {
        if (isMhsStageLocked(nim)) {
            showDWToast('Pendaftaran telah disetujui di tahap berikutnya.', false);
            return;
        }

        const catatanEl = document.getElementById(`catatanMhs_${nim}`);
        const catatan = catatanEl ? catatanEl.value.trim() : '';

        if (!catatan) {
            showDWToast('Alasan penolakan / catatan revisi wajib diisi!', false);
            if (catatanEl) catatanEl.focus();
            return;
        }

        const btn = document.getElementById(`btnSubmitRejectMhs_${nim}`);
        if (btn) btn.disabled = true;

        const formData = new FormData();
        formData.append('nim', nim);
        formData.append('status', 'Rejected');
        formData.append('catatan_wali', catatan);

        fetch('<?= site_url("dosenwali/ajax_approval"); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (btn) btn.disabled = false;
            if (res.success) {
                if (window.mhsDataMap && window.mhsDataMap[nim]) {
                    window.mhsDataMap[nim].status_approval_wali = 'Rejected';
                    window.mhsDataMap[nim].catatan_wali = catatan;
                    window.mhsDataMap[nim].current_stage = 'Dosen Wali (Revisi)';
                }

                // Tutup semua tab preview berkas milik mahasiswa ini jika ada
                window.activePreviews = (window.activePreviews || []).filter(p => String(p.nim).trim() !== String(nim).trim());
                refreshLihatBerkasView();
                updateTableBerkasSummaryBadges(nim);
                showDWToast(`Catatan revisi pendaftaran mahasiswa ${nim} berhasil dikirim!`, true);

                if (typeof pollRealtimeData === 'function') {
                    lastDataHash = '';
                    pollRealtimeData();
                }
            } else {
                showDWToast(res.message || 'Gagal menyimpan penolakan.', false);
            }
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            console.error(err);
            showDWToast('Terjadi kesalahan jaringan.', false);
        });
    }

    function confirmApproveStudentWali(nim) {
        if (isMhsStageLocked(nim)) {
            showDWToast('Pendaftaran telah disetujui di tahap berikutnya.', false);
            return;
        }

        const mhs = window.mhsDataMap ? window.mhsDataMap[nim] : {};
        const fullName = (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : (mhs.nama || 'Mahasiswa ' + nim)).trim();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Setujui Pendaftaran TA?',
                html: `
                    <div class="text-xs text-slate-600 text-left space-y-2 mt-2">
                        <p>Anda akan menyetujui pendaftaran Tugas Akhir untuk mahasiswa:</p>
                        <div class="p-2.5 bg-orange-50 border border-orange-200 rounded-xl font-medium">
                            <p class="font-bold text-slate-900">${fullName}</p>
                            <p class="font-mono text-orange-700 text-[11px]">${nim}</p>
                        </div>
                        <p class="text-slate-500">Seluruh berkas persyaratan akan otomatis ditandai <strong>Valid</strong> dan pendaftaran diteruskan ke tahap <strong>Admin Layanan</strong>.</p>
                    </div>
                `,
                icon: 'question',
                iconColor: '#10b981',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Ya, Setujui Pendaftaran',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl shadow-2xl border border-emerald-100',
                    confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs cursor-pointer shadow-md shadow-emerald-600/20',
                    cancelButton: 'rounded-xl font-semibold px-4 py-2 text-xs cursor-pointer'
                }
            }).then(result => {
                if (result.isConfirmed) {
                    processApproveStudentWali(nim, fullName);
                }
            });
        } else {
            if (confirm(`Yakin ingin menyetujui pendaftaran ${fullName} (${nim})? Pengajuan akan diteruskan ke Admin Layanan.`)) {
                processApproveStudentWali(nim, fullName);
            }
        }
    }

    function processApproveStudentWali(nim, fullName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Menyimpan Persetujuan...',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
        }

        const formData = new FormData();
        formData.append('nim', nim);
        formData.append('status', 'Approved');
        formData.append('catatan_wali', '');

        fetch('<?= site_url("dosenwali/ajax_approval"); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (typeof Swal !== 'undefined') Swal.close();
            if (res.success) {
                if (window.mhsDataMap && window.mhsDataMap[nim]) {
                    window.mhsDataMap[nim].status_approval_wali = 'Approved';
                    window.mhsDataMap[nim].current_stage = 'Admin Layanan';
                    const docList = getDocList();
                    docList.forEach(d => {
                        window.mhsDataMap[nim][`status_file_${d.key}`] = 'Approved';
                        window.mhsDataMap[nim][`catatan_file_${d.key}`] = '';
                    });
                }

                // Tutup semua tab preview berkas milik mahasiswa ini jika ada
                window.activePreviews = (window.activePreviews || []).filter(p => String(p.nim).trim() !== String(nim).trim());
                refreshLihatBerkasView();
                updateTableBerkasSummaryBadges(nim);

                showDWToast(`Pendaftaran ${fullName} (${nim}) berhasil disetujui!`, true);

                if (typeof pollRealtimeData === 'function') {
                    lastDataHash = '';
                    pollRealtimeData();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Gagal',
                        text: res.message || 'Gagal menyimpan persetujuan.',
                        icon: 'error',
                        confirmButtonColor: '#ea580c'
                    });
                } else {
                    alert(res.message || 'Gagal menyimpan persetujuan.');
                }
            }
        })
        .catch(err => {
            if (typeof Swal !== 'undefined') Swal.close();
            console.error(err);
            showDWToast('Terjadi kesalahan jaringan.', false);
        });
    }

    function togglePreviewIframeInteraction(nim, docKey) {
        const iframe = document.getElementById(`iframePreviewBerkas_${nim}_${docKey}`);
        const btn = document.getElementById(`btnPreviewInteract_${nim}_${docKey}`);
        const icon = document.getElementById(`iconPreviewInteract_${nim}_${docKey}`);
        if (!iframe) return;

        const isLocked = iframe.classList.contains('pointer-events-none');
        if (isLocked) {
            iframe.classList.remove('pointer-events-none');
            if (btn) {
                btn.className = 'w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center text-xs transition cursor-pointer shadow-2xs';
                btn.title = 'Mode Scroll Aktif. Klik untuk kunci kursor kembali.';
            }
            if (icon) icon.className = 'fa-solid fa-hand text-xs';
        } else {
            iframe.classList.add('pointer-events-none');
            if (btn) {
                btn.className = 'w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer';
                btn.title = 'Kursor Normal (Terkunci). Klik jika ingin mengaktifkan scroll di dalam berkas.';
            }
            if (icon) icon.className = 'fa-solid fa-arrow-pointer text-xs';
        }
    }

    function closeSinglePreview(index) {
        if (index < 0 || index >= window.activePreviews.length) return;
        window.activePreviews.splice(index, 1);
        refreshLihatBerkasView();
    }

    function closeAllPreviews() {
        window.activePreviews = [];
        refreshLihatBerkasView();
    }

    function removeStudentFromLihatBerkas(nim) {
        const nimStr = String(nim).trim();
        window.activeLihatBerkasNims = (window.activeLihatBerkasNims || []).filter(n => String(n).trim() !== nimStr);
        window.activePreviews = (window.activePreviews || []).filter(p => String(p.nim).trim() !== nimStr);

        if (window.activeLihatBerkasNims.length === 0) {
            closeLihatBerkasPanel();
        } else {
            refreshLihatBerkasView();
        }
        updateTableButtonHighlights();
    }

    function closeLihatBerkasPanel() {
        const container = document.getElementById('lihatBerkasContainer');
        if (container) {
            container.style.display = 'none';
            container.classList.add('hidden');
            container.classList.remove('flex');
        }
        window.activePreviews = [];
        window.activeLihatBerkasNims = [];

        const wrapper = document.getElementById('wrapperDaftarMhs');
        if (wrapper) wrapper.innerHTML = '';

        const previewWrapper = document.getElementById('wrapperPreviewBerkas');
        if (previewWrapper) {
            previewWrapper.innerHTML = '';
            previewWrapper.classList.add('hidden');
        }

        updateTableButtonHighlights();
    }

    function updateTableButtonHighlights() {
        const activeNims = (window.activeLihatBerkasNims || []).map(n => String(n).trim());
        document.querySelectorAll('[id^="btn_lihat_berkas_"]').forEach(btn => {
            const nim = String(btn.id.replace('btn_lihat_berkas_', '')).trim();
            const isActive = activeNims.includes(nim);
            if (isActive) {
                btn.className = 'inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-600 text-white font-bold text-xs transition cursor-pointer shadow-md ring-2 ring-orange-400 scale-105';
                btn.title = 'Sedang Melihat Berkas';
            } else {
                btn.className = 'inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 font-bold text-xs transition cursor-pointer shadow-2xs';
                btn.title = 'Lihat Berkas';
            }
        });
    }

    window.toggleLihatBerkasPanel = toggleLihatBerkasPanel;
    window.renderAllLihatBerkasCards = renderAllLihatBerkasCards;
    window.previewBerkasItem = previewBerkasItem;
    window.renderAllPreviewCards = renderAllPreviewCards;
    window.togglePreviewIframeInteraction = togglePreviewIframeInteraction;
    window.closeSinglePreview = closeSinglePreview;
    window.closeAllPreviews = closeAllPreviews;
    window.removeStudentFromLihatBerkas = removeStudentFromLihatBerkas;
    window.closeLihatBerkasPanel = closeLihatBerkasPanel;
    window.updateTableButtonHighlights = updateTableButtonHighlights;
    window.togglePreviewRejectBox = togglePreviewRejectBox;
    window.setPreviewPresetNote = setPreviewPresetNote;
    window.submitPreviewDocApproval = submitPreviewDocApproval;
    window.openStudentBerkasPreview = openStudentBerkasPreview;
    window.openQuickDocReview = openStudentBerkasPreview;
    window.updateTableBerkasSummaryBadges = updateTableBerkasSummaryBadges;
    window.toggleStudentRejectBox = toggleStudentRejectBox;
    window.setStudentPresetNote = setStudentPresetNote;
    window.submitStudentRejectWali = submitStudentRejectWali;
    window.confirmApproveStudentWali = confirmApproveStudentWali;

    // Keyboard ESC Shortcut untuk menutup modal & panel
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Tutup pratinjau terakhir jika sedang aktif
            if (window.activePreviews && window.activePreviews.length > 0) {
                closeSinglePreview(window.activePreviews.length - 1);
                return;
            }
            // Tutup container lihat berkas jika aktif
            const lihatContainer = document.getElementById('lihatBerkasContainer');
            if (lihatContainer && lihatContainer.style.display !== 'none' && !lihatContainer.classList.contains('hidden')) {
                closeLihatBerkasPanel();
                return;
            }
            const quickModal = document.getElementById('quickDocReviewModal');
            if (quickModal && !quickModal.classList.contains('hidden')) {
                closeQuickDocReviewModal();
                return;
            }
            const pdfModal = document.getElementById('pdfModalDW');
            if (pdfModal && !pdfModal.classList.contains('hidden')) {
                closePdfModalDW();
                return;
            }
            const batchModal = document.getElementById('batchReviewModalDW');
            if (batchModal && !batchModal.classList.contains('hidden')) {
                closeBatchModalDW();
            }
        }
    });
    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>

