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

    <!-- Header Glass Navbar (Clean White Glass - Identical to Dashboard) -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs mb-6">
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
                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link flex items-center gap-2 tracking-wide">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link active-link flex items-center gap-2 tracking-wide">
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

    <!-- Main Container (Centered Form Wizard Layout) -->
    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow space-y-6">
        
        <!-- Section Title & Step Counter -->
        <div class="flex items-end justify-between mb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">FORMULIR PENDAFTARAN</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Selesaikan data Anda</h2>
            </div>
            <span class="text-xs font-bold tracking-wider text-orange-700 uppercase bg-orange-100/90 px-4 py-1.5 rounded-full border border-orange-300 badge-3d" id="stepCounterText">LANGKAH 1 / 6</span>
        </div>

        <!-- Horizontal Stepper Progress Bar -->
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
                        <span class="step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300">Jenis TA</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-item flex flex-col items-center" id="step-item-2">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">2</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Judul</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="step-item flex flex-col items-center" id="step-item-3">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">3</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">KSM</span>
                    </div>

                    <!-- Step 4 -->
                    <div class="step-item flex flex-col items-center" id="step-item-4">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">4</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Transkrip</span>
                    </div>

                    <!-- Step 5 -->
                    <div class="step-item flex flex-col items-center" id="step-item-5">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">5</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Pernyataan</span>
                    </div>

                    <!-- Step 6 -->
                    <div class="step-item flex flex-col items-center" id="step-item-6">
                        <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">6</div>
                        <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Bebas Lab</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!empty($has_revisi)): ?>
            <!-- Revision Notice Banner -->
            <div class="p-5 mb-6 rounded-2xl bg-rose-500/10 border-2 border-rose-400/80 text-rose-950 shadow-xs flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xl font-bold box-3d shrink-0">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div class="flex-grow">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800 block">STATUS: PERLU REVISI / PERBAIKAN BERKAS</span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed mt-1">
                        Terdapat catatan perbaikan dari peninjau. Formulir telah diaktifkan kembali sehingga Anda dapat memperbarui data atau mengunggah ulang dokumen PDF yang diminta.
                    </p>
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
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($is_locked)): ?>
            <!-- Locked View-Only Notice Banner -->
            <div class="p-5 mb-6 rounded-2xl bg-amber-500/10 border-2 border-amber-400/80 text-amber-950 shadow-xs flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl font-bold box-3d shrink-0">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800 block">STATUS: FORMULIR TERKUNCI (SEDANG DITINJAU)</span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed mt-1">
                        Pengajuan Tugas Akhir Anda saat ini sedang dalam proses peninjauan berjenjang. Kolom formulir berstatus <strong>hanya lihat (tidak dapat diedit)</strong>. Anda dapat menelusuri tiap langkah untuk memeriksa berkas yang telah dikirim. Kolom formulir akan otomatis aktif kembali jika terdapat catatan revisi.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Card Container 3D -->
        <div class="card-3d-warm rounded-2xl mb-8 relative">
            <form action="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" method="POST" enctype="multipart/form-data" id="formPendaftaranTA">
                <fieldset class="<?= !empty($is_locked) ? 'opacity-70 select-none' : ''; ?>" <?= !empty($is_locked) ? 'disabled' : ''; ?>>
                
                <div class="p-6 sm:p-10">
                    <!-- STEP 1 -->
                    <div id="step-content-1" class="step-content space-y-6">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap lengkapi data!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                01
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Jenis TA</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Tentukan jenis tugas akhir yang sesuai dengan jalur akademik Anda.
                        </p>

                        <div class="space-y-4 pt-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                    Jenis tugas akhir <span class="text-orange-500">*</span>
                                </label>

                                <!-- Custom 3D Glass Dropdown for Jenis TA -->
                                <div class="custom-dropdown relative w-full z-30" id="dropdownJenisTA">
                                    <input type="hidden" name="jenis_ta" id="inputJenisTA" value="<?= htmlspecialchars($pendaftaran['jenis_ta'] ?? ''); ?>" required>
                                    <input type="hidden" name="file_ksm_old" value="<?= htmlspecialchars($pendaftaran['file_ksm'] ?? ''); ?>">
                                    <input type="hidden" name="file_transkrip_old" value="<?= htmlspecialchars($pendaftaran['file_transkrip'] ?? ''); ?>">
                                    <input type="hidden" name="file_pernyataan_old" value="<?= htmlspecialchars($pendaftaran['file_pernyataan'] ?? ''); ?>">
                                    <input type="hidden" name="file_bebas_lab_old" value="<?= htmlspecialchars($pendaftaran['file_bebas_lab'] ?? ''); ?>">

                                    <button type="button" class="dropdown-trigger w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 hover:border-orange-400 focus:ring-4 focus:ring-orange-500/10 outline-none text-slate-800 font-semibold text-xs flex items-center justify-between transition shadow-xs">
                                        <span class="trigger-label text-slate-400 font-normal">-- Pilih Jenis TA --</span>
                                        <i class="bi bi-chevron-down text-orange-500 font-bold text-xs transition-transform duration-200 chevron-icon"></i>
                                    </button>

                                    <div class="dropdown-menu hidden absolute left-0 right-0 top-full mt-2 bg-white backdrop-blur-xl border border-orange-200/90 rounded-2xl p-2 shadow-2xl z-[100] space-y-1">
                                        <div class="dropdown-option px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition flex items-center justify-between cursor-pointer" data-value="Proyek Akhir">
                                            <span>Proyek Akhir</span>
                                            <i class="bi bi-check-lg text-orange-600 font-bold text-sm hidden check-icon"></i>
                                        </div>
                                        <div class="dropdown-option px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition flex items-center justify-between cursor-pointer" data-value="Tugas Akhir Reguler">
                                            <span>Tugas Akhir Reguler</span>
                                            <i class="bi bi-check-lg text-orange-600 font-bold text-sm hidden check-icon"></i>
                                        </div>
                                        <div class="dropdown-option px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition flex items-center justify-between cursor-pointer" data-value="Tugas Akhir jalur Magang (MBKM)">
                                            <span>Tugas Akhir jalur Magang (MBKM)</span>
                                            <i class="bi bi-check-lg text-orange-600 font-bold text-sm hidden check-icon"></i>
                                        </div>
                                        <div class="dropdown-option px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition flex items-center justify-between cursor-pointer" data-value="Tugas Akhir jalur Prestasi / Lomba">
                                            <span>Tugas Akhir jalur Prestasi / Lomba</span>
                                            <i class="bi bi-check-lg text-orange-600 font-bold text-sm hidden check-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Choice Badge Card -->
                            <div class="hidden p-4 rounded-xl bg-orange-100/60 border border-orange-200 flex items-center gap-3 transition-all duration-200" id="previewJenisTA">
                                <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-xs font-bold shrink-0 box-3d">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-orange-700 block">JENIS TUGAS AKHIR DIPILIH</span>
                                    <span class="text-xs font-bold text-slate-900" id="previewTextJenisTA">Proyek Akhir</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div id="step-content-2" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap lengkapi usulan judul!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                02
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Judul Usulan</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Masukkan 3 usulan alternatif judul Bahasa Indonesia, Bahasa Inggris, dan pilihan konsentrasi.
                        </p>

                        <div class="space-y-4">
                            <!-- Judul Utama (Wajib) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Judul Usulan 1 (Utama) <span class="text-orange-500">*</span></label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium" id="inputJudul1" name="judul_1" value="<?= htmlspecialchars($pendaftaran['judul_1'] ?? ''); ?>" placeholder="Masukkan judul utama..." required>
                            </div>

                            <!-- Judul Alternatif 1 (Dinamis) -->
                            <div id="containerJudul2" class="<?= empty($pendaftaran['judul_2']) ? 'hidden' : ''; ?> p-4 rounded-xl bg-orange-50/70 border border-orange-200/90 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Judul Usulan 2 (Alternatif 1)</label>
                                    <button type="button" class="btn-remove-alt text-xs font-semibold text-rose-500 hover:text-rose-700 flex items-center gap-1 hover:underline cursor-pointer" data-target="2">
                                        <i class="bi bi-trash3"></i> Hapus Alternatif 1
                                    </button>
                                </div>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white/90 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium" id="inputJudul2" name="judul_2" value="<?= htmlspecialchars($pendaftaran['judul_2'] ?? ''); ?>" placeholder="Masukkan alternatif judul ke-2...">
                            </div>

                            <!-- Judul Alternatif 2 (Dinamis) -->
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

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Konsentrasi (Otomatis dari Biodata)</label>
                                <div class="relative">
                                    <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100/90 text-slate-800 font-semibold text-xs outline-none cursor-not-allowed pr-28" value="<?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?>" readonly>
                                    <input type="hidden" name="konsentrasi_dkv" value="<?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?>">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-300 px-2.5 py-1 rounded-lg flex items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Otomatis
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1.5 font-normal">Diambil secara otomatis sesuai konsentrasi program studi dari profil biodata Anda.</p>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div id="step-content-3" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap unggah berkas KSM PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                03
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Unggah KSM</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Unggah berkas Kartu Studi Mahasiswa (KSM) terkini berformat PDF.
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300 rounded-2xl p-8 text-center bg-orange-50/40 hover:bg-orange-100/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_ksm" class="hidden" accept=".pdf">
                            <input type="hidden" name="file_ksm_old" id="file_ksm_old" value="<?= htmlspecialchars($pendaftaran['file_ksm'] ?? ''); ?>">

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-14 h-14 bg-orange-500/10 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 box-3d group-hover:scale-105 transition-transform">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-xs">Drag & Drop file PDF KSM di sini</h3>
                                <p class="text-[11px] text-slate-400 mt-1 font-normal">atau klik untuk memilih file dari komputer (Hanya .PDF)</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300 rounded-xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-semibold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-semibold text-xs text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-[11px] text-slate-500 font-normal mt-0.5">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4 -->
                    <div id="step-content-4" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap unggah berkas Transkrip Nilai PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                04
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Transkrip Nilai</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Unggah berkas Transkrip Nilai terakhir berformat PDF.
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300 rounded-2xl p-8 text-center bg-orange-50/40 hover:bg-orange-100/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_transkrip" class="hidden" accept=".pdf">
                            <input type="hidden" name="file_transkrip_old" id="file_transkrip_old" value="<?= htmlspecialchars($pendaftaran['file_transkrip'] ?? ''); ?>">

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-14 h-14 bg-rose-500/10 text-rose-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 box-3d group-hover:scale-105 transition-transform">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-xs">Drag & Drop file PDF Transkrip di sini</h3>
                                <p class="text-[11px] text-slate-400 mt-1 font-normal">Hanya file berformat PDF</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300 rounded-xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-semibold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-semibold text-xs text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-[11px] text-slate-500 font-normal mt-0.5">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5 -->
                    <div id="step-content-5" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap unggah berkas Surat Pernyataan PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                05
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Surat Pernyataan</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Unggah berkas Surat Pernyataan Keaslian & Orisinalitas Judul (PDF).
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300 rounded-2xl p-8 text-center bg-orange-50/40 hover:bg-orange-100/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_pernyataan" class="hidden" accept=".pdf">
                            <input type="hidden" name="file_pernyataan_old" id="file_pernyataan_old" value="<?= htmlspecialchars($pendaftaran['file_pernyataan'] ?? ''); ?>">

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-14 h-14 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 box-3d group-hover:scale-105 transition-transform">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-xs">Drag & Drop file PDF Surat Pernyataan di sini</h3>
                                <p class="text-[11px] text-slate-400 mt-1 font-normal">Hanya file berformat PDF</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300 rounded-xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-semibold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-semibold text-xs text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-[11px] text-slate-500 font-normal mt-0.5">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 6 -->
                    <div id="step-content-6" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-semibold">Harap unggah berkas Surat Bebas Lab PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                06
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">TA REGISTRATION</span>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Bebas Lab</h3>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                            Unggah berkas Surat Bebas Tanggungan Laboratorium (PDF).
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300 rounded-2xl p-8 text-center bg-orange-50/40 hover:bg-orange-100/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_bebas_lab" class="hidden" accept=".pdf">
                            <input type="hidden" name="file_bebas_lab_old" id="file_bebas_lab_old" value="<?= htmlspecialchars($pendaftaran['file_bebas_lab'] ?? ''); ?>">

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-14 h-14 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 box-3d group-hover:scale-105 transition-transform">
                                    <i class="bi bi-journal-check"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-xs">Drag & Drop file PDF Surat Bebas Lab di sini</h3>
                                <p class="text-[11px] text-slate-400 mt-1 font-normal">Hanya file berformat PDF</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300 rounded-xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-semibold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-semibold text-xs text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-[11px] text-slate-500 font-normal mt-0.5">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </fieldset>

                <!-- Footer Navigation -->
                <div class="px-6 sm:px-10 py-5 bg-orange-100/40 border-t border-orange-200/60 flex items-center justify-between">
                    <button type="button" class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 border border-slate-300 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-xs shadow-xs box-3d cursor-pointer" id="btnPrev">
                        <i class="bi bi-arrow-left text-base font-bold"></i> <span>Kembali ke Dashboard</span>
                    </button>
                    
                    <div class="ml-auto flex gap-3">
                        <button type="button" class="btn-3d-orange flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-bold text-xs" id="btnNext">
                            <span>Lanjutkan</span> <i class="bi bi-arrow-right text-sm"></i>
                        </button>
                        <?php if(!empty($is_locked)): ?>
                            <button type="button" class="hidden flex items-center gap-2 bg-slate-200 border border-slate-300 text-slate-500 font-bold px-6 py-2.5 rounded-xl shadow-none transition text-xs select-none cursor-not-allowed" id="btnSubmit" disabled>
                                <i class="bi bi-lock-fill text-sm"></i> Formulir Terkunci (Sedang Ditinjau)
                            </button>
                        <?php else: ?>
                            <button type="submit" class="hidden flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-xs box-3d" id="btnSubmit">
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

    <script>
        window.CURRENT_USER_NIM = "<?= htmlspecialchars($mahasiswa['nim'] ?? ($this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: ''))); ?>";
        window.UPLOAD_AJAX_URL = "<?= site_url('mahasiswa/ajax_upload_file_ta'); ?>";
    </script>
    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>?v=<?= time(); ?>"></script>
    <script src="<?= base_url('assets/js/pendaftaran_ta_stepper.js'); ?>?v=<?= time(); ?>"></script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>

