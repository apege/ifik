<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .clean-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-600 selection:text-white">

    <!-- Header Navbar Partial -->
    <?php $this->load->view('partials/app_navbar', [
        'user_role_label'   => 'Pusat Kendali Admin',
        'user_display_name' => 'Super Administrator',
        'user_display_sub'  => 'Fakultas Industri Kreatif'
    ]); ?>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-8">

        <!-- Welcome Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-orange-50 border border-orange-200 rounded-full text-[10px] font-bold text-orange-700 uppercase tracking-wider mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Control Center
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pusat Kendali Admin (Admin Panel)</h1>
                <p class="text-slate-500 text-xs mt-0.5">Akses terpadu seluruh modul manajemen layanan fakultas, validasi berkas TA, publikasi berita, dan fasilitas.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('/'); ?>" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-200 text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                    <i class="bi bi-house-door-fill text-orange-600"></i> Lihat Beranda Utama Website
                </a>
            </div>
        </div>

        <!-- Top Metric Counters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Counter 1 -->
            <div class="clean-card rounded-2xl p-5 hover:border-amber-300 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block mb-1">Perlu Validasi LAA</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 tracking-tight block"><?= $laa_stats['pending']; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">Menunggu Pemeriksaan</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <!-- Counter 2 -->
            <div class="clean-card rounded-2xl p-5 hover:border-emerald-300 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Bimbingan Terbuka</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight block"><?= $ta_unlocked; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">Lolos Approval 4 Tahap</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-unlock-fill"></i>
                </div>
            </div>

            <!-- Counter 3 -->
            <div class="clean-card rounded-2xl p-5 hover:border-slate-300 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Berita &amp; Artikel</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight block"><?= $total_news; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">Total Berita Aktif</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 border border-orange-200 flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-newspaper"></i>
                </div>
            </div>

            <!-- Counter 4 -->
            <div class="clean-card rounded-2xl p-5 hover:border-slate-300 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Peminjaman Ruangan</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight block"><?= $total_booking; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">Total Reservasi Fasilitas</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center text-lg font-bold">
                    <i class="bi bi-calendar2-check"></i>
                </div>
            </div>
        </div>

        <!-- Bento Grid Admin Modules -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-grid-1x2-fill text-orange-600"></i> Modul Manajemen &amp; Layanan Panel
                </h2>
                <span class="text-xs text-slate-500">Pilih modul yang ingin Anda kelola</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

                <!-- Module 1: Admin Layanan (LAA) -->
                <div class="clean-card rounded-2xl p-6 flex flex-col justify-between hover:border-orange-300 transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200 uppercase">
                                Layanan Akademik (LAA)
                            </span>
                            <div class="w-9 h-9 rounded-xl bg-orange-100 border border-orange-200 text-orange-600 flex items-center justify-center text-base">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-orange-600 transition-colors">
                            Validasi Berkas Tugas Akhir
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Pemeriksaan 4 dokumen (KSM, Transkrip, Pernyataan, Bebas Lab), checklist kekurangan, dan approval/pengembalian revisi ke mahasiswa.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-amber-700 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> <?= $laa_stats['pending']; ?> Perlu dicek
                        </span>
                        <a href="<?= site_url('adminlayanan'); ?>" class="px-3.5 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                            Buka Modul LAA <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Module 2: Newsroom Manager & Frame Selector -->
                <div class="clean-card rounded-2xl p-6 flex flex-col justify-between hover:border-orange-300 transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                News &amp; Content
                            </span>
                            <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-base">
                                <i class="bi bi-newspaper"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-orange-600 transition-colors">
                            Admin Newsroom &amp; Desain Frame
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Tambah dan edit berita fakultas, atur durasi auto-scroll, dan pilih template border frame estetis (Spiral Swirl, Batik Geometris, Polaroid, dll).
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-400">
                            <?= $total_news; ?> Berita Aktif
                        </span>
                        <a href="<?= site_url('news/admin'); ?>" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                            Kelola Berita <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Module 3: Booking Ruangan & Fasilitas -->
                <div class="clean-card rounded-2xl p-6 flex flex-col justify-between hover:border-orange-300 transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200 uppercase">
                                Facility &amp; Labs
                            </span>
                            <div class="w-9 h-9 rounded-xl bg-teal-50 border border-teal-200 text-teal-600 flex items-center justify-center text-base">
                                <i class="bi bi-calendar2-range"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-orange-600 transition-colors">
                            Kelola Peminjaman Ruangan
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Reservasi laboratorium, ruang sidang, galeri pameran, serta validasi jadwal bentrok dan approval peminjaman fasilitas.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-400">
                            <?= $total_booking; ?> Peminjaman
                        </span>
                        <a href="<?= site_url('kelolabooking'); ?>" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                            Buka Booking <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Module 4: Ketua Kelompok Keahlian (KK) -->
                <div class="clean-card rounded-2xl p-6 flex flex-col justify-between hover:border-orange-300 transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                4 Kelompok Keahlian
                            </span>
                            <div class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center text-base">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-orange-600 transition-colors">
                            Portal Ketua Kelompok Keahlian
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Persetujuan kepakaran 4 rumpun keahlian (VCM, PDA, IAS, AHC) dan pembukaan gembok akses modul Bimbingan Tugas Akhir.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-400">
                            Tahap 04 Approval
                        </span>
                        <a href="<?= site_url('ketuakk'); ?>" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                            Portal Ketua KK <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Module 5: Koordinator Tugas Akhir -->
                <div class="clean-card rounded-2xl p-6 flex flex-col justify-between hover:border-orange-300 transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                Koordinator Fakultas
                            </span>
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center text-base">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-orange-600 transition-colors">
                            Portal Koordinator TA
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Validasi topik rencana Tugas Akhir mahasiswa tingkat fakultas dan pengaturan kuota dosen pembimbing.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-400">
                            Tahap 03 Approval
                        </span>
                        <a href="<?= site_url('koordinatorta'); ?>" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                            Portal Koor TA <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Module 6: Import Data Email / User -->
                <div class="clean-card rounded-2xl p-6 flex flex-col justify-between hover:border-orange-300 transition-all group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                User &amp; Accounts
                            </span>
                            <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center text-base">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-orange-600 transition-colors">
                            Import &amp; Manajemen User
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Import massal akun mahasiswa dan dosen wali melalui format CSV/Excel serta sinkronisasi data master.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-400">
                            Master Data
                        </span>
                        <a href="<?= site_url('importemail'); ?>" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                            Import Akun <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-8 text-center text-xs text-slate-500">
        &copy; <?= date('Y'); ?> Fakultas Industri Kreatif - Telkom University. Central Admin Control Panel.
    </footer>

</body>
</html>
