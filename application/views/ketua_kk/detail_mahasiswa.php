<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Portal</title>
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
        'user_role_label'   => 'Ketua Kelompok Keahlian (KK)',
        'user_display_name' => 'Ketua KK Fakultas',
        'user_display_sub'  => 'Approval Bidang Keilmuan'
    ]); ?>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('error')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xs flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <p class="font-semibold"><?= $this->session->flashdata('error'); ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <!-- Student Profile Card -->
        <div class="clean-card rounded-2xl p-6 sm:p-7 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Left Info -->
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 border border-orange-200 text-orange-600 font-extrabold text-xl flex items-center justify-center flex-shrink-0">
                        <?= strtoupper(substr($detail['nama_depan'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                <?= htmlspecialchars($detail['nama_depan'] . ' ' . $detail['nama_belakang']); ?>
                            </h1>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-[11px] font-mono font-bold border border-slate-200">
                                <?= htmlspecialchars($detail['nim']); ?>
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span><i class="bi bi-mortarboard text-slate-400 mr-1"></i> <?= htmlspecialchars($detail['prodi'] ?? 'DKV'); ?></span>
                            <span><i class="bi bi-diagram-3 text-slate-400 mr-1"></i> <?= htmlspecialchars($detail['nama_kk'] ?? 'Visual Communication & Multimedia'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Right Status Pill -->
                <div class="flex flex-col sm:items-end gap-1.5">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Status Approval Ketua KK:</span>
                    <?php if($detail['status_approval_kk'] === 'Approved'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold shadow-xs">
                            <i class="bi bi-unlock-fill text-emerald-600"></i> Disetujui KK &amp; Bimbingan Terbuka
                        </span>
                    <?php elseif($detail['status_approval_kk'] === 'Rejected'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold shadow-xs">
                            <i class="bi bi-x-circle-fill text-rose-600"></i> Ditolak oleh Ketua KK
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold shadow-xs">
                            <i class="bi bi-clock text-amber-600"></i> Menunggu Persetujuan Ketua KK
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Prerequisite Chain Status Bar (Validasi Prasyarat Seluruh Approval) -->
        <?php
            $w_ok = ($detail['status_approval_wali'] === 'Approved');
            $a_ok = ($detail['status_approval_admin'] === 'Approved');
            $k_ok = ($detail['status_approval_koor'] === 'Approved');
        ?>
        <div class="clean-card rounded-2xl p-6 sm:p-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold text-orange-600 tracking-wider block mb-0.5">VALIDASI PRASYARAT TAHAP SEBELUMNYA</span>
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-check-square text-orange-600"></i> Kelayakan Approval Ketua KK
                    </h2>
                </div>
                <?php if($is_prerequisite_met): ?>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold flex items-center gap-1.5">
                        <i class="bi bi-check-circle-fill text-emerald-500"></i> Siap Disetujui
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-xs font-bold flex items-center gap-1.5">
                        <i class="bi bi-lock-fill text-rose-500"></i> Menunggu Tahap Sebelumnya
                    </span>
                <?php endif; ?>
            </div>

            <!-- 3 Step Prerequisite Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                <!-- Tahap 1: Dosen Wali -->
                <div class="p-4 rounded-xl border <?= $w_ok ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50'; ?> flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tahap 01</span>
                        <h4 class="font-bold text-xs text-slate-900">Dosen Wali</h4>
                        <span class="text-[11px] font-semibold <?= $w_ok ? 'text-emerald-700' : 'text-slate-500'; ?>">
                            <?= $detail['status_approval_wali']; ?>
                        </span>
                    </div>
                    <div class="w-8 h-8 rounded-xl <?= $w_ok ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center font-bold text-sm">
                        <i class="bi <?= $w_ok ? 'bi-check-lg' : 'bi-clock'; ?>"></i>
                    </div>
                </div>

                <!-- Tahap 2: Admin Layanan (LAA) -->
                <div class="p-4 rounded-xl border <?= $a_ok ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50'; ?> flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tahap 02</span>
                        <h4 class="font-bold text-xs text-slate-900">Admin Layanan (LAA)</h4>
                        <span class="text-[11px] font-semibold <?= $a_ok ? 'text-emerald-700' : 'text-slate-500'; ?>">
                            <?= $detail['status_approval_admin']; ?> (Berkas Lengkap)
                        </span>
                    </div>
                    <div class="w-8 h-8 rounded-xl <?= $a_ok ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center font-bold text-sm">
                        <i class="bi <?= $a_ok ? 'bi-check-lg' : 'bi-clock'; ?>"></i>
                    </div>
                </div>

                <!-- Tahap 3: Koordinator TA -->
                <div class="p-4 rounded-xl border <?= $k_ok ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50'; ?> flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tahap 03</span>
                        <h4 class="font-bold text-xs text-slate-900">Koordinator TA</h4>
                        <span class="text-[11px] font-semibold <?= $k_ok ? 'text-emerald-700' : 'text-slate-500'; ?>">
                            <?= $detail['status_approval_koor']; ?>
                        </span>
                    </div>
                    <div class="w-8 h-8 rounded-xl <?= $k_ok ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-400'; ?> flex items-center justify-center font-bold text-sm">
                        <i class="bi <?= $k_ok ? 'bi-check-lg' : 'bi-clock'; ?>"></i>
                    </div>
                </div>
            </div>

            <?php if(!$is_prerequisite_met): ?>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center gap-2">
                    <i class="bi bi-info-circle text-amber-600 text-base flex-shrink-0"></i>
                    <span>Catatan: Ketua KK hanya dapat memberikan persetujuan final apabila Dosen Wali, Admin Layanan (LAA), dan Koordinator TA telah berstatus <strong>Approved</strong>.</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Topic Details Card -->
        <div class="clean-card rounded-2xl p-6 sm:p-7 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="bi bi-journal-text text-orange-600"></i> Usulan Rencana Topik &amp; Judul Tugas Akhir
            </h2>

            <div class="space-y-3">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-orange-600 tracking-wider block mb-1">Judul Utama (Pilihan 1)</span>
                    <p class="text-xs font-bold text-slate-900 leading-relaxed"><?= htmlspecialchars($detail['judul_1']); ?></p>
                </div>

                <?php if(!empty($detail['judul_2'])): ?>
                    <div class="p-3.5 rounded-xl bg-slate-50/60 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-0.5">Judul Alternatif 2</span>
                        <p class="text-xs font-medium text-slate-700"><?= htmlspecialchars($detail['judul_2']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($detail['judul_3'])): ?>
                    <div class="p-3.5 rounded-xl bg-slate-50/60 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-0.5">Judul Alternatif 3</span>
                        <p class="text-xs font-medium text-slate-700"><?= htmlspecialchars($detail['judul_3']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($detail['judul_en'])): ?>
                    <div class="p-3.5 rounded-xl bg-slate-50/60 border border-slate-200 italic">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block not-italic mb-0.5">English Title</span>
                        <p class="text-xs text-slate-600">"<?= htmlspecialchars($detail['judul_en']); ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approval Form Section -->
        <form method="POST" action="<?= site_url('ketuakk/submit_approval/' . $detail['nim']); ?>">
            <div class="clean-card rounded-2xl p-6 sm:p-7 space-y-5">
                <div>
                    <label for="catatan_kk" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1 flex items-center gap-2">
                        <i class="bi bi-chat-left-text text-orange-600"></i> Catatan Rekomendasi / Pengarahan Ketua KK
                    </label>
                    <textarea id="catatan_kk" name="catatan_kk" rows="3" 
                              placeholder="Tuliskan catatan arahan keilmuan atau rekomendasi pembimbing untuk mahasiswa..."
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition-all"><?= htmlspecialchars($detail['catatan_kk'] ?? ''); ?></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="<?= site_url('ketuakk'); ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors order-2 sm:order-1 flex items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard KK
                    </a>

                    <div class="flex items-center gap-3 w-full sm:w-auto order-1 sm:order-2">
                        <!-- Reject Button -->
                        <button type="submit" name="status" value="Rejected" onclick="return confirm('Yakin ingin menolak pengajuan TA ini di tingkat Kelompok Keahlian?');" 
                                class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center justify-center gap-1.5">
                            <i class="bi bi-x-circle"></i> Tolak / Minta Perubahan
                        </button>

                        <!-- Approve & Unlock Bimbingan Button -->
                        <button type="submit" name="status" value="Approved" 
                                <?= !$is_prerequisite_met ? 'disabled title="Prasyarat tahap sebelumnya belum lengkap"' : ''; ?>
                                onclick="return confirm('Yakin menyetujui topik TA mahasiswa ini? Akses modul Bimbingan TA akan otomatis dibuka (Unlocked).');" 
                                class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold shadow-xs transition-all flex items-center justify-center gap-1.5">
                            <i class="bi bi-unlock-fill"></i> Setujui &amp; Unlock Bimbingan TA
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-8 text-center text-xs text-slate-500">
        &copy; <?= date('Y'); ?> Fakultas Industri Kreatif - Telkom University. Modul Ketua Kelompok Keahlian (KK).
    </footer>

</body>
</html>
