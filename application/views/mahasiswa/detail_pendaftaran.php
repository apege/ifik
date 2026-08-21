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
    <style>
        @media print {
            header, footer, .no-print, #pdfModalPreview, #customCursorDot, #customCursorRing {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .card-3d-warm, .card-3d-orange {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- Main Content Container (Continuous Vertical Long-Page Layout) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-8 flex-grow space-y-8 max-w-7xl mx-auto">

        <!-- Top Header & Action Breadcrumbs -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-orange-200/70 pb-5">
            <div class="flex items-center gap-4">
                <a href="<?= site_url('mahasiswa'); ?>" class="w-10 h-10 rounded-xl bg-white hover:bg-orange-100/80 text-slate-700 hover:text-orange-600 border border-orange-200 flex items-center justify-center transition shadow-2xs box-3d" title="Kembali ke Dashboard">
                    <i class="bi bi-arrow-left text-lg font-bold"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-600">BERKAS PENDAFTARAN</span>
                        <span class="text-[10px] bg-orange-100 text-orange-800 font-bold px-2.5 py-0.5 rounded-full border border-orange-200">TA 2025/2026</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                        Detail Lengkap Pengajuan Tugas Akhir
                    </h1>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3 no-print">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-2xs transition cursor-pointer">
                    <i class="bi bi-printer-fill text-sm"></i> Cetak / Simpan PDF
                </button>
                <a href="<?= site_url('mahasiswa/edit_pendaftaran'); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold text-xs shadow-md hover:from-orange-700 hover:to-amber-700 transition box-3d">
                    <i class="bi bi-pencil-square text-sm"></i> Ubah Formulir Data
                </a>
            </div>
        </div>

        <!-- Overall Status Banner & Approval Workflow -->
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

            $is_fully_approved = ($approved_count === 4);
            $has_rejection = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected');
        ?>

        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl <?= $is_fully_approved ? 'bg-emerald-500 text-white' : ($has_rejection ? 'bg-rose-500 text-white' : 'bg-gradient-to-tr from-orange-500 to-amber-400 text-white'); ?> flex items-center justify-center text-2xl font-bold box-3d shadow-md">
                        <i class="bi <?= $is_fully_approved ? 'bi-patch-check-fill' : ($has_rejection ? 'bi-exclamation-octagon-fill' : 'bi-clock-history'); ?>"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block">STATUS KESELURUHAN</span>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900">
                            <?php if($is_fully_approved): ?>
                                <span class="text-emerald-600">Pengajuan Tugas Akhir Disetujui Penuh (100%)</span>
                            <?php elseif($has_rejection): ?>
                                <span class="text-rose-600">Terdapat Tahapan yang Perlu Revisi / Ditolak</span>
                            <?php else: ?>
                                <span class="text-slate-800">Sedang Dalam Tahap Persetujuan Berjenjang (<?= $approved_count; ?>/4 Disetujui)</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-4 py-2 rounded-full border <?= $is_fully_approved ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($has_rejection ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-amber-100 text-amber-900 border-amber-300'); ?> badge-3d">
                        <i class="bi bi-circle-fill text-[8px] mr-1.5 <?= $is_fully_approved ? 'text-emerald-500' : ($has_rejection ? 'text-rose-500' : 'text-amber-500 animate-pulse'); ?>"></i>
                        <?= $is_fully_approved ? 'Pendaftaran Selesai' : ($has_rejection ? 'Status: Revisi' : 'Tahap: ' . ($pendaftaran['current_stage'] ?? 'Dosen Wali')); ?>
                    </span>
                </div>
            </div>

            <!-- 4-Stage Horizontal Workflow Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                <!-- Tahap 1: Dosen Wali -->
                <div class="p-4 rounded-xl border <?= ($w_status === 'Approved') ? 'bg-emerald-50/70 border-emerald-300' : (($w_status === 'Rejected') ? 'bg-rose-50/70 border-rose-300' : 'bg-orange-50/70 border-orange-300'); ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tahap 01</span>
                        <i class="bi <?= ($w_status === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : (($w_status === 'Rejected') ? 'bi-x-circle-fill text-rose-600' : 'bi-clock-fill text-orange-500'); ?> text-base"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-900">Dosen Wali</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Persetujuan usulan judul</p>
                    <div class="mt-2.5">
                        <span class="inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-md border <?= ($w_status === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : (($w_status === 'Rejected') ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-orange-200/80 text-orange-900 border-orange-300'); ?>">
                            <?= $w_status; ?>
                        </span>
                    </div>
                </div>

                <!-- Tahap 2: Admin Layanan -->
                <div class="p-4 rounded-xl border <?= ($a_status === 'Approved') ? 'bg-emerald-50/70 border-emerald-300' : (($a_status === 'Rejected') ? 'bg-rose-50/70 border-rose-300' : 'bg-slate-50 border-slate-200'); ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tahap 02</span>
                        <i class="bi <?= ($a_status === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : (($a_status === 'Rejected') ? 'bi-x-circle-fill text-rose-600' : 'bi-clock-fill text-slate-400'); ?> text-base"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-900">Admin Layanan</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Verifikasi kelengkapan PDF</p>
                    <div class="mt-2.5">
                        <span class="inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-md border <?= ($a_status === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : (($a_status === 'Rejected') ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-slate-200 text-slate-700 border-slate-300'); ?>">
                            <?= $a_status; ?>
                        </span>
                    </div>
                </div>

                <!-- Tahap 3: Koordinator TA -->
                <div class="p-4 rounded-xl border <?= ($k_status === 'Approved') ? 'bg-emerald-50/70 border-emerald-300' : (($k_status === 'Rejected') ? 'bg-rose-50/70 border-rose-300' : 'bg-slate-50 border-slate-200'); ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tahap 03</span>
                        <i class="bi <?= ($k_status === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : (($k_status === 'Rejected') ? 'bi-x-circle-fill text-rose-600' : 'bi-clock-fill text-slate-400'); ?> text-base"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-900">Koordinator TA</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Validasi topik & kuota</p>
                    <div class="mt-2.5">
                        <span class="inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-md border <?= ($k_status === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : (($k_status === 'Rejected') ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-slate-200 text-slate-700 border-slate-300'); ?>">
                            <?= $k_status; ?>
                        </span>
                    </div>
                </div>

                <!-- Tahap 4: Ketua KK -->
                <div class="p-4 rounded-xl border <?= ($kk_status === 'Approved') ? 'bg-emerald-50/70 border-emerald-300' : (($kk_status === 'Rejected') ? 'bg-rose-50/70 border-rose-300' : 'bg-slate-50 border-slate-200'); ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tahap 04</span>
                        <i class="bi <?= ($kk_status === 'Approved') ? 'bi-check-circle-fill text-emerald-600' : (($kk_status === 'Rejected') ? 'bi-x-circle-fill text-rose-600' : 'bi-clock-fill text-slate-400'); ?> text-base"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-900">Ketua KK</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Persetujuan akhir pembimbing</p>
                    <div class="mt-2.5">
                        <span class="inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-md border <?= ($kk_status === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : (($kk_status === 'Rejected') ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-slate-200 text-slate-700 border-slate-300'); ?>">
                            <?= $kk_status; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Data Mahasiswa & Informasi Akademik -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-orange-100">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg box-3d">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 1</span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Data Identitas Pengusul</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
                <div class="p-4 rounded-xl bg-orange-50/50 border border-orange-200/80 space-y-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap Mahasiswa</span>
                    <p class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars(($mahasiswa['nama_depan'] ?? '') . ' ' . ($mahasiswa['nama_belakang'] ?? '')); ?></p>
                </div>

                <div class="p-4 rounded-xl bg-orange-50/50 border border-orange-200/80 space-y-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nomor Induk Mahasiswa (NIM)</span>
                    <p class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($mahasiswa['nim'] ?? '1301210001'); ?></p>
                </div>

                <div class="p-4 rounded-xl bg-orange-50/50 border border-orange-200/80 space-y-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Program Studi</span>
                    <p class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($mahasiswa['prodi'] ?? 'S1 Desain Komunikasi Visual'); ?></p>
                </div>

                <div class="p-4 rounded-xl bg-orange-50/50 border border-orange-200/80 space-y-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Peminatan / Konsentrasi</span>
                    <p class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($pendaftaran['konsentrasi_dkv'] ?? ($mahasiswa['konsentrasi_dkv'] ?? 'Desain Grafis')); ?></p>
                </div>

                <div class="p-4 rounded-xl bg-orange-50/50 border border-orange-200/80 space-y-1 sm:col-span-2">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Alamat Domisili &amp; Lokasi</span>
                    <p class="text-xs font-semibold text-slate-800 flex items-start gap-2 mt-0.5">
                        <i class="bi bi-geo-alt-fill text-orange-500 text-sm shrink-0"></i>
                        <span><?= htmlspecialchars($mahasiswa['alamat'] ?? 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung, Jawa Barat'); ?></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 2: Pilihan & Karakteristik Tugas Akhir -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-orange-100">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg box-3d">
                    <i class="bi bi-tag-fill"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 2</span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Jenis &amp; Kategori Tugas Akhir</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs space-y-1.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Tugas Akhir Terpilih</span>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($pendaftaran['jenis_ta'] ?? 'Proyek Akhir'); ?></span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs space-y-1.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Pengajuan</span>
                    <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                        <i class="bi bi-calendar-event text-orange-500"></i>
                        <span><?= !empty($pendaftaran['created_at']) ? date('d F Y (H:i)', strtotime($pendaftaran['created_at'])) : date('d F Y'); ?></span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs space-y-1.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dosen Wali Pembimbing</span>
                    <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                        <i class="bi bi-person-badge text-orange-500"></i>
                        <span><?= htmlspecialchars($pendaftaran['nama_dosen_wali'] ?? 'Dosen Wali Akademik'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Usulan Judul Tugas Akhir (Lengkap & Terstruktur) -->
        <?php
            $st_j = $pendaftaran['status_judul'] ?? 'Pending';
            $note_j = $pendaftaran['catatan_judul'] ?? '';
        ?>
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-orange-100 flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg box-3d">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 3</span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Usulan Judul Tugas Akhir</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold <?= ($st_j === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (($st_j === 'Rejected') ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-amber-100 text-amber-800 border-amber-300'); ?> px-3 py-1 rounded-full border">
                        <?= ($st_j === 'Approved') ? '✅ Judul Disetujui' : (($st_j === 'Rejected') ? '❌ Judul Ditolak / Perlu Revisi' : '⏳ Menunggu Review Judul'); ?>
                    </span>
                </div>
            </div>

            <!-- Saran / Catatan Revisi Judul dari Dosen Wali (Jika Ada) -->
            <?php if(!empty($note_j) || $st_j === 'Rejected'): ?>
                <div class="p-4 rounded-2xl <?= ($st_j === 'Rejected') ? 'bg-rose-100/80 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1.5 shadow-2xs">
                    <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_j === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>">
                        <i class="bi bi-chat-left-dots-fill"></i> Saran / Catatan Dosen Wali:
                    </span>
                    <p class="italic text-xs sm:text-sm font-medium leading-relaxed">
                        "<?= !empty($note_j) ? htmlspecialchars($note_j) : 'Judul ini perlu diperbaiki sesuai arahan dosen.'; ?>"
                    </p>
                </div>
            <?php endif; ?>

            <!-- Judul Utama (Bahasa Indonesia) Highlight Card -->
            <div class="p-5 sm:p-6 rounded-2xl <?= ($st_j === 'Approved') ? 'bg-emerald-50/50 border-2 border-emerald-400 ring-2 ring-emerald-300/30' : 'bg-gradient-to-br from-orange-500/10 via-amber-500/5 to-transparent border-2 border-orange-300/80'; ?> shadow-xs relative overflow-hidden space-y-2">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 <?= ($st_j === 'Approved') ? 'bg-emerald-600' : 'bg-orange-600'; ?> text-white rounded-lg text-[10px] font-extrabold uppercase tracking-wider box-3d">
                            USULAN UTAMA 1 (PRIORITAS)
                        </span>
                        <span class="text-[11px] font-semibold text-orange-700 bg-orange-100 px-2 py-0.5 rounded-md border border-orange-200">
                            Bahasa Indonesia
                        </span>
                    </div>
                </div>
                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">
                    <?= htmlspecialchars($pendaftaran['judul_1'] ?? 'Belum ada usulan judul utama.'); ?>
                </h3>
            </div>

            <!-- Judul Bahasa Inggris (English Translation) -->
            <?php if(!empty($pendaftaran['judul_en'])): ?>
                <div class="p-5 rounded-2xl bg-sky-50/70 border border-sky-200 shadow-2xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-sky-600 text-white rounded-lg text-[10px] font-extrabold uppercase tracking-wider box-3d">
                            TRANSLATION IN ENGLISH
                        </span>
                        <span class="text-[11px] font-semibold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-md border border-sky-200 flex items-center gap-1">
                            <i class="bi bi-translate"></i> Bahasa Inggris
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-bold text-slate-800 italic leading-snug">
                        "<?= htmlspecialchars($pendaftaran['judul_en']); ?>"
                    </p>
                </div>
            <?php endif; ?>

            <!-- Judul Usulan Alternatif 2 & 3 (Jika Ada) -->
            <?php if(!empty($pendaftaran['judul_2']) || !empty($pendaftaran['judul_3'])): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <?php if(!empty($pendaftaran['judul_2'])): ?>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider block">Judul Usulan 2 (Alternatif 1)</span>
                            <p class="font-bold text-xs sm:text-sm text-slate-800 leading-relaxed">
                                <?= htmlspecialchars($pendaftaran['judul_2']); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($pendaftaran['judul_3'])): ?>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-2xs space-y-1">
                            <span class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider block">Judul Usulan 3 (Alternatif 2)</span>
                            <p class="font-bold text-xs sm:text-sm text-slate-800 leading-relaxed">
                                <?= htmlspecialchars($pendaftaran['judul_3']); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Section 4: Berkas & Dokumen Persyaratan (PDF Viewer Ready) -->
        <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-orange-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg box-3d">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">BAGIAN 4</span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Berkas &amp; Dokumen Persyaratan (PDF)</h2>
                    </div>
                </div>
                <span class="text-[11px] font-bold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full border border-emerald-300">
                    4 Dokumen Wajib
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- 1. KSM -->
                <?php 
                    $file_ksm = $pendaftaran['file_ksm'] ?? '';
                    $has_ksm = !empty($file_ksm);
                    $url_ksm = $has_ksm ? base_url('uploads/persyaratan_ta/' . $file_ksm) : '#';
                    $st_ksm = $pendaftaran['status_file_ksm'] ?? 'Pending';
                    $note_ksm = $pendaftaran['catatan_file_ksm'] ?? '';
                    if (empty($note_ksm) && !empty($pendaftaran['catatan_wali']) && preg_match('/\[KSM[^\]]*\]\s*:\s*([^\n\r]+)/i', $pendaftaran['catatan_wali'], $m)) $note_ksm = trim($m[1]);
                ?>
                <div class="p-5 rounded-2xl border <?= ($st_ksm === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_ksm === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200/90 bg-white'); ?> shadow-2xs transition-all flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl <?= ($st_ksm === 'Approved') ? 'bg-emerald-500 text-white' : (($st_ksm === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-600'); ?> flex items-center justify-center text-xl font-bold box-3d shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Dokumen 01</span>
                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">Kartu Studi Mahasiswa (KSM)</h4>
                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                        <?= $has_ksm ? htmlspecialchars($file_ksm) : 'Belum diunggah'; ?>
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold <?= ($st_ksm === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_ksm === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-1 rounded-full border shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($st_ksm === 'Approved') ? 'bg-emerald-500' : (($st_ksm === 'Rejected') ? 'bg-rose-500' : 'bg-amber-500'); ?>"></span>
                                <?= ($st_ksm === 'Approved') ? 'Disetujui' : (($st_ksm === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                            </span>
                        </div>

                        <?php if(!empty($note_ksm) || $st_ksm === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_ksm === 'Rejected') ? 'bg-rose-100/70 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_ksm === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>"><i class="bi bi-chat-left-dots-fill"></i> Catatan Review Dosen:</span>
                                <p class="italic text-xs font-medium">"<?= !empty($note_ksm) ? htmlspecialchars($note_ksm) : 'Berkas perlu diperbaiki.'; ?>"</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <?php if($has_ksm): ?>
                            <button type="button" onclick="openPdfPreview('<?= $url_ksm; ?>', 'Kartu Studi Mahasiswa (KSM)', '<?= htmlspecialchars($file_ksm); ?>')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs transition border border-sky-200 cursor-pointer">
                                <i class="bi bi-eye-fill"></i> Lihat Dokumen
                            </button>
                            <a href="<?= $url_ksm; ?>" download class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">File belum tersedia</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. Transkrip Nilai -->
                <?php 
                    $file_transkrip = $pendaftaran['file_transkrip'] ?? '';
                    $has_transkrip = !empty($file_transkrip);
                    $url_transkrip = $has_transkrip ? base_url('uploads/persyaratan_ta/' . $file_transkrip) : '#';
                    $st_trn = $pendaftaran['status_file_transkrip'] ?? 'Pending';
                    $note_trn = $pendaftaran['catatan_file_transkrip'] ?? '';
                    if (empty($note_trn) && !empty($pendaftaran['catatan_wali']) && preg_match('/\[TRANSKRIP[^\]]*\]\s*:\s*([^\n\r]+)/i', $pendaftaran['catatan_wali'], $m)) $note_trn = trim($m[1]);
                ?>
                <div class="p-5 rounded-2xl border <?= ($st_trn === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_trn === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200/90 bg-white'); ?> shadow-2xs transition-all flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl <?= ($st_trn === 'Approved') ? 'bg-emerald-500 text-white' : (($st_trn === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-600'); ?> flex items-center justify-center text-xl font-bold box-3d shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Dokumen 02</span>
                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">Transkrip Nilai Akademik</h4>
                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                        <?= $has_transkrip ? htmlspecialchars($file_transkrip) : 'Belum diunggah'; ?>
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold <?= ($st_trn === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_trn === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-1 rounded-full border shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($st_trn === 'Approved') ? 'bg-emerald-500' : (($st_trn === 'Rejected') ? 'bg-rose-500' : 'bg-amber-500'); ?>"></span>
                                <?= ($st_trn === 'Approved') ? 'Disetujui' : (($st_trn === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                            </span>
                        </div>

                        <?php if(!empty($note_trn) || $st_trn === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_trn === 'Rejected') ? 'bg-rose-100/70 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_trn === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>"><i class="bi bi-chat-left-dots-fill"></i> Catatan Review Dosen:</span>
                                <p class="italic text-xs font-medium">"<?= !empty($note_trn) ? htmlspecialchars($note_trn) : 'Berkas perlu diperbaiki.'; ?>"</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <?php if($has_transkrip): ?>
                            <button type="button" onclick="openPdfPreview('<?= $url_transkrip; ?>', 'Transkrip Nilai Akademik', '<?= htmlspecialchars($file_transkrip); ?>')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs transition border border-sky-200 cursor-pointer">
                                <i class="bi bi-eye-fill"></i> Lihat Dokumen
                            </button>
                            <a href="<?= $url_transkrip; ?>" download class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">File belum tersedia</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3. Surat Pernyataan -->
                <?php 
                    $file_pernyataan = $pendaftaran['file_pernyataan'] ?? '';
                    $has_pernyataan = !empty($file_pernyataan);
                    $url_pernyataan = $has_pernyataan ? base_url('uploads/persyaratan_ta/' . $file_pernyataan) : '#';
                    $st_prn = $pendaftaran['status_file_pernyataan'] ?? 'Pending';
                    $note_prn = $pendaftaran['catatan_file_pernyataan'] ?? '';
                    if (empty($note_prn) && !empty($pendaftaran['catatan_wali']) && preg_match('/\[PERNYATAAN[^\]]*\]\s*:\s*([^\n\r]+)/i', $pendaftaran['catatan_wali'], $m)) $note_prn = trim($m[1]);
                ?>
                <div class="p-5 rounded-2xl border <?= ($st_prn === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_prn === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200/90 bg-white'); ?> shadow-2xs transition-all flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl <?= ($st_prn === 'Approved') ? 'bg-emerald-500 text-white' : (($st_prn === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-600'); ?> flex items-center justify-center text-xl font-bold box-3d shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Dokumen 03</span>
                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">Surat Pernyataan Keaslian</h4>
                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                        <?= $has_pernyataan ? htmlspecialchars($file_pernyataan) : 'Belum diunggah'; ?>
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold <?= ($st_prn === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_prn === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-1 rounded-full border shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($st_prn === 'Approved') ? 'bg-emerald-500' : (($st_prn === 'Rejected') ? 'bg-rose-500' : 'bg-amber-500'); ?>"></span>
                                <?= ($st_prn === 'Approved') ? 'Disetujui' : (($st_prn === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                            </span>
                        </div>

                        <?php if(!empty($note_prn) || $st_prn === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_prn === 'Rejected') ? 'bg-rose-100/70 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_prn === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>"><i class="bi bi-chat-left-dots-fill"></i> Catatan Review Dosen:</span>
                                <p class="italic text-xs font-medium">"<?= !empty($note_prn) ? htmlspecialchars($note_prn) : 'Berkas perlu diperbaiki.'; ?>"</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <?php if($has_pernyataan): ?>
                            <button type="button" onclick="openPdfPreview('<?= $url_pernyataan; ?>', 'Surat Pernyataan Keaslian', '<?= htmlspecialchars($file_pernyataan); ?>')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs transition border border-sky-200 cursor-pointer">
                                <i class="bi bi-eye-fill"></i> Lihat Dokumen
                            </button>
                            <a href="<?= $url_pernyataan; ?>" download class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">File belum tersedia</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 4. Bebas Lab -->
                <?php 
                    $file_bebas_lab = $pendaftaran['file_bebas_lab'] ?? '';
                    $has_bebas_lab = !empty($file_bebas_lab);
                    $url_bebas_lab = $has_bebas_lab ? base_url('uploads/persyaratan_ta/' . $file_bebas_lab) : '#';
                    $st_lab = $pendaftaran['status_file_bebas_lab'] ?? 'Pending';
                    $note_lab = $pendaftaran['catatan_file_bebas_lab'] ?? '';
                    if (empty($note_lab) && !empty($pendaftaran['catatan_wali']) && preg_match('/\[BEBAS_LAB[^\]]*\]\s*:\s*([^\n\r]+)/i', $pendaftaran['catatan_wali'], $m)) $note_lab = trim($m[1]);
                ?>
                <div class="p-5 rounded-2xl border <?= ($st_lab === 'Rejected') ? 'border-rose-300 bg-rose-50/40 ring-1 ring-rose-200' : (($st_lab === 'Approved') ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200/90 bg-white'); ?> shadow-2xs transition-all flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl <?= ($st_lab === 'Approved') ? 'bg-emerald-500 text-white' : (($st_lab === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-600'); ?> flex items-center justify-center text-xl font-bold box-3d shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Dokumen 04</span>
                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">Surat Bebas Laboratorium</h4>
                                    <p class="text-[11px] text-slate-500 font-mono truncate mt-0.5">
                                        <?= $has_bebas_lab ? htmlspecialchars($file_bebas_lab) : 'Belum diunggah'; ?>
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold <?= ($st_lab === 'Approved') ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : (($st_lab === 'Rejected') ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-amber-800 bg-amber-100 border-amber-300'); ?> px-2.5 py-1 rounded-full border shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($st_lab === 'Approved') ? 'bg-emerald-500' : (($st_lab === 'Rejected') ? 'bg-rose-500' : 'bg-amber-500'); ?>"></span>
                                <?= ($st_lab === 'Approved') ? 'Disetujui' : (($st_lab === 'Rejected') ? 'Ditolak' : 'Pending'); ?>
                            </span>
                        </div>

                        <?php if(!empty($note_lab) || $st_lab === 'Rejected'): ?>
                            <div class="p-3 rounded-xl <?= ($st_lab === 'Rejected') ? 'bg-rose-100/70 border border-rose-200 text-rose-900' : 'bg-amber-50 border border-amber-200 text-amber-900'; ?> text-xs space-y-1">
                                <span class="font-bold text-[10px] uppercase tracking-wider block <?= ($st_lab === 'Rejected') ? 'text-rose-700' : 'text-amber-700'; ?>"><i class="bi bi-chat-left-dots-fill"></i> Catatan Review Dosen:</span>
                                <p class="italic text-xs font-medium">"<?= !empty($note_lab) ? htmlspecialchars($note_lab) : 'Berkas perlu diperbaiki.'; ?>"</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <?php if($has_bebas_lab): ?>
                            <button type="button" onclick="openPdfPreview('<?= $url_bebas_lab; ?>', 'Surat Bebas Laboratorium', '<?= htmlspecialchars($file_bebas_lab); ?>')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs transition border border-sky-200 cursor-pointer">
                                <i class="bi bi-eye-fill"></i> Lihat Dokumen
                            </button>
                            <a href="<?= $url_bebas_lab; ?>" download class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">File belum tersedia</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Catatan Review Dosen Wali / Tim (Jika Ada) -->
        <?php if(!empty($pendaftaran['catatan_wali'])): ?>
            <div class="card-3d-warm card-no-hover rounded-2xl p-6 sm:p-8 border-2 border-amber-300 bg-amber-50/60 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg box-3d">
                        <i class="bi bi-chat-left-text-fill"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">CATATAN &amp; REVISI DOSEN WALI</span>
                        <h3 class="text-base font-extrabold text-slate-900">Umpan Balik / Catatan untuk Mahasiswa</h3>
                    </div>
                </div>
                <div class="p-4 rounded-xl bg-white border border-amber-200 text-xs sm:text-sm font-medium text-slate-800 leading-relaxed">
                    <?= nl2br(htmlspecialchars($pendaftaran['catatan_wali'])); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Bottom Action Buttons Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 pb-10 border-t border-orange-200/70 no-print">
            <a href="<?= site_url('mahasiswa'); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-orange-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-2xs transition box-3d">
                <i class="bi bi-arrow-left text-base"></i> Kembali ke Dashboard
            </a>

            <div class="flex items-center gap-3">
                <a href="<?= site_url('mahasiswa/edit_pendaftaran'); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 text-white font-bold text-xs shadow-md hover:from-orange-700 hover:to-amber-700 transition box-3d">
                    <i class="bi bi-pencil-square text-base"></i> Edit Formulir Data
                </a>
            </div>
        </div>

    </main>

    <!-- PDF Review / Preview Modal -->
    <div id="pdfModalPreview" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4 overflow-y-auto no-print">
        <div class="bg-white rounded-2xl max-w-4xl w-full h-[85vh] flex flex-col shadow-2xl border border-orange-100 overflow-hidden my-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg font-bold box-3d">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" id="previewModalTitle">Preview Dokumen PDF</h3>
                        <span class="text-[10px] text-slate-500 font-mono" id="previewModalFilename">file.pdf</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a id="previewModalDownloadBtn" href="#" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold transition border border-orange-200">
                        <i class="bi bi-download"></i> Unduh
                    </a>
                    <button type="button" onclick="closePdfPreview()" class="text-slate-400 hover:text-slate-700 font-bold text-lg p-1.5 rounded-lg transition cursor-pointer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Iframe) -->
            <div class="flex-grow bg-slate-100 relative">
                <iframe id="previewModalIframe" src="about:blank" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <script>
    function openPdfPreview(url, title, filename) {
        const modal = document.getElementById('pdfModalPreview');
        const modalTitle = document.getElementById('previewModalTitle');
        const modalFilename = document.getElementById('previewModalFilename');
        const modalIframe = document.getElementById('previewModalIframe');
        const downloadBtn = document.getElementById('previewModalDownloadBtn');

        if (modalTitle) modalTitle.textContent = title || 'Preview Dokumen PDF';
        if (modalFilename) modalFilename.textContent = filename || 'document.pdf';
        if (downloadBtn) downloadBtn.href = url || '#';
        if (modalIframe) modalIframe.src = url || 'about:blank';

        if (modal) modal.classList.remove('hidden');
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfModalPreview');
        const modalIframe = document.getElementById('previewModalIframe');
        if (modal) modal.classList.add('hidden');
        if (modalIframe) modalIframe.src = 'about:blank';
    }
    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
