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

    <!-- Toast Notification Popup -->
    <div id="inPageToastAlert" class="fixed top-20 right-6 z-[9999] max-w-md bg-rose-600 text-white p-4 rounded-2xl shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-[-20px] opacity-0 hidden ring-4 ring-rose-300/50">
        <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm font-bold shrink-0 box-3d">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="flex-grow pt-0.5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-200">Validasi Formulir</h4>
            <p class="text-xs font-semibold text-white mt-0.5 leading-relaxed" id="toastAlertMessage">Pesan...</p>
        </div>
        <button type="button" class="text-white/80 hover:text-white p-1 text-sm transition cursor-pointer" onclick="hideToast()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Header Glass Navbar (Clean White Glass - Consistent) -->
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

    <!-- Main Content Container (Continuous Vertical Single-Page Form) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-8 flex-grow space-y-8 max-w-5xl mx-auto">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-orange-200/70 pb-5">
            <div class="flex items-center gap-4">
                <a href="<?= site_url('mahasiswa'); ?>" class="w-10 h-10 rounded-xl bg-white hover:bg-orange-100/80 text-slate-700 hover:text-orange-600 border border-orange-200 flex items-center justify-center transition shadow-2xs box-3d" title="Kembali ke Dashboard Mahasiswa">
                    <i class="bi bi-arrow-left text-lg font-bold"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-600">FORMULIR PENGAJUAN</span>
                        <?php if(!empty($is_locked)): ?>
                            <span class="text-[10px] bg-slate-200 text-slate-700 font-bold px-2.5 py-0.5 rounded-full border border-slate-300 flex items-center gap-1">
                                <i class="bi bi-lock-fill"></i> Terkunci (Sedang Ditinjau)
                            </span>
                        <?php else: ?>
                            <span class="text-[10px] bg-rose-100 text-rose-800 font-bold px-2.5 py-0.5 rounded-full border border-rose-300 flex items-center gap-1">
                                <i class="bi bi-pencil-fill"></i> Mode Revisi
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                        <?= !empty($is_locked) ? 'Ringkasan Formulir Tugas Akhir' : 'Edit Formulir Pengajuan Tugas Akhir'; ?>
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= site_url('mahasiswa/detail_pendaftaran'); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-2xs transition">
                    <i class="bi bi-eye-fill"></i> Status Approval
                </a>
                <a href="<?= site_url('mahasiswa'); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 shadow-2xs transition">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </div>
        </div>

        <?php if(!empty($is_locked)): ?>
            <!-- Locked View-Only Notice Banner -->
            <div class="p-5 rounded-2xl bg-amber-500/10 border-2 border-amber-400/80 text-amber-950 shadow-xs flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl font-bold box-3d shrink-0">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800 block">STATUS: FORMULIR TERKUNCI (SEDANG DITINJAU)</span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed mt-1">
                        Pengajuan Tugas Akhir Anda saat ini sedang dalam proses peninjauan berjenjang. Kolom formulir dibuat <strong>hanya lihat (tidak dapat diedit)</strong>. Seluruh kolom input akan otomatis dapat diedit kembali jika terdapat catatan revisi dari Dosen Wali atau Admin Layanan.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($pendaftaran['catatan_wali'])): ?>
            <!-- Reminder Alert: Feedback Dosen Wali -->
            <div class="p-5 rounded-2xl bg-amber-50 border-2 border-amber-300 shadow-xs flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl font-bold box-3d shrink-0">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">CATATAN REVISI DARI DOSEN WALI</span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed mt-1">
                        <?= nl2br(htmlspecialchars($pendaftaran['catatan_wali'])); ?>
                    </p>
                    <span class="text-[11px] text-amber-700 font-medium block mt-2">
                        Silakan sesuaikan data judul atau unggah ulang berkas PDF yang diminta di bawah ini.
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Edit Pendaftaran (All Form Fields on 1 Long Continuous Page) -->
        <form action="<?= site_url('mahasiswa/edit_pendaftaran'); ?>" method="POST" enctype="multipart/form-data" id="formEditPendaftaranTA" class="space-y-8">
            <fieldset class="space-y-8 <?= !empty($is_locked) ? 'opacity-65 select-none' : ''; ?>" <?= !empty($is_locked) ? 'disabled' : ''; ?>>

            <!-- BAGIAN 1: Pilihan Jenis Tugas Akhir -->
            <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-3.5 pb-4 border-b border-orange-100">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                        01
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 1</span>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Jenis Tugas Akhir</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-600 font-normal">
                    Pilih jalur tugas akhir akademik yang sedang Anda tempuh.
                </p>

                <?php 
                    $current_jenis = $pendaftaran['jenis_ta'] ?? 'Tugas Akhir Reguler';
                    $opsi_jenis = [
                        'Proyek Akhir',
                        'Tugas Akhir Reguler',
                        'Tugas Akhir jalur Magang (MBKM)',
                        'Tugas Akhir jalur Prestasi / Lomba'
                    ];
                ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <?php foreach($opsi_jenis as $idx => $opt): ?>
                        <?php $is_checked = ($current_jenis === $opt); ?>
                        <label class="jenis-radio-label relative p-4 rounded-xl border-2 cursor-pointer transition-all flex items-start gap-3 <?= $is_checked ? 'border-orange-500 bg-orange-50/70 shadow-xs' : 'border-slate-200 bg-white hover:border-orange-300'; ?>">
                            <input type="radio" name="jenis_ta" value="<?= htmlspecialchars($opt); ?>" class="mt-0.5 text-orange-600 focus:ring-orange-500" <?= $is_checked ? 'checked' : ''; ?> required>
                            <div class="min-w-0">
                                <span class="font-bold text-xs sm:text-sm text-slate-900 block"><?= htmlspecialchars($opt); ?></span>
                                <span class="text-[11px] text-slate-500 font-normal">Jalur skema akademik tugas akhir</span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- BAGIAN 2: Usulan Judul Tugas Akhir -->
            <?php
                $st_j = $pendaftaran['status_judul'] ?? 'Pending';
                $note_j = $pendaftaran['catatan_judul'] ?? '';
            ?>
            <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-orange-100 flex-wrap gap-2">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                            02
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 2</span>
                            <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Usulan Judul &amp; Konsentrasi</h3>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold <?= ($st_j === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (($st_j === 'Rejected') ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-amber-100 text-amber-800 border-amber-300'); ?> px-3 py-1 rounded-full border">
                        <?= ($st_j === 'Approved') ? '✅ Judul Disetujui' : (($st_j === 'Rejected') ? '❌ Judul Ditolak / Perlu Revisi' : '⏳ Status Judul: Pending'); ?>
                    </span>
                </div>

                <!-- Saran / Catatan Revisi Judul dari Dosen Wali -->
                <?php if(!empty($note_j) || $st_j === 'Rejected'): ?>
                    <div class="p-4 rounded-2xl <?= ($st_j === 'Rejected') ? 'bg-rose-100/80 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1.5 shadow-2xs">
                        <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_j === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>">
                            <i class="bi bi-chat-left-dots-fill"></i> Saran / Catatan Dosen Wali:
                        </span>
                        <p class="italic text-xs sm:text-sm font-medium leading-relaxed">
                            "<?= !empty($note_j) ? htmlspecialchars($note_j) : 'Judul perlu diperbaiki sesuai arahan dosen.'; ?>"
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Judul Utama 1 -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Judul Usulan 1 (Utama - Bahasa Indonesia) <span class="text-orange-500">*</span>
                    </label>
                    <textarea name="judul_1" id="inputJudul1" rows="3" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs sm:text-sm font-semibold text-slate-900" placeholder="Masukkan judul utama..." required><?= htmlspecialchars($pendaftaran['judul_1'] ?? ''); ?></textarea>
                </div>

                <!-- Judul Bahasa Inggris -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            Judul dalam Bahasa Inggris <span class="text-orange-500">*</span>
                        </label>
                        <button type="button" id="btnAutoTranslate" class="text-[11px] font-bold text-orange-600 hover:text-orange-700 bg-orange-100/90 hover:bg-orange-200 px-3 py-1 rounded-lg border border-orange-300 transition flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-2xs">
                            <i class="bi bi-translate text-xs"></i>
                            <span id="btnAutoTranslateText">Translate Otomatis (ID &rarr; EN)</span>
                        </button>
                    </div>
                    <div class="relative">
                        <textarea name="judul_en" id="inputJudulEn" rows="3" class="w-full px-4 py-3 rounded-xl border border-orange-200 bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs sm:text-sm font-medium text-slate-900 italic" placeholder="Title in English..." required><?= htmlspecialchars($pendaftaran['judul_en'] ?? ''); ?></textarea>
                        <span id="translateSpinner" class="hidden absolute right-3.5 top-3.5 text-orange-500 pointer-events-none">
                            <i class="bi bi-arrow-repeat animate-spin text-lg"></i>
                        </span>
                    </div>
                </div>

                <!-- Judul Alternatif 2 & 3 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Judul Usulan 2 (Alternatif 1) <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>
                        </label>
                        <textarea name="judul_2" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium text-slate-800" placeholder="Alternatif judul 2..."><?= htmlspecialchars($pendaftaran['judul_2'] ?? ''); ?></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Judul Usulan 3 (Alternatif 2) <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>
                        </label>
                        <textarea name="judul_3" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-xs font-medium text-slate-800" placeholder="Alternatif judul 3..."><?= htmlspecialchars($pendaftaran['judul_3'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Konsentrasi -->
                <div class="space-y-1.5 pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konsentrasi Studi (Otomatis dari Biodata)</label>
                    <div class="relative">
                        <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 text-slate-800 font-semibold text-xs outline-none cursor-not-allowed" value="<?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?>" readonly>
                        <input type="hidden" name="konsentrasi_dkv" value="<?= htmlspecialchars(!empty($mahasiswa['konsentrasi_dkv']) ? $mahasiswa['konsentrasi_dkv'] : ($pendaftaran['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual')); ?>">
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: Berkas Persyaratan PDF (4 Berkas) -->
            <?php
                $note_ksm = !empty($pendaftaran['catatan_file_ksm']) ? $pendaftaran['catatan_file_ksm'] : '';
                $note_trn = !empty($pendaftaran['catatan_file_transkrip']) ? $pendaftaran['catatan_file_transkrip'] : '';
                $note_prn = !empty($pendaftaran['catatan_file_pernyataan']) ? $pendaftaran['catatan_file_pernyataan'] : '';
                $note_lab = !empty($pendaftaran['catatan_file_bebas_lab']) ? $pendaftaran['catatan_file_bebas_lab'] : '';

                $gen_notes = $pendaftaran['catatan_wali'] ?? '';
                if (!empty($gen_notes)) {
                    if (empty($note_ksm) && preg_match('/\[KSM[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_ksm = trim($m[1]);
                    if (empty($note_trn) && preg_match('/\[TRANSKRIP[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_trn = trim($m[1]);
                    if (empty($note_prn) && preg_match('/\[PERNYATAAN[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_prn = trim($m[1]);
                    if (empty($note_lab) && preg_match('/\[BEBAS_LAB[^\]]*\]\s*:\s*([^\n\r]+)/i', $gen_notes, $m)) $note_lab = trim($m[1]);
                }

                $st_ksm = $pendaftaran['status_file_ksm'] ?? 'Pending';
                $st_trn = $pendaftaran['status_file_transkrip'] ?? 'Pending';
                $st_prn = $pendaftaran['status_file_pernyataan'] ?? 'Pending';
                $st_lab = $pendaftaran['status_file_bebas_lab'] ?? 'Pending';
            ?>
            <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-6">
                <div class="flex items-center gap-3.5 pb-4 border-b border-orange-100">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-bold text-base flex items-center justify-center shrink-0 box-3d">
                        03
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 3</span>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Berkas Persyaratan (Format PDF)</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-600 font-normal">
                    Jika tidak ingin mengubah file berkas tertentu, biarkan kolom upload kosong (file lama akan tetap dipertahankan).
                </p>

                <div class="space-y-6">
                    <!-- 1. KSM -->
                    <div class="p-5 rounded-2xl border <?= ($st_ksm === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_ksm === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200 bg-white'); ?> space-y-3.5 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i> 1. KSM Terakhir
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold <?= ($st_ksm === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_ksm === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-0.5 rounded-full border">
                                    <?= ($st_ksm === 'Approved') ? 'Disetujui' : (($st_ksm === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">PDF</span>
                            </div>
                        </div>

                        <?php if(!empty($pendaftaran['file_ksm'])): ?>
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between gap-2">
                                <span class="truncate font-mono font-semibold text-slate-800"><?= htmlspecialchars($pendaftaran['file_ksm']); ?></span>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase shrink-0">Tersimpan</span>
                            </div>
                        <?php endif; ?>

                        <!-- Note / Catatan Dosen Wali -->
                        <?php if(!empty($note_ksm) || $st_ksm === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_ksm === 'Rejected') ? 'bg-rose-100/80 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_ksm === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>">
                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen Wali:
                                </span>
                                <p class="italic text-xs font-medium leading-relaxed">
                                    "<?= !empty($note_ksm) ? htmlspecialchars($note_ksm) : 'Berkas ini belum disetujui, silakan ganti dengan dokumen yang sesuai.'; ?>"
                                </p>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="file_ksm_old" value="<?= htmlspecialchars($pendaftaran['file_ksm'] ?? ''); ?>">
                        <div class="pt-1">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Ganti File KSM (.pdf baru):</label>
                            <input type="file" name="file_ksm" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        </div>
                    </div>

                    <!-- 2. Transkrip Nilai -->
                    <div class="p-5 rounded-2xl border <?= ($st_trn === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_trn === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200 bg-white'); ?> space-y-3.5 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i> 2. Transkrip Nilai
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold <?= ($st_trn === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_trn === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-0.5 rounded-full border">
                                    <?= ($st_trn === 'Approved') ? 'Disetujui' : (($st_trn === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">PDF</span>
                            </div>
                        </div>

                        <?php if(!empty($pendaftaran['file_transkrip'])): ?>
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between gap-2">
                                <span class="truncate font-mono font-semibold text-slate-800"><?= htmlspecialchars($pendaftaran['file_transkrip']); ?></span>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase shrink-0">Tersimpan</span>
                            </div>
                        <?php endif; ?>

                        <!-- Note / Catatan Dosen Wali -->
                        <?php if(!empty($note_trn) || $st_trn === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_trn === 'Rejected') ? 'bg-rose-100/80 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_trn === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>">
                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen Wali:
                                </span>
                                <p class="italic text-xs font-medium leading-relaxed">
                                    "<?= !empty($note_trn) ? htmlspecialchars($note_trn) : 'Berkas ini belum disetujui, silakan ganti dengan dokumen yang sesuai.'; ?>"
                                </p>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="file_transkrip_old" value="<?= htmlspecialchars($pendaftaran['file_transkrip'] ?? ''); ?>">
                        <div class="pt-1">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Ganti File Transkrip (.pdf baru):</label>
                            <input type="file" name="file_transkrip" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        </div>
                    </div>

                    <!-- 3. Surat Pernyataan -->
                    <div class="p-5 rounded-2xl border <?= ($st_prn === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_prn === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200 bg-white'); ?> space-y-3.5 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i> 3. Surat Pernyataan
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold <?= ($st_prn === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_prn === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-0.5 rounded-full border">
                                    <?= ($st_prn === 'Approved') ? 'Disetujui' : (($st_prn === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">PDF</span>
                            </div>
                        </div>

                        <?php if(!empty($pendaftaran['file_pernyataan'])): ?>
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between gap-2">
                                <span class="truncate font-mono font-semibold text-slate-800"><?= htmlspecialchars($pendaftaran['file_pernyataan']); ?></span>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase shrink-0">Tersimpan</span>
                            </div>
                        <?php endif; ?>

                        <!-- Note / Catatan Dosen Wali -->
                        <?php if(!empty($note_prn) || $st_prn === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_prn === 'Rejected') ? 'bg-rose-100/80 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_prn === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>">
                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen Wali:
                                </span>
                                <p class="italic text-xs font-medium leading-relaxed">
                                    "<?= !empty($note_prn) ? htmlspecialchars($note_prn) : 'Berkas ini belum disetujui, silakan ganti dengan dokumen yang sesuai.'; ?>"
                                </p>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="file_pernyataan_old" value="<?= htmlspecialchars($pendaftaran['file_pernyataan'] ?? ''); ?>">
                        <div class="pt-1">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Ganti File Pernyataan (.pdf baru):</label>
                            <input type="file" name="file_pernyataan" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        </div>
                    </div>

                    <!-- 4. Bebas Lab -->
                    <div class="p-5 rounded-2xl border <?= ($st_lab === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_lab === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200 bg-white'); ?> space-y-3.5 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i> 4. Bebas Lab
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold <?= ($st_lab === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_lab === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-0.5 rounded-full border">
                                    <?= ($st_lab === 'Approved') ? 'Disetujui' : (($st_lab === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">PDF</span>
                            </div>
                        </div>

                        <?php if(!empty($pendaftaran['file_bebas_lab'])): ?>
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between gap-2">
                                <span class="truncate font-mono font-semibold text-slate-800"><?= htmlspecialchars($pendaftaran['file_bebas_lab']); ?></span>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase shrink-0">Tersimpan</span>
                            </div>
                        <?php endif; ?>

                        <!-- Note / Catatan Dosen Wali -->
                        <?php if(!empty($note_lab) || $st_lab === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_lab === 'Rejected') ? 'bg-rose-100/80 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_lab === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>">
                                    <i class="bi bi-chat-left-dots-fill"></i> Catatan Dosen Wali:
                                </span>
                                <p class="italic text-xs font-medium leading-relaxed">
                                    "<?= !empty($note_lab) ? htmlspecialchars($note_lab) : 'Berkas ini belum disetujui, silakan ganti dengan dokumen yang sesuai.'; ?>"
                                </p>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="file_bebas_lab_old" value="<?= htmlspecialchars($pendaftaran['file_bebas_lab'] ?? ''); ?>">
                        <div class="pt-1">
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Ganti File Bebas Lab (.pdf baru):</label>
                            <input type="file" name="file_bebas_lab" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            </fieldset>

            <!-- Bottom Submit & Cancel Bar -->
            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 pb-12 border-t border-orange-200/70">
                <a href="<?= site_url('mahasiswa'); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 shadow-2xs transition box-3d">
                    <i class="bi bi-arrow-left text-base"></i> Kembali ke Dashboard
                </a>

                <?php if(!empty($is_locked)): ?>
                    <button type="button" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs border border-slate-300 shadow-none cursor-not-allowed select-none" disabled>
                        <i class="bi bi-lock-fill text-base"></i> Formulir Terkunci (Sedang Ditinjau)
                    </button>
                <?php else: ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg transition box-3d cursor-pointer hover:scale-105 active:scale-95">
                        <i class="bi bi-check-circle-fill text-base"></i> Simpan Perubahan Pendaftaran
                    </button>
                <?php endif; ?>
            </div>

        </form>

    </main>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script>
    // Radio buttons styling update
    document.querySelectorAll('.jenis-radio-label input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.jenis-radio-label').forEach(lbl => {
                lbl.classList.remove('border-orange-500', 'bg-orange-50/70', 'shadow-xs');
                lbl.classList.add('border-slate-200', 'bg-white');
            });
            if (this.checked) {
                const parent = this.closest('.jenis-radio-label');
                parent.classList.add('border-orange-500', 'bg-orange-50/70', 'shadow-xs');
                parent.classList.remove('border-slate-200', 'bg-white');
            }
        });
    });

    // Auto Translate ID -> EN
    const btnAutoTranslate = document.getElementById('btnAutoTranslate');
    const inputJudul1 = document.getElementById('inputJudul1');
    const inputJudulEn = document.getElementById('inputJudulEn');
    const translateSpinner = document.getElementById('translateSpinner');
    const btnAutoTranslateText = document.getElementById('btnAutoTranslateText');

    if (btnAutoTranslate && inputJudul1 && inputJudulEn) {
        btnAutoTranslate.addEventListener('click', function() {
            const textId = inputJudul1.value.trim();
            if (!textId) {
                showToast('Isi judul utama Bahasa Indonesia terlebih dahulu!');
                inputJudul1.focus();
                return;
            }

            if (translateSpinner) translateSpinner.classList.remove('hidden');
            if (btnAutoTranslateText) btnAutoTranslateText.textContent = 'Menerjemahkan...';
            btnAutoTranslate.disabled = true;

            const formData = new FormData();
            formData.append('text', textId);

            fetch('<?= site_url("mahasiswa/translate_judul"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    inputJudulEn.value = data.translated;
                } else {
                    showToast(data.message || 'Gagal menerjemahkan judul.');
                }
            })
            .catch(() => {
                showToast('Koneksi terjemahan gagal, silakan isi manual.');
            })
            .finally(() => {
                if (translateSpinner) translateSpinner.classList.add('hidden');
                if (btnAutoTranslateText) btnAutoTranslateText.textContent = 'Translate Otomatis (ID → EN)';
                btnAutoTranslate.disabled = false;
            });
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('inPageToastAlert');
        const toastMsg = document.getElementById('toastAlertMessage');
        if (toast && toastMsg) {
            toastMsg.textContent = msg;
            toast.classList.remove('hidden', 'opacity-0', 'translate-y-[-20px]');
            toast.classList.add('opacity-100', 'translate-y-0');
            setTimeout(hideToast, 4000);
        }
    }

    function hideToast() {
        const toast = document.getElementById('inPageToastAlert');
        if (toast) {
            toast.classList.add('opacity-0', 'translate-y-[-20px]');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }
    }
    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
