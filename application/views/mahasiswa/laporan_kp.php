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
            $has_laporan    = !empty($wf['has_uploaded_laporan']);
            $is_app_laporan = !empty($wf['laporan_is_approved']);
            $is_rej_laporan = !empty($wf['laporan_is_rejected']);
            $pembimbing1    = $wf['pembimbing1_nama'] ?: ($kp['dosen_pembimbing1'] ?? '');
        ?>

        <main class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-grow space-y-6">

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

            <!-- HERO CARD (Seperti Bimbingan TA) -->
            <div class="card-3d-orange rounded-3xl p-5 sm:p-6 relative overflow-hidden w-full shadow-xl text-white">
                <div class="absolute inset-0 bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#d97706] z-0"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-[11px] font-bold">
                            <i class="bi bi-journal-check text-amber-200"></i> Modul Laporan &amp; Penilaian Kerja Praktik
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Upload Laporan &amp; Penilaian KP</h1>
                        <p class="text-xs text-orange-100">
                            Unggah berkas Laporan Akhir KP yang di dalamnya memuat Form Penilaian KP dari Pembimbing Lapangan.
                        </p>
                    </div>

                    <!-- Ringkasan Pembimbing & Tempat KP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 lg:w-[440px] shrink-0">
                        <div class="p-3 rounded-2xl bg-black/25 backdrop-blur-md border border-white/20">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block">Tempat Kerja Praktik</span>
                            <h4 class="text-xs font-extrabold text-white truncate mt-0.5">
                                <?= !empty($kp['nama_perusahaan']) ? htmlspecialchars($kp['nama_perusahaan']) : 'Belum Diisi'; ?>
                            </h4>
                            <span class="text-[10px] text-orange-100 truncate block">
                                <?= !empty($kp['nama_tujuan']) ? 'Pembimbing Lapangan: ' . htmlspecialchars($kp['nama_tujuan']) : 'Silakan isi Form Pengajuan KP'; ?>
                            </span>
                        </div>
                        <div class="p-3 rounded-2xl bg-black/25 backdrop-blur-md border border-white/20">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-amber-200 block">Dosen Pembimbing 1 KP</span>
                            <h4 class="text-xs font-extrabold text-white truncate mt-0.5">
                                <?= !empty($pembimbing1) ? htmlspecialchars($pembimbing1) : 'Menunggu Plotting Koordinator KP'; ?>
                            </h4>
                            <span class="text-[10px] text-orange-100 truncate block">
                                Status LAA: <?= !empty($wf['syarat_all_approved']) ? 'Persyaratan Disetujui' : 'Menunggu Approval LAA'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT GRID: KIRI = FORM UPLOAD LAPORAN KP, KANAN = STATUS & RIWAYAT -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full">

                <!-- KOLOM KIRI: FORMULIR UPLOAD LAPORAN KP + FORM PENILAIAN KP -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-6 sm:p-8 space-y-6">
                        <div class="border-b border-orange-100 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">FORMULIR UNGGAH LAPORAN KP</span>
                            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                <i class="bi bi-cloud-arrow-up-fill text-orange-500 text-2xl"></i> Upload File Laporan KP &amp; Form Penilaian
                            </h3>
                            <p class="text-xs text-slate-600 mt-1">
                                Pastikan di dalam file PDF Laporan KP yang diunggah sudah terdapat <strong>Form Penilaian KP</strong> yang telah dinilai dan ditandatangani oleh Pembimbing Lapangan.
                            </p>
                        </div>

                        <?php if (!$wf['syarat_all_approved']): ?>
                            <div class="p-6 text-center bg-amber-50 border border-amber-200 rounded-2xl space-y-2">
                                <i class="bi bi-clock-history text-4xl text-amber-600 block"></i>
                                <h4 class="font-bold text-base text-amber-900">Persyaratan Belum Disetujui Admin Layanan (LAA)</h4>
                                <p class="text-amber-700 text-xs">Anda baru dapat mengunggah Laporan KP setelah seluruh berkas persyaratan diverifikasi &amp; disetujui oleh Admin LAA pada Tab 1.</p>
                                <a href="<?= site_url('mahasiswa/pengajuan_kp'); ?>" class="inline-flex items-center gap-2 mt-2 px-4 py-2 rounded-xl bg-orange-600 text-white font-bold text-xs hover:bg-orange-700 transition">
                                    <i class="bi bi-arrow-left"></i> Ke Menu Pengajuan KP
                                </a>
                            </div>
                        <?php elseif (!$wf['has_pembimbing1']): ?>
                            <div class="p-6 text-center bg-indigo-50 border border-indigo-200 rounded-2xl space-y-2">
                                <i class="bi bi-person-fill-gear text-4xl text-indigo-600 block"></i>
                                <h4 class="font-bold text-base text-indigo-900">Menunggu Plotting Dosen Pembimbing 1</h4>
                                <p class="text-indigo-700 text-xs">Seluruh berkas persyaratan Anda telah disetujui oleh Admin LAA! Saat ini menunggu Koordinator KP menetapkan Dosen Pembimbing 1 sebelum Anda dapat mengunggah Laporan KP.</p>
                            </div>
                        <?php elseif ($is_app_laporan): ?>
                            <div class="p-6 text-center bg-emerald-50 border border-emerald-200 rounded-2xl space-y-2">
                                <i class="bi bi-patch-check-fill text-4xl text-emerald-600 block"></i>
                                <h4 class="font-bold text-base text-emerald-900">Laporan KP Telah Disetujui LAA (Selesai)</h4>
                                <p class="text-emerald-700 text-xs">Seluruh rangkaian Kerja Praktik Anda telah dinyatakan selesai oleh Admin LAA.</p>
                            </div>
                        <?php else: ?>
                            <form action="<?= site_url('mahasiswa/laporan_kp'); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">

                                <!-- Input Nilai / Poin Penilaian KP dari Pembimbing Lapangan -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                                        Nilai / Poin Penilaian Pembimbing Lapangan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" name="poin" min="0" max="100" required
                                           value="<?= htmlspecialchars((string)($laporan['poin'] ?? '')); ?>"
                                           placeholder="Masukkan nilai angka pada Form Penilaian KP (0 - 100)"
                                           class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-300 text-xs sm:text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-orange-400 outline-none">
                                    <p class="text-[11px] text-slate-500 mt-1">Sesuaikan dengan angka nilai yang tertera pada lembar Form Penilaian KP di dalam laporan.</p>
                                </div>

                                <!-- Dropzone File Laporan KP -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                                        File Laporan KP (Termasuk Form Penilaian KP) <span class="text-rose-500">*</span>
                                    </label>

                                    <div id="laporanDropZone" class="relative border-2 border-dashed border-orange-300 hover:border-orange-500 bg-orange-50/40 hover:bg-orange-50/70 rounded-3xl p-8 text-center transition-all cursor-pointer group"
                                         onclick="document.getElementById('fileLaporanKpInput').click()">
                                        <input type="file" name="file_laporan_kp" id="fileLaporanKpInput" accept=".pdf,application/pdf" required
                                               class="hidden" onchange="onSelectLaporanFile(this)">

                                        <div class="space-y-3 pointer-events-none">
                                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white group-hover:scale-105 flex items-center justify-center text-2xl mx-auto transition-transform box-3d shadow-md">
                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs sm:text-sm font-bold text-slate-900" id="laporanDropLabel">
                                                    Klik untuk memilih file PDF atau seret ke sini
                                                </p>
                                                <p class="text-[11px] text-slate-500 font-medium mt-1">
                                                    Format wajib PDF (Maksimal 10 MB) • Memuat Laporan KP &amp; Form Penilaian KP
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="laporanSelectedBadge" class="hidden mt-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <i class="bi bi-file-earmark-check-fill text-emerald-600 text-lg shrink-0"></i>
                                            <span id="laporanSelectedName" class="truncate">laporan_kp.pdf</span>
                                        </div>
                                        <span id="laporanSelectedSize" class="text-[11px] bg-white px-2.5 py-1 rounded-lg border border-emerald-200 shrink-0">0 MB</span>
                                    </div>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                        <span><?= $has_laporan ? 'Perbarui Laporan & Penilaian KP' : 'Unggah Laporan & Penilaian KP'; ?></span>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- KOLOM KANAN: STATUS VERIFIKASI LAA & BERKAS TERUNGGAH -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="card-3d-warm rounded-3xl p-6 sm:p-7 space-y-5">
                        <div class="border-b border-orange-100 pb-4 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">STATUS BERKAS</span>
                                <h3 class="text-base font-extrabold text-slate-900">Riwayat &amp; Verifikasi Laporan KP</h3>
                            </div>
                            <i class="bi bi-shield-check text-2xl text-orange-500"></i>
                        </div>

                        <?php if (!$has_laporan): ?>
                            <div class="p-6 rounded-2xl bg-white/80 border border-slate-200 text-center space-y-2">
                                <i class="bi bi-inbox text-3xl text-slate-400 block"></i>
                                <h4 class="text-xs font-bold text-slate-700">Belum Ada Laporan KP Diunggah</h4>
                                <p class="text-[11px] text-slate-500">Silakan unggah file PDF Laporan KP beserta Form Penilaian KP melalui formulir di samping.</p>
                            </div>
                        <?php else: ?>
                            <?php
                                $lap_filepath = $laporan['file'] ?? '';
                                $lap_filename = $laporan['file_basename'] ?? basename($lap_filepath);
                                $lap_file_url = !empty($lap_filepath) ? (strpos($lap_filepath, 'uploads/') === 0 ? base_url($lap_filepath) : base_url('uploads/laporan_kp/' . $lap_filepath)) : '#';
                            ?>
                            <div class="p-4 rounded-2xl bg-white border <?= $is_app_laporan ? 'border-emerald-300' : ($is_rej_laporan ? 'border-rose-300' : 'border-amber-300'); ?> space-y-3 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-extrabold text-slate-900 truncate"><?= htmlspecialchars($lap_filename); ?></h4>
                                            <span class="text-[11px] text-slate-500 block">Nilai Penilaian KP: <strong><?= (int)($laporan['poin'] ?? 0); ?></strong></span>
                                        </div>
                                    </div>

                                    <?php if ($is_app_laporan): ?>
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-extrabold shrink-0">
                                            Disetujui LAA
                                        </span>
                                    <?php elseif ($is_rej_laporan): ?>
                                        <span class="px-2.5 py-1 rounded-full bg-rose-600 text-white text-[10px] font-extrabold shrink-0">
                                            Revisi LAA
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-300 text-[10px] font-extrabold shrink-0">
                                            Menunggu LAA
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400">Verifikator: Admin LAA</span>
                                    <a href="<?= $lap_file_url; ?>" target="_blank"
                                       class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 transition">
                                        <i class="bi bi-eye-fill text-rose-600"></i> Lihat File PDF
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>
                function initLaporanDropZone() {
            const zone = document.getElementById('laporanDropZone');
            const input = document.getElementById('fileLaporanKpInput');
            if (!zone || !input) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
                window.addEventListener(eventName, function(e) {
                    if (eventName === 'dragover' || eventName === 'drop') {
                        e.preventDefault();
                    }
                }, false);
            });

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
                const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                if (!isPdf) {
                    alert('Hanya berkas berformat PDF yang diperbolehkan.');
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file maksimal adalah 10 MB.');
                    return;
                }

                try {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                } catch (err) {
                    input.files = dt.files;
                }

                onSelectLaporanFile(input);
            }, false);
        }

        function onSelectLaporanFile(input) {
            const badge = document.getElementById('laporanSelectedBadge');
            const nameEl = document.getElementById('laporanSelectedName');
            const sizeEl = document.getElementById('laporanSelectedSize');
            if (input.files && input.files.length > 0) {
                const f = input.files[0];
                if (badge) badge.classList.remove('hidden');
                if (nameEl) nameEl.textContent = f.name;
                if (sizeEl) sizeEl.textContent = (f.size / 1024 / 1024).toFixed(2) + ' MB';
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initLaporanDropZone();
            const form = document.querySelector('form[action*="laporan_kp"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const btn = form.querySelector('button[type="submit"]');
                    if (!btn) return;
                    if (btn.disabled || btn.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    btn.setAttribute('data-submitting', 'true');
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.classList.remove('cursor-pointer', 'hover:from-orange-700', 'hover:to-amber-600');
                    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Mengunggah Laporan...</span>';
                });
            }
        });
    </script>
</body>
</html>
