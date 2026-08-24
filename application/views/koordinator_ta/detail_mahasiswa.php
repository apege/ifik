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

    <!-- Koordinator TA Custom Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/koordinator_ta.css?v=' . time()); ?>">
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-20">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 glass-header px-6 py-4 mb-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-lg shadow-sm">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Detail Mahasiswa & Plotting Tugas Akhir</h1>
                    <p class="text-xs text-slate-500 font-normal">Gunakan menu dock di sebelah kiri untuk berpindah tab peninjauan.</p>
                </div>
            </div>

            <a href="<?= site_url('koordinatorta'); ?>" class="text-xs font-bold text-slate-700 hover:text-orange-600 bg-white hover:bg-slate-100 border border-slate-200 px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Dashboard
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex-grow w-full space-y-6">

        <?php 
            $stWali  = $detail['status_approval_wali'] ?? 'Pending';
            $stAdmin = $detail['status_approval_admin'] ?? 'Pending';
            $stKoor  = $detail['status_approval_koor'] ?? 'Pending';
            $stKk    = $detail['status_approval_kk'] ?? 'Pending';

            // Hitung nomor tahap yang sedang aktif (1: Dosen Wali, 2: Admin Layanan, 3: Koordinator TA, 4: Ketua KK, 5: Selesai Approval)
            $activeStageNum = 1;
            if (strcasecmp($stWali, 'Approved') === 0) {
                $activeStageNum = 2;
                if (strcasecmp($stAdmin, 'Approved') === 0) {
                    $activeStageNum = 3;
                    if (strcasecmp($stKoor, 'Approved') === 0) {
                        $activeStageNum = 4;
                        if (strcasecmp($stKk, 'Approved') === 0) {
                            $activeStageNum = 5;
                        }
                    }
                }
            }

            $isWaliApproved = (strcasecmp($stWali, 'Approved') === 0);
            $isLAAApproved  = (strcasecmp($stAdmin, 'Approved') === 0);
            $isKoorEligible = ($activeStageNum >= 3);
        ?>

        <!-- Warning Alert if previous stages are not yet approved -->
        <div id="topWarningContainer">
            <?php if(!$isWaliApproved): ?>
                <div class="bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-2xl shadow-xs flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wide">Menunggu Review Dosen Wali</h4>
                        <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                            Mahasiswa ini masih berada di tahap peninjauan <strong>1. Dosen Wali</strong>. Berkas belum diverifikasi Admin Layanan maupun Koordinator TA.
                        </p>
                    </div>
                </div>
            <?php elseif(!$isLAAApproved): ?>
                <div class="bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-2xl shadow-xs flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wide">Menunggu Approval Admin Layanan (LAA)</h4>
                        <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                            Mahasiswa ini telah disetujui Dosen Wali namun <strong>belum diverifikasi berkas & SKS-nya oleh Admin Layanan (LAA)</strong>. Penetapan Dosen Pembimbing dan persetujuan Koordinator TA baru dapat diproses setelah status Admin LAA <strong>Approved</strong>.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- 1. STATUS PERSETUJUAN BERJENJANG (DI ATAS SEBAGAI BANNER UTAMA) -->
        <div class="card-custom p-6 sm:p-7">
            <div class="flex items-center justify-between mb-5 pb-3.5 border-b border-slate-200 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-600 text-white flex items-center justify-center text-base font-bold shrink-0 shadow-md shadow-orange-500/20">
                        <i class="fa-solid fa-timeline"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Status Persetujuan Berjenjang</h2>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Sync
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-normal">Tahapan persetujuan pendaftaran Tugas Akhir mahasiswa IFIK.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider hidden sm:inline">Tahap Terakhir:</span>
                    <span id="tahapTerakhirBadge" class="px-3 py-1 bg-orange-50 text-orange-700 text-xs font-extrabold rounded-full border border-orange-200 shadow-2xs">
                        <?= $detail['current_stage'] ?? 'Koordinator TA'; ?>
                    </span>
                </div>
            </div>

            <div id="statusGridStages" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <!-- Stage 1: Dosen Wali -->
                <?php 
                    $isCurrent1 = ($activeStageNum === 1);
                    $bgWali = ($stWali === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stWali === 'Rejected') ? 'bg-rose-50 border-rose-200 text-rose-800' : ($isCurrent1 ? 'bg-amber-50 border-amber-300 text-amber-800 border-2 ring-2 ring-orange-500/20' : 'bg-slate-50 border-slate-200 text-slate-600'));
                ?>
                <div class="p-3.5 rounded-xl border <?= $bgWali; ?> shadow-xs relative">
                    <?php if($isCurrent1): ?>
                        <span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>
                    <?php endif; ?>
                    <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">1. Dosen Wali</span>
                    <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                        <?php if($stWali === 'Approved'): ?>
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Disetujui
                        <?php elseif($stWali === 'Rejected'): ?>
                            <i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i> Ditolak
                        <?php else: ?>
                            <i class="fa-solid fa-clock text-amber-600 text-xs"></i> Pending
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($detail['catatan_wali'])): ?>
                        <p class="text-[10px] opacity-80 mt-1 italic line-clamp-2" title="<?= htmlspecialchars($detail['catatan_wali']); ?>">"<?= $detail['catatan_wali']; ?>"</p>
                    <?php endif; ?>
                </div>

                <!-- Stage 2: Admin Layanan -->
                <?php 
                    $isCurrent2 = ($activeStageNum === 2);
                    $bgAdmin = ($stAdmin === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stAdmin === 'Rejected') ? 'bg-rose-50 border-rose-200 text-rose-800' : ($isCurrent2 ? 'bg-amber-50 border-amber-300 text-amber-800 border-2 ring-2 ring-orange-500/20' : 'bg-slate-50 border-slate-200 text-slate-600'));
                ?>
                <div class="p-3.5 rounded-xl border <?= $bgAdmin; ?> shadow-xs relative">
                    <?php if($isCurrent2): ?>
                        <span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>
                    <?php endif; ?>
                    <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">2. Admin Layanan</span>
                    <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                        <?php if($stAdmin === 'Approved'): ?>
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Disetujui
                        <?php elseif($stAdmin === 'Rejected'): ?>
                            <i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i> Ditolak
                        <?php elseif($isCurrent2): ?>
                            <i class="fa-solid fa-clock text-amber-600 text-xs"></i> Pending
                        <?php else: ?>
                            <i class="fa-solid fa-clock text-slate-400 text-xs"></i> Menunggu
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($detail['catatan_admin'])): ?>
                        <p class="text-[10px] opacity-80 mt-1 italic line-clamp-2" title="<?= htmlspecialchars($detail['catatan_admin']); ?>">"<?= $detail['catatan_admin']; ?>"</p>
                    <?php endif; ?>
                </div>

                <!-- Stage 3: Koordinator TA -->
                <?php 
                    $isCurrent3 = ($activeStageNum === 3);
                    $bgKoor = ($stKoor === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stKoor === 'Rejected') ? 'bg-rose-50 border-rose-300 text-rose-800' : ($isCurrent3 ? 'bg-amber-50 border-amber-300 text-amber-800 border-2 ring-2 ring-orange-500/20' : 'bg-slate-50 border-slate-200 text-slate-600'));
                ?>
                <div class="p-3.5 rounded-xl border <?= $bgKoor; ?> shadow-xs relative">
                    <?php if($isCurrent3): ?>
                        <span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>
                    <?php endif; ?>
                    <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">3. Koordinator TA</span>
                    <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                        <?php if($stKoor === 'Approved'): ?>
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Disetujui
                        <?php elseif($stKoor === 'Rejected'): ?>
                            <i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i> Ditolak
                        <?php elseif($isCurrent3): ?>
                            <i class="fa-solid fa-clock text-amber-600 text-xs"></i> Pending
                        <?php else: ?>
                            <i class="fa-solid fa-clock text-slate-400 text-xs"></i> Menunggu
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($detail['catatan_koor'])): ?>
                        <p class="text-[10px] opacity-80 mt-1 italic line-clamp-2" title="<?= htmlspecialchars($detail['catatan_koor']); ?>">"<?= $detail['catatan_koor']; ?>"</p>
                    <?php endif; ?>
                </div>

                <!-- Stage 4: Ketua KK -->
                <?php 
                    $isCurrent4 = ($activeStageNum === 4);
                    $bgKk = ($stKk === 'Approved') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (($stKk === 'Rejected') ? 'bg-rose-50 border-rose-200 text-rose-800' : ($isCurrent4 ? 'bg-amber-50 border-amber-300 text-amber-800 border-2 ring-2 ring-orange-500/20' : 'bg-slate-50 border-slate-200 text-slate-600'));
                ?>
                <div class="p-3.5 rounded-xl border <?= $bgKk; ?> shadow-xs relative">
                    <?php if($isCurrent4): ?>
                        <span class="absolute -top-2.5 right-2 px-1.5 py-0.5 bg-orange-600 text-white text-[8px] font-extrabold rounded-full shadow-xs">SAAT INI</span>
                    <?php endif; ?>
                    <span class="text-[9px] font-bold uppercase tracking-wider block opacity-70 mb-1">4. Ketua KK</span>
                    <div class="font-bold flex items-center gap-1.5 text-xs mb-1">
                        <?php if($stKk === 'Approved'): ?>
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Disetujui
                        <?php elseif($stKk === 'Rejected'): ?>
                            <i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i> Ditolak
                        <?php elseif($isCurrent4): ?>
                            <i class="fa-solid fa-clock text-amber-600 text-xs"></i> Pending
                        <?php else: ?>
                            <i class="fa-solid fa-clock text-slate-400 text-xs"></i> Menunggu
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MAIN WORKSPACE: VERTICAL FLOATING DOCK (LEFT) + TAB CONTENT (RIGHT) -->
        <div class="flex flex-col md:flex-row items-start gap-8 relative">
            
            <!-- VERTICAL FLOATING DOCK (LEFT) -->
            <div class="w-full md:w-[84px] md:min-w-[84px] md:max-w-[84px] flex md:flex-col justify-center items-center shrink-0">
                <nav class="vertical-floating-dock flex-row md:flex-col" aria-label="Tab Navigation Dock">
                    
                    <!-- Dock 1: Profil Mahasiswa (Default Open) -->
                    <button type="button" onclick="switchDockTab(0)" class="dock-item active" id="dockBtn-0">
                        <i class="fa-solid fa-user-graduate text-base"></i>
                        <span class="dock-tooltip">1. Profil Mahasiswa</span>
                    </button>

                    <!-- Dock 2: Usulan Judul -->
                    <button type="button" onclick="switchDockTab(1)" class="dock-item" id="dockBtn-1">
                        <i class="fa-solid fa-book-open text-base"></i>
                        <span class="dock-tooltip">2. Usulan Judul</span>
                    </button>

                    <!-- Dock 3: Berkas Persyaratan -->
                    <button type="button" onclick="switchDockTab(2)" class="dock-item" id="dockBtn-2">
                        <i class="fa-solid fa-file-lines text-base"></i>
                        <span class="dock-tooltip">3. Berkas Persyaratan</span>
                    </button>

                    <!-- Dock 4: Keputusan Approval -->
                    <button type="button" onclick="switchDockTab(3)" class="dock-item" id="dockBtn-3">
                        <i class="fa-solid fa-user-check text-base"></i>
                        <span class="dock-tooltip">4. Keputusan Approval</span>
                    </button>

                </nav>
            </div>

            <!-- DYNAMIC TAB PANELS CONTAINER (RIGHT) -->
            <div class="flex-1 w-full min-w-0">

                <!-- TAB 1: PROFIL MAHASISWA (DEFAULT OPEN) -->
                <section id="tabPanel-0" class="tab-panel active card-custom p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-orange-500/20">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">1. Profil Mahasiswa</h3>
                                <p class="text-xs text-slate-500">Informasi identitas akademik dan kontak mahasiswa pendaftar.</p>
                            </div>
                        </div>
                        <span class="px-3.5 py-1.5 bg-orange-50 text-orange-700 text-xs font-bold rounded-full border border-orange-200">
                            <?= $detail['konsentrasi_dkv'] ?? 'Informatika'; ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <div class="text-center p-6 bg-slate-50 rounded-2xl border border-slate-200 md:col-span-1">
                            <div class="w-24 h-24 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-3xl flex items-center justify-center text-4xl font-bold mx-auto mb-3 shadow-lg shadow-orange-500/25">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 leading-tight">
                                <?= $detail['nama_depan'] ?? 'Mahasiswa'; ?> <?= $detail['nama_belakang'] ?? ''; ?>
                            </h4>
                            <p class="text-xs font-mono font-bold text-slate-500 mt-1">
                                NIM: <?= $detail['nim'] ?? '1301210001'; ?>
                            </p>
                        </div>

                        <div class="md:col-span-2 space-y-4 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Program Studi / Konsentrasi:</span>
                                    <span class="font-bold text-slate-800 text-xs"><?= $detail['konsentrasi_dkv'] ?? 'Informatika'; ?></span>
                                </div>
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nomor WhatsApp / HP:</span>
                                    <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-orange-600"></i> <?= $detail['no_hp'] ?? '081234567890'; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Email Telkom University:</span>
                                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    <i class="fa-regular fa-envelope text-orange-600"></i>
                                    <?= $detail['email'] ?? strtolower(($detail['nama_depan'] ?? 'mhs') . '.' . ($detail['nim'] ?? '1301210001')) . '@student.telkomuniversity.ac.id'; ?>
                                </span>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Alamat Domisili:</span>
                                <span class="font-medium text-slate-700 text-xs flex items-start gap-1.5 leading-relaxed">
                                    <i class="fa-solid fa-location-dot text-orange-600 mt-0.5 shrink-0"></i>
                                    <?= $detail['alamat'] ?? 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung, Jawa Barat'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Next Step Action -->
                    <div class="pt-4 border-t border-slate-200 flex justify-end">
                        <button type="button" onclick="switchDockTab(1)" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md inline-flex items-center gap-2 transition hover:-translate-y-0.5">
                            <span>Lanjut ke Usulan Judul</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </section>

                <!-- TAB 2: USULAN JUDUL TA -->
                <section id="tabPanel-1" class="tab-panel card-custom p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-orange-500/20">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">2. Usulan Judul Tugas Akhir</h3>
                                <p class="text-xs text-slate-500">Daftar usulan judul utama dan alternatif yang diajukan oleh mahasiswa.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-star text-amber-500"></i> Usulan Judul 1 (Utama):
                            </span>
                            <div class="p-4 bg-orange-50/50 rounded-2xl border-2 border-orange-200 font-bold text-slate-900 text-xs leading-relaxed shadow-2xs">
                                <?= $detail['judul_1'] ?? 'Pengembangan Sistem Informasi IFIK Berbasis Web'; ?>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Usulan Judul 2 (Alternatif 1):</span>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 font-medium text-slate-800 text-xs shadow-2xs">
                                <?= $detail['judul_2'] ?? 'Rancang Bangun Modul Mahasiswa dan Dosen Wali IFIK'; ?>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Usulan Judul 3 (Alternatif 2):</span>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 font-medium text-slate-800 text-xs shadow-2xs">
                                <?= $detail['judul_3'] ?? 'Implementasi Workflow Approval Pendaftaran Tugas Akhir'; ?>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Judul (Bahasa Inggris):</span>
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 font-medium italic text-slate-700 text-xs shadow-2xs">
                                <?= $detail['judul_en'] ?? 'Development of Web-Based IFIK Information System'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Step Actions Navigation -->
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                        <button type="button" onclick="switchDockTab(0)" class="bg-white hover:bg-slate-100 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-xs border border-slate-200 shadow-xs inline-flex items-center gap-2 transition">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            <span>Kembali ke Profil</span>
                        </button>
                        <button type="button" onclick="switchDockTab(2)" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md inline-flex items-center gap-2 transition hover:-translate-y-0.5">
                            <span>Lanjut ke Berkas Persyaratan</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </section>

                <!-- TAB 3: BERKAS PERSYARATAN PDF -->
                <section id="tabPanel-2" class="tab-panel card-custom p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-orange-500/20">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">3. Berkas Persyaratan (PDF)</h3>
                                <p class="text-xs text-slate-500">Pratinjau dan unduh berkas akademik yang diunggah oleh mahasiswa.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <?php
                            $files = array(
                                'Kartu Studi Mahasiswa (KSM)' => $detail['file_ksm'] ?? '',
                                'Transkrip Nilai Akademik' => $detail['file_transkrip'] ?? '',
                                'Surat Pernyataan TA' => $detail['file_pernyataan'] ?? '',
                                'Surat Bebas Laboratorium' => $detail['file_bebas_lab'] ?? ''
                            );
                        ?>
                        <?php foreach($files as $title => $filename): ?>
                            <?php $url = !empty($filename) ? base_url('uploads/persyaratan_ta/' . $filename) : ''; ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between shadow-2xs hover:border-orange-400 transition">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base font-bold shrink-0">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block"><?= $title; ?></span>
                                        <span class="text-[10px] text-slate-400 font-mono"><?= !empty($filename) ? $filename : 'Belum diunggah'; ?></span>
                                    </div>
                                </div>
                                <?php if(!empty($url)): ?>
                                    <a href="<?= $url; ?>" target="_blank" class="text-xs bg-white hover:bg-orange-50 text-orange-600 hover:text-orange-700 border border-slate-200 hover:border-orange-300 font-bold px-3.5 py-2 rounded-xl shadow-2xs transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-eye text-xs"></i> Lihat PDF
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Tidak Ada</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Step Actions Navigation -->
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                        <button type="button" onclick="switchDockTab(1)" class="bg-white hover:bg-slate-100 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-xs border border-slate-200 shadow-xs inline-flex items-center gap-2 transition">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            <span>Kembali ke Usulan Judul</span>
                        </button>
                        <button type="button" onclick="switchDockTab(3)" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md inline-flex items-center gap-2 transition hover:-translate-y-0.5">
                            <span>Lanjut ke Keputusan Approval</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </section>

                <!-- TAB 4: KEPUTUSAN APPROVAL & PLOTTING DOSEN PEMBIMBING -->
                <section id="tabPanel-3" class="tab-panel card-custom p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-emerald-500/20">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">4. Keputusan Approval & Plotting Dosen Pembimbing</h3>
                                <p class="text-xs text-slate-500">Tentukan keputusan persetujuan dan pilih Dosen Pembimbing 1 & 2.</p>
                            </div>
                        </div>

                        <span class="px-3.5 py-1.5 text-xs font-bold rounded-full border shadow-2xs <?= ($stKoor === 'Approved') ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : (($stKoor === 'Rejected') ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-amber-50 text-amber-700 border-amber-300'); ?>">
                            Status: <?= $stKoor; ?>
                        </span>
                    </div>

                    <form id="formApprovalKoor" onsubmit="handleAjaxApproval(event)" class="space-y-6">
                        <input type="hidden" name="nim" value="<?= $detail['nim']; ?>">

                        <!-- 1. Keputusan Status -->
                        <div>
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-gavel text-orange-600 text-xs"></i> Pilih Keputusan Persetujuan <span class="text-rose-500">*</span>:
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Option Approve -->
                                <label class="relative flex items-center justify-between p-4 sm:p-5 rounded-2xl cursor-pointer transition-all duration-200 <?= ($stKoor !== 'Rejected') ? 'border-2 border-emerald-500 bg-gradient-to-br from-emerald-50/90 to-teal-50/40 ring-4 ring-emerald-500/10 shadow-lg shadow-emerald-500/10' : 'border border-slate-200 bg-white hover:bg-slate-50 hover:-translate-y-0.5 shadow-xs'; ?>" id="labelOptApprove">
                                    <input type="radio" name="status" value="Approved" <?= ($stKoor !== 'Rejected') ? 'checked' : ''; ?> onchange="onStatusDecisionChange('Approved')" class="sr-only">
                                    
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-lg font-bold shrink-0 shadow-md shadow-emerald-500/25">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-sm text-slate-900 block leading-tight">Approve (Disetujui)</span>
                                            <span class="text-xs text-slate-500 block mt-0.5 truncate">Lanjut ke Ketua KK & tetapkan pembimbing</span>
                                        </div>
                                    </div>

                                    <div class="shrink-0 ml-3" id="indicatorApprove">
                                        <div class="w-6 h-6 rounded-full <?= ($stKoor !== 'Rejected') ? 'bg-emerald-600 text-white shadow-sm' : 'border-2 border-slate-300 bg-white text-transparent'; ?> flex items-center justify-center text-xs transition">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Option Reject -->
                                <label class="relative flex items-center justify-between p-4 sm:p-5 rounded-2xl cursor-pointer transition-all duration-200 <?= ($stKoor === 'Rejected') ? 'border-2 border-rose-500 bg-gradient-to-br from-rose-50/90 to-pink-50/40 ring-4 ring-rose-500/10 shadow-lg shadow-rose-500/10' : 'border border-slate-200 bg-white hover:bg-slate-50 hover:-translate-y-0.5 shadow-xs'; ?>" id="labelOptReject">
                                    <input type="radio" name="status" value="Rejected" <?= ($stKoor === 'Rejected') ? 'checked' : ''; ?> onchange="onStatusDecisionChange('Rejected')" class="sr-only">
                                    
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-rose-600 to-pink-500 text-white flex items-center justify-center text-lg font-bold shrink-0 shadow-md shadow-rose-500/25">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-sm text-slate-900 block leading-tight">Reject (Tolak / Revisi)</span>
                                            <span class="text-xs text-slate-500 block mt-0.5 truncate">Kirim catatan perbaikan ke mahasiswa</span>
                                        </div>
                                    </div>

                                    <div class="shrink-0 ml-3" id="indicatorReject">
                                        <div class="w-6 h-6 rounded-full <?= ($stKoor === 'Rejected') ? 'bg-rose-600 text-white shadow-sm' : 'border-2 border-slate-300 bg-white text-transparent'; ?> flex items-center justify-center text-xs transition">
                                            <i class="fa-solid fa-xmark"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- 2. Plotting Dosen Pembimbing 1 & 2 (Visible when Approved) -->
                        <div id="sectionPembimbing" class="space-y-5 pt-5 border-t border-slate-200 <?= ($stKoor === 'Rejected') ? 'hidden' : ''; ?>">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-users text-orange-600 text-xs"></i> Plotting Dosen Pembimbing <span class="text-rose-500">*</span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium">Pembimbing 1 & 2 wajib berbeda</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Dosen Pembimbing 1 Search Autocomplete -->
                                <div class="relative" id="comboboxWrapper1" style="z-index: 25;">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                        <span>Dosen Pembimbing 1 (Utama): <span class="text-rose-500">*</span></span>
                                        <span id="badgeP1" class="hidden text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2 py-0.5 rounded-md">Terpilih</span>
                                    </label>

                                    <input type="hidden" name="pembimbing_1" id="inputPembimbing1" value="<?= $detail['pembimbing_1'] ?? ''; ?>">

                                    <!-- Search Input -->
                                    <div class="relative" id="searchContainer1">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                        </div>
                                        <input 
                                            type="text" 
                                            id="searchP1" 
                                            autocomplete="off"
                                            placeholder="Ketik nama dosen atau NIP..." 
                                            onfocus="openDosenDropdown(1)" 
                                            onclick="openDosenDropdown(1)"
                                            oninput="filterDosen(1)" 
                                            class="w-full text-xs font-bold pl-9 pr-14 py-3 bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-2xs transition cursor-pointer"
                                        >
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                                            <button type="button" id="clearP1" onclick="clearDosenSelection(1)" class="hidden text-slate-400 hover:text-slate-600 transition">
                                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                            </button>
                                            <button type="button" onclick="openDosenDropdown(1)" class="text-slate-400 hover:text-orange-600 transition">
                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Selected Chip Card -->
                                    <div id="chipP1" class="hidden mt-2 p-3 bg-gradient-to-r from-orange-50 to-amber-50/60 border border-orange-200/80 rounded-xl flex items-center justify-between shadow-2xs">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm" id="chipAvatarP1">
                                                <i class="fa-solid fa-user-tie text-xs"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p id="chipNameP1" class="text-xs font-bold text-slate-900 truncate"></p>
                                                <p id="chipNipP1" class="text-[10px] text-slate-500 font-mono mt-0.5"></p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="changeDosen(1)" class="text-[11px] font-bold text-orange-600 hover:text-orange-700 bg-white hover:bg-orange-100/60 border border-orange-200 px-3 py-1.5 rounded-lg transition shadow-2xs shrink-0 ml-2 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Ganti
                                        </button>
                                    </div>

                                    <!-- Floating Dropdown Results -->
                                    <div id="dropdownList1" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100 ring-1 ring-black/5">
                                        <!-- populated via JS -->
                                    </div>
                                </div>

                                <!-- Dosen Pembimbing 2 Search Autocomplete -->
                                <div class="relative" id="comboboxWrapper2" style="z-index: 20;">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                        <span>Dosen Pembimbing 2 (Pendamping): <span class="text-rose-500">*</span></span>
                                        <span id="badgeP2" class="hidden text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2 py-0.5 rounded-md">Terpilih</span>
                                    </label>

                                    <input type="hidden" name="pembimbing_2" id="inputPembimbing2" value="<?= $detail['pembimbing_2'] ?? ''; ?>">

                                    <!-- Search Input -->
                                    <div class="relative" id="searchContainer2">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                        </div>
                                        <input 
                                            type="text" 
                                            id="searchP2" 
                                            autocomplete="off"
                                            placeholder="Ketik nama dosen atau NIP..." 
                                            onfocus="openDosenDropdown(2)" 
                                            onclick="openDosenDropdown(2)"
                                            oninput="filterDosen(2)" 
                                            class="w-full text-xs font-bold pl-9 pr-14 py-3 bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-2xs transition cursor-pointer"
                                        >
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                                            <button type="button" id="clearP2" onclick="clearDosenSelection(2)" class="hidden text-slate-400 hover:text-slate-600 transition">
                                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                            </button>
                                            <button type="button" onclick="openDosenDropdown(2)" class="text-slate-400 hover:text-orange-600 transition">
                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Selected Chip Card -->
                                    <div id="chipP2" class="hidden mt-2 p-3 bg-gradient-to-r from-orange-50 to-amber-50/60 border border-orange-200/80 rounded-xl flex items-center justify-between shadow-2xs">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm" id="chipAvatarP2">
                                                <i class="fa-solid fa-user-tie text-xs"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p id="chipNameP2" class="text-xs font-bold text-slate-900 truncate"></p>
                                                <p id="chipNipP2" class="text-[10px] text-slate-500 font-mono mt-0.5"></p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="changeDosen(2)" class="text-[11px] font-bold text-orange-600 hover:text-orange-700 bg-white hover:bg-orange-100/60 border border-orange-200 px-3 py-1.5 rounded-lg transition shadow-2xs shrink-0 ml-2 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Ganti
                                        </button>
                                    </div>

                                    <!-- Floating Dropdown Results -->
                                    <div id="dropdownList2" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100 ring-1 ring-black/5">
                                        <!-- populated via JS -->
                                    </div>
                                </div>
                            </div>

                            <p id="pembimbingConflictWarning" class="hidden text-xs font-bold text-rose-600 flex items-center gap-1.5 bg-rose-50 p-2.5 rounded-lg border border-rose-200">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> Dosen Pembimbing 1 dan Pembimbing 2 tidak boleh sama! Silakan pilih dosen yang berbeda.
                            </p>
                        </div>

                        <!-- 3. Catatan Koordinator TA -->
                        <div class="pt-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2" id="catatanLabel">
                                Catatan Koordinator TA:
                            </label>
                            <textarea class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium text-slate-800 leading-relaxed transition" name="catatan_koor" id="catatanKoor" rows="3" placeholder="Masukkan catatan usulan judul atau arahan untuk mahasiswa/pembimbing..."><?= $detail['catatan_koor'] ?? ''; ?></textarea>
                            <p class="hidden text-xs font-semibold text-rose-600 mt-2 flex items-center gap-1.5" id="catatanWarning">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> Alasan penolakan wajib diisi sebelum menyimpan keputusan Reject!
                            </p>
                        </div>

                        <!-- 4. Tombol Submit Approval via AJAX -->
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-4">
                            <button type="button" onclick="switchDockTab(2)" class="bg-white hover:bg-slate-100 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-xs border border-slate-200 shadow-xs inline-flex items-center gap-2 transition">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                                <span>Kembali ke Berkas</span>
                            </button>

                            <button type="submit" id="btnSubmitApproval" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold px-6 py-3 rounded-xl text-xs shadow-md shadow-orange-600/20 inline-flex items-center gap-2 transition-all hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span id="btnSubmitText">Simpan Keputusan Approval & Pembimbing</span>
                            </button>
                        </div>
                    </form>
                </section>

            </div>

        </div>

    </main>

    <!-- Global Config for External Modular Script -->
    <script>
        window.KOOR_CONFIG = {
            nim: "<?= $detail['nim']; ?>",
            ajaxApprovalUrl: "<?= site_url('koordinatorta/ajax_approval'); ?>",
            ajaxRealtimeUrl: "<?= site_url('koordinatorta/ajax_realtime_status/' . $detail['nim']); ?>",
            stWali: "<?= $stWali; ?>",
            stAdmin: "<?= $stAdmin; ?>",
            stKoor: "<?= $stKoor; ?>",
            stKk: "<?= $stKk; ?>",
            activeStageNum: <?= $activeStageNum; ?>,
            tahapTerakhir: "<?= $detail['current_stage'] ?? 'Dosen Wali'; ?>",
            isWaliApproved: <?= $isWaliApproved ? 'true' : 'false'; ?>,
            isLAAApproved: <?= $isLAAApproved ? 'true' : 'false'; ?>,
            dosenList: <?= json_encode($dosen_list ?? []); ?>,
            initialP1: "<?= $detail['pembimbing_1'] ?? ''; ?>",
            initialP2: "<?= $detail['pembimbing_2'] ?? ''; ?>"
        };
    </script>

    <!-- Koordinator TA Modular JavaScript Engine -->
    <script src="<?= base_url('assets/js/koordinator_ta_detail.js?v=' . time()); ?>"></script>
</body>
</html>
