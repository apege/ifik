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

        $w_is_app = ($w_status === 'Approved');
        $w_is_rej = ($w_status === 'Rejected');

        $a_is_app = ($a_status === 'Approved');
        $a_is_rej = ($a_status === 'Rejected');

        $k_is_app = ($k_status === 'Approved');
        $k_is_rej = ($k_status === 'Rejected');

        $kk_is_app = ($kk_status === 'Approved');
        $kk_is_rej = ($kk_status === 'Rejected');

        $approved_count = 0;
        if ($w_is_app) $approved_count++;
        if ($a_is_app) $approved_count++;
        if ($k_is_app) $approved_count++;
        if ($kk_is_app) $approved_count++;

        $progress_pct = round(($approved_count / 4) * 100);

        // Circular Progress Math (radius = 32, circumference = 2 * pi * 32 ≈ 201)
        $circumference = 201;
        $dashoffset = $circumference - ($circumference * $progress_pct / 100);
    ?>

    <!-- Header Glass Navbar (Clean White Glass) -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow w-full space-y-6">

        <?php if($this->session->flashdata('success')): ?>
            <div class="bg-emerald-600 text-white p-3.5 rounded-xl shadow-md flex items-center justify-between transition-all text-xs border border-emerald-400">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white text-emerald-700 flex items-center justify-center font-bold text-xs box-3d">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <p class="font-semibold"><?= $this->session->flashdata('success'); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-emerald-200 font-bold"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Hero Welcome & Progress Radial Card (3D Rich Orange Bento Layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Left Hero Panel (2 Cols) - Rich Orange 3D Card -->
            <div class="lg:col-span-2 card-3d-orange rounded-2xl p-6 sm:p-7 relative overflow-hidden flex flex-col justify-between text-white">
                <!-- Floating 3D Orbs / Spheres Accent -->
                <div class="sph-3d w-24 h-24 -top-6 -right-6 bg-gradient-to-tr from-amber-300 to-orange-400 opacity-40" style="animation-duration: 7s;"></div>
                <div class="sph-3d w-12 h-12 bottom-4 right-40 bg-gradient-to-tr from-yellow-200 to-amber-300 opacity-30" style="animation-duration: 5s;"></div>

                <div>
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-[11px] font-semibold text-white badge-3d">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse-glow"></span>
                            Portal Tugas Akhir IFIK
                        </div>
                        <span class="text-[11px] text-amber-100 font-medium hidden sm:inline">TA 2025/2026</span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight mb-2">
                        Selamat Datang, <?= $mahasiswa['nama_depan'] ?? 'Mahasiswa'; ?>! 👋
                    </h1>
                    <p class="text-amber-100 text-xs leading-relaxed max-w-lg font-normal">
                        Pantau status usulan judul Tugas Akhir, kelengkapan berkas PDF, dan progres persetujuan 4 tahap secara real-time.
                    </p>
                </div>

                <div class="pt-4 mt-5 border-t border-white/20 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center font-semibold text-sm box-3d border border-white/30">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block leading-none">NIM</span>
                                <span class="text-xs font-semibold text-white"><?= $mahasiswa['nim'] ?? '1301210001'; ?></span>
                            </div>
                        </div>
                        <div class="h-6 w-px bg-white/20 hidden sm:block"></div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center font-semibold text-sm box-3d border border-white/30">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block leading-none">PRODI</span>
                                <span class="text-xs font-semibold text-white"><?= $mahasiswa['prodi'] ?? 'Informatika / DKV'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/25 text-xs font-medium text-white">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Status: Mahasiswa Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Right Progress Circle Gauge (1 Col) - Warm Clay Card -->
            <div class="card-3d-warm card-no-hover rounded-2xl p-6 flex flex-col justify-between items-center text-center">
                <div class="w-full text-left flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-orange-600 flex items-center gap-1">
                        <i class="bi bi-speedometer2"></i> Overall Progres
                    </span>
                    <span class="text-[10px] font-semibold text-orange-800 bg-orange-100 px-2 py-0.5 rounded-md badge-3d"><?= $approved_count; ?> / 4 Tahap</span>
                </div>

                <!-- Radial Progress Ring 3D -->
                <div class="relative w-28 h-28 flex items-center justify-center my-1">
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
                        <span class="text-2xl font-bold text-slate-900 leading-none"><?= $progress_pct; ?><span class="text-xs font-semibold text-orange-600">%</span></span>
                        <span class="text-[9px] font-medium text-orange-500 mt-0.5 uppercase tracking-wider">Selesai</span>
                    </div>
                </div>

                <div class="w-full pt-3 border-t border-orange-100 text-xs flex items-center justify-between">
                    <span class="font-normal text-slate-500 text-[11px]">Akses Bimbingan:</span>
                    <?php if($kk_status === 'Approved'): ?>
                        <span class="font-semibold text-emerald-600 text-[11px] flex items-center gap-1"><i class="bi bi-unlock-fill"></i> Terbuka</span>
                    <?php else: ?>
                        <span class="font-semibold text-orange-600 text-[11px] flex items-center gap-1"><i class="bi bi-clock-history"></i> Proses Approval</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Rejection / Revision Notes Alert Card (Only when a stage is actually Rejected or berkas_kurang is filled) -->
        <?php 
            $has_rejection = ($w_is_rej || $a_is_rej || $k_is_rej || $kk_is_rej || !empty($pendaftaran['berkas_kurang']));

            // Hitung langkah (step) target untuk langsung di-redirect
            $rev_step = 1;
            if ($a_is_rej || !empty($pendaftaran['berkas_kurang'])) {
                $raw_k = array_filter(explode(',', $pendaftaran['berkas_kurang'] ?? ''));
                if (in_array('ksm', $raw_k)) $rev_step = 3;
                else if (in_array('transkrip', $raw_k)) $rev_step = 4;
                else if (in_array('pernyataan', $raw_k)) $rev_step = 5;
                else if (in_array('bebas_lab', $raw_k)) $rev_step = 6;
                else $rev_step = 3;
            } else if ($w_is_rej || $k_is_rej || $kk_is_rej) {
                $rev_step = 2; // Step 2 (Judul Usulan)
            }
        ?>
        <?php if($has_rejection): ?>
            <div class="bg-gradient-to-r from-rose-600 via-rose-700 to-red-800 text-white rounded-2xl p-6 shadow-md border border-rose-400 relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 box-3d border border-white/30">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
                                Pengajuan TA Anda Perlu Revisi / Ditolak
                            </h3>
                            <a href="<?= site_url('mahasiswa/pendaftaran_ta?step=' . $rev_step); ?>" class="px-3.5 py-1.5 bg-white hover:bg-rose-50 text-rose-800 rounded-xl text-xs font-bold shadow-xs transition-all flex items-center gap-1.5">
                                <i class="bi bi-pencil-square"></i> Edit &amp; Perbarui Berkas (Form 6-Step)
                            </a>
                        </div>
                        <p class="text-xs text-rose-100 mb-4 font-normal">
                            Mohon perhatikan catatan revisi dari pihak berwenang di bawah ini untuk memperbarui pendaftaran Anda:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <?php if($w_is_rej): ?>
                                <div class="bg-black/20 backdrop-blur-md rounded-xl p-3.5 border border-white/20">
                                    <span class="text-[10px] uppercase font-bold text-amber-200 block mb-1">
                                        <i class="bi bi-person-check-fill"></i> Catatan Revisi Dosen Wali
                                    </span>
                                    <p class="text-white leading-relaxed font-medium">
                                        "<?= htmlspecialchars(!empty($pendaftaran['catatan_wali']) ? $pendaftaran['catatan_wali'] : 'Dosen wali meminta revisi usulan.'); ?>"
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($a_is_rej || !empty($pendaftaran['berkas_kurang'])): ?>
                                <?php 
                                    $berkas_map = array(
                                        'ksm'        => array('label' => 'KSM (Kartu Studi Mahasiswa)', 'name' => 'KSM'),
                                        'transkrip'  => array('label' => 'Transkrip Nilai Akademik', 'name' => 'Transkrip'),
                                        'pernyataan' => array('label' => 'Surat Pernyataan Mahasiswa', 'name' => 'Surat Pernyataan'),
                                        'bebas_lab'  => array('label' => 'Surat Bebas Lab & Perpus', 'name' => 'Bebas Lab')
                                    );
                                    $raw_kurang = array_filter(explode(',', $pendaftaran['berkas_kurang'] ?? ''));
                                    $catatan_admin = $pendaftaran['catatan_admin'] ?? '';
                                ?>
                                <div class="bg-black/20 backdrop-blur-md rounded-xl p-3.5 border border-white/20 col-span-1 md:col-span-2">
                                    <span class="text-[10px] uppercase font-bold text-amber-200 block mb-1">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> Catatan Verifikasi Berkas LAA
                                    </span>
                                    
                                    <?php if(!empty($raw_kurang)): ?>
                                        <div class="mb-1">
                                            <span class="text-[11px] font-bold text-rose-200 block mb-1.5">📄 Dokumen PDF yang Harus Diunggah Ulang:</span>
                                            <ul class="list-disc list-inside space-y-1.5 text-xs text-white font-medium pl-1">
                                                <?php 
                                                    $matched_notes = array();
                                                    foreach($raw_kurang as $bk): 
                                                        $bk = trim($bk);
                                                        $info = $berkas_map[$bk] ?? array('label' => $bk, 'name' => $bk);
                                                        $lbl = $info['label'];
                                                        $sname = $info['name'];

                                                        $item_note = '';
                                                        if (!empty($catatan_admin)) {
                                                            if (preg_match('/' . preg_quote($sname, '/') . '\s*:\s*([^-\n]+)/i', $catatan_admin, $m)) {
                                                                $item_note = trim($m[1]);
                                                                $matched_notes[] = $m[0];
                                                            } else if (preg_match('/' . preg_quote($bk, '/') . '\s*:\s*([^-\n]+)/i', $catatan_admin, $m)) {
                                                                $item_note = trim($m[1]);
                                                                $matched_notes[] = $m[0];
                                                            }
                                                        }
                                                ?>
                                                    <li>
                                                        <span class="bg-rose-900/80 border border-rose-400 px-2 py-0.5 rounded font-bold text-white text-[11px] inline-block my-0.5"><?= htmlspecialchars($lbl); ?></span>
                                                        <?php if(!empty($item_note)): ?>
                                                            <span class="text-amber-200 font-bold ml-1"> — "<?= htmlspecialchars($item_note); ?>"</span>
                                                        <?php else: ?>
                                                            <span class="text-rose-100 italic ml-1"> — File Kurang / Perlu Revisi</span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(!empty($catatan_admin) && empty($matched_notes)): ?>
                                        <p class="text-white leading-relaxed font-medium italic mt-2">
                                            "<?= htmlspecialchars($catatan_admin); ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if($k_is_rej): ?>
                                <div class="bg-black/20 backdrop-blur-md rounded-xl p-3.5 border border-white/20">
                                    <span class="text-[10px] uppercase font-bold text-amber-200 block mb-1">
                                        <i class="bi bi-award-fill"></i> Catatan Revisi Koordinator TA
                                    </span>
                                    <p class="text-white leading-relaxed font-medium">
                                        "<?= htmlspecialchars(!empty($pendaftaran['catatan_koor']) ? $pendaftaran['catatan_koor'] : 'Koordinator TA meminta revisi rencana topik.'); ?>"
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($kk_is_rej): ?>
                                <div class="bg-black/20 backdrop-blur-md rounded-xl p-3.5 border border-white/20">
                                    <span class="text-[10px] uppercase font-bold text-amber-200 block mb-1">
                                        <i class="bi bi-mortarboard-fill"></i> Catatan Revisi Ketua KK
                                    </span>
                                    <p class="text-white leading-relaxed font-medium">
                                        "<?= htmlspecialchars(!empty($pendaftaran['catatan_kk']) ? $pendaftaran['catatan_kk'] : 'Ketua KK meminta penyesuaian rumpun keilmuan.'); ?>"
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Workflow Approval Chain Tracker (3D Stepper Line) -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-7 relative">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-0.5">REAL-TIME WORKFLOW</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-orange-500"></i> Status Approval 4 Tahap
                    </h2>
                </div>
                <div class="flex items-center gap-1.5 bg-orange-100/80 border border-orange-300/80 px-3 py-1 rounded-full text-[11px] font-semibold text-orange-700 badge-3d">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
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

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
                    <!-- Stage 1: Dosen Wali -->
                    <?php 
                        $w_card_bg = $w_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($w_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-orange-300 bg-orange-100/50');
                        $w_icon_bg = $w_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($w_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-gradient-to-tr from-orange-500 to-amber-500 text-white');
                        $w_badge_cls = $w_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($w_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-orange-200 text-orange-900 border-orange-300');
                    ?>
                    <div class="bg-white/90 p-4 sm:p-5 rounded-xl border <?= $w_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-bold tracking-wider uppercase text-slate-400">Tahap 01</span>
                            <div class="w-8 h-8 rounded-xl <?= $w_icon_bg; ?> flex items-center justify-center text-sm font-semibold box-3d">
                                <i class="bi <?= $w_is_app ? 'bi-check-lg' : ($w_is_rej ? 'bi-x-lg' : 'bi-person-check'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-xs text-slate-900 mb-0.5">Dosen Wali</h3>
                            <p class="text-[10px] text-slate-500 mb-2">Persetujuan akademik</p>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-semibold rounded-md border <?= $w_badge_cls; ?> badge-3d">
                                <span class="w-1.5 h-1.5 rounded-full <?= $w_is_app ? 'bg-emerald-500' : ($w_is_rej ? 'bg-rose-500' : 'bg-orange-600'); ?>"></span>
                                <?= $w_status; ?>
                            </span>

                            <?php if(!empty($pendaftaran['catatan_wali'])): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] <?= $w_is_app ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900'; ?>">
                                    <span class="font-bold block mb-0.5"><i class="bi bi-chat-text"></i> Catatan:</span>
                                    <p class="line-clamp-3 leading-snug">"<?= htmlspecialchars($pendaftaran['catatan_wali']); ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stage 2: Admin Layanan -->
                    <?php 
                        $a_card_bg = $a_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($a_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $a_icon_bg = $a_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($a_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $a_badge_cls = $a_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($a_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                        $laa_note = $pendaftaran['catatan_admin'] ?? '';
                    ?>
                    <div class="bg-white/90 p-4 sm:p-5 rounded-xl border <?= $a_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-bold tracking-wider uppercase text-slate-400">Tahap 02</span>
                            <div class="w-8 h-8 rounded-xl <?= $a_icon_bg; ?> flex items-center justify-center text-sm font-semibold box-3d">
                                <i class="bi <?= $a_is_app ? 'bi-check-lg' : ($a_is_rej ? 'bi-x-lg' : 'bi-shield-check'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-xs text-slate-900 mb-0.5">Admin Layanan</h3>
                            <p class="text-[10px] text-slate-500 mb-2">Verifikasi berkas PDF</p>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-semibold rounded-md border <?= $a_badge_cls; ?> badge-3d">
                                <span class="w-1.5 h-1.5 rounded-full <?= $a_is_app ? 'bg-emerald-500' : ($a_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $a_status; ?>
                            </span>

                            <?php if(!empty($raw_kurang) || !empty($laa_note)): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] <?= $a_is_app ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900'; ?>">
                                    <span class="font-bold block mb-0.5"><i class="bi bi-chat-text"></i> Catatan LAA:</span>
                                    <?php if(!empty($laa_note)): ?>
                                        <p class="line-clamp-3 leading-snug">"<?= htmlspecialchars($laa_note); ?>"</p>
                                    <?php else: ?>
                                        <p class="line-clamp-3 leading-snug">File kurang: <?= implode(', ', array_map('strtoupper', $raw_kurang)); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stage 3: Koordinator TA -->
                    <?php 
                        $k_card_bg = $k_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($k_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $k_icon_bg = $k_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($k_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $k_badge_cls = $k_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($k_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/90 p-4 sm:p-5 rounded-xl border <?= $k_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-bold tracking-wider uppercase text-slate-400">Tahap 03</span>
                            <div class="w-8 h-8 rounded-xl <?= $k_icon_bg; ?> flex items-center justify-center text-sm font-semibold box-3d">
                                <i class="bi <?= $k_is_app ? 'bi-check-lg' : ($k_is_rej ? 'bi-x-lg' : 'bi-award'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-xs text-slate-900 mb-0.5">Koordinator TA</h3>
                            <p class="text-[10px] text-slate-500 mb-2">Validasi topik & kuota</p>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-semibold rounded-md border <?= $k_badge_cls; ?> badge-3d">
                                <span class="w-1.5 h-1.5 rounded-full <?= $k_is_app ? 'bg-emerald-500' : ($k_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $k_status; ?>
                            </span>

                            <?php if(!empty($pendaftaran['catatan_koor'])): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] <?= $k_is_app ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900'; ?>">
                                    <span class="font-bold block mb-0.5"><i class="bi bi-chat-text"></i> Catatan Koor:</span>
                                    <p class="line-clamp-3 leading-snug">"<?= htmlspecialchars($pendaftaran['catatan_koor']); ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stage 4: Ketua KK -->
                    <?php 
                        $kk_card_bg = $kk_is_app ? 'border-emerald-300 bg-emerald-50/60' : ($kk_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $kk_icon_bg = $kk_is_app ? 'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white' : ($kk_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $kk_badge_cls = $kk_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($kk_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/90 p-4 sm:p-5 rounded-xl border <?= $kk_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-bold tracking-wider uppercase text-slate-400">Tahap 04</span>
                            <div class="w-8 h-8 rounded-xl <?= $kk_icon_bg; ?> flex items-center justify-center text-sm font-semibold box-3d">
                                <i class="bi <?= $kk_is_app ? 'bi-check-lg' : ($kk_is_rej ? 'bi-x-lg' : 'bi-mortarboard'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-xs text-slate-900 mb-0.5">Ketua KK</h3>
                            <p class="text-[10px] text-slate-500 mb-2">Persetujuan akhir</p>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-semibold rounded-md border <?= $kk_badge_cls; ?> badge-3d">
                                <span class="w-1.5 h-1.5 rounded-full <?= $kk_is_app ? 'bg-emerald-500' : ($kk_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $kk_status; ?>
                            </span>

                            <?php if(!empty($pendaftaran['catatan_kk'])): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] <?= $kk_is_app ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900'; ?>">
                                    <span class="font-bold block mb-0.5"><i class="bi bi-chat-text"></i> Catatan KK:</span>
                                    <p class="line-clamp-3 leading-snug">"<?= htmlspecialchars($pendaftaran['catatan_kk']); ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unlock Access Indicator Bar 3D -->
            <div class="mt-6 pt-5 border-t border-orange-200/80 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl <?= $kk_is_app ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-200 text-orange-800'; ?> flex items-center justify-center font-bold text-base box-3d">
                        <i class="bi <?= $kk_is_app ? 'bi-unlock-fill' : 'bi-lock-fill'; ?>"></i>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block leading-tight">Status Akses Bimbingan Akademik</span>
                        <span class="text-[11px] text-slate-500 font-normal">Memerlukan persetujuan hingga Tahap 04 Ketua Kelompok Keahlian (KK)</span>
                    </div>
                </div>

                <?php if($kk_is_app): ?>
                    <span class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-semibold rounded-xl flex items-center gap-1.5 box-3d">
                        <i class="bi bi-patch-check-fill text-sm"></i> UNLOCKED — Terbuka
                    </span>
                <?php else: ?>
                    <span class="px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-600 text-white text-xs font-semibold rounded-xl flex items-center gap-1.5 box-3d">
                        <i class="bi bi-clock-fill text-sm"></i> LOCKED — Menunggu
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Proposal Status Summary Widget (Full Width) -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-7 w-full">
            <div class="flex items-center justify-between border-b border-orange-100 pb-4 mb-5">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-orange-600 block mb-0.5">RINGKASAN USULAN</span>
                    <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-journal-text text-orange-500"></i> Judul Tugas Akhir Terdaftar
                    </h3>
                </div>
                <?php if(!empty($pendaftaran['judul_1'])): ?>
                    <span class="text-[11px] bg-emerald-100 border border-emerald-300 text-emerald-800 font-semibold px-3 py-1 rounded-full flex items-center gap-1.5 badge-3d">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terdaftar
                    </span>
                <?php else: ?>
                    <span class="text-[11px] bg-amber-100 border border-amber-300 text-amber-900 font-semibold px-3 py-1 rounded-full flex items-center gap-1.5 badge-3d">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum Pengajuan
                    </span>
                <?php endif; ?>
            </div>

            <?php if(!empty($pendaftaran['judul_1'])): ?>
                <div class="space-y-3">
                    <div class="p-4 rounded-xl bg-orange-100/50 border border-orange-200">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-orange-700 block mb-1">JUDUL UTAMA (PROPOSAL 1)</span>
                        <h4 class="font-semibold text-slate-900 text-sm leading-snug"><?= $pendaftaran['judul_1']; ?></h4>
                        <?php if(!empty($pendaftaran['judul_en'])): ?>
                            <p class="text-xs text-slate-600 italic mt-1.5 flex items-center gap-1.5 font-normal">
                                <i class="bi bi-translate text-orange-400"></i> "<?= $pendaftaran['judul_en']; ?>"
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-6 px-4 border border-dashed border-orange-300 rounded-xl bg-orange-50/50">
                    <div class="w-10 h-10 rounded-xl bg-orange-200 text-orange-700 flex items-center justify-center font-semibold text-lg mx-auto mb-2 box-3d">
                        <i class="bi bi-file-earmark-plus"></i>
                    </div>
                    <h4 class="font-semibold text-slate-800 text-xs mb-1">Belum Ada Usulan Judul</h4>
                    <p class="text-[11px] text-slate-500 max-w-xs mx-auto font-normal">Silakan lakukan pendaftaran judul melalui menu <strong>Pendaftaran TA</strong> di navbar atas.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
</body>
</html>




