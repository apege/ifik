<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Verifikasi Keabsahan Surat — IFIK Telkom University') ?></title>
    
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
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .pulse-emerald {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% {
                transform: scale(0.97);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 14px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.97);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Checkerboard pattern for transparent signature */
        .checkerboard-bg {
            background-color: #ffffff;
            background-image: 
                linear-gradient(45deg, #f8fafc 25%, transparent 25%), 
                linear-gradient(-45deg, #f8fafc 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #f8fafc 75%), 
                linear-gradient(-45deg, transparent 75%, #f8fafc 75%);
            background-size: 10px 10px;
            background-position: 0 0, 0 5px, 5px -5px, -5px 0px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-orange-50/30 to-slate-100 min-h-screen text-slate-800 antialiased py-6 px-4 sm:py-10">

    <div class="max-w-xl mx-auto space-y-6">

        <!-- Top Header: Institution & Verification Title -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 p-2 flex items-center justify-center shrink-0">
                <img src="<?= base_url('assets/img/telu.png') ?>" alt="Telkom University Logo" class="max-h-full max-w-full object-contain" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/0/03/Logo_Telkom_University_potrait.png'">
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider uppercase bg-orange-100 text-orange-700 mb-1">
                    <i class="bi bi-shield-check"></i>
                    E-Verify Document Portal
                </span>
                <h1 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">
                    Fakultas Industri Kreatif
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Unit Laboratorium & Fasilitas Akademik • Telkom University
                </p>
            </div>
        </div>

        <?php if ($status_verifikasi === 'NOT_FOUND' || !$booking): ?>
            <!-- CARD: NOT FOUND -->
            <div class="bg-white rounded-3xl p-8 text-center border border-rose-200 shadow-sm space-y-4">
                <div class="w-16 h-16 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-3xl">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-rose-950">Dokumen Tidak Ditemukan</h2>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Kode dokumen atau nomor surat yang Anda pindai tidak terdaftar dalam basis data resmi Laboratorium IFIK.
                    </p>
                </div>
                <div class="pt-4">
                    <a href="<?= site_url(); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition-all">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

        <?php else: ?>

            <!-- STATUS BADGE BANNER -->
            <?php if ($status_verifikasi === 'VALID'): ?>
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-3xl p-6 shadow-md shadow-emerald-600/20 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white text-emerald-600 flex items-center justify-center text-3xl shrink-0 shadow-sm pulse-emerald">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <span class="inline-block text-[11px] font-extrabold uppercase tracking-widest text-emerald-100">Hasil Verifikasi Keabsahan</span>
                            <h2 class="text-lg sm:text-xl font-extrabold text-white tracking-tight">
                                RESMI DISETUJUI (VALID)
                            </h2>
                            <p class="text-xs text-emerald-50 mt-1 leading-snug">
                                Dokumen surat peminjaman ini sah, otentik, dan diterbitkan oleh otoritas resmi Laboratorium IFIK.
                            </p>
                        </div>
                    </div>
                </div>
            <?php elseif ($status_verifikasi === 'PENDING'): ?>
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-3xl p-6 shadow-md shadow-amber-500/20">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white text-amber-500 flex items-center justify-center text-3xl shrink-0 shadow-sm">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <span class="inline-block text-[11px] font-extrabold uppercase tracking-widest text-amber-100">Hasil Verifikasi</span>
                            <h2 class="text-lg sm:text-xl font-extrabold text-white">MENUNGGU PERSETUJUAN</h2>
                            <p class="text-xs text-amber-50 mt-1">Permohonan peminjaman ini sedang dalam proses verifikasi petugas.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-gradient-to-r from-rose-600 to-red-600 text-white rounded-3xl p-6 shadow-md shadow-rose-600/20">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white text-rose-600 flex items-center justify-center text-3xl shrink-0 shadow-sm">
                            <i class="bi bi-slash-circle"></i>
                        </div>
                        <div>
                            <span class="inline-block text-[11px] font-extrabold uppercase tracking-widest text-rose-100">Hasil Verifikasi</span>
                            <h2 class="text-lg sm:text-xl font-extrabold text-white">PERMOHONAN DITOLAK</h2>
                            <p class="text-xs text-rose-50 mt-1">Permohonan peminjaman ini tidak disetujui / dibatalkan.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- MAIN DETAIL CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200/80 space-y-6">

                <!-- Header Document Info -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nomor Registrasi Surat</p>
                        <p class="text-sm sm:text-base font-extrabold text-slate-800 font-mono tracking-tight"><?= htmlspecialchars($nomor_surat); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tanggal Pengesahan</p>
                        <p class="text-xs sm:text-sm font-bold text-slate-700"><?= date('d F Y', strtotime($booking->updated_at ?? $booking->created_at)); ?></p>
                    </div>
                </div>

                <!-- Info Grid List -->
                <div class="space-y-3.5 text-xs sm:text-sm">
                    
                    <div class="flex items-start justify-between py-2 border-b border-slate-50 gap-4">
                        <span class="text-slate-500 font-medium shrink-0 flex items-center gap-2">
                            <i class="bi bi-person-fill text-orange-500 text-sm"></i>
                            Nama Peminjam
                        </span>
                        <span class="font-bold text-slate-800 text-right"><?= htmlspecialchars($booking->nama_lengkap); ?></span>
                    </div>

                    <div class="flex items-start justify-between py-2 border-b border-slate-50 gap-4">
                        <span class="text-slate-500 font-medium shrink-0 flex items-center gap-2">
                            <i class="bi bi-door-open-fill text-orange-500 text-sm"></i>
                            Ruangan / Lab
                        </span>
                        <span class="font-bold text-slate-800 text-right">
                            <?= htmlspecialchars(($booking->kode_ruangan ? $booking->kode_ruangan . ' - ' : '') . $booking->nama_ruangan); ?>
                        </span>
                    </div>

                    <div class="flex items-start justify-between py-2 border-b border-slate-50 gap-4">
                        <span class="text-slate-500 font-medium shrink-0 flex items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-orange-500 text-sm"></i>
                            Lokasi Fasilitas
                        </span>
                        <span class="font-bold text-slate-700 text-right"><?= htmlspecialchars($booking->lokasi ?: 'Gedung Industri Kreatif (Sebatik)'); ?></span>
                    </div>

                    <div class="flex items-start justify-between py-2 border-b border-slate-50 gap-4">
                        <span class="text-slate-500 font-medium shrink-0 flex items-center gap-2">
                            <i class="bi bi-calendar-event-fill text-orange-500 text-sm"></i>
                            Tanggal Pelaksanaan
                        </span>
                        <span class="font-bold text-slate-800 text-right">
                            <?= ($booking->tanggal_mulai === $booking->tanggal_selesai) ? date('d F Y', strtotime($booking->tanggal_mulai)) : date('d F Y', strtotime($booking->tanggal_mulai)) . ' s/d ' . date('d F Y', strtotime($booking->tanggal_selesai)); ?>
                        </span>
                    </div>

                    <div class="flex items-start justify-between py-2 border-b border-slate-50 gap-4">
                        <span class="text-slate-500 font-medium shrink-0 flex items-center gap-2">
                            <i class="bi bi-clock-fill text-orange-500 text-sm"></i>
                            Waktu Peminjaman
                        </span>
                        <span class="font-bold text-slate-800 text-right font-mono">
                            <?= substr($booking->jam_mulai, 0, 5); ?> - <?= substr($booking->jam_selesai, 0, 5); ?> WIB
                        </span>
                    </div>

                    <div class="flex items-start justify-between py-2 border-b border-slate-50 gap-4">
                        <span class="text-slate-500 font-medium shrink-0 flex items-center gap-2">
                            <i class="bi bi-card-text text-orange-500 text-sm"></i>
                            Agenda / Keperluan
                        </span>
                        <span class="font-semibold text-slate-700 text-right"><?= htmlspecialchars($booking->keterangan ?: '-'); ?></span>
                    </div>

                </div>

                <!-- SIGNER INFO & DIGITAL SIGNATURE BOX -->
                <div class="mt-6 pt-5 border-t border-slate-200">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">
                        Otorisasi & Pengesahan Digital
                    </p>
                    
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 mb-1">
                                <?= htmlspecialchars($penandatangan['jabatan'] ?? 'Petugas Berwenang'); ?>
                            </span>
                            <h4 class="text-sm font-extrabold text-slate-800"><?= htmlspecialchars($penandatangan['nama'] ?? 'Kaur / Ka. Lab FIK'); ?></h4>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono">NIP: <?= htmlspecialchars($penandatangan['nip'] ?? '-'); ?></p>
                        </div>

                        <!-- Signature Image Box -->
                        <div class="w-40 h-20 checkerboard-bg rounded-xl border border-slate-200 p-2 flex items-center justify-center shrink-0">
                            <?php if (!empty($penandatangan['tanda_tangan']) && file_exists(FCPATH . 'uploads/signatures/' . $penandatangan['tanda_tangan'])): ?>
                                <img src="<?= base_url('uploads/signatures/' . $penandatangan['tanda_tangan']); ?>" 
                                     alt="Tanda Tangan Digital" 
                                     class="max-h-16 max-w-full object-contain filter drop-shadow-xs">
                            <?php else: ?>
                                <span class="text-[10px] text-slate-400 font-semibold italic text-center">Tanda Tangan Terotentikasi Sistem</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <a href="<?= site_url('verifikasi/surat/cetak/' . $booking->id); ?>" target="_blank" 
                       class="flex-1 py-3 px-4 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm text-center shadow-md shadow-orange-600/25 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-file-earmark-pdf-fill text-base"></i>
                        <span>Lihat Dokumen Surat Asli (PDF)</span>
                    </a>
                    
                    <button type="button" onclick="window.print()" 
                            class="py-3 px-5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-printer-fill"></i>
                        <span>Cetak Ringkasan</span>
                    </button>
                </div>

            </div>

        <?php endif; ?>

        <!-- Footer Notice -->
        <div class="text-center text-[11px] text-slate-400 py-2 space-y-1">
            <p>Sistem Informasi Pengelolaan & Peminjaman Laboratorium Fakultas Industri Kreatif (IFIK)</p>
            <p>© <?= date('Y'); ?> Telkom University Bandung. All rights reserved.</p>
        </div>

    </div>

</body>
</html>
