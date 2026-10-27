<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col flex-grow">

        <!-- Header Navbar -->
        <?php $this->load->view('partials/mahasiswa_navbar', ['mahasiswa' => $mahasiswa]); ?>

        <?php
            $nim_mhs   = $mhs_db['nim'] ?? '';
            $nama_mhs  = $mhs_db['nama'] ?? '';
            $prodi_mhs = !empty($kp['peminatan']) ? $kp['peminatan'] : ($mhs_db['prodi'] ?? '');

            $is_form_locked = !empty($wf['syarat_all_approved']) || !empty($wf['has_pembimbing1']) || !empty($wf['is_selesai']);
            $cur_status_kp  = $kp['status_file'] ?? '';
            $is_submitted_kp = in_array($cur_status_kp, [
                'Sudah Upload Persyaratan', 'Disetujui LAA', 'Revisi Persyaratan',
                'Sudah Plot Pembimbing', 'Sudah Upload Laporan', 'Revisi Laporan', 'Selesai'
            ]);
            $syarat_list    = $syarat_kp ?? [];
            $files_map      = $files_kp ?? [];
            $initial_step   = (int)($this->input->get('step') ?: 1);
            if ($initial_step < 1 || $initial_step > 3) {
                $initial_step = 1;
            }
        ?>

        <!-- Main Container (Centered Form Wizard Layout - Identical to Pendaftaran TA) -->
        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow space-y-6">

            <!-- Section Title & Step Counter -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-2">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">FORMULIR PENGAJUAN KERJA PRAKTIK</span>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Pengajuan Kerja Praktik (KP)</h2>
                        <div id="dbSaveStatusKp" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $is_submitted_kp ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/90'; ?> shadow-2xs transition-all duration-300">
                            <i class="bi <?= $is_submitted_kp ? 'bi-send-check-fill text-indigo-600' : 'bi-cloud-check-fill text-emerald-500'; ?> text-sm" id="dbSaveStatusKpIcon"></i>
                            <span id="dbSaveStatusKpText">
                                <?= $is_submitted_kp ? 'Telah dikirim ke Admin LAA' : (!empty($kp) ? 'Draft tersimpan di database' : 'Siap menyimpan draft otomatis'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <span class="text-xs font-bold tracking-wider text-orange-700 uppercase bg-orange-100/90 px-4 py-1.5 rounded-full border border-orange-300 badge-3d shrink-0 self-start sm:self-auto" id="stepCounterText">LANGKAH 1 / 3</span>
            </div>

            <!-- Horizontal Stepper Progress Bar (3 Steps - Seperti Pendaftaran TA) -->
            <div class="card-3d-warm rounded-2xl p-6 sm:p-7 mb-6 relative">
                <div class="relative px-6 sm:px-12 py-1">
                    <div class="absolute top-[22px] left-12 right-12 h-[3px] bg-orange-200/60 -translate-y-1/2 z-0 rounded-full"></div>
                    <div class="absolute top-[22px] left-12 right-12 h-[3px] -translate-y-1/2 z-0 overflow-hidden pointer-events-none rounded-full">
                        <div class="h-full bg-gradient-to-r from-orange-600 to-amber-500 transition-all duration-300 rounded-full shadow-xs" id="stepperProgressLine" style="width: 0%;"></div>
                    </div>

                    <div class="relative z-10 flex justify-between items-center">
                        <!-- Step 1 -->
                        <div class="step-item active flex flex-col items-center cursor-pointer" id="step-item-1" onclick="goToStep(1)">
                            <div class="step-counter w-11 h-11 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold flex items-center justify-center text-sm box-3d ring-4 ring-orange-200/80 transition-all duration-300 z-10">1</div>
                            <span class="step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300">Form Pengajuan KP</span>
                        </div>

                        <!-- Step 2 -->
                        <div class="step-item flex flex-col items-center cursor-pointer" id="step-item-2" onclick="goToStep(2)">
                            <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">2</div>
                            <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Berkas Persyaratan</span>
                        </div>

                        <!-- Step 3 -->
                        <div class="step-item flex flex-col items-center cursor-pointer" id="step-item-3" onclick="goToStep(3)">
                            <div class="step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10">3</div>
                            <span class="step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300">Konfirmasi &amp; Kirim</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 shadow-xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shrink-0">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="text-xs font-semibold"><?= $this->session->flashdata('success'); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 shadow-xs flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="text-xs font-semibold"><?= $this->session->flashdata('error'); ?></div>
                </div>
            <?php endif; ?>

            <!-- Main Card Container 3D -->
            <div class="card-3d-warm rounded-2xl mb-8 relative w-full max-w-full overflow-hidden">
                <form action="<?= site_url('mahasiswa/pengajuan_kp'); ?>" method="POST" enctype="multipart/form-data" id="formPengajuanKP">
                    <fieldset class="<?= $is_form_locked ? 'opacity-80' : ''; ?>" <?= $is_form_locked ? 'disabled' : ''; ?>>

                        <div class="p-5 sm:p-8 lg:p-10 w-full max-w-full overflow-hidden">

                            <!-- =======================================================
                                 STEP 1: FORMULIR PENGAJUAN KP (BIODATA, TEMPAT KP, PEMBIMBING LAPANGAN)
                                 ======================================================= -->
                            <div id="step-content-1" class="step-content space-y-6">
                                <div id="step1Alert" class="hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                                    <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                                    <span id="step1AlertText" class="text-xs font-semibold">Harap lengkapi seluruh kolom wajib bertanda bintang (*)!</span>
                                </div>

                                <div class="flex items-center gap-4 mb-2">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                        01
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">KP REGISTRATION</span>
                                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Biodata Mahasiswa &amp; Tempat Kerja Praktik</h3>
                                    </div>
                                </div>

                                <!-- Sub-Bagian A: Biodata & Akademik Mahasiswa -->
                                <div class="pt-2">
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3 flex items-center gap-2">
                                        <i class="bi bi-person-vcard-fill"></i> A. Biodata &amp; Akademik Mahasiswa
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">NIM Mahasiswa</label>
                                            <input type="text" id="inputNim" value="<?= htmlspecialchars($nim_mhs); ?>" readonly
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600 cursor-not-allowed">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                                            <input type="text" id="inputNamaMhs" value="<?= htmlspecialchars($nama_mhs); ?>" readonly
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600 cursor-not-allowed">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Program Studi / Peminatan <span class="text-rose-500">*</span></label>
                                            <input type="text" name="peminatan" id="inputPeminatan" value="<?= htmlspecialchars($prodi_mhs); ?>" required
                                                   placeholder="Program Studi / Peminatan"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">SKS Lulus <span class="text-rose-500">*</span></label>
                                            <input type="number" name="sks_lulus" id="inputSksLulus" min="0" value="<?= htmlspecialchars((string)($kp['sks_lulus'] ?? '')); ?>" required
                                                   placeholder="Contoh: 110"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">SKS Berjalan <span class="text-rose-500">*</span></label>
                                            <input type="number" name="sks_berjalan" id="inputSksBerjalan" min="0" value="<?= htmlspecialchars((string)($kp['sks_berjalan'] ?? '')); ?>" required
                                                   placeholder="Contoh: 128"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis KP <span class="text-rose-500">*</span></label>
                                            <input type="text" name="jenis_kp" id="inputJenisKp" value="<?= htmlspecialchars($kp['jenis_kp'] ?? ''); ?>" required
                                                   placeholder="Contoh: Reguler / Magang Industri"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-orange-200/60">

                                <!-- Sub-Bagian B: Biodata & Alamat Tempat KP (Perusahaan) -->
                                <div>
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3 flex items-center gap-2">
                                        <i class="bi bi-building-fill"></i> B. Biodata &amp; Alamat Tempat KP (Perusahaan)
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Instansi Tempat KP <span class="text-rose-500">*</span></label>
                                            <input type="text" name="nama_perusahaan" id="inputNamaPerusahaan" value="<?= htmlspecialchars($kp['nama_perusahaan'] ?? ''); ?>" required
                                                   placeholder="Masukkan nama resmi perusahaan atau instansi tempat KP"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap Tempat KP <span class="text-rose-500">*</span></label>
                                            <textarea name="alamat_perusahaan" id="inputAlamatPerusahaan" rows="2" required
                                                      placeholder="Masukkan alamat lengkap tempat pelaksanaan Kerja Praktik"
                                                      class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none"><?= htmlspecialchars($kp['alamat_perusahaan'] ?? ''); ?></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                                            <input type="text" name="kota" id="inputKota" value="<?= htmlspecialchars($kp['kota'] ?? ''); ?>" required
                                                   placeholder="Kota lokasi perusahaan"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Pos <span class="text-rose-500">*</span></label>
                                            <input type="text" name="kode_pos" id="inputKodePos" value="<?= htmlspecialchars($kp['kode_pos'] ?? ''); ?>" required
                                                   placeholder="Kode Pos"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Telepon / Fax Perusahaan <span class="text-rose-500">*</span></label>
                                            <input type="text" name="telp_fax" id="inputTelpFax" value="<?= htmlspecialchars($kp['telp_fax'] ?? ''); ?>" required
                                                   placeholder="Nomor telepon / fax perusahaan"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Perusahaan <span class="text-rose-500">*</span></label>
                                            <input type="email" name="email_perusahaan" id="inputEmailPerusahaan" value="<?= htmlspecialchars($kp['email_perusahaan'] ?? ''); ?>" required
                                                   placeholder="email@perusahaan.com"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-orange-200/60">

                                <!-- Sub-Bagian C: Pembimbing Lapangan & Posisi KP -->
                                <div>
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3 flex items-center gap-2">
                                        <i class="bi bi-person-badge-fill"></i> C. Pembimbing Lapangan &amp; Posisi KP
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Posisi / Divisi Pekerjaan KP <span class="text-rose-500">*</span></label>
                                            <input type="text" name="posisi_pekerjaan" id="inputPosisiPekerjaan" value="<?= htmlspecialchars($kp['posisi_pekerjaan'] ?? ''); ?>" required
                                                   placeholder="Posisi atau divisi penempatan KP"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pembimbing Lapangan / Tujuan Surat <span class="text-rose-500">*</span></label>
                                            <input type="text" name="nama_tujuan" id="inputNamaTujuan" value="<?= htmlspecialchars($kp['nama_tujuan'] ?? ''); ?>" required
                                                   placeholder="Nama Pembimbing Lapangan / penerima surat"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Pembimbing Lapangan <span class="text-rose-500">*</span></label>
                                            <input type="text" name="jabatan" id="inputJabatan" value="<?= htmlspecialchars($kp['jabatan'] ?? ''); ?>" required
                                                   placeholder="Jabatan Pembimbing Lapangan di perusahaan"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Kontak / No. HP Pembimbing Lapangan (CP) <span class="text-rose-500">*</span></label>
                                            <input type="text" name="cp" id="inputCp" value="<?= htmlspecialchars($kp['cp'] ?? ''); ?>" required
                                                   placeholder="Nomor WhatsApp / HP Pembimbing Lapangan"
                                                   class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-orange-400 outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- =======================================================
                                 STEP 2: UNGGAH BERKAS PERSYARATAN KP DINAMIS (DARI SUPER ADMIN)
                                 ======================================================= -->
                            <div id="step-content-2" class="step-content space-y-6 hidden w-full max-w-full overflow-hidden">
                                <div id="step2Alert" class="hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center gap-3">
                                    <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                                    <span id="step2AlertText" class="text-xs font-semibold">Harap unggah seluruh berkas persyaratan wajib (PDF)!</span>
                                </div>

                                <div class="flex items-center gap-4 mb-2">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                        02
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">KP REGISTRATION</span>
                                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Unggah Berkas Persyaratan KP</h3>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                    Unggah dokumen persyaratan pendaftaran Kerja Praktik sesuai daftar dinamis yang diatur pada Super Admin (Format PDF, Maksimal 5MB per berkas).
                                </p>

                                <?php
                                    $active_kp_templates = [];
                                    foreach ($syarat_list as $sbt) {
                                        if (!empty($sbt['file_template'])) {
                                            $active_kp_templates[] = $sbt;
                                        }
                                    }
                                ?>
                                <?php if (!empty($active_kp_templates)): ?>
                                    <!-- Unduh File Template Resmi Dokumen KP (Dinamis Sesuai Syarat Berkas KP di Super Admin) -->
                                    <div class="p-4 sm:p-5 rounded-2xl bg-indigo-50/70 border-2 border-indigo-200 shadow-xs space-y-3">
                                        <div class="flex items-center justify-between gap-2 border-b border-indigo-200/80 pb-2.5">
                                            <div class="flex items-center gap-2 text-indigo-950 font-black text-xs uppercase tracking-wider">
                                                <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-2xs">
                                                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                                </span>
                                                <span>Unduh File Template Resmi Dokumen KP</span>
                                            </div>
                                            <span class="text-[10px] font-bold text-indigo-700 bg-white border border-indigo-200 px-2.5 py-0.5 rounded-full">
                                                <?= count($active_kp_templates); ?> Template Tersedia
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                            <?php foreach ($active_kp_templates as $tmpl): ?>
                                                <?php
                                                    $tmpl_ext = strtolower(pathinfo($tmpl['file_template'], PATHINFO_EXTENSION));
                                                    $is_doc = in_array($tmpl_ext, ['doc', 'docx']);
                                                    $is_pdf = ($tmpl_ext === 'pdf');
                                                    $icon_class = $is_doc ? 'bi-file-earmark-word-fill text-blue-600' : ($is_pdf ? 'bi-file-earmark-pdf-fill text-rose-600' : 'bi-file-earmark-text-fill text-amber-600');
                                                ?>
                                                <div class="flex items-center justify-between gap-2 p-3 rounded-xl border border-indigo-200/80 bg-white hover:border-indigo-400 hover:shadow-xs transition group">
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

                                <?php if (empty($syarat_list)): ?>
                                    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-2">
                                        <i class="bi bi-folder2-open text-amber-600 text-3xl block"></i>
                                        <h4 class="text-sm font-bold text-amber-950">Belum Ada Daftar Persyaratan KP Dinamis</h4>
                                        <p class="text-xs text-amber-800">
                                            Super Admin belum menambahkan item berkas persyaratan pada menu <strong>Syarat Berkas KP</strong>. Anda dapat langsung melanjutkan ke tahap konfirmasi untuk menyimpan data pengajuan KP.
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-5 pt-2 w-full max-w-full overflow-hidden" id="dynamicKpDocContainer">
                                        <?php $b_idx = 1; ?>
                                        <?php foreach ($syarat_list as $sb): ?>
                                            <?php
                                                $kode         = $sb['kode_berkas'];
                                                $req          = ((int)($sb['is_required'] ?? 1) === 1);
                                                $f_row        = $files_map[$kode] ?? null;
                                                $old_filepath = $f_row['file'] ?? '';
                                                $old_filename = $f_row['file_basename'] ?? basename($old_filepath);
                                                $old_file_url = !empty($old_filepath) ? (strpos($old_filepath, 'uploads/') === 0 ? base_url($old_filepath) : base_url('uploads/persyaratan_kp/' . $old_filepath)) : '';
                                                $st_laa       = $f_row['status_adminlaa'] ?? '';
                                                $is_app_laa   = $this->Kp_model->is_status_approved($st_laa);
                                                $is_rej_laa   = $this->Kp_model->is_status_rejected($st_laa);
                                            ?>
                                            <div class="p-4 sm:p-5 rounded-2xl bg-white border <?= $is_app_laa ? 'border-emerald-300' : ($is_rej_laa ? 'border-rose-300' : 'border-orange-200/90'); ?> shadow-2xs space-y-3 kp-doc-card w-full max-w-full overflow-hidden box-border"
                                                 data-kode="<?= htmlspecialchars($kode); ?>"
                                                 data-nama="<?= htmlspecialchars($sb['nama_berkas']); ?>"
                                                 data-required="<?= $req ? '1' : '0'; ?>"
                                                 data-has-old="<?= !empty($old_filename) ? '1' : '0'; ?>">

                                                <div class="flex items-start justify-between gap-3 w-full max-w-full overflow-hidden">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h4 class="font-bold text-sm text-slate-900"><?= $b_idx; ?>. <?= htmlspecialchars($sb['nama_berkas']); ?></h4>
                                                            <?php if ($req): ?>
                                                                <span class="text-[9px] font-extrabold text-rose-700 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">Wajib</span>
                                                            <?php else: ?>
                                                                <span class="text-[9px] font-extrabold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">Opsional</span>
                                                            <?php endif; ?>

                                                            <?php if (!empty($sb['file_template'])): ?>
                                                                <a href="<?= base_url('uploads/templates/' . $sb['file_template']); ?>" download target="_blank"
                                                                   class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2.5 py-0.5 rounded-full transition shadow-2xs shrink-0"
                                                                   title="Unduh template resmi: <?= htmlspecialchars($sb['file_template']); ?>">
                                                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                                                    <span>Unduh Template</span>
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php
                                                                $is_draft_laa = (strtolower(trim((string)$st_laa)) === 'draft' || $st_laa === '');
                                                            ?>
                                                            <span id="statusBadge_<?= htmlspecialchars($kode); ?>" class="<?= empty($old_filename) ? 'hidden' : 'inline-flex'; ?> text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider items-center gap-1 <?= $is_app_laa ? 'text-white bg-emerald-600' : ($is_rej_laa ? 'text-white bg-rose-600' : ($is_draft_laa ? 'text-slate-700 bg-slate-200 border border-slate-300' : 'text-amber-800 bg-amber-100 border border-amber-300')); ?>">
                                                                <?php if ($is_app_laa): ?>
                                                                    <i class="bi bi-check-circle-fill"></i> Disetujui LAA
                                                                <?php elseif ($is_rej_laa): ?>
                                                                    <i class="bi bi-x-circle-fill"></i> Revisi LAA
                                                                <?php elseif ($is_draft_laa): ?>
                                                                    <i class="bi bi-cloud-check"></i> Draft Tersimpan
                                                                <?php else: ?>
                                                                    <i class="bi bi-hourglass-split"></i> Menunggu LAA
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>

                                                        <?php if (!empty($sb['deskripsi'])): ?>
                                                            <p class="text-xs text-slate-500 mt-1 font-medium leading-normal"><?= htmlspecialchars($sb['deskripsi']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Drop Zone Box -->
                                                <div class="kp-drop-zone border-2 border-dashed border-orange-300 rounded-2xl p-4 sm:p-5 text-center bg-orange-50/40 hover:bg-orange-100/50 transition-all duration-300 cursor-pointer group relative w-full max-w-full overflow-hidden box-border"
                                                     onclick="triggerKpFileInput('<?= htmlspecialchars($kode); ?>', event)">
                                                    <input type="file" name="file_kp_<?= htmlspecialchars($kode); ?>" id="fileInput_<?= htmlspecialchars($kode); ?>"
                                                           class="hidden kp-file-input" accept=".pdf,application/pdf"
                                                           data-kode="<?= htmlspecialchars($kode); ?>"
                                                           onchange="handleKpFileSelect(this)">

                                                    <!-- Unselected Prompt -->
                                                    <div id="prompt_<?= htmlspecialchars($kode); ?>" class="<?= !empty($old_filename) ? 'hidden' : ''; ?>">
                                                        <div class="w-11 h-11 bg-orange-500/10 text-orange-600 rounded-2xl flex items-center justify-center text-lg mx-auto mb-2 box-3d group-hover:scale-105 transition-transform">
                                                            <i class="bi bi-cloud-arrow-up-fill"></i>
                                                        </div>
                                                        <h5 class="font-bold text-slate-800 text-xs">Klik atau seret file PDF <?= htmlspecialchars($sb['nama_berkas']); ?> ke sini</h5>
                                                        <p class="text-[10px] text-slate-400 mt-0.5 font-normal">Format PDF (Maksimal 5MB) • Otomatis tersimpan sebagai Draft</p>
                                                    </div>

                                                    <!-- Selected File Card Preview -->
                                                    <div id="selected_<?= htmlspecialchars($kode); ?>" class="<?= empty($old_filename) ? 'hidden' : 'flex'; ?> flex-col sm:flex-row items-center justify-between gap-3 p-3 bg-white border border-emerald-300 rounded-xl shadow-xs transition-all duration-300 w-full max-w-full overflow-hidden box-border">
                                                        <div class="flex items-center gap-3 text-left min-w-0 flex-1 w-full max-w-full overflow-hidden">
                                                            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold shrink-0 box-3d">
                                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                                            </div>
                                                            <div class="min-w-0 flex-1 max-w-full overflow-hidden">
                                                                <div class="flex items-center gap-2 min-w-0 max-w-full overflow-hidden">
                                                                    <span class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300 shrink-0">FILE TERPILIH</span>
                                                                    <h5 id="fileName_<?= htmlspecialchars($kode); ?>" class="font-bold text-xs text-slate-900 truncate block"><?= htmlspecialchars($old_filename ?: 'berkas.pdf'); ?></h5>
                                                                </div>
                                                                <p id="fileSub_<?= htmlspecialchars($kode); ?>" class="text-[10px] text-slate-500 font-medium mt-0.5">
                                                                    <?= !empty($old_filename) ? ($is_draft_laa ? 'Tersimpan sebagai Draft di database (Belum dikirim ke LAA)' : 'Telah dikirim ke Admin LAA') : 'Siap diunggah'; ?>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto" onclick="event.stopPropagation();">
                                                            <a id="btnView_<?= htmlspecialchars($kode); ?>"
                                                               href="<?= !empty($old_file_url) ? $old_file_url : '#'; ?>" target="_blank"
                                                               class="<?= empty($old_file_url) ? 'hidden' : 'flex'; ?> text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg transition items-center gap-1.5">
                                                                <i class="bi bi-eye-fill text-xs"></i> Lihat
                                                            </a>
                                                            <?php if (!$is_app_laa && !$is_form_locked): ?>
                                                                <button type="button" onclick="triggerKpFileInput('<?= htmlspecialchars($kode); ?>', event)"
                                                                        class="text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                                                    <i class="bi bi-arrow-repeat text-xs"></i> Ganti File
                                                                </button>
                                                                <button type="button" id="btnDel_<?= htmlspecialchars($kode); ?>"
                                                                        onclick="deleteKpFileAjax('<?= htmlspecialchars($kode); ?>', event)"
                                                                        class="<?= empty($old_filename) ? 'hidden' : 'flex'; ?> text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold px-3 py-1.5 rounded-lg transition items-center gap-1.5 cursor-pointer">
                                                                    <i class="bi bi-trash3 text-xs"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $b_idx++; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- =======================================================
                                 STEP 3: RINGKASAN & KONFIRMASI PENGAJUAN KP
                                 ======================================================= -->
                            <div id="step-content-3" class="step-content space-y-6 hidden">
                                <div class="flex items-center gap-4 mb-2">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                                        03
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">KP REGISTRATION</span>
                                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Ringkasan &amp; Konfirmasi Pengajuan KP</h3>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                    Tinjau kembali seluruh data pengajuan Kerja Praktik dan berkas persyaratan Anda sebelum dikirimkan ke Admin Layanan Akademik (LAA).
                                </p>

                                <!-- Summary Display Card -->
                                <div class="p-6 rounded-2xl bg-orange-50/80 border border-orange-200 space-y-5">
                                    <div class="border-b border-orange-200/80 pb-4 flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-600">PERUSAHAAN / INSTANSI TEMPAT KP</span>
                                            <h4 class="text-sm font-black text-slate-900 mt-0.5" id="sumNamaPerusahaan">-</h4>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-300 px-3 py-1 rounded-full">Siap Dikirim</span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="p-3 rounded-xl bg-white/80 border border-orange-100">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">MAHASISWA</span>
                                            <p class="text-xs font-bold text-slate-900 mt-0.5" id="sumMhs">-</p>
                                            <span class="text-[11px] text-slate-500" id="sumProdiSks">-</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-white/80 border border-orange-100">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">PEMBIMBING LAPANGAN</span>
                                            <p class="text-xs font-bold text-slate-900 mt-0.5" id="sumPembimbingLapangan">-</p>
                                            <span class="text-[11px] text-slate-500" id="sumKontakLapangan">-</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-white/80 border border-orange-100 sm:col-span-2">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">ALAMAT &amp; POSISI TEMPAT KP</span>
                                            <p class="text-xs font-bold text-slate-900 mt-0.5" id="sumAlamatKp">-</p>
                                            <span class="text-[11px] text-orange-700 font-semibold" id="sumPosisiKp">-</span>
                                        </div>
                                    </div>

                                    <!-- Document Status Summary List -->
                                    <div class="pt-3 border-t border-orange-200/80">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-600 block mb-2">KELENGKAPAN BERKAS PERSYARATAN</span>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="summaryKpDocList"></div>
                                    </div>
                                </div>

                                <!-- Confirmation Checkbox -->
                                <div class="p-4 rounded-xl bg-white border border-slate-200">
                                    <label class="inline-flex items-start gap-3 cursor-pointer select-none">
                                        <input type="checkbox" id="checkKonfirmasiKp" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 mt-0.5 cursor-pointer" required>
                                        <span class="text-xs font-semibold text-slate-800 leading-relaxed">
                                            Saya menyatakan bahwa data tempat Kerja Praktik, pembimbing lapangan, dan dokumen persyaratan yang saya unggah adalah <strong>benar dan lengkap</strong>.
                                        </span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </fieldset>

                    <!-- Footer Navigation (Identical to Pendaftaran TA) -->
                    <div class="px-6 sm:px-10 py-5 bg-orange-100/40 border-t border-orange-200/60 flex items-center justify-between">
                        <button type="button" id="btnPrevStep" onclick="prevStep()"
                                class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 border border-slate-300 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-xs shadow-xs box-3d cursor-pointer">
                            <i class="bi bi-arrow-left text-base font-bold"></i> <span>Kembali</span>
                        </button>

                        <div class="ml-auto flex items-center gap-3">
                            <button type="button" id="btnNextStep" onclick="nextStep()"
                                    class="btn-3d-orange flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-bold text-xs cursor-pointer">
                                <span>Lanjutkan</span> <i class="bi bi-arrow-right text-sm"></i>
                            </button>

                            <?php if ($is_form_locked): ?>
                                <button type="button" id="btnSubmitKp" disabled
                                        class="hidden flex items-center gap-2 bg-slate-200 border border-slate-300 text-slate-500 font-bold px-6 py-2.5 rounded-xl text-xs cursor-not-allowed">
                                    <i class="bi bi-lock-fill text-sm"></i> Terkunci (Sudah Disetujui)
                                </button>
                            <?php else: ?>
                                <button type="submit" id="btnSubmitKp"
                                        class="hidden flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-xs box-3d cursor-pointer">
                                    <i class="bi bi-send-fill text-sm"></i> Kirim Pengajuan KP
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const CURRENT_NIM_KP = "<?= htmlspecialchars($nim_mhs ?: 'mhs'); ?>";
        const STEP_STORAGE_KEY = 'kp_active_step_' + CURRENT_NIM_KP;
        const DRAFT_STORAGE_KEY = 'kp_draft_fields_' + CURRENT_NIM_KP;
        const HAS_URL_STEP = <?= $this->input->get('step') ? 'true' : 'false'; ?>;
        const SAVE_DRAFT_KP_URL = "<?= site_url('mahasiswa/ajax_save_draft_kp'); ?>";
        const UPLOAD_FILE_KP_URL = "<?= site_url('mahasiswa/ajax_upload_file_kp'); ?>";
        const DELETE_FILE_KP_URL = "<?= site_url('mahasiswa/ajax_delete_file_kp'); ?>";
        let draftSaveTimer = null;

        const KP_FIELD_IDS = [
            'inputPeminatan', 'inputSksLulus', 'inputSksBerjalan', 'inputJenisKp',
            'inputNamaPerusahaan', 'inputAlamatPerusahaan', 'inputKota', 'inputKodePos',
            'inputTelpFax', 'inputEmailPerusahaan', 'inputPosisiPekerjaan',
            'inputNamaTujuan', 'inputJabatan', 'inputCp'
        ];

        let currentKpStep = <?= (int)$initial_step; ?>;
        if (!HAS_URL_STEP) {
            const savedStep = parseInt(localStorage.getItem(STEP_STORAGE_KEY) || '1', 10);
            if (savedStep >= 1 && savedStep <= 3) {
                currentKpStep = savedStep;
            }
        }

        function setDbStatusIndicator(state, customText) {
            const badge = document.getElementById('dbSaveStatusKp');
            const icon = document.getElementById('dbSaveStatusKpIcon');
            const text = document.getElementById('dbSaveStatusKpText');
            if (!badge || !icon || !text) return;

            if (state === 'saving') {
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs transition-all duration-300';
                icon.className = 'bi bi-arrow-repeat animate-spin text-amber-600 text-sm';
                text.textContent = customText || 'Menyimpan draft ke database...';
            } else if (state === 'saved') {
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs transition-all duration-300';
                icon.className = 'bi bi-cloud-check-fill text-emerald-500 text-sm';
                text.textContent = customText || 'Draft tersimpan di database';
            } else if (state === 'error') {
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs transition-all duration-300';
                icon.className = 'bi bi-exclamation-circle-fill text-rose-500 text-sm';
                text.textContent = customText || 'Gagal menyimpan draft';
            }
        }

        function saveLocalDraft() {
            const draft = {};
            KP_FIELD_IDS.forEach((id) => {
                const el = document.getElementById(id);
                if (el) draft[id] = el.value;
            });
            try {
                localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(draft));
            } catch (e) {}
        }

        function restoreLocalDraft() {
            try {
                const raw = localStorage.getItem(DRAFT_STORAGE_KEY);
                if (!raw) return;
                const draft = JSON.parse(raw);
                KP_FIELD_IDS.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el && el.value.trim() === '' && draft[id]) {
                        el.value = draft[id];
                    }
                });
            } catch (e) {}
        }

        function autoSaveStep1ToDb() {
            const form = document.getElementById('formPengajuanKP');
            if (!form) return;
            setDbStatusIndicator('saving', 'Menyimpan draft ke database...');
            const fd = new FormData();
            KP_FIELD_IDS.forEach((id) => {
                const el = document.getElementById(id);
                if (el && el.name) {
                    fd.append(el.name, el.value);
                }
            });
            fetch(SAVE_DRAFT_KP_URL, {
                method: 'POST',
                body: fd
            })
            .then((r) => r.json())
            .then((res) => {
                if (res && res.status === 'ok') {
                    const label = res.status_file === 'Draft' ? 'Draft tersimpan di database' : 'Data tersimpan di database';
                    setDbStatusIndicator('saved', label);
                }
            })
            .catch(() => {});
        }

        function scheduleAutoSaveStep1() {
            saveLocalDraft();
            if (draftSaveTimer) clearTimeout(draftSaveTimer);
            draftSaveTimer = setTimeout(() => {
                autoSaveStep1ToDb();
            }, 600);
        }

        function updateStepperUI() {
            try {
                localStorage.setItem(STEP_STORAGE_KEY, String(currentKpStep));
                const url = new URL(window.location.href);
                url.searchParams.set('step', String(currentKpStep));
                window.history.replaceState({}, '', url.toString());
            } catch (e) {}

            for (let i = 1; i <= 3; i++) {
                const content = document.getElementById('step-content-' + i);
                const item = document.getElementById('step-item-' + i);
                const counter = item.querySelector('.step-counter');
                const title = item.querySelector('.step-title');

                if (i === currentKpStep) {
                    content.classList.remove('hidden');
                    counter.className = 'step-counter w-11 h-11 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white font-bold flex items-center justify-center text-sm box-3d ring-4 ring-orange-200/80 transition-all duration-300 z-10';
                    counter.innerHTML = i;
                    title.className = 'step-title font-bold text-xs sm:text-sm text-orange-600 mt-2 text-center transition-all duration-300';
                } else if (i < currentKpStep) {
                    content.classList.add('hidden');
                    counter.className = 'step-counter w-10 h-10 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-xs shadow-xs transition-all duration-300 z-10';
                    counter.innerHTML = '<i class="bi bi-check-lg"></i>';
                    title.className = 'step-title font-semibold text-xs text-emerald-700 mt-2 text-center transition-all duration-300';
                } else {
                    content.classList.add('hidden');
                    counter.className = 'step-counter w-10 h-10 rounded-full bg-white text-slate-400 font-semibold border border-orange-200 flex items-center justify-center text-xs transition-all duration-300 z-10';
                    counter.innerHTML = i;
                    title.className = 'step-title font-medium text-xs text-slate-400 mt-2 text-center transition-all duration-300';
                }
            }

            const progressLine = document.getElementById('stepperProgressLine');
            if (progressLine) {
                progressLine.style.width = ((currentKpStep - 1) / 2 * 100) + '%';
            }

            const stepCounterText = document.getElementById('stepCounterText');
            if (stepCounterText) {
                stepCounterText.textContent = 'LANGKAH ' + currentKpStep + ' / 3';
            }

            const btnNext = document.getElementById('btnNextStep');
            const btnSubmit = document.getElementById('btnSubmitKp');
            if (currentKpStep === 3) {
                btnNext.classList.add('hidden');
                btnSubmit.classList.remove('hidden');
                populateStep3Summary();
            } else {
                btnNext.classList.remove('hidden');
                btnSubmit.classList.add('hidden');
            }
        }

        function validateStep1() {
            for (const id of KP_FIELD_IDS) {
                const el = document.getElementById(id);
                if (el && !el.disabled && el.value.trim() === '') {
                    const alertBox = document.getElementById('step1Alert');
                    if (alertBox) alertBox.classList.remove('hidden');
                    el.focus();
                    return false;
                }
            }
            const alertBox = document.getElementById('step1Alert');
            if (alertBox) alertBox.classList.add('hidden');
            return true;
        }

        function validateStep2() {
            const cards = document.querySelectorAll('.kp-doc-card');
            for (const card of cards) {
                const isReq = card.getAttribute('data-required') === '1';
                const hasOld = card.getAttribute('data-has-old') === '1';
                const kode = card.getAttribute('data-kode');
                const fileInput = document.getElementById('fileInput_' + kode);
                const hasNew = fileInput && fileInput.files && fileInput.files.length > 0;
                if (isReq && !hasOld && !hasNew) {
                    const alertBox = document.getElementById('step2Alert');
                    if (alertBox) alertBox.classList.remove('hidden');
                    return false;
                }
            }
            const alertBox = document.getElementById('step2Alert');
            if (alertBox) alertBox.classList.add('hidden');
            return true;
        }

        function goToStep(targetStep) {
            if (targetStep > currentKpStep) {
                if (currentKpStep === 1) {
                    if (!validateStep1()) return;
                    saveLocalDraft();
                    autoSaveStep1ToDb();
                }
                if (targetStep === 3 && !validateStep2()) {
                    currentKpStep = 2;
                    updateStepperUI();
            initKpDropZones();
                    return;
                }
            }
            currentKpStep = targetStep;
            updateStepperUI();
            initKpDropZones();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextStep() {
            if (currentKpStep === 1) {
                if (!validateStep1()) return;
                saveLocalDraft();
                autoSaveStep1ToDb();
                currentKpStep = 2;
            } else if (currentKpStep === 2) {
                if (!validateStep2()) return;
                currentKpStep = 3;
            }
            updateStepperUI();
            initKpDropZones();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function prevStep() {
            if (currentKpStep > 1) {
                currentKpStep--;
                updateStepperUI();
            initKpDropZones();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                window.location.href = "<?= site_url('mahasiswa'); ?>";
            }
        }

        function triggerKpFileInput(kode, event) {
            if (event) event.stopPropagation();
            const input = document.getElementById('fileInput_' + kode);
            if (input && !input.disabled) {
                input.click();
            }
        }

        function handleKpFileSelect(input) {
            const kode = input.getAttribute('data-kode');
            const card = document.querySelector('.kp-doc-card[data-kode="' + kode + '"]');
            const promptEl = document.getElementById('prompt_' + kode);
            const selectedEl = document.getElementById('selected_' + kode);
            const nameEl = document.getElementById('fileName_' + kode);
            const subEl = document.getElementById('fileSub_' + kode);
            const statusBadge = document.getElementById('statusBadge_' + kode);
            const btnView = document.getElementById('btnView_' + kode);
            const btnDel = document.getElementById('btnDel_' + kode);

            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];

            if (promptEl) promptEl.classList.add('hidden');
            if (selectedEl) {
                selectedEl.classList.remove('hidden');
                selectedEl.classList.add('flex');
            }
            if (nameEl) nameEl.textContent = file.name;
            if (subEl) subEl.textContent = 'Mengunggah & menyimpan draft ke database...';
            setDbStatusIndicator('saving', 'Mengunggah berkas draft ke database...');

            const fd = new FormData();
            fd.append('kode_berkas', kode);
            fd.append('file_kp_' + kode, file);

            fetch(UPLOAD_FILE_KP_URL, {
                method: 'POST',
                body: fd
            })
            .then((r) => r.json())
            .then((res) => {
                if (res && res.success) {
                    if (card) card.setAttribute('data-has-old', '1');
                    if (nameEl) nameEl.textContent = res.file_name;
                    if (subEl) {
                        subEl.textContent = res.status_laa === 'Draft'
                            ? 'Tersimpan sebagai Draft di database (Belum dikirim ke LAA)'
                            : 'Telah dikirim ke Admin LAA';
                    }
                    if (statusBadge) {
                        statusBadge.classList.remove('hidden');
                        statusBadge.classList.add('inline-flex');
                        if (res.status_laa === 'Draft') {
                            statusBadge.className = 'inline-flex text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider items-center gap-1 text-slate-700 bg-slate-200 border border-slate-300';
                            statusBadge.innerHTML = '<i class="bi bi-cloud-check"></i> Draft Tersimpan';
                        } else {
                            statusBadge.className = 'inline-flex text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider items-center gap-1 text-amber-800 bg-amber-100 border border-amber-300';
                            statusBadge.innerHTML = '<i class="bi bi-hourglass-split"></i> Menunggu LAA';
                        }
                    }
                    if (btnView && res.file_url) {
                        btnView.href = res.file_url;
                        btnView.classList.remove('hidden');
                        btnView.classList.add('flex');
                    }
                    if (btnDel) {
                        btnDel.classList.remove('hidden');
                        btnDel.classList.add('flex');
                    }
                    // Clear file input so final submit doesn't re-upload the same file twice
                    input.value = '';
                    setDbStatusIndicator('saved', 'Draft berkas tersimpan di database');
                } else {
                    setDbStatusIndicator('error', res && res.message ? res.message : 'Gagal mengunggah berkas');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengunggah Berkas',
                        text: (res && res.message) ? res.message : 'Pastikan file berformat PDF maksimal 5 MB.'
                    });
                }
            })
            .catch(() => {
                setDbStatusIndicator('error', 'Gagal mengunggah berkas');
            });
        }

        function deleteKpFileAjax(kode, event) {
            if (event) event.stopPropagation();
            if (!confirm('Hapus berkas persyaratan ini dari draft?')) return;

            const card = document.querySelector('.kp-doc-card[data-kode="' + kode + '"]');
            const promptEl = document.getElementById('prompt_' + kode);
            const selectedEl = document.getElementById('selected_' + kode);
            const statusBadge = document.getElementById('statusBadge_' + kode);
            const fileInput = document.getElementById('fileInput_' + kode);

            setDbStatusIndicator('saving', 'Menghapus berkas dari database...');

            const fd = new FormData();
            fd.append('kode_berkas', kode);

            fetch(DELETE_FILE_KP_URL, {
                method: 'POST',
                body: fd
            })
            .then((r) => r.json())
            .then((res) => {
                if (res && res.success) {
                    if (card) card.setAttribute('data-has-old', '0');
                    if (fileInput) fileInput.value = '';
                    if (selectedEl) {
                        selectedEl.classList.add('hidden');
                        selectedEl.classList.remove('flex');
                    }
                    if (promptEl) promptEl.classList.remove('hidden');
                    if (statusBadge) {
                        statusBadge.classList.add('hidden');
                        statusBadge.classList.remove('inline-flex');
                    }
                    setDbStatusIndicator('saved', 'Berkas dihapus dari draft database');
                } else {
                    setDbStatusIndicator('error', (res && res.message) ? res.message : 'Gagal menghapus berkas');
                }
            })
            .catch(() => {
                setDbStatusIndicator('error', 'Gagal menghapus berkas');
            });
        }

        function populateStep3Summary() {
            const val = (id) => {
                const el = document.getElementById(id);
                return el && el.value.trim() !== '' ? el.value.trim() : '-';
            };
            document.getElementById('sumNamaPerusahaan').textContent = val('inputNamaPerusahaan');
            document.getElementById('sumMhs').textContent = val('inputNamaMhs') + ' (' + val('inputNim') + ')';
            document.getElementById('sumProdiSks').textContent = val('inputPeminatan') + ' • SKS Lulus: ' + val('inputSksLulus') + ' / Berjalan: ' + val('inputSksBerjalan');
            document.getElementById('sumPembimbingLapangan').textContent = val('inputNamaTujuan') + ' (' + val('inputJabatan') + ')';
            document.getElementById('sumKontakLapangan').textContent = 'HP/CP: ' + val('inputCp') + ' • Email: ' + val('inputEmailPerusahaan');
            document.getElementById('sumAlamatKp').textContent = val('inputAlamatPerusahaan') + ', ' + val('inputKota') + ' ' + val('inputKodePos');
            document.getElementById('sumPosisiKp').textContent = 'Posisi/Divisi: ' + val('inputPosisiPekerjaan') + ' (' + val('inputJenisKp') + ')';

            const docListEl = document.getElementById('summaryKpDocList');
            if (!docListEl) return;
            docListEl.innerHTML = '';

            const cards = document.querySelectorAll('.kp-doc-card');
            if (cards.length === 0) {
                docListEl.innerHTML = '<div class="text-xs text-slate-500 col-span-2">Belum ada item syarat berkas dinamis yang diatur oleh Super Admin.</div>';
                return;
            }

            cards.forEach((card) => {
                const kode = card.getAttribute('data-kode');
                const nama = card.getAttribute('data-nama');
                const hasOld = card.getAttribute('data-has-old') === '1';
                const fileInput = document.getElementById('fileInput_' + kode);
                const hasNew = fileInput && fileInput.files && fileInput.files.length > 0;
                const ok = hasOld || hasNew;

                const div = document.createElement('div');
                div.className = 'p-2.5 rounded-xl border flex items-center gap-2 text-xs font-semibold ' +
                    (ok ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-100 border-slate-200 text-slate-500');
                div.innerHTML = '<i class="bi ' + (ok ? 'bi-check-circle-fill text-emerald-600' : 'bi-dash-circle text-slate-400') + '"></i>' +
                    '<span class="truncate">' + nama + '</span>';
                docListEl.appendChild(div);
            });
        }

        function initKpDropZones() {
            // Cegah browser membuka file secara default saat drop di luar dropzone
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
                window.addEventListener(eventName, function(e) {
                    if (eventName === 'dragover' || eventName === 'drop') {
                        e.preventDefault();
                    }
                }, false);
            });

            document.querySelectorAll('.kp-drop-zone').forEach((zone) => {
                ['dragenter', 'dragover'].forEach((eventName) => {
                    zone.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.add('border-orange-500', 'bg-orange-100/90', 'ring-4', 'ring-orange-200');
                        zone.classList.remove('border-orange-300', 'bg-orange-50/40');
                    }, false);
                });

                ['dragleave', 'dragend'].forEach((eventName) => {
                    zone.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (e.relatedTarget && zone.contains(e.relatedTarget)) return;
                        zone.classList.remove('border-orange-500', 'bg-orange-100/90', 'ring-4', 'ring-orange-200');
                        zone.classList.add('border-orange-300', 'bg-orange-50/40');
                    }, false);
                });

                zone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    zone.classList.remove('border-orange-500', 'bg-orange-100/90', 'ring-4', 'ring-orange-200');
                    zone.classList.add('border-orange-300', 'bg-orange-50/40');

                    const dt = e.dataTransfer;
                    if (!dt || !dt.files || dt.files.length === 0) return;

                    const file = dt.files[0];
                    const input = zone.querySelector('input.kp-file-input');
                    if (!input || input.disabled) return;

                    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                    if (!isPdf) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Format Berkas Tidak Sesuai',
                                text: 'Hanya berkas berformat PDF yang diperbolehkan.'
                            });
                        } else {
                            alert('Hanya berkas berformat PDF yang diperbolehkan.');
                        }
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ukuran Terlalu Besar',
                                text: 'Ukuran file maksimal adalah 5 MB.'
                            });
                        } else {
                            alert('Ukuran file maksimal adalah 5 MB.');
                        }
                        return;
                    }

                    try {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                    } catch (err) {
                        input.files = dt.files;
                    }

                    handleKpFileSelect(input);
                }, false);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreLocalDraft();

            const formKp = document.getElementById('formPengajuanKP');
            if (formKp) {
                formKp.addEventListener('submit', function(e) {
                    const btn = document.getElementById('btnSubmitKp');
                    if (!btn) return;
                    if (btn.disabled || btn.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    btn.setAttribute('data-submitting', 'true');
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.classList.remove('cursor-pointer', 'hover:from-emerald-700', 'hover:to-teal-700');
                    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Mengirim Pengajuan...</span>';
                });
            }
            KP_FIELD_IDS.forEach((id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', scheduleAutoSaveStep1);
                    el.addEventListener('change', scheduleAutoSaveStep1);
                }
            });
            if (currentKpStep > 1 && !validateStep1()) {
                currentKpStep = 1;
            }
            updateStepperUI();
            initKpDropZones();
        });
    </script>
</body>
</html>
