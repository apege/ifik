<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- PHP Progress Calculation -->
    <?php
        $w_status = $pendaftaran['status_approval_wali'] ?? 'Pending';
        $a_status = $pendaftaran['status_approval_admin'] ?? 'Pending';
        $k_status = $pendaftaran['status_approval_koor'] ?? 'Pending';
        $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';

        $approved_count = 0;
        if ($w_status === 'Approved') $approved_count++;
        if ($a_status === 'Approved') $approved_count++;
        if ($k_status === 'Approved') $approved_count++;
        if ($kk_status === 'Approved') $approved_count++;

        $progress_pct = round(($approved_count / 4) * 100);

        // Circular Progress Math (radius = 32, circumference = 2 * pi * 32 ≈ 201)
        $circumference = 201;
        $dashoffset = $circumference - ($circumference * $progress_pct / 100);
    ?>

    <!-- Header Glass Navbar (Clean White Glass) -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-16 sm:h-18">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-xl font-bold text-lg flex items-center justify-center box-3d">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider text-orange-500 mt-0.5 block">Akademik Mahasiswa</span>
                    </div>
                </div>

                <!-- Nav Menu -->
                <nav class="hidden md:flex items-center gap-7 relative" id="mainNav">
                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link active-link flex items-center gap-2 tracking-wide">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link flex items-center gap-2 tracking-wide">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pendaftaran TA</span>
                    </a>
                </nav>

                <!-- User Quick Info -->
                <div class="flex items-center gap-2.5">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-semibold text-slate-800 leading-tight"><?= $mahasiswa['nama_depan'] ?? 'Mahasiswa'; ?></span>
                        <span class="text-[9px] text-slate-400 font-medium"><?= $mahasiswa['nim'] ?? 'NIM Mahasiswa'; ?></span>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-xs box-3d">
                        <?= strtoupper(substr($mahasiswa['nama_depan'] ?? 'M', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content (Edge to Edge Full Width) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 flex-grow space-y-6">

        <?php if($this->session->flashdata('success')): ?>
            <!-- Floating Side Toast Alert (Right Corner) -->
            <div id="sideToastAlert" class="fixed top-20 right-6 z-[9999] max-w-sm w-full bg-slate-900/95 text-white p-4 rounded-2xl shadow-2xl border border-emerald-500/50 backdrop-blur-md flex items-start gap-3 transition-all duration-300">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-base font-bold shrink-0 shadow-md box-3d">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="flex-grow min-w-0">
                    <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Pemberitahuan</h4>
                    <p class="text-xs text-slate-200 font-medium leading-snug mt-0.5"><?= $this->session->flashdata('success'); ?></p>
                </div>
                <button type="button" onclick="document.getElementById('sideToastAlert').remove()" class="text-slate-400 hover:text-white font-bold text-xs p-1 transition cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <script>
            setTimeout(function() {
                const toast = document.getElementById('sideToastAlert');
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(30px)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 4000);
            </script>
        <?php endif; ?>        <!-- Hero Welcome & Progress Radial Card (3D Rich Orange Bento Layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Hero Panel (3 Cols) - Rich Orange 3D Card with Clear Campus Building Background -->
            <div class="lg:col-span-3 card-3d-orange rounded-2xl p-7 sm:p-8 relative overflow-hidden flex flex-col justify-between text-white">
                <!-- Full Width Campus Building Background Illustration (Vibrant Full-Color Artwork) -->
                <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden rounded-2xl">
                    <img src="<?= base_url('assets/images/background.png'); ?>" alt="FIK Building Illustration" class="w-full h-full object-cover object-[85%_center] opacity-85 saturate-110 contrast-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#c2410c] via-[#ea580c]/75 to-transparent"></div>
                </div>

                <!-- Floating 3D Orbs / Spheres Accent -->
                <div class="sph-3d w-24 h-24 -top-6 -right-6 bg-gradient-to-tr from-amber-300 to-orange-400 opacity-20 z-0" style="animation-duration: 7s;"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-xs font-bold text-white badge-3d">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse-glow"></span>
                            Portal Tugas Akhir IFIK
                        </div>
                        <span class="text-xs text-amber-100 font-semibold hidden sm:inline">TA 2025/2026</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mb-2.5">
                        Selamat Datang, <?= $mahasiswa['nama_depan'] ?? 'Mahasiswa'; ?>! 👋
                    </h1>
                    <p class="text-amber-100 text-sm sm:text-base leading-relaxed max-w-xl font-normal">
                        Pantau status usulan judul Tugas Akhir, kelengkapan berkas PDF, dan progres persetujuan 4 tahap secara real-time.
                    </p>
                </div>

                <div class="relative z-10 pt-5 mt-6 border-t border-white/25 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base box-3d border border-white/30">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-200 block leading-none">NIM</span>
                                <span class="text-sm font-extrabold text-white mt-0.5 block"><?= $mahasiswa['nim'] ?? '1301210001'; ?></span>
                            </div>
                        </div>
                        <div class="h-7 w-px bg-white/25 hidden sm:block"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base box-3d border border-white/30">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-200 block leading-none">PRODI</span>
                                <span class="text-sm font-extrabold text-white mt-0.5 block"><?= $mahasiswa['prodi'] ?? 'Informatika / DKV'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/30 text-xs font-bold text-white">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <span>Status: Mahasiswa Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Right Progress Circle Gauge (1 Col) - Warm Clay Card -->
            <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-7 flex flex-col justify-between items-center text-center">
                <div class="w-full text-left flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 flex items-center gap-1.5">
                        <i class="bi bi-speedometer2"></i> Overall Progres
                    </span>
                    <span class="text-xs font-bold text-orange-900 bg-orange-100 px-2.5 py-1 rounded-lg badge-3d"><?= $approved_count; ?> / 4 Tahap</span>
                </div>

                <!-- Radial Progress Ring 3D -->
                <div class="relative w-32 h-32 flex items-center justify-center my-2">
                    <svg class="w-full h-full" viewBox="0 0 80 80">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="#fed7aa" stroke-width="6" />
                        <circle cx="40" cy="40" r="32" fill="none" stroke="url(#orangeGrad3D)" stroke-width="6"
                                stroke-dasharray="<?= $circumference; ?>"
                                stroke-dashoffset="<?= $dashoffset; ?>"
                                stroke-linecap="round"
                                class="progress-ring-circle" />
                        <defs>
                            <linearGradient id="orangeGrad3D" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#ea580c" />
                                <stop offset="100%" stop-color="#10b981" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-extrabold text-slate-900 leading-none"><?= $progress_pct; ?><span class="text-sm font-bold text-orange-600">%</span></span>
                        <span class="text-xs font-bold text-orange-600 mt-1 uppercase tracking-wider">Selesai</span>
                    </div>
                </div>

                <div class="w-full pt-3.5 border-t border-orange-100 text-xs flex items-center justify-between">
                    <span class="font-medium text-slate-600">Akses Bimbingan:</span>
                    <?php if($kk_status === 'Approved'): ?>
                        <span class="font-bold text-emerald-600 text-xs flex items-center gap-1"><i class="bi bi-unlock-fill"></i> Terbuka</span>
                    <?php else: ?>
                        <span class="font-bold text-orange-600 text-xs flex items-center gap-1"><i class="bi bi-clock-history"></i> Proses Approval</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Workflow Approval Chain Tracker (3D Stepper Line) -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-7 relative">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-0.5">REAL-TIME WORKFLOW</span>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-orange-500"></i> Status Approval 4 Tahap
                    </h2>
                </div>
                <div class="flex items-center gap-2 bg-orange-100/90 border border-orange-300 px-3.5 py-1.5 rounded-full text-xs font-bold text-orange-800 badge-3d">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Live Tracking</span>
                </div>
            </div>

            <!-- Stepper Container -->
            <div class="stepper-connector relative">
                <!-- Line Progress Background (Desktop Only) -->
                <div class="stepper-line hidden lg:block">
                    <?php 
                        $stepper_pct = 0;
                        if ($w_status === 'Approved') $stepper_pct = 33;
                        if ($w_status === 'Approved' && $a_status === 'Approved') $stepper_pct = 66;
                        if ($w_status === 'Approved' && $a_status === 'Approved' && $k_status === 'Approved') $stepper_pct = 90;
                        if ($w_status === 'Approved' && $a_status === 'Approved' && $k_status === 'Approved' && $kk_status === 'Approved') $stepper_pct = 100;
                    ?>
                    <div class="stepper-line-progress" style="width: <?= $stepper_pct; ?>%;"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 relative z-10">
                    <!-- Stage 1: Dosen Wali -->
                    <?php 
                        $w_is_app = ($w_status === 'Approved');
                        $w_is_rej = ($w_status === 'Rejected');
                        $w_card_bg = $w_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($w_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-orange-300 bg-orange-100/50');
                        $w_icon_bg = $w_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($w_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-gradient-to-tr from-orange-500 to-amber-500 text-white');
                        $w_badge_cls = $w_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($w_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-orange-200 text-orange-900 border-orange-300');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $w_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 01</span>
                            <div class="w-9 h-9 rounded-xl <?= $w_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $w_is_app ? 'bi-check-lg' : ($w_is_rej ? 'bi-x-lg' : 'bi-person-check'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Dosen Wali</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Persetujuan akademik</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $w_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $w_is_app ? 'bg-emerald-500' : ($w_is_rej ? 'bg-rose-500' : 'bg-orange-600'); ?>"></span>
                                <?= $w_status; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stage 2: Admin Layanan -->
                    <?php 
                        $a_is_app = ($a_status === 'Approved');
                        $a_is_rej = ($a_status === 'Rejected');
                        $a_card_bg = $a_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($a_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $a_icon_bg = $a_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($a_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $a_badge_cls = $a_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($a_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $a_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 02</span>
                            <div class="w-9 h-9 rounded-xl <?= $a_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $a_is_app ? 'bi-check-lg' : ($a_is_rej ? 'bi-x-lg' : 'bi-shield-check'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Admin Layanan</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Verifikasi berkas PDF</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $a_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $a_is_app ? 'bg-emerald-500' : ($a_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $a_status; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stage 3: Koordinator TA -->
                    <?php 
                        $k_is_app = ($k_status === 'Approved');
                        $k_is_rej = ($k_status === 'Rejected');
                        $k_card_bg = $k_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($k_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $k_icon_bg = $k_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($k_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $k_badge_cls = $k_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($k_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $k_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 03</span>
                            <div class="w-9 h-9 rounded-xl <?= $k_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $k_is_app ? 'bi-check-lg' : ($k_is_rej ? 'bi-x-lg' : 'bi-award'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Koordinator TA</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Validasi topik & kuota</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $k_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $k_is_app ? 'bg-emerald-500' : ($k_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $k_status; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stage 4: Ketua KK -->
                    <?php 
                        $kk_is_app = ($kk_status === 'Approved');
                        $kk_is_rej = ($kk_status === 'Rejected');
                        $kk_card_bg = $kk_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($kk_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $kk_icon_bg = $kk_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($kk_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $kk_badge_cls = $kk_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($kk_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $kk_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 04</span>
                            <div class="w-9 h-9 rounded-xl <?= $kk_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $kk_is_app ? 'bi-check-lg' : ($kk_is_rej ? 'bi-x-lg' : 'bi-mortarboard'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Ketua KK</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Persetujuan akhir</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $kk_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $kk_is_app ? 'bg-emerald-500' : ($kk_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $kk_status; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bimbingan Status Bottom Bar -->
            <div class="mt-6 pt-4 border-t border-orange-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-lg font-bold shrink-0 box-3d">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Status Akses Bimbingan Akademik</h4>
                        <p class="text-xs text-slate-500 font-medium">Memerlukan persetujuan hingga Tahap 04 Ketua Kelompok Keahlian (KK)</p>
                    </div>
                </div>

                <?php if($kk_is_app): ?>
                    <span class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-bold rounded-xl flex items-center gap-2 box-3d">
                        <i class="bi bi-patch-check-fill text-base"></i> UNLOCKED — Terbuka
                    </span>
                <?php else: ?>
                    <span class="px-5 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 text-white text-xs font-bold rounded-xl flex items-center gap-2 box-3d">
                        <i class="bi bi-clock-fill text-base"></i> LOCKED — Menunggu
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Proposal Status Summary Widget (Full Width) -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-7 w-full">
            <div class="flex items-center justify-between border-b border-orange-100 pb-4 mb-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-0.5">RINGKASAN USULAN</span>
                    <h3 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-journal-bookmark-fill text-orange-500"></i> Daftar Proposal Tugas Akhir
                    </h3>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Kelola dan pantau pengajuan judul tugas akhir Anda.</p>
                </div>
                <?php if(!empty($pendaftaran['judul_1'])): ?>
                    <span class="text-xs bg-emerald-100 border border-emerald-300 text-emerald-800 font-bold px-4 py-2 rounded-full flex items-center gap-2 badge-3d">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terdaftar
                    </span>
                <?php else: ?>
                    <span class="text-xs bg-amber-100 border border-amber-300 text-amber-900 font-bold px-4 py-2 rounded-full flex items-center gap-2 badge-3d">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Belum Pengajuan
                    </span>
                <?php endif; ?>
            </div>

            <?php if(!empty($pendaftaran['judul_1'])): ?>
                <!-- Table View for Submitted Proposal (Matching Design Reference 2) -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-700 font-extrabold uppercase text-xs tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-4.5 px-6 text-center w-14">#</th>
                                <th class="py-4.5 px-6">Nama Kegiatan / Judul TA</th>
                                <th class="py-4.5 px-6">Jenis &amp; Konsentrasi</th>
                                <th class="py-4.5 px-6">Tanggal Pengajuan</th>
                                <th class="py-4.5 px-6 text-center">Status</th>
                                <th class="py-4.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr class="hover:bg-orange-50/20 transition-colors">
                                <td class="py-5 px-6 text-center font-bold text-slate-700 text-base">1</td>
                                <td class="py-5 px-6">
                                    <div class="font-bold text-slate-900 text-base mb-1 leading-snug">
                                        <?= htmlspecialchars($pendaftaran['judul_1']); ?>
                                    </div>
                                    <?php if(!empty($pendaftaran['judul_en'])): ?>
                                        <div class="text-xs text-slate-500 italic flex items-center gap-1.5 font-normal">
                                            <i class="bi bi-translate text-orange-500"></i> "<?= htmlspecialchars($pendaftaran['judul_en']); ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-5 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                        <?= htmlspecialchars($pendaftaran['jenis_ta'] ?? 'Reguler TA'); ?>
                                    </span>
                                    <div class="text-xs text-slate-500 font-semibold mt-1">
                                        <?= htmlspecialchars($pendaftaran['konsentrasi_dkv'] ?? 'Desain Grafis'); ?>
                                    </div>
                                </td>
                                <td class="py-5 px-6 text-slate-800 font-bold text-xs whitespace-nowrap">
                                    <?= !empty($pendaftaran['created_at']) ? date('d F Y', strtotime($pendaftaran['created_at'])) : date('d F Y'); ?>
                                </td>
                                <td class="py-5 px-6 text-center whitespace-nowrap">
                                    <?php if(($pendaftaran['status_approval_wali'] ?? 'Pending') === 'Approved'): ?>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Disetujui
                                        </span>
                                    <?php elseif(($pendaftaran['status_approval_wali'] ?? 'Pending') === 'Rejected'): ?>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Penolakan / Revisi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Pending Review
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-5 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-3">
                                        <button type="button" onclick="openDetailModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-bold transition cursor-pointer" title="Lihat Detail Usulan & Berkas PDF">
                                            <i class="bi bi-eye-fill text-sm"></i> Detail
                                        </button>
                                        <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition" title="Edit Pendaftaran">
                                            <i class="bi bi-pencil-square text-sm"></i> Edit
                                        </a>
                                        <button type="button" onclick="openResetModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-bold transition cursor-pointer" title="Reset / Batalkan Pengajuan">
                                            <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>   </div>
            <?php else: ?>
                <div class="py-8 px-4 text-center rounded-xl bg-slate-50/70 border border-dashed border-slate-200">
                    <p class="text-xs font-semibold text-slate-500">Belum Ada Pengajuan</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Modal Detail Usulan & Berkas TA -->
    <div id="modalDetailTA" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-orange-100 space-y-5 my-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base box-3d">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Detail Pengajuan Tugas Akhir</h3>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Dokumen &amp; Status Terdaftar</span>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg p-1">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <!-- Judul Utama -->
                <div class="p-4 rounded-xl bg-orange-50/70 border border-orange-200/80">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-orange-600 block mb-1">Judul Utama (Bahasa Indonesia)</span>
                    <h4 class="font-bold text-slate-900 text-sm leading-snug"><?= htmlspecialchars($pendaftaran['judul_1'] ?? '-'); ?></h4>
                    <?php if(!empty($pendaftaran['judul_en'])): ?>
                        <p class="text-xs text-slate-600 italic mt-2 flex items-center gap-1.5 font-normal">
                            <i class="bi bi-translate text-orange-500"></i> "<?= htmlspecialchars($pendaftaran['judul_en']); ?>"
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Alternatif Judul jika ada -->
                <?php if(!empty($pendaftaran['judul_2']) || !empty($pendaftaran['judul_3'])): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php if(!empty($pendaftaran['judul_2'])): ?>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Judul Alternatif 1</span>
                                <p class="font-semibold text-slate-800 text-xs"><?= htmlspecialchars($pendaftaran['judul_2']); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($pendaftaran['judul_3'])): ?>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Judul Alternatif 2</span>
                                <p class="font-semibold text-slate-800 text-xs"><?= htmlspecialchars($pendaftaran['judul_3']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Berkas Syarat PDF -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Berkas Persyaratan Terunggah (PDF)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div class="p-3 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500 text-base"></i>
                                <span class="font-semibold text-slate-700">KSM Terakhir</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Terunggah</span>
                        </div>
                        <div class="p-3 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500 text-base"></i>
                                <span class="font-semibold text-slate-700">Transkrip Nilai</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Terunggah</span>
                        </div>
                        <div class="p-3 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500 text-base"></i>
                                <span class="font-semibold text-slate-700">Surat Pernyataan</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Terunggah</span>
                        </div>
                        <div class="p-3 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500 text-base"></i>
                                <span class="font-semibold text-slate-700">Bebas Laboratorium</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Terunggah</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs transition cursor-pointer">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Reset TA -->
    <div id="modalResetTA" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-rose-100 space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl mx-auto box-3d">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="text-center space-y-1.5">
                <h3 class="text-lg font-bold text-slate-900">Reset Pengajuan Tugas Akhir?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tindakan ini akan <strong>menghapus data pendaftaran TA</strong> yang telah Anda kirimkan. Anda harus mengisi ulang dari formulir Langkah 1.
                </p>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeResetModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <a href="<?= site_url('mahasiswa/reset_pendaftaran'); ?>" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-trash3-fill"></i> Ya, Reset Pengajuan
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script>
    function openResetModal() {
        document.getElementById('modalResetTA').classList.remove('hidden');
    }
    function closeResetModal() {
        document.getElementById('modalResetTA').classList.add('hidden');
    }
    function openDetailModal() {
        document.getElementById('modalDetailTA').classList.remove('hidden');
    }
    function closeDetailModal() {
        document.getElementById('modalDetailTA').classList.add('hidden');
    }
    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>




