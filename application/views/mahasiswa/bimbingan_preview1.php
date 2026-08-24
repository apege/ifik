<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Bimbingan & Evaluasi Preview TA — IFIK Portal'; ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">

    <!-- Global Styling -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .orb-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <!-- Header Glass Navbar (Clean White Glass - Consistent) -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand -->
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 bg-gradient-to-tr from-orange-600 via-orange-500 to-amber-500 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d shadow-md shadow-orange-500/20">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-lg sm:text-xl text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] sm:text-xs uppercase font-bold tracking-widest text-orange-600 mt-1 block">Akademik Mahasiswa</span>
                    </div>
                </div>

                <!-- Nav Menu -->
                <nav class="hidden md:flex items-center gap-8 relative" id="mainNav">
                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link flex items-center gap-2.5 text-sm font-semibold tracking-wide">
                        <i class="bi bi-grid-1x2-fill text-base"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link flex items-center gap-2.5 text-sm font-semibold tracking-wide">
                        <i class="bi bi-file-earmark-text text-base"></i>
                        <span>Pendaftaran TA</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/bimbingan'); ?>" class="nav-link active-link flex items-center gap-2.5 text-sm font-bold tracking-wide">
                        <i class="bi bi-person-video3 text-base"></i>
                        <span>Bimbingan TA</span>
                    </a>
                </nav>

                <!-- User Quick Info -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-bold text-slate-800 leading-tight"><?= $mahasiswa['nama_depan'] ?? 'Mahasiswa'; ?></span>
                        <span class="text-xs text-slate-400 font-mono font-medium"><?= $mahasiswa['nim'] ?? 'NIM Mahasiswa'; ?></span>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-sm box-3d shadow-sm">
                        <?= strtoupper(substr($mahasiswa['nama_depan'] ?? 'M', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content (Full Width Fluid Layout) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">

        <!-- Flashdata Alert -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-rose-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Hero Card & Supervisor Info (3D Rich Orange Sunset with Campus Art) -->
        <div class="card-3d-orange hover-card-elevate rounded-3xl p-7 sm:p-9 relative overflow-hidden transition-all duration-300 w-full shadow-2xl text-white">
            <!-- Full Width Campus Building Background Illustration (Vibrant Artwork) -->
            <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden rounded-3xl">
                <img src="<?= base_url('assets/images/background.png'); ?>" alt="FIK Building Illustration" class="w-full h-full object-cover object-[85%_center] opacity-75 saturate-110 contrast-105">
                <div class="absolute inset-0 bg-gradient-to-r from-[#9a3412]/95 via-[#ea580c]/85 to-[#c2410c]/70"></div>
            </div>

            <!-- Floating 3D Orbs / Spheres Accent -->
            <div class="sph-3d w-28 h-28 -top-8 -right-8 bg-gradient-to-tr from-amber-300 to-orange-400 opacity-25 z-0"></div>
            <div class="sph-3d w-20 h-20 -bottom-6 left-1/3 bg-gradient-to-tr from-rose-300 to-amber-300 opacity-20 z-0" style="animation-duration: 8s;"></div>

            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <!-- Left Title & Info -->
                <div class="space-y-4 max-w-3xl">
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold badge-3d shadow-xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <i class="bi bi-mortarboard-fill text-amber-200 text-sm"></i> Hub Bimbingan &amp; Evaluasi Preview TA
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight drop-shadow-sm">
                        Bimbingan Tugas Akhir
                    </h1>
                    
                    <p class="text-sm sm:text-base text-orange-100/95 font-normal leading-relaxed max-w-2xl">
                        Kelola seluruh proses bimbingan dan pengunggahan berkas checkpoint evaluasi (<strong class="text-white font-bold">Preview 1, Preview 2, dan Preview 3</strong>) hingga persiapan menuju Sidang Akhir Tugas Akhir.
                    </p>
                    
                    <!-- Judul TA Singkat -->
                    <?php if(!empty($pendaftaran['judul_1'])): ?>
                        <div class="pt-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-200 block mb-1.5">Judul Tugas Akhir Utama:</span>
                            <div class="p-4 sm:p-5 bg-black/25 backdrop-blur-md rounded-2xl border border-white/20 font-bold text-white text-sm sm:text-base shadow-inner flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-white flex items-center justify-center text-lg shrink-0 box-3d shadow-sm">
                                    <i class="bi bi-bookmark-star-fill text-slate-900"></i>
                                </div>
                                <span class="leading-relaxed font-semibold"><?= htmlspecialchars($pendaftaran['judul_1']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Info Dosen Pembimbing & Penguji Card (Seamless Frosted Glass) -->
                <div class="w-full xl:w-[460px] bg-black/25 backdrop-blur-xl rounded-3xl p-6 sm:p-7 border border-white/20 shadow-2xl space-y-4 shrink-0 text-white hover-card-elevate transition-all duration-300">
                    <div class="flex items-center justify-between border-b border-white/15 pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm font-bold box-3d border border-white/20">
                                <i class="bi bi-people-fill text-amber-300"></i>
                            </div>
                            <span class="text-sm font-bold text-white uppercase tracking-wider">
                                Tim Pembimbing &amp; Penguji
                            </span>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/25 text-emerald-300 border border-emerald-400/40 shadow-2xs backdrop-blur-md">
                            <i class="bi bi-patch-check-fill mr-1"></i> Aktif
                        </span>
                    </div>

                    <!-- Pembimbing 1 -->
                    <div class="flex items-start gap-3.5 p-3 rounded-2xl bg-white/10 hover:bg-white/15 transition-colors border border-white/10">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-extrabold text-base shrink-0 box-3d shadow-sm">
                            P1
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-amber-200 uppercase tracking-wider block">Pembimbing Utama (Penilai P1 &amp; P3)</span>
                            <h4 class="font-bold text-sm sm:text-base text-white truncate mt-0.5"><?= htmlspecialchars($pembimbing_1); ?></h4>
                        </div>
                    </div>

                    <!-- Pembimbing 2 -->
                    <div class="flex items-start gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-colors border border-white/10">
                        <div class="w-11 h-11 rounded-2xl bg-white/20 text-white flex items-center justify-center font-bold text-base shrink-0 box-3d border border-white/10">
                            P2
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-orange-200/80 uppercase tracking-wider block">Pembimbing Pendamping (Bimbingan Teknis)</span>
                            <h4 class="font-bold text-sm sm:text-base text-white/95 truncate mt-0.5"><?= htmlspecialchars($pembimbing_2); ?></h4>
                        </div>
                    </div>

                    <!-- Dosen Penguji (Preview 2) -->
                    <div class="flex items-start gap-3.5 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-colors border border-white/10">
                        <div class="w-11 h-11 rounded-2xl bg-purple-500/40 text-purple-200 border border-purple-400/30 flex items-center justify-center font-bold text-base shrink-0 box-3d">
                            U1
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-purple-200 uppercase tracking-wider block">Dosen Penguji (Penilai Preview 2)</span>
                            <h4 class="font-bold text-sm sm:text-base text-white/95 truncate mt-0.5"><?= htmlspecialchars($penguji_ta); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestone Stepper Workflow / Tab Switcher (Full Width) -->
        <?php
            $is_p1_app = ($latest_p1 && $latest_p1['status_pembimbing'] === 'Approved');
            $is_p2_app = ($latest_p2 && $latest_p2['status_pembimbing'] === 'Approved');
            $is_p3_app = ($latest_p3 && $latest_p3['status_pembimbing'] === 'Approved');
        ?>
        <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-7 w-full shadow-lg shadow-orange-500/5">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-orange-100 pb-5">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">PILIH TAHAPAN EVALUASI</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                        <i class="bi bi-diagram-3-fill text-orange-500 text-xl"></i> Milestone Bimbingan &amp; Evaluasi TA
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs">
                        <i class="bi bi-cursor-fill text-orange-500 mr-1.5"></i> Klik kartu tahapan untuk berganti tab
                    </span>
                </div>
            </div>

            <!-- 4 Milestone Tab Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Tab 1: Preview 1 -->
                <div onclick="switchPreviewTab('preview1')" id="tabBtnPreview1" class="tab-card p-6 rounded-3xl border-2 border-orange-500 bg-gradient-to-b from-orange-50/80 to-white shadow-md shadow-orange-500/15 space-y-3.5 relative overflow-hidden hover-card-elevate cursor-pointer transition-all duration-300 ring-4 ring-orange-400/20">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-orange-700">Tahap 01</span>
                        <div class="w-10 h-10 rounded-2xl <?= $is_p1_app ? 'bg-emerald-500 shadow-md shadow-emerald-500/40' : 'bg-orange-500 shadow-md shadow-orange-500/40'; ?> text-white flex items-center justify-center font-bold text-lg box-3d">
                            <i class="bi <?= $is_p1_app ? 'bi-check-lg' : 'bi-file-earmark-text'; ?>"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Preview 1</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Proposal &amp; Bab 1–3 (Pembimbing 1)</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p1_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($upload_count_p1 > 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-orange-100 text-orange-800 border border-orange-300'); ?>">
                            <span class="w-2 h-2 rounded-full <?= $is_p1_app ? 'bg-emerald-500' : 'bg-orange-500'; ?>"></span>
                            <?= $is_p1_app ? 'Disetujui Pembimbing 1' : ($upload_count_p1 > 0 ? 'Menunggu Review' : 'Aktif / Sedang Berlangsung'); ?>
                        </span>
                    </div>
                </div>

                <!-- Tab 2: Preview 2 -->
                <div onclick="switchPreviewTab('preview2')" id="tabBtnPreview2" class="tab-card p-6 rounded-3xl border-2 <?= $is_p1_app ? 'border-amber-300 bg-amber-50/50' : 'border-slate-200 bg-slate-50/80 opacity-80'; ?> space-y-3.5 relative overflow-hidden hover-card-elevate cursor-pointer transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-slate-500">Tahap 02</span>
                        <div class="w-10 h-10 rounded-2xl <?= $is_p2_app ? 'bg-emerald-500 text-white' : ($is_p1_app ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-500'); ?> flex items-center justify-center font-bold text-lg box-3d">
                            <i class="bi <?= $is_p2_app ? 'bi-check-lg' : ($is_p1_app ? 'bi-hammer' : 'bi-lock-fill text-sm'); ?>"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Preview 2</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Progress Karya 50% (Dosen Penguji)</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p2_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($is_p1_app ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-500 border border-slate-200'); ?>">
                            <i class="bi <?= $is_p2_app ? 'bi-check-circle-fill' : ($is_p1_app ? 'bi-unlock' : 'bi-clock'); ?>"></i>
                            <?= $is_p2_app ? 'Disetujui Penguji' : ($is_p1_app ? 'Terbuka / Siap Upload' : 'Terkunci (Syarat P1)'); ?>
                        </span>
                    </div>
                </div>

                <!-- Tab 3: Preview 3 -->
                <div onclick="switchPreviewTab('preview3')" id="tabBtnPreview3" class="tab-card p-6 rounded-3xl border-2 <?= $is_p2_app ? 'border-indigo-300 bg-indigo-50/50' : 'border-slate-200 bg-slate-50/80 opacity-80'; ?> space-y-3.5 relative overflow-hidden hover-card-elevate cursor-pointer transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-slate-500">Tahap 03</span>
                        <div class="w-10 h-10 rounded-2xl <?= $is_p3_app ? 'bg-emerald-500 text-white' : ($is_p2_app ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-500'); ?> flex items-center justify-center font-bold text-lg box-3d">
                            <i class="bi <?= $is_p3_app ? 'bi-check-lg' : ($is_p2_app ? 'bi-journal-check' : 'bi-lock-fill text-sm'); ?>"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Preview 3</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Pra-Sidang Naskah 100% (Pembimbing 1)</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p3_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($is_p2_app ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-500 border border-slate-200'); ?>">
                            <i class="bi <?= $is_p3_app ? 'bi-check-circle-fill' : ($is_p2_app ? 'bi-unlock' : 'bi-clock'); ?>"></i>
                            <?= $is_p3_app ? 'Rekomendasi Sidang' : ($is_p2_app ? 'Terbuka' : 'Terkunci (Syarat P2)'); ?>
                        </span>
                    </div>
                </div>

                <!-- Tab 4: Sidang Akhir -->
                <div onclick="switchPreviewTab('sidang')" id="tabBtnSidang" class="tab-card p-6 rounded-3xl border-2 <?= $is_p3_app ? 'border-emerald-400 bg-emerald-50/50' : 'border-slate-200 bg-slate-50/80 opacity-80'; ?> space-y-3.5 relative overflow-hidden hover-card-elevate cursor-pointer transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold tracking-wider uppercase text-slate-500">Tahap 04</span>
                        <div class="w-10 h-10 rounded-2xl <?= $is_p3_app ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'; ?> flex items-center justify-center font-bold text-lg box-3d">
                            <i class="bi <?= $is_p3_app ? 'bi-mortarboard-fill' : 'bi-lock-fill text-sm'; ?>"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Sidang Tugas Akhir</h4>
                        <p class="text-xs text-slate-500 font-medium mt-1 leading-snug">Pendaftaran &amp; Penjadwalan Sidang</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $is_p3_app ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-500 border border-slate-200'; ?>">
                            <i class="bi <?= $is_p3_app ? 'bi-check-circle-fill' : 'bi-clock'; ?>"></i>
                            <?= $is_p3_app ? 'Siap Daftar Sidang' : 'Terkunci (Syarat P3)'; ?>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 1 ================= -->
        <div id="panelPreview1" class="tab-panel space-y-7">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 w-full">
                <!-- Kolom Kiri: Form Upload Berkas Preview 1 (7 Kolom) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-orange-500/5">
                        <div class="border-b border-orange-100 pb-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">FORMULIR UNGGAH PREVIEW 1</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-cloud-arrow-up-fill text-orange-500 text-2xl"></i> Upload Draft Proposal &amp; Bab 1–3
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                                Berkas akan ditinjau oleh <strong class="text-slate-900 font-semibold">Pembimbing 1 (<?= htmlspecialchars($pembimbing_1); ?>)</strong>. Format: <strong class="text-slate-900 font-semibold">PDF, DOCX, ZIP</strong> (Maks. 10MB).
                            </p>
                        </div>

                        <!-- Form Upload -->
                        <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview1', 'class' => 'space-y-6']); ?>
                            <input type="hidden" name="tahap_preview" value="Preview 1">
                            
                            <!-- Drag & Drop Zone -->
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Berkas Draft Laporan / Proposal TA <span class="text-rose-500">*</span>
                                </label>
                                
                                <div class="drop-zone relative border-2 border-dashed border-orange-300 hover:border-orange-500 bg-orange-50/30 hover:bg-orange-50/60 rounded-3xl p-8 sm:p-10 text-center transition-all cursor-pointer group" id="dropZoneP1">
                                    <input type="file" name="file_draft" id="fileDraftP1" accept=".pdf,.docx,.zip" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    
                                    <div class="space-y-3.5 pointer-events-none">
                                        <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-3xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white group-hover:scale-105 flex items-center justify-center text-3xl mx-auto transition-transform box-3d shadow-md shadow-orange-500/20">
                                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm sm:text-base font-bold text-slate-900">
                                                Klik untuk memilih file atau seret &amp; lepas ke sini
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium mt-1">
                                                PDF, DOCX, atau ZIP (Maksimal ukuran: 10MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="fileBadgeP1" class="hidden mt-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 box-3d">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                        </div>
                                        <span id="fileNameP1" class="truncate font-mono">draft.pdf</span>
                                    </div>
                                    <span id="fileSizeP1" class="text-xs text-emerald-800 font-bold shrink-0 ml-3 bg-white px-3 py-1 rounded-xl border border-emerald-200">2.4 MB</span>
                                </div>
                            </div>

                            <!-- Catatan Progres Mahasiswa -->
                            <div>
                                <label for="catatanP1" class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Catatan Progres Bimbingan <span class="text-slate-400 font-normal normal-case">(Opsional)</span>
                                </label>
                                <textarea name="catatan_mahasiswa" id="catatanP1" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-400/20 focus:border-orange-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Contoh: Mengunggah draft revisi Bab 1 s/d Bab 3 sesuai arahan Pembimbing 1..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-sm sm:text-base shadow-lg shadow-orange-500/25 transition flex items-center justify-center gap-3 cursor-pointer hover:scale-[1.01] active:scale-[0.99] box-3d">
                                    <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    <span>Kirim Berkas Draft Preview 1</span>
                                </button>
                            </div>

                        <?= form_close(); ?>
                    </div>
                </div>

                <!-- Kolom Kanan: Gatekeeper Validasi Syarat Lanjut ke Preview 2 (5 Kolom) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-orange-500/5">
                        <div class="border-b border-orange-100 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">GATEKEEPER MILESTONE</span>
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-shield-lock-fill text-orange-500 text-xl"></i> Syarat Melangkah ke Preview 2
                            </h3>
                        </div>

                        <!-- Checklist Syarat -->
                        <div class="space-y-4">
                            <!-- Syarat 1: Minimal 1x Upload -->
                            <?php $req1_passed = ($upload_count_p1 > 0); ?>
                            <div class="p-4 rounded-2xl border <?= $req1_passed ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-slate-50/60'; ?> flex items-start gap-3.5 transition-colors">
                                <div class="w-9 h-9 rounded-xl <?= $req1_passed ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center text-base font-bold shrink-0 mt-0.5 box-3d">
                                    <i class="bi <?= $req1_passed ? 'bi-check-lg' : 'bi-dash'; ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-slate-900">Minimal 1 Kali Upload Berkas</h4>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        <?= $req1_passed ? "Sudah mengunggah {$upload_count_p1} kali berkas draft." : "Wajib mengunggah minimal 1 kali berkas draft."; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Syarat 2: ACC Pembimbing 1 -->
                            <div class="p-4 rounded-2xl border <?= $is_p1_app ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-slate-50/60'; ?> flex items-start gap-3.5 transition-colors">
                                <div class="w-9 h-9 rounded-xl <?= $is_p1_app ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center text-base font-bold shrink-0 mt-0.5 box-3d">
                                    <i class="bi <?= $is_p1_app ? 'bi-check-lg' : 'bi-dash'; ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-slate-900">Persetujuan Pembimbing 1</h4>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        <?= $is_p1_app ? "Draft disetujui untuk melangkah ke Preview 2." : "Menunggu peninjauan &amp; persetujuan Pembimbing 1."; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Preview 2 -->
                        <div class="pt-2">
                            <?php if($req1_passed && $is_p1_app): ?>
                                <button type="button" onclick="switchPreviewTab('preview2')" class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2.5 box-3d hover:scale-105 active:scale-95 cursor-pointer">
                                    <i class="bi bi-arrow-right-circle-fill text-base"></i>
                                    <span>Buka Tahap Preview 2</span>
                                </button>
                            <?php else: ?>
                                <div class="w-full py-4 px-6 rounded-2xl bg-slate-100 text-slate-400 font-bold text-xs sm:text-sm border border-slate-200 flex items-center justify-center gap-2.5 cursor-not-allowed opacity-80" title="Selesaikan syarat di atas untuk membuka tahap Preview 2">
                                    <i class="bi bi-lock-fill text-sm"></i>
                                    <span>Tahap Preview 2 Masih Terkunci</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Riwayat Upload Berkas Preview 1 -->
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-orange-500/5">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-orange-100 pb-5">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">LOG AKTIVITAS PREVIEW 1</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-orange-500 text-xl"></i> Riwayat Pengajuan Berkas Preview 1
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Daftar draft yang diajukan ke Pembimbing 1 beserta catatan review.</p>
                    </div>
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                        Total: <?= count($riwayat_preview1); ?> Dokumen
                    </span>
                </div>

                <?php if(!empty($riwayat_preview1)): ?>
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-xs tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-6 text-center w-14">#</th>
                                    <th class="py-4 px-6">Nama Berkas</th>
                                    <th class="py-4 px-6">Catatan Mahasiswa</th>
                                    <th class="py-4 px-6">Waktu Upload</th>
                                    <th class="py-4 px-6 text-center">Status Pembimbing</th>
                                    <th class="py-4 px-6">Catatan / Feedback</th>
                                    <th class="py-4 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-xs sm:text-sm">
                                <?php $no = 1; foreach($riwayat_preview1 as $row): ?>
                                    <?php
                                        $st = $row['status_pembimbing'] ?? 'Pending';
                                        $badge_cls = ($st === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (($st === 'Revision') ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-amber-100 text-amber-900 border-amber-300');
                                        $badge_icon = ($st === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : (($st === 'Revision') ? 'bi-x-circle-fill text-rose-600' : 'bi-clock-fill text-amber-600');
                                        $badge_text = ($st === 'Approved') ? 'Disetujui (ACC)' : (($st === 'Revision') ? 'Perlu Revisi' : 'Menunggu Review');
                                    ?>
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="py-4.5 px-6 text-center font-bold text-slate-700"><?= $no++; ?></td>
                                        <td class="py-4.5 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg shrink-0 box-3d">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 block truncate max-w-xs text-sm"><?= htmlspecialchars($row['file_draft']); ?></span>
                                                    <span class="text-[11px] text-slate-400 font-mono uppercase font-medium">Draft Preview 1</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4.5 px-6 text-slate-600 max-w-xs font-normal">
                                            <p class="line-clamp-2 italic"><?= !empty($row['catatan_mahasiswa']) ? htmlspecialchars($row['catatan_mahasiswa']) : '-'; ?></p>
                                        </td>
                                        <td class="py-4.5 px-6 whitespace-nowrap text-slate-600 font-medium">
                                            <?= date('d M Y, H:i', strtotime($row['created_at'])); ?> WIB
                                        </td>
                                        <td class="py-4.5 px-6 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border <?= $badge_cls; ?> shadow-2xs">
                                                <i class="bi <?= $badge_icon; ?>"></i> <?= $badge_text; ?>
                                            </span>
                                        </td>
                                        <td class="py-4.5 px-6 text-slate-700 max-w-xs">
                                            <?php if(!empty($row['catatan_pembimbing'])): ?>
                                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs italic font-medium text-slate-800">
                                                    "<?= htmlspecialchars($row['catatan_pembimbing']); ?>"
                                                </div>
                                            <?php else: ?>
                                                <span class="text-slate-400 italic font-normal">Belum ada catatan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4.5 px-6 text-right whitespace-nowrap">
                                            <a href="<?= base_url('uploads/preview_ta/' . $row['file_draft']); ?>" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-800 font-bold text-xs transition shadow-2xs hover:scale-105 active:scale-95">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-12 px-4 text-center rounded-3xl bg-slate-50/60 border border-dashed border-slate-200 space-y-2.5">
                        <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl mx-auto box-3d shadow-xs">
                            <i class="bi bi-inbox-fill"></i>
                        </div>
                        <h4 class="font-bold text-base text-slate-800">Belum Ada Riwayat Upload Preview 1</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto font-normal">Silakan gunakan formulir di atas untuk mengunggah berkas draft Preview 1 Anda.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 2 ================= -->
        <div id="panelPreview2" class="tab-panel hidden space-y-7">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 w-full">
                <!-- Form Upload Preview 2 -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 shadow-md shadow-amber-500/5">
                        <div class="border-b border-amber-100 pb-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">FORMULIR UNGGAH PREVIEW 2</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-hammer text-amber-500 text-xl"></i> Upload Progress Produk &amp; Prototype 50%
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                                Berkas akan dievaluasi oleh <strong class="text-slate-900 font-semibold">Dosen Penguji (<?= htmlspecialchars($penguji_ta); ?>)</strong>.
                            </p>
                        </div>

                        <?= form_open_multipart('mahasiswa/upload_preview', ['id' => 'formUploadPreview2', 'class' => 'space-y-6']); ?>
                            <input type="hidden" name="tahap_preview" value="Preview 2">
                            
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Berkas Progress Bab 4 &amp; Link Prototype / Demo <span class="text-rose-500">*</span>
                                </label>
                                
                                <div class="drop-zone relative border-2 border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/30 hover:bg-amber-50/60 rounded-3xl p-8 sm:p-10 text-center transition-all cursor-pointer group">
                                    <input type="file" name="file_draft" id="fileDraftP2" accept=".pdf,.docx,.zip" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="space-y-3.5 pointer-events-none">
                                        <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white group-hover:scale-105 flex items-center justify-center text-3xl mx-auto transition-transform box-3d shadow-md shadow-amber-500/20">
                                            <i class="bi bi-file-earmark-zip-fill"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm sm:text-base font-bold text-slate-900">
                                                Pilih file dokumen / laporan progress Preview 2
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium mt-1">PDF, DOCX, atau ZIP (Maksimal 10MB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                                    Catatan Progress Karya / Link Demo Video &amp; Figma
                                </label>
                                <textarea name="catatan_mahasiswa" rows="3" class="w-full p-4 text-sm rounded-2xl border border-slate-200 focus:ring-4 focus:ring-amber-400/20 focus:border-amber-500 outline-none resize-none text-slate-900 placeholder:text-slate-400 transition bg-white font-medium" placeholder="Tuliskan tautan prototype Figma, link repositori GitHub, atau ringkasan progres Bab 4..."></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full py-4 px-8 rounded-2xl bg-gradient-to-r from-amber-600 via-amber-500 to-orange-500 hover:from-amber-700 hover:to-orange-600 text-white font-bold text-sm sm:text-base shadow-lg shadow-amber-500/25 transition flex items-center justify-center gap-3 cursor-pointer box-3d">
                                    <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    <span>Kirim Berkas Preview 2</span>
                                </button>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>

                <!-- Info Dosen Penguji -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-5 shadow-md shadow-amber-500/5">
                        <div class="border-b border-amber-100 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">PENGUJI EVALUASI</span>
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-person-check-fill text-amber-600 text-xl"></i> Dosen Penguji Preview 2
                            </h3>
                        </div>
                        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2">
                            <h4 class="font-bold text-base text-slate-900"><?= htmlspecialchars($penguji_ta); ?></h4>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                Dosen penguji bertugas mengevaluasi kelayakan teknis rancangan produk dan metodologi sebelum Anda diperbolehkan menyusun draft laporan lengkap di Preview 3.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Preview 2 -->
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-amber-500/5">
                <div class="flex items-center justify-between border-b border-amber-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block mb-1">LOG AKTIVITAS PREVIEW 2</span>
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                            <i class="bi bi-clock-history text-amber-600 text-xl"></i> Riwayat Pengajuan Berkas Preview 2
                        </h3>
                    </div>
                    <span class="text-xs font-bold px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 border border-slate-200">
                        Total: <?= count($riwayat_preview2); ?> Dokumen
                    </span>
                </div>
                <?php if(!empty($riwayat_preview2)): ?>
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead class="bg-slate-50 text-slate-700 font-bold uppercase py-3.5 border-b border-slate-200">
                                <tr>
                                    <th class="py-3.5 px-6">Nama Berkas</th>
                                    <th class="py-3.5 px-6">Catatan Mahasiswa</th>
                                    <th class="py-3.5 px-6">Waktu</th>
                                    <th class="py-3.5 px-6 text-center">Status</th>
                                    <th class="py-3.5 px-6">Catatan Penguji</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <?php foreach($riwayat_preview2 as $r): ?>
                                    <tr>
                                        <td class="py-4.5 px-6 font-bold text-slate-900"><?= htmlspecialchars($r['file_draft']); ?></td>
                                        <td class="py-4.5 px-6 text-slate-600 font-normal"><?= htmlspecialchars($r['catatan_mahasiswa'] ?? '-'); ?></td>
                                        <td class="py-4.5 px-6"><?= date('d M Y H:i', strtotime($r['created_at'])); ?></td>
                                        <td class="py-4.5 px-6 text-center font-bold"><?= $r['status_pembimbing']; ?></td>
                                        <td class="py-4.5 px-6 italic text-slate-700"><?= htmlspecialchars($r['catatan_pembimbing'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-10 px-4 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-xs text-slate-500 font-medium">
                        Belum ada dokumen yang diunggah untuk Preview 2.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: PREVIEW 3 ================= -->
        <div id="panelPreview3" class="tab-panel hidden space-y-7">
            <div class="card-3d-warm rounded-3xl p-7 sm:p-9 space-y-6 w-full shadow-md shadow-indigo-500/5">
                <div class="border-b border-indigo-100 pb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-1">FORMULIR PRA-SIDANG (PREVIEW 3)</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                        <i class="bi bi-journal-check text-indigo-600 text-xl"></i> Upload Naskah Lengkap TA (Bab 1 – 5) &amp; Karya Final
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5">
                        Persetujuan Preview 3 oleh Pembimbing 1 akan membuka akses pendaftaran Sidang Tugas Akhir Anda.
                    </p>
                </div>

                <?= form_open_multipart('mahasiswa/upload_preview', ['class' => 'space-y-6 max-w-3xl']); ?>
                    <input type="hidden" name="tahap_preview" value="Preview 3">
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">
                            Dokumen Lengkap Pra-Sidang (PDF / ZIP) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="file_draft" accept=".pdf,.docx,.zip" required class="w-full p-4 border border-indigo-200 rounded-2xl bg-indigo-50/30 text-xs sm:text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-2.5">Catatan Kelayakan Pra-Sidang</label>
                        <textarea name="catatan_mahasiswa" rows="3" class="w-full p-4 border border-slate-200 rounded-2xl text-xs sm:text-sm font-medium" placeholder="Uraikan kelengkapan naskah dan karya yang siap disidangkan..."></textarea>
                    </div>
                    <button type="submit" class="py-4 px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                        <i class="bi bi-send-check-fill mr-2"></i> Submit Berkas Pra-Sidang (Preview 3)
                    </button>
                <?= form_close(); ?>
            </div>
        </div>

        <!-- ================= TAB CONTENT PANEL: SIDANG AKHIR ================= -->
        <div id="panelSidang" class="tab-panel hidden space-y-7">
            <div class="card-3d-warm rounded-3xl p-10 sm:p-14 text-center space-y-5 max-w-4xl mx-auto shadow-lg shadow-emerald-500/10">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-4xl mx-auto box-3d shadow-md shadow-emerald-500/30">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Pendaftaran Sidang Akhir Tugas Akhir</h3>
                <p class="text-sm sm:text-base text-slate-600 max-w-lg mx-auto leading-relaxed font-normal">
                    Setelah menyelesaikan seluruh tahapan Preview 1 s/d Preview 3 dan mendapatkan ACC dari Pembimbing, Anda dapat melanjutkan ke alur pendaftaran dan penjadwalan sidang.
                </p>
                <div class="pt-3">
                    <button type="button" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm sm:text-base shadow-lg shadow-emerald-500/30 transition flex items-center gap-3 mx-auto box-3d cursor-pointer hover:scale-105 active:scale-95">
                        <i class="bi bi-calendar-check-fill text-lg"></i> Buka Menu Pendaftaran Sidang
                    </button>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-6 text-center text-xs sm:text-sm text-slate-500 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script>
        // File selection preview for Preview 1
        const fileInputP1 = document.getElementById('fileDraftP1');
        const fileBadgeP1 = document.getElementById('fileBadgeP1');
        const fileNameP1 = document.getElementById('fileNameP1');
        const fileSizeP1 = document.getElementById('fileSizeP1');

        if (fileInputP1) {
            fileInputP1.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    fileNameP1.textContent = file.name;
                    fileSizeP1.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    fileBadgeP1.classList.remove('hidden');
                }
            });
        }

        // Tab Switcher Logic
        function switchPreviewTab(targetTab) {
            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(el => el.classList.add('hidden'));

            // Reset tab button borders & rings
            document.querySelectorAll('.tab-card').forEach(el => {
                el.classList.remove('ring-4', 'ring-orange-400/20', 'ring-amber-400/20', 'ring-indigo-400/20', 'ring-emerald-400/20', 'border-orange-500', 'border-amber-300', 'border-indigo-300', 'border-emerald-400');
            });

            if (targetTab === 'preview1') {
                document.getElementById('panelPreview1').classList.remove('hidden');
                document.getElementById('tabBtnPreview1').classList.add('border-orange-500', 'ring-4', 'ring-orange-400/20');
            } else if (targetTab === 'preview2') {
                document.getElementById('panelPreview2').classList.remove('hidden');
                document.getElementById('tabBtnPreview2').classList.add('border-amber-300', 'ring-4', 'ring-amber-400/20');
            } else if (targetTab === 'preview3') {
                document.getElementById('panelPreview3').classList.remove('hidden');
                document.getElementById('tabBtnPreview3').classList.add('border-indigo-300', 'ring-4', 'ring-indigo-400/20');
            } else if (targetTab === 'sidang') {
                document.getElementById('panelSidang').classList.remove('hidden');
                document.getElementById('tabBtnSidang').classList.add('border-emerald-400', 'ring-4', 'ring-emerald-400/20');
            }
        }
    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
