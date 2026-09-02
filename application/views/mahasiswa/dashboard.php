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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- PHP Progress Calculation -->
    <?php
        $has_ta = !empty($pendaftaran['judul_1']);

        $w_status = $has_ta ? ($pendaftaran['status_approval_wali'] ?? 'Pending') : 'Belum Diajukan';
        $a_status = $has_ta ? ($pendaftaran['status_approval_admin'] ?? 'Pending') : 'Belum Diajukan';
        $k_status = $has_ta ? ($pendaftaran['status_approval_koor'] ?? 'Pending') : 'Belum Diajukan';
        $kk_status = $has_ta ? ($pendaftaran['status_approval_kk'] ?? 'Pending') : 'Belum Diajukan';

        $w_is_app = ($w_status === 'Approved');
        $w_is_rej = ($w_status === 'Rejected');

        $a_is_app = ($a_status === 'Approved');
        $a_is_rej = ($a_status === 'Rejected');

        $k_is_app = ($k_status === 'Approved');
        $k_is_rej = ($k_status === 'Rejected');

        $kk_is_app = ($kk_status === 'Approved');
        $kk_is_rej = ($kk_status === 'Rejected');

        $approved_count = 0;
        if ($has_ta) {
            if ($w_status === 'Approved') $approved_count++;
            if ($a_status === 'Approved') $approved_count++;
            if ($k_status === 'Approved') $approved_count++;
            if ($kk_status === 'Approved') $approved_count++;
        }

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
                    <a href="<?= site_url('mahasiswa/bimbingan'); ?>" class="nav-link flex items-center gap-2 tracking-wide">
                        <i class="bi bi-person-video3"></i>
                        <span>Bimbingan TA</span>
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

        <!-- Rejection / Revision Notes Alert Card (Only when a stage is actually Rejected or berkas_kurang is filled) -->
        <?php 
            $has_rejection = ($w_is_rej || $a_is_rej || $k_is_rej || $kk_is_rej || !empty($pendaftaran['berkas_kurang']));

            // Hitung langkah (step) target untuk langsung di-redirect
            $rev_step = 1;
            if ($a_is_rej || !empty($pendaftaran['berkas_kurang'])) {
                $raw_str = $pendaftaran['berkas_kurang'] ?? '';
                $raw_k = array();
                if (!empty($raw_str)) {
                    if (is_string($raw_str) && (strpos(trim($raw_str), '[') === 0 || strpos(trim($raw_str), '{') === 0)) {
                        $decoded = json_decode($raw_str, true);
                        if (is_array($decoded)) $raw_k = array_map('trim', $decoded);
                    }
                    if (empty($raw_k)) {
                        $parts = explode(',', $raw_str);
                        foreach ($parts as $p) {
                            $p = trim($p, "[]\"' \t\n\r\0\x0B");
                            if ($p !== '') $raw_k[] = $p;
                        }
                    }
                }
                if (!empty($raw_k)) $rev_step = 3;
                else $rev_step = 3;
            } else if ($w_is_rej || $k_is_rej || $kk_is_rej) {
                $rev_step = 2; // Step 2 (Usulan Judul)
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
                                <i class="bi bi-pencil-square"></i> Edit &amp; Perbarui Berkas
                            </a>
                        </div>
                        <p class="text-xs text-rose-100 mb-4 font-normal">
                            Mohon perhatikan catatan revisi dari pihak berwenang di bawah ini untuk memperbarui pendaftaran Anda:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <?php if($w_is_rej): ?>
                                <?php
                                    $raw_catatan_wali = trim($pendaftaran['catatan_wali'] ?? '');
                                    // Bersihkan catatan dari format bracket legacy [KSM]: ... / [Transkrip]: ... agar murni hanya menampilkan box komentar umum
                                    $clean_catatan_wali = preg_replace('/\[(KSM|TRANSKRIP|PERNYATAAN|BEBAS_LAB|JENIS TA|JUDUL TA)[^\]]*\]\s*:\s*[^\n\r]+/i', '', $raw_catatan_wali);
                                    $clean_catatan_wali = trim($clean_catatan_wali);
                                ?>
                                <div class="bg-black/20 backdrop-blur-md rounded-xl p-3.5 border border-white/20">
                                    <span class="text-[10px] uppercase font-bold text-amber-200 block mb-1">
                                        <i class="bi bi-person-check-fill"></i> Catatan Revisi Dosen Wali
                                    </span>
                                    <p class="text-white leading-relaxed font-medium">
                                        "<?= htmlspecialchars(!empty($clean_catatan_wali) ? $clean_catatan_wali : 'Dosen wali meminta perbaikan pada pengajuan Anda. Silakan periksa rincian pada tabel di bawah.'); ?>"
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
                                    $raw_kurang = $raw_k;
                                    $catatan_admin = $pendaftaran['catatan_admin'] ?? '';

                                    $doc_notes = array();
                                    $general_notes = array();

                                    if (!empty($catatan_admin)) {
                                        $berkas_keywords = array(
                                            'ksm'        => array('KSM', 'Kartu Studi'),
                                            'transkrip'  => array('Transkrip Nilai Akademik', 'Transkrip Nilai', 'Transkrip'),
                                            'pernyataan' => array('Surat Pernyataan Mahasiswa', 'Surat Pernyataan', 'Pernyataan'),
                                            'bebas_lab'  => array('Surat Bebas Laboratorium & Perpustakaan', 'Surat Bebas Lab & Perpustakaan', 'Surat Bebas Lab & Perpus', 'Surat Bebas Lab', 'Bebas Lab')
                                        );

                                        $segments = preg_split('/(?:^|\n|\s+-\s+)/', $catatan_admin);
                                        foreach ($segments as $seg) {
                                            $seg = trim($seg, "- \t\n\r\0\x0B");
                                            if (empty($seg)) continue;

                                            $matched_key = null;
                                            $extracted_text = '';

                                            foreach ($berkas_keywords as $key => $kw_list) {
                                                foreach ($kw_list as $kw) {
                                                    if (stripos($seg, $kw . ':') !== false) {
                                                        $parts = explode(':', $seg, 2);
                                                        $extracted_text = trim($parts[1] ?? '');
                                                        $matched_key = $key;
                                                        break 2;
                                                    }
                                                }
                                            }

                                            if ($matched_key && !empty($extracted_text)) {
                                                $doc_notes[$matched_key] = $extracted_text;
                                            } else {
                                                $general_notes[] = $seg;
                                            }
                                        }
                                    }
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
                                                    foreach($raw_kurang as $bk): 
                                                        $bk = trim($bk);
                                                        $info = $berkas_map[$bk] ?? array('label' => $bk, 'name' => $bk);
                                                        $lbl = $info['label'];
                                                        $item_note = $doc_notes[$bk] ?? '';
                                                ?>
                                                    <li class="my-1">
                                                        <span class="bg-rose-900/80 border border-rose-400 px-2 py-0.5 rounded font-bold text-white text-[11px] inline-block"><?= htmlspecialchars($lbl); ?></span>
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

                                    <?php if(!empty($general_notes)): ?>
                                        <p class="text-white leading-relaxed font-medium italic mt-2.5 pt-2 border-t border-white/20">
                                            "<?= htmlspecialchars(implode(' | ', $general_notes)); ?>"
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
                        $w_card_bg = $w_is_app ? 'border-2 border-emerald-400 bg-emerald-50/50 shadow-md shadow-emerald-500/10' : ($w_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-orange-300 bg-orange-100/50');
                        $w_icon_bg = $w_is_app ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/50 ring-4 ring-emerald-400/25' : ($w_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-gradient-to-tr from-orange-500 to-amber-500 text-white');
                        $w_badge_cls = $w_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($w_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-orange-200 text-orange-900 border-orange-300');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $w_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 01</span>
                            <div class="w-9 h-9 rounded-xl <?= $w_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $w_is_app ? 'bi-check-lg text-lg' : ($w_is_rej ? 'bi-x-lg' : 'bi-person-check'); ?>"></i>
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
                        $a_card_bg = $a_is_app ? 'border-2 border-emerald-400 bg-emerald-50/50 shadow-md shadow-emerald-500/10' : ($a_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $a_icon_bg = $a_is_app ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/50 ring-4 ring-emerald-400/25' : ($a_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $a_badge_cls = $a_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($a_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                        $laa_note = $pendaftaran['catatan_admin'] ?? '';
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $a_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 02</span>
                            <div class="w-9 h-9 rounded-xl <?= $a_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $a_is_app ? 'bi-check-lg text-lg' : ($a_is_rej ? 'bi-x-lg' : 'bi-shield-check'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Admin Layanan</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Verifikasi berkas PDF</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $a_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $a_is_app ? 'bg-emerald-500' : ($a_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $a_status; ?>
                            </span>

                            <?php if($a_is_rej && (!empty($raw_kurang) || !empty($laa_note))): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] bg-rose-50 border border-rose-200 text-rose-900">
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
                        $k_card_bg = $k_is_app ? 'border-2 border-emerald-400 bg-emerald-50/50 shadow-md shadow-emerald-500/10' : ($k_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $k_icon_bg = $k_is_app ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/50 ring-4 ring-emerald-400/25' : ($k_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $k_badge_cls = $k_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($k_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $k_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 03</span>
                            <div class="w-9 h-9 rounded-xl <?= $k_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $k_is_app ? 'bi-check-lg text-lg' : ($k_is_rej ? 'bi-x-lg' : 'bi-award'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Koordinator TA</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Penetapan Pembimbing</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $k_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $k_is_app ? 'bg-emerald-500' : ($k_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $k_status; ?>
                            </span>

                            <?php if($k_is_rej && !empty($pendaftaran['catatan_koor'])): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] bg-rose-50 border border-rose-200 text-rose-900">
                                    <span class="font-bold block mb-0.5"><i class="bi bi-chat-text"></i> Catatan Koor:</span>
                                    <p class="line-clamp-3 leading-snug">"<?= htmlspecialchars($pendaftaran['catatan_koor']); ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stage 4: Ketua KK -->
                    <?php 
                        $kk_card_bg = $kk_is_app ? 'border-2 border-emerald-400 bg-emerald-50/50 shadow-md shadow-emerald-500/10' : ($kk_is_rej ? 'border-rose-300 bg-rose-50/60' : 'border-slate-200 bg-slate-50/80');
                        $kk_icon_bg = $kk_is_app ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/50 ring-4 ring-emerald-400/25' : ($kk_is_rej ? 'bg-gradient-to-tr from-rose-600 to-red-400 text-white' : 'bg-slate-200 text-slate-500');
                        $kk_badge_cls = $kk_is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($kk_is_rej ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                    ?>
                    <div class="bg-white/95 p-5 rounded-2xl border <?= $kk_card_bg; ?> shadow-xs hover-card-elevate flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Tahap 04</span>
                            <div class="w-9 h-9 rounded-xl <?= $kk_icon_bg; ?> flex items-center justify-center text-base font-bold box-3d">
                                <i class="bi <?= $kk_is_app ? 'bi-check-lg text-lg' : ($kk_is_rej ? 'bi-x-lg' : 'bi-mortarboard'); ?>"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 mb-1">Ketua KK</h3>
                            <p class="text-xs text-slate-500 mb-3 font-medium">Persetujuan topik &amp; KK</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-lg border <?= $kk_badge_cls; ?> badge-3d">
                                <span class="w-2 h-2 rounded-full <?= $kk_is_app ? 'bg-emerald-500' : ($kk_is_rej ? 'bg-rose-500' : 'bg-slate-400'); ?>"></span>
                                <?= $kk_status; ?>
                            </span>

                            <?php if($kk_is_rej && !empty($pendaftaran['catatan_kk'])): ?>
                                <div class="mt-2.5 p-2 rounded-lg text-[10px] bg-rose-50 border border-rose-200 text-rose-900">
                                    <span class="font-bold block mb-0.5"><i class="bi bi-chat-text"></i> Catatan KK:</span>
                                    <p class="line-clamp-3 leading-snug">"<?= htmlspecialchars($pendaftaran['catatan_kk']); ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bimbingan Status Bottom Bar -->
            <div class="mt-6 pt-4 border-t border-orange-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl <?= $k_is_app ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'; ?> flex items-center justify-center text-lg font-bold shrink-0 box-3d">
                        <i class="bi <?= $k_is_app ? 'bi-unlock-fill' : 'bi-lock-fill'; ?>"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Status Akses Bimbingan Akademik</h4>
                        <p class="text-xs text-slate-500 font-medium">
                            <?= $k_is_app ? 'Persetujuan 4 tahap selesai. Dosen Pembimbing resmi ditetapkan.' : 'Memerlukan persetujuan hingga Tahap 04 Koordinator TA (Penetapan Pembimbing)'; ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <?php if($k_is_app): ?>
                        <a href="<?= site_url('mahasiswa/bimbingan'); ?>" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 box-3d shadow-md hover:scale-105 active:scale-95 transition-all">
                            <i class="bi bi-patch-check-fill text-base"></i> UNLOCKED — Masuk Bimbingan &amp; Upload Preview 1 <i class="bi bi-arrow-right text-xs"></i>
                        </a>
                    <?php else: ?>
                        <span class="px-5 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 text-white text-xs font-bold rounded-xl flex items-center gap-2 box-3d">
                            <i class="bi bi-clock-fill text-base"></i> LOCKED — Menunggu
                        </span>
                        <a href="<?= site_url('mahasiswa/bimbingan'); ?>" class="px-3.5 py-2 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-800 text-[11px] font-bold transition flex items-center gap-1.5" title="Uji Coba Langsung Modul Bimbingan & Preview 1">
                            <i class="bi bi-box-arrow-up-right"></i> Buka Bimbingan (Testing)
                        </a>
                    <?php endif; ?>
                </div>
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
                                <th class="py-4.5 px-6 text-center">Review Berkas</th>
                                <th class="py-4.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr class="hover:bg-orange-50/20 transition-colors">
                                <td class="py-5 px-6 text-center font-bold text-slate-700 text-base">1</td>
                                <td class="py-5 px-6">
                                    <?php
                                        $st_j = $pendaftaran['status_judul'] ?? 'Pending';
                                    ?>
                                    <div class="font-bold text-slate-900 text-base mb-1.5 leading-snug">
                                        <?= htmlspecialchars($pendaftaran['judul_1'] ?? '-'); ?>
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap text-xs">
                                        <?php if($st_j === 'Approved'): ?>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <i class="bi bi-check-circle-fill text-emerald-600"></i> Judul Disetujui
                                            </span>
                                        <?php elseif($st_j === 'Rejected'): ?>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300">
                                                <i class="bi bi-x-circle-fill text-rose-600"></i> Judul Ditolak / Revisi
                                            </span>
                                        <?php endif; ?>
                                        <?php if(!empty($pendaftaran['judul_en'])): ?>
                                            <span class="text-slate-500 italic flex items-center gap-1 font-normal text-xs">
                                                <i class="bi bi-translate text-orange-500"></i> "<?= htmlspecialchars($pendaftaran['judul_en']); ?>"
                                            </span>
                                        <?php endif; ?>
                                    </div>
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
                                <?php
                                    $s_ksm = $pendaftaran['status_file_ksm'] ?? 'Pending';
                                    $s_trn = $pendaftaran['status_file_transkrip'] ?? 'Pending';
                                    $s_prn = $pendaftaran['status_file_pernyataan'] ?? 'Pending';
                                    $s_lab = $pendaftaran['status_file_bebas_lab'] ?? 'Pending';
                                    $s_jud = $pendaftaran['status_judul'] ?? 'Pending';
                                    $s_jen = $pendaftaran['status_jenis_ta'] ?? 'Pending';

                                    $app_items = 0;
                                    if ($s_ksm === 'Approved') $app_items++;
                                    if ($s_trn === 'Approved') $app_items++;
                                    if ($s_prn === 'Approved') $app_items++;
                                    if ($s_lab === 'Approved') $app_items++;
                                    if ($s_jud === 'Approved') $app_items++;
                                    if ($s_jen === 'Approved') $app_items++;

                                    $rej_items = 0;
                                    if ($s_ksm === 'Rejected') $rej_items++;
                                    if ($s_trn === 'Rejected') $rej_items++;
                                    if ($s_prn === 'Rejected') $rej_items++;
                                    if ($s_lab === 'Rejected') $rej_items++;
                                    if ($s_jud === 'Rejected') $rej_items++;
                                    if ($s_jen === 'Rejected') $rej_items++;

                                    $total_eval_items = 6;
                                    $pen_items = max(0, 6 - $app_items - $rej_items);

                                    $overall_status = $pendaftaran['status_approval_wali'] ?? 'Pending';
                                    if ($overall_status === 'Approved') {
                                        $overall_badge_text = 'Disetujui';
                                        $overall_badge_cls = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                        $overall_dot_cls = 'bg-emerald-500';
                                    } elseif ($overall_status === 'Rejected' || $rej_items > 0) {
                                        $overall_badge_text = 'Perlu Revisi';
                                        $overall_badge_cls = 'bg-rose-100 text-rose-800 border-rose-300';
                                        $overall_dot_cls = 'bg-rose-500 animate-pulse';
                                    } else {
                                        $overall_badge_text = 'Pending';
                                        $overall_badge_cls = 'bg-amber-100 text-amber-800 border-amber-300';
                                        $overall_dot_cls = 'bg-amber-500 animate-pulse';
                                    }
                                ?>
                                <!-- Kolom Status Keseluruhan -->
                                <td class="py-5 px-6 text-center whitespace-nowrap align-middle">
                                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold border <?= $overall_badge_cls; ?>">
                                        <span class="w-2 h-2 rounded-full <?= $overall_dot_cls; ?>"></span>
                                        <?= $overall_badge_text; ?>
                                    </span>
                                </td>

                                <!-- Kolom Rincian Review Berkas (Di Kanan Status) -->
                                <td class="py-5 px-6 text-center whitespace-nowrap align-middle">
                                    <button type="button" onclick="openFileBreakdownModal()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[11px] font-bold <?= ($rej_items > 0) ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200'; ?> transition shadow-2xs cursor-pointer group" title="Klik untuk melihat rincian status berkas & catatan dosen">
                                        <?php if($rej_items > 0): ?>
                                            <i class="bi bi-exclamation-triangle-fill text-rose-500"></i>
                                            <span><?= $rej_items; ?> Revisi</span>
                                        <?php elseif($app_items >= 6): ?>
                                            <i class="bi bi-check-all text-emerald-600 font-bold text-sm"></i>
                                            <span class="text-emerald-800">Semua Valid (6/6)</span>
                                        <?php else: ?>
                                            <i class="bi bi-clock text-slate-500"></i>
                                            <span><?= $app_items > 0 ? $app_items . ' Valid, ' : ''; ?><?= $pen_items; ?> Menunggu</span>
                                        <?php endif; ?>
                                        <i class="bi bi-chevron-right text-[9px] opacity-60 group-hover:translate-x-0.5 transition-transform"></i>
                                    </button>
                                </td>

                                <!-- Kolom Aksi (Ditumpuk Vertikal - Tombol Edit Dihapus) -->
                                <td class="py-5 px-6 text-right whitespace-nowrap align-middle">
                                    <div class="inline-flex flex-col items-end gap-1.5">
                                        <a href="<?= site_url('mahasiswa/detail_pendaftaran'); ?>" class="w-24 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-full bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-bold transition shadow-2xs hover:scale-105 active:scale-95" title="Lihat Detail Lengkap Pengajuan Tugas Akhir">
                                            <i class="bi bi-eye-fill text-xs"></i> Detail
                                        </a>
                                        <button type="button" onclick="openResetModal()" class="w-24 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-bold transition cursor-pointer shadow-2xs hover:scale-105 active:scale-95" title="Reset / Batalkan Pengajuan">
                                            <i class="bi bi-arrow-counterclockwise text-xs"></i> Reset
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="py-12 px-6 text-center rounded-2xl bg-white/60 border-2 border-dashed border-orange-200/80 shadow-2xs space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-2xl mx-auto box-3d">
                        <i class="bi bi-journal-plus"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-extrabold text-base sm:text-lg text-slate-800">Belum Ada Pengajuan Tugas Akhir</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">Anda belum mengajukan usulan judul dan berkas persyaratan Tugas Akhir. Silakan klik tombol di bawah untuk mengisi formulir pendaftaran.</p>
                    </div>
                    <div>
                        <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow-md transition box-3d hover:scale-105 active:scale-95">
                            <i class="bi bi-plus-circle-fill"></i> Mulai Ajukan Tugas Akhir
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <?php if($has_ta): ?>
        <!-- Modal Rincian Status Berkas Persyaratan & Catatan Dosen -->
        <?php
            $note_ksm = !empty($pendaftaran['catatan_file_ksm']) ? $pendaftaran['catatan_file_ksm'] : '';
            $note_trn = !empty($pendaftaran['catatan_file_transkrip']) ? $pendaftaran['catatan_file_transkrip'] : '';
            $note_prn = !empty($pendaftaran['catatan_file_pernyataan']) ? $pendaftaran['catatan_file_pernyataan'] : '';
            $note_lab = !empty($pendaftaran['catatan_file_bebas_lab']) ? $pendaftaran['catatan_file_bebas_lab'] : '';

            // Fallback parse dari catatan_wali jika catatan_file_* kosong
            $gen_notes = $pendaftaran['catatan_wali'] ?? '';
            if (!empty($gen_notes)) {
                if (empty($note_ksm) && preg_match('/\[KSM[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_ksm = trim($m[1]);
                if (empty($note_trn) && preg_match('/\[TRANSKRIP[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_trn = trim($m[1]);
                if (empty($note_prn) && preg_match('/\[PERNYATAAN[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_prn = trim($m[1]);
                if (empty($note_lab) && preg_match('/\[BEBAS_LAB[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_lab = trim($m[1]);
            }

            $files_list = [
                [
                    'key'      => 'ksm',
                    'field'    => 'file_ksm',
                    'title'    => 'Kartu Studi Mahasiswa (KSM)',
                    'filename' => $pendaftaran['file_ksm'] ?? '',
                    'status'   => $pendaftaran['status_file_ksm'] ?? 'Pending',
                    'note'     => $note_ksm,
                ],
                [
                    'key'      => 'transkrip',
                    'field'    => 'file_transkrip',
                    'title'    => 'Transkrip Nilai Akademik',
                    'filename' => $pendaftaran['file_transkrip'] ?? '',
                    'status'   => $pendaftaran['status_file_transkrip'] ?? 'Pending',
                    'note'     => $note_trn,
                ],
                [
                    'key'      => 'pernyataan',
                    'field'    => 'file_pernyataan',
                    'title'    => 'Surat Pernyataan Keaslian',
                    'filename' => $pendaftaran['file_pernyataan'] ?? '',
                    'status'   => $pendaftaran['status_file_pernyataan'] ?? 'Pending',
                    'note'     => $note_prn,
                ],
                [
                    'key'      => 'bebas_lab',
                    'field'    => 'file_bebas_lab',
                    'title'    => 'Surat Bebas Laboratorium',
                    'filename' => $pendaftaran['file_bebas_lab'] ?? '',
                    'status'   => $pendaftaran['status_file_bebas_lab'] ?? 'Pending',
                    'note'     => $note_lab,
                ],
            ];
        ?>

        <div id="modalFileBreakdown" style="display: none;" onclick="if(event.target === this) closeFileBreakdownModal()" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-6 md:p-8 overflow-hidden">
            <div class="bg-white rounded-3xl max-w-3xl lg:max-w-4xl w-full shadow-2xl border border-orange-100 max-h-[88vh] flex flex-col overflow-hidden my-auto mx-auto">
                <form action="<?= site_url('mahasiswa/upload_revisi_berkas'); ?>" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                    <!-- Header Modal (Fixed) -->
                    <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl <?= ($rej_items > 0) ? 'bg-rose-100 text-rose-600' : 'bg-orange-100 text-orange-600'; ?> flex items-center justify-center font-bold text-lg box-3d">
                                <i class="bi <?= ($rej_items > 0) ? 'bi-exclamation-octagon-fill' : 'bi-folder-check'; ?>"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">
                                    <?= ($rej_items > 0) ? 'Perbaikan Tugas Akhir (' . $rej_items . ' Bagian Perlu Revisi)' : 'Rincian Verifikasi Berkas & Usulan Judul'; ?>
                                </h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    <?= ($rej_items > 0) ? 'Silakan perbaiki usulan judul atau unggah dokumen perbaikan yang ditolak oleh Dosen Wali di bawah ini.' : 'Status persetujuan &amp; catatan perbaikan dari Dosen Wali.'; ?>
                                </p>
                            </div>
                        </div>
                        <button type="button" onclick="closeFileBreakdownModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Scrollable Modal Body -->
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-4 flex-1">
                        <!-- Summary Chips -->
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-800 font-bold rounded-lg border border-emerald-200 flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-emerald-600"></i> <?= $app_items; ?> Disetujui
                            </span>
                            <span class="px-3 py-1 <?= ($rej_items > 0) ? 'bg-rose-50 text-rose-800 border-rose-200 font-bold' : 'bg-slate-50 text-slate-500 border-slate-200'; ?> rounded-lg border flex items-center gap-1.5">
                                <i class="bi <?= ($rej_items > 0) ? 'bi-x-circle-fill text-rose-600' : 'bi-dash-circle text-slate-400'; ?>"></i> <?= $rej_items; ?> Ditolak / Revisi
                            </span>
                            <span class="px-3 py-1 bg-slate-100 text-slate-700 font-bold rounded-lg border border-slate-200 flex items-center gap-1.5">
                                <i class="bi bi-clock-history text-slate-500"></i> <?= $pen_items; ?> Menunggu Review
                            </span>
                        </div>

                        <?php
                            $st_jen = $pendaftaran['status_jenis_ta'] ?? 'Pending';
                            $note_jen = $pendaftaran['catatan_jenis_ta'] ?? '';
                            $st_j = $pendaftaran['status_judul'] ?? 'Pending';
                            $note_j = $pendaftaran['catatan_judul'] ?? '';
                        ?>

                        <!-- Daftar Berkas & Usulan Komponen Pendaftaran -->
                        <div class="space-y-3.5">
                            <?php if($rej_items > 0): ?>
                                <!-- 1. HANYA MENAMPILKAN KOMPONEN YANG PERLU REVISI (SALAH) -->
                                <?php if($st_jen === 'Rejected'): ?>
                                    <div class="p-5 rounded-2xl border-2 border-rose-400 bg-rose-50/70 shadow-sm space-y-3.5 transition-all">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shrink-0 box-3d shadow-xs">
                                                    <i class="bi bi-diagram-3-fill"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="font-black text-sm text-rose-950 truncate">
                                                        Jenis &amp; Skema Tugas Akhir
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                                        Skema Saat Ini: <?= htmlspecialchars($pendaftaran['jenis_ta'] ?? '-'); ?> (<?= htmlspecialchars($pendaftaran['konsentrasi_dkv'] ?? 'Informatika'); ?>)
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300 shrink-0">
                                                <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                                                Perlu Revisi
                                            </span>
                                        </div>

                                        <?php if(!empty($note_jen)): ?>
                                            <div class="p-3.5 rounded-xl bg-white/95 border border-rose-200 text-xs space-y-1.5 shadow-2xs">
                                                <div class="flex items-center gap-1.5 font-black text-[11px] text-rose-700 uppercase tracking-wider">
                                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Perbaikan Dosen Wali:
                                                </div>
                                                <p class="text-xs font-medium leading-relaxed italic text-slate-800">
                                                    "<?= htmlspecialchars($note_jen); ?>"
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if($st_j === 'Rejected'): ?>
                                    <div class="p-5 rounded-2xl border-2 border-rose-400 bg-rose-50/70 shadow-sm space-y-3.5 transition-all">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shrink-0 box-3d shadow-xs">
                                                    <i class="bi bi-journal-x"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="font-black text-sm text-rose-950 truncate">
                                                        Usulan Judul Tugas Akhir (Utama)
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                                        Judul Sebelumnya: "<?= htmlspecialchars($pendaftaran['judul_1'] ?? '-'); ?>"
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300 shrink-0">
                                                <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                                                Perlu Revisi
                                            </span>
                                        </div>

                                        <?php if(!empty($note_j)): ?>
                                            <div class="p-3.5 rounded-xl bg-white/95 border border-rose-200 text-xs space-y-1.5 shadow-2xs">
                                                <div class="flex items-center gap-1.5 font-black text-[11px] text-rose-700 uppercase tracking-wider">
                                                    <i class="bi bi-chat-left-dots-fill"></i> Saran / Catatan Dosen Wali:
                                                </div>
                                                <p class="text-xs font-medium leading-relaxed italic text-slate-800">
                                                    "<?= htmlspecialchars($note_j); ?>"
                                                </p>
                                            </div>
                                        <?php endif; ?>

                                        <div class="p-3.5 bg-white rounded-xl border border-dashed border-rose-300 space-y-2">
                                            <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="bi bi-pencil-square text-orange-600 text-sm"></i> Perbarui Usulan Judul TA (Utama):
                                            </label>
                                            <input type="text" name="judul_1" value="<?= htmlspecialchars($pendaftaran['judul_1'] ?? ''); ?>" required placeholder="Ketik usulan judul perbaikan..." class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none bg-slate-50">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php foreach($files_list as $f): ?>
                                    <?php if($f['status'] === 'Rejected'): ?>
                                        <div class="p-5 rounded-2xl border-2 border-rose-400 bg-rose-50/70 shadow-sm space-y-3.5 transition-all">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shrink-0 box-3d shadow-xs">
                                                        <i class="bi bi-file-earmark-x-fill"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h4 class="font-black text-sm text-rose-950 truncate">
                                                            <?= $f['title']; ?>
                                                        </h4>
                                                        <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                                            File Sebelumnya: <?= !empty($f['filename']) ? htmlspecialchars($f['filename']) : 'Belum diunggah'; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300 shrink-0">
                                                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                                                    Perlu Revisi
                                                </span>
                                            </div>

                                            <!-- Catatan Perbaikan dari Dosen Wali -->
                                            <div class="p-3.5 rounded-xl bg-white/95 border border-rose-200 text-xs space-y-1.5 shadow-2xs">
                                                <div class="flex items-center gap-1.5 font-black text-[11px] text-rose-700 uppercase tracking-wider">
                                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Perbaikan Dosen Wali:
                                                </div>
                                                <p class="text-xs font-medium leading-relaxed italic text-slate-800">
                                                    "<?= !empty($f['note']) ? htmlspecialchars($f['note']) : 'Berkas belum sesuai ketentuan, silakan unggah ulang file PDF perbaikan.'; ?>"
                                                </p>
                                            </div>

                                            <!-- Form Upload File Pengganti -->
                                            <div class="p-3.5 bg-white rounded-xl border border-dashed border-rose-300 space-y-2">
                                                <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                                    <i class="bi bi-cloud-arrow-up-fill text-orange-600 text-sm"></i> Unggah File PDF Pengganti:
                                                </label>
                                                <input type="file" name="<?= $f['field']; ?>" accept=".pdf" class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-wider file:bg-orange-600 file:text-white hover:file:bg-orange-700 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                                                <p class="text-[10px] text-slate-400">Format: PDF (Ukuran Maksimal 5MB)</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if($app_items > 0 || $pen_items > 0): ?>
                                    <!-- Accordion Bagian Yang Sudah Valid / Masih Menunggu -->
                                    <div class="pt-2">
                                        <button type="button" onclick="document.getElementById('accordionApprovedDocs').classList.toggle('hidden')" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 py-1.5 cursor-pointer transition">
                                            <i class="bi bi-check-circle-fill text-emerald-600"></i> Lihat Bagian Lainnya yang Sudah Valid / Menunggu (<?= $app_items + $pen_items; ?> Bagian) <i class="bi bi-chevron-down text-[10px]"></i>
                                        </button>
                                        <div id="accordionApprovedDocs" class="hidden mt-2 space-y-2 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                            <?php if($st_jen !== 'Rejected'): ?>
                                                <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-slate-200 text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <i class="bi <?= ($st_jen === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : 'bi-clock-history text-slate-400'; ?> text-sm"></i>
                                                        <span class="font-bold text-slate-800">Jenis &amp; Skema TA (<?= htmlspecialchars($pendaftaran['jenis_ta'] ?? 'Reguler'); ?>)</span>
                                                    </div>
                                                    <span class="px-2.5 py-0.5 <?= ($st_jen === 'Approved') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'; ?> border rounded-full text-[10px] font-bold"><?= ($st_jen === 'Approved') ? 'Disetujui' : 'Menunggu Review'; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($st_j !== 'Rejected'): ?>
                                                <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-slate-200 text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <i class="bi <?= ($st_j === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : 'bi-clock-history text-slate-400'; ?> text-sm"></i>
                                                        <span class="font-bold text-slate-800">Usulan Judul TA (Utama)</span>
                                                    </div>
                                                    <span class="px-2.5 py-0.5 <?= ($st_j === 'Approved') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'; ?> border rounded-full text-[10px] font-bold"><?= ($st_j === 'Approved') ? 'Disetujui' : 'Menunggu Review'; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php foreach($files_list as $f): ?>
                                                <?php if($f['status'] !== 'Rejected'): ?>
                                                    <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-slate-200 text-xs">
                                                        <div class="flex items-center gap-2">
                                                            <i class="bi <?= ($f['status'] === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : 'bi-clock-history text-slate-400'; ?> text-sm"></i>
                                                            <span class="font-bold text-slate-800"><?= $f['title']; ?></span>
                                                        </div>
                                                        <span class="px-2.5 py-0.5 <?= ($f['status'] === 'Approved') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'; ?> border rounded-full text-[10px] font-bold"><?= ($f['status'] === 'Approved') ? 'Disetujui' : 'Menunggu Review'; ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- 2. TAMPILKAN SELURUH 6 BAGIAN SAAT REVIEW / TIDAK ADA REVISI -->
                                
                                <!-- Bagian 1: Jenis & Skema TA -->
                                <?php 
                                    $is_app_jen = ($st_jen === 'Approved');
                                    $card_bg_jen = $is_app_jen ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 bg-white';
                                    $badge_cls_jen = $is_app_jen ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-slate-100 text-slate-700 border-slate-200';
                                ?>
                                <div class="p-4 rounded-2xl border <?= $card_bg_jen; ?> shadow-2xs space-y-2.5 transition-all">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl <?= $is_app_jen ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'; ?> flex items-center justify-center text-base font-bold shrink-0 box-3d">
                                                <i class="bi <?= $is_app_jen ? 'bi-check-lg' : 'bi-diagram-3-fill'; ?>"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">Jenis &amp; Skema Tugas Akhir</h4>
                                                <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                                                    Skema: <strong class="text-slate-700"><?= htmlspecialchars($pendaftaran['jenis_ta'] ?? 'Reguler TA'); ?></strong> &bull; Konsentrasi: <strong class="text-slate-700"><?= htmlspecialchars($pendaftaran['konsentrasi_dkv'] ?? 'Informatika'); ?></strong>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badge_cls_jen; ?> shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $is_app_jen ? 'bg-emerald-500' : 'bg-slate-400'; ?>"></span>
                                            <?= $is_app_jen ? 'Disetujui' : 'Menunggu Review'; ?>
                                        </span>
                                    </div>

                                    <?php if(!empty($note_jen)): ?>
                                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                                            <div class="flex items-center gap-1.5 font-bold text-[11px] text-amber-700 uppercase tracking-wider">
                                                <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen:
                                            </div>
                                            <p class="text-xs font-medium leading-relaxed italic">"<?= htmlspecialchars($note_jen); ?>"</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Bagian 2: Usulan Judul TA -->
                                <?php 
                                    $is_app_j = ($st_j === 'Approved');
                                    $card_bg_j = $is_app_j ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 bg-white';
                                    $badge_cls_j = $is_app_j ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-slate-100 text-slate-700 border-slate-200';
                                ?>
                                <div class="p-4 rounded-2xl border <?= $card_bg_j; ?> shadow-2xs space-y-2.5 transition-all">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl <?= $is_app_j ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'; ?> flex items-center justify-center text-base font-bold shrink-0 box-3d">
                                                <i class="bi <?= $is_app_j ? 'bi-check-lg' : 'bi-journal-text'; ?>"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">Usulan Judul Tugas Akhir (Utama)</h4>
                                                <p class="text-[11px] text-slate-600 font-medium leading-snug mt-0.5">
                                                    "<?= htmlspecialchars($pendaftaran['judul_1'] ?? '-'); ?>"
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badge_cls_j; ?> shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $is_app_j ? 'bg-emerald-500' : 'bg-slate-400'; ?>"></span>
                                            <?= $is_app_j ? 'Disetujui' : 'Menunggu Review'; ?>
                                        </span>
                                    </div>

                                    <?php if(!empty($note_j)): ?>
                                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                                            <div class="flex items-center gap-1.5 font-bold text-[11px] text-amber-700 uppercase tracking-wider">
                                                <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen:
                                            </div>
                                            <p class="text-xs font-medium leading-relaxed italic">"<?= htmlspecialchars($note_j); ?>"</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Bagian 3-6: 4 Dokumen Persyaratan -->
                                <?php foreach($files_list as $f): ?>
                                    <?php 
                                        $is_app = ($f['status'] === 'Approved');
                                        $card_bg = $is_app ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 bg-white';
                                        $badge_cls = $is_app ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-slate-100 text-slate-700 border-slate-200';
                                    ?>
                                    <div class="p-4 rounded-2xl border <?= $card_bg; ?> shadow-2xs space-y-2.5 transition-all">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-9 h-9 rounded-xl <?= $is_app ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'; ?> flex items-center justify-center text-base font-bold shrink-0 box-3d">
                                                    <i class="bi <?= $is_app ? 'bi-check-lg' : 'bi-file-earmark-pdf'; ?>"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate"><?= $f['title']; ?></h4>
                                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5"><?= !empty($f['filename']) ? htmlspecialchars($f['filename']) : 'Belum diunggah'; ?></p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badge_cls; ?> shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $is_app ? 'bg-emerald-500' : 'bg-slate-400'; ?>"></span>
                                                <?= $is_app ? 'Disetujui' : 'Menunggu Review'; ?>
                                            </span>
                                        </div>

                                        <?php if(!empty($f['note'])): ?>
                                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                                                <div class="flex items-center gap-1.5 font-bold text-[11px] text-amber-700 uppercase tracking-wider">
                                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen:
                                                </div>
                                                <p class="text-xs font-medium leading-relaxed italic">"<?= htmlspecialchars($f['note']); ?>"</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer Modal (Fixed) -->
                    <div class="p-4 px-6 border-t border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-3 shrink-0">
                        <button type="button" onclick="closeFileBreakdownModal()" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-bold transition cursor-pointer">
                            Tutup
                        </button>
                        <?php if($rej_items > 0): ?>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-black uppercase tracking-wider text-xs shadow-md transition box-3d hover:scale-105 active:scale-95 cursor-pointer">
                                <i class="bi bi-cloud-arrow-up-fill text-sm"></i> Simpan &amp; Kirim Perbaikan (<?= $rej_items; ?> Revisi)
                            </button>
                        <?php endif; ?>
                </form>
            </div>
        </div>

    <?php endif; ?>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script>
    function openResetModal() {
        Swal.fire({
            title: 'Reset Pengajuan Tugas Akhir?',
            html: '<p class="text-xs text-slate-600 leading-relaxed mt-1">Tindakan ini akan <strong>menghapus data pendaftaran TA</strong> yang telah Anda kirimkan. Anda harus mengisi ulang dari formulir Langkah 1.</p>',
            icon: 'warning',
            iconColor: '#e11d48',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="bi bi-trash3-fill"></i> Ya, Reset Pengajuan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-rose-100',
                confirmButton: 'rounded-xl font-bold px-4 py-2.5 text-xs shadow-md cursor-pointer',
                cancelButton: 'rounded-xl font-semibold px-4 py-2.5 text-xs cursor-pointer'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Bersihkan cache draft client-side seketika
                const userNim = "<?= htmlspecialchars($mahasiswa['nim'] ?? ($this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: ''))); ?>";
                if (userNim) {
                    try {
                        localStorage.removeItem('ifik_ta_active_step_' + userNim);
                        localStorage.removeItem('ifik_ta_form_draft_' + userNim);
                        sessionStorage.removeItem('ifik_ta_active_step_' + userNim);
                        sessionStorage.removeItem('ifik_ta_form_draft_' + userNim);
                    } catch (e) {}
                }
                try {
                    localStorage.removeItem('ifik_ta_active_step');
                    localStorage.removeItem('ifik_ta_form_draft');
                    sessionStorage.removeItem('ifik_ta_active_step');
                    sessionStorage.removeItem('ifik_ta_form_draft');
                } catch (e) {}

                window.location.href = '<?= site_url("mahasiswa/reset_pendaftaran"); ?>';
            }
        });
    }

    function openFileBreakdownModal() {
        const modal = document.getElementById('modalFileBreakdown');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }
        document.body.style.overflow = 'hidden';
    }
    function closeFileBreakdownModal() {
        const modal = document.getElementById('modalFileBreakdown');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
        document.body.style.overflow = '';
    }

    // AJAX Polling: Cek perubahan status approval pengajuan TA & verifikasi berkas mahasiswa secara real-time
    (() => {
        let lastStatusKey = '<?= implode("_", [
            $pendaftaran['status_approval_wali'] ?? ($w_status ?? 'Pending'),
            $pendaftaran['status_approval_admin'] ?? ($a_status ?? 'Pending'),
            $pendaftaran['status_approval_koor'] ?? ($k_status ?? 'Pending'),
            $pendaftaran['status_approval_kk'] ?? ($kk_status ?? 'Pending'),
            $pendaftaran['status_jenis_ta'] ?? 'Pending',
            $pendaftaran['status_judul'] ?? 'Pending',
            $pendaftaran['status_file_ksm'] ?? 'Pending',
            $pendaftaran['status_file_transkrip'] ?? 'Pending',
            $pendaftaran['status_file_pernyataan'] ?? 'Pending',
            $pendaftaran['status_file_bebas_lab'] ?? 'Pending',
            $pendaftaran['updated_at'] ?? ''
        ]); ?>';

        async function checkStudentTAStatus() {
            try {
                const res = await fetch('<?= site_url("mahasiswa/get_status_pendaftaran_ajax"); ?>', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const json = await res.json();
                if (!json || !json.success || !json.has_ta) return;

                const newStatusKey = `${json.status_wali}_${json.status_admin}_${json.status_koor}_${json.status_kk}_${json.status_jenis_ta}_${json.status_judul}_${json.status_file_ksm}_${json.status_file_transkrip}_${json.status_file_pernyataan}_${json.status_file_bebas_lab}_${json.updated_at || ''}`;
                
                if (lastStatusKey && newStatusKey !== lastStatusKey) {
                    console.log('Status TA atau review berkas berubah, memperbarui tampilan...');
                    // Reload otomatis agar tampilan tabel status, badge review, dan bimbingan ter-update seketika
                    window.location.reload();
                }
                lastStatusKey = newStatusKey;
            } catch (e) {
                // Silent fail
            }
        }

        // Jalankan polling otomatis setiap 4 detik
        setInterval(checkStudentTAStatus, 4000);
    })();
    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>





