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
                                        <?= !empty($mhs['judul_1']) ? character_limiter($mhs['judul_1'], 50) : '<span class="text-slate-400 italic font-normal">Belum Mendaftar</span>'; ?>
                                    </td>

                                    <!-- Status 4 Berkas -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs">
                                            <span class="<?= $ksm_st === 'Approved' ? 'text-emerald-600 font-extrabold' : ($ksm_st === 'Rejected' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">KSM</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="<?= $trs_st === 'Approved' ? 'text-emerald-600 font-extrabold' : ($trs_st === 'Rejected' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">TRS</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="<?= $prn_st === 'Approved' ? 'text-emerald-600 font-extrabold' : ($prn_st === 'Rejected' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">SRT</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="<?= $lab_st === 'Approved' ? 'text-emerald-600 font-extrabold' : ($lab_st === 'Rejected' ? 'text-rose-600 font-extrabold' : 'text-slate-400'); ?>">LAB</span>
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
                                        <a href="<?= site_url('dosenwali/detail_mahasiswa/' . $mhs['nim']); ?>" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                            <i class="bi bi-search text-xs"></i> Detail & Approval
                                        </a>
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
    <div id="pdfModalDW" style="display: none;" onclick="if(event.target === this) closePdfModalDW()" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-2xl max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200" onclick="event.stopPropagation()">
            <div class="p-3.5 px-5 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white flex items-center gap-2" id="pdfModalTitleDW">Pratinjau Dokumen PDF</h3>
                        <p class="text-[10px] text-slate-400" id="pdfModalSubtitleDW">Memuat tampilan dokumen...</p>
                    </div>
                </div>
                <button type="button" onclick="closePdfModalDW()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 relative overflow-hidden">
                <iframe id="pdfFrameDW" src="about:blank" class="w-full h-full border-none"></iframe>
            </div>
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
                    tableBody.innerHTML = json.data.map(mhs => {
                        const st = mhs.status_approval_wali || 'Pending';
                        const badgeStyle = (st === 'Approved') 
                            ? 'bg-emerald-100 text-emerald-700 border-emerald-300' 
                            : ((st === 'Rejected') ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-amber-100 text-amber-700 border-amber-300');
                        const judulShort = mhs.judul ? (mhs.judul.length > 50 ? mhs.judul.substring(0, 50) + '...' : mhs.judul) : '<span class="text-slate-400 italic font-normal">Belum Mendaftar</span>';
                        const isChecked = currentlyChecked.includes(mhs.nim) ? 'checked' : '';

                        const ksmClass = (mhs.status_file_ksm === 'Approved') ? 'text-emerald-600 font-extrabold' : ((mhs.status_file_ksm === 'Rejected') ? 'text-rose-600 font-extrabold' : 'text-slate-400');
                        const trsClass = (mhs.status_file_transkrip === 'Approved') ? 'text-emerald-600 font-extrabold' : ((mhs.status_file_transkrip === 'Rejected') ? 'text-rose-600 font-extrabold' : 'text-slate-400');
                        const srtClass = (mhs.status_file_pernyataan === 'Approved') ? 'text-emerald-600 font-extrabold' : ((mhs.status_file_pernyataan === 'Rejected') ? 'text-rose-600 font-extrabold' : 'text-slate-400');
                        const labClass = (mhs.status_file_bebas_lab === 'Approved') ? 'text-emerald-600 font-extrabold' : ((mhs.status_file_bebas_lab === 'Rejected') ? 'text-rose-600 font-extrabold' : 'text-slate-400');

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
                                    <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-200 text-[9px] font-mono shadow-2xs">
                                        <span class="${ksmClass}">KSM</span><span class="text-slate-300">·</span>
                                        <span class="${trsClass}">TRS</span><span class="text-slate-300">·</span>
                                        <span class="${srtClass}">SRT</span><span class="text-slate-300">·</span>
                                        <span class="${labClass}">LAB</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 font-semibold text-[11px] rounded-full border shadow-xs inline-block ${badgeStyle}">${st}</span>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block">${mhs.current_stage || 'Draft'}</span>
                                </td>
                                <td class="py-4 px-4 pr-6 text-right whitespace-nowrap">
                                    <a href="${mhs.detail_url}" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                        <i class="bi bi-search text-xs"></i> Detail & Approval
                                    </a>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                allRows = Array.from(document.querySelectorAll('.mhs-row'));
                applyAll();
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

            // Generate 4 live embedded document cards with bottom checkboxes & conditional comment box
            let docsHtml = '';
            Object.keys(docNames).forEach(key => {
                const info = docNames[key];
                const fileObj = (st.files && st.files[key]) ? st.files[key] : { name: 'Belum diunggah', url: '', status: 'Pending', note: '' };
                const isDocApprove = (fileObj.status === 'Approved');
                const isDocReject  = (fileObj.status === 'Rejected');

                let badgeHtml = '<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200"><i class="fa-solid fa-clock text-[9px] mr-1 text-slate-400"></i>Belum Ditinjau</span>';
                if (isDocApprove) {
                    badgeHtml = '<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check text-[9px] mr-1 text-emerald-600"></i>Valid</span>';
                } else if (isDocReject) {
                    badgeHtml = '<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-xmark text-[9px] mr-1 text-rose-600"></i>Kurang/Revisi</span>';
                }

                docsHtml += `
                    <div class="bg-white rounded-2xl p-4 sm:p-5 border ${isDocReject ? 'border-rose-300 bg-rose-50/10' : (isDocApprove ? 'border-emerald-200' : 'border-slate-200')} shadow-xs flex flex-col justify-between space-y-4">
                        <div>
                            <!-- Header Doc with Status in Top Right -->
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 font-bold text-xs flex items-center justify-center shrink-0">
                                        <i class="fa-solid ${info.icon}"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm">${info.title}</span>
                                </div>
                                ${badgeHtml}
                            </div>
                            <p class="text-[11px] font-mono text-slate-400 truncate mb-2" title="${fileObj.name}">${fileObj.name}</p>

                            <!-- Live Embedded PDF View Frame -->
                            <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100 mb-2">
                                <div class="p-2 px-3 bg-slate-800 text-white flex items-center justify-between text-[11px]">
                                    <span class="font-mono text-slate-300 truncate max-w-[200px]"><i class="fa-solid fa-file-pdf text-rose-400 mr-1.5"></i> ${fileObj.name}</span>
                                    <button type="button" onclick="previewDocPdfDW('${fileObj.url}', '${info.title} - ${st.nama}')" class="text-orange-300 hover:text-white font-bold flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-expand text-[10px]"></i> Layar Penuh
                                    </button>
                                </div>
                                <iframe src="${fileObj.url}#view=FitH&zoom=100&toolbar=1" class="w-full h-[450px] border-none bg-slate-100" loading="lazy"></iframe>
                            </div>
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
        const modal = document.getElementById('pdfModalDW');
        const frame = document.getElementById('pdfFrameDW');
        const titleEl = document.getElementById('pdfModalTitleDW');
        const subtitleEl = document.getElementById('pdfModalSubtitleDW');

        if (!modal || !frame) return;

        titleEl.textContent = title || 'Pratinjau Dokumen Persyaratan';
        subtitleEl.textContent = 'Memuat tampilan berkas PDF...';
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

    // Keyboard ESC Shortcut untuk menutup modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
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

