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

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d">
                        W
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block">Dosen Wali Akademik</span>
                    </div>
                </div>

                <!-- User Profile Pill (Kode, Nama, Kejuruan Dosen Wali) -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800 leading-tight"><?= $dosen_info['nama_dosen'] ?? 'Alif Dosen, S.T., M.T.'; ?></span>
                        <div class="flex items-center justify-end gap-2 text-[10px] font-semibold text-slate-500 mt-0.5">
                            <span class="px-2 py-0.5 bg-orange-100/90 text-orange-700 rounded-md border border-orange-200/80 font-bold"><?= $dosen_info['kode_dosen'] ?? 'DW-001'; ?></span>
                            <span>Prodi: <strong class="text-slate-700"><?= $dosen_info['kejuruan'] ?? 'Informatika / DKV'; ?></strong></span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-base box-3d shadow-xs">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container (Full Wide Layout) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-10 flex-grow">

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">OVERVIEW BIMBINGAN</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Wali</h1>
                <p class="text-slate-600 text-xs mt-1 font-normal">Kelola persetujuan pendaftaran Tugas Akhir mahasiswa bimbingan Anda secara praktis.</p>
            </div>
            <div class="px-4 py-2 bg-white/90 rounded-xl border border-orange-200 shadow-xs text-xs font-semibold text-slate-700 flex items-center gap-2 w-fit">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Approval System Active</span>
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
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Mahasiswa</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statTotalMhs"><?= $totalMhs; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Bimbingan Akademik</p>
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
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statPendingMhs"><?= $pendingCount; ?> <span class="text-xs font-semibold text-cyan-600 font-normal">(<?= $totalMhs > 0 ? round(($pendingCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Perlu Ditolak / Disetujui</p>
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
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statApprovedMhs"><?= $approvedCount; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $totalMhs > 0 ? round(($approvedCount/$totalMhs)*100) : 0; ?>%)</span></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Lanjut ke Admin</p>
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
                            <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight" id="statRejectedMhs"><?= $rejectedCount; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Telah Ditolak / Perlu Revisi</p>
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
                            <th class="py-3.5 px-3 text-center w-8">
                                <input type="checkbox" id="checkAllStudents" onchange="toggleAllCheckboxesDW(this)" title="Pilih Semua Mahasiswa" class="w-4 h-4 text-orange-600 rounded border-slate-600 focus:ring-orange-500 cursor-pointer">
                            </th>
                            <th class="py-3.5 px-4 pl-3">MAHASISWA</th>
                            <th class="py-3.5 px-4">JUDUL RENCANA TA</th>
                            <th class="py-3.5 px-4 text-center">STATUS 4 BERKAS</th>
                            <th class="py-3.5 px-4 text-center">DOSEN WALI</th>
                            <th class="py-3.5 px-4 text-center">TAHAP SAAT INI</th>
                            <th class="py-3.5 px-4 pr-6 text-right">AKSI</th>
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
                                    <td class="py-4 px-3 text-center whitespace-nowrap">
                                        <input type="checkbox" name="batch_select[]" value="<?= $mhs['nim']; ?>" 
                                               data-name="<?= htmlspecialchars($full_name); ?>" 
                                               onchange="updateBatchBarDW()"
                                               class="student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                    </td>
                                    
                                    <!-- Mahasiswa Info -->
                                    <td class="py-4 px-4 pl-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-orange-100 border border-orange-200 text-orange-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
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
                                    <td class="py-4 px-4 text-slate-700 max-w-xs leading-relaxed text-xs">
                                        <?php if(!empty($mhs['judul_1'])): ?>
                                            <button type="button" onclick="openQuickDocReview('<?= $mhs['nim']; ?>', 'judul')" class="text-left font-semibold hover:text-orange-600 transition-colors group cursor-pointer inline-flex items-center gap-1.5" title="Klik untuk review Judul TA">
                                                <span><?= character_limiter($mhs['judul_1'], 50); ?></span>
                                                <i class="fa-solid fa-pen-to-square text-[10px] text-slate-400 group-hover:text-orange-600 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-slate-400 italic font-normal">Belum Mendaftar</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status 4 Berkas -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 text-[10px] font-mono shadow-2xs">
                                            <button type="button" 
                                                    onclick="openQuickDocReview('<?= $mhs['nim']; ?>', 'ksm')" 
                                                    id="badge_doc_<?= $mhs['nim']; ?>_ksm"
                                                    title="Review Berkas KSM - <?= htmlspecialchars($full_name); ?>"
                                                    class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer <?= $ksm_st === 'Approved' ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ($ksm_st === 'Rejected' ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60'); ?>">
                                                KSM
                                            </button>
                                            <span class="text-slate-300">·</span>
                                            <button type="button" 
                                                    onclick="openQuickDocReview('<?= $mhs['nim']; ?>', 'transkrip')" 
                                                    id="badge_doc_<?= $mhs['nim']; ?>_transkrip"
                                                    title="Review Transkrip Nilai - <?= htmlspecialchars($full_name); ?>"
                                                    class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer <?= $trs_st === 'Approved' ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ($trs_st === 'Rejected' ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60'); ?>">
                                                TRS
                                            </button>
                                            <span class="text-slate-300">·</span>
                                            <button type="button" 
                                                    onclick="openQuickDocReview('<?= $mhs['nim']; ?>', 'pernyataan')" 
                                                    id="badge_doc_<?= $mhs['nim']; ?>_pernyataan"
                                                    title="Review Surat Pernyataan - <?= htmlspecialchars($full_name); ?>"
                                                    class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer <?= $prn_st === 'Approved' ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ($prn_st === 'Rejected' ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60'); ?>">
                                                SRT
                                            </button>
                                            <span class="text-slate-300">·</span>
                                            <button type="button" 
                                                    onclick="openQuickDocReview('<?= $mhs['nim']; ?>', 'bebas_lab')" 
                                                    id="badge_doc_<?= $mhs['nim']; ?>_bebas_lab"
                                                    title="Review Bebas Lab & Perpus - <?= htmlspecialchars($full_name); ?>"
                                                    class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer <?= $lab_st === 'Approved' ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ($lab_st === 'Rejected' ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60'); ?>">
                                                LAB
                                            </button>
                                        </div>
                                    </td>

                                    <!-- Status Approval Dosen Wali -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full border shadow-xs inline-block <?= $badgeStyle; ?>"><?= $st; ?></span>
                                    </td>

                                    <!-- Tahap Saat Ini -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block"><?= $mhs['current_stage'] ?? 'Draft'; ?></span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-4 pr-6 text-right whitespace-nowrap">
                                        <!-- Tombol Lama Detail & Approval (Dicomment)
                                        <a href="<?= site_url('dosenwali/detail_mahasiswa/' . $mhs['nim']); ?>" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                            <i class="bi bi-search text-xs"></i> Detail & Approval
                                        </a>
                                        -->
                                        <!-- Tombol Baru: Lihat Berkas -->
                                        <button type="button" 
                                                onclick="toggleLihatBerkasPanel('<?= $mhs['nim']; ?>')" 
                                                id="btn_lihat_berkas_<?= $mhs['nim']; ?>"
                                                class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs cursor-pointer"
                                                title="Lihat 4 Berkas Persyaratan TA">
                                            <i class="fa-solid fa-folder-open text-xs"></i> Lihat Berkas
                                        </button>
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
    <div id="batchReviewModalDW" style="display: none;" onclick="if(event.target === this) closeBatchModalDW()" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-3 sm:p-5 overflow-y-auto">
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

    <!-- Quick Single / Comparative Document Review Modal -->
    <div id="quickDocReviewModal" style="display: none;" onclick="if(event.target === this) closeQuickDocReviewModal()" class="fixed inset-0 z-[65] bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5 overflow-y-auto">
        <div id="quickDocModalDialog" class="bg-white rounded-3xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 transition-all duration-300 mx-auto my-auto relative" onclick="event.stopPropagation()">
            
            <!-- In-Modal Aligned Notification Toast (Khusus Card / Quick Review Modal) -->
            <div id="quickDocToast" class="absolute top-4 left-1/2 -translate-x-1/2 z-[70] transform transition-all duration-300 -translate-y-16 opacity-0 pointer-events-none">
                <div class="bg-slate-900/95 text-white px-4 py-2 rounded-2xl shadow-2xl flex items-center gap-2.5 border border-slate-700 backdrop-blur-md">
                    <div class="w-5 h-5 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" id="quickDocToastIcon">
                        <i class="fa-solid fa-circle-info text-amber-400"></i>
                    </div>
                    <span class="text-xs font-bold whitespace-nowrap" id="quickDocToastMsg">Pemberitahuan</span>
                </div>
            </div>

            <!-- Modal Header -->
            <div class="p-4 px-6 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-4 shrink-0">
                <!-- Student Info & Title -->
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-base shrink-0 shadow-2xs">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-bold text-white tracking-tight" id="quickDocStudentName">Nama Mahasiswa</h3>
                            <span class="px-2 py-0.5 rounded-md bg-white/10 text-orange-300 font-mono text-[11px] font-bold" id="quickDocStudentNim">NIM</span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate mt-0.5" id="quickDocJudulTa">Judul Tugas Akhir</p>
                    </div>
                </div>

                <!-- Document & Usulan Selector / Multi-Card Toggles -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[11px] font-bold text-slate-400 mr-1 hidden sm:inline">Pilih / Bandingkan:</span>
                    
                    <!-- Usulan: Judul & Skema TA (Disatukan) -->
                    <button type="button" onclick="toggleDocInQuickReview('judul')" id="btnToggleDoc_judul" class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-[11px] flex items-center gap-1.5" title="Review Usulan Judul &amp; Skema TA">
                        <i class="fa-solid fa-graduation-cap text-orange-400 text-xs"></i>
                        <span>JUDUL &amp; SKEMA</span>
                    </button>

                    <span class="text-slate-600 text-xs hidden sm:inline">|</span>

                    <!-- 4 Dokumen Persyaratan -->
                    <div class="inline-flex items-center bg-slate-800/90 p-1 rounded-xl border border-slate-700 gap-1 text-[11px] font-mono">
                        <button type="button" onclick="toggleDocInQuickReview('ksm')" id="btnToggleDoc_ksm" class="px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer text-slate-300 hover:text-white hover:bg-slate-700" title="Review KSM">
                            KSM
                        </button>
                        <button type="button" onclick="toggleDocInQuickReview('transkrip')" id="btnToggleDoc_transkrip" class="px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer text-slate-300 hover:text-white hover:bg-slate-700" title="Review Transkrip">
                            TRS
                        </button>
                        <button type="button" onclick="toggleDocInQuickReview('pernyataan')" id="btnToggleDoc_pernyataan" class="px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer text-slate-300 hover:text-white hover:bg-slate-700" title="Review Surat Pernyataan">
                            SRT
                        </button>
                        <button type="button" onclick="toggleDocInQuickReview('bebas_lab')" id="btnToggleDoc_bebas_lab" class="px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer text-slate-300 hover:text-white hover:bg-slate-700" title="Review Bebas Lab & Perpus">
                            LAB
                        </button>
                    </div>

                    <!-- Close Modal Button -->
                    <button type="button" onclick="closeQuickDocReviewModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-sm transition-colors cursor-pointer ml-1" title="Tutup Modal (ESC)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Cards Body Grid (1 Column if 1 doc, 2 Columns if 2 or more docs) -->
            <div class="p-4 sm:p-5 overflow-y-auto flex-1 bg-slate-50">
                <div id="quickDocCardsContainer" class="grid grid-cols-1 gap-5">
                    <!-- Cards dynamically rendered here -->
                </div>
            </div>

            <!-- Modal Footer Note -->
            <div class="p-3 px-6 bg-white border-t border-slate-200 flex items-center justify-between shrink-0 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-orange-500 text-xs"></i>
                    <span>Pilih 2 berkas (misal <strong>KSM</strong> &amp; <strong>TRS</strong>) untuk melihat &amp; membandingkan 2 pop-up berkas berdampingan.</span>
                </span>
                <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Tekan <strong>ESC</strong> untuk menutup</span>
            </div>

        </div>
    </div>

    <!-- Floating Non-Blocking Container: Lihat & Pratinjau Berkas (Layar Luar Tetap Bebas Diklik) -->
    <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-50 flex items-center justify-center p-3 sm:p-5 gap-4 sm:gap-5 overflow-x-auto" style="display: none;">
        
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
    <div id="dwToast" class="fixed top-6 right-6 z-50 transform transition-all duration-300 translate-y-[-150%] opacity-0 pointer-events-none">
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
                            ? `<button type="button" onclick="openQuickDocReview('${mhs.nim}', 'judul')" class="text-left font-semibold hover:text-orange-600 transition-colors group cursor-pointer inline-flex items-center gap-1" title="Klik untuk review Judul TA"><span>${mhs.judul.length > 50 ? mhs.judul.substring(0, 50) + '...' : mhs.judul}</span><i class="fa-solid fa-pen-to-square text-[10px] text-slate-400 group-hover:text-orange-600 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></i></button>`
                            : '<span class="text-slate-400 italic font-normal">Belum Mendaftar</span>';
                        const isChecked = currentlyChecked.includes(mhs.nim) ? 'checked' : '';

                        const ksmClass = (mhs.status_file_ksm === 'Approved') ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ((mhs.status_file_ksm === 'Rejected') ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60');
                        const trsClass = (mhs.status_file_transkrip === 'Approved') ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ((mhs.status_file_transkrip === 'Rejected') ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60');
                        const srtClass = (mhs.status_file_pernyataan === 'Approved') ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ((mhs.status_file_pernyataan === 'Rejected') ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60');
                        const labClass = (mhs.status_file_bebas_lab === 'Approved') ? 'bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200' : ((mhs.status_file_bebas_lab === 'Rejected') ? 'bg-rose-100/90 text-rose-700 hover:bg-rose-200' : 'bg-white text-slate-500 hover:bg-orange-100 hover:text-orange-700 border border-slate-200/60');

                        const prodiHtml = mhs.konsentrasi ? `<span>•</span><span class="text-orange-600 font-medium">${mhs.konsentrasi}</span>` : '';

                        return `
                            <tr class="hover:bg-orange-50/50 transition-all duration-150 mhs-row" data-status="${st}" data-nim="${(mhs.nim || '').toLowerCase()}" data-nama="${(mhs.nama || '').toLowerCase()}" data-judul="${(mhs.judul || '').toLowerCase()}" data-stage="${(mhs.current_stage || 'draft').toLowerCase()}">
                                <td class="py-4 px-3 text-center whitespace-nowrap">
                                    <input type="checkbox" name="batch_select[]" value="${mhs.nim}" data-name="${mhs.nama}" ${isChecked} onchange="updateBatchBarDW()" class="student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="py-4 px-4 pl-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-orange-100 border border-orange-200 text-orange-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
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
                                <td class="py-4 px-4 text-slate-700 max-w-xs leading-relaxed text-xs">${judulShort}</td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 text-[10px] font-mono shadow-2xs">
                                        <button type="button" onclick="openQuickDocReview('${mhs.nim}', 'ksm')" id="badge_doc_${mhs.nim}_ksm" class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer ${ksmClass}">KSM</button><span class="text-slate-300">·</span>
                                        <button type="button" onclick="openQuickDocReview('${mhs.nim}', 'transkrip')" id="badge_doc_${mhs.nim}_transkrip" class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer ${trsClass}">TRS</button><span class="text-slate-300">·</span>
                                        <button type="button" onclick="openQuickDocReview('${mhs.nim}', 'pernyataan')" id="badge_doc_${mhs.nim}_pernyataan" class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer ${srtClass}">SRT</button><span class="text-slate-300">·</span>
                                        <button type="button" onclick="openQuickDocReview('${mhs.nim}', 'bebas_lab')" id="badge_doc_${mhs.nim}_bebas_lab" class="px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer ${labClass}">LAB</button>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 font-semibold text-[11px] rounded-full border shadow-xs inline-block ${badgeStyle}">${st}</span>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block">${mhs.current_stage || 'Draft'}</span>
                                </td>
                                <td class="py-4 px-4 pr-6 text-right whitespace-nowrap">
                                    <!-- Tombol Lama Detail & Approval (Dicomment)
                                    <a href="${mhs.detail_url}" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                        <i class="bi bi-search text-xs"></i> Detail & Approval
                                    </a>
                                    -->
                                    <!-- Tombol Baru: Lihat Berkas -->
                                    <button type="button" 
                                            onclick="toggleLihatBerkasPanel('${mhs.nim}')" 
                                            id="btn_lihat_berkas_${mhs.nim}"
                                            class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs cursor-pointer"
                                            title="Lihat 4 Berkas Persyaratan TA">
                                        <i class="fa-solid fa-folder-open text-xs"></i> Lihat Berkas
                                    </button>
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

                    const fileKsmStatus = getInitStatus(st.files?.ksm?.status);
                    const fileTrnStatus = getInitStatus(st.files?.transkrip?.status);
                    const filePrnStatus = getInitStatus(st.files?.pernyataan?.status);
                    const fileLabStatus = getInitStatus(st.files?.bebas_lab?.status);

                    const allStatuses = [rawJenis, rawJudul, fileKsmStatus, fileTrnStatus, filePrnStatus, fileLabStatus];
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
                        files: {
                            ksm: {
                                ...st.files.ksm,
                                status: fileKsmStatus,
                                note: st.files.ksm.note || ''
                            },
                            transkrip: {
                                ...st.files.transkrip,
                                status: fileTrnStatus,
                                note: st.files.transkrip.note || ''
                            },
                            pernyataan: {
                                ...st.files.pernyataan,
                                status: filePrnStatus,
                                note: st.files.pernyataan.note || ''
                            },
                            bebas_lab: {
                                ...st.files.bebas_lab,
                                status: fileLabStatus,
                                note: st.files.bebas_lab.note || ''
                            }
                        }
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
                { name: 'Usulan Judul TA', status: st.status_judul, secId: `batch_sec_judul_${st.nim}` },
                { name: 'KSM', status: st.files.ksm.status, secId: `batch_sec_file_ksm_${st.nim}` },
                { name: 'Transkrip', status: st.files.transkrip.status, secId: `batch_sec_file_transkrip_${st.nim}` },
                { name: 'Surat Pernyataan', status: st.files.pernyataan.status, secId: `batch_sec_file_pernyataan_${st.nim}` },
                { name: 'Bebas Lab', status: st.files.bebas_lab.status, secId: `batch_sec_file_bebas_lab_${st.nim}` }
            ];
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
                formData.append('decisions_json', JSON.stringify(window.batchStudentsDW.map(st => ({
                    nim: st.nim,
                    action: st.action,
                    status_jenis_ta: st.status_jenis_ta,
                    catatan_jenis_ta: st.catatan_jenis_ta,
                    status_judul: st.status_judul,
                    catatan_judul: st.catatan_judul,
                    status_file_ksm: st.files.ksm.status,
                    catatan_file_ksm: st.files.ksm.note,
                    status_file_transkrip: st.files.transkrip.status,
                    catatan_file_transkrip: st.files.transkrip.note,
                    status_file_pernyataan: st.files.pernyataan.status,
                    catatan_file_pernyataan: st.files.pernyataan.note,
                    status_file_bebas_lab: st.files.bebas_lab.status,
                    catatan_file_bebas_lab: st.files.bebas_lab.note,
                    catatan_wali: st.catatan_wali
                }))));
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
    // QUICK SINGLE & COMPARATIVE DOC REVIEW
    // ==========================================
    window.currentReviewNim = null;
    window.activeReviewDocs = [];
    window.quickDocDecisions = {};

    const docDefinitions = {
        'judul': {
            type: 'data',
            title: 'Usulan Judul & Skema Tugas Akhir',
            short: 'JUDUL & SKEMA',
            desc: 'Pemeriksaan usulan judul TA, translasi bahasa Inggris, peminatan dan skema pendaftaran.',
            statusField: 'status_judul',
            noteField: 'catatan_judul',
            icon: 'fa-solid fa-graduation-cap',
            presets: ['Judul Terlalu Luas', 'Sesuaikan dengan KK', 'Perbaiki Tata Bahasa (EYD)', 'Judul Terlalu Singkat / Kurang Spesifik', 'Skema TA Tidak Sesuai']
        },
        'ksm': {
            type: 'file',
            title: '1. KSM (Kartu Studi Mahasiswa)',
            short: 'KSM',
            desc: 'Bukti KRS semester aktif yang memuat mata kuliah Tugas Akhir.',
            fileField: 'file_ksm',
            statusField: 'status_file_ksm',
            noteField: 'catatan_file_ksm',
            icon: 'fa-solid fa-file-lines',
            presets: ['Tanpa TTD Dosen Wali', 'Mata Kuliah TA Belum Ada', 'File Buram / Tidak Jelas']
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
            presets: ['Belum Update Semester Terbaru', 'SKS Kelulusan Kurang', 'Belum Tervalidasi Resmi']
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
            presets: ['Tanpa Materai Rp 10.000', 'Belum Ditandatangani', 'Format Surat Salah']
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
            presets: ['Tanpa Stempel Resmi Lab', 'Pinjaman Alat Lab Belum Lunas', 'Buku Perpus Belum Kembali']
        }
    };

    function resolveDocPdfUrl(filename) {
        if (!filename) return '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
        if (filename.startsWith('http://') || filename.startsWith('https://')) return filename;
        if (filename.startsWith('uploads/')) return '<?= base_url(); ?>' + filename;
        return '<?= base_url("uploads/persyaratan_ta/"); ?>' + filename;
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
        if (window.currentReviewNim !== nim) {
            window.currentReviewNim = nim;
            window.activeReviewDocs = [docKey];
            window.quickDocDecisions = {};
        } else {
            if (!window.activeReviewDocs.includes(docKey)) {
                if (window.activeReviewDocs.length >= 2) {
                    window.activeReviewDocs[1] = docKey;
                } else {
                    window.activeReviewDocs.push(docKey);
                }
            }
        }

        renderQuickDocReviewModal();
    }

    function toggleDocInQuickReview(docKey) {
        if (!window.currentReviewNim || !docDefinitions[docKey]) return;

        const idx = window.activeReviewDocs.indexOf(docKey);
        if (idx !== -1) {
            if (window.activeReviewDocs.length === 1) {
                showQuickDocToast('Minimal 1 item harus aktif ditampilkan.', 'info');
                return;
            }
            window.activeReviewDocs.splice(idx, 1);
        } else {
            if (window.activeReviewDocs.length >= 2) {
                window.activeReviewDocs[1] = docKey;
            } else {
                window.activeReviewDocs.push(docKey);
            }
        }

        renderQuickDocReviewModal();
    }

    function removeDocFromQuickReview(docKey) {
        const idx = window.activeReviewDocs.indexOf(docKey);
        if (idx !== -1) {
            window.activeReviewDocs.splice(idx, 1);
        }
        if (window.activeReviewDocs.length === 0) {
            closeQuickDocReviewModal();
        } else {
            renderQuickDocReviewModal();
        }
    }

    function renderQuickDocReviewModal() {
        const mhs = window.mhsDataMap[window.currentReviewNim];
        if (!mhs) return;

        const modal = document.getElementById('quickDocReviewModal');
        const dialog = document.getElementById('quickDocModalDialog');
        const container = document.getElementById('quickDocCardsContainer');

        const fullName = (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : (mhs.nama || 'Mahasiswa')).trim();
        document.getElementById('quickDocStudentName').textContent = fullName;
        document.getElementById('quickDocStudentNim').textContent = mhs.nim;
        document.getElementById('quickDocJudulTa').textContent = mhs.judul_1 || mhs.judul || 'Judul Rencana Tugas Akhir';

        // Update Toggle Buttons Status
        ['judul', 'ksm', 'transkrip', 'pernyataan', 'bebas_lab'].forEach(key => {
            const btn = document.getElementById('btnToggleDoc_' + key);
            if (!btn) return;
            const isActive = window.activeReviewDocs.includes(key);
            const stItem = mhs[docDefinitions[key].statusField] || 'Pending';

            if (key === 'judul') {
                if (isActive) {
                    btn.className = 'px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer bg-orange-600 text-white shadow-xs border border-orange-500 text-[11px] flex items-center gap-1.5';
                    btn.innerHTML = `<i class="fa-solid fa-check text-xs"></i><span>JUDUL &amp; SKEMA</span>`;
                } else {
                    const badgeColor = (stItem === 'Approved') ? 'text-emerald-400 hover:text-emerald-300' : ((stItem === 'Rejected') ? 'text-rose-400 hover:text-rose-300' : 'text-slate-300 hover:text-white');
                    btn.className = `px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer bg-slate-800 hover:bg-slate-700 border border-slate-700 text-[11px] flex items-center gap-1.5 ${badgeColor}`;
                    btn.innerHTML = `<i class="fa-solid fa-graduation-cap text-orange-400 text-xs"></i><span>JUDUL &amp; SKEMA</span>`;
                }
            } else {
                if (isActive) {
                    btn.className = 'px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer bg-orange-600 text-white shadow-xs';
                    btn.innerHTML = `<i class="fa-solid fa-check text-[10px] mr-1"></i>${docDefinitions[key].short}`;
                } else {
                    const badgeColor = (stItem === 'Approved') ? 'text-emerald-400 hover:text-emerald-300' : ((stItem === 'Rejected') ? 'text-rose-400 hover:text-rose-300' : 'text-slate-400 hover:text-white');
                    btn.className = `px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer hover:bg-slate-700 ${badgeColor}`;
                    btn.innerHTML = `+ ${docDefinitions[key].short}`;
                }
            }
        });

        if (window.activeReviewDocs.length <= 1) {
            dialog.className = 'bg-white rounded-3xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 transition-all duration-300 mx-auto my-auto relative';
            container.className = 'grid grid-cols-1 gap-5';
        } else {
            dialog.className = 'bg-white rounded-3xl max-w-7xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 transition-all duration-300 mx-auto my-auto relative';
            container.className = 'grid grid-cols-1 lg:grid-cols-2 gap-5';
        }

        container.innerHTML = window.activeReviewDocs.map(docKey => {
            const docMeta = docDefinitions[docKey];
            const currentStatus = window.quickDocDecisions[docKey] || mhs[docMeta.statusField] || 'Pending';
            const currentNote = mhs[docMeta.noteField] || '';

            const statusBadgeClass = (currentStatus === 'Approved')
                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                : ((currentStatus === 'Rejected') ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-100 text-slate-600 border-slate-200');
            const statusText = (currentStatus === 'Approved') ? 'Valid (Approved)' : ((currentStatus === 'Rejected') ? 'Kurang/Revisi' : 'Belum Dicek');

            const isApproved = (currentStatus === 'Approved');
            const isRejected = (currentStatus === 'Rejected');

            let bodyContentHtml = '';
            let headerSubtitle = '';
            let headerExtraBtn = '';

            if (docMeta.type === 'file') {
                const rawFilename = mhs[docMeta.fileField] || `${docKey}_${mhs.nim}.pdf`;
                const pdfUrl = resolveDocPdfUrl(rawFilename);
                headerSubtitle = `<span class="text-[10px] text-slate-400 font-mono truncate block">${rawFilename}</span>`;
                headerExtraBtn = `
                    <button type="button" onclick="toggleIframeInteraction('${docKey}')" id="btnInteract_${docKey}" 
                            class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs transition cursor-pointer" 
                            title="Kursor Normal (Terkunci). Klik jika ingin mengaktifkan scroll di dalam berkas">
                        <i class="fa-solid fa-arrow-pointer text-[10px]" id="iconInteract_${docKey}"></i>
                    </button>
                    <a href="${pdfUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center text-xs transition cursor-pointer" title="Buka di tab baru / unduh">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                `;
                bodyContentHtml = `
                    <div class="h-[340px] sm:h-[380px] bg-slate-200 relative border-b border-slate-200 overflow-hidden cursor-default select-none" id="frameWrapper_${docKey}">
                        <iframe id="iframeDoc_${docKey}" src="${pdfUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 pointer-events-none select-none" title="${docMeta.title}"></iframe>
                    </div>
                `;
            } else if (docKey === 'judul') {
                headerSubtitle = `<span class="text-[10px] text-slate-500 font-medium truncate block">Skema: ${mhs.jenis_ta || 'Reguler'} • ${mhs.nama_kk || mhs.kode_kk || 'DKV'}</span>`;
                bodyContentHtml = `
                    <div class="h-[340px] sm:h-[380px] bg-gradient-to-br from-slate-50 to-orange-50/30 p-5 overflow-y-auto border-b border-slate-200 flex flex-col justify-center space-y-3">
                        <!-- Skema, KK & Peminatan Info Bar -->
                        <div class="bg-white p-3 px-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] uppercase font-bold text-slate-400">Skema Pendaftaran:</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-extrabold border border-orange-200 flex items-center gap-1.5">
                                    <i class="fa-solid fa-layer-group text-[10px]"></i>
                                    <span>${mhs.jenis_ta || 'Reguler'}</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="text-slate-500 font-medium">KK: <strong class="text-slate-800">${mhs.nama_kk || mhs.kode_kk || 'DKV & Multimedia'}</strong></span>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-500 font-medium">Peminatan: <strong class="text-slate-800">${mhs.mhs_konsentrasi || mhs.konsentrasi || 'Desain Grafis'}</strong></span>
                            </div>
                        </div>

                        <!-- Usulan Judul Bahasa Indonesia -->
                        <div class="bg-white p-3.5 px-4 rounded-2xl border border-slate-200 shadow-2xs">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-flag text-rose-500"></i> Usulan Judul (Bahasa Indonesia)
                            </span>
                            <p class="text-sm font-bold text-slate-900 leading-relaxed">${mhs.judul_1 || mhs.judul || '<span class="italic text-slate-400 font-normal">Belum Mengisi Judul</span>'}</p>
                        </div>

                        <!-- Usulan Judul Bahasa Inggris -->
                        <div class="bg-white p-3 px-4 rounded-2xl border border-slate-200 shadow-2xs">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-globe text-blue-500"></i> Terjemahan Judul (English)
                            </span>
                            <p class="text-xs font-semibold text-slate-700 italic leading-relaxed">${mhs.judul_en || '<span class="italic text-slate-400 font-normal">Belum ada translasi bahasa Inggris</span>'}</p>
                        </div>
                    </div>
                `;
            }

            return `
                <div class="bg-white rounded-2xl border ${isApproved ? 'border-emerald-200 ring-1 ring-emerald-200/60' : (isRejected ? 'border-rose-200 ring-1 ring-rose-200/60' : 'border-slate-200')} shadow-sm flex flex-col overflow-hidden" id="quickCard_${docKey}">
                    <!-- Card Header -->
                    <div class="p-3.5 px-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-orange-100 border border-orange-200 text-orange-600 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                <i class="${docMeta.icon}"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-black text-slate-900 truncate">${docMeta.title}</h4>
                                ${headerSubtitle}
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${statusBadgeClass}" id="cardBadge_${docKey}">
                                ${statusText}
                            </span>
                            ${headerExtraBtn}
                            ${window.activeReviewDocs.length > 1 ? `
                            <button type="button" onclick="removeDocFromQuickReview('${docKey}')" class="w-7 h-7 rounded-lg hover:bg-rose-100 text-slate-400 hover:text-rose-600 flex items-center justify-center text-xs transition cursor-pointer" title="Tutup kartu ini">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Live Content / Viewer -->
                    ${bodyContentHtml}

                    <!-- Actions & Notes Section -->
                    <div class="p-4 space-y-3.5 bg-white flex-1 flex flex-col justify-between">
                        <div class="space-y-3">
                            <!-- Verification Status Choices -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-stamp text-orange-600"></i> Status Verifikasi:
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="setQuickCardDecision('${docKey}', 'Approved')" id="btnDec_${docKey}_Approved" 
                                            class="px-3 py-2 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer ${isApproved ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border-slate-200'}">
                                        <i class="fa-solid fa-circle-check"></i> Valid &amp; Lolos
                                    </button>
                                    <button type="button" onclick="setQuickCardDecision('${docKey}', 'Rejected')" id="btnDec_${docKey}_Rejected" 
                                            class="px-3 py-2 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer ${isRejected ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-slate-50 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border-slate-200'}">
                                        <i class="fa-solid fa-circle-xmark"></i> Kurang / Revisi
                                    </button>
                                </div>
                            </div>

                            <!-- Comment Box with Preset Chips -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">
                                        Catatan Revisi / Instruksi:
                                    </label>
                                    <span class="text-[10px] text-slate-400">Pesan feedback khusus ${docMeta.short}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1 mb-1">
                                    ${docMeta.presets.map(ps => `
                                        <button type="button" onclick="appendQuickCardNote('${docKey}', '${ps}')" 
                                                class="px-2 py-0.5 rounded-md bg-orange-50 hover:bg-orange-100 text-orange-700 text-[10px] font-medium border border-orange-200 transition cursor-pointer">
                                            + ${ps}
                                        </button>
                                    `).join('')}
                                </div>
                                <input type="text" id="inputNote_${docKey}" value="${currentNote}" placeholder="Tuliskan catatan perbaikan ${docMeta.short}..." 
                                       class="w-full px-3 py-1.5 bg-slate-50 focus:bg-white border border-slate-300 focus:border-orange-500 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Card Footer: Save Button -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                            <span class="text-[11px] font-medium text-slate-500" id="saveIndicator_${docKey}">
                                ${isApproved ? '<i class="fa-solid fa-check text-emerald-500 mr-1"></i>Status: Valid' : (isRejected ? '<i class="fa-solid fa-xmark text-rose-500 mr-1"></i>Status: Revisi' : '<i class="fa-solid fa-clock text-amber-500 mr-1"></i>Belum Diproses')}
                            </span>
                            <button type="button" onclick="saveQuickCardDecision('${docKey}')" id="btnSaveCard_${docKey}" 
                                    class="px-4 py-2 bg-slate-900 hover:bg-orange-600 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Status ${docMeta.short}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function setQuickCardDecision(docKey, status) {
        window.quickDocDecisions[docKey] = status;
        const btnApp = document.getElementById(`btnDec_${docKey}_Approved`);
        const btnRej = document.getElementById(`btnDec_${docKey}_Rejected`);
        const badge = document.getElementById(`cardBadge_${docKey}`);
        const card = document.getElementById(`quickCard_${docKey}`);

        if (status === 'Approved') {
            if (btnApp) btnApp.className = 'px-3 py-2 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-emerald-600 text-white border-emerald-600 shadow-sm';
            if (btnRej) btnRej.className = 'px-3 py-2 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border-slate-200';
            if (badge) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200';
                badge.textContent = 'Valid (Approved)';
            }
            if (card) {
                card.className = 'bg-white rounded-2xl border border-emerald-200 ring-1 ring-emerald-200/60 shadow-sm flex flex-col overflow-hidden';
            }
        } else if (status === 'Rejected') {
            if (btnApp) btnApp.className = 'px-3 py-2 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border-slate-200';
            if (btnRej) btnRej.className = 'px-3 py-2 rounded-xl text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-rose-600 text-white border-rose-600 shadow-sm';
            if (badge) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-rose-50 text-rose-700 border-rose-200';
                badge.textContent = 'Kurang/Revisi';
            }
            if (card) {
                card.className = 'bg-white rounded-2xl border border-rose-200 ring-1 ring-rose-200/60 shadow-sm flex flex-col overflow-hidden';
            }
        }
    }

    function appendQuickCardNote(docKey, text) {
        const input = document.getElementById(`inputNote_${docKey}`);
        if (!input) return;
        if (input.value.trim().length > 0) {
            if (!input.value.includes(text)) {
                input.value += ', ' + text;
            }
        } else {
            input.value = text;
        }
        input.focus();
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

    function toggleIframeInteraction(docKey) {
        const iframe = document.getElementById(`iframeDoc_${docKey}`);
        const btn = document.getElementById(`btnInteract_${docKey}`);
        const icon = document.getElementById(`iconInteract_${docKey}`);
        if (!iframe) return;

        const isCurrentlyLocked = iframe.classList.contains('pointer-events-none');
        if (isCurrentlyLocked) {
            iframe.classList.remove('pointer-events-none');
            if (btn) {
                btn.className = 'w-7 h-7 rounded-lg bg-orange-100 text-orange-700 border border-orange-200 flex items-center justify-center text-xs transition cursor-pointer shadow-2xs';
                btn.title = 'Mode Scroll Aktif. Klik untuk kunci kursor kembali.';
            }
            if (icon) icon.className = 'fa-solid fa-hand text-[10px] text-orange-600';
            showQuickDocToast('Mode scroll aktif (kursor bisa interaksi di dalam file).', 'info');
        } else {
            iframe.classList.add('pointer-events-none');
            if (btn) {
                btn.className = 'w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs transition cursor-pointer';
                btn.title = 'Kursor Normal (Terkunci). Klik jika ingin mengaktifkan scroll di dalam berkas.';
            }
            if (icon) icon.className = 'fa-solid fa-arrow-pointer text-[10px]';
            showQuickDocToast('Kursor terkunci (kursor tetap normal panah).', 'info');
        }
    }

    function saveQuickCardDecision(docKey) {
        const nim = window.currentReviewNim;
        if (!nim) return;

        const itemMeta = docDefinitions[docKey];
        if (!itemMeta) return;
        const docMeta = itemMeta;

        const status = window.quickDocDecisions[docKey] || (window.mhsDataMap[nim] ? window.mhsDataMap[nim][itemMeta.statusField] : 'Pending');
        if (status === 'Pending') {
            alert('Silakan pilih status keputusan (Valid atau Kurang/Revisi) terlebih dahulu!');
            return;
        }

        const inputNote = document.getElementById(`inputNote_${docKey}`);
        const comment = inputNote ? inputNote.value.trim() : '';

        if (status === 'Rejected' && !comment) {
            alert('Harap isi catatan revisi jika memilih status Kurang/Revisi!');
            if (inputNote) inputNote.focus();
            return;
        }

        const btnSave = document.getElementById(`btnSaveCard_${docKey}`);
        const originalBtnHtml = btnSave ? btnSave.innerHTML : '';
        if (btnSave) {
            btnSave.disabled = true;
            btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        }

        if (docKey === 'judul') {
            // Update Judul TA and Jenis TA in sync
            const fJudul = new FormData();
            fJudul.append('nim', nim);
            fJudul.append('status_judul', status);
            fJudul.append('catatan_judul', comment);

            const fJenis = new FormData();
            fJenis.append('nim', nim);
            fJenis.append('status_jenis_ta', status);
            fJenis.append('catatan_jenis_ta', comment);

            Promise.all([
                fetch('<?= site_url("dosenwali/update_judul_approval_ajax"); ?>', { method: 'POST', body: fJudul, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()),
                fetch('<?= site_url("dosenwali/update_jenis_approval_ajax"); ?>', { method: 'POST', body: fJenis, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json())
            ])
            .then(([resJudul, resJenis]) => {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalBtnHtml;
                }

                if (resJudul.success) {
                    if (window.mhsDataMap && window.mhsDataMap[nim]) {
                        window.mhsDataMap[nim]['status_judul'] = status;
                        window.mhsDataMap[nim]['catatan_judul'] = comment;
                        window.mhsDataMap[nim]['status_jenis_ta'] = status;
                        window.mhsDataMap[nim]['catatan_jenis_ta'] = comment;
                    }

                    const indicator = document.getElementById(`saveIndicator_${docKey}`);
                    if (indicator) {
                        indicator.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Tersimpan ke Database!';
                    }

                    showQuickDocToast('Status Usulan Judul &amp; Skema TA berhasil disimpan!', 'success');
                } else {
                    alert(resJudul.message || 'Gagal menyimpan status usulan TA.');
                }
            })
            .catch(err => {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalBtnHtml;
                }
                console.error(err);
                alert('Terjadi kesalahan jaringan saat menyimpan status.');
            });
            return;
        }

        // Saving file
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
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = originalBtnHtml;
            }

            if (res.success) {
                if (window.mhsDataMap && window.mhsDataMap[nim]) {
                    window.mhsDataMap[nim][itemMeta.statusField] = status;
                    window.mhsDataMap[nim][itemMeta.noteField] = comment;
                }

                // Update table badge
                const tableBadge = document.getElementById(`badge_doc_${nim}_${docKey}`);
                if (tableBadge) {
                    if (status === 'Approved') {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-emerald-100/90 text-emerald-700 hover:bg-emerald-200';
                    } else if (status === 'Rejected') {
                        tableBadge.className = 'px-1.5 py-0.5 rounded-md font-bold transition-all hover:scale-110 active:scale-95 cursor-pointer bg-rose-100/90 text-rose-700 hover:bg-rose-200';
                    }
                }

                const indicator = document.getElementById(`saveIndicator_${docKey}`);
                if (indicator) {
                    indicator.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Tersimpan ke Database!';
                }

                showQuickDocToast(`Status ${docMeta.title} berhasil disimpan!`, 'success');
            } else {
                alert(res.message || 'Gagal menyimpan status.');
            }
        })
        .catch(err => {
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = originalBtnHtml;
            }
            console.error(err);
            alert('Terjadi kesalahan jaringan saat menyimpan status.');
        });
    }

    function closeQuickDocReviewModal() {
        const modal = document.getElementById('quickDocReviewModal');
        const container = document.getElementById('quickDocCardsContainer');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        if (container) {
            container.innerHTML = '';
        }
        window.activeReviewDocs = [];
        window.currentReviewNim = null;

        const batchModal = document.getElementById('batchReviewModalDW');
        if (!batchModal || batchModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    }

    window.openQuickDocReview = openQuickDocReview;
    window.toggleDocInQuickReview = toggleDocInQuickReview;
    window.removeDocFromQuickReview = removeDocFromQuickReview;
    window.setQuickCardDecision = setQuickCardDecision;
    window.appendQuickCardNote = appendQuickCardNote;
    window.saveQuickCardDecision = saveQuickCardDecision;
    window.closeQuickDocReviewModal = closeQuickDocReviewModal;

    // ==========================================
    // FLOATING NON-BLOCKING: LIHAT & PREVIEW BERKAS
    // ==========================================
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
                if (data && data.length > 0) {
                    if (!window.mhsDataMap) window.mhsDataMap = {};
                    window.mhsDataMap[nim] = data[0];
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

        const docList = [
            { key: 'ksm', title: '1. KSM', fileField: 'file_ksm', statusField: 'status_file_ksm', icon: 'fa-solid fa-file-lines' },
            { key: 'transkrip', title: '2. Transkrip Nilai', fileField: 'file_transkrip', statusField: 'status_file_transkrip', icon: 'fa-solid fa-file-spreadsheet' },
            { key: 'pernyataan', title: '3. Surat Pernyataan', fileField: 'file_pernyataan', statusField: 'status_file_pernyataan', icon: 'fa-solid fa-file-contract' },
            { key: 'bebas_lab', title: '4. Bebas Lab & Perpus', fileField: 'file_bebas_lab', statusField: 'status_file_bebas_lab', icon: 'fa-solid fa-building-circle-check' }
        ];

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

            const itemsHtml = docList.map(doc => {
                const rawFilename = mhs[doc.fileField] || `${doc.key}_${nim}.pdf`;
                const pdfUrl = resolveDocPdfUrl(rawFilename);
                const status = mhs[doc.statusField] || 'Pending';
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
                                ${statusBadge}
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
                                <h4 class="text-xs font-bold text-white truncate max-w-[150px] sm:max-w-[180px]">${fullName}</h4>
                                <span class="px-1.5 py-0.2 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${nim}</span>
                            </div>
                        </div>
                        <button type="button" onclick="removeStudentFromLihatBerkas('${nim}')" class="w-6 h-6 rounded-md bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-[11px] font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- List of 4 Berkas -->
                    <div class="p-2.5 space-y-1.5 bg-slate-50/50 overflow-y-auto">
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

        // Tambahkan ke slot (maksimal 2 pratinjau berdampingan)
        if (window.activePreviews.length >= 2) {
            // Jika sudah 2 pratinjau, geser pratinjau terlama dan buka yang baru
            window.activePreviews.shift();
            window.activePreviews.push({ nim, docKey });
            if (typeof showDWToast === 'function') {
                showDWToast(`Maksimal 2 pratinjau. Menampilkan 2 dokumen terbaru.`, true);
            }
        } else {
            window.activePreviews.push({ nim, docKey });
        }

        refreshLihatBerkasView();
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

        const totalPreviews = window.activePreviews.length;
        const panelWidthClass = totalPreviews > 1 ? 'w-[430px] sm:w-[470px] lg:w-[490px]' : 'w-[500px] sm:w-[540px]';

        const docList = [
            { key: 'ksm', title: '1. KSM', fileField: 'file_ksm' },
            { key: 'transkrip', title: '2. Transkrip Nilai', fileField: 'file_transkrip' },
            { key: 'pernyataan', title: '3. Surat Pernyataan', fileField: 'file_pernyataan' },
            { key: 'bebas_lab', title: '4. Bebas Lab & Perpus', fileField: 'file_bebas_lab' }
        ];

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

            if (cardEl) {
                // Jangan sentuh iframe! Pertahankan dokumen yang sedang dibuka agar tidak hitam/reload
                cardEl.className = `preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 transition-all duration-300 ${panelWidthClass}`;
                const badgeEl = cardEl.querySelector('.preview-slot-badge');
                if (badgeEl) {
                    badgeEl.innerHTML = totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>';
                }
                const closeBtn = cardEl.querySelector('.preview-close-btn');
                if (closeBtn) {
                    closeBtn.setAttribute('onclick', `closeSinglePreview(${index})`);
                }
            } else {
                const mhs = window.mhsDataMap[p.nim] || {};
                const doc = docList.find(d => d.key === p.docKey) || { title: p.docKey, fileField: '' };
                const rawFilename = mhs[doc.fileField] || `${p.docKey}_${p.nim}.pdf`;
                const pdfUrl = resolveDocPdfUrl(rawFilename);
                const fullName = (mhs.nama_depan ? (mhs.nama_depan + ' ' + (mhs.nama_belakang || '')) : (mhs.nama || 'Mahasiswa ' + p.nim)).trim();

                const div = document.createElement('div');
                div.id = cardId;
                div.className = `preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 transition-all duration-300 ${panelWidthClass}`;
                div.innerHTML = `
                    <!-- Header Pratinjau Kompak -->
                    <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="preview-slot-badge w-7 h-7 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                ${totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>'}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white truncate max-w-[190px] sm:max-w-[240px]">${doc.title}</h4>
                                <p class="text-[10px] text-slate-300 font-medium truncate">${fullName} · <span class="font-mono text-slate-400">${rawFilename}</span></p>
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
                    <div class="h-[410px] sm:h-[470px] bg-slate-200 relative border-b border-slate-200 overflow-hidden cursor-default select-none">
                        <iframe id="iframePreviewBerkas_${p.nim}_${p.docKey}" src="${pdfUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 pointer-events-none" title="Pratinjau Dokumen PDF"></iframe>
                    </div>

                    <!-- Footer Pratinjau -->
                    <div class="p-2 px-3 bg-white flex items-center justify-between text-xs shrink-0 gap-2">
                        <a href="${pdfUrl}" download="${rawFilename}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs text-[11px]">
                            <i class="fa-solid fa-download text-[10px]"></i>
                            <span>Unduh File</span>
                        </a>
                        <button type="button" onclick="closeSinglePreview(${index})" class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold transition cursor-pointer text-[11px] shadow-2xs">
                            Tutup
                        </button>
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
                btn.className = 'btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs cursor-pointer ring-2 ring-orange-400 scale-105 shadow-md';
                btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i> Melihat';
            } else {
                btn.className = 'btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs cursor-pointer';
                btn.innerHTML = '<i class="fa-solid fa-folder-open text-xs"></i> Lihat Berkas';
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

