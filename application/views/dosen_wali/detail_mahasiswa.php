<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white relative">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d shadow-md">
                        W
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block">Detail & Approval Dosen Wali</span>
                    </div>
                </div>

                <!-- Dosen Wali Info Pill (Kode, Nama, Kejuruan) -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800 leading-tight"><?= $dosen_info['nama_dosen'] ?? 'Alif Dosen, S.T., M.T.'; ?></span>
                        <div class="flex items-center justify-end gap-2 text-[10px] font-semibold text-slate-500 mt-0.5">
                            <span class="px-2 py-0.5 bg-orange-100/90 text-orange-700 rounded-md border border-orange-200/80 font-bold"><?= $dosen_info['kode_dosen'] ?? 'DW-001'; ?></span>
                            <span>Prodi: <strong class="text-slate-700"><?= $dosen_info['kejuruan'] ?? 'Informatika / DKV'; ?></strong></span>
                        </div>
                    </div>
                    <a href="<?= site_url('dosenwali'); ?>" class="text-xs font-bold text-slate-600 hover:text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                        <i class="bi bi-arrow-left text-sm"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container (Full Wide Layout) -->
    <main class="w-full px-4 sm:px-6 lg:px-10 py-10 flex-grow">

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm box-3d">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="mb-8 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-sm box-3d">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('error'); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar Profile Card -->
            <div class="lg:col-span-1">
                <div class="card-3d-warm rounded-2xl p-6 sm:p-8 text-center sticky top-24">
                    <div class="w-20 h-20 bg-gradient-to-tr from-orange-500 to-amber-400 text-white rounded-3xl flex items-center justify-center text-3xl font-bold mx-auto mb-4 box-3d shadow-md shadow-orange-500/20">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight mb-1"><?= $detail['nama_depan'] ?? 'Mahasiswa'; ?> <?= $detail['nama_belakang'] ?? ''; ?></h2>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">NIM: <?= $detail['nim'] ?? '1301210001'; ?></p>
                    <span class="px-3.5 py-1.5 bg-orange-100/80 text-orange-700 text-xs font-semibold rounded-full border border-orange-200 inline-block"><?= $detail['konsentrasi_dkv'] ?? 'Informatika'; ?></span>
                    
                    <div class="border-t border-orange-200/60 mt-6 pt-5 text-left">
                        <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider block mb-2">Alamat & Geodata:</span>
                        <p class="text-xs text-slate-700 font-medium leading-relaxed flex items-start gap-2.5 bg-white/70 p-4 rounded-xl border border-orange-200/70 shadow-xs">
                            <i class="bi bi-geo-alt-fill text-orange-500 text-base shrink-0 mt-0.5"></i>
                            <span><?= $detail['alamat'] ?? 'Bandung, Jawa Barat'; ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Judul TA Review & Keputusan Card -->
                <?php
                    $st_judul = $detail['status_judul'] ?? 'Pending';
                    $note_judul = $detail['catatan_judul'] ?? '';
                    $isJudulDecided = in_array($st_judul, array('Approved', 'Rejected'));
                ?>
                <div id="card-judul-global" class="card-3d-warm rounded-2xl p-6 sm:p-8 space-y-5 transition-all">
                    <!-- Header Judul -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-orange-200/60 gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center text-xl shrink-0 box-3d">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="font-bold text-base sm:text-lg text-slate-900 tracking-tight">Usulan Judul Tugas Akhir</h2>
                                    <span id="badge-status-judul-global" class="px-2.5 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap <?= ($st_judul === 'Approved') ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (($st_judul === 'Rejected') ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200'); ?>">
                                        <?= ($st_judul === 'Approved') ? '✅ Disetujui' : (($st_judul === 'Rejected') ? '❌ Ditolak' : '⏳ Menunggu'); ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-normal mt-0.5">Tinjau usulan judul tugas akhir mahasiswa dan berikan keputusan serta saran perbaikan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Usulan Judul Mahasiswa -->
                    <div class="space-y-3.5 text-xs">
                        <!-- Judul 1 -->
                        <div class="p-4 rounded-xl bg-white/90 border border-orange-200/80 shadow-2xs space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-600 block">Usulan Judul 1 (Utama)</span>
                            <p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-snug">
                                <?= htmlspecialchars($detail['judul_1'] ?? '-'); ?>
                            </p>
                        </div>

                        <!-- Judul 2 -->
                        <?php if(!empty($detail['judul_2'])): ?>
                            <div class="p-4 rounded-xl bg-white/90 border border-orange-200/80 shadow-2xs space-y-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">Usulan Judul 2 (Alternatif 1)</span>
                                <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                    <?= htmlspecialchars($detail['judul_2']); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Judul 3 -->
                        <?php if(!empty($detail['judul_3'])): ?>
                            <div class="p-4 rounded-xl bg-white/90 border border-orange-200/80 shadow-2xs space-y-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">Usulan Judul 3 (Alternatif 2)</span>
                                <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                    <?= htmlspecialchars($detail['judul_3']); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Judul EN -->
                        <?php if(!empty($detail['judul_en'])): ?>
                            <div class="p-3.5 rounded-xl bg-white/90 border border-orange-200/80 text-slate-600 flex items-center gap-2">
                                <i class="bi bi-translate text-orange-500 text-sm shrink-0"></i>
                                <span class="font-bold text-[11px] text-slate-500 shrink-0">Translasi EN:</span>
                                <span class="italic font-medium text-xs text-slate-800">"<?= htmlspecialchars($detail['judul_en']); ?>"</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Area Saran / Catatan & Tombol Keputusan (Approve, Reject, Reset) -->
                    <div class="pt-4 border-t border-orange-200/60 space-y-3.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span>Saran / Catatan untuk Judul TA:</span>
                                <span id="comment-status-judul" class="text-[10px] <?= $isJudulDecided ? 'text-amber-600 font-semibold' : 'text-slate-400 font-normal'; ?>">
                                    <?= $isJudulDecided ? '(Terkunci, klik Reset untuk mengedit kembali)' : '(Dapat diedit)'; ?>
                                </span>
                            </label>
                            <textarea id="catatan_judul" rows="2" placeholder="Tuliskan saran atau catatan revisi untuk judul tugas akhir..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition <?= $isJudulDecided ? 'bg-slate-50 opacity-80 cursor-not-allowed' : 'bg-white'; ?>" <?= $isJudulDecided ? 'readonly' : ''; ?>><?= htmlspecialchars($note_judul); ?></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2.5" id="action-buttons-judul">
                            <!-- Reset Button -->
                            <button type="button" id="btn-reset-judul" onclick="resetJudulDecision()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / saran">
                                <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                            </button>

                            <?php if($st_judul === 'Approved'): ?>
                                <button type="button" id="btn-approve-judul" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                                    <i class="bi bi-check-circle-fill text-sm"></i> Approve
                                </button>
                                <button type="button" id="btn-reject-judul" onclick="handleResetFirstWarningJudul('Denied')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Denied">
                                    <i class="bi bi-x-circle text-sm"></i> Denied
                                </button>
                            <?php elseif($st_judul === 'Rejected'): ?>
                                <button type="button" id="btn-approve-judul" onclick="handleResetFirstWarningJudul('Approve')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Approve">
                                    <i class="bi bi-check-circle text-sm"></i> Approve
                                </button>
                                <button type="button" id="btn-reject-judul" class="px-5 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                                    <i class="bi bi-x-circle-fill text-sm"></i> Denied
                                </button>
                            <?php else: ?>
                                <button type="button" id="btn-approve-judul" onclick="decideJudul('Approved')" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                                    <i class="bi bi-check-circle-fill text-sm"></i> Approve
                                </button>
                                <button type="button" id="btn-reject-judul" onclick="decideJudul('Rejected')" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                                    <i class="bi bi-x-circle-fill text-sm"></i> Denied
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Berkas Persyaratan PDF Card -->
                <div class="card-3d-warm rounded-2xl p-6 sm:p-8 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-orange-200/60">
                        <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center text-base font-bold shrink-0 box-3d">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Berkas Persyaratan PDF (Verifikasi Dokumen)</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Klik tombol <strong class="text-amber-600">Buka &amp; Review PDF</strong> untuk meninjau dokumen dan membuka kunci tombol <strong>Approve</strong> &amp; <strong>Denied</strong>.</p>
                        </div>
                    </div>

                    <div class="space-y-5 text-xs">

                            <?php
                            $files_list = array(
                                'ksm'        => array('title' => 'Kartu Studi Mahasiswa (KSM)', 'short' => 'KSM', 'file' => $detail['file_ksm'] ?? '', 'reviewed' => !empty($detail['review_file_ksm']), 'status' => $detail['status_file_ksm'] ?? 'Pending'),
                                'transkrip'  => array('title' => 'Transkrip Nilai Akademik', 'short' => 'Transkrip', 'file' => $detail['file_transkrip'] ?? '', 'reviewed' => !empty($detail['review_file_transkrip']), 'status' => $detail['status_file_transkrip'] ?? 'Pending'),
                                'pernyataan' => array('title' => 'Surat Pernyataan TA', 'short' => 'Surat Pernyataan', 'file' => $detail['file_pernyataan'] ?? '', 'reviewed' => !empty($detail['review_file_pernyataan']), 'status' => $detail['status_file_pernyataan'] ?? 'Pending'),
                                'bebas_lab'  => array('title' => 'Surat Bebas Laboratorium', 'short' => 'Bebas Lab', 'file' => $detail['file_bebas_lab'] ?? '', 'reviewed' => !empty($detail['review_file_bebas_lab']), 'status' => $detail['status_file_bebas_lab'] ?? 'Pending'),
                            );
                            ?>

                            <div class="grid grid-cols-1 gap-4">
                                <?php foreach($files_list as $key => $item): ?>
                                    <?php 
                                        $isRev = $item['reviewed'];
                                        $st = $item['status'];
                                        $isDecided = in_array($st, array('Approved', 'Rejected'));
                                        $cardBorder = $st === 'Approved' ? 'border-emerald-200 bg-emerald-50/30' : ($st === 'Rejected' ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-white');
                                        $fileUrl = !empty($item['file']) ? base_url('uploads/persyaratan_ta/' . $item['file']) : '#';
                                    ?>

                                    <!-- Outer card per file -->
                                    <div id="card-file-<?= $key; ?>" class="rounded-2xl border <?= $cardBorder; ?> shadow-xs overflow-hidden transition-all">

                                        <!-- TOP: Header card (icon + nama file + badge + tombol buka PDF) -->
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-slate-100/80 gap-3">
                                            <div class="flex items-center gap-3.5">
                                                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-bold text-sm text-slate-900"><?= $item['title']; ?></span>
                                                        <!-- Badge Status -->
                                                        <span id="badge-status-<?= $key; ?>" class="px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap <?= $st === 'Approved' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($st === 'Rejected' ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-slate-100 text-slate-600 border-slate-200'); ?>">
                                                            <?= $st === 'Approved' ? '✅ Disetujui' : ($st === 'Rejected' ? '❌ Ditolak' : '⏳ Menunggu'); ?>
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                                        <i class="bi bi-paperclip"></i> <?= !empty($item['file']) ? htmlspecialchars($item['file']) : '<em>File belum diunggah</em>'; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Tombol Buka & Review PDF (Membuka Modal Review PDF Interaktif) -->
                                            <?php if(!empty($item['file'])): ?>
                                                <button type="button" id="btn-review-<?= $key; ?>" onclick="openPdfReviewModal('<?= $key; ?>', '<?= addslashes($item['title']); ?>', '<?= addslashes($item['file']); ?>', '<?= $fileUrl; ?>')" class="shrink-0 px-4 py-2 <?= $isRev ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-amber-500 hover:bg-amber-600 text-white border border-amber-600 shadow-sm'; ?> font-bold text-xs rounded-xl transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                                                    <i class="bi bi-eye-fill"></i>
                                                    <span id="text-review-<?= $key; ?>"><?= $isRev ? 'Buka Ulang PDF' : 'Buka & Review PDF'; ?></span>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" id="btn-review-<?= $key; ?>" onclick="openPdfReviewModal('<?= $key; ?>', '<?= addslashes($item['title']); ?>', 'File Tidak Ditemukan', '#')" class="shrink-0 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 whitespace-nowrap cursor-pointer shadow-xs">
                                                    <i class="bi bi-eye-fill"></i>
                                                    <span id="text-review-<?= $key; ?>">Buka & Review PDF</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <!-- BOTTOM: Form Komentar & Tombol Aksi -->
                                        <div class="px-5 py-4 bg-white space-y-3.5">
                                            <!-- Form Komentar Per File -->
                                            <?php
                                                $comment_val = $detail['catatan_file_' . $key] ?? '';
                                                if (empty($comment_val) && !empty($detail['catatan_wali']) && preg_match('/\[' . preg_quote(strtoupper($key), '/') . '[^\]]*\]\s*:\s*([^\n\r]+)/i', $detail['catatan_wali'], $cm)) {
                                                    $comment_val = trim($cm[1]);
                                                }
                                            ?>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                                    <span>Komentar / Catatan untuk Berkas Ini:</span>
                                                    <span id="comment-status-<?= $key; ?>" class="text-[10px] <?= $isDecided ? 'text-amber-600 font-semibold' : 'text-slate-400 font-normal'; ?>">
                                                        <?= $isDecided ? '(Terkunci, klik Reset untuk mengedit kembali)' : '(Dapat diedit)'; ?>
                                                    </span>
                                                </label>
                                                <textarea id="comment-<?= $key; ?>" rows="2" placeholder="Tambahkan catatan atau revisi untuk berkas <?= $item['short']; ?>..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition <?= $isDecided ? 'bg-slate-50 opacity-80 cursor-not-allowed' : 'bg-white'; ?>" <?= $isDecided ? 'readonly' : ''; ?>><?= htmlspecialchars($comment_val); ?></textarea>
                                            </div>

                                            <!-- Tombol Reset, Approve & Denied di kanan bawah -->
                                            <div class="flex items-center justify-end gap-2.5" id="action-buttons-<?= $key; ?>">
                                                <!-- Tombol Reset di sebelah kiri Approve (Selalu langsung aktif) -->
                                                <button type="button" id="btn-reset-<?= $key; ?>" onclick="resetSingleFile('<?= $key; ?>')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                                                    <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                                                </button>

                                                <!-- Tombol Approve & Denied -->
                                                <?php if(!$isRev): ?>
                                                    <!-- Belum direview: kedua tombol terkunci (klik akan membuka review modal) -->
                                                    <button type="button" id="btn-approve-<?= $key; ?>" onclick="handleLockedClick('<?= $key; ?>', '<?= addslashes($item['title']); ?>', '<?= addslashes($item['file']); ?>', '<?= $fileUrl; ?>')" class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-pointer flex items-center gap-1.5 opacity-70 hover:opacity-100 hover:bg-slate-200 transition" title="Klik untuk Buka PDF & Buka Kunci Approve">
                                                        <i class="bi bi-lock-fill text-xs"></i> Approve
                                                    </button>
                                                    <button type="button" id="btn-reject-<?= $key; ?>" onclick="handleLockedClick('<?= $key; ?>', '<?= addslashes($item['title']); ?>', '<?= addslashes($item['file']); ?>', '<?= $fileUrl; ?>')" class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-pointer flex items-center gap-1.5 opacity-70 hover:opacity-100 hover:bg-slate-200 transition" title="Klik untuk Buka PDF & Buka Kunci Denied">
                                                        <i class="bi bi-lock-fill text-xs"></i> Denied
                                                    </button>
                                                <?php elseif($st === 'Approved'): ?>
                                                    <!-- Sudah Approve: Approve hijau, Denied abu-abu (wajib reset untuk ubah) -->
                                                    <button type="button" id="btn-approve-<?= $key; ?>" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                                                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                                                    </button>
                                                    <button type="button" id="btn-reject-<?= $key; ?>" onclick="handleResetFirstWarning('<?= $key; ?>', 'Denied')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Denied">
                                                        <i class="bi bi-x-circle text-sm"></i> Denied
                                                    </button>
                                                <?php elseif($st === 'Rejected'): ?>
                                                    <!-- Sudah Denied: Approve abu-abu (wajib reset untuk ubah), Denied merah -->
                                                    <button type="button" id="btn-approve-<?= $key; ?>" onclick="handleResetFirstWarning('<?= $key; ?>', 'Approve')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Approve">
                                                        <i class="bi bi-check-circle text-sm"></i> Approve
                                                    </button>
                                                    <button type="button" id="btn-reject-<?= $key; ?>" class="px-5 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                                                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Pending (Sudah direview): Kedua tombol aktif hijau & merah -->
                                                    <button type="button" id="btn-approve-<?= $key; ?>" onclick="approveSingleFile('<?= $key; ?>', 'Approved')" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                                                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                                                    </button>
                                                    <button type="button" id="btn-reject-<?= $key; ?>" onclick="approveSingleFile('<?= $key; ?>', 'Rejected')" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                                                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php
                            $allReviewed = !empty($detail['review_file_ksm']) && !empty($detail['review_file_transkrip']) && !empty($detail['review_file_pernyataan']) && !empty($detail['review_file_bebas_lab']);
                            $allApproved = ($detail['status_file_ksm'] ?? '') === 'Approved' && ($detail['status_file_transkrip'] ?? '') === 'Approved' && ($detail['status_file_pernyataan'] ?? '') === 'Approved' && ($detail['status_file_bebas_lab'] ?? '') === 'Approved';
                            $allRejected = ($detail['status_file_ksm'] ?? '') === 'Rejected' && ($detail['status_file_transkrip'] ?? '') === 'Rejected' && ($detail['status_file_pernyataan'] ?? '') === 'Rejected' && ($detail['status_file_bebas_lab'] ?? '') === 'Rejected';
                            ?>

                            <!-- Bulk Actions Bar -->
                            <div class="mt-5 pt-4 border-t border-orange-200/60 flex flex-col sm:flex-row items-center justify-between gap-3 bg-orange-50/50 rounded-2xl p-4">
                                <div class="text-xs text-slate-500 flex items-center gap-2">
                                    <i class="bi bi-lightning-charge-fill text-orange-400"></i>
                                    <span>Aksi Masal: Reset, Setujui, atau Tolak semua berkas sekaligus.</span>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0" id="bulk-action-buttons">
                                    <!-- Reset Semua: SELALU BISA DIPENCET KAPAN SAJA -->
                                    <button type="button" onclick="resetAllFiles()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-xs" title="Reset semua berkas ke Menunggu">
                                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset Semua
                                    </button>

                                    <!-- Approve Semua & Denied Semua -->
                                    <?php if(!$allReviewed): ?>
                                        <button type="button" id="btn-bulk-approve" onclick="handleBulkLockedClick()" class="px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed flex items-center gap-2 opacity-60" title="Wajib buka dan review semua berkas terlebih dahulu">
                                            <i class="bi bi-lock-fill text-xs"></i> Approve Semua
                                        </button>
                                        <button type="button" id="btn-bulk-reject" onclick="handleBulkLockedClick()" class="px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed flex items-center gap-2 opacity-60" title="Wajib buka dan review semua berkas terlebih dahulu">
                                            <i class="bi bi-lock-fill text-xs"></i> Denied Semua
                                        </button>
                                    <?php elseif($allApproved): ?>
                                        <button type="button" id="btn-bulk-approve" class="px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl flex items-center gap-2 cursor-default shadow-xs" title="Semua berkas telah disetujui">
                                            <i class="bi bi-check-all text-base"></i> Approve Semua
                                        </button>
                                        <button type="button" id="btn-bulk-reject" onclick="handleResetFirstWarning('semua berkas', 'Denied Semua')" class="px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Semua berkas sudah di-Approve. Klik Reset Semua terlebih dahulu jika ingin mengganti ke Denied Semua">
                                            <i class="bi bi-x-circle text-sm"></i> Denied Semua
                                        </button>
                                    <?php elseif($allRejected): ?>
                                        <button type="button" id="btn-bulk-approve" onclick="handleResetFirstWarning('semua berkas', 'Approve Semua')" class="px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Semua berkas sudah di-Denied. Klik Reset Semua terlebih dahulu jika ingin mengganti ke Approve Semua">
                                            <i class="bi bi-check-all text-base"></i> Approve Semua
                                        </button>
                                        <button type="button" id="btn-bulk-reject" class="px-4 py-2 bg-rose-500 text-white font-bold text-xs rounded-xl flex items-center gap-2 cursor-default shadow-xs" title="Semua berkas telah ditolak">
                                            <i class="bi bi-x-circle-fill text-sm"></i> Denied Semua
                                        </button>
                                    <?php else: ?>
                                        <button type="button" id="btn-bulk-approve" onclick="approveAllFiles('Approved')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs" title="Approve semua berkas sekaligus">
                                            <i class="bi bi-check-all text-base"></i> Approve Semua
                                        </button>
                                        <button type="button" id="btn-bulk-reject" onclick="approveAllFiles('Rejected')" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs" title="Denied semua berkas sekaligus">
                                            <i class="bi bi-x-circle-fill text-sm"></i> Denied Semua
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

    </main>

    <!-- Modal Review PDF Dokumen Mahasiswa (Ukuran Luas & Full Screen PDF Preview) -->
    <div id="modalReviewPdf" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 backdrop-blur-md p-3 sm:p-5 transition-all duration-300">
        <div class="bg-white rounded-3xl max-w-6xl w-full h-[94vh] overflow-hidden shadow-2xl border border-orange-200/80 flex flex-col">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-orange-100 flex items-center justify-between bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center text-xl font-bold box-3d shadow-xs shrink-0">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight" id="pdfModalTitle">Review Dokumen PDF</h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[11px] font-semibold text-slate-500 font-mono flex items-center gap-1 bg-white/80 px-2.5 py-0.5 rounded-lg border border-orange-200/60" id="pdfModalFilename">
                                <i class="bi bi-paperclip"></i> <span>document.pdf</span>
                            </span>
                            <a id="pdfModalDownloadBtn" href="#" target="_blank" class="text-[11px] font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1 hover:underline">
                                <i class="bi bi-box-arrow-up-right"></i> Buka Tab Baru
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="closePdfReviewModal()" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center font-bold text-xl cursor-pointer transition shadow-xs">
                    &times;
                </button>
            </div>
            
            <!-- Modal Body (Full 1 PDF Viewport) -->
            <div class="flex-grow p-3 bg-slate-900/5 relative overflow-hidden">
                <iframe id="pdfModalIframe" src="" class="w-full h-full rounded-2xl border border-slate-300 shadow-sm bg-white" title="PDF Preview"></iframe>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 border-t border-orange-100 bg-white flex items-center justify-between gap-3 shrink-0">
                <div class="hidden sm:flex items-center gap-2 text-xs text-emerald-700 font-semibold bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                    <i class="bi bi-shield-check text-sm"></i>
                    <span>Peninjauan berkas akan membuka tombol Approve & Denied secara instan</span>
                </div>
                <div class="flex items-center gap-3 ml-auto">
                    <button type="button" onclick="closePdfReviewModal()" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" id="btnConfirmReviewed" onclick="confirmPdfReviewed()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-check-lg text-base"></i> Konfirmasi Selesai Review & Buka Kunci ACC
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Toast Notification -->
    <div id="sideToastAlert" class="hidden fixed top-20 right-6 z-[9999] max-w-sm w-full bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200 shadow-xl p-4 transition-all duration-300 transform translate-x-full">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-grow">
                <h4 class="text-xs font-bold text-slate-900 tracking-tight" id="toastTitle">Pemberitahuan</h4>
                <p class="text-xs text-slate-600 font-normal leading-relaxed mt-0.5" id="toastMessage">Tindakan berhasil diproses!</p>
            </div>
            <button type="button" onclick="hideSideToast()" class="text-slate-400 hover:text-slate-600 font-bold text-base shrink-0 cursor-pointer">
                &times;
            </button>
        </div>
    </div>

    <script>
    let activeReviewKey = '';
    let activeReviewTitle = '';
    let activeReviewFilename = '';
    let activeReviewUrl = '';
    const currentNim = '<?= $detail['nim'] ?? '1301210001'; ?>';
    
    // Status State Tracker
    const fileCurrentStatus = {
        ksm: '<?= $detail['status_file_ksm'] ?? 'Pending'; ?>',
        transkrip: '<?= $detail['status_file_transkrip'] ?? 'Pending'; ?>',
        pernyataan: '<?= $detail['status_file_pernyataan'] ?? 'Pending'; ?>',
        bebas_lab: '<?= $detail['status_file_bebas_lab'] ?? 'Pending'; ?>'
    };

    let fileReviewedState = {
        ksm: <?= !empty($detail['review_file_ksm']) ? 'true' : 'false'; ?>,
        transkrip: <?= !empty($detail['review_file_transkrip']) ? 'true' : 'false'; ?>,
        pernyataan: <?= !empty($detail['review_file_pernyataan']) ? 'true' : 'false'; ?>,
        bebas_lab: <?= !empty($detail['review_file_bebas_lab']) ? 'true' : 'false'; ?>
    };

    function showSideToast(message, title = 'Pemberitahuan', isError = false) {
        const toast = document.getElementById('sideToastAlert');
        const toastTitle = document.getElementById('toastTitle');
        const toastMsg = document.getElementById('toastMessage');

        if (!toast || !toastMsg) return;

        toastTitle.textContent = title;
        toastMsg.textContent = message;

        if (isError) {
            toast.className = 'fixed top-20 right-6 z-[9999] max-w-sm w-full bg-white/95 backdrop-blur-md rounded-2xl border border-rose-200 shadow-xl p-4 transition-all duration-300 transform translate-x-0';
            toastTitle.className = 'text-xs font-bold text-rose-700 tracking-tight';
        } else {
            toast.className = 'fixed top-20 right-6 z-[9999] max-w-sm w-full bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200 shadow-xl p-4 transition-all duration-300 transform translate-x-0';
            toastTitle.className = 'text-xs font-bold text-slate-900 tracking-tight';
        }

        toast.classList.remove('hidden');

        if (window.toastTimer) clearTimeout(window.toastTimer);
        window.toastTimer = setTimeout(() => {
            hideSideToast();
        }, 3200);
    }

    function hideSideToast() {
        const toast = document.getElementById('sideToastAlert');
        if (toast) {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }
    }

    // Modal Review Functions
    function openPdfReviewModal(key, title, filename, url) {
        activeReviewKey = key;
        activeReviewTitle = title;
        activeReviewFilename = filename;
        activeReviewUrl = url;

        const modal = document.getElementById('modalReviewPdf');
        const modalTitle = document.getElementById('pdfModalTitle');
        const modalFilename = document.getElementById('pdfModalFilename');
        const modalDocName = document.getElementById('pdfModalDocName');
        const modalIframe = document.getElementById('pdfModalIframe');
        const downloadBtn = document.getElementById('pdfModalDownloadBtn');

        if (modalTitle) modalTitle.textContent = `Review Berkas: ${title}`;
        if (modalFilename) modalFilename.innerHTML = `<i class="bi bi-paperclip"></i> <span>${filename || 'document.pdf'}</span>`;
        if (modalDocName) modalDocName.textContent = title;
        
        if (downloadBtn) {
            downloadBtn.href = url && url !== '#' ? url : '#';
        }

        if (modalIframe) {
            modalIframe.src = url && url !== '#' ? url : 'about:blank';
        }

        if (modal) {
            modal.classList.remove('hidden');
        }

        // Buka kunci tombol & catat review secara instan saat modal dibuka
        onFileReviewed(key, false);
    }

    function closePdfReviewModal() {
        const modal = document.getElementById('modalReviewPdf');
        const modalIframe = document.getElementById('pdfModalIframe');
        if (modal) {
            modal.classList.add('hidden');
        }
        if (modalIframe) {
            modalIframe.src = '';
        }
    }

    function confirmPdfReviewed() {
        if (activeReviewKey) {
            onFileReviewed(activeReviewKey, true);
        }
        closePdfReviewModal();
    }

    function reviewAndOpenPdf(key, url) {
        const fileNames = {
            ksm: 'Kartu Studi Mahasiswa (KSM)',
            transkrip: 'Transkrip Nilai Akademik',
            pernyataan: 'Surat Pernyataan TA',
            bebas_lab: 'Surat Bebas Laboratorium'
        };
        openPdfReviewModal(key, fileNames[key] || key.toUpperCase(), 'document.pdf', url);
    }

    function checkAllReviewed() {
        return updateBulkButtonsState();
    }

    function updateBulkButtonsState() {
        const keys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
        const allRev = keys.every(k => fileReviewedState[k] === true);
        const allApp = keys.every(k => fileCurrentStatus[k] === 'Approved');
        const allRej = keys.every(k => fileCurrentStatus[k] === 'Rejected');
        
        const btnApprove = document.getElementById('btn-bulk-approve');
        const btnReject = document.getElementById('btn-bulk-reject');

        if (!btnApprove || !btnReject) return allRev;

        if (!allRev) {
            btnApprove.className = 'px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed flex items-center gap-2 opacity-60';
            btnApprove.onclick = handleBulkLockedClick;
            btnApprove.innerHTML = '<i class="bi bi-lock-fill text-xs"></i> Approve Semua';
            btnApprove.title = 'Wajib buka dan review semua berkas terlebih dahulu';

            btnReject.className = 'px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed flex items-center gap-2 opacity-60';
            btnReject.onclick = handleBulkLockedClick;
            btnReject.innerHTML = '<i class="bi bi-lock-fill text-xs"></i> Denied Semua';
            btnReject.title = 'Wajib buka dan review semua berkas terlebih dahulu';
        } else if (allApp) {
            btnApprove.className = 'px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl flex items-center gap-2 cursor-default shadow-xs';
            btnApprove.onclick = null;
            btnApprove.innerHTML = '<i class="bi bi-check-all text-base"></i> Approve Semua';
            btnApprove.title = 'Semua berkas telah disetujui';

            btnReject.className = 'px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2';
            btnReject.onclick = () => handleResetFirstWarning('semua berkas', 'Denied Semua');
            btnReject.innerHTML = '<i class="bi bi-x-circle text-sm"></i> Denied Semua';
            btnReject.title = 'Semua berkas sudah di-Approve. Klik Reset Semua terlebih dahulu jika ingin mengganti ke Denied Semua';
        } else if (allRej) {
            btnApprove.className = 'px-4 py-2 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2';
            btnApprove.onclick = () => handleResetFirstWarning('semua berkas', 'Approve Semua');
            btnApprove.innerHTML = '<i class="bi bi-check-all text-base"></i> Approve Semua';
            btnApprove.title = 'Semua berkas sudah di-Denied. Klik Reset Semua terlebih dahulu jika ingin mengganti ke Approve Semua';

            btnReject.className = 'px-4 py-2 bg-rose-500 text-white font-bold text-xs rounded-xl flex items-center gap-2 cursor-default shadow-xs';
            btnReject.onclick = null;
            btnReject.innerHTML = '<i class="bi bi-x-circle-fill text-sm"></i> Denied Semua';
            btnReject.title = 'Semua berkas telah ditolak';
        } else {
            btnApprove.className = 'px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs';
            btnApprove.onclick = () => approveAllFiles('Approved');
            btnApprove.innerHTML = '<i class="bi bi-check-all text-base"></i> Approve Semua';
            btnApprove.title = 'Approve semua berkas sekaligus';

            btnReject.className = 'px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs';
            btnReject.onclick = () => approveAllFiles('Rejected');
            btnReject.innerHTML = '<i class="bi bi-x-circle-fill text-sm"></i> Denied Semua';
            btnReject.title = 'Denied semua berkas sekaligus';
        }
        return allRev;
    }

    function handleBulkLockedClick() {
        const keys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
        const unrevCount = keys.filter(k => !fileReviewedState[k]).length;
        showSideToast(`Masih ada ${unrevCount} berkas yang belum direview. Silakan buka semua berkas PDF sebelum menggunakan Approve Semua atau Denied Semua.`, 'Pemberitahuan', true);
    }

    function handleLockedClick(key, title, filename, url) {
        showSideToast(`Membuka berkas ${title || key.toUpperCase()} untuk ditinjau...`, 'Review Berkas');
        openPdfReviewModal(key, title || key.toUpperCase(), filename || '', url || '#');
    }

    function onFileReviewed(key, showToast = true) {
        if (!key) return;

        fileReviewedState[key] = true;

        // 1. Update text & style button review to green
        const btnReview = document.getElementById(`btn-review-${key}`);
        if (btnReview) {
            btnReview.className = 'shrink-0 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100 font-bold text-xs rounded-xl transition flex items-center gap-2 whitespace-nowrap cursor-pointer shadow-2xs';
        }
        const textReview = document.getElementById(`text-review-${key}`);
        if (textReview) {
            textReview.textContent = 'Buka Ulang PDF';
        }

        // 2. UNLOCK Approve & Denied buttons immediately for this file!
        const actionContainer = document.getElementById(`action-buttons-${key}`);
        if (actionContainer) {
            const st = fileCurrentStatus[key] || 'Pending';
            if (st === 'Approved') {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" onclick="handleResetFirstWarning('${key}', 'Denied')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Denied">
                        <i class="bi bi-x-circle text-sm"></i> Denied
                    </button>
                `;
            } else if (st === 'Rejected') {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" onclick="handleResetFirstWarning('${key}', 'Approve')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Approve">
                        <i class="bi bi-check-circle text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" class="px-5 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                    </button>
                `;
            } else {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" onclick="approveSingleFile('${key}', 'Approved')" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" onclick="approveSingleFile('${key}', 'Rejected')" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                    </button>
                `;
            }
        }

        // 3. Update bulk buttons if all 4 are now reviewed!
        checkAllReviewed();

        if (showToast) {
            showSideToast(`Berkas ${key.toUpperCase()} telah ditinjau. Tombol Approve & Denied sekarang aktif.`, 'Review Berkas');
        }

        // Send async log to backend
        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('file_type', key);

        fetch('<?= site_url('dosenwali/log_review_ajax'); ?>', {
            method: 'POST',
            body: formData
        }).catch(err => console.error(err));
    }

    function handleResetFirstWarning(key, targetStatus) {
        showSideToast(`Berkas sudah di-${fileCurrentStatus[key]}. Klik tombol "Reset" terlebih dahulu jika ingin mengganti ke ${targetStatus}.`, 'Pemberitahuan', true);
    }

    function resetSingleFile(key) {
        if (!key) return;

        fileCurrentStatus[key] = 'Pending';

        // 1. Update Status Badge
        const statusBadge = document.getElementById(`badge-status-${key}`);
        if (statusBadge) {
            statusBadge.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-slate-100 text-slate-600 border-slate-200';
            statusBadge.textContent = '⏳ Menunggu';
        }

        // 2. Update Card Border
        const cardOuter = document.getElementById(`card-file-${key}`);
        if (cardOuter) {
            cardOuter.className = 'rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden transition-all';
        }

        // 3. Restore both Approve & Denied to active clickable states if reviewed
        const actionContainer = document.getElementById(`action-buttons-${key}`);
        if (actionContainer) {
            if (fileReviewedState[key]) {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" onclick="approveSingleFile('${key}', 'Approved')" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" onclick="approveSingleFile('${key}', 'Rejected')" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                    </button>
                `;
            } else {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" onclick="handleLockedClick('${key}')" class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-pointer flex items-center gap-1.5 opacity-70 hover:opacity-100 hover:bg-slate-200 transition" title="Klik untuk Buka PDF & Buka Kunci Approve">
                        <i class="bi bi-lock-fill text-xs"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" onclick="handleLockedClick('${key}')" class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-pointer flex items-center gap-1.5 opacity-70 hover:opacity-100 hover:bg-slate-200 transition" title="Klik untuk Buka PDF & Buka Kunci Denied">
                        <i class="bi bi-lock-fill text-xs"></i> Denied
                    </button>
                `;
            }
        }

        // 4. Re-enable comment textarea
        const commentArea = document.getElementById(`comment-${key}`);
        if (commentArea) {
            commentArea.removeAttribute('readonly');
            commentArea.className = 'w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition bg-white';
            commentArea.focus();
        }

        const commentStatus = document.getElementById(`comment-status-${key}`);
        if (commentStatus) {
            commentStatus.className = 'text-[10px] text-slate-400 font-normal';
            commentStatus.textContent = '(Dapat diedit)';
        }

        showSideToast(`Status berkas ${key.toUpperCase()} di-reset ke Menunggu. Form komentar & tombol keputusan terbuka kembali.`, 'Pemberitahuan');

        // Update bulk buttons state immediately
        updateBulkButtonsState();

        // 5. Send AJAX update
        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('file_type', key);
        formData.append('status', 'Pending');

        fetch('<?= site_url('dosenwali/update_file_approval_ajax'); ?>', {
            method: 'POST',
            body: formData
        }).catch(err => console.error(err));
    }

    function approveSingleFile(key, status) {
        if (!fileReviewedState[key]) {
            handleLockedClick(key);
            return;
        }

        if (fileCurrentStatus[key] === status) {
            return;
        }

        // Update state
        fileCurrentStatus[key] = status;

        const commentArea = document.getElementById(`comment-${key}`);
        const commentVal = commentArea ? commentArea.value.trim() : '';

        // Lock comment textarea on Decision
        if (commentArea) {
            commentArea.setAttribute('readonly', 'true');
            commentArea.className = 'w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition bg-slate-50 opacity-80 cursor-not-allowed';
        }

        const commentStatus = document.getElementById(`comment-status-${key}`);
        if (commentStatus) {
            commentStatus.className = 'text-[10px] text-amber-600 font-semibold';
            commentStatus.textContent = '(Terkunci, klik Reset untuk mengedit kembali)';
        }

        // Update Status Badge
        const statusBadge = document.getElementById(`badge-status-${key}`);
        if (statusBadge) {
            if (status === 'Approved') {
                statusBadge.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-emerald-100 text-emerald-800 border-emerald-300';
                statusBadge.textContent = '✅ Disetujui';
            } else {
                statusBadge.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-rose-100 text-rose-700 border-rose-300';
                statusBadge.textContent = '❌ Ditolak';
            }
        }

        // Update Card Border
        const cardOuter = document.getElementById(`card-file-${key}`);
        if (cardOuter) {
            if (status === 'Approved') {
                cardOuter.className = 'rounded-2xl border border-emerald-300 bg-emerald-50/30 shadow-xs overflow-hidden transition-all';
            } else {
                cardOuter.className = 'rounded-2xl border border-rose-300 bg-rose-50/20 shadow-xs overflow-hidden transition-all';
            }
        }

        // Update buttons: Selected stays colored, opposite becomes GREY / disabled until reset!
        const actionContainer = document.getElementById(`action-buttons-${key}`);
        if (actionContainer) {
            if (status === 'Approved') {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" onclick="handleResetFirstWarning('${key}', 'Denied')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Denied">
                        <i class="bi bi-x-circle text-sm"></i> Denied
                    </button>
                `;
            } else {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-${key}" onclick="resetSingleFile('${key}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-${key}" onclick="handleResetFirstWarning('${key}', 'Approve')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Approve">
                        <i class="bi bi-check-circle text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-${key}" class="px-5 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                    </button>
                `;
            }
        }

        showSideToast(`Berkas ${key.toUpperCase()} berhasil di-${status === 'Approved' ? 'Approve' : 'Denied'}. Form komentar dikunci.`, 'Pemberitahuan');

        // Update bulk buttons state
        updateBulkButtonsState();

        // Send AJAX request with comment
        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('file_type', key);
        formData.append('status', status);
        formData.append('comment', commentVal);

        fetch('<?= site_url('dosenwali/update_file_approval_ajax'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showSideToast(data.message || 'Gagal memperbarui status berkas.', 'Peringatan', true);
            }
        })
        .catch(err => {
            console.error(err);
        });
    }

    function resetAllFiles() {
        const keys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
        
        keys.forEach(k => {
            fileCurrentStatus[k] = 'Pending';

            const statusBadge = document.getElementById(`badge-status-${k}`);
            if (statusBadge) {
                statusBadge.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-slate-100 text-slate-600 border-slate-200';
                statusBadge.textContent = '⏳ Menunggu';
            }

            const cardOuter = document.getElementById(`card-file-${k}`);
            if (cardOuter) {
                cardOuter.className = 'rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden transition-all';
            }

            const actionContainer = document.getElementById(`action-buttons-${k}`);
            if (actionContainer) {
                if (fileReviewedState[k]) {
                    actionContainer.innerHTML = `
                        <button type="button" id="btn-reset-${k}" onclick="resetSingleFile('${k}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                            <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                        </button>
                        <button type="button" id="btn-approve-${k}" onclick="approveSingleFile('${k}', 'Approved')" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                            <i class="bi bi-check-circle-fill text-sm"></i> Approve
                        </button>
                        <button type="button" id="btn-reject-${k}" onclick="approveSingleFile('${k}', 'Rejected')" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                            <i class="bi bi-x-circle-fill text-sm"></i> Denied
                        </button>
                    `;
                } else {
                    actionContainer.innerHTML = `
                        <button type="button" id="btn-reset-${k}" onclick="resetSingleFile('${k}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                            <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                        </button>
                        <button type="button" id="btn-approve-${k}" onclick="handleLockedClick('${k}')" class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-pointer flex items-center gap-1.5 opacity-70 hover:opacity-100 hover:bg-slate-200 transition" title="Klik untuk Buka PDF & Buka Kunci Approve">
                            <i class="bi bi-lock-fill text-xs"></i> Approve
                        </button>
                        <button type="button" id="btn-reject-${k}" onclick="handleLockedClick('${k}')" class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed flex items-center gap-1.5 opacity-60" title="Wajib buka PDF terlebih dahulu">
                            <i class="bi bi-lock-fill text-xs"></i> Denied
                        </button>
                    `;
                }
            }

            const commentArea = document.getElementById(`comment-${k}`);
            if (commentArea) {
                commentArea.removeAttribute('readonly');
                commentArea.className = 'w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition bg-white';
            }

            const commentStatus = document.getElementById(`comment-status-${k}`);
            if (commentStatus) {
                commentStatus.className = 'text-[10px] text-slate-400 font-normal';
                commentStatus.textContent = '(Dapat diedit)';
            }
        });

        showSideToast(`Semua berkas berhasil di-reset ke Menunggu.`, 'Pemberitahuan');

        // Update bulk buttons state immediately
        updateBulkButtonsState();

        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('status', 'Pending');

        fetch('<?= site_url('dosenwali/approve_all_files_ajax'); ?>', {
            method: 'POST',
            body: formData
        }).catch(err => console.error(err));
    }

    function approveAllFiles(status) {
        if (!checkAllReviewed()) {
            handleBulkLockedClick();
            return;
        }

        const keys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
        
        keys.forEach(k => {
            fileCurrentStatus[k] = status;

            // 1. Update Status Badges
            const statusBadge = document.getElementById(`badge-status-${k}`);
            if (statusBadge) {
                if (status === 'Approved') {
                    statusBadge.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-emerald-100 text-emerald-800 border-emerald-300';
                    statusBadge.textContent = '✅ Disetujui';
                } else {
                    statusBadge.className = 'px-2 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-rose-100 text-rose-700 border-rose-300';
                    statusBadge.textContent = '❌ Ditolak';
                }
            }

            // 2. Lock Comment Textarea
            const commentArea = document.getElementById(`comment-${k}`);
            if (commentArea) {
                commentArea.setAttribute('readonly', 'true');
                commentArea.className = 'w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition bg-slate-50 opacity-80 cursor-not-allowed';
            }

            const commentStatus = document.getElementById(`comment-status-${k}`);
            if (commentStatus) {
                commentStatus.className = 'text-[10px] text-amber-600 font-semibold';
                commentStatus.textContent = '(Terkunci, klik Reset untuk mengedit kembali)';
            }

            // 3. Update Card Border
            const cardOuter = document.getElementById(`card-file-${k}`);
            if (cardOuter) {
                if (status === 'Approved') {
                    cardOuter.className = 'rounded-2xl border border-emerald-300 bg-emerald-50/30 shadow-xs overflow-hidden transition-all';
                } else {
                    cardOuter.className = 'rounded-2xl border border-rose-300 bg-rose-50/20 shadow-xs overflow-hidden transition-all';
                }
            }

            // 4. Update Action Buttons
            const actionContainer = document.getElementById(`action-buttons-${k}`);
            if (actionContainer) {
                if (status === 'Approved') {
                    actionContainer.innerHTML = `
                        <button type="button" id="btn-reset-${k}" onclick="resetSingleFile('${k}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                            <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                        </button>
                        <button type="button" id="btn-approve-${k}" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                            <i class="bi bi-check-circle-fill text-sm"></i> Approve
                        </button>
                        <button type="button" id="btn-reject-${k}" onclick="handleResetFirstWarning('${k}', 'Denied')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Denied">
                            <i class="bi bi-x-circle text-sm"></i> Denied
                        </button>
                    `;
                } else {
                    actionContainer.innerHTML = `
                        <button type="button" id="btn-reset-${k}" onclick="resetSingleFile('${k}')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / komentar">
                            <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                        </button>
                        <button type="button" id="btn-approve-${k}" onclick="handleResetFirstWarning('${k}', 'Approve')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Approve">
                            <i class="bi bi-check-circle text-sm"></i> Approve
                        </button>
                        <button type="button" id="btn-reject-${k}" class="px-5 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                            <i class="bi bi-x-circle-fill text-sm"></i> Denied
                        </button>
                    `;
                }
            }
        });

        showSideToast(`Semua berkas berhasil di-${status === 'Approved' ? 'Approve' : 'Denied'}. Form komentar dikunci.`, 'Pemberitahuan');

        // Update bulk buttons visual state immediately
        updateBulkButtonsState();

        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('status', status);

        fetch('<?= site_url('dosenwali/approve_all_files_ajax'); ?>', {
            method: 'POST',
            body: formData
        }).catch(err => console.error(err));
    }

    // --- FITUR KEPUTUSAN JUDUL TA (APPROVE / REJECT / RESET) ---
    function handleResetFirstWarningJudul(targetAction) {
        showSideToast(`Status judul saat ini sudah ditetapkan. Silakan klik tombol "Reset" terlebih dahulu jika ingin mengganti menjadi ${targetAction}.`, 'Perhatian');
    }

    function resetJudulDecision() {
        const statusBadge = document.getElementById('badge-status-judul-global');
        if (statusBadge) {
            statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-slate-100 text-slate-600 border-slate-200';
            statusBadge.textContent = '⏳ Menunggu';
        }

        const actionContainer = document.getElementById('action-buttons-judul');
        if (actionContainer) {
            actionContainer.innerHTML = `
                <button type="button" id="btn-reset-judul" onclick="resetJudulDecision()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / saran">
                    <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                </button>
                <button type="button" id="btn-approve-judul" onclick="decideJudul('Approved')" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                    <i class="bi bi-check-circle-fill text-sm"></i> Approve
                </button>
                <button type="button" id="btn-reject-judul" onclick="decideJudul('Rejected')" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                    <i class="bi bi-x-circle-fill text-sm"></i> Denied
                </button>
            `;
        }

        const commentArea = document.getElementById('catatan_judul');
        if (commentArea) {
            commentArea.removeAttribute('readonly');
            commentArea.className = 'w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition bg-white';
            commentArea.focus();
        }

        const commentStatus = document.getElementById('comment-status-judul');
        if (commentStatus) {
            commentStatus.className = 'text-[10px] text-slate-400 font-normal';
            commentStatus.textContent = '(Dapat diedit)';
        }

        showSideToast('Status Judul di-reset ke Menunggu. Form saran & tombol keputusan terbuka kembali.', 'Pemberitahuan');

        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('status_judul', 'Pending');
        formData.append('catatan_judul', commentArea ? commentArea.value.trim() : '');

        fetch('<?= site_url('dosenwali/update_judul_approval_ajax'); ?>', {
            method: 'POST',
            body: formData
        }).catch(err => console.error(err));
    }

    function decideJudul(status) {
        const commentArea = document.getElementById('catatan_judul');
        const commentVal = commentArea ? commentArea.value.trim() : '';

        if (commentArea) {
            commentArea.setAttribute('readonly', 'true');
            commentArea.className = 'w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 outline-none resize-none text-slate-700 placeholder:text-slate-400 transition bg-slate-50 opacity-80 cursor-not-allowed';
        }

        const commentStatus = document.getElementById('comment-status-judul');
        if (commentStatus) {
            commentStatus.className = 'text-[10px] text-amber-600 font-semibold';
            commentStatus.textContent = '(Terkunci, klik Reset untuk mengedit kembali)';
        }

        const statusBadge = document.getElementById('badge-status-judul-global');
        if (statusBadge) {
            if (status === 'Approved') {
                statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-emerald-100 text-emerald-800 border-emerald-300';
                statusBadge.textContent = '✅ Disetujui';
            } else {
                statusBadge.className = 'px-2.5 py-0.5 text-[10px] font-bold rounded-md border whitespace-nowrap bg-rose-100 text-rose-700 border-rose-300';
                statusBadge.textContent = '❌ Ditolak';
            }
        }

        const actionContainer = document.getElementById('action-buttons-judul');
        if (actionContainer) {
            if (status === 'Approved') {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-judul" onclick="resetJudulDecision()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / saran">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-judul" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                        <i class="bi bi-check-circle-fill text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-judul" onclick="handleResetFirstWarningJudul('Denied')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Denied">
                        <i class="bi bi-x-circle text-sm"></i> Denied
                    </button>
                `;
            } else {
                actionContainer.innerHTML = `
                    <button type="button" id="btn-reset-judul" onclick="resetJudulDecision()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs" title="Reset status ke Menunggu & buka kunci tombol / saran">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset
                    </button>
                    <button type="button" id="btn-approve-judul" onclick="handleResetFirstWarningJudul('Approve')" class="px-5 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed opacity-50 flex items-center gap-2" title="Klik Reset jika ingin mengganti ke Approve">
                        <i class="bi bi-check-circle text-sm"></i> Approve
                    </button>
                    <button type="button" id="btn-reject-judul" class="px-5 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs cursor-default">
                        <i class="bi bi-x-circle-fill text-sm"></i> Denied
                    </button>
                `;
            }
        }

        showSideToast(`Status Judul berhasil di-${status === 'Approved' ? 'Approve' : 'Denied'}. Form saran dikunci.`, 'Pemberitahuan');

        const formData = new FormData();
        formData.append('nim', currentNim);
        formData.append('status_judul', status);
        formData.append('catatan_judul', commentVal);

        fetch('<?= site_url('dosenwali/update_judul_approval_ajax'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showSideToast(data.message || 'Gagal memperbarui status judul.', 'Peringatan', true);
            }
        })
        .catch(err => {
            console.error(err);
        });
    }

    </script>
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
