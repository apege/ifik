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
            $has_form       = !empty($wf['has_form']);
            $current_step   = (int)($wf['current_step_num'] ?? 1);
            $pembimbing1    = $wf['pembimbing1_nama'] ?: ($kp['dosen_pembimbing1'] ?? '');

            $steps_info = [
                1 => ['label' => 'Isi Form KP'],
                2 => ['label' => 'Upload Syarat'],
                3 => ['label' => 'Verifikasi LAA'],
                4 => ['label' => 'Plot Pembimbing 1'],
                5 => ['label' => 'Upload Laporan KP'],
                6 => ['label' => 'Verifikasi LAA'],
                7 => ['label' => 'Selesai'],
            ];
        ?>

        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow space-y-6">

            <!-- Page Header & Quick Navigation Tabs (3 Tahap Menu Mahasiswa) -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-1">MODUL KERJA PRAKTIK (KP)</span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Persyaratan KP (Dinamis)</h1>
                    <p class="text-xs text-slate-600 mt-1">Unggah berkas persyaratan Kerja Praktik sesuai daftar aktif dari Super Admin untuk diverifikasi oleh Admin LAA.</p>
                </div>

                <!-- Switch Tab: 1. Form Pengajuan KP | 2. Persyaratan KP | 3. Upload Laporan KP -->
                <div class="inline-flex flex-wrap p-1.5 rounded-2xl bg-white/90 border border-orange-200 shadow-xs self-start md:self-auto gap-1">
                    <a href="<?= site_url('mahasiswa/pengajuan_kp'); ?>" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-orange-600 transition flex items-center gap-1.5">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>1. Form Pengajuan KP</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/persyaratan_kp'); ?>" class="px-3.5 py-2 rounded-xl text-xs font-extrabold bg-gradient-to-r from-orange-600 to-amber-500 text-white shadow-xs flex items-center gap-1.5">
                        <i class="bi <?= $has_form ? 'bi-cloud-arrow-up-fill' : 'bi-lock-fill'; ?>"></i>
                        <span>2. Persyaratan KP</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/laporan_kp'); ?>" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-orange-600 transition flex items-center gap-1.5">
                        <i class="bi <?= !empty($wf['can_upload_laporan']) ? 'bi-unlock-fill text-emerald-600' : 'bi-lock-fill text-slate-400'; ?>"></i>
                        <span>3. Upload Laporan KP</span>
                    </a>
                </div>
            </div>

            <!-- 7-Step KP Workflow Progress Tracker -->
            <div class="card-3d-warm rounded-2xl p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-orange-600"></i> Alur Tahapan Kerja Praktik (KP)
                    </span>
                    <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-orange-100 text-orange-800 border border-orange-300">
                        Tahap <?= $current_step; ?> / 7
                    </span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
                    <?php foreach ($steps_info as $num => $st): ?>
                        <?php
                            $is_done   = ($num < $current_step) || ($num === 7 && !empty($wf['is_selesai']));
                            $is_active = ($num === $current_step && empty($wf['is_selesai']));
                            if ($is_done) {
                                $box_cls = 'bg-emerald-50 border-emerald-300 text-emerald-800';
                                $badge_cls = 'bg-emerald-600 text-white';
                            } elseif ($is_active) {
                                $box_cls = 'bg-orange-50 border-orange-400 text-orange-900 ring-2 ring-orange-200';
                                $badge_cls = 'bg-orange-600 text-white animate-pulse';
                            } else {
                                $box_cls = 'bg-white/70 border-slate-200 text-slate-400';
                                $badge_cls = 'bg-slate-200 text-slate-500';
                            }
                        ?>
                        <div class="p-3 rounded-xl border <?= $box_cls; ?> flex flex-col items-center text-center transition">
                            <div class="w-7 h-7 rounded-lg <?= $badge_cls; ?> flex items-center justify-center text-xs font-bold mb-1.5">
                                <?php if ($is_done): ?>
                                    <i class="bi bi-check-lg"></i>
                                <?php else: ?>
                                    <?= $num; ?>
                                <?php endif; ?>
                            </div>
                            <span class="text-[11px] font-bold leading-tight"><?= $st['label']; ?></span>
                        </div>
                    <?php endforeach; ?>
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

            <?php if (!$has_form): ?>
                <!-- LOCKED STATE: BELUM MENGISI FORM PENGAJUAN KP -->
                <div class="card-3d-warm rounded-2xl p-6 sm:p-8 border-2 border-orange-300">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 border border-orange-300 text-orange-600 flex items-center justify-center text-2xl shrink-0">
                                <i class="bi bi-lock-fill"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Tahap Persyaratan KP Belum Terbuka</h2>
                                <p class="text-xs text-slate-600 mt-1">
                                    Silakan lengkapi dan simpan <strong>Tahap 1: Formulir Pengajuan KP</strong> terlebih dahulu sebelum mengunggah berkas persyaratan KP.
                                </p>
                            </div>
                        </div>
                        <a href="<?= site_url('mahasiswa/pengajuan_kp'); ?>" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 text-white font-bold text-xs shadow-sm hover:from-orange-700 hover:to-amber-600 transition flex items-center gap-2 shrink-0">
                            <i class="bi bi-pencil-square"></i> Isi Formulir Pengajuan KP
                        </a>
                    </div>
                </div>
            <?php else: ?>

                <!-- Ringkasan Singkat Tempat KP & Status Verifikasi -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-white/90 border border-orange-200 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shrink-0">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Perusahaan / Tempat KP</span>
                            <p class="text-xs font-extrabold text-slate-900 truncate"><?= htmlspecialchars($kp['nama_perusahaan'] ?? ''); ?></p>
                            <span class="text-[11px] font-semibold text-orange-700 truncate block"><?= htmlspecialchars($kp['posisi_pekerjaan'] ?? ''); ?></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/90 border border-orange-200 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl <?= !empty($wf['syarat_all_approved']) ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600'; ?> flex items-center justify-center text-xl shrink-0">
                            <i class="bi <?= !empty($wf['syarat_all_approved']) ? 'bi-patch-check-fill' : 'bi-shield-check'; ?>"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Verifikasi Syarat (LAA)</span>
                            <p class="text-xs font-extrabold text-slate-900">
                                <?= (int)$wf['total_approved_wajib']; ?> / <?= (int)$wf['total_syarat_wajib']; ?> Berkas Wajib Disetujui
                            </p>
                            <span class="text-[11px] font-semibold <?= !empty($wf['syarat_all_approved']) ? 'text-emerald-600' : 'text-amber-600'; ?>">
                                <?= !empty($wf['syarat_all_approved']) ? 'Semua syarat telah disetujui LAA' : 'Menunggu kelengkapan / verifikasi LAA'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/90 border border-orange-200 shadow-xs flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl <?= !empty($wf['has_pembimbing1']) ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500'; ?> flex items-center justify-center text-xl shrink-0">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Plot Koordinator KP</span>
                            <?php if (!empty($wf['has_pembimbing1'])): ?>
                                <p class="text-xs font-extrabold text-slate-900 truncate"><?= htmlspecialchars($pembimbing1); ?></p>
                                <span class="text-[11px] font-semibold text-emerald-600">Dosen Pembimbing 1</span>
                            <?php else: ?>
                                <p class="text-xs font-bold text-slate-500">Belum Diplot</p>
                                <span class="text-[11px] text-slate-400">Menunggu Koordinator KP</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- CARD UPLOAD PERSYARATAN KP DINAMIS -->
                <form action="<?= site_url('mahasiswa/persyaratan_kp'); ?>" method="POST" enctype="multipart/form-data" id="formUploadPersyaratanKp" class="space-y-6">
                    <div class="card-3d-warm rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-slate-800 to-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center font-extrabold text-sm">02</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-extrabold tracking-tight">Upload Persyaratan KP (Dinamis)</h2>
                                    <p class="text-[11px] text-slate-300">Diverifikasi oleh Admin LAA &bull; Format berkas wajib PDF (Maks. 10 MB / file)</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-white/10 text-amber-300 text-[11px] font-bold self-start sm:self-auto">
                                <?= (int)$wf['total_approved_wajib']; ?> / <?= (int)$wf['total_syarat_wajib']; ?> Wajib Disetujui LAA
                            </span>
                        </div>

                        <div class="p-6 sm:p-8 space-y-4">
                            <?php if (empty($syarat_kp)): ?>
                                <div class="p-6 rounded-xl bg-amber-50 border border-amber-200 text-center">
                                    <i class="bi bi-info-circle-fill text-amber-500 text-2xl mb-1 block"></i>
                                    <p class="text-xs font-bold text-amber-900">Belum ada daftar persyaratan KP aktif yang dikonfigurasi oleh Super Admin.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($syarat_kp as $item): ?>
                                    <?php
                                        $kode   = $item['kode_berkas'];
                                        $wajib  = (int)$item['is_wajib'] === 1;
                                        $f_row  = $files_map[$kode] ?? null;
                                        $st_laa = $f_row ? (int)$f_row['status_adminlaa'] : null; // 0=pending, 1=approved, 2=rejected
                                        $locked_item = ($st_laa === 1);
                                    ?>
                                    <div class="p-4 sm:p-5 rounded-2xl border <?= $st_laa === 1 ? 'bg-emerald-50/50 border-emerald-300' : ($st_laa === 2 ? 'bg-rose-50/50 border-rose-300' : 'bg-white border-slate-200'); ?> transition shadow-2xs">
                                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                            <!-- Info Syarat -->
                                            <div class="space-y-1 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="text-xs sm:text-sm font-extrabold text-slate-900"><?= htmlspecialchars($item['nama_berkas']); ?></span>
                                                    <?php if ($wajib): ?>
                                                        <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 text-[10px] font-extrabold uppercase">Wajib</span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">Opsional</span>
                                                    <?php endif; ?>

                                                    <!-- Status Verifikasi LAA -->
                                                    <?php if ($f_row): ?>
                                                        <?php if ($st_laa === 1): ?>
                                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold inline-flex items-center gap-1">
                                                                <i class="bi bi-check-circle-fill"></i> Disetujui LAA
                                                            </span>
                                                        <?php elseif ($st_laa === 2): ?>
                                                            <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-bold inline-flex items-center gap-1">
                                                                <i class="bi bi-x-circle-fill"></i> Ditolak / Revisi LAA
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 text-[10px] font-bold inline-flex items-center gap-1">
                                                                <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi LAA
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-semibold">
                                                            Belum Diunggah
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if (!empty($item['keterangan'])): ?>
                                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($item['keterangan']); ?></p>
                                                <?php endif; ?>

                                                <?php if ($f_row && !empty($f_row['komentar'])): ?>
                                                    <div class="mt-2 p-2.5 rounded-xl bg-rose-100/80 border border-rose-200 text-rose-900 text-xs flex items-start gap-2">
                                                        <i class="bi bi-chat-left-dots-fill text-rose-600 mt-0.5"></i>
                                                        <div>
                                                            <span class="font-bold">Catatan LAA:</span> <?= htmlspecialchars($f_row['komentar']); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Aksi File & Input Upload -->
                                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                                                <?php if ($f_row && !empty($f_row['file'])): ?>
                                                    <a href="<?= base_url('uploads/kp/persyaratan/' . $f_row['file']); ?>" target="_blank"
                                                       class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                                        <i class="bi bi-file-earmark-pdf-fill text-rose-600"></i> Lihat File
                                                    </a>
                                                    <?php if (!$locked_item): ?>
                                                        <a href="<?= site_url('mahasiswa/hapus_berkas_kp/' . urlencode($kode)); ?>"
                                                           onclick="return confirm('Hapus berkas ini?');"
                                                           class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold inline-flex items-center justify-center gap-1 transition">
                                                            <i class="bi bi-trash3"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if (!$locked_item): ?>
                                                    <label class="cursor-pointer">
                                                        <input type="file" name="berkas_kp_<?= htmlspecialchars($kode); ?>" accept=".pdf,application/pdf"
                                                               class="block w-full sm:w-60 text-xs text-slate-600
                                                                      file:mr-3 file:py-2 file:px-3.5
                                                                      file:rounded-xl file:border-0
                                                                      file:text-xs file:font-bold
                                                                      file:bg-orange-100 file:text-orange-700
                                                                      hover:file:bg-orange-200 transition">
                                                    </label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($syarat_kp)): ?>
                            <div class="px-6 py-4 bg-orange-50/70 border-t border-orange-200/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div class="text-xs text-slate-600 flex items-center gap-2">
                                    <i class="bi bi-shield-check text-orange-600 text-base"></i>
                                    <span>Pilih berkas PDF yang ingin diunggah atau diperbarui, lalu klik tombol <strong>Unggah Persyaratan KP</strong>.</span>
                                </div>
                                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                                    <?php if (!empty($wf['can_upload_laporan'])): ?>
                                        <a href="<?= site_url('mahasiswa/laporan_kp'); ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                                            <span>Lanjut Upload Laporan KP</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button type="submit" id="btnSubmitPersyaratanKp" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="bi bi-cloud-arrow-up-fill" id="iconSubmitPersyaratanKp"></i>
                                        <span id="textSubmitPersyaratanKp">Unggah Persyaratan KP</span>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>

            <?php endif; ?>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formUploadPersyaratanKp');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const btn = document.getElementById('btnSubmitPersyaratanKp');
                    if (!btn) return;
                    if (btn.disabled || btn.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    btn.setAttribute('data-submitting', 'true');
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.classList.remove('cursor-pointer', 'hover:from-orange-700', 'hover:to-amber-600');
                    const icon = document.getElementById('iconSubmitPersyaratanKp');
                    const text = document.getElementById('textSubmitPersyaratanKp');
                    if (icon) icon.className = 'bi bi-arrow-repeat animate-spin text-sm';
                    if (text) text.textContent = 'Mengunggah Berkas...';
                });
            }
        });
    </script>
</body>
</html>
