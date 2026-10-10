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

    <!-- High-Visibility Floating Web Toast Notification -->
    <div id="inPageToastAlert" class="fixed top-24 right-6 z-[9999] max-w-md bg-rose-600 text-white p-4 rounded-2xl shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-[-20px] opacity-0 hidden ring-4 ring-rose-300/50">
        <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm font-bold shrink-0 box-3d">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="flex-grow pt-0.5">
            <h4 class="text-xs font-semibold uppercase tracking-wider text-rose-200">Pemberitahuan Validasi</h4>
            <p class="text-xs font-semibold text-white mt-0.5 leading-relaxed" id="toastAlertMessage">Pesan validasi...</p>
        </div>
        <button type="button" class="text-white/80 hover:text-white p-1 text-sm transition" id="btnCloseToast">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Auto Role-Aware Curved Animated Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Page Content Wrapper (Shrinks / Expands with Sidebar) -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col flex-grow">

        <!-- Header Glass Navbar (Clean White Glass - Identical to Dashboard) -->
        <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs mb-6">
            <div class="w-full px-4 sm:px-6 lg:px-10">
                <div class="flex items-center justify-between h-16 sm:h-18">
                    <!-- Brand -->
                    <div class="flex items-center gap-3 header-title-shift pl-11 lg:pl-0">
                    <div class="w-9 h-9 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-xl font-bold text-lg flex items-center justify-center box-3d">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider text-orange-500 mt-0.5 block">Akademik Mahasiswa</span>
                    </div>
                </div>

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

    <!-- Main Container (Centered Form Wizard Layout) -->
    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow space-y-6">
        
        <!-- Section Title, Database Status Indicator & Step Counter -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">FORMULIR PENDAFTARAN</span>
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Selesaikan data Anda</h2>
                    <!-- Status Tersimpan di Database Indicator -->
                    <div id="dbSaveStatus" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs transition-all duration-300">
                        <i class="bi bi-cloud-check-fill text-emerald-500 text-sm" id="dbSaveStatusIcon"></i>
                        <span id="dbSaveStatusText">Draft tersimpan di database</span>
                    </div>
                </div>
            </div>
            <span class="text-xs font-bold tracking-wider text-orange-700 uppercase bg-orange-100/90 px-4 py-1.5 rounded-full border border-orange-300 badge-3d shrink-0 self-start sm:self-auto" id="stepCounterText">LANGKAH 1 / 3</span>
        </div>

        <!-- Horizontal Stepper Progress Bar (3 Steps) -->
        <div class="card-3d-warm rounded-2xl p-6 sm:p-7 mb-8 relative">
            <div class="relative px-6 sm:px-12 py-1">
                <div class="absolute top-[22px] left-12 right-12 h-[3px] bg-orange-200/60 -translate-y-1/2 z-0 rounded-full"></div>
                <div class="absolute top-[22px] left-12 right-12 h-[3px] -translate-y-1/2 z-0 overflow-hidden pointer-events-none rounded-full">
                    <div class="h-full bg-gradient-to-r from-orange-600 to-amber-500 transition-all duration-300 rounded-full shadow-xs" id="stepperProgressLine" style="width: 0%;"></div>
                </div>

                <div class="relative z-10 flex justify-between items-center">
                    <!-- Step 1 -->
                    <div class="step-item active flex flex-col items-center" id="step-item-1">
                        <div class="step-counter w-11 h-11 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold flex items-center justify-center text-sm box-3d ring-4 ring-orange-200/80 transition-all duration-300 z-10">1</div>
                        <span class="step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300">Jenis & Judul TA</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-item flex flex-col items-center" id="step-item-2">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">2</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Berkas Persyaratan</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="step-item flex flex-col items-center" id="step-item-3">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">3</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Konfirmasi & Kirim</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="p-4 mb-6 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold box-3d shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="text-xs font-semibold">
                    <?= $this->session->flashdata('success'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="p-4 mb-6 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold box-3d shrink-0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="text-xs font-semibold">
                    <?= $this->session->flashdata('error'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($has_revisi)): ?>
            <!-- Revision Notice Banner -->
            <div class="p-5 mb-6 rounded-2xl bg-rose-500/10 border-2 border-rose-400/80 text-rose-950 shadow-xs flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xl font-bold box-3d shrink-0">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div class="flex-grow">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800 block">STATUS: PERLU REVISI / PERBAIKAN BERKAS</span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed mt-1">
                        Pendaftaran Anda telah diajukan dan terdapat catatan perbaikan dari peninjau. Formulir pendaftaran awal ini berstatus <strong>hanya lihat</strong>. Untuk mengunggah ulang dokumen perbaikan, silakan gunakan tombol <strong>Perbaikan Berkas</strong> di Dashboard.
                    </p>
                    <div class="mt-3">
                        <a href="<?= site_url('mahasiswa'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                            <i class="bi bi-arrow-left-circle"></i> Menuju Dashboard untuk Perbaikan Berkas
                        </a>
                    </div>
                    <?php if(!empty($pendaftaran['catatan_wali'])): ?>
                        <div class="mt-2.5 p-3 bg-white/80 rounded-xl border border-rose-200 text-xs text-rose-900 font-medium">
                            <strong>Catatan Dosen Wali:</strong> <?= htmlspecialchars($pendaftaran['catatan_wali']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($pendaftaran['catatan_admin'])): ?>
                        <div class="mt-2 p-3 bg-white/80 rounded-xl border border-rose-200 text-xs text-rose-900 font-medium">
                            <strong>Catatan Admin Layanan (LAA):</strong> <?= htmlspecialchars($pendaftaran['catatan_admin']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($pendaftaran['catatan_koor'])): ?>
                        <div class="mt-2 p-3 bg-white/80 rounded-xl border border-rose-200 text-xs text-rose-900 font-medium">
                            <strong>Catatan Koordinator TA:</strong> <?= htmlspecialchars($pendaftaran['catatan_koor']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($pendaftaran['catatan_kk'])): ?>
                        <div class="mt-2 p-3 bg-white/80 rounded-xl border border-rose-200 text-xs text-rose-900 font-medium">
                            <strong>Catatan Ketua Kelompok Keahlian (KK):</strong> <?= htmlspecialchars($pendaftaran['catatan_kk']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($is_locked) && empty($has_revisi)): ?>
            <!-- Locked View-Only Notice Banner -->
            <div class="p-5 mb-6 rounded-2xl bg-amber-500/10 border-2 border-amber-400/80 text-amber-950 shadow-xs flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl font-bold box-3d shrink-0">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800 block">STATUS: FORMULIR TERKUNCI (SEDANG DITINJAU)</span>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed mt-1">
                        Pengajuan Tugas Akhir Anda saat ini sedang dalam proses peninjauan berjenjang. Kolom formulir berstatus <strong>hanya lihat (tidak dapat diedit)</strong>.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Card Container 3D -->
        <div class="card-3d-warm rounded-2xl mb-8 relative w-full max-w-full overflow-hidden">
            <form action="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" method="POST" enctype="multipart/form-data" id="formPendaftaranTA">
                <input type="hidden" name="is_submitted" id="inputIsSubmitted" value="0">
                <fieldset class="<?= !empty($is_locked) ? 'opacity-70 select-none' : ''; ?>" <?= !empty($is_locked) ? 'disabled' : ''; ?>>
                
                <div class="p-4 sm:p-8 lg:p-10 w-full max-w-full overflow-hidden">
                    <!-- STEP 1: Jenis & Judul TA -->
                    <div id="step-content-1" class="step-content space-y-6">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap lengkapi Jenis & Judul Utama TA!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                01
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Jenis & Judul Usulan TA</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Tentukan jenis tugas akhir yang sesuai dengan jalur akademik Anda dan masukkan usulan judul Bahasa Indonesia & Bahasa Inggris.
                        </p>

                        <!-- Panduan & Contoh Template Judul TA (Benar vs Salah) -->
                        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50/70 p-4 sm:p-5 transition-all shadow-xs">
                            <div class="flex items-center justify-between cursor-pointer select-none" onclick="toggleGuideTemplate('guideJudulTemplate', 'iconGuideJudul', 'labelGuideJudul')">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base font-bold shadow-xs box-3d shrink-0">
                                        <i class="bi bi-lightbulb-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-bold text-amber-950 flex flex-wrap items-center gap-2">
                                            Panduan &amp; Template Judul Tugas Akhir
                                            <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-200/90 text-amber-900 border border-amber-300">Tips Lolos Review</span>
                                        </h4>
                                        <p class="text-[11px] text-amber-800/90 font-medium mt-0.5">Lihat contoh penulisan judul yang benar agar cepat disetujui oleh Dosen Wali &amp; Ketua KK.</p>
                                    </div>
                                </div>
                                <button type="button" class="text-amber-800 text-xs font-bold flex items-center gap-1.5 hover:text-amber-950 transition shrink-0 bg-white/80 border border-amber-300 px-3 py-1.5 rounded-xl cursor-pointer shadow-2xs">
                                    <span class="text-[11px] hidden sm:inline" id="labelGuideJudul">Buka Panduan</span>
                                    <i class="bi bi-chevron-down text-xs transition-transform duration-200" id="iconGuideJudul"></i>
                                </button>
                            </div>

                            <!-- Collapsible Guide Content -->
                            <div id="guideJudulTemplate" class="hidden mt-4 pt-4 border-t border-amber-200 space-y-3.5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 text-xs">
                                    <!-- CONTOH BENAR -->
                                    <div class="p-4 rounded-xl bg-white border-2 border-emerald-400 shadow-xs space-y-3">
                                        <div class="flex items-center gap-2 text-emerald-800 font-black text-xs uppercase tracking-wider">
                                            <span class="w-5 h-5 rounded-md bg-emerald-500 text-white flex items-center justify-center text-xs">
                                                <i class="bi bi-check-lg"></i>
                                            </span>
                                            Contoh Judul yang BENAR (Disetujui)
                                        </div>
                                        <div class="space-y-2 text-[11px]">
                                            <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 space-y-1">
                                                <strong class="text-emerald-950 font-bold block leading-snug">"Klasifikasi Citra Medis MRI Otak untuk Diagnosis Tumor Menggunakan Convolutional Neural Network"</strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-emerald-700 font-bold">&check; Alasan:</span> Memiliki objek spesifik (MRI Otak), tujuan jelas (Diagnosis Tumor), dan metode eksplisit (CNN).
                                                </p>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 space-y-1">
                                                <strong class="text-emerald-950 font-bold block leading-snug">"Rancang Bangun Sistem Informasi Pemantauan Logistik Berbasis Internet of Things pada Gudang Distribusi"</strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-emerald-700 font-bold">&check; Alasan:</span> Skala sistem jelas, menggunakan Title Case (huruf kapital di awal kata), dan tata bahasa baku.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="p-2 rounded-lg bg-emerald-100/70 border border-emerald-200 text-[10px] text-emerald-900 font-medium">
                                            💡 <strong>Rumus Judul Ideal:</strong> [Tindakan / Metode] + [Objek Penelitian] + [Tujuan / Solusi] + [Studi Kasus / Lokasi jika ada]
                                        </div>
                                    </div>

                                    <!-- CONTOH SALAH -->
                                    <div class="p-4 rounded-xl bg-white border-2 border-rose-300 shadow-xs space-y-3">
                                        <div class="flex items-center gap-2 text-rose-800 font-black text-xs uppercase tracking-wider">
                                            <span class="w-5 h-5 rounded-md bg-rose-500 text-white flex items-center justify-center text-xs">
                                                <i class="bi bi-x-lg"></i>
                                            </span>
                                            Contoh Judul yang SALAH (Sering Ditolak)
                                        </div>
                                        <div class="space-y-2 text-[11px]">
                                            <div class="p-2.5 rounded-lg bg-rose-50/80 border border-rose-200 space-y-1">
                                                <strong class="text-rose-950 font-bold block leading-snug">"SISTEM INFORMASI KESEHATAN"</strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-rose-700 font-bold">&times; Kesalahan:</span> Huruf kapital semua (ALL CAPS), terlalu umum/singkat, tidak ada metode maupun batasan masalah.
                                                </p>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-rose-50/80 border border-rose-200 space-y-1">
                                                <strong class="text-rose-950 font-bold block leading-snug">"Bikin Aplikasi Kasir Toko Baju Online Pakai PHP &amp; MySQL"</strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-rose-700 font-bold">&times; Kesalahan:</span> Bahasa informal/slang ("Bikin", "Pakai"), topik setara tugas kuliah biasa (kurang bobot skripsi).
                                                </p>
                                            </div>
                                        </div>
                                        <div class="p-2 rounded-lg bg-rose-100/70 border border-rose-200 text-[10px] text-rose-900 font-medium">
                                            ⚠️ <strong>Hindari:</strong> Menulis judul kurang dari 5 kata, menggunakan singkatan tidak baku, atau tidak menyebutkan teknologi/metode yang diuji.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                            <!-- Jenis TA -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                    Jenis tugas akhir <span class="text-orange-500">*</span>
                                </label>

                                <?php
                                    $current_jenis_val = !empty(trim($pendaftaran['jenis_ta'] ?? '')) ? trim($pendaftaran['jenis_ta']) : '';
                                    $standard_jenis_opts = ['TA Reguler', 'TA Jurnal', 'TA HKI', 'TA PROYEK', 'Pengkaryaan', 'Penulisan'];
                                    if (!empty($current_jenis_val) && !in_array($current_jenis_val, $standard_jenis_opts)) {
                                        $standard_jenis_opts[] = $current_jenis_val;
                                    }
                                ?>
                                <!-- Custom 3D Glass Dropdown for Jenis TA -->
                                <div class="custom-dropdown relative w-full z-30" id="dropdownJenisTA">
                                    <input type="hidden" name="jenis_ta" id="inputJenisTA" value="<?= htmlspecialchars($current_jenis_val); ?>" required>

                                    <button type="button" class="dropdown-trigger w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 hover:border-orange-400 focus:ring-4 focus:ring-orange-500/10 outline-none text-slate-800 font-semibold text-xs flex items-center justify-between transition shadow-xs">
                                        <span class="trigger-label <?= !empty($current_jenis_val) ? 'text-slate-900 font-semibold' : 'text-slate-400 font-normal'; ?>">
                                            <?= htmlspecialchars(!empty($current_jenis_val) ? $current_jenis_val : '-- Pilih Jenis TA --'); ?>
                                        </span>
                                        <i class="bi bi-chevron-down text-orange-500 font-bold text-xs transition-transform duration-200 chevron-icon"></i>
                                    </button>

                                    <div class="dropdown-menu hidden absolute left-0 right-0 top-full mt-2 bg-white backdrop-blur-xl border border-orange-200/90 rounded-2xl p-2 shadow-2xl z-[100] space-y-1 max-h-60 overflow-y-auto">
                                        <?php foreach ($standard_jenis_opts as $s_opt): ?>
                                            <?php $is_sel = ($current_jenis_val === $s_opt); ?>
                                            <div class="dropdown-option <?= $is_sel ? 'bg-orange-100/80 text-orange-700 font-bold' : ''; ?> px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition flex items-center justify-between cursor-pointer" data-value="<?= htmlspecialchars($s_opt); ?>">
                                                <span><?= htmlspecialchars($s_opt); ?></span>
                                                <i class="bi bi-check-lg text-orange-600 font-bold text-sm <?= $is_sel ? '' : 'hidden'; ?> check-icon"></i>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Choice Badge Card -->
                            <div class="<?= empty($current_jenis_val) ? 'hidden' : ''; ?> p-4 rounded-xl bg-orange-100/60 border border-orange-200 flex items-center gap-3 transition-all duration-200" id="previewJenisTA">
                                <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-xs font-bold shrink-0 box-3d">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-orange-700 block">JENIS TUGAS AKHIR DIPILIH</span>
                                    <span class="text-xs font-bold text-slate-900" id="previewTextJenisTA"><?= htmlspecialchars($current_jenis_val); ?></span>
                                </div>
                            </div>

                            <!-- Judul Utama (Wajib) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Judul Usulan 1 (Utama) <span class="text-orange-500">*</span></label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium" id="inputJudul1" name="judul_1" value="<?= htmlspecialchars($pendaftaran['judul_1'] ?? ''); ?>" placeholder="Masukkan judul utama..." required>
                            </div>

                            <!-- Judul Alternatif 1 -->
                            <div id="containerJudul2" class="<?= empty($pendaftaran['judul_2']) ? 'hidden' : ''; ?> p-4 rounded-xl bg-orange-50/70 border border-orange-200/90 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Judul Usulan 2 (Alternatif 1)</label>
                                    <button type="button" class="btn-remove-alt text-xs font-semibold text-rose-500 hover:text-rose-700 flex items-center gap-1 hover:underline cursor-pointer" data-target="2">
                                        <i class="bi bi-trash3"></i> Hapus Alternatif 1
                                    </button>
                                </div>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium" id="inputJudul2" name="judul_2" value="<?= htmlspecialchars($pendaftaran['judul_2'] ?? ''); ?>" placeholder="Masukkan alternatif judul ke-2...">
                            </div>

                            <!-- Judul Alternatif 2 -->
                            <div id="containerJudul3" class="<?= empty($pendaftaran['judul_3']) ? 'hidden' : ''; ?> p-4 rounded-xl bg-orange-50/70 border border-orange-200/90 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Judul Usulan 3 (Alternatif 2)</label>
                                    <button type="button" class="btn-remove-alt text-xs font-semibold text-rose-500 hover:text-rose-700 flex items-center gap-1 hover:underline cursor-pointer" data-target="3">
                                        <i class="bi bi-trash3"></i> Hapus Alternatif 2
                                    </button>
                                </div>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium" id="inputJudul3" name="judul_3" value="<?= htmlspecialchars($pendaftaran['judul_3'] ?? ''); ?>" placeholder="Masukkan alternatif judul ke-3...">
                            </div>

                            <!-- Tombol Tambah Judul Alternatif -->
                            <div class="pt-0.5">
                                <button type="button" id="btnAddJudulAlt" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-orange-600 hover:text-orange-700 bg-orange-100/70 hover:bg-orange-200/80 border border-dashed border-orange-300 transition-all active:scale-95 shadow-2xs cursor-pointer">
                                    <i class="bi bi-plus-circle-fill text-sm"></i>
                                    <span>Tambah Judul Alternatif</span>
                                </button>
                            </div>

                            <!-- Judul dalam Bahasa Inggris -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Judul dalam Bahasa Inggris <span class="text-orange-500">*</span></label>
                                    <button type="button" id="btnAutoTranslate" class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 bg-orange-100/90 hover:bg-orange-200 px-3 py-1 rounded-lg border border-orange-300/80 transition-all flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95" title="Terjemahkan otomatis dari Judul Utama">
                                        <i class="bi bi-translate text-xs"></i>
                                        <span id="btnAutoTranslateText">Translate Otomatis</span>
                                    </button>
                                </div>
                                <div class="relative">
                                    <input type="text" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium pr-10" id="inputJudulEn" name="judul_en" value="<?= htmlspecialchars($pendaftaran['judul_en'] ?? ''); ?>" placeholder="Title in English..." required>
                                    <span id="translateSpinner" class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-orange-500 pointer-events-none">
                                        <i class="bi bi-arrow-repeat animate-spin text-base"></i>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1.5 font-normal flex items-center gap-1">
                                    <i class="bi bi-info-circle text-orange-500"></i> Klik <strong>Translate Otomatis</strong> untuk menerjemahkan Judul Utama (ID &rarr; EN), atau ketik langsung secara manual.
                                </p>
                            </div>

                            <!-- Konsentrasi -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Konsentrasi (Otomatis dari Biodata)</label>
                                <div class="relative">
                                    <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100/90 text-slate-800 font-semibold text-xs outline-none cursor-not-allowed pr-28" value="<?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?>" readonly>
                                    <input type="hidden" name="konsentrasi_dkv" value="<?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?>">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-300 px-2.5 py-1 rounded-lg flex items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Otomatis
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Unggah Berkas Persyaratan Dinamis (LAA System) -->
                    <div id="step-content-2" class="step-content space-y-6 hidden w-full max-w-full overflow-hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap unggah seluruh berkas wajib PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                02
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Unggah Berkas Persyaratan TA</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Unggah seluruh dokumen persyaratan pendaftaran Tugas Akhir yang disyaratkan oleh Layanan Akademik (Format PDF, Maksimal 5MB per berkas).
                        </p>

                        <!-- Panduan & Contoh Template Berkas TA (Benar vs Salah) -->
                        <div class="rounded-2xl border-2 border-indigo-200 bg-indigo-50/70 p-4 sm:p-5 transition-all shadow-xs">
                            <div class="flex items-center justify-between cursor-pointer select-none" onclick="toggleGuideTemplate('guideBerkasTemplate', 'iconGuideBerkas', 'labelGuideBerkas')">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-base font-bold shadow-xs box-3d shrink-0">
                                        <i class="bi bi-file-earmark-check-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-bold text-indigo-950 flex flex-wrap items-center gap-2">
                                            Panduan &amp; Template Dokumen Persyaratan
                                            <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-indigo-200/90 text-indigo-900 border border-indigo-300">Format &amp; Ketentuan</span>
                                        </h4>
                                        <p class="text-[11px] text-indigo-800/90 font-medium mt-0.5">Panduan dokumen yang lolos verifikasi Admin Layanan vs dokumen yang sering ditolak.</p>
                                    </div>
                                </div>
                                <button type="button" class="text-indigo-800 text-xs font-bold flex items-center gap-1.5 hover:text-indigo-950 transition shrink-0 bg-white/80 border border-indigo-300 px-3 py-1.5 rounded-xl cursor-pointer shadow-2xs">
                                    <span class="text-[11px] hidden sm:inline" id="labelGuideBerkas">Buka Panduan</span>
                                    <i class="bi bi-chevron-down text-xs transition-transform duration-200" id="iconGuideBerkas"></i>
                                </button>
                            </div>

                            <!-- Collapsible Guide Content -->
                            <div id="guideBerkasTemplate" class="hidden mt-4 pt-4 border-t border-indigo-200 space-y-3.5">
                                <?php 
                                    $active_sb_templates = array();
                                    $check_list_tmpl = !empty($syarat_berkas) ? $syarat_berkas : array();
                                    foreach ($check_list_tmpl as $sbt) {
                                        if (!empty($sbt['file_template'])) {
                                            $active_sb_templates[] = $sbt;
                                        }
                                    }
                                ?>
                                <?php if (!empty($active_sb_templates)): ?>
                                    <!-- Unduh File Template Resmi Dokumen (Dinamis Sesuai Syarat Berkas di Admin LAA) -->
                                    <div class="p-4 rounded-xl bg-white border-2 border-indigo-300 shadow-xs space-y-3">
                                        <div class="flex items-center justify-between gap-2 border-b border-indigo-100 pb-2.5">
                                            <div class="flex items-center gap-2 text-indigo-950 font-black text-xs uppercase tracking-wider">
                                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-2xs">
                                                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                                </span>
                                                <span>Unduh File Template Resmi Dokumen</span>
                                            </div>
                                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-full">
                                                <?= count($active_sb_templates); ?> Template Tersedia
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                            <?php foreach ($active_sb_templates as $tmpl): ?>
                                                <?php 
                                                    $tmpl_ext = strtolower(pathinfo($tmpl['file_template'], PATHINFO_EXTENSION));
                                                    $is_doc = in_array($tmpl_ext, array('doc', 'docx'));
                                                    $is_pdf = ($tmpl_ext === 'pdf');
                                                    $icon_class = $is_doc ? 'bi-file-earmark-word-fill text-blue-600' : ($is_pdf ? 'bi-file-earmark-pdf-fill text-rose-600' : 'bi-file-earmark-text-fill text-amber-600');
                                                ?>
                                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-indigo-300 hover:shadow-2xs transition group">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <i class="bi <?= $icon_class; ?> text-xl shrink-0"></i>
                                                        <div class="min-w-0">
                                                            <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-indigo-950" title="<?= htmlspecialchars($tmpl['nama_berkas']); ?>">
                                                                <?= htmlspecialchars($tmpl['nama_berkas']); ?>
                                                            </span>
                                                            <span class="text-[10px] text-slate-400 font-mono block truncate">
                                                                <?= htmlspecialchars($tmpl['file_template']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <a href="<?= base_url('uploads/templates/' . $tmpl['file_template']); ?>" download target="_blank"
                                                       class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-[11px] flex items-center gap-1.5 shrink-0 shadow-2xs transition">
                                                        <i class="bi bi-download text-[10px]"></i>
                                                        <span>Unduh</span>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 text-xs">
                                    <!-- CONTOH BENAR -->
                                    <div class="p-4 rounded-xl bg-white border-2 border-emerald-400 shadow-xs space-y-3">
                                        <div class="flex items-center gap-2 text-emerald-800 font-black text-xs uppercase tracking-wider">
                                            <span class="w-5 h-5 rounded-md bg-emerald-500 text-white flex items-center justify-center text-xs">
                                                <i class="bi bi-check-lg"></i>
                                            </span>
                                            Dokumen yang BENAR (Lolos Verifikasi)
                                        </div>
                                        <div class="space-y-2 text-[11px]">
                                            <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 space-y-1">
                                                <strong class="text-emerald-950 font-bold block flex items-center gap-1.5">
                                                    <i class="bi bi-file-earmark-pdf-fill text-emerald-600"></i> Format PDF Asli / Scan Resolusi Tinggi
                                                </strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-emerald-700 font-bold">&check; Ketentuan:</span> File berekstensi <code>.pdf</code> resmi, teks dapat dibaca jelas tanpa blur, orientasi tegak lurus, ukuran &le; 5 MB.
                                                </p>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 space-y-1">
                                                <strong class="text-emerald-950 font-bold block flex items-center gap-1.5">
                                                    <i class="bi bi-calendar-check-fill text-emerald-600"></i> KSM Semester Berjalan
                                                </strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-emerald-700 font-bold">&check; Ketentuan:</span> Mencantumkan semester aktif dan mata kuliah Tugas Akhir dengan stempel/tanda tangan digital i-Gracias.
                                                </p>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 space-y-1">
                                                <strong class="text-emerald-950 font-bold block flex items-center gap-1.5">
                                                    <i class="bi bi-pen-fill text-emerald-600"></i> Surat Pernyataan Lengkap &amp; Tertandatangani
                                                </strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-emerald-700 font-bold">&check; Ketentuan:</span> Bertanda tangan basah atau tersertifikasi, dan dibubuhi meterai jika disyaratkan.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CONTOH SALAH -->
                                    <div class="p-4 rounded-xl bg-white border-2 border-rose-300 shadow-xs space-y-3">
                                        <div class="flex items-center gap-2 text-rose-800 font-black text-xs uppercase tracking-wider">
                                            <span class="w-5 h-5 rounded-md bg-rose-500 text-white flex items-center justify-center text-xs">
                                                <i class="bi bi-x-lg"></i>
                                            </span>
                                            Kesalahan Dokumen (Pasti DITOLAK)
                                        </div>
                                        <div class="space-y-2 text-[11px]">
                                            <div class="p-2.5 rounded-lg bg-rose-50/80 border border-rose-200 space-y-1">
                                                <strong class="text-rose-950 font-bold block flex items-center gap-1.5">
                                                    <i class="bi bi-exclamation-octagon-fill text-rose-600"></i> Rename Manual Foto Menjadi .PDF
                                                </strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-rose-700 font-bold">&times; Kesalahan:</span> Mengubah nama file <code>foto.jpg</code> langsung ke <code>foto.pdf</code> (file corrupt dan gagal dibuka).
                                                </p>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-rose-50/80 border border-rose-200 space-y-1">
                                                <strong class="text-rose-950 font-bold block flex items-center gap-1.5">
                                                    <i class="bi bi-camera-fill text-rose-600"></i> Foto Kamera Gelap / Miring / Terpotong
                                                </strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-rose-700 font-bold">&times; Kesalahan:</span> Tulisan transkrip atau KSM tidak terbaca, ada bayangan jari, atau halaman terpotong.
                                                </p>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-rose-50/80 border border-rose-200 space-y-1">
                                                <strong class="text-rose-950 font-bold block flex items-center gap-1.5">
                                                    <i class="bi bi-clock-history text-rose-600"></i> Dokumen Kadaluarsa / KSM Semester Lama
                                                </strong>
                                                <p class="text-slate-600 text-[10px] leading-relaxed">
                                                    <span class="text-rose-700 font-bold">&times; Kesalahan:</span> Mengunggah KSM semester lalu yang bukan semester aktif pendaftaran.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Document List Loop -->
                        <div class="space-y-6 pt-2 w-full max-w-full overflow-hidden" id="dynamicDocContainer">
                            <?php 
                                $active_list = !empty($syarat_berkas) ? $syarat_berkas : array(
                                    array('kode_berkas' => 'ksm', 'nama_berkas' => 'KSM (Kartu Studi Mahasiswa)', 'deskripsi' => 'Bukti KRS semester aktif yang memuat mata kuliah Tugas Akhir.', 'is_required' => 1),
                                    array('kode_berkas' => 'transkrip', 'nama_berkas' => 'Transkrip Nilai Akademik Terakhir', 'deskripsi' => 'Transkrip nilai resmi yang sudah divalidasi.', 'is_required' => 1),
                                    array('kode_berkas' => 'pernyataan', 'nama_berkas' => 'Surat Pernyataan Mahasiswa', 'deskripsi' => 'Surat kesanggupan menyelesaikan TA bermaterai.', 'is_required' => 1),
                                    array('kode_berkas' => 'bebas_lab', 'nama_berkas' => 'Surat Bebas Lab & Perpustakaan', 'deskripsi' => 'Surat keterangan bebas pinjaman alat lab FIK.', 'is_required' => 1)
                                );
                                $b_idx = 1;
                            ?>

                            <?php foreach ($active_list as $sb): ?>
                                <?php 
                                    $kode = $sb['kode_berkas'];
                                    $req = !empty($sb['is_required']);
                                    $st_info = $student_berkas[$kode] ?? null;
                                    $old_filename = $st_info['file_name'] ?? ($pendaftaran['file_' . $kode] ?? '');
                                ?>
                                <div class="p-4 sm:p-5 rounded-2xl bg-white border border-orange-200/90 shadow-2xs space-y-3 doc-requirement-card w-full max-w-full overflow-hidden box-border" data-kode="<?= $kode; ?>" data-required="<?= $req ? '1' : '0'; ?>">
                                    <div class="flex items-start justify-between gap-3 w-full max-w-full overflow-hidden">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="font-bold text-sm text-slate-900 truncate max-w-full"><?= $b_idx; ?>. <?= htmlspecialchars($sb['nama_berkas']); ?></h4>
                                                <?php if($req): ?>
                                                    <span class="text-[9px] font-extrabold text-rose-700 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">Wajib</span>
                                                <?php else: ?>
                                                    <span class="text-[9px] font-extrabold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">Opsional</span>
                                                <?php endif; ?>
                                                <?php if(!empty($sb['file_template'])): ?>
                                                    <a href="<?= base_url('uploads/templates/' . $sb['file_template']); ?>" download target="_blank"
                                                       class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2.5 py-0.5 rounded-full transition shadow-2xs shrink-0" title="Unduh template resmi: <?= htmlspecialchars($sb['file_template']); ?>">
                                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                                        <span>Unduh Template</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 font-medium leading-normal"><?= htmlspecialchars(($sb['deskripsi'] ?? '') ?: 'Unggah berkas ' . ($sb['nama_berkas'] ?? '')); ?></p>
                                        </div>
                                    </div>

                                    <!-- Drop Zone Box -->
                                    <div class="drop-zone border-2 border-dashed border-orange-300 rounded-2xl p-4 sm:p-5 text-center bg-orange-50/40 hover:bg-orange-100/50 transition-all duration-300 cursor-pointer group relative w-full max-w-full overflow-hidden box-border">
                                        <input type="file" name="file_<?= $kode; ?>" class="hidden input-doc-file" accept=".pdf" data-kode="<?= $kode; ?>">
                                        <input type="hidden" name="file_<?= $kode; ?>_old" value="<?= htmlspecialchars($old_filename); ?>" class="input-doc-old">

                                        <!-- Unselected Prompt -->
                                        <div class="drop-zone-prompt <?= !empty($old_filename) ? 'hidden' : ''; ?>">
                                            <div class="w-11 h-11 bg-orange-500/10 text-orange-600 rounded-2xl flex items-center justify-center text-lg mx-auto mb-2 box-3d group-hover:scale-105 transition-transform">
                                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                            </div>
                                            <h5 class="font-bold text-slate-800 text-xs">Drag & Drop file PDF <?= htmlspecialchars($sb['nama_berkas']); ?> di sini</h5>
                                            <p class="text-[10px] text-slate-400 mt-0.5 font-normal">atau klik untuk memilih file PDF (Maksimal 5MB)</p>
                                        </div>

                                        <!-- Selected File Card Preview -->
                                        <div class="drop-zone-selected <?= empty($old_filename) ? 'hidden' : 'flex'; ?> flex-col sm:flex-row items-center justify-between gap-3 p-3 bg-white border border-emerald-300 rounded-xl shadow-xs transition-all duration-300 w-full max-w-full overflow-hidden box-border">
                                            <div class="flex items-center gap-3 text-left min-w-0 flex-1 w-full max-w-full overflow-hidden">
                                                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold shrink-0 box-3d">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="min-w-0 flex-1 max-w-full overflow-hidden">
                                                    <div class="flex items-center gap-2 min-w-0 max-w-full overflow-hidden">
                                                        <span class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300 shrink-0">FILE TERPILIH</span>
                                                        <h5 class="file-name font-bold text-xs text-slate-900 truncate max-w-[130px] sm:max-w-[200px] md:max-w-[280px] lg:max-w-[340px] block"><?= htmlspecialchars($old_filename ?: 'file.pdf'); ?></h5>
                                                    </div>
                                                    <p class="file-size text-[10px] text-slate-500 font-medium mt-0.5">
                                                        <?= (!empty($is_locked) || !empty($pendaftaran['is_submitted'])) ? 'Berkas Diajukan (Sedang Ditinjau Dosen Wali)' : 'Berkas Tersimpan (Siap Diperbarui Jika Perlu)'; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <?php if(empty($is_locked) && empty($pendaftaran['is_submitted'])): ?>
                                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                                        <i class="bi bi-arrow-repeat text-xs"></i> Ganti File
                                                    </button>
                                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                                        <i class="bi bi-trash3 text-xs"></i> Hapus
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $b_idx++; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- STEP 3: Ringkasan & Konfirmasi Pendaftaran -->
                    <div id="step-content-3" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap centang persetujuan konfirmasi!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                03
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Ringkasan & Konfirmasi Pendaftaran</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Tinjau ulang seluruh data dan berkas pendaftaran Tugas Akhir Anda sebelum dikirimkan ke Dosen Wali dan Layanan Akademik (LAA).
                        </p>

                        <!-- Summary Display Card -->
                        <div class="p-6 rounded-2xl bg-orange-50/80 border border-orange-200 space-y-5">
                            <div class="border-b border-orange-200/80 pb-4 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-600">JENIS TUGAS AKHIR</span>
                                    <h4 class="text-sm font-black text-slate-900 mt-0.5" id="summaryJenisTA"><?= htmlspecialchars($pendaftaran['jenis_ta'] ?? '-'); ?></h4>
                                </div>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-300 px-3 py-1 rounded-full">Siap Dikirim</span>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">JUDUL UTAMA (BAHASA INDONESIA)</span>
                                    <p class="text-xs font-bold text-slate-900 mt-0.5 leading-relaxed" id="summaryJudul1"><?= htmlspecialchars($pendaftaran['judul_1'] ?? '-'); ?></p>
                                </div>

                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">JUDUL BAHASA INGGRIS</span>
                                    <p class="text-xs font-semibold italic text-slate-700 mt-0.5 leading-relaxed" id="summaryJudulEn"><?= htmlspecialchars($pendaftaran['judul_en'] ?? '-'); ?></p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <div class="p-3 rounded-xl bg-white/80 border border-orange-100 shadow-2xs">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">JUDUL USULAN 2 (ALTERNATIF 1)</span>
                                        <p class="text-xs font-bold text-slate-900 mt-0.5 leading-relaxed" id="summaryJudul2"><?= htmlspecialchars(!empty($pendaftaran['judul_2']) ? $pendaftaran['judul_2'] : '- (Tidak diisi)'); ?></p>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white/80 border border-orange-100 shadow-2xs">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">JUDUL USULAN 3 (ALTERNATIF 2)</span>
                                        <p class="text-xs font-bold text-slate-900 mt-0.5 leading-relaxed" id="summaryJudul3"><?= htmlspecialchars(!empty($pendaftaran['judul_3']) ? $pendaftaran['judul_3'] : '- (Tidak diisi)'); ?></p>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">KONSENTRASI PROGRAM STUDI</span>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" id="summaryKonsentrasi"><?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?></p>
                                </div>
                            </div>

                            <!-- Document Status Summary List -->
                            <div class="pt-3 border-t border-orange-200/80">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-600 block mb-2">KELENGKAPAN BERKAS TERUNGGAH</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="summaryDocList">
                                    <!-- Populated dynamically via JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div class="p-4 rounded-xl bg-white border border-slate-200">
                            <label class="inline-flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" id="checkKonfirmasiSubmit" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 mt-0.5 cursor-pointer" required>
                                <span class="text-xs font-semibold text-slate-800 leading-relaxed">
                                    Saya menyatakan dengan sesungguhnya bahwa seluruh data dan dokumen persyaratan Tugas Akhir yang saya unggah adalah <strong>benar, lengkap, dan sah</strong>.
                                </span>
                            </label>
                        </div>

                        <!-- Inline Progress Bar Component (Show when submitting) -->
                        <div id="inlineSubmitProgress" class="hidden p-4 rounded-2xl bg-orange-50 border border-orange-200/90 space-y-2 mt-4 transition-all duration-300">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-orange-600 uppercase tracking-wider text-[10px] flex items-center gap-1.5" id="inlineProgressStatusText">
                                    <i class="bi bi-arrow-repeat animate-spin text-xs"></i> Mengirimkan Finalisasi TA...
                                </span>
                                <span class="text-slate-800 font-extrabold" id="inlineProgressPercent">0%</span>
                            </div>
                            <div class="w-full h-3 bg-orange-200/60 rounded-full overflow-hidden p-0.5 border border-orange-300">
                                <div id="inlineProgressBar" class="h-full bg-gradient-to-r from-orange-500 via-amber-500 to-emerald-500 rounded-full transition-all duration-300 shadow-xs" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                </fieldset>

                <!-- Footer Navigation -->
                <div class="px-6 sm:px-10 py-5 bg-orange-100/40 border-t border-orange-200/60 flex items-center justify-between">
                    <button type="button" class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 border border-slate-300 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-xs shadow-xs box-3d cursor-pointer" id="btnPrev" data-dashboard-url="<?= site_url('mahasiswa'); ?>">
                        <i class="bi bi-arrow-left text-base font-bold"></i> <span>Kembali</span>
                    </button>
                    
                    <div class="ml-auto flex items-center gap-3">
                        <button type="button" class="btn-3d-orange flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-bold text-xs cursor-pointer" id="btnNext">
                            <span>Lanjutkan</span> <i class="bi bi-arrow-right text-sm"></i>
                        </button>
                        <?php if(!empty($is_locked)): ?>
                            <button type="button" class="hidden flex items-center gap-2 bg-slate-200 border border-slate-300 text-slate-500 font-bold px-6 py-2.5 rounded-xl shadow-none transition text-xs select-none cursor-not-allowed" id="btnSubmit" disabled>
                                <i class="bi bi-lock-fill text-sm"></i> <?= !empty($has_revisi) ? 'Terkunci (Perbaikan di Dashboard)' : 'Formulir Terkunci (Sedang Ditinjau)'; ?>
                            </button>
                        <?php else: ?>
                            <button type="submit" class="hidden flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-xs box-3d cursor-pointer" id="btnSubmit">
                                <i class="bi bi-send-fill text-sm"></i> Kirim Pendaftaran
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

            </form>
        </div>



    </div>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>
    </div> <!-- /#mainPageContent -->

    <?php
        $has_saved_draft = !empty($has_ta) && (!empty($pendaftaran['jenis_ta']) || !empty($pendaftaran['judul_1']) || !empty($pendaftaran['file_ksm']) || !empty($student_berkas));
    ?>
    <script>
        window.CURRENT_USER_NIM = "<?= htmlspecialchars($mahasiswa['nim'] ?? ($this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: ''))); ?>";
        window.IS_LOCKED_SUBMISSION = <?= (!empty($is_locked) || !empty($pendaftaran['is_submitted'])) ? 'true' : 'false'; ?>;
        window.UPLOAD_AJAX_URL = "<?= site_url('mahasiswa/ajax_upload_file_ta'); ?>";
        window.DELETE_FILE_AJAX_URL = "<?= site_url('mahasiswa/ajax_delete_file_ta'); ?>";
        window.SAVE_DRAFT_AJAX_URL = "<?= site_url('mahasiswa/ajax_save_draft_ta'); ?>";
        window.SERVER_DRAFT_STEP = <?= (int)($server_draft_step ?? 1); ?>;
        window.SERVER_HAS_DRAFT = <?= $has_saved_draft ? 'true' : 'false'; ?>;

        function toggleGuideTemplate(contentId, iconId, labelId) {
            const el = document.getElementById(contentId);
            const icon = document.getElementById(iconId);
            const label = labelId ? document.getElementById(labelId) : null;
            if (!el) return;
            const isHidden = el.classList.toggle('hidden');
            if (icon) {
                icon.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
            }
            if (label) {
                label.textContent = isHidden ? 'Buka Panduan' : 'Tutup Panduan';
            }
        }
    </script>
    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script src="<?= base_url('assets/js/pendaftaran_ta_stepper.js'); ?>?v=<?= time(); ?>"></script>
    <!-- High-Visibility Progress Bar Modal Overlay (Finalisasi Penambahan TA) -->
    <div id="submitProgressModal" class="fixed inset-0 z-[10000] bg-slate-900/75 backdrop-blur-md hidden flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-orange-100 text-center space-y-6 relative overflow-hidden">
            <!-- Background Glow Accents -->
            <div class="absolute -top-10 -right-10 w-36 h-36 bg-orange-400/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-36 h-36 bg-emerald-400/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center text-3xl mx-auto shadow-lg shadow-orange-500/30 box-3d">
                <i class="bi bi-cloud-arrow-up-fill animate-bounce"></i>
            </div>

            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-orange-600 bg-orange-100 px-3 py-1 rounded-full border border-orange-200">FINALISASI TUGAS AKHIR</span>
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight mt-2.5">Mengirimkan Berkas &amp; Pendaftaran</h3>
                <p class="text-xs text-slate-500 mt-1.5 font-medium leading-relaxed">
                    Mohon tunggu, berkas dan data pendaftaran Tugas Akhir sedang diunggah dan diproses oleh sistem.
                </p>
            </div>

            <!-- Progress Bar Track & Percent -->
            <div class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-orange-600 uppercase tracking-wider text-[10px] flex items-center gap-1.5" id="submitProgressStatusText">
                        <i class="bi bi-arrow-repeat animate-spin text-xs"></i> Memulai Pengiriman...
                    </span>
                    <span class="text-slate-800 font-extrabold" id="submitProgressPercent">0%</span>
                </div>
                <div class="w-full h-3.5 bg-slate-200/80 rounded-full overflow-hidden p-0.5 border border-slate-300/60 shadow-inner">
                    <div id="submitProgressBar" class="h-full bg-gradient-to-r from-orange-500 via-amber-500 to-emerald-500 rounded-full transition-all duration-300 shadow-xs" style="width: 0%;"></div>
                </div>
            </div>

            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-[11px] text-rose-800 font-semibold flex items-center justify-center gap-2">
                <i class="bi bi-shield-lock-fill text-rose-600 text-sm"></i>
                <span>Tombol terkunci agar berkas tidak terkirim ganda.</span>
            </div>
        </div>
    </div>

    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>

